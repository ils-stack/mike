<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    protected SecureRequest $api;

    public function __construct()
    {
        $this->api = new SecureRequest();
    }

    public function policySchedule(Request $request)
    {
        $entityId = session('investor_entity_unique_id');

        if (!$entityId && session('investor_id')) {
            $entityId = Investor::where('id', session('investor_id'))
                ->value('entity_unique_id');

            if ($entityId) {
                session(['investor_entity_unique_id' => $entityId]);
            }
        }

        abort_unless($entityId, 400, 'Select an investor before generating the policy schedule.');

        try {
            $entity = $this->api->get('/api/entities/' . $entityId);
            $accountsPayload = $this->api->get('/api/entities/' . $entityId . '/accounts');
        } catch (Exception $exception) {
            abort(502, 'Unable to load policy data for the selected investor.');
        }

        $periodLabels = $this->buildPeriodLabels();
        $rows = [];
        $totals = [
            'inception_value' => 0.0,
            'period_values' => [0.0, 0.0, 0.0, 0.0],
        ];

        foreach (($accountsPayload['Accounts'] ?? []) as $account) {
            $accountNumber = $account['Account number'] ?? $account['Contract number'] ?? null;

            if (!$accountNumber) {
                continue;
            }

            $summary = [];

            try {
                $summary = $this->api->get(
                    '/api/accounts/' . $accountNumber . '/investmentSummarySinceInception'
                );
            } catch (Exception $exception) {
                $summary = [];
            }

            $currentValue = (float) (
                data_get($account, 'Market value.value')
                ?? data_get($account, 'Market value.amount')
                ?? data_get($account, 'Market Value In Reporting Currency.value')
                ?? 0
            );

            $inceptionValue = (float) (
                data_get($summary, 'Opening balance.value')
                ?? 0
            );

            $rows[] = [
                'company' => $account['Product'] ?? $account['Description'] ?? '-',
                'product_type' => $account['Description'] ?? $account['Product'] ?? '-',
                'policy_number' => $account['Contract number'] ?? $account['Account number'] ?? '-',
                'inception_date' => $this->formatDate(
                    $summary['Start date'] ?? $account['Inception Date'] ?? null
                ),
                'inception_value' => $inceptionValue,
                'period_values' => [$currentValue, null, null, null],
            ];

            $totals['inception_value'] += $inceptionValue;
            $totals['period_values'][0] += $currentValue;
        }

        $clientName = trim(implode(' ', array_filter([
            $entity['First Name'] ?? null,
            $entity['Surname'] ?? null,
        ])));

        if ($clientName === '') {
            $clientName = $entity['Investor name']
                ?? $entity['Name']
                ?? $entity['Company Name']
                ?? 'Selected Investor';
        }

        $clientReference = $entity['ID Number']
            ?? $entity['Identification number']
            ?? $entity['Client Number']
            ?? $entityId;

        $pdf = Pdf::loadView('reports.policy-schedule-pdf', [
            'title' => 'Policy Schedule',
            'clientName' => $clientName,
            'clientReference' => $clientReference,
            'periodLabels' => $periodLabels,
            'rows' => $rows,
            'totals' => $totals,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('policy-schedule.pdf');
    }

    protected function buildPeriodLabels(): array
    {
        $now = Carbon::now();

        return [
            $now->format('M Y'),
            $now->copy()->endOfQuarter()->format('M Y'),
            $now->copy()->addQuarter()->endOfQuarter()->format('M Y'),
            $now->copy()->addQuarters(2)->endOfQuarter()->format('M Y'),
        ];
    }

    protected function formatDate(?string $date): string
    {
        if (!$date) {
            return '-';
        }

        try {
            return Carbon::parse($date)->format('d-M-y');
        } catch (Exception $exception) {
            return $date;
        }
    }
}

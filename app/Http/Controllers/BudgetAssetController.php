<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithBudgetAssetData;
use App\Models\SsAls;
use App\Models\SsUsersFields;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BudgetAssetController extends Controller
{
    use InteractsWithBudgetAssetData;

    public function index()
    {
        return view('assets', [
            'budget_cats' => SsAls::query()->orderBy('alorder')->orderByDesc('alname')->get()->toArray(),
            'user_fields' => SsUsersFields::query()
                ->where('typeid', 5)
                ->where('userid', $this->budgetAssetClientId())
                ->orderBy('field')
                ->get()
                ->toArray(),
            'client_id' => $this->budgetAssetClientId(),
            'spouse_ref' => $this->budgetAssetSpouseRef(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        foreach (['client_description', 'client_value', 'client_owing'] as $field) {
            $this->storeAssetField($field, $request->string($field)->toString());
        }

        foreach (['spouse_value', 'spouse_owing'] as $field) {
            $this->storeAssetField($field, $request->string($field)->toString(), true);
        }

        return redirect('/crm-assets');
    }

    public function destroy(string $bid): RedirectResponse
    {
        [$categoryId, $rowId] = explode('-', $bid);

        $clientKey = $this->budgetAssetClientId() . '_' . $categoryId . '_' . $rowId;
        $spouseKey = $this->budgetAssetSpouseRef() . '_' . $categoryId . '_' . $rowId;

        SsUsersFields::query()
            ->where('typeid', 5)
            ->where('userid', $this->budgetAssetClientId())
            ->where(function ($query) use ($clientKey, $spouseKey) {
                $query->where('field', 'like', '%' . $clientKey . '%')
                    ->orWhere('field', 'like', '%' . $spouseKey . '%');
            })
            ->delete();

        return redirect('/crm-assets');
    }

    public function print()
    {
        $userFields = SsUsersFields::query()
            ->where('typeid', 5)
            ->where('userid', $this->budgetAssetClientId())
            ->orderBy('field')
            ->get()
            ->toArray();

        return Pdf::loadView('pdf.asset_report', [
            'budget_cats' => SsAls::query()->orderBy('alorder')->orderByDesc('alname')->get()->toArray(),
            'user_fields' => $userFields,
            'client_id' => $this->budgetAssetClientId(),
            'spouse_ref' => $this->budgetAssetSpouseRef(),
            'client_nm' => $this->budgetAssetClientName(),
            'client_total_summary' => $this->rowTotals($userFields),
            'assetObj' => $this,
        ])->setPaper('a4', 'landscape')->download('asset_report.pdf');
    }

    public function rowTotals(array $userFields): array
    {
        $totals = [];

        foreach ($userFields as $field) {
            $fieldName = $field['field'] ?? '';
            $value = trim((string) ($field['value'] ?? ''));

            if (! str_contains($fieldName, '_') || ! is_numeric($value)) {
                continue;
            }

            $summaryKey = preg_replace('/[0-9|_]/', '', $fieldName);
            $totals[$summaryKey] = ($totals[$summaryKey] ?? 0) + (float) $value;
        }

        return $totals;
    }

    protected function storeAssetField(string $prefix, string $value, bool $spouse = false): void
    {
        $owner = $spouse ? $this->budgetAssetSpouseRef() : (string) $this->budgetAssetClientId();
        $fieldName = $prefix . '_' . $owner . '_' . request('budget_id') . '_' . request('nth_row');

        $record = SsUsersFields::query()
            ->where('typeid', 5)
            ->where('userid', $this->budgetAssetClientId())
            ->where('field', $fieldName)
            ->first();

        if ($record) {
            $record->value = trim($value);
            $record->save();

            return;
        }

        SsUsersFields::query()->create([
            'userid' => $this->budgetAssetClientId(),
            'typeid' => 5,
            'field' => $fieldName,
            'value' => trim($value),
        ]);
    }
}

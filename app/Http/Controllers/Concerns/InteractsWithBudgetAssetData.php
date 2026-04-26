<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Expenses;
use App\Models\Incomes;
use App\Models\SsUsersFields;

trait InteractsWithBudgetAssetData
{
    protected function budgetAssetClientId(): int
    {
        return (int) auth()->id();
    }

    protected function budgetAssetSpouseRef(): string
    {
        return 'sp' . $this->budgetAssetClientId();
    }

    protected function budgetAssetClientName(): string
    {
        $user = auth()->user();

        return trim($user->name ?? $user->email ?? 'User');
    }

    protected function budgetAssetFieldMap(int $typeId): array
    {
        return SsUsersFields::query()
            ->where('typeid', $typeId)
            ->where('userid', $this->budgetAssetClientId())
            ->orderBy('ufid')
            ->get()
            ->mapWithKeys(fn (SsUsersFields $field) => [$field->field => $field->value ?? ''])
            ->all();
    }

    protected function upsertBudgetAssetFields(int $typeId, array $fields): void
    {
        unset($fields['_token']);

        foreach ($fields as $field => $value) {
            $record = SsUsersFields::query()
                ->where('field', $field)
                ->where('userid', $this->budgetAssetClientId())
                ->where('typeid', $typeId)
                ->first();

            if ($record) {
                $record->value = $value ?: '';
                $record->save();

                continue;
            }

            SsUsersFields::query()->create([
                'userid' => $this->budgetAssetClientId(),
                'typeid' => $typeId,
                'field' => $field,
                'value' => $value ?: '',
            ]);
        }
    }

    protected function buildIncomeRows(int $typeId, bool $readonly = false): array
    {
        $clientId = $this->budgetAssetClientId();
        $spouseRef = $this->budgetAssetSpouseRef();
        $valueMap = $this->budgetAssetFieldMap($typeId);
        $rows = Incomes::query()->where('parentid', 0)->get()->toArray();

        $totals = [
            'c_tax' => 0,
            'c_ntax' => 0,
            's_tax' => 0,
            's_ntax' => 0,
        ];

        foreach ($rows as &$row) {
            $clientTaxField = 'client_tx_' . $clientId . '_' . $row['incomeid'];
            $clientNonTaxField = 'client_ntx_' . $clientId . '_' . $row['incomeid'];
            $spouseTaxField = 'spouse_tx_' . $spouseRef . '_' . $row['incomeid'];
            $spouseNonTaxField = 'spouse_ntx_' . $spouseRef . '_' . $row['incomeid'];

            $clientTax = $this->cleanCurrencyValue($valueMap[$clientTaxField] ?? '0');
            $clientNonTax = $this->cleanCurrencyValue($valueMap[$clientNonTaxField] ?? '0');
            $spouseTax = $this->cleanCurrencyValue($valueMap[$spouseTaxField] ?? '0');
            $spouseNonTax = $this->cleanCurrencyValue($valueMap[$spouseNonTaxField] ?? '0');

            $totals['c_tax'] += $clientTax;
            $totals['c_ntax'] += $clientNonTax;
            $totals['s_tax'] += $spouseTax;
            $totals['s_ntax'] += $spouseNonTax;

            $row['c_taxable'] = $this->renderAmountCell($clientTaxField, $valueMap[$clientTaxField] ?? '', $readonly);
            $row['c_nontaxable'] = $this->renderAmountCell($clientNonTaxField, $valueMap[$clientNonTaxField] ?? '', $readonly);
            $row['s_taxable'] = $this->renderAmountCell($spouseTaxField, $valueMap[$spouseTaxField] ?? '', $readonly);
            $row['s_nontaxable'] = $this->renderAmountCell($spouseNonTaxField, $valueMap[$spouseNonTaxField] ?? '', $readonly);
        }

        unset($row);

        return [$rows, $totals];
    }

    protected function buildExpenseRows(int $typeId, bool $readonly = false): array
    {
        $valueMap = $this->budgetAssetFieldMap($typeId);
        $expenseHeads = Expenses::query()->where('parentid', 0)->orderBy('expenseorder')->get();
        $rows = [];
        $expenseTotal = 0;

        foreach ($expenseHeads as $expenseHead) {
            $rows[] = [
                'expensename' => $readonly
                    ? '<div class="text-dark p-0 text-left"><strong>' . e($expenseHead->expensename) . '</strong></div>'
                    : '<div class = "text-primary p-2 text-center"><h5>' . e($expenseHead->expensename) . '</h5></div>',
                'c_taxable' => $readonly
                    ? '<div class = "text-dark p-0 text-left"><strong>Amount</strong></div>'
                    : '<div class = "text-primary p-2 text-center"><h5>Amount</h5></div>',
            ];

            $subHeads = Expenses::query()
                ->where('parentid', $expenseHead->expenseid)
                ->orderBy('expenseorder')
                ->get();

            foreach ($subHeads as $subHead) {
                $amount = $this->cleanCurrencyValue($valueMap[$subHead->expenseid] ?? '0');
                $expenseTotal += $amount;

                $rows[] = [
                    'expensename' => e($subHead->expensename),
                    'c_taxable' => $readonly
                        ? '<span>' . e($this->formatRand($amount)) . '</span>'
                        : '<input type = "text" autocomplete = "off" value = "' . e($valueMap[$subHead->expenseid] ?? '') . '" name = "' . e((string) $subHead->expenseid) . '" class="form-control">',
                ];
            }
        }

        return [$rows, $expenseTotal];
    }

    protected function renderAmountCell(string $field, string $value, bool $readonly): string
    {
        if ($readonly) {
            return '<span>' . e($this->formatRand($this->cleanCurrencyValue($value))) . '</span>';
        }

        return '<input type = "text" class="form-control" autocomplete = "off" value = "' . e($value) . '" name = "' . e($field) . '" />';
    }

    protected function cleanCurrencyValue(string|int|float|null $amount): float
    {
        $cleaned = preg_replace('/[\s,]|R|rand|ZAR/i', '', (string) ($amount ?? ''));

        return is_numeric($cleaned) ? (float) $cleaned : 0.0;
    }

    public function formatRand(string|int|float|null $amount): string
    {
        $number = round($this->cleanCurrencyValue($amount));

        return $number === 0.0 ? '-' : 'R ' . number_format((float) $number, 0, '', ',');
    }
}

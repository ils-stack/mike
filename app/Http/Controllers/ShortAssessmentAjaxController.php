<?php

namespace App\Http\Controllers;

use App\Models\EstateCashFlow;
use App\Models\SsTaxRebate;
use App\Models\SsTaxTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShortAssessmentAjaxController extends Controller
{
    public function taxCalcGraph(Request $request)
    {
        $income = (float) $request->input('income', 0);
        $nonTaxableIncome = (float) $request->input('nt_income', 0);
        $adjuster = (float) $request->input('adjuster', 0);
        $deductibleContributions = (float) $request->input('d_cont', 0);
        $age = (int) $request->input('age', 0);
        $budgetAfterTax = (float) $request->input('budget_aft_tax', 0);
        $taxYear = (int) $request->input('year_id', 0);

        $startYear = $taxYear - 9;
        $years = [];
        $amounts = [];

        for ($year = $startYear; $year <= $taxYear; $year++) {
            $calculation = $this->calculateTaxYear(
                $year,
                $age,
                $income,
                $nonTaxableIncome,
                $adjuster,
                $deductibleContributions,
                $budgetAfterTax
            );

            if (!$calculation) {
                return response()->json(false);
            }

            $years[] = $year;
            $amounts[] = $calculation['tax_payable_raw'];
        }

        return response()->json([$years, $amounts]);
    }

    public function taxCalc(Request $request)
    {
        $taxYear = (int) $request->input('year_id', 0);
        $age = (int) $request->input('age', 0);
        $income = (float) $request->input('income', 0);
        $nonTaxableIncome = (float) $request->input('nt_income', 0);
        $adjuster = (float) $request->input('adjuster', 0);
        $deductibleContributions = (float) $request->input('d_cont', 0);
        $budgetAfterTax = (float) $request->input('budget_aft_tax', 0);

        $calculation = $this->calculateTaxYear(
            $taxYear,
            $age,
            $income,
            $nonTaxableIncome,
            $adjuster,
            $deductibleContributions,
            $budgetAfterTax
        );

        if (!$calculation) {
            return response()->json(false);
        }

        return response()->json([
            'gr_tax_inc' => 'R ' . number_format($calculation['gross_taxable_income'], 0),
            'net_tax_income' => 'R ' . number_format($calculation['net_taxable_income'], 0),
            'tax_payable' => 'R ' . number_format($calculation['tax_payable_raw'], 0),
            'avl_aft_tax' => 'R ' . number_format($calculation['available_after_tax'], 0),
            'short_fall' => 'R ' . number_format($calculation['shortfall'], 0),
            'tax_rate' => $calculation['tax_rate'],
        ]);
    }

    public function ajaxRequestPost(Request $request)
    {
        return response()->json($this->doCalcEstate($request->all()));
    }

    public function ajaxCashFlow()
    {
        return response()->json(
            EstateCashFlow::where('clientid', $this->currentClientId(999999999999999))
                ->where('type', 1)
                ->get()
        );
    }

    public function ajaxCashFlowDis()
    {
        return response()->json(
            EstateCashFlow::where('clientid', $this->currentClientId(999999999999998))
                ->where('type', 2)
                ->get()
        );
    }

    public function ajaxCashFlowRt()
    {
        return response()->json(
            EstateCashFlow::where('clientid', $this->currentClientId(999999999999997))
                ->where('type', 3)
                ->get()
        );
    }

    public function ajaxPostedFv(Request $request)
    {
        $rate = (float) $request->input('rate', 0);
        $extraYears = (int) $request->input('extra_years', 0);
        $pv = (float) $request->input('pv', 0);
        $presentAge = (int) $request->input('present_age', 0);
        $retireAge = (int) $request->input('retire_age', 0);

        $nper = max(0, $retireAge - $presentAge) + $extraYears;

        return response()->json(round($this->fv($rate, $nper, 0, $pv), 0));
    }

    public function ajaxPostedFvPv(Request $request)
    {
        $rate = (float) $request->input('rate', 0);
        $extraYears = (int) $request->input('extra_years', 0);
        $pv = (float) $request->input('pv', 0);
        $presentAge = (int) $request->input('present_age', 0);
        $retireAge = (int) $request->input('retire_age', 0);

        $nper = max(0, $retireAge - $presentAge) + $extraYears;

        return response()->json(round($this->fv($rate, $nper, 0, $pv), 0));
    }

    public function postedSimpleInterest(Request $request)
    {
        $presentAge = (int) $request->input('present_age', 0);
        $retireAge = (int) $request->input('retire_age', 0);
        $extraYears = (int) $request->input('extra_years', 0);
        $esRate = (float) $request->input('rate', 0);
        $growthRate = (float) $request->input('growth_rate', 0);
        $type = (int) $request->input('type', 1);
        $payment = (float) $request->input('pmt', 0);
        $pv = (float) $request->input('pv', 0);

        $nper = max(0, $retireAge - $presentAge) + $extraYears;
        $payments = $nper * 12;

        $fixedFv = $this->calMatAmtFixedDeposit($pv, $nper, $type, $growthRate);
        $recurringFv = $this->calculateMaturityAmount($payment, $payments, $type, $growthRate, $esRate);

        return response()->json(round($fixedFv + $recurringFv, 0));
    }

    public function doCalcRetirement(Request $request)
    {
        $growthRate = (float) $request->input('growth_rate', 0);
        $inflationRate = (float) $request->input('infrate', 0);
        $presentAge = (int) $request->input('present_age', 0);
        $retireAge = (int) $request->input('retire_age', 0);
        $spouseAge = (int) $request->input('spouse_age', 0);
        $spouseRetire = (int) $request->input('spouse_retire', 0);
        $otherIncome = (float) $request->input('other_inc', 0);
        $fvOtherIncome = (float) $request->input('fv_other_inc', 0);
        $spouseIncome = (float) $request->input('spouse_inc', 0);
        $fvSpouseIncome = (float) $request->input('fv_spouse_inc', 0);
        $homeCash = (float) $request->input('home_cash', 0);
        $fvHomeCash = (float) $request->input('fv_home_cash', 0);
        $fvPersonalLiabilities = (float) $request->input('fv_p_liabilities', 0);
        $capitalLiabilities = (float) $request->input('cap_liabilities', 0);
        $fvCapitalLiabilities = (float) $request->input('fv_cap_liabilities', 0);
        $taxValue = (float) $request->input('tax_val', 0);
        $fvTaxValue = (float) $request->input('fv_tax_val', 0);
        $insuranceTerm = (int) $request->input('income_req_period', 0);
        $sellCash = (float) $request->input('sell_cash', 0);
        $fvSellCash = (float) $request->input('fv_sell_cash', 0);
        $personalLiabilities = (float) $request->input('p_liabilities', 0);
        $monthlyContribution = (float) $request->input('monthly_cont', 0);
        $fvMonthlyContribution = (float) $request->input('fv_monthly_cont', 0);
        $retireFund = (float) $request->input('retire_fund', 0);
        $fvCapital = (float) $request->input('fv_capital', 0);
        $spouseInvestValue = (float) $request->input('sp_invest_val', 0);
        $fvSpouseEstateCapital = (float) $request->input('fv_es_capital', 0);
        $spouseMonthlyInvestment = (float) $request->input('sp_monthly_inv', 0);
        $fvSpouseInvestment = (float) $request->input('fv_sp_inv', 0);

        $clientId = $this->currentClientId(999999999999997);

        $incomeTerm = $presentAge > 0 ? $retireAge - ($presentAge - 1) : 0;
        $insuranceAge = $retireAge;
        $spouseIncomeTerm = 0;

        if ($spouseAge > 0) {
            $spouseIncomeTerm = $spouseRetire - ($spouseAge - 1);
            $spouseIncomeTerm -= $incomeTerm;
        }

        if ($insuranceTerm > 0) {
            $insuranceTerm += 1;
        }

        $availableIncome = [$fvOtherIncome, $fvSpouseIncome];
        $incomeFromYear = [1, 1];
        $incomeToYear = [$insuranceTerm, $spouseIncomeTerm];
        $incomeEscalation = [$inflationRate, $inflationRate];

        $availableExpense = [$fvHomeCash, $fvTaxValue];
        $expenseFromYear = [1, 1];
        $expenseToYear = [$insuranceTerm, $insuranceTerm];
        $expenseEscalation = [$inflationRate, $inflationRate];

        $availableAsset = [$fvMonthlyContribution, $fvCapital, $fvSpouseEstateCapital, $fvSpouseInvestment, $fvSellCash];
        $assetFromYear = [1, 1, 1, 1, 1];

        $spouseAssets = [$spouseInvestValue];
        $availableLiabilities = [$fvPersonalLiabilities, $fvCapitalLiabilities];
        $liabilityFromYear = [1, 1];
        $spouseLiabilities = [0];

        $totalIncome = $fvOtherIncome + $fvSpouseIncome;
        $totalIncomePv = $otherIncome + $spouseIncome;
        $totalExpense = $fvHomeCash + $fvTaxValue;
        $totalExpensePv = $homeCash + $taxValue;
        $initialCapital = $fvMonthlyContribution + $fvCapital + $fvSpouseEstateCapital + $fvSpouseInvestment + $fvSellCash;
        $initialCapitalPv = $monthlyContribution + $retireFund + $spouseInvestValue + $spouseMonthlyInvestment + $sellCash;
        $initialExpense = $fvPersonalLiabilities + $fvCapitalLiabilities;
        $initialExpensePv = $personalLiabilities + $capitalLiabilities;

        EstateCashFlow::where('clientid', $clientId)->where('type', 3)->delete();

        $reqTerm = 0;
        $capital = 0;
        $previousIncome = 0;
        $previousCapital = 0;

        for ($i = 0; $i < $insuranceTerm; $i++) {
            $incomeTotal = 0;
            $expenseTotal = 0;

            foreach ($availableIncome as $key => $value) {
                if (($incomeFromYear[$key] ?? 0) > 0) {
                    if ($incomeFromYear[$key] === ($i + 1)) {
                        $incomeTotal += $availableIncome[$key] = round($value, 2);
                    } elseif ($incomeFromYear[$key] <= ($i + 1) && ($incomeToYear[$key] ?? 0) >= ($i + 1)) {
                        $incomeTotal += $availableIncome[$key] = round($value + ($value * ($this->stripPercent($incomeEscalation[$key]) / 100)), 2);
                    }
                }
            }

            foreach ($availableExpense as $key => $value) {
                if ($value && ($expenseFromYear[$key] ?? 0) <= ($i + 1) && ($expenseToYear[$key] ?? 0) >= ($i + 1)) {
                    if (($expenseFromYear[$key] ?? 0) === ($i + 1)) {
                        $expenseTotal += round($value, 2);
                    } else {
                        $expenseTotal += $availableExpense[$key] = round($value + ($value * ($this->stripPercent($expenseEscalation[$key]) / 100)), 2);
                    }
                }
            }

            if ($i === 0) {
                $capital = 0;

                foreach ($availableAsset as $key => $value) {
                    if (($assetFromYear[$key] ?? 0) === ($i + 1)) {
                        $capital += round($value, 2);
                    }
                }

                foreach ($spouseAssets as $key => $value) {
                    if (($assetFromYear[$key] ?? 0) === ($i + 1)) {
                        $capital += round($value, 2);
                    }
                }

                foreach ($availableLiabilities as $key => $value) {
                    if (($liabilityFromYear[$key] ?? 0) === ($i + 1)) {
                        $capital -= round($value, 2);
                    }
                }

                foreach ($spouseLiabilities as $key => $value) {
                    if (($liabilityFromYear[$key] ?? 0) === ($i + 1)) {
                        $capital -= round($value, 2);
                    }
                }
            } else {
                $capital = ($previousCapital * ($this->stripPercent($growthRate) / 100)) + $previousCapital + $previousIncome;

                foreach ($availableAsset as $key => $value) {
                    if (($assetFromYear[$key] ?? 0) === ($i + 1)) {
                        $capital += round($value, 2);
                    }
                }

                foreach ($spouseAssets as $key => $value) {
                    if (($assetFromYear[$key] ?? 0) === ($i + 1)) {
                        $capital += round($value, 2);
                    }
                }

                foreach ($availableLiabilities as $key => $value) {
                    if (($liabilityFromYear[$key] ?? 0) === ($i + 1)) {
                        $capital -= round($value, 2);
                    }
                }

                foreach ($spouseLiabilities as $key => $value) {
                    if (($liabilityFromYear[$key] ?? 0) === ($i + 1)) {
                        $capital -= round($value, 2);
                    }
                }
            }

            $income = $incomeTotal;
            $requiredBudget = $expenseTotal;
            $previousIncome = ($income * 12) - ($requiredBudget * 12);
            $previousCapital = $capital;

            if ($capital >= 0) {
                $reqTerm = $i;
            }

            $this->storeCashFlowRow($clientId, 3, Auth::id() ?: 999999999999997, $insuranceAge + $i, $i + 1, $capital, $income, $requiredBudget, $insuranceTerm);
        }

        $futureDeficit = 0;
        if ($capital < 0) {
            $futureDeficit = $this->calcInvestVal($capital, $growthRate, $insuranceTerm - 1);
        }

        return response()->json([
            'req_term' => $reqTerm,
            'deficit' => $futureDeficit,
            'm_income' => $totalIncome,
            'm_expense' => $totalExpense,
            'init_capital' => $initialCapital,
            'init_expense' => $initialExpense,
            'req_deficit_pv' => 0,
            'm_income_pv' => $totalIncomePv,
            'm_expense_pv' => $totalExpensePv,
            'init_capital_pv' => $initialCapitalPv,
            'init_expense_pv' => $initialExpensePv,
            'msg' => '',
        ]);
    }

    public function doCalcDisability(Request $request)
    {
        $growthRate = (float) $request->input('growth_rate', 0);
        $inflationRate = (float) $request->input('infrate', 0);
        $insurancePolicy = (float) $request->input('ins_policy', 0);
        $waitPeriod = (string) $request->input('wait_period', 0);
        $annualIncrease = (float) $request->input('annual_inc', 0);
        $presentAge = (int) $request->input('present_age', 0);
        $incomeTerm = (int) $request->input('cease_age', 0);
        $otherIncome = (float) $request->input('other_inc', 0);
        $spouseIncome = (float) $request->input('spouse_inc', 0);
        $spouseRetire = (int) $request->input('spouse_retire', 0);
        $homeCash = (float) $request->input('home_cash', 0);
        $taxValue = (float) $request->input('tax_val', 0);
        $insuranceTerm = (int) $request->input('income_req_period', 0);
        $sellCash = (float) $request->input('sell_cash', 0);
        $personalLiabilities = (float) $request->input('p_liabilities', 0);
        $otherModification = (float) $request->input('other_modification', 0);
        $disabilityCover = (float) $request->input('dis_cover', 0);
        $investValue = (float) $request->input('invest_val', 0);
        $retireFund = (float) $request->input('retire_fund', 0);
        $spouseInvestValue = (float) $request->input('sp_invest_val', 0);
        $spouseRetireFund = (float) $request->input('sp_retire_fund', 0);

        $clientId = $this->currentClientId(999999999999998);

        if ($presentAge > 0) {
            $incomeTerm -= $presentAge;
        }
        $insuranceAge = $presentAge;

        if ($incomeTerm > $insuranceTerm) {
            $insuranceTerm = $incomeTerm;
        }

        if ($spouseRetire <= 0) {
            $spouseRetire = $insuranceTerm;
        }

        $insuranceFromYear = match ($waitPeriod) {
            '12' => 2,
            '24' => 3,
            default => 1,
        };

        $initialCapital = $sellCash + $disabilityCover + $investValue + $retireFund + $spouseInvestValue + $spouseRetireFund;
        $availableAsset = [$sellCash, $disabilityCover, $investValue, $retireFund];
        $assetFromYear = [1, 1, 1, 1];
        $spouseAssets = [$spouseInvestValue, $spouseRetireFund];

        $initialExpense = $personalLiabilities + $otherModification;
        $availableLiabilities = [$personalLiabilities, $otherModification];
        $spouseLiabilities = [0];
        $liabilityFromYear = [1, 1];

        $summaryIncome = $otherIncome;
        $totalIncome = $insurancePolicy + $otherIncome + $spouseIncome;
        $availableIncome = [$summaryIncome, $spouseIncome, $insurancePolicy];
        $incomeFromYear = [1, 1, $insuranceFromYear];
        $incomeToYear = [$insuranceTerm, $spouseRetire, $incomeTerm];
        $incomeEscalation = [$inflationRate, $inflationRate, $annualIncrease];

        $summaryExpense = $homeCash + $taxValue;
        $availableExpense = [$homeCash, $taxValue];
        $expenseFromYear = [1, 1];
        $expenseToYear = [$insuranceTerm, $insuranceTerm];
        $expenseEscalation = [$inflationRate, $inflationRate];

        EstateCashFlow::where('clientid', $clientId)->where('type', 2)->delete();

        $reqTerm = 0;
        $capital = 0;
        $previousIncome = 0;
        $previousCapital = 0;

        for ($i = 0; $i < $insuranceTerm; $i++) {
            $incomeTotal = 0;
            $expenseTotal = 0;

            foreach ($availableIncome as $key => $value) {
                if (($incomeFromYear[$key] ?? 0) > 0) {
                    if ($incomeFromYear[$key] === ($i + 1)) {
                        $incomeTotal += $availableIncome[$key] = round($value, 2);
                    } elseif ($incomeFromYear[$key] <= ($i + 1) && ($incomeToYear[$key] ?? 0) >= ($i + 1)) {
                        $incomeTotal += $availableIncome[$key] = round($value + ($value * ($this->stripPercent($incomeEscalation[$key]) / 100)), 2);
                    }
                }
            }

            foreach ($availableExpense as $key => $value) {
                if ($value && ($expenseFromYear[$key] ?? 0) <= ($i + 1) && ($expenseToYear[$key] ?? 0) >= ($i + 1)) {
                    if (($expenseFromYear[$key] ?? 0) === ($i + 1)) {
                        $expenseTotal += round($value, 2);
                    } else {
                        $expenseTotal += $availableExpense[$key] = round($value + ($value * ($this->stripPercent($expenseEscalation[$key]) / 100)), 2);
                    }
                }
            }

            if ($i === 0) {
                $capital = 0;

                foreach ($availableAsset as $key => $value) {
                    if (($assetFromYear[$key] ?? 0) === ($i + 1)) {
                        $capital += round($value, 2);
                    }
                }

                foreach ($spouseAssets as $key => $value) {
                    if (($assetFromYear[$key] ?? 0) === ($i + 1)) {
                        $capital += round($value, 2);
                    }
                }

                foreach ($availableLiabilities as $key => $value) {
                    if (($liabilityFromYear[$key] ?? 0) === ($i + 1)) {
                        $capital -= round($value, 2);
                    }
                }

                foreach ($spouseLiabilities as $key => $value) {
                    if (($liabilityFromYear[$key] ?? 0) === ($i + 1)) {
                        $capital -= round($value, 2);
                    }
                }
            } else {
                $capital = round(($previousCapital * ($this->stripPercent($growthRate) / 100)) + $previousCapital + $previousIncome, 2);

                foreach ($availableAsset as $key => $value) {
                    if (($assetFromYear[$key] ?? 0) === ($i + 1)) {
                        $capital += round($value, 2);
                    }
                }

                foreach ($spouseAssets as $key => $value) {
                    if (($assetFromYear[$key] ?? 0) === ($i + 1)) {
                        $capital += round($value, 2);
                    }
                }

                foreach ($availableLiabilities as $key => $value) {
                    if (($liabilityFromYear[$key] ?? 0) === ($i + 1)) {
                        $capital -= round($value, 2);
                    }
                }

                foreach ($spouseLiabilities as $key => $value) {
                    if (($liabilityFromYear[$key] ?? 0) === ($i + 1)) {
                        $capital -= round($value, 2);
                    }
                }
            }

            $income = $incomeTotal;
            $requiredBudget = $expenseTotal;
            $previousIncome = ($income * 12) - ($requiredBudget * 12);
            $previousCapital = $capital;

            if ($capital >= 0) {
                $reqTerm = $i + 1;
            }

            $this->storeCashFlowRow($clientId, 2, Auth::id() ?: 999999999999998, $insuranceAge + $i, $i + 1, $capital, $income, $requiredBudget, $insuranceTerm);
        }

        $presentValueDeficit = 0;
        if ($capital < 0) {
            $presentValueDeficit = $this->calcInvestVal($capital, $growthRate, $insuranceTerm);
        }

        return response()->json([
            'req_term' => $reqTerm,
            'deficit' => $presentValueDeficit,
            'm_income' => $totalIncome,
            'm_expense' => $summaryExpense,
            'init_capital' => $initialCapital,
            'init_expense' => $initialExpense,
            'msg' => '',
        ]);
    }

    private function doCalcEstate(array $input): array
    {
        $growthRate = (float) ($input['growth_rate'] ?? 0);
        $inflationRate = (float) ($input['infrate'] ?? 0);
        $monthlyIncome = (float) ($input['m_income'] ?? ($input['s_income'] ?? 0));
        $monthlyExpense = (float) ($input['m_expense'] ?? ($input['s_expense'] ?? 0));
        $incomeTerm = (int) ($input['income_term'] ?? 0);
        $insuranceAge = (int) ($input['ins_age'] ?? 0);
        $insuranceTerm = (int) ($input['ins_term'] ?? 0);
        $initialCapital = (float) ($input['init_capital'] ?? 0);
        $initialExpense = (float) ($input['init_expense'] ?? 0);

        if ($incomeTerm <= 0) {
            $incomeTerm = $insuranceTerm;
        }

        $clientId = $this->currentClientId(999999999999999);
        EstateCashFlow::where('clientid', $clientId)->where('type', 1)->delete();

        $reqTerm = 0;
        $capital = 0;
        $previousIncome = 0;
        $previousCapital = 0;
        $deficit = 0;

        for ($i = 0; $i < $insuranceTerm; $i++) {
            if ($i === 0) {
                $capital = $initialCapital - $initialExpense;
                $incomeTotal = $monthlyIncome;
                $expenseTotal = $monthlyExpense;
            } else {
                $capital = round(($previousCapital * ($this->stripPercent($growthRate) / 100)) + $previousCapital + $previousIncome, 2);

                if ($incomeTerm >= ($i + 1)) {
                    $incomeTotal = round($incomeTotal + ($incomeTotal * ($this->stripPercent($inflationRate) / 100)), 2);
                } else {
                    $incomeTotal = 0;
                }

                $expenseTotal = round($expenseTotal + ($expenseTotal * ($this->stripPercent($inflationRate) / 100)), 2);
            }

            $income = $incomeTotal;
            $requiredBudget = $expenseTotal;
            $previousIncome = ($income * 12) - ($requiredBudget * 12);
            $previousCapital = $capital;

            if ($capital >= 0) {
                $reqTerm = $i + 1;
            } else {
                $deficit += $requiredBudget;
            }

            $this->storeCashFlowRow($clientId, 1, Auth::id() ?: 999999999999999, $insuranceAge + $i, $i + 1, $capital, $income, $requiredBudget, $insuranceTerm);
        }

        $presentValueDeficit = 0;
        if ($capital < 0) {
            $presentValueDeficit = $this->calcInvestVal($capital, $growthRate, $insuranceTerm);
        }

        return [
            'req_term' => $reqTerm,
            'deficit' => $presentValueDeficit,
            'msg' => 'Ready.',
        ];
    }

    private function calculateTaxYear(
        int $taxYear,
        int $age,
        float $income,
        float $nonTaxableIncome,
        float $adjuster,
        float $deductibleContributions,
        float $budgetAfterTax
    ): array|false {
        $rebateRow = SsTaxRebate::where('tax_year', $taxYear)
            ->whereRaw('? between age_limit and age_limit_higher', [$age])
            ->first();

        if (!$rebateRow) {
            return false;
        }

        $grossTaxableIncome = $income - $nonTaxableIncome + $adjuster;
        $netTaxableIncome = $grossTaxableIncome - $deductibleContributions;

        if ($netTaxableIncome == 0.0) {
            return false;
        }

        $yearlyNet = $netTaxableIncome * 12;
        $taxSlab = SsTaxTable::where('tax_year', $taxYear)
            ->whereRaw('? between amt_start_range and amt_end_range', [$yearlyNet])
            ->first();

        if (!$taxSlab) {
            return false;
        }

        $rebate = round($rebateRow->rebate / 12);
        $taxPayable = round((($netTaxableIncome - round($taxSlab->amt_start_range / 12)) * (round($taxSlab->per_tax) / 100)) + round($taxSlab->fixed_tax / 12), 0);

        if ($taxPayable >= $rebate) {
            $taxPayable -= $rebate;
        } else {
            $taxPayable = 0;
        }

        $availableAfterTax = $grossTaxableIncome + $nonTaxableIncome - $taxPayable;
        $shortfall = $availableAfterTax - $budgetAfterTax;

        return [
            'gross_taxable_income' => $grossTaxableIncome,
            'net_taxable_income' => $netTaxableIncome,
            'tax_payable_raw' => $taxPayable,
            'available_after_tax' => $availableAfterTax,
            'shortfall' => $shortfall,
            'tax_rate' => round($taxPayable / $netTaxableIncome * 100) . '%',
        ];
    }

    private function currentClientId(int $fallback): int
    {
        return (int) (Auth::id() ?: request()->input('clientId') ?: request()->input('recId') ?: $fallback);
    }

    private function stripPercent(string|float|int $value): float
    {
        return (float) str_replace(['##PERCENT##', '%'], '', (string) $value);
    }

    private function calcInvestVal(float $futureValue, string|float|int $interestRate, int $years): int
    {
        $rate = $this->stripPercent($interestRate) / 100;
        $growth = $rate + 1;
        $initialValue = abs($futureValue);

        for ($i = 1; $i < $years; $i++) {
            $initialValue /= $growth;
        }

        return (int) round($initialValue);
    }

    private function storeCashFlowRow(int $clientId, int $type, int $userId, int $age, int $term, float $capital, float $income, float $requiredBudget, int $insuranceTerm): void
    {
        $row = new EstateCashFlow();
        $row->userid = $userId;
        $row->clientid = $clientId;
        $row->age = $age;
        $row->term = $term;
        $row->capital = 'R ' . number_format(round($capital), 0);
        $row->income = 'R ' . number_format(round($income), 0);
        $row->req_budget = 'R ' . number_format(round($requiredBudget), 0);
        $row->update_date = now();
        $row->ins_term = $insuranceTerm;
        $row->type = $type;
        $row->client_cap = 0.0;
        $row->spouse_cap = 0.0;
        $row->save();
    }

    private function fv(float $rate = 0, int $nper = 0, float $pmt = 0, float $pv = 0, int $type = 0): float|bool
    {
        if (!in_array($type, [0, 1], true)) {
            return false;
        }

        $rate /= 100;

        if ($rate != 0.0) {
            return round(abs(-$pv * pow(1 + $rate, $nper) - $pmt * (1 + $rate * $type) * (pow(1 + $rate, $nper) - 1) / $rate), 2);
        }

        return round(abs(-$pv - $pmt * $nper), 2);
    }

    private function calMatAmtFixedDeposit(float $principal, int $years, int $compoundFrequency, float $rate): float
    {
        $rate = $rate / 100 / $compoundFrequency;

        return $principal * pow((1 + $rate / $compoundFrequency), $years * $compoundFrequency);
    }

    private function calculateMaturityAmount(float $payment, int $months, int $compoundFrequency, float $rate, float $escalation = 0): float
    {
        $x = $this->calculateX($rate, $compoundFrequency);
        $years = $months / 12;
        $paymentValue = $payment;
        $payments = [];

        for ($year = 1; $year <= $years; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                if ($month === 1) {
                    if ($year === 1) {
                        $payments[1] = $paymentValue;
                    } else {
                        $payments[] = $paymentValue = round($paymentValue + ($paymentValue * $escalation / 100), 0);
                    }
                } else {
                    $payments[] = $paymentValue;
                }
            }
        }

        $index = 1;
        $finalTotal = 0;

        for ($year = 0; $year < $years; $year++) {
            if (isset($paymentValue)) {
                $paymentFv = $this->calMatAmtFixedDeposit($paymentValue, 1, $compoundFrequency, $rate);
            }

            $paymentValue = 0;
            for ($month = 1; $month <= 12; $month++) {
                $paymentValue += $payments[$index] * pow($x, $compoundFrequency * $this->calculateMonthsInYear($month));
                $index++;
            }

            if (isset($paymentFv)) {
                $paymentValue += $paymentFv;
            }

            $finalTotal = round($paymentValue, 0);
        }

        return round($finalTotal, 0);
    }

    private function calculateX(float $rate, int $compoundFrequency): float
    {
        return 1 + (($rate / 100) / $compoundFrequency);
    }

    private function calculateMonthsInYear(int $month): float
    {
        return $month / 12;
    }
}

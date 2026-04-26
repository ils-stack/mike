<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithBudgetAssetData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    use InteractsWithBudgetAssetData;

    public function current(Request $request)
    {
        if ($request->boolean('print')) {
            [$currIncomes, $totalArr] = $this->buildIncomeRows(1, true);
            [$currExpenses, $expenseTotal] = $this->buildExpenseRows(1, true);

            return Pdf::loadView('pdf.budgetCurrent_rep', [
                'client_nm' => $this->budgetAssetClientName(),
                'curr_incomes' => $currIncomes,
                'curr_expenses' => $currExpenses,
                'exp_tot' => $expenseTotal,
                'total_arr' => $totalArr,
                'budgetObj' => $this,
                'print' => true,
            ])->setPaper('a4', 'landscape')->download('current_budget_report.pdf');
        }

        return view('budgetCurrent');
    }

    public function estate()
    {
        return view('budgetEstate');
    }

    public function disability()
    {
        return view('budgetDisability');
    }

    public function retirement()
    {
        return view('budgetRetirement');
    }

    public function saveCurrentBudgets(Request $request): RedirectResponse
    {
        $this->upsertBudgetAssetFields(1, $request->all());

        return redirect('/budget');
    }

    public function saveCurrentExpenses(Request $request): RedirectResponse
    {
        $this->upsertBudgetAssetFields(1, $request->all());

        return redirect('/budget');
    }

    public function saveEstateBudgets(Request $request): RedirectResponse
    {
        $this->upsertBudgetAssetFields(2, $request->all());

        return redirect('/budget/estate');
    }

    public function saveEstateExpenses(Request $request): RedirectResponse
    {
        $this->upsertBudgetAssetFields(2, $request->all());

        return redirect('/budget/estate');
    }

    public function saveDisabilityBudgets(Request $request): RedirectResponse
    {
        $this->upsertBudgetAssetFields(3, $request->all());

        return redirect('/budget/disability');
    }

    public function saveDisabilityExpenses(Request $request): RedirectResponse
    {
        $this->upsertBudgetAssetFields(3, $request->all());

        return redirect('/budget/disability');
    }

    public function saveRetirementBudgets(Request $request): RedirectResponse
    {
        $this->upsertBudgetAssetFields(4, $request->all());

        return redirect('/budget/retirement');
    }

    public function saveRetirementExpenses(Request $request): RedirectResponse
    {
        $this->upsertBudgetAssetFields(4, $request->all());

        return redirect('/budget/retirement');
    }

    public function currentIncomeHeads(): JsonResponse
    {
        return response()->json($this->buildIncomeRows(1)[0]);
    }

    public function currentExpenseHeads(): JsonResponse
    {
        return response()->json($this->buildExpenseRows(1)[0]);
    }

    public function estateIncomeHeads(): JsonResponse
    {
        return response()->json($this->buildIncomeRows(2)[0]);
    }

    public function estateExpenseHeads(): JsonResponse
    {
        return response()->json($this->buildExpenseRows(2)[0]);
    }

    public function disabilityIncomeHeads(): JsonResponse
    {
        return response()->json($this->buildIncomeRows(3)[0]);
    }

    public function disabilityExpenseHeads(): JsonResponse
    {
        return response()->json($this->buildExpenseRows(3)[0]);
    }

    public function retirementIncomeHeads(): JsonResponse
    {
        return response()->json($this->buildIncomeRows(4)[0]);
    }

    public function retirementExpenseHeads(): JsonResponse
    {
        return response()->json($this->buildExpenseRows(4)[0]);
    }
}

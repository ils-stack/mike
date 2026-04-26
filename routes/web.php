<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BudgetAssetController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BladeController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ShortAssessmentAjaxController;
use App\Http\Controllers\ShortAssessmentController;

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::match(['get','post'], '/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Dashboard / Home
|--------------------------------------------------------------------------
*/
// Route::get('/', [BladeController::class, 'home'])->middleware('auth');
// Route::get('/dashboard', [BladeController::class, 'home'])->middleware('auth');

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntitiesController;
use App\Http\Controllers\PropertyDocsController;

Route::get('/', [DashboardController::class, 'home'])->middleware('auth');
Route::get('/dashboard', [DashboardController::class, 'home'])->middleware('auth');

Route::get('/holdings', [EntitiesController::class, 'getHome'])->middleware('auth');

Route::get('/statement','App\Http\Controllers\StatementController@getScreen')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Client & Personal Information
|--------------------------------------------------------------------------
*/
Route::get('/family-details', [BladeController::class, 'familyDetails'])->middleware('auth');
Route::post('/family-details', [BladeController::class, 'storeFamilyDetails'])->middleware('auth');
Route::get('/clients', [BladeController::class, 'clientList'])->middleware('auth');
Route::get('/notes-tasks', [BladeController::class, 'notesTasks'])->middleware('auth');
Route::post('/notes-tasks', [BladeController::class, 'storeNoteTask'])->middleware('auth');
Route::patch('/notes-tasks/{noteTask}', [BladeController::class, 'updateNoteTask'])->middleware('auth');
Route::patch('/notes-tasks/{noteTask}/list', [BladeController::class, 'updateNoteTaskList'])->middleware('auth');
Route::delete('/notes-tasks/{noteTask}', [BladeController::class, 'destroyNoteTask'])->middleware('auth');

/*
|--------------------------------------------------------------------------
| Financial Planning & Analysis
|--------------------------------------------------------------------------
*/
Route::get('/budget', [BudgetController::class, 'current'])->middleware('auth');
Route::get('/budget/current', [BudgetController::class, 'current'])->middleware('auth');
Route::post('/budget/update-budgets', [BudgetController::class, 'saveCurrentBudgets'])->middleware('auth');
Route::post('/budget/update-expenses', [BudgetController::class, 'saveCurrentExpenses'])->middleware('auth');
Route::get('/budget/estate', [BudgetController::class, 'estate'])->middleware('auth');
Route::post('/budget/update-est-budgets', [BudgetController::class, 'saveEstateBudgets'])->middleware('auth');
Route::post('/budget/update-expenses-est', [BudgetController::class, 'saveEstateExpenses'])->middleware('auth');
Route::get('/budget/disability', [BudgetController::class, 'disability'])->middleware('auth');
Route::post('/budget/update-dis-budgets', [BudgetController::class, 'saveDisabilityBudgets'])->middleware('auth');
Route::post('/budget/update-expenses-dis', [BudgetController::class, 'saveDisabilityExpenses'])->middleware('auth');
Route::get('/budget/retirement', [BudgetController::class, 'retirement'])->middleware('auth');
Route::post('/budget/update-rt-budgets', [BudgetController::class, 'saveRetirementBudgets'])->middleware('auth');
Route::post('/budget/update-expenses-rt', [BudgetController::class, 'saveRetirementExpenses'])->middleware('auth');
Route::get('/crm-assets', [BudgetAssetController::class, 'index'])->middleware('auth');
Route::post('/crm-assets', [BudgetAssetController::class, 'store'])->middleware('auth');
Route::get('/crm-assets/delete-asset/{bid}', [BudgetAssetController::class, 'destroy'])->middleware('auth');
Route::get('/crm-assets/print', [BudgetAssetController::class, 'print'])->middleware('auth');
Route::get('/getIncomeHeads', [BudgetController::class, 'currentIncomeHeads'])->middleware('auth')->name('getIncomeHeads.get');
Route::get('/getExpenseHeads', [BudgetController::class, 'currentExpenseHeads'])->middleware('auth')->name('getExpenseHeads.get');
Route::get('/getIncomeHeadsEst', [BudgetController::class, 'estateIncomeHeads'])->middleware('auth')->name('getIncomeHeadsEst.get');
Route::get('/getExpenseHeadsEst', [BudgetController::class, 'estateExpenseHeads'])->middleware('auth')->name('getExpenseHeadsEst.get');
Route::get('/getIncomeHeadsDis', [BudgetController::class, 'disabilityIncomeHeads'])->middleware('auth')->name('getIncomeHeadsDis.get');
Route::get('/getExpenseHeadsDis', [BudgetController::class, 'disabilityExpenseHeads'])->middleware('auth')->name('getExpenseHeadsDis.get');
Route::get('/getIncomeHeadsRt', [BudgetController::class, 'retirementIncomeHeads'])->middleware('auth')->name('getIncomeHeadsRt.get');
Route::get('/getExpenseHeadsRt', [BudgetController::class, 'retirementExpenseHeads'])->middleware('auth')->name('getExpenseHeadsRt.get');
Route::get('/calculators', [BladeController::class, 'calculators'])->middleware('auth');
Route::get('/reports', [BladeController::class, 'reports'])->middleware('auth');
Route::get('/reports/policy-schedule', [ReportsController::class, 'policySchedule'])->middleware('auth');

/*
|--------------------------------------------------------------------------
| Insurance, Policies & Compliance
|--------------------------------------------------------------------------
*/
Route::get('/policies', [BladeController::class, 'policies'])->middleware('auth');
Route::get('/compliance', [BladeController::class, 'compliance'])->middleware('auth');

/*
|--------------------------------------------------------------------------
| Documents & Digital Assets
|--------------------------------------------------------------------------
*/
Route::get('/documents', [PropertyDocsController::class, 'index'])->middleware('auth');
Route::get('/dropbox-assets', [BladeController::class, 'dropboxAssets'])->middleware('auth');
Route::get('/web-docs', [BladeController::class, 'webDocs'])->middleware('auth');
Route::get('/web-images', [BladeController::class, 'webImages'])->middleware('auth');
Route::get('/pdf-import', [BladeController::class, 'pdfImport'])->middleware('auth');

/*
|--------------------------------------------------------------------------
| Property Docs
|--------------------------------------------------------------------------
*/
Route::get('/ajax/property/{property}/docs', [PropertyDocsController::class, 'propertyDocs'])->middleware('auth');
Route::get('/ajax/unit/{id}/docs', [PropertyDocsController::class, 'unitDocs'])->middleware('auth');
Route::post('/documents/upload', [PropertyDocsController::class, 'upload'])->middleware('auth');
Route::get('/documents/{id}/preview', [PropertyDocsController::class, 'preview'])->name('documents.preview')->middleware('auth');
Route::get('/documents/{id}/download', [PropertyDocsController::class, 'download'])->name('documents.download')->middleware('auth');
Route::delete('/documents/{id}', [PropertyDocsController::class, 'destroy'])->middleware('auth');
Route::post('/ajax/property-docs/toggle', [PropertyDocsController::class, 'toggle'])->middleware('auth');

/*
|--------------------------------------------------------------------------
| Short Assessments
|--------------------------------------------------------------------------
*/
Route::get('/short-assessments', [ShortAssessmentController::class, 'estate'])->middleware('auth');
Route::get('/short-assessments/disabilty', [ShortAssessmentController::class, 'disability'])->middleware('auth');
Route::get('/short-assessments/retirement', [ShortAssessmentController::class, 'retirement'])->middleware('auth');
Route::get('/short-assessments/tax-calc', [ShortAssessmentController::class, 'tax'])->middleware('auth');
Route::get('/short-assessments/new-calc', [ShortAssessmentController::class, 'newTax'])->middleware('auth');

Route::get('/ajaxCashFlow', [ShortAssessmentAjaxController::class, 'ajaxCashFlow'])->middleware('auth')->name('ajaxCashFlow.post');
Route::get('/ajaxCashFlowRet', [ShortAssessmentAjaxController::class, 'ajaxCashFlowRt'])->middleware('auth')->name('ajaxCashFlowRet.post');
Route::get('/ajaxCashFlowDis', [ShortAssessmentAjaxController::class, 'ajaxCashFlowDis'])->middleware('auth')->name('ajaxCashFlowDis.post');

Route::post('/ajaxRequest', [ShortAssessmentAjaxController::class, 'ajaxRequestPost'])->middleware('auth')->name('ajaxRequest.post');
Route::post('/ajaxRequestRetire', [ShortAssessmentAjaxController::class, 'doCalcRetirement'])->middleware('auth')->name('ajaxRequestRetire.post');
Route::post('/ajaxRequestDis', [ShortAssessmentAjaxController::class, 'doCalcDisability'])->middleware('auth')->name('ajaxRequestDis.post');

Route::post('/ajaxPostedFv', [ShortAssessmentAjaxController::class, 'ajaxPostedFv'])->middleware('auth')->name('ajaxPostedFv.post');
Route::post('/ajaxPosted_fv_pv', [ShortAssessmentAjaxController::class, 'ajaxPostedFvPv'])->middleware('auth')->name('ajaxPosted_fv_pv.post');
Route::post('/ajaxPosted_simple_int', [ShortAssessmentAjaxController::class, 'postedSimpleInterest'])->middleware('auth')->name('ajaxPosted_simple_int.post');
Route::post('/ajaxTaxCalc', [ShortAssessmentAjaxController::class, 'taxCalc'])->middleware('auth')->name('ajaxTaxCalc.post');
Route::post('/ajaxTaxCalcTen', [ShortAssessmentAjaxController::class, 'taxCalcGraph'])->middleware('auth')->name('ajaxTaxCalcTen.post');

require __DIR__.'/source.routes.php';

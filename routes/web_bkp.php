<?php
use App\Http\Controllers\Auth\LoginController;

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('layouts.app');
// });

// ---- BA: login stuff
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
// ---- BA:  login stuff

Route::get('/database','App\Http\Controllers\Database@getScreen')->middleware('auth');
Route::get('/database-1','App\Http\Controllers\Database@getScreenOne')->middleware('auth');

// Route::get('/documents','App\Http\Controllers\Documents@getScreen')->middleware('auth');

// Route::get('/assets','App\Http\Controllers\AssetController@getScreen')->middleware('auth');

use App\Http\Controllers\AssetController;

Route::get('/asset-library', [AssetController::class, 'index'])->middleware('auth');
Route::post('/asset-library/upload', [AssetController::class, 'upload'])->middleware('auth');
Route::delete('/asset-library/{id}', [AssetController::class, 'destroy'])->middleware('auth');

Route::get('/vacancy-schedule','App\Http\Controllers\VacancySchedule@getScreen')->middleware('auth');
Route::get('/deals','App\Http\Controllers\Deals@getScreen')->middleware('auth');
Route::get('/leaderboard','App\Http\Controllers\Leaderboard@getScreen')->middleware('auth');

// BA: user admin
Route::get('/admin','App\Http\Controllers\Admin@getScreen')->middleware('auth');
Route::get('/admin/list','App\Http\Controllers\Admin@getScreen')->middleware('auth');
Route::get('/admin/edit/{id}','App\Http\Controllers\Admin@editUser')->middleware('auth');
Route::post('/admin/update/{id}','App\Http\Controllers\Admin@update')->middleware('auth');
Route::get('/admin/create','App\Http\Controllers\Admin@create')->middleware('auth');
Route::post('/admin/store','App\Http\Controllers\Admin@store')->middleware('auth');

// toggle user active status
Route::post('/admin/toggle-active/{id}', 'App\Http\Controllers\Admin@toggleActive')
    ->middleware('auth');

Route::post('/admin/update-role/{id}', 'App\Http\Controllers\Admin@updateRole')
    ->middleware('auth');

Route::post('/admin/delete/{id}', 'App\Http\Controllers\Admin@softDelete')
    ->middleware('auth');

// Route::get('/property-list','App\Http\Controllers\PropList@getScreen')->middleware('auth');
//
// Route::get('/property-details','App\Http\Controllers\PropList@propertyDetails')->middleware('auth');
// Route::get('/property-details/{id}','App\Http\Controllers\PropList@propertyDetails')->middleware('auth');
//
// // property listing & details
// Route::get('/properties', [PropList::class, 'getScreen']);
// Route::get('/property/{id}', [PropList::class, 'propertyDetails']);

use App\Http\Controllers\PropList;
use App\Http\Controllers\PropertyNotesController;

// Property listing
Route::get('/property-list', [PropList::class, 'getScreen'])->middleware('auth');
// Route::get('/property-details','App\Http\Controllers\PropList@propertyDetails')->middleware('auth');

// Property details (preferred URL naming)
Route::get('/property-details/{id}', [PropList::class, 'propertyDetails'])->middleware('auth');

// BA: assign assets to property
Route::get('/ajax/property/{property}/assets', [PropList::class, 'ajaxPropertyAssets'])
     ->middleware('auth');

 Route::post('/ajax/property/asset-toggle', [PropList::class, 'ajaxTogglePropertyAsset'])
      ->middleware('auth');

Route::get('/property/{id}/note', [PropertyNotesController::class, 'load']);
Route::post('/property/{id}/note/save', [PropertyNotesController::class, 'save']);

// AJAX endpoints (keep separate for clarity)
Route::get('/ajax/property/{id}', [PropList::class, 'ajaxGetProperty'])->middleware('auth');
Route::post('/ajax/property/save', [PropList::class, 'ajaxSaveProperty'])->middleware('auth');

// ajax endpoints for modal
// Route::get('/ajax/property/{id}', [App\Http\Controllers\PropList::class, 'ajaxGetProperty']);
// Route::post('/ajax/property/save', [App\Http\Controllers\PropList::class, 'ajaxSaveProperty']);

Route::get('/add-property','App\Http\Controllers\ManageProperties@getScreen')->middleware('auth');
// Route::get('/login','App\Http\Controllers\Login@getScreen')->middleware('auth');
Route::get('/vacancy','App\Http\Controllers\Vacancy@getScreen')->middleware('auth');
Route::get('/deals-1','App\Http\Controllers\Deals@getScreenOne')->middleware('auth');

// Landlord listing screen
Route::get('/landlords', [App\Http\Controllers\Landlords::class, 'getScreen'])
    ->middleware('auth');

// Tenant listing screen
// Route::get('/tenants', [App\Http\Controllers\TenantController::class, 'getScreen'])
//     ->middleware('auth');

// Tenant listing screen
Route::get('/tenants', [App\Http\Controllers\TenantController::class, 'getScreen'])
    ->middleware('auth');

// AJAX routes
Route::post('/ajax/tenant/save', [App\Http\Controllers\TenantController::class, 'ajaxSaveTenant'])
    ->middleware('auth');

Route::get('/ajax/tenant/{id}', [App\Http\Controllers\TenantController::class, 'ajaxGetTenant'])
    ->middleware('auth');

Route::delete('/ajax/tenant/{id}', [App\Http\Controllers\TenantController::class, 'ajaxDeleteTenant'])
    ->middleware('auth');

// Assign or update tenant-property links
Route::post('/ajax/tenant/{id}/assign', [App\Http\Controllers\TenantController::class, 'ajaxAssignProperties'])
    ->middleware('auth');

// Fetch existing linked properties for a tenant (used in editTenant)
Route::get('/ajax/tenant/{id}/properties', [App\Http\Controllers\TenantController::class, 'ajaxGetTenantProperties'])
    ->middleware('auth');

// Property Managers listing screen
Route::get('/property-managers', [App\Http\Controllers\PropertyManagerController::class, 'getScreen'])
    ->middleware('auth');

// AJAX endpoints
Route::post('/ajax/property-manager/save', [App\Http\Controllers\PropertyManagerController::class, 'ajaxSaveManager'])
    ->middleware('auth');

Route::get('/ajax/property-manager/{id}', [App\Http\Controllers\PropertyManagerController::class, 'ajaxGetManager'])
    ->middleware('auth');

Route::delete('/ajax/property-manager/{id}', [App\Http\Controllers\PropertyManagerController::class, 'ajaxDeleteManager'])
    ->middleware('auth');

// Agents listing screen
Route::get('/agents', [App\Http\Controllers\AgentController::class, 'getScreen'])
    ->middleware('auth');

// AJAX routes
Route::post('/ajax/agent/save', [App\Http\Controllers\AgentController::class, 'ajaxSaveAgent'])
    ->middleware('auth');

Route::get('/ajax/agent/{id}', [App\Http\Controllers\AgentController::class, 'ajaxGetAgent'])
    ->middleware('auth');

Route::delete('/ajax/agent/{id}', [App\Http\Controllers\AgentController::class, 'ajaxDeleteAgent'])
    ->middleware('auth');

// Assign or update properties for an agent
Route::post('/ajax/agent/{id}/assign', [App\Http\Controllers\AgentController::class, 'ajaxAssignProperties'])
    ->middleware('auth');

// Fetch linked properties for an agent
Route::get('/ajax/agent/{id}/properties', [App\Http\Controllers\AgentController::class, 'ajaxGetAgentProperties'])
    ->middleware('auth');


// Get single landlord (AJAX)
Route::get('/ajax/landlord/{id}', [App\Http\Controllers\Landlords::class, 'ajaxGetLandlord'])
    ->middleware('auth');

Route::delete('/ajax/landlord/{id}', [App\Http\Controllers\Landlords::class, 'ajaxDeleteLandlord'])
    ->middleware('auth');

// Save landlord (AJAX)
Route::post('/ajax/landlord/save', [App\Http\Controllers\Landlords::class, 'ajaxSaveLandlord'])
    ->name('ajax.landlord.save')
    ->middleware('auth');

use App\Http\Controllers\Landlords;
// use App\Http\Controllers\PropList;

// Assignments
// For property → landlords
Route::post('/ajax/property/{id}/landlords', [PropList::class, 'ajaxAssignLandlords'])->middleware('auth');

// For landlord → properties
Route::post('/ajax/landlord/{id}/properties', [Landlords::class, 'ajaxAssignProperties'])->middleware('auth');


// Route::get('/brochure','App\Http\Controllers\Brochure@getScreen')->middleware('auth');

/**
 * ---------------------------
 *  BROCHURE MODULE ROUTES
 * ---------------------------
 */

use App\Http\Controllers\BrochureController;

// Listing page
Route::get('/brochure', [BrochureController::class, 'index'])->name('brochure.index')->middleware('auth');

// Create brochure page (builder)
Route::get('/brochure/create', [BrochureController::class, 'create'])->name('brochure.create')->middleware('auth');

// Store final brochure (generate PDF)
Route::post('/brochure/store', [BrochureController::class, 'store'])->name('brochure.store')->middleware('auth');

// Delete brochure (AJAX)
Route::delete('/brochure/delete/{id}', [BrochureController::class, 'destroy'])->name('brochure.delete')->middleware('auth');

Route::get('/brochure/preview/{id}', [App\Http\Controllers\BrochureController::class, 'preview'])
     ->name('brochure.preview')->middleware('auth');

Route::get('/brochure/download/{id}', [App\Http\Controllers\BrochureController::class, 'download'])
   ->name('brochure.download')->middleware('auth');


// ----------------------
// AJAX Cart Functions
// ----------------------

// Add property to cart
Route::post('/brochure/cart/add', [BrochureController::class, 'cartAdd']);

// Remove property from cart
Route::post('/brochure/cart/remove', [BrochureController::class, 'cartRemove']);

// Get count for navbar
Route::get('/brochure/cart/count', [BrochureController::class, 'cartCount']);

// Clear cart
Route::post('/brochure/cart/clear', [BrochureController::class, 'cartClear']);

/**
 * ---------------------------
 *  BROCHURE MODULE ROUTES ENDs
 * ---------------------------
 */

Route::get('/dashboard','App\Http\Controllers\Home@getScreen')->middleware('auth');
Route::get('/','App\Http\Controllers\Home@getScreen')->middleware('auth');

// info box for the dashbaord
Route::get('/ajax/property/{id}/infobox', [App\Http\Controllers\Home::class, 'infobox']);

// right panel
Route::get('/ajax/properties-in-bounds', [App\Http\Controllers\Home::class, 'propertiesInBounds']);


Route::get('/search','App\Http\Controllers\Search@getScreen')->middleware('auth');

// AJAX controlers
Route::post('ajaxSaveProp', [App\Http\Controllers\AjaxController::class, 'ajaxSaveProp'])->name('ajaxSaveProp.post');
Route::post('ajaxSavePropertyManager', [App\Http\Controllers\AjaxController::class, 'ajaxSavePropertyManager'])->name('ajaxSavePropertyManager.post');

// Route::post('ajaxSaveTenant', [App\Http\Controllers\AjaxController::class, 'ajaxSaveTenant'])->name('ajaxSaveTenant.post');

// Unit listing screen
Route::get('/units', [App\Http\Controllers\UnitDetailController::class, 'getScreen'])->middleware('auth');

// AJAX
Route::post('/ajax/unit/save', [App\Http\Controllers\UnitDetailController::class, 'ajaxSaveUnit'])->middleware('auth');
Route::get('/ajax/unit/{id}', [App\Http\Controllers\UnitDetailController::class, 'ajaxGetUnit'])->middleware('auth');
Route::delete('/ajax/unit/{id}', [App\Http\Controllers\UnitDetailController::class, 'ajaxDeleteUnit'])->middleware('auth');

// Assign/Fetch relations
Route::post('/ajax/unit/{id}/properties', [App\Http\Controllers\UnitDetailController::class, 'ajaxAssignProperties'])->middleware('auth');

//unit gallery
Route::get('/ajax/unit/{id}/assets', [App\Http\Controllers\UnitDetailController::class, 'unitAssets']);

Route::post('/ajax/unit/asset-toggle', [App\Http\Controllers\UnitDetailController::class, 'toggleUnitAsset'])
    ->name('ajax.unit.asset.toggle');

Route::post('/ajax/unit/assets/sort', [AssetController::class, 'sortUnitAssets']);

// property masters
// Route::get('/master/property-types', [App\Http\Controllers\UnitDetailController::class, 'getScreen'])->middleware('auth');

use App\Http\Controllers\PropertyTypeController;

Route::get('/property-types', [PropertyTypeController::class, 'index']);
Route::post('/property-types', [PropertyTypeController::class, 'store']);
Route::put('/property-types/{id}', [PropertyTypeController::class, 'update']);
Route::delete('/property-types/{id}', [PropertyTypeController::class, 'destroy']);

// ----

use App\Http\Controllers\PropertyStatusController;

Route::get('/property-status', [PropertyStatusController::class, 'index']);
Route::post('/property-status', [PropertyStatusController::class, 'store']);
Route::put('/property-status/{id}', [PropertyStatusController::class, 'update']);
Route::delete('/property-status/{id}', [PropertyStatusController::class, 'destroy']);

// ----

use App\Http\Controllers\PropertyZoningController;

Route::get('/property-zoning', [PropertyZoningController::class, 'index']);
Route::post('/property-zoning', [PropertyZoningController::class, 'store']);
Route::put('/property-zoning/{id}', [PropertyZoningController::class, 'update']);
Route::delete('/property-zoning/{id}', [PropertyZoningController::class, 'destroy']);

// ----

use App\Http\Controllers\PropertyLocationController;

Route::get('/property-location', [PropertyLocationController::class, 'index']);
Route::post('/property-location', [PropertyLocationController::class, 'store']);
Route::put('/property-location/{id}', [PropertyLocationController::class, 'update']);
Route::delete('/property-location/{id}', [PropertyLocationController::class, 'destroy']);

// ----

use App\Http\Controllers\PropertyAreaController;

Route::get('/property-area', [PropertyAreaController::class, 'index']);
Route::post('/property-area', [PropertyAreaController::class, 'store']);
Route::put('/property-area/{id}', [PropertyAreaController::class, 'update']);
Route::delete('/property-area/{id}', [PropertyAreaController::class, 'destroy']);

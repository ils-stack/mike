<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdvisorController;

Route::get('/sync-advisors', [AdvisorController::class, 'sync']);
Route::get('/sync-all-clients', [AdvisorController::class, 'syncAllClients']);

Route::get('/advisors/{advisorCode}', [AdvisorController::class, 'details']);
Route::get('/advisors/{advisorCode}/clients', [AdvisorController::class, 'clientDetails']);
Route::get('/advisors/{advisorCode}/fee-report', [AdvisorController::class, 'feeReport']);

Route::get('/brokerages/{brokerageCode}/clients', [AdvisorController::class, 'brokerageClients']);
Route::get('/brokerages/{brokerageCode}/fee-report', [AdvisorController::class, 'brokerageFeeReport']);
Route::get('/brokerages/{brokerageCode}/fee-statement', [AdvisorController::class, 'brokerageFeeStatement']);

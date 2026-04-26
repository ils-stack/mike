<?php

use App\Http\Controllers\SourceController;

Route::get('/source', [SourceController::class, 'getScreen']);
Route::post('/source/upload-zip', [SourceController::class, 'uploadZip'])->name('source.uploadZip');
Route::get('/source/files', [SourceController::class, 'files'])->name('source.files');
Route::get('/source/csv-preview', [SourceController::class, 'csvPreview'])->name('source.csvPreview');
Route::get('/source/csv-raw-preview', [SourceController::class, 'rawCsvPreview'])->name('source.rawCsvPreview');
Route::post('/source/file-metadata', [SourceController::class, 'saveMetadata'])->name('source.fileMetadata');
Route::post('/source/file-delete', [SourceController::class, 'deleteMetadata'])->name('source.fileDelete');
Route::post('/source/import-csv', [SourceController::class, 'importCsv'])->name('source.importCsv');
Route::post('/source/import-column-map', [SourceController::class, 'mapImportColumn'])->name('source.importColumnMap');
Route::post('/source/import-rows-delete', [SourceController::class, 'deleteImportRows'])->name('source.importRowsDelete');
Route::get('/source/import-name-check', [SourceController::class, 'importNameCheck'])->name('source.importNameCheck');
Route::post('/source/source-alt-name', [SourceController::class, 'saveSourceAltName'])->name('source.sourceAltName');
Route::get('/source/import-post-comm-summary', [SourceController::class, 'importPostCommSummary'])->name('source.importPostCommSummary');
Route::post('/source/import-post-comm', [SourceController::class, 'postImportedCommission'])->name('source.importPostComm');
Route::post('/source/posted-comm-delete', [SourceController::class, 'deletePostedCommissionRecord'])->name('source.postedCommDelete');

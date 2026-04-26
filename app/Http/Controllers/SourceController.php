<?php

namespace App\Http\Controllers;

use App\Models\SourceFileMetadata;
use App\Models\SourceFileImportBatch;
use App\Models\SourceFileImportColumn;
use App\Models\SourceFileImportRow;
use App\Models\SourceAltName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Redirect;
use ZipArchive;

class SourceController extends Controller
{
    private $deletedSourcePathHashes = null;

    public function getScreen(Request $request)
    {
        if (!Auth::check()) {
            return Redirect::to('/login');
        }

        $providers = collect();
        $productsModel = $this->sourceProductsModel();

        if ($productsModel) {
            $providers = $productsModel::query()
                ->where('active', 1)
                ->orderBy('product_name', 'ASC')
                ->get(['id', 'product_name']);
        }

        return view('source.index', compact('providers'));
    }

    public function uploadZip(Request $request)
    {
        if (!Auth::check()) {
            return Redirect::to('/login');
        }

        $request->validate([
            'source_zip' => 'required|file|mimes:zip|max:204800',
        ]);

        $zipFile = $request->file('source_zip');
        $zip = new ZipArchive();

        if ($zip->open($zipFile->getRealPath()) !== true) {
            return redirect()->back()->with('error', 'Could not open the uploaded ZIP file.');
        }

        $zipName = pathinfo($zipFile->getClientOriginalName(), PATHINFO_FILENAME);
        $zipName = preg_replace('/[^A-Za-z0-9_-]+/', '-', $zipName);
        $zipName = trim($zipName, '-_') ?: 'source';

        $targetRoot = storage_path('app/public/source-admin/'.date('Ymd_His').'_'.$zipName);
        $fileCount = 0;
        $dirCount = 0;

        if (!is_dir($targetRoot)) {
            mkdir($targetRoot, 0755, true);
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entryName = $zip->getNameIndex($i);
            $relativePath = $this->normalizeZipPath($entryName);

            if ($relativePath === null) {
                $zip->close();

                return redirect()
                    ->back()
                    ->with('error', 'ZIP contains an unsafe path: '.$entryName);
            }

            $targetPath = $targetRoot.'/'.$relativePath;

            if (substr($entryName, -1) === '/') {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
                $dirCount++;
                continue;
            }

            $targetDir = dirname($targetPath);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $source = $zip->getStream($entryName);
            if ($source === false) {
                $zip->close();

                return redirect()
                    ->back()
                    ->with('error', 'Could not read a file from the ZIP: '.$entryName);
            }

            $target = fopen($targetPath, 'w');
            stream_copy_to_stream($source, $target);
            fclose($source);
            fclose($target);

            $fileCount++;
        }

        $zip->close();

        return redirect()
            ->back()
            ->with('success', 'ZIP extracted successfully.')
            ->with('extract_path', str_replace(storage_path('app/public').'/', 'storage/app/public/', $targetRoot))
            ->with('extract_files', $fileCount)
            ->with('extract_dirs', $dirCount);
    }

    public function files(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $root = storage_path('app/public/source-admin');
        if (!is_dir($root)) {
            mkdir($root, 0755, true);
        }

        $rootReal = realpath($root);
        $relativePath = $this->normalizeExplorerPath($request->get('path', ''));
        $targetPath = $rootReal.($relativePath ? '/'.$relativePath : '');
        $targetReal = realpath($targetPath);

        if ($targetReal === false || !$this->isPathInsideRoot($targetReal, $rootReal) || !is_dir($targetReal)) {
            return response()->json(['message' => 'Directory not found.'], 404);
        }

        $items = [];
        $flatten = filter_var($request->get('flatten', false), FILTER_VALIDATE_BOOLEAN);

        if ($flatten) {
            $items = $this->collectFlattenedCsvFiles($targetReal, $targetReal, $relativePath);
        } else {
            foreach (scandir($targetReal) as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }

                $fullPath = $targetReal.'/'.$entry;
                if (substr($entry, 0, 1) === '.' || (is_dir($fullPath) && substr($entry, 0, 1) === '_')) {
                    continue;
                }

                $itemRelativePath = trim(($relativePath ? $relativePath.'/' : '').$entry, '/');

                $items[] = [
                    'name' => $entry,
                    'type' => is_dir($fullPath) ? 'directory' : 'file',
                    'path' => $itemRelativePath,
                    'size' => is_file($fullPath) ? filesize($fullPath) : null,
                    'csv_count' => is_dir($fullPath) ? $this->countCsvFiles($fullPath, $itemRelativePath) : null,
                    'modified' => date('Y-m-d H:i:s', filemtime($fullPath)),
                ];
            }
        }

        usort($items, function ($a, $b) {
            if ($a['type'] !== $b['type']) {
                return $a['type'] === 'directory' ? -1 : 1;
            }

            return strcasecmp($a['name'], $b['name']);
        });

        $items = $this->attachSourceFileMetadata($items);

        return response()->json([
            'root' => 'storage/app/public/source-admin',
            'path' => $relativePath,
            'parent' => $this->parentExplorerPath($relativePath),
            'file_types' => $flatten ? ['csv'] : $this->collectFileTypes($targetReal),
            'flatten' => $flatten,
            'items' => $items,
        ]);
    }

    public function csvPreview(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $root = storage_path('app/public/source-admin');
        if (!is_dir($root)) {
            return response()->json(['message' => 'Source admin root does not exist.'], 404);
        }

        $rootReal = realpath($root);
        $relativePath = $this->normalizeExplorerPath($request->get('path', ''));
        $targetPath = $rootReal.($relativePath ? '/'.$relativePath : '');
        $targetReal = realpath($targetPath);

        if ($targetReal === false || !$this->isPathInsideRoot($targetReal, $rootReal) || !is_file($targetReal)) {
            return response()->json(['message' => 'CSV file not found.'], 404);
        }

        if (strtolower(pathinfo($targetReal, PATHINFO_EXTENSION)) !== 'csv') {
            return response()->json(['message' => 'Only CSV files can be previewed.'], 422);
        }

        $metadata = SourceFileMetadata::where('path_hash', hash('sha256', $relativePath))->first();
        if (!$metadata) {
            return response()->json(['message' => 'No imported data found. Please run Import before opening this file.'], 422);
        }

        $batch = SourceFileImportBatch::where('source_file_metadata_id', $metadata->id)
            ->orderBy('id', 'DESC')
            ->first();

        if (!$batch) {
            return response()->json(['message' => 'No imported data found. Please run Import before opening this file.'], 422);
        }

        $page = max((int) $request->get('page', 1), 1);
        $showAll = filter_var($request->get('show_all', false), FILTER_VALIDATE_BOOLEAN);

        return $this->importedCsvPreviewResponse($batch, $relativePath, basename($targetReal), $page, $showAll);
    }

    public function rawCsvPreview(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $root = storage_path('app/public/source-admin');
        if (!is_dir($root)) {
            return response()->json(['message' => 'Source admin root does not exist.'], 404);
        }

        $rootReal = realpath($root);
        $relativePath = $this->normalizeExplorerPath($request->get('path', ''));
        $targetPath = $rootReal.($relativePath ? '/'.$relativePath : '');
        $targetReal = realpath($targetPath);

        if ($targetReal === false || !$this->isPathInsideRoot($targetReal, $rootReal) || !is_file($targetReal)) {
            return response()->json(['message' => 'CSV file not found.'], 404);
        }

        if (strtolower(pathinfo($targetReal, PATHINFO_EXTENSION)) !== 'csv') {
            return response()->json(['message' => 'Only CSV files can be previewed.'], 422);
        }

        $handle = fopen($targetReal, 'r');
        if ($handle === false) {
            return response()->json(['message' => 'Could not open CSV file.'], 500);
        }

        $maxRows = 200;
        $rows = [];
        $rowCount = 0;
        $columnCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowCount++;
            $row = $this->cleanCsvRow($row, $rowCount === 1);
            $columnCount = max($columnCount, count($row));

            if (count($rows) < $maxRows) {
                $rows[] = $row;
            }
        }

        fclose($handle);

        return response()->json([
            'path' => $relativePath,
            'name' => basename($targetReal),
            'source' => 'raw',
            'rows' => $rows,
            'row_count' => $rowCount,
            'column_count' => $columnCount,
            'shown_rows' => count($rows),
            'truncated' => $rowCount > count($rows),
        ]);
    }

    private function importedCsvPreviewResponse($batch, $relativePath, $name, $page = 1, $showAll = false)
    {
        $perPage = 15;
        $columns = SourceFileImportColumn::where('batch_id', $batch->id)
            ->where('ignored', false)
            ->orderBy('column_index', 'ASC')
            ->get();

        $headers = $columns->map(function ($column) {
            return $column->display_name ?: $column->original_name ?: 'Column '.($column->column_index + 1);
        })->toArray();
        $columnIds = $columns->pluck('id')->toArray();
        $columnMappings = $columns->pluck('mapped_field')->toArray();

        $rowCount = SourceFileImportRow::where('batch_id', $batch->id)
            ->where('deleted', false)
            ->count();
        $totalPages = $showAll ? 1 : max((int) ceil($rowCount / $perPage), 1);
        $page = $showAll ? 1 : min(max((int) $page, 1), $totalPages);
        $offset = $showAll ? 0 : (($page - 1) * $perPage);
        $rows = [$headers];
        $rowIds = [null];
        $importRowsQuery = SourceFileImportRow::where('batch_id', $batch->id)
            ->where('deleted', false)
            ->orderBy('csv_row_number', 'ASC');

        if (!$showAll) {
            $importRowsQuery->offset($offset)->limit($perPage);
        }

        $importRows = $importRowsQuery->get();

        foreach ($importRows as $importRow) {
            $rowData = $importRow->row_data ?: [];
            $rowIds[] = $importRow->id;
            $rows[] = $columns->map(function ($column) use ($rowData) {
                $key = $column->original_name !== '' && $column->original_name !== null
                    ? $column->original_name
                    : 'Column '.($column->column_index + 1);

                if (!array_key_exists($key, $rowData)) {
                    $key = $key.' #'.($column->column_index + 1);
                }

                return $rowData[$key] ?? '';
            })->toArray();
        }

        $shownRows = max(count($rows) - 1, 0);

        return response()->json([
            'path' => $relativePath,
            'name' => $name,
            'source' => 'imported',
            'batch_id' => $batch->id,
            'rows' => $rows,
            'row_ids' => $rowIds,
            'column_ids' => $columnIds,
            'column_mappings' => $columnMappings,
            'row_count' => $rowCount,
            'column_count' => count($headers),
            'shown_rows' => $shownRows,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'from_row' => $rowCount > 0 ? $offset + 1 : 0,
            'to_row' => $offset + $shownRows,
            'has_previous_page' => $page > 1,
            'has_next_page' => $page < $totalPages,
            'truncated' => !$showAll && $page < $totalPages,
            'show_all' => $showAll,
            'can_delete_rows' => true,
            'can_map_columns' => true,
        ]);
    }

    public function mapImportColumn(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'batch_id' => 'required|integer|exists:source_file_import_batches,id',
            'column_id' => 'required|integer|exists:source_file_import_columns,id',
            'mapped_field' => 'nullable|in:1,2,3',
        ]);

        $column = SourceFileImportColumn::where('batch_id', $data['batch_id'])
            ->where('id', $data['column_id'])
            ->firstOrFail();

        DB::transaction(function () use ($data, $column) {
            $mappedField = $data['mapped_field'] ?? null;

            if ($mappedField !== null) {
                SourceFileImportColumn::where('batch_id', $data['batch_id'])
                    ->where('mapped_field', $mappedField)
                    ->where('id', '<>', $column->id)
                    ->update(['mapped_field' => null]);
            }

            $column->mapped_field = $mappedField;
            $column->save();
        });

        $mappings = SourceFileImportColumn::where('batch_id', $data['batch_id'])
            ->orderBy('column_index', 'ASC')
            ->pluck('mapped_field', 'id');

        return response()->json([
            'message' => 'Column mapping saved.',
            'mappings' => $mappings,
        ]);
    }

    public function deleteImportRows(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'batch_id' => 'required|integer|exists:source_file_import_batches,id',
            'row_ids' => 'required|array|min:1',
            'row_ids.*' => 'integer',
        ]);

        $deletedCount = SourceFileImportRow::where('batch_id', $data['batch_id'])
            ->whereIn('id', $data['row_ids'])
            ->where('deleted', false)
            ->update(['deleted' => true]);

        return response()->json([
            'message' => 'Rows deleted.',
            'deleted_count' => $deletedCount,
        ]);
    }

    public function importNameCheck(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'batch_id' => 'required|integer|exists:source_file_import_batches,id',
        ]);

        if (!$this->sourceBrokerTableAvailable()) {
            return response()->json([
                'needs_mapping' => false,
                'message' => 'Broker mapping is not available in this project yet. Missing `broker_master`.',
                'values' => [],
                'broker_names' => [],
                'existing_mappings' => [],
                'count' => 0,
            ], 422);
        }

        $providerColumn = SourceFileImportColumn::where('batch_id', $data['batch_id'])
            ->where('mapped_field', '1')
            ->first();

        if (!$providerColumn) {
            return response()->json([
                'needs_mapping' => true,
                'message' => 'Map a Provider column in Edit & Map before running Name Check.',
                'values' => [],
                'count' => 0,
            ]);
        }

        $providerKey = $providerColumn->original_name !== '' && $providerColumn->original_name !== null
            ? $providerColumn->original_name
            : 'Column '.($providerColumn->column_index + 1);
        $fallbackKey = $providerKey.' #'.($providerColumn->column_index + 1);
        $values = [];

        SourceFileImportRow::where('batch_id', $data['batch_id'])
            ->where('deleted', false)
            ->orderBy('csv_row_number', 'ASC')
            ->chunk(500, function ($rows) use (&$values, $providerKey, $fallbackKey) {
                foreach ($rows as $row) {
                    $rowData = $row->row_data ?: [];
                    $value = array_key_exists($providerKey, $rowData)
                        ? $rowData[$providerKey]
                        : ($rowData[$fallbackKey] ?? '');
                    $value = trim((string) $value);

                    if ($value !== '') {
                        $values[$value] = true;
                    }
                }
            });

        $values = array_keys($values);
        natcasesort($values);
        $values = array_values($values);
        $existingMappings = empty($values) ? [] : SourceAltName::whereIn('alt_name', $values)
            ->pluck('broker_name', 'alt_name')
            ->toArray();
        $brokerNames = DB::table('broker_master')
            ->whereNotNull('broker_name')
            ->where('broker_name', '<>', '')
            ->select(DB::raw('DISTINCT broker_name'))
            ->orderBy('broker_name', 'ASC')
            ->pluck('broker_name')
            ->map(function ($brokerName) {
                return trim((string) $brokerName);
            })
            ->filter()
            ->values()
            ->toArray();

        return response()->json([
            'needs_mapping' => false,
            'mapped_column' => $providerColumn->display_name ?: $providerColumn->original_name ?: 'Column '.($providerColumn->column_index + 1),
            'values' => $values,
            'broker_names' => $brokerNames,
            'existing_mappings' => $existingMappings,
            'count' => count($values),
        ]);
    }

    public function saveSourceAltName(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'alt_name' => 'required|string|max:255',
            'broker_name' => 'required|string|max:255',
        ]);

        if (!$this->sourceBrokerTableAvailable()) {
            return response()->json(['message' => 'Broker mapping is not available in this project yet. Missing `broker_master`.'], 422);
        }

        $brokerExists = DB::table('broker_master')
            ->where('broker_name', $data['broker_name'])
            ->exists();

        if (!$brokerExists) {
            return response()->json(['message' => 'Selected broker name was not found.'], 422);
        }

        $mapping = SourceAltName::updateOrCreate(
            ['alt_name' => trim($data['alt_name'])],
            ['broker_name' => trim($data['broker_name'])]
        );

        return response()->json([
            'message' => 'Provider mapping saved.',
            'mapping' => [
                'alt_name' => $mapping->alt_name,
                'broker_name' => $mapping->broker_name,
            ],
        ]);
    }

    public function importPostCommSummary(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'batch_id' => 'required|integer|exists:source_file_import_batches,id',
            'existing_page' => 'nullable|integer|min:1',
        ]);

        $summary = $this->buildPostCommSummary((int) $data['batch_id'], (int) ($data['existing_page'] ?? 1));

        return response()->json($summary);
    }

    public function postImportedCommission(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'batch_id' => 'required|integer|exists:source_file_import_batches,id',
            'keep_existing' => 'nullable|boolean',
        ]);

        if (!$this->sourcePostingDependenciesReady()) {
            return response()->json([
                'message' => 'Post Comm is not available in this project yet. Missing legacy posting models or tables.',
                'summary' => $this->buildPostCommSummary((int) $data['batch_id']),
            ], 422);
        }

        $summary = $this->buildPostCommSummary((int) $data['batch_id']);
        if (!$summary['can_post']) {
            return response()->json([
                'message' => 'Post Comm is not ready. Fix the listed issues first.',
                'summary' => $summary,
            ], 422);
        }

        $batch = SourceFileImportBatch::findOrFail($data['batch_id']);
        $metadata = SourceFileMetadata::findOrFail($batch->source_file_metadata_id);
        $providerColumn = SourceFileImportColumn::where('batch_id', $batch->id)->where('mapped_field', '1')->firstOrFail();
        $totalColumn = SourceFileImportColumn::where('batch_id', $batch->id)->where('mapped_field', '2')->firstOrFail();
        $providerKey = $this->sourceImportColumnKey($providerColumn);
        $providerFallbackKey = $providerKey.' #'.($providerColumn->column_index + 1);
        $totalKey = $this->sourceImportColumnKey($totalColumn);
        $totalFallbackKey = $totalKey.' #'.($totalColumn->column_index + 1);
        $commDate = sprintf('%04d-%02d-01', $metadata->year, $metadata->month);
        $newRegimeCsvName = $this->newRegimeCsvName($metadata);
        $altNames = SourceAltName::pluck('broker_name', 'alt_name')->toArray();
        $brokerIds = DB::table('broker_master')
            ->whereNotNull('broker_name')
            ->pluck('id', 'broker_name')
            ->toArray();
        $postedCount = 0;
        $postedTotal = 0;
        $deletedCount = 0;
        $keepExisting = filter_var($data['keep_existing'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $ssCommDtaModel = $this->sourceRequiredModel(\App\Models\SsCommDta::class);
        $dumperModel = $this->sourceRequiredModel(\App\Models\Dumper::class);
        $dumperRowAssignModel = $this->sourceRequiredModel(\App\Models\DumperRowAssign::class);

        DB::transaction(function () use ($metadata, $batch, $providerKey, $providerFallbackKey, $totalKey, $totalFallbackKey, $commDate, $newRegimeCsvName, $altNames, $brokerIds, $keepExisting, $ssCommDtaModel, $dumperModel, $dumperRowAssignModel, &$postedCount, &$postedTotal, &$deletedCount) {
            if (!$keepExisting) {
                $deletedCount = $ssCommDtaModel::query()->where('comm_dt', $commDate)
                    ->where('import_type', $metadata->provider_id)
                    ->where('active', 1)
                    ->update(['active' => 0]);
            }

            $dumperModel::query()->where('csv_name', $newRegimeCsvName)->delete();
            $dumperRowAssignModel::query()->where('csv_name', $newRegimeCsvName)->delete();

            $dumperRowAssignModel::query()->insert([
                [
                    'col_type' => 1,
                    'sel_col' => 1,
                    'csv_name' => $newRegimeCsvName,
                ],
                [
                    'col_type' => 2,
                    'sel_col' => 2,
                    'csv_name' => $newRegimeCsvName,
                ],
            ]);

            SourceFileImportRow::where('batch_id', $batch->id)
                ->where('deleted', false)
                ->orderBy('csv_row_number', 'ASC')
                ->chunk(500, function ($rows) use ($metadata, $providerKey, $providerFallbackKey, $totalKey, $totalFallbackKey, $commDate, $newRegimeCsvName, $altNames, $brokerIds, $ssCommDtaModel, $dumperModel, &$postedCount, &$postedTotal) {
                    foreach ($rows as $row) {
                        $rowData = $row->row_data ?: [];
                        $providerName = trim((string) $this->sourceImportRowValue($rowData, $providerKey, $providerFallbackKey));
                        $totalValue = $this->sourceImportRowValue($rowData, $totalKey, $totalFallbackKey);
                        $brokerName = $altNames[$providerName] ?? null;

                        $dumperModel::query()->insert($this->newRegimeDumperRow($metadata, $newRegimeCsvName, $providerName, $totalValue));

                        if (!$brokerName) {
                            continue;
                        }

                        $amount = $this->sourceImportNumber($totalValue);
                        $record = new $ssCommDtaModel();
                        $record->broker_id = $brokerIds[$brokerName] ?? 0;
                        $record->dumper_id = $row->id;
                        $record->broker_name = $brokerName;
                        $record->brokerage = '';
                        $record->amt = $amount;
                        $record->csv_name = $newRegimeCsvName;
                        $record->comm_dt = $commDate;
                        $record->import_type = $metadata->provider_id;
                        $record->percent = '';
                        $record->active = 1;
                        $record->save();

                        $postedCount++;
                        $postedTotal += $amount;
                    }
                });

            $metadata->processed_status = 1;
            $metadata->save();
        });

        return response()->json([
            'message' => 'Commission posted.',
            'posted_count' => $postedCount,
            'posted_total' => round($postedTotal, 2),
            'deleted_count' => $deletedCount,
            'summary' => $this->buildPostCommSummary((int) $data['batch_id']),
        ]);
    }

    public function deletePostedCommissionRecord(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        if (!$this->sourcePostingDependenciesReady()) {
            return response()->json(['message' => 'Posted commission records are not available in this project yet.'], 422);
        }

        $ssCommDtaModel = $this->sourceRequiredModel(\App\Models\SsCommDta::class);

        $updated = $ssCommDtaModel::query()->whereIn('id', $data['ids'])
            ->where('active', 1)
            ->update(['active' => 0]);

        return response()->json([
            'message' => 'Posted commission record deleted.',
            'deleted_count' => $updated,
        ]);
    }

    public function saveMetadata(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $currentYear = (int) date('Y');
        $data = $request->validate([
            'path' => 'required|string',
            'provider_id' => $this->sourceProviderValidationRules(),
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:'.($currentYear - 5).'|max:'.($currentYear + 2),
        ]);

        $root = storage_path('app/public/source-admin');
        if (!is_dir($root)) {
            return response()->json(['message' => 'Source admin root does not exist.'], 404);
        }

        $rootReal = realpath($root);
        $relativePath = $this->normalizeExplorerPath($data['path']);
        $targetPath = $rootReal.($relativePath ? '/'.$relativePath : '');
        $targetReal = realpath($targetPath);

        if ($targetReal === false || !$this->isPathInsideRoot($targetReal, $rootReal) || !is_file($targetReal)) {
            return response()->json(['message' => 'Source file not found.'], 404);
        }

        if (strtolower(pathinfo($targetReal, PATHINFO_EXTENSION)) !== 'csv') {
            return response()->json(['message' => 'Only CSV files can be assigned a month and year.'], 422);
        }

        $pathHash = hash('sha256', $relativePath);
        $metadata = SourceFileMetadata::firstOrNew(['path_hash' => $pathHash]);
        $metadata->fill([
            'name' => basename($targetReal),
            'provider_id' => $data['provider_id'] ?? null,
            'month' => $data['month'] ?? null,
            'path' => $relativePath,
            'path_hash' => $pathHash,
            'year' => $data['year'] ?? null,
        ]);

        if (!$metadata->exists) {
            $metadata->processed_status = 0;
            $metadata->deleted = false;
        }

        $metadata->save();

        return response()->json([
            'message' => 'Source file metadata saved.',
            'metadata' => [
                'name' => $metadata->name,
                'path' => $metadata->path,
                'provider_id' => $metadata->provider_id,
                'month' => $metadata->month,
                'year' => $metadata->year,
                'processed_status' => $metadata->processed_status,
                'deleted' => $metadata->deleted,
            ],
        ]);
    }

    public function deleteMetadata(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'path' => 'required|string',
        ]);

        $root = storage_path('app/public/source-admin');
        if (!is_dir($root)) {
            return response()->json(['message' => 'Source admin root does not exist.'], 404);
        }

        $rootReal = realpath($root);
        $relativePath = $this->normalizeExplorerPath($data['path']);
        $targetPath = $rootReal.($relativePath ? '/'.$relativePath : '');
        $targetReal = realpath($targetPath);

        if ($targetReal === false || !$this->isPathInsideRoot($targetReal, $rootReal) || !is_file($targetReal)) {
            return response()->json(['message' => 'Source file not found.'], 404);
        }

        $pathHash = hash('sha256', $relativePath);
        $metadata = SourceFileMetadata::firstOrNew(['path_hash' => $pathHash]);
        $metadata->fill([
            'name' => basename($targetReal),
            'path' => $relativePath,
            'path_hash' => $pathHash,
            'deleted' => true,
        ]);

        if (!$metadata->exists) {
            $metadata->processed_status = 0;
        }

        $metadata->save();

        return response()->json([
            'message' => 'Source file deleted from list.',
            'metadata' => [
                'name' => $metadata->name,
                'path' => $metadata->path,
                'provider_id' => $metadata->provider_id,
                'month' => $metadata->month,
                'year' => $metadata->year,
                'processed_status' => $metadata->processed_status,
                'deleted' => $metadata->deleted,
            ],
        ]);
    }

    public function importCsv(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $currentYear = (int) date('Y');
        $data = $request->validate([
            'path' => 'required|string',
            'provider_id' => $this->sourceProviderValidationRules(),
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:'.($currentYear - 5).'|max:'.($currentYear + 2),
        ]);

        $root = storage_path('app/public/source-admin');
        if (!is_dir($root)) {
            return response()->json(['message' => 'Source admin root does not exist.'], 404);
        }

        $rootReal = realpath($root);
        $relativePath = $this->normalizeExplorerPath($data['path']);
        $targetPath = $rootReal.($relativePath ? '/'.$relativePath : '');
        $targetReal = realpath($targetPath);

        if ($targetReal === false || !$this->isPathInsideRoot($targetReal, $rootReal) || !is_file($targetReal)) {
            return response()->json(['message' => 'Source file not found.'], 404);
        }

        if (strtolower(pathinfo($targetReal, PATHINFO_EXTENSION)) !== 'csv') {
            return response()->json(['message' => 'Only CSV files can be imported.'], 422);
        }

        $handle = fopen($targetReal, 'r');
        if ($handle === false) {
            return response()->json(['message' => 'Could not open CSV file.'], 500);
        }

        $headers = null;
        $rows = [];
        $rowNumber = 0;
        $columnCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $row = $this->cleanCsvRow($row, $rowNumber === 1);
            $columnCount = max($columnCount, count($row));

            if ($headers === null) {
                $headers = $row;
                continue;
            }

            $rows[] = [
                'row_number' => $rowNumber,
                'row' => $row,
            ];
        }

        fclose($handle);

        if ($headers === null) {
            return response()->json(['message' => 'This CSV file is empty.'], 422);
        }

        $headers = array_pad($headers, $columnCount, '');

        $batch = DB::transaction(function () use ($relativePath, $targetReal, $data, $headers, $rows, $columnCount) {
            $pathHash = hash('sha256', $relativePath);
            $metadata = SourceFileMetadata::firstOrNew(['path_hash' => $pathHash]);
            $metadata->fill([
                'name' => basename($targetReal),
                'path' => $relativePath,
                'path_hash' => $pathHash,
                'provider_id' => $data['provider_id'] ?? $metadata->provider_id,
                'month' => $data['month'] ?? $metadata->month,
                'year' => $data['year'] ?? $metadata->year,
                'deleted' => false,
            ]);

            if (!$metadata->exists) {
                $metadata->processed_status = 0;
            }

            $metadata->save();

            $batch = SourceFileImportBatch::create([
                'source_file_metadata_id' => $metadata->id,
                'status' => 'imported',
                'imported_at' => now(),
                'row_count' => count($rows),
                'column_count' => $columnCount,
            ]);

            foreach ($headers as $index => $header) {
                SourceFileImportColumn::create([
                    'batch_id' => $batch->id,
                    'column_index' => $index,
                    'original_name' => $header,
                    'display_name' => $header !== '' ? $header : 'Column '.($index + 1),
                    'ignored' => false,
                    'mapped_field' => null,
                ]);
            }

            foreach ($rows as $rowData) {
                $row = array_pad($rowData['row'], $columnCount, '');
                SourceFileImportRow::create([
                    'batch_id' => $batch->id,
                    'csv_row_number' => $rowData['row_number'],
                    'row_data' => $this->combineRowWithHeaders($headers, $row),
                    'deleted' => false,
                ]);
            }

            return $batch;
        });

        return response()->json([
            'message' => 'CSV imported.',
            'batch_id' => $batch->id,
            'row_count' => $batch->row_count,
            'column_count' => $batch->column_count,
        ]);
    }

    private function buildPostCommSummary($batchId, $existingPage = 1)
    {
        $batch = SourceFileImportBatch::findOrFail($batchId);
        $metadata = SourceFileMetadata::find($batch->source_file_metadata_id);
        $providerColumn = SourceFileImportColumn::where('batch_id', $batch->id)->where('mapped_field', '1')->first();
        $totalColumn = SourceFileImportColumn::where('batch_id', $batch->id)->where('mapped_field', '2')->first();
        $issues = [];

        if (!$metadata) {
            $issues[] = 'Source file metadata was not found.';
        }

        if ($metadata && !$metadata->provider_id) {
            $issues[] = 'Select a Provider for this source file in the file list.';
        }

        if ($metadata && (!$metadata->month || !$metadata->year)) {
            $issues[] = 'Select Month and Year for this source file in the file list.';
        }

        if (!$providerColumn) {
            $issues[] = 'Map a Provider column in Edit & Map.';
        }

        if (!$totalColumn) {
            $issues[] = 'Map a Total column in Edit & Map.';
        }

        $rowCount = SourceFileImportRow::where('batch_id', $batch->id)->where('deleted', false)->count();
        if ($rowCount === 0) {
            $issues[] = 'There are no imported rows to post.';
        }

        $mappedRows = 0;
        $missingProviderMappings = [];
        $postTotal = 0;
        $postableRowCount = 0;
        $skippedRowCount = 0;

        if ($providerColumn && $totalColumn) {
            $providerKey = $this->sourceImportColumnKey($providerColumn);
            $providerFallbackKey = $providerKey.' #'.($providerColumn->column_index + 1);
            $totalKey = $this->sourceImportColumnKey($totalColumn);
            $totalFallbackKey = $totalKey.' #'.($totalColumn->column_index + 1);
            $providerNames = [];
            $altMappings = SourceAltName::pluck('broker_name', 'alt_name')->toArray();

            SourceFileImportRow::where('batch_id', $batch->id)
                ->where('deleted', false)
                ->orderBy('csv_row_number', 'ASC')
                ->chunk(500, function ($rows) use (&$providerNames, &$postTotal, &$postableRowCount, &$skippedRowCount, $altMappings, $providerKey, $providerFallbackKey, $totalKey, $totalFallbackKey) {
                    foreach ($rows as $row) {
                        $rowData = $row->row_data ?: [];
                        $providerName = trim((string) $this->sourceImportRowValue($rowData, $providerKey, $providerFallbackKey));

                        if ($providerName !== '') {
                            $providerNames[$providerName] = true;
                        }

                        if ($providerName !== '' && isset($altMappings[$providerName]) && trim((string) $altMappings[$providerName]) !== '') {
                            $postTotal += $this->sourceImportNumber($this->sourceImportRowValue($rowData, $totalKey, $totalFallbackKey));
                            $postableRowCount++;
                        } else {
                            $skippedRowCount++;
                        }
                    }
                });

            $providerNames = array_keys($providerNames);
            $missingProviderMappings = array_values(array_filter($providerNames, function ($providerName) use ($altMappings) {
                return !isset($altMappings[$providerName]) || trim((string) $altMappings[$providerName]) === '';
            }));
            $mappedRows = max(count($providerNames) - count($missingProviderMappings), 0);
        }

        if ($providerColumn && $totalColumn && $rowCount > 0 && $postableRowCount === 0) {
            $issues[] = 'No rows have mapped provider names to post.';
        }

        if (!$this->sourcePostingDependenciesReady()) {
            $issues[] = 'Post Comm integration is not available in this project yet. Missing legacy posting models or tables.';
        }

        $commDate = $metadata && $metadata->month && $metadata->year
            ? sprintf('%04d-%02d-01', $metadata->year, $metadata->month)
            : null;
        $ssCommDtaModel = $this->sourcePostingDependenciesReady()
            ? $this->sourceRequiredModel(\App\Models\SsCommDta::class)
            : null;
        $existingCount = ($metadata && $commDate && $metadata->provider_id && $ssCommDtaModel)
            ? $ssCommDtaModel::query()->where('comm_dt', $commDate)
                ->where('import_type', $metadata->provider_id)
                ->where('active', 1)
                ->count()
            : 0;
        $existingPerPage = 15;
        $existingTotalPages = max((int) ceil($existingCount / $existingPerPage), 1);
        $existingPage = min(max((int) $existingPage, 1), $existingTotalPages);
        $existingOffset = ($existingPage - 1) * $existingPerPage;
        $existingRecords = ($metadata && $commDate && $metadata->provider_id && $ssCommDtaModel)
            ? $ssCommDtaModel::query()->where('comm_dt', $commDate)
                ->where('import_type', $metadata->provider_id)
                ->where('active', 1)
                ->orderBy('broker_name', 'ASC')
                ->offset($existingOffset)
                ->limit($existingPerPage)
                ->get(['id', 'broker_name', 'amt', 'csv_name', 'comm_dt', 'import_type'])
                ->map(function ($record) {
                    return [
                        'id' => $record->id,
                        'broker_name' => $record->broker_name,
                        'amt' => $record->amt,
                        'csv_name' => $record->csv_name,
                        'comm_dt' => $record->comm_dt,
                        'import_type' => $record->import_type,
                    ];
                })
                ->toArray()
            : [];

        return [
            'batch_id' => $batch->id,
            'can_post' => empty($issues),
            'issues' => $issues,
            'file_name' => $metadata ? $metadata->name : '',
            'provider_id' => $metadata ? $metadata->provider_id : null,
            'month' => $metadata ? $metadata->month : null,
            'year' => $metadata ? $metadata->year : null,
            'comm_dt' => $commDate,
            'row_count' => $rowCount,
            'mapped_provider_count' => $mappedRows,
            'missing_provider_count' => count($missingProviderMappings),
            'missing_provider_names' => array_slice($missingProviderMappings, 0, 20),
            'postable_row_count' => $postableRowCount,
            'skipped_row_count' => $skippedRowCount,
            'post_total' => round($postTotal, 2),
            'existing_count' => $existingCount,
            'existing_page' => $existingPage,
            'existing_per_page' => $existingPerPage,
            'existing_total_pages' => $existingTotalPages,
            'existing_from' => $existingCount > 0 ? $existingOffset + 1 : 0,
            'existing_to' => $existingOffset + count($existingRecords),
            'existing_has_previous_page' => $existingPage > 1,
            'existing_has_next_page' => $existingPage < $existingTotalPages,
            'existing_records' => $existingRecords,
            'mapped_columns' => [
                'provider' => $providerColumn ? ($providerColumn->display_name ?: $providerColumn->original_name ?: 'Column '.($providerColumn->column_index + 1)) : null,
                'total' => $totalColumn ? ($totalColumn->display_name ?: $totalColumn->original_name ?: 'Column '.($totalColumn->column_index + 1)) : null,
            ],
        ];
    }

    private function sourceImportColumnKey($column)
    {
        return $column->original_name !== '' && $column->original_name !== null
            ? $column->original_name
            : 'Column '.($column->column_index + 1);
    }

    private function sourceImportRowValue($rowData, $key, $fallbackKey)
    {
        return array_key_exists($key, $rowData) ? $rowData[$key] : ($rowData[$fallbackKey] ?? '');
    }

    private function sourceImportNumber($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return 0;
        }

        $isNegative = preg_match('/^\(.*\)$/', $value) === 1;
        $value = preg_replace('/[^0-9.\-]/', '', $value);
        $number = is_numeric($value) ? (float) $value : 0;

        return $isNegative ? -abs($number) : $number;
    }

    private function newRegimeCsvName($metadata)
    {
        return substr(sprintf('%02d%04d_new_regime_%s', $metadata->month, $metadata->year, $metadata->name), 0, 150);
    }

    private function newRegimeDumperRow($metadata, $csvName, $providerName, $totalValue)
    {
        $row = [];

        for ($index = 1; $index <= 45; $index++) {
            $row['row'.$index] = '';
        }

        $row['row1'] = substr((string) $providerName, 0, 150);
        $row['row2'] = substr((string) $totalValue, 0, 150);
        $row['csv_name'] = $csvName;
        $row['brokerage_id'] = 0;
        $row['month'] = sprintf('%02d', $metadata->month);
        $row['year'] = (string) $metadata->year;
        $row['import_type'] = $metadata->provider_id;
        $row['alt_name'] = '';
        $row['updated_at'] = date('Y-m-d');

        return $row;
    }

    private function normalizeZipPath($path)
    {
        $path = str_replace('\\', '/', $path);

        if ($path === '' || substr($path, 0, 1) === '/' || preg_match('/(^|\/)\.\.(\/|$)/', $path) || preg_match('/^[A-Za-z]:\//', $path)) {
            return null;
        }

        $path = trim($path, '/');

        return $path;
    }

    private function normalizeExplorerPath($path)
    {
        $path = str_replace('\\', '/', (string) $path);
        $parts = array_filter(explode('/', $path), function ($part) {
            return $part !== '' && $part !== '.';
        });

        $cleanParts = [];
        foreach ($parts as $part) {
            if ($part === '..') {
                return '';
            }

            $cleanParts[] = $part;
        }

        return implode('/', $cleanParts);
    }

    private function parentExplorerPath($path)
    {
        if ($path === '') {
            return null;
        }

        $parts = explode('/', $path);
        array_pop($parts);

        return implode('/', $parts);
    }

    private function isPathInsideRoot($path, $root)
    {
        return $path === $root || strpos($path, $root.'/') === 0;
    }

    private function collectFileTypes($directory)
    {
        $types = [];
        $entries = scandir($directory);

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $fullPath = $directory.'/'.$entry;

            if (substr($entry, 0, 1) === '.' || (is_dir($fullPath) && substr($entry, 0, 1) === '_')) {
                continue;
            }

            if (is_dir($fullPath)) {
                $types = array_merge($types, $this->collectFileTypes($fullPath));
                continue;
            }

            if (!is_file($fullPath)) {
                continue;
            }

            $extension = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
            $types[] = $extension !== '' ? $extension : '__no_ext';
        }

        $types = array_values(array_unique($types));
        sort($types);

        return $types;
    }

    private function countCsvFiles($directory, $relativePath = '')
    {
        $count = 0;
        $entries = scandir($directory);

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $fullPath = $directory.'/'.$entry;

            if (substr($entry, 0, 1) === '.' || (is_dir($fullPath) && substr($entry, 0, 1) === '_')) {
                continue;
            }

            if (is_dir($fullPath)) {
                $childRelativePath = trim(($relativePath ? $relativePath.'/' : '').$entry, '/');
                $count += $this->countCsvFiles($fullPath, $childRelativePath);
                continue;
            }

            $fileRelativePath = trim(($relativePath ? $relativePath.'/' : '').$entry, '/');
            if (
                is_file($fullPath) &&
                strtolower(pathinfo($entry, PATHINFO_EXTENSION)) === 'csv' &&
                !$this->isSourcePathDeleted($fileRelativePath)
            ) {
                $count++;
            }
        }

        return $count;
    }

    private function isSourcePathDeleted($path)
    {
        if ($this->deletedSourcePathHashes === null) {
            $this->deletedSourcePathHashes = array_flip(
                SourceFileMetadata::where('deleted', true)
                    ->pluck('path_hash')
                    ->toArray()
            );
        }

        return isset($this->deletedSourcePathHashes[hash('sha256', $path)]);
    }

    private function cleanCsvRow($row, $isFirstRow)
    {
        $row = array_map(function ($cell) {
            return is_string($cell) ? $cell : '';
        }, $row);

        if ($isFirstRow && isset($row[0])) {
            $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
        }

        return $row;
    }

    private function combineRowWithHeaders($headers, $row)
    {
        $data = [];

        foreach ($headers as $index => $header) {
            $key = trim($header) !== '' ? trim($header) : 'Column '.($index + 1);
            if (array_key_exists($key, $data)) {
                $key = $key.' #'.($index + 1);
            }

            $data[$key] = $row[$index] ?? '';
        }

        return $data;
    }

    private function collectFlattenedCsvFiles($directory, $baseDirectory, $baseRelativePath)
    {
        $items = [];
        $entries = scandir($directory);

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $fullPath = $directory.'/'.$entry;

            if (substr($entry, 0, 1) === '.' || (is_dir($fullPath) && substr($entry, 0, 1) === '_')) {
                continue;
            }

            if (is_dir($fullPath)) {
                $items = array_merge($items, $this->collectFlattenedCsvFiles($fullPath, $baseDirectory, $baseRelativePath));
                continue;
            }

            if (!is_file($fullPath) || strtolower(pathinfo($entry, PATHINFO_EXTENSION)) !== 'csv') {
                continue;
            }

            $relativeToBase = ltrim(str_replace($baseDirectory, '', $fullPath), '/');
            $itemRelativePath = trim(($baseRelativePath ? $baseRelativePath.'/' : '').$relativeToBase, '/');

            $items[] = [
                'name' => $relativeToBase,
                'type' => 'file',
                'path' => $itemRelativePath,
                'size' => filesize($fullPath),
                'csv_count' => null,
                'modified' => date('Y-m-d H:i:s', filemtime($fullPath)),
            ];
        }

        return $items;
    }

    private function attachSourceFileMetadata($items)
    {
        $paths = array_values(array_filter(array_map(function ($item) {
            return $item['type'] === 'file' ? $item['path'] : null;
        }, $items)));

        if (empty($paths)) {
            return $items;
        }

        $pathHashes = array_map(function ($path) {
            return hash('sha256', $path);
        }, $paths);

        $metadataByPath = SourceFileMetadata::whereIn('path_hash', $pathHashes)
            ->get()
            ->keyBy('path');
        $metadataIds = $metadataByPath->pluck('id')->toArray();
        $importedMetadataIds = empty($metadataIds) ? [] : array_flip(
            SourceFileImportBatch::whereIn('source_file_metadata_id', $metadataIds)
                ->pluck('source_file_metadata_id')
                ->toArray()
        );
        $latestBatches = empty($metadataIds) ? collect() : SourceFileImportBatch::whereIn('source_file_metadata_id', $metadataIds)
            ->orderBy('id', 'DESC')
            ->get()
            ->unique('source_file_metadata_id')
            ->keyBy('source_file_metadata_id');
        $batchIds = $latestBatches->pluck('id')->toArray();
        $mappedFieldCounts = empty($batchIds) ? [] : SourceFileImportColumn::whereIn('batch_id', $batchIds)
            ->whereIn('mapped_field', ['1', '2'])
            ->select('batch_id', DB::raw('COUNT(DISTINCT mapped_field) as mapped_count'))
            ->groupBy('batch_id')
            ->pluck('mapped_count', 'batch_id')
            ->toArray();

        $visibleItems = [];

        foreach ($items as $item) {
            if ($item['type'] !== 'file' || !$metadataByPath->has($item['path'])) {
                $item['metadata'] = null;
                $item['imported'] = false;
                $visibleItems[] = $item;
                continue;
            }

            $metadata = $metadataByPath->get($item['path']);
            if ($metadata->deleted) {
                continue;
            }

            $item['metadata'] = [
                'provider_id' => $metadata->provider_id,
                'month' => $metadata->month,
                'year' => $metadata->year,
                'processed_status' => $metadata->processed_status,
                'deleted' => $metadata->deleted,
                'imported' => isset($importedMetadataIds[$metadata->id]),
                'columns_mapped' => isset($latestBatches[$metadata->id])
                    ? (($mappedFieldCounts[$latestBatches[$metadata->id]->id] ?? 0) >= 2)
                    : false,
            ];
            $item['imported'] = $item['metadata']['imported'];

            $visibleItems[] = $item;
        }

        return $visibleItems;
    }

    private function sourceProductsModel(): ?string
    {
        return $this->sourceOptionalModel(\App\Models\Products::class, ['ss_products']);
    }

    private function sourceProviderValidationRules(): array
    {
        $rules = ['nullable', 'integer'];

        if ($this->sourceProductsModel()) {
            $rules[] = Rule::exists('ss_products', 'id');
        }

        return $rules;
    }

    private function sourceBrokerTableAvailable(): bool
    {
        return Schema::hasTable('broker_master');
    }

    private function sourcePostingDependenciesReady(): bool
    {
        return $this->sourceBrokerTableAvailable()
            && $this->sourceOptionalModel(\App\Models\SsCommDta::class, ['ss_comm_dta']) !== null
            && $this->sourceOptionalModel(\App\Models\Dumper::class, ['dumper']) !== null
            && $this->sourceOptionalModel(\App\Models\DumperRowAssign::class, ['dumper_row_assign']) !== null;
    }

    private function sourceOptionalModel(string $className, array $requiredTables = []): ?string
    {
        if (!class_exists($className)) {
            return null;
        }

        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                return null;
            }
        }

        return $className;
    }

    private function sourceRequiredModel(string $className, array $requiredTables = []): string
    {
        $resolved = $this->sourceOptionalModel($className, $requiredTables);

        if ($resolved === null) {
            abort(422, 'Required source integration is missing.');
        }

        return $resolved;
    }
}

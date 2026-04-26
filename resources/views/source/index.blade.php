@extends('layouts.app')

@section('content')

<div class="card p-4">
  <div class="card-header py-3">
    <h5 class="mb-0 text-center"><strong>Source Admin</strong></h5>
  </div>

  <div class="card-body">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">
        {{ $errors->first() }}
      </div>
    @endif

    <ul class="nav nav-tabs mb-3" id="sourceAdminTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="source-files-tab" data-bs-toggle="tab" data-bs-target="#source-files" type="button" role="tab" aria-controls="source-files" aria-selected="true">
          Source Files
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="upload-zip-tab" data-bs-toggle="tab" data-bs-target="#upload-zip" type="button" role="tab" aria-controls="upload-zip" aria-selected="false">
          Upload ZIP
        </button>
      </li>
    </ul>

    <div class="tab-content" id="sourceAdminTabsContent">
      <div class="tab-pane fade show active" id="source-files" role="tabpanel" aria-labelledby="source-files-tab">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
          <div>
            <div class="fw-bold">Root: source-admin</div>
            <nav aria-label="Source folder path">
              <ol class="breadcrumb mb-0 small" id="sourceExplorerBreadcrumb">
                <li class="breadcrumb-item active" aria-current="page">/</li>
              </ol>
            </nav>
          </div>
          <div class="mt-2 mt-md-0">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="sourceExplorerBack" title="Back" aria-label="Back">
              <i class="fas fa-chevron-left"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="sourceExplorerForward" title="Forward" aria-label="Forward">
              <i class="fas fa-chevron-right"></i>
            </button>
            <select class="form-select form-select-sm d-inline-block w-auto" id="sourceExplorerTypeFilter" aria-label="Filter by file type">
              <option value="all">All file types</option>
            </select>
            <div class="form-check form-check-inline ms-2">
              <input class="form-check-input" type="checkbox" id="sourceExplorerOnlyCsv">
              <label class="form-check-label small" for="sourceExplorerOnlyCsv">Only CSV files</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="sourceExplorerFlatten">
              <label class="form-check-label small" for="sourceExplorerFlatten">Flatten</label>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="sourceExplorerRefresh" title="Refresh" aria-label="Refresh">
              <i class="fas fa-sync-alt"></i>
            </button>
          </div>
        </div>
        <div class="small mb-3" id="sourceExplorerLegend" aria-label="Source status filters">
          <button type="button" class="btn btn-link btn-sm p-0 source-legend-filter active" data-status="all">All</button>
          <button type="button" class="btn btn-link btn-sm p-0 ms-2 source-legend-filter" data-status="ready"><span class="source-legend-box source-legend-ready"></span> Ready for posting</button>
          <button type="button" class="btn btn-link btn-sm p-0 ms-2 source-legend-filter" data-status="processed"><span class="source-legend-box source-legend-processed"></span> Processed</button>
          <button type="button" class="btn btn-link btn-sm p-0 ms-2 source-legend-filter" data-status="missing-meta"><span class="source-legend-box source-legend-missing"></span> Provider/month/year not selected</button>
          <button type="button" class="btn btn-link btn-sm p-0 ms-2 source-legend-filter" data-status="unmapped-columns"><span class="source-legend-box source-legend-unmapped"></span> Unmapped columns</button>
          <div class="form-check form-check-inline ms-3 mb-0">
            <input class="form-check-input" type="checkbox" id="sourceExplorerHideProcessed" checked>
            <label class="form-check-label small" for="sourceExplorerHideProcessed">Hide Processed</label>
          </div>
          <div class="d-inline-flex align-items-center ms-2">
            <label class="form-label small mb-0 me-2" for="sourceExplorerProviderFilter">Provider</label>
            <select class="form-select form-select-sm w-auto" id="sourceExplorerProviderFilter" aria-label="Filter by provider">
              <option value="all">All providers</option>
              @foreach(($providers ?? []) as $provider)
                <option value="{{ $provider->id }}">{{ $provider->product_name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle" id="sourceExplorerTable">
            <colgroup>
              <col class="source-name-col">
              <col class="source-type-col">
              <col class="source-csv-count-col">
              <col class="source-size-col">
              <col class="source-modified-col">
              <col class="source-provider-col">
              <col class="source-month-col">
              <col class="source-year-col">
              <col class="source-action-col">
            </colgroup>
            <thead>
              <tr>
                <th class="source-name-col">Name</th>
                <th class="source-type-col">Type</th>
                <th class="text-end source-csv-count-col" title="CSV Files">CSV</th>
                <th class="text-end source-size-col">Size</th>
                <th class="source-modified-col">Modified</th>
                <th class="source-provider-col">Provider</th>
                <th class="source-month-col">Month</th>
                <th class="source-year-col">Year</th>
                <th class="source-action-col">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="9" class="text-muted">Loading source files...</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="form-text">Double click a directory to open it, or double click a CSV file to preview it.</div>
      </div>

      <div class="tab-pane fade" id="upload-zip" role="tabpanel" aria-labelledby="upload-zip-tab">
        <form method="POST" action="{{ route('source.uploadZip') }}" enctype="multipart/form-data">
          @csrf

          <div class="row">
            <div class="col-md-8">
              <label class="form-label" for="source_zip">ZIP file</label>
              <input type="file" class="form-control" id="source_zip" name="source_zip" accept=".zip" required>
              <div class="form-text">
                Files will be extracted to storage/app/public/source-admin and the ZIP directory structure will be preserved.
              </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
              <button type="submit" class="btn btn-primary w-100">Upload and Extract</button>
            </div>
          </div>
        </form>

        @if(session('extract_path'))
          <div class="alert alert-info mt-3 mb-0">
            <div><strong>Extracted to:</strong> {{ session('extract_path') }}</div>
            <div><strong>Files:</strong> {{ session('extract_files') }} | <strong>Directories:</strong> {{ session('extract_dirs') }}</div>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<style>
  #sourceExplorerTable .source-name-col {
    width: 520px;
  }

  #sourceExplorerTable .source-type-col {
    width: 90px;
  }

  #sourceExplorerTable .source-csv-count-col {
    width: 74px;
  }

  #sourceExplorerTable .source-size-col {
    width: 82px;
  }

  #sourceExplorerTable .source-modified-col {
    width: 142px;
  }

  #sourceExplorerTable .source-provider-col {
    width: 205px;
  }

  #sourceExplorerTable .source-month-col {
    width: 94px;
  }

  #sourceExplorerTable .source-year-col {
    width: 86px;
  }

  #sourceExplorerTable .source-action-col {
    width: 154px;
  }

  #sourceExplorerTable .source-file-name {
    display: inline-block;
    max-width: 600px;
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: middle;
    white-space: nowrap;
  }

  #sourceExplorerTable .source-cell-clip {
    display: inline-block;
    max-width: 9ch;
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: middle;
    white-space: nowrap;
  }

  #sourceExplorerTable td {
    white-space: nowrap;
  }

  #sourceExplorerTable th {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  #sourceExplorerTable .source-file-provider {
    width: 100%;
  }

  #sourceExplorerTable .source-file-month {
    width: 86px;
  }

  #sourceExplorerTable .source-file-year {
    width: 78px;
  }

  #sourceExplorerTable .source-action-cell {
    width: 154px;
  }

  #csvPreviewModal .modal-dialog,
  #rawCsvPreviewModal .modal-dialog {
    max-width: 98vw;
  }

  #csvPreviewTable,
  #rawCsvPreviewTable {
    font-size: 11px;
  }

  #csvPreviewTable th,
  #csvPreviewTable td,
  #rawCsvPreviewTable th,
  #rawCsvPreviewTable td {
    padding: 0.2rem 0.35rem;
    white-space: nowrap;
  }

  #csvPreviewTable .source-import-header-row th,
  #csvPreviewTable .source-import-header-row td {
    color: #198754;
    font-weight: 700;
  }

  #csvPreviewTable .source-import-select-cell,
  #csvPreviewTable .source-import-delete-cell {
    text-align: center;
    width: 36px;
  }

  #csvPreviewTable .source-column-label {
    margin-bottom: 0.25rem;
  }

  #csvPreviewTable .source-column-map {
    min-width: 130px;
  }

  #csvPreviewTable .source-column-map-mapped {
    background-color: #d1e7dd;
    border-color: #198754;
    color: #0f5132;
    font-weight: 700;
  }

  .source-import-pagination {
    align-items: center;
    display: flex;
    gap: 0.25rem;
  }

  .source-import-page-select {
    width: 92px;
  }

  .source-broker-search {
    position: relative;
  }

  .source-broker-search-menu {
    background: #fff;
    border: 1px solid #ced4da;
    border-radius: 6px;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.12);
    display: none;
    left: 0;
    max-height: 220px;
    overflow-y: auto;
    position: absolute;
    right: 0;
    top: calc(100% + 2px);
    z-index: 1090;
  }

  .source-broker-search-menu.show {
    display: block;
  }

  .source-broker-option {
    background: transparent;
    border: 0;
    display: block;
    padding: 0.35rem 0.5rem;
    text-align: left;
    width: 100%;
  }

  .source-broker-option:hover,
  .source-broker-option:focus {
    background: #e9f5ef;
    outline: 0;
  }

  .source-alt-name-mapped-row td {
    background-color: #cfe2ff;
  }

  .source-existing-records-table {
    font-size: 11px;
  }

  .source-existing-records-table th,
  .source-existing-records-table td {
    padding: 0.2rem 0.35rem;
    white-space: nowrap;
  }

  .source-legend-box {
    border: 1px solid rgba(0, 0, 0, 0.15);
    display: inline-block;
    height: 11px;
    margin-right: 0.25rem;
    vertical-align: -1px;
    width: 18px;
  }

  .source-legend-filter {
    color: #495057;
    text-decoration: none;
  }

  .source-legend-filter.active {
    font-weight: 700;
    text-decoration: underline;
  }

  .source-legend-ready {
    background: #d1e7dd;
  }

  .source-legend-processed {
    background: #cfe2ff;
  }

  .source-legend-missing {
    background: #fff3cd;
  }

  .source-legend-unmapped {
    background: #ffe5d0;
  }

  #sourceExplorerTable tr.source-row-ready > td {
    background-color: #d1e7dd;
  }

  #sourceExplorerTable tr.source-row-processed > td {
    background-color: #cfe2ff;
  }

  #sourceExplorerTable tr.source-row-missing-meta > td {
    background-color: #fff3cd;
  }

  #sourceExplorerTable tr.source-row-unmapped-columns > td {
    background-color: #ffe5d0;
  }
</style>

<div class="modal fade" id="csvPreviewModal" tabindex="-1" aria-labelledby="csvPreviewModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="csvPreviewModalLabel">CSV Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs mb-3" id="csvImportTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="csv-edit-map-tab" data-bs-toggle="tab" data-bs-target="#csv-edit-map-pane" type="button" role="tab" aria-controls="csv-edit-map-pane" aria-selected="true">
              Edit &amp; Map
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="csv-name-check-tab" data-bs-toggle="tab" data-bs-target="#csv-name-check-pane" type="button" role="tab" aria-controls="csv-name-check-pane" aria-selected="false">
              Name Check
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="csv-post-comm-tab" data-bs-toggle="tab" data-bs-target="#csv-post-comm-pane" type="button" role="tab" aria-controls="csv-post-comm-pane" aria-selected="false">
              Post Comm
            </button>
          </li>
        </ul>

        <div class="tab-content" id="csvImportTabsContent">
          <div class="tab-pane fade show active" id="csv-edit-map-pane" role="tabpanel" aria-labelledby="csv-edit-map-tab">
            <div class="small text-muted mb-2" id="csvPreviewMeta"></div>
            <div class="d-flex justify-content-between align-items-center mb-2 d-none source-import-row-actions" id="csvPreviewTopActions">
              <span class="small text-muted source-import-selected-count">0 rows selected</span>
              <div class="source-import-pagination">
                <div class="form-check form-check-inline mb-0 align-self-center">
                  <input class="form-check-input source-import-show-all" type="checkbox">
                  <label class="form-check-label small">Show all</label>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm source-import-page-prev" title="Previous page" aria-label="Previous page">
                  <i class="fas fa-chevron-left"></i>
                </button>
                <span class="small text-muted align-self-center source-import-page-status">Page 1 of 1</span>
                <select class="form-select form-select-sm source-import-page-select" aria-label="Jump to page"></select>
                <button type="button" class="btn btn-outline-secondary btn-sm source-import-page-next" title="Next page" aria-label="Next page">
                  <i class="fas fa-chevron-right"></i>
                </button>
              </div>
              <button type="button" class="btn btn-outline-danger btn-sm source-delete-selected-import-rows">
                <i class="fas fa-trash-can me-1"></i>Delete selected
              </button>
            </div>
            <div class="table-responsive">
              <table class="table table-sm table-bordered" id="csvPreviewTable">
                <tbody>
                  <tr>
                    <td class="text-muted">No CSV loaded.</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2 d-none source-import-row-actions" id="csvPreviewBottomActions">
              <span class="small text-muted source-import-selected-count">0 rows selected</span>
              <div class="source-import-pagination">
                <div class="form-check form-check-inline mb-0 align-self-center">
                  <input class="form-check-input source-import-show-all" type="checkbox">
                  <label class="form-check-label small">Show all</label>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm source-import-page-prev" title="Previous page" aria-label="Previous page">
                  <i class="fas fa-chevron-left"></i>
                </button>
                <span class="small text-muted align-self-center source-import-page-status">Page 1 of 1</span>
                <select class="form-select form-select-sm source-import-page-select" aria-label="Jump to page"></select>
                <button type="button" class="btn btn-outline-secondary btn-sm source-import-page-next" title="Next page" aria-label="Next page">
                  <i class="fas fa-chevron-right"></i>
                </button>
              </div>
              <button type="button" class="btn btn-outline-danger btn-sm source-delete-selected-import-rows">
                <i class="fas fa-trash-can me-1"></i>Delete selected
              </button>
            </div>
          </div>
          <div class="tab-pane fade" id="csv-name-check-pane" role="tabpanel" aria-labelledby="csv-name-check-tab">
            <div id="csvNameCheckContent">
              <div class="text-muted">Open this tab after mapping a Provider column.</div>
            </div>
          </div>
          <div class="tab-pane fade" id="csv-post-comm-pane" role="tabpanel" aria-labelledby="csv-post-comm-tab">
            <div id="csvPostCommContent">
              <div class="text-muted">Open this tab after completing Edit &amp; Map and Name Check.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="rawCsvPreviewModal" tabindex="-1" aria-labelledby="rawCsvPreviewModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rawCsvPreviewModalLabel">Raw CSV Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="small text-muted mb-2" id="rawCsvPreviewMeta"></div>
        <div class="table-responsive">
          <table class="table table-sm table-bordered" id="rawCsvPreviewTable">
            <tbody>
              <tr>
                <td class="text-muted">No CSV loaded.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;" id="sourceToastContainer"></div>

@endsection

@push('page-js')
<script>
$(document).ready(function() {
    let currentPath = '';
    let parentPath = null;
    let historyStack = [''];
    let historyIndex = 0;
    let currentItems = [];
    let currentImportedPreviewPath = null;
    let currentImportedBatchId = null;
    let currentImportedPage = 1;
    let currentImportedShowAll = false;
    let sourceStatusFilter = 'all';
    let sourceProviderFilter = 'all';
    const currentYear = {{ (int) date('Y') }};
    const yearOptions = [];
    const providerOptions = @json($providers ?? []);
    const monthOptions = [
        { value: 1, label: 'Jan' },
        { value: 2, label: 'Feb' },
        { value: 3, label: 'Mar' },
        { value: 4, label: 'Apr' },
        { value: 5, label: 'May' },
        { value: 6, label: 'Jun' },
        { value: 7, label: 'Jul' },
        { value: 8, label: 'Aug' },
        { value: 9, label: 'Sep' },
        { value: 10, label: 'Oct' },
        { value: 11, label: 'Nov' },
        { value: 12, label: 'Dec' }
    ];

    for (let year = currentYear - 5; year <= currentYear + 2; year++) {
        yearOptions.push(year);
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function showSourceToast(message, type) {
        type = type || 'success';

        const bgClass = type === 'danger' ? 'bg-danger' : (type === 'warning' ? 'bg-warning text-dark' : 'bg-success');
        const $toast = $('<div class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true"></div>');
        const $body = $('<div class="d-flex"></div>');
        const $message = $('<div class="toast-body"></div>').text(message);
        const $close = $('<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>');

        $toast.addClass(bgClass);
        if (type === 'warning') {
            $close.removeClass('btn-close-white');
        }

        $body.append($message);
        $body.append($close);
        $toast.append($body);
        $('#sourceToastContainer').append($toast);

        const toast = new bootstrap.Toast($toast[0], { delay: 3000 });
        $toast.on('hidden.bs.toast', function() {
            $toast.remove();
        });
        toast.show();
    }

    function formatSize(bytes) {
        if (bytes === null || bytes === undefined) return '';
        if (bytes < 1024) return bytes + ' B';

        const units = ['KB', 'MB', 'GB'];
        let size = bytes / 1024;
        let unitIndex = 0;

        while (size >= 1024 && unitIndex < units.length - 1) {
            size = size / 1024;
            unitIndex++;
        }

        return size.toFixed(2) + ' ' + units[unitIndex];
    }

    function getFileType(name) {
        const lastDot = name.lastIndexOf('.');

        if (lastDot <= 0 || lastDot === name.length - 1) {
            return '__no_ext';
        }

        return name.substring(lastDot + 1).toLowerCase();
    }

    function getFileTypeLabel(type) {
        return type === '__no_ext' ? 'No extension' : type.toUpperCase();
    }

    function isCsvFile(item) {
        return item.type === 'file' && getFileType(item.name) === 'csv';
    }

    function buildProviderSelect(item) {
        const selectedProviderId = item.metadata && item.metadata.provider_id ? parseInt(item.metadata.provider_id, 10) : '';
        const $select = $('<select class="form-select form-select-sm source-file-provider" aria-label="Source provider"></select>');

        $select.attr('data-path', item.path);
        $select.append('<option value="">Provider</option>');

        providerOptions.forEach(function(provider) {
            const $option = $('<option></option>').val(provider.id).text(provider.product_name);
            if (selectedProviderId === parseInt(provider.id, 10)) {
                $option.prop('selected', true);
            }
            $select.append($option);
        });

        return $select;
    }

    function buildMonthSelect(item) {
        const selectedMonth = item.metadata && item.metadata.month ? parseInt(item.metadata.month, 10) : '';
        const $select = $('<select class="form-select form-select-sm source-file-month" aria-label="Source month"></select>');

        $select.attr('data-path', item.path);
        $select.append('<option value="">Month</option>');

        monthOptions.forEach(function(month) {
            const $option = $('<option></option>').val(month.value).text(month.label);
            if (selectedMonth === month.value) {
                $option.prop('selected', true);
            }
            $select.append($option);
        });

        return $select;
    }

    function buildYearSelect(item) {
        const selectedYear = item.metadata && item.metadata.year ? parseInt(item.metadata.year, 10) : '';
        const $select = $('<select class="form-select form-select-sm source-file-year" aria-label="Source year"></select>');

        $select.attr('data-path', item.path);
        $select.append('<option value="">Year</option>');

        yearOptions.forEach(function(year) {
            const $option = $('<option></option>').val(year).text(year);
            if (selectedYear === year) {
                $option.prop('selected', true);
            }
            $select.append($option);
        });

        return $select;
    }

    function updateCurrentItemMetadata(path, metadata) {
        currentItems = currentItems.map(function(item) {
            if (item.path === path) {
                item.metadata = {
                    provider_id: metadata.provider_id,
                    month: metadata.month,
                    year: metadata.year,
                    processed_status: metadata.processed_status,
                    deleted: metadata.deleted,
                    imported: metadata.imported,
                    columns_mapped: metadata.columns_mapped
                };
                item.imported = metadata.imported;
            }

            return item;
        });
    }

    function markCurrentItemImported(path) {
        currentItems = currentItems.map(function(item) {
            if (item.path === path) {
                item.imported = true;
                item.metadata = item.metadata || {};
                item.metadata.imported = true;
            }

            return item;
        });
    }

    function saveSourceFileMetadata(path, $row) {
        const providerId = $row.find('.source-file-provider').val();
        const month = $row.find('.source-file-month').val();
        const year = $row.find('.source-file-year').val();

        $row.find('select').prop('disabled', true);

        $.post("{{ route('source.fileMetadata') }}", {
            path: path,
            provider_id: providerId || null,
            month: month || null,
            year: year || null
        })
            .done(function(response) {
                updateCurrentItemMetadata(path, response.metadata);
                renderRows(currentItems);
                showSourceToast('Saved');
            })
            .fail(function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Save failed';
                showSourceToast(message, 'danger');
            })
            .always(function() {
                $row.find('select').prop('disabled', false);
            });
    }

    function deleteSourceFileMetadata(path, $row) {
        if (!window.confirm('Remove this file from the list?')) {
            return;
        }

        const $deleteButton = $row.find('.source-delete-file');
        $deleteButton.prop('disabled', true);

        $.post("{{ route('source.fileDelete') }}", {
            path: path
        })
            .done(function() {
                currentItems = currentItems.filter(function(item) {
                    return item.path !== path;
                });
                renderRows(currentItems);
            })
            .fail(function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Delete failed';
                alert(message);
            })
            .always(function() {
                $deleteButton.prop('disabled', false);
            });
    }

    function importSourceCsv(path, $row) {
        const providerId = $row.find('.source-file-provider').val();
        const month = $row.find('.source-file-month').val();
        const year = $row.find('.source-file-year').val();
        const $importButton = $row.find('.source-import-csv');

        $importButton.prop('disabled', true);
        showSourceToast('Importing...', 'warning');

        $.post("{{ route('source.importCsv') }}", {
            path: path,
            provider_id: providerId || null,
            month: month || null,
            year: year || null
        })
            .done(function(response) {
                markCurrentItemImported(path);
                renderRows(currentItems);
                showSourceToast('Imported ' + response.row_count + ' rows');
            })
            .fail(function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Import failed';
                showSourceToast(message, 'danger');
            })
            .always(function() {
                $importButton.prop('disabled', false);
            });
    }

    function updateTypeFilter(types) {
        const selectedType = $('#sourceExplorerTypeFilter').val() || 'all';
        types = types || [];

        const $filter = $('#sourceExplorerTypeFilter');
        $filter.empty();
        $filter.append('<option value="all">All file types</option>');

        types.forEach(function(type) {
            $filter.append($('<option></option>').val(type).text(getFileTypeLabel(type)));
        });

        $filter.val(types.includes(selectedType) ? selectedType : 'all');
    }

    function getSourceItemStatus(item) {
        if (!isCsvFile(item) || !(item.imported || (item.metadata && item.metadata.imported))) {
            return 'none';
        }

        const metadata = item.metadata || {};
        const hasRequiredMetadata = Boolean(metadata.provider_id && metadata.month && metadata.year);

        if (parseInt(metadata.processed_status || 0, 10) === 1) {
            return 'processed';
        }

        if (!hasRequiredMetadata) {
            return 'missing-meta';
        }

        if (!metadata.columns_mapped) {
            return 'unmapped-columns';
        }

        return 'ready';
    }

    function renderRows(items) {
        const $tbody = $('#sourceExplorerTable tbody');
        const onlyCsv = $('#sourceExplorerOnlyCsv').is(':checked');
        const flatten = $('#sourceExplorerFlatten').is(':checked');
        const hideProcessed = $('#sourceExplorerHideProcessed').is(':checked');
        const selectedProvider = sourceProviderFilter || 'all';
        const selectedType = onlyCsv || flatten ? 'csv' : ($('#sourceExplorerTypeFilter').val() || 'all');
        const visibleItems = items.filter(function(item) {
            const itemStatus = getSourceItemStatus(item);
            const itemProviderId = item.metadata && item.metadata.provider_id ? String(item.metadata.provider_id) : '';

            if (flatten) {
                if (!(item.type === 'file' && getFileType(item.name) === 'csv')) {
                    return false;
                }
            } else if (!(item.type === 'directory' || selectedType === 'all' || getFileType(item.name) === selectedType)) {
                return false;
            }

            if (hideProcessed && sourceStatusFilter !== 'processed' && itemStatus === 'processed') {
                return false;
            }

            if (selectedProvider !== 'all' && itemProviderId !== selectedProvider) {
                return false;
            }

            return sourceStatusFilter === 'all' || itemStatus === sourceStatusFilter;
        });

        $tbody.empty();

        if (parentPath !== null) {
            const $parentRow = $('<tr class="source-parent-row" style="cursor:pointer;"></tr>');
            $parentRow.append('<td><i class="fas fa-level-up-alt fa-fw me-2 text-secondary"></i>..</td>');
            $parentRow.append('<td>Directory</td>');
            $parentRow.append('<td class="text-end"></td>');
            $parentRow.append('<td class="text-end"></td>');
            $parentRow.append('<td></td>');
            $parentRow.append('<td></td>');
            $parentRow.append('<td></td>');
            $parentRow.append('<td></td>');
            $parentRow.append('<td></td>');
            $tbody.append($parentRow);
        }

        if (!visibleItems.length && parentPath === null) {
            $tbody.append('<tr><td colspan="9" class="text-muted">This directory is empty.</td></tr>');
            return;
        }

        if (!visibleItems.length) {
            $tbody.append('<tr><td colspan="9" class="text-muted">No files match this filter.</td></tr>');
            return;
        }

        visibleItems.forEach(function(item) {
            const icon = item.type === 'directory' ? 'fa-folder text-warning' : 'fa-file text-secondary';
            const typeLabel = item.type === 'directory' ? 'Directory' : 'File';
            const $row = $('<tr class="source-file-row" style="cursor:pointer;"></tr>');
            const isImported = item.imported || (item.metadata && item.metadata.imported);

            $row.attr('data-type', item.type);
            $row.attr('data-path', item.path);
            const itemStatus = getSourceItemStatus(item);
            if (itemStatus === 'processed') {
                $row.addClass('source-row-processed');
            } else if (itemStatus === 'missing-meta') {
                $row.addClass('source-row-missing-meta');
            } else if (itemStatus === 'unmapped-columns') {
                $row.addClass('source-row-unmapped-columns');
            } else if (itemStatus === 'ready') {
                $row.addClass('source-row-ready');
            }
            $row.append('<td><i class="fas ' + icon + ' fa-fw me-2"></i><span class="source-file-name" title="' + $('<div>').text(item.name).html() + '">' + $('<div>').text(item.name).html() + '</span></td>');
            $row.append('<td><span class="source-cell-clip" title="' + typeLabel + '">' + typeLabel + '</span></td>');
            const csvCountText = item.csv_count !== null && item.csv_count !== undefined ? String(item.csv_count) : '';
            $row.append('<td class="text-end"><span class="source-cell-clip" title="' + csvCountText + '">' + csvCountText + '</span></td>');
            $row.append('<td class="text-end">' + formatSize(item.size) + '</td>');
            $row.append('<td>' + item.modified + '</td>');

            const $providerCell = $('<td></td>');
            const $monthCell = $('<td></td>');
            const $yearCell = $('<td></td>');
            const $actionCell = $('<td class="source-action-cell"></td>');
            if (isCsvFile(item)) {
                const $previewButton = $('<button type="button" class="btn btn-outline-secondary btn-sm source-preview-csv" title="Preview raw CSV" aria-label="Preview raw CSV"><i class="fas fa-search"></i></button>');
                const $openButton = $('<button type="button" class="btn btn-outline-primary btn-sm ms-2 source-open-csv" title="Open imported data" aria-label="Open imported data"><i class="fas fa-edit"></i></button>');
                $openButton.attr('data-path', item.path);
                $previewButton.attr('data-path', item.path);

                $providerCell.append(buildProviderSelect(item));
                $monthCell.append(buildMonthSelect(item));
                $yearCell.append(buildYearSelect(item));
                $actionCell.append($previewButton);
                $actionCell.append($openButton);
                if (!isImported) {
                    const $importButton = $('<button type="button" class="btn btn-outline-success btn-sm ms-2 source-import-csv">Import</button>');
                    $importButton.attr('data-path', item.path);
                    $actionCell.append($importButton);
                }
            }
            if (item.type === 'file') {
                const $deleteButton = $('<button type="button" class="btn btn-outline-danger btn-sm ms-2 source-delete-file" title="Remove from list" aria-label="Remove from list"><i class="fas fa-trash-can"></i></button>');
                $deleteButton.attr('data-path', item.path);
                $actionCell.append($deleteButton);
            }
            $row.append($providerCell);
            $row.append($monthCell);
            $row.append($yearCell);
            $row.append($actionCell);

            $tbody.append($row);
        });
    }

    function renderCsvPreview(response, options) {
        options = options || {};
        const $table = $(options.tableSelector || '#csvPreviewTable');
        const $label = $(options.labelSelector || '#csvPreviewModalLabel');
        const $meta = $(options.metaSelector || '#csvPreviewMeta');
        const sourceLabel = options.sourceLabel || 'Imported data';
        const canDeleteRows = response.can_delete_rows === true && !options.disableRowDelete;
        const canMapColumns = response.can_map_columns === true && !options.disableColumnMap;

        $table.empty();
        toggleImportRowActions(canDeleteRows);

        $label.text(response.name || 'CSV Preview');

        const showingText = response.from_row !== undefined
            ? 'Showing ' + response.from_row + '-' + response.to_row + ' of ' + response.row_count + ' rows'
            : 'Showing ' + response.shown_rows + ' of ' + response.row_count + ' rows';
        const metaText = sourceLabel + ' | ' + (response.path || '') + ' | Columns: ' + response.column_count + ' | ' + showingText;
        $meta.text(metaText);
        updateImportPaginationState(response);

        if (!response.rows || !response.rows.length) {
            $table.append('<tbody><tr><td class="text-muted">This CSV file is empty.</td></tr></tbody>');
            return;
        }

        const $tbody = $('<tbody></tbody>');

        response.rows.forEach(function(row, rowIndex) {
            const $tr = $('<tr></tr>');
            const rowId = response.row_ids && response.row_ids[rowIndex] ? response.row_ids[rowIndex] : null;

            if (canDeleteRows && rowIndex === 0) {
                $tr.addClass('source-import-header-row');
                $tr.append('<th class="source-import-select-cell"><input type="checkbox" class="form-check-input source-import-select-all" aria-label="Select all rows"></th>');
            } else if (canDeleteRows) {
                const $selectCell = $('<td class="source-import-select-cell"></td>');
                if (rowId) {
                    $selectCell.append('<input type="checkbox" class="form-check-input source-import-row-check" aria-label="Select row" value="' + rowId + '">');
                }
                $tr.append($selectCell);
            }

            row.forEach(function(cell, columnIndex) {
                const $cell = rowIndex === 0 ? $('<th></th>') : $('<td></td>');
                if (canMapColumns && rowIndex === 0) {
                    const columnId = response.column_ids && response.column_ids[columnIndex] ? response.column_ids[columnIndex] : null;
                    const mappedField = response.column_mappings && response.column_mappings[columnIndex] ? response.column_mappings[columnIndex] : '';
                    const $label = $('<div class="source-column-label"></div>').text(cell);
                    const $select = buildColumnMapSelect(columnId, mappedField);

                    $cell.append($label);
                    $cell.append($select);
                } else {
                    $cell.text(cell);
                }
                $tr.append($cell);
            });

            if (canDeleteRows && rowIndex === 0) {
                $tr.append('<th class="source-import-delete-cell">Delete</th>');
            } else if (canDeleteRows) {
                const $deleteCell = $('<td class="source-import-delete-cell"></td>');
                if (rowId) {
                    $deleteCell.append('<button type="button" class="btn btn-outline-danger btn-sm source-delete-import-row" title="Delete row" aria-label="Delete row" data-row-id="' + rowId + '"><i class="fas fa-trash-can"></i></button>');
                }
                $tr.append($deleteCell);
            }

            $tbody.append($tr);
        });

        $table.append($tbody);
        updateImportRowSelectionState();
    }

    function buildColumnMapSelect(columnId, mappedField) {
        const $select = $('<select class="form-select form-select-sm source-column-map" aria-label="Map column"></select>');
        $select.attr('data-column-id', columnId || '');
        $select.append('<option value="">Map field</option>');
        $select.append('<option value="1">Provider</option>');
        $select.append('<option value="2">Total</option>');
        $select.append('<option value="3">MarketValue</option>');
        $select.val(mappedField || '');
        $select.toggleClass('source-column-map-mapped', Boolean(mappedField));
        return $select;
    }

    function updateColumnMappingOptions(mappings) {
        if (!mappings) {
            return;
        }

        $('#csvPreviewTable .source-column-map').each(function() {
            const columnId = $(this).data('column-id');
            const mappedField = mappings[columnId] || '';
            $(this).val(mappedField);
            $(this).toggleClass('source-column-map-mapped', Boolean(mappedField));
        });
    }

    function updateImportPaginationState(response) {
        const isImportedPage = response && response.can_delete_rows === true;
        const page = response.page || 1;
        const totalPages = response.total_pages || 1;
        const showAll = response.show_all === true;
        const $pageSelects = $('.source-import-page-select');

        $('.source-import-show-all').prop('checked', showAll);
        $('.source-import-page-status').text(showAll ? 'Showing all rows' : 'Page ' + page + ' of ' + totalPages);
        $('.source-import-page-prev').prop('disabled', !isImportedPage || showAll || !response.has_previous_page);
        $('.source-import-page-next').prop('disabled', !isImportedPage || showAll || !response.has_next_page);

        $pageSelects.empty();
        for (let pageNumber = 1; pageNumber <= totalPages; pageNumber++) {
            $pageSelects.append($('<option></option>').val(pageNumber).text('Page ' + pageNumber));
        }
        $pageSelects.val(String(page));
        $pageSelects.prop('disabled', !isImportedPage || showAll || totalPages <= 1);

        currentImportedPage = response.page || 1;
        currentImportedShowAll = showAll;
    }

    function saveColumnMapping($select) {
        const columnId = $select.data('column-id');

        if (!columnId || !currentImportedBatchId) {
            showSourceToast('Column mapping could not be saved', 'danger');
            return;
        }

        $select.prop('disabled', true);

        $.post("{{ route('source.importColumnMap') }}", {
            batch_id: currentImportedBatchId,
            column_id: columnId,
            mapped_field: $select.val() || null
        })
            .done(function(response) {
                updateColumnMappingOptions(response.mappings);
                showSourceToast('Column mapping saved');
            })
            .fail(function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Column mapping failed';
                showSourceToast(message, 'danger');
            })
            .always(function() {
                $select.prop('disabled', false);
            });
    }

    function loadNameCheck() {
        if (!currentImportedBatchId) {
            $('#csvNameCheckContent').html('<div class="alert alert-warning mb-0">Open an imported file before running Name Check.</div>');
            return;
        }

        $('#csvNameCheckContent').html('<div class="text-muted">Loading provider names...</div>');

        $.getJSON("{{ route('source.importNameCheck') }}", {
            batch_id: currentImportedBatchId
        })
            .done(function(response) {
                if (response.needs_mapping) {
                    $('#csvNameCheckContent').html('<div class="alert alert-warning mb-0">' + $('<div>').text(response.message).html() + '</div>');
                    return;
                }

                const $wrap = $('<div class="col-md-10"></div>');
                const $meta = $('<div class="small text-muted mb-2"></div>').text('Provider column: ' + response.mapped_column + ' | Distinct names: ' + response.count);
                const $table = $('<table class="table table-sm table-bordered mb-0"></table>');
                const $thead = $('<thead><tr><th>Provider</th><th>Broker Name</th><th>Status</th><th>Action</th></tr></thead>');
                const $tbody = $('<tbody></tbody>');
                const brokerNames = response.broker_names || [];
                const existingMappings = response.existing_mappings || {};
                $('#csvNameCheckContent').data('broker-names', brokerNames);

                if (!response.values || !response.values.length) {
                    $tbody.append('<tr><td colspan="4" class="text-muted">No provider names found.</td></tr>');
                } else {
                    response.values.forEach(function(value) {
                        const mappedBrokerName = existingMappings[value] || '';
                        const $brokerSearch = $('<div class="source-broker-search"></div>');
                        const $brokerInput = $('<input type="text" class="form-control form-control-sm source-name-check-broker" aria-label="Broker name" placeholder="Search broker">');
                        const $brokerMenu = $('<div class="source-broker-search-menu"></div>');
                        $brokerInput.val(mappedBrokerName);
                        $brokerSearch.append($brokerInput);
                        $brokerSearch.append($brokerMenu);
                        renderBrokerSearchOptions($brokerMenu, brokerNames, mappedBrokerName);
                        const $status = mappedBrokerName
                            ? $('<span class="badge bg-success">Already mapped</span>')
                            : $('<span class="badge bg-warning text-dark">Not mapped</span>');
                        const $saveButton = $('<button type="button" class="btn btn-outline-primary btn-sm source-save-alt-name">Save mapped</button>');
                        $saveButton.attr('data-alt-name', value);

                        const $row = $('<tr></tr>');
                        if (mappedBrokerName) {
                            $row.addClass('source-alt-name-mapped-row');
                        }

                        $row
                            .append($('<td></td>').text(value))
                            .append($('<td></td>').append($brokerSearch))
                            .append($('<td></td>').append($status))
                            .append($('<td></td>').append($saveButton))
                            .appendTo($tbody);
                    });
                }

                $table.append($thead);
                $table.append($tbody);
                $wrap.append($meta);
                $wrap.append($table);
                $('#csvNameCheckContent').empty().append($wrap);
            })
            .fail(function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Name Check failed';
                $('#csvNameCheckContent').html('<div class="alert alert-danger mb-0">' + $('<div>').text(message).html() + '</div>');
            });
    }

    function renderBrokerSearchOptions($menu, brokerNames, filterText) {
        const normalizedFilter = (filterText || '').toString().trim().toLowerCase();
        const matches = brokerNames.filter(function(brokerName) {
            return !normalizedFilter || brokerName.toLowerCase().indexOf(normalizedFilter) !== -1;
        }).slice(0, 40);

        $menu.empty();

        if (!matches.length) {
            $menu.append('<div class="text-muted small px-2 py-1">No matches</div>');
            return;
        }

        matches.forEach(function(brokerName) {
            $menu.append($('<button type="button" class="source-broker-option"></button>').text(brokerName).attr('data-value', brokerName));
        });
    }

    function saveSourceAltName($button) {
        const $row = $button.closest('tr');
        const altName = $button.data('alt-name');
        const brokerName = $row.find('.source-name-check-broker').val();

        if (!brokerName) {
            showSourceToast('Select broker name before saving', 'warning');
            return;
        }

        $button.prop('disabled', true);

        $.post("{{ route('source.sourceAltName') }}", {
            alt_name: altName,
            broker_name: brokerName
        })
            .done(function() {
                $row.find('td').eq(2).html('<span class="badge bg-success">Already mapped</span>');
                $row.addClass('source-alt-name-mapped-row');
                showSourceToast('Provider mapping saved');
            })
            .fail(function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Provider mapping failed';
                showSourceToast(message, 'danger');
            })
            .always(function() {
                $button.prop('disabled', false);
            });
    }

    function loadPostComm(existingPage) {
        if (!currentImportedBatchId) {
            $('#csvPostCommContent').html('<div class="alert alert-warning mb-0">Open an imported file before posting commission.</div>');
            return;
        }

        $('#csvPostCommContent').html('<div class="text-muted">Checking posting readiness...</div>');

        $.getJSON("{{ route('source.importPostCommSummary') }}", {
            batch_id: currentImportedBatchId,
            existing_page: existingPage || 1
        })
            .done(function(summary) {
                renderPostCommSummary(summary);
            })
            .fail(function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Post Comm check failed';
                $('#csvPostCommContent').html('<div class="alert alert-danger mb-0">' + $('<div>').text(message).html() + '</div>');
            });
    }

    function renderPostCommSummary(summary) {
        const $wrap = $('<div></div>');
        const $grid = $('<div class="row g-2 mb-3"></div>');
        const mappedColumns = summary.mapped_columns || {};

        [
            ['File', summary.file_name || ''],
            ['Period', summary.comm_dt || 'Missing'],
            ['Rows', summary.row_count],
            ['Postable Rows', summary.postable_row_count],
            ['Skipped Rows', summary.skipped_row_count],
            ['Posting Total', summary.post_total],
            ['Existing Posted Rows', summary.existing_count]
        ].forEach(function(item) {
            const $card = $('<div class="col-md-2"></div>');
            const $cardBody = $('<div class="border rounded p-2 h-100"></div>');
            $cardBody.append($('<div class="small text-muted"></div>').text(item[0]));
            $cardBody.append($('<div class="fw-bold"></div>').text(item[1]));
            $card.append($cardBody);
            $grid.append($card);
        });

        $wrap.append($grid);
        $wrap.append(
            $('<div class="small text-muted mb-2"></div>').text(
                'Mapped columns: Provider = ' + (mappedColumns.provider || 'Missing') +
                ' | Total = ' + (mappedColumns.total || 'Missing')
            )
        );

        if (summary.issues && summary.issues.length) {
            const $alert = $('<div class="alert alert-warning"></div>');
            const $list = $('<ul class="mb-0"></ul>');
            summary.issues.forEach(function(issue) {
                $list.append($('<li></li>').text(issue));
            });
            $alert.append($list);
            $wrap.append($alert);
        } else {
            $wrap.append('<div class="alert alert-success">Ready to post commission.</div>');
        }

        if (summary.missing_provider_names && summary.missing_provider_names.length) {
            const $missing = $('<div class="alert alert-info"></div>');
            $missing.text('Unmapped provider names will be skipped: ' + summary.missing_provider_names.join(', '));
            $wrap.append($missing);
        }

        if (summary.existing_records && summary.existing_records.length) {
            const $existingHeader = $('<div class="d-flex justify-content-between align-items-center mt-3 mb-2"></div>');
            const $existingTitle = $('<h6 class="mb-0">Existing Records</h6>');
            const $existingControls = $('<div class="d-flex align-items-center gap-2"></div>');
            const $existingPrev = $('<button type="button" class="btn btn-outline-secondary btn-sm source-existing-page-prev" title="Previous existing records page" aria-label="Previous existing records page"><i class="fas fa-chevron-left"></i></button>');
            const $existingNext = $('<button type="button" class="btn btn-outline-secondary btn-sm source-existing-page-next" title="Next existing records page" aria-label="Next existing records page"><i class="fas fa-chevron-right"></i></button>');
            const $existingStatus = $('<span class="small text-muted"></span>').text('Page ' + summary.existing_page + ' of ' + summary.existing_total_pages + ' | Showing ' + summary.existing_from + '-' + summary.existing_to + ' of ' + summary.existing_count);
            const $deleteSelected = $('<button type="button" class="btn btn-outline-danger btn-sm source-delete-selected-posted-comm-records"><i class="fas fa-trash-can me-1"></i>Delete selected</button>');
            $existingPrev.attr('data-page', summary.existing_page - 1);
            $existingNext.attr('data-page', summary.existing_page + 1);
            $existingPrev.prop('disabled', !summary.existing_has_previous_page);
            $existingNext.prop('disabled', !summary.existing_has_next_page);
            $deleteSelected.prop('disabled', true);
            $existingControls.append($existingPrev, $existingStatus, $existingNext, $deleteSelected);
            $existingHeader.append($existingTitle, $existingControls);

            const $existingTable = $('<table class="table table-sm table-bordered source-existing-records-table"></table>');
            const $existingHead = $('<thead><tr><th><input type="checkbox" class="form-check-input source-existing-record-select-all" aria-label="Select all existing records"></th><th>ID</th><th>Broker</th><th class="text-end">Amount</th><th>CSV</th><th>Date</th><th>Action</th></tr></thead>');
            const $existingBody = $('<tbody></tbody>');

            summary.existing_records.forEach(function(record) {
                $('<tr></tr>')
                    .append($('<td></td>').append($('<input type="checkbox" class="form-check-input source-existing-record-check" aria-label="Select existing record">').val(record.id)))
                    .append($('<td></td>').text(record.id))
                    .append($('<td></td>').text(record.broker_name))
                    .append($('<td class="text-end"></td>').text(record.amt))
                    .append($('<td></td>').text(record.csv_name))
                    .append($('<td></td>').text(record.comm_dt))
                    .append($('<td></td>').append($('<button type="button" class="btn btn-outline-danger btn-sm source-delete-posted-comm-record" title="Delete posted record" aria-label="Delete posted record"><i class="fas fa-trash-can"></i></button>').attr('data-id', record.id)))
                    .appendTo($existingBody);
            });

            $existingTable.append($existingHead);
            $existingTable.append($existingBody);
            $wrap.append($existingHeader);
            $wrap.append($('<div class="table-responsive"></div>').append($existingTable));
        }

        const $button = $('<button type="button" class="btn btn-primary source-post-comm">Post Comm</button>');
        $button.prop('disabled', !summary.can_post);
        if (summary.existing_count > 0) {
            const $replaceAlert = $('<div class="alert alert-danger"></div>');
            const $keepWrap = $('<div class="form-check mt-2"></div>');
            const $keepInput = $('<input class="form-check-input" type="checkbox" id="sourceKeepExistingPostedRows">');
            const $keepLabel = $('<label class="form-check-label" for="sourceKeepExistingPostedRows">Keep existing data and post new rows</label>');
            $replaceAlert.append('<div><strong>Posting will replace the existing rows for this provider and period.</strong></div>');
            $keepWrap.append($keepInput);
            $keepWrap.append($keepLabel);
            $replaceAlert.append($keepWrap);
            $wrap.append($replaceAlert);
        }
        $wrap.append($button);

        $('#csvPostCommContent').empty().append($wrap);
    }

    function postComm() {
        if (!currentImportedBatchId) {
            showSourceToast('Open an imported file before posting', 'warning');
            return;
        }

        const keepExisting = $('#sourceKeepExistingPostedRows').is(':checked');
        const confirmMessage = keepExisting
            ? 'Post commission and keep the existing rows for this provider and period?'
            : 'Post commission for this imported file? Existing posted rows for this provider and period will be soft deleted.';

        if (!window.confirm(confirmMessage)) {
            return;
        }

        const $button = $('.source-post-comm');
        $button.prop('disabled', true).text('Posting...');

        $.post("{{ route('source.importPostComm') }}", {
            batch_id: currentImportedBatchId,
            keep_existing: keepExisting ? 1 : 0
        })
            .done(function(response) {
                showSourceToast('Posting complete.');
                renderPostCommSummary(response.summary);
                loadExplorer(currentPath, { fromHistory: true });
            })
            .fail(function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Post Comm failed';
                showSourceToast(message, 'danger');
                if (xhr.responseJSON && xhr.responseJSON.summary) {
                    renderPostCommSummary(xhr.responseJSON.summary);
                }
            })
            .always(function() {
                $button.prop('disabled', false).text('Post Comm');
            });
    }

    function getSelectedPostedCommRecordIds() {
        return $('.source-existing-record-check:checked').map(function() {
            return parseInt($(this).val(), 10);
        }).get();
    }

    function updatePostedCommSelectionState() {
        const selectedCount = getSelectedPostedCommRecordIds().length;
        const totalCount = $('.source-existing-record-check').length;
        $('.source-delete-selected-posted-comm-records').prop('disabled', selectedCount === 0);
        $('.source-existing-record-select-all')
            .prop('checked', totalCount > 0 && selectedCount === totalCount)
            .prop('indeterminate', selectedCount > 0 && selectedCount < totalCount);
    }

    function deletePostedCommRecords(recordIds) {
        recordIds = recordIds || [];
        if (!recordIds.length) {
            showSourceToast('Select posted records to delete', 'warning');
            return;
        }

        if (!window.confirm('Delete selected posted commission records?')) {
            return;
        }

        $.post("{{ route('source.postedCommDelete') }}", {
            ids: recordIds
        })
            .done(function(response) {
                showSourceToast('Deleted ' + response.deleted_count + ' posted records');
                loadPostComm();
            })
            .fail(function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Delete failed';
                showSourceToast(message, 'danger');
            });
    }

    function toggleImportRowActions(show) {
        $('.source-import-row-actions').toggleClass('d-none', !show);
        if (!show) {
            currentImportedBatchId = null;
        }
    }

    function getSelectedImportRowIds() {
        return $('#csvPreviewTable .source-import-row-check:checked').map(function() {
            return parseInt($(this).val(), 10);
        }).get();
    }

    function updateImportRowSelectionState() {
        const selectedCount = getSelectedImportRowIds().length;
        const totalCount = $('#csvPreviewTable .source-import-row-check').length;

        $('.source-import-selected-count').text(selectedCount + ' rows selected');
        $('.source-delete-selected-import-rows').prop('disabled', selectedCount === 0);
        $('#csvPreviewTable .source-import-select-all')
            .prop('checked', totalCount > 0 && selectedCount === totalCount)
            .prop('indeterminate', selectedCount > 0 && selectedCount < totalCount);
    }

    function deleteImportedRows(rowIds) {
        rowIds = rowIds || [];
        if (!rowIds.length || !currentImportedBatchId) {
            showSourceToast('Select rows to delete', 'warning');
            return;
        }

        if (!window.confirm('Delete selected imported rows?')) {
            return;
        }

        $('.source-delete-selected-import-rows, .source-delete-import-row').prop('disabled', true);

        $.post("{{ route('source.importRowsDelete') }}", {
            batch_id: currentImportedBatchId,
            row_ids: rowIds
        })
            .done(function(response) {
                showSourceToast('Deleted ' + response.deleted_count + ' rows');
                if (currentImportedPreviewPath) {
                    openCsvPreview(currentImportedPreviewPath, currentImportedPage);
                }
            })
            .fail(function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Delete failed';
                showSourceToast(message, 'danger');
            })
            .always(function() {
                $('.source-delete-selected-import-rows, .source-delete-import-row').prop('disabled', false);
            });
    }

    function openCsvPreview(path, page) {
        currentImportedPreviewPath = path;
        currentImportedBatchId = null;
        currentImportedPage = currentImportedShowAll ? 1 : (page || 1);
        const editMapTab = document.getElementById('csv-edit-map-tab');
        if (editMapTab) {
            bootstrap.Tab.getOrCreateInstance(editMapTab).show();
        }
        $('#csvPreviewModalLabel').text('CSV Preview');
        $('#csvPreviewMeta').text('Loading...');
        $('#csvPreviewTable').html('<tbody><tr><td class="text-muted">Loading imported data...</td></tr></tbody>');
        toggleImportRowActions(false);

        const modalElement = document.getElementById('csvPreviewModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        if (!$(modalElement).hasClass('show')) {
            modal.show();
        }

        $.getJSON("{{ route('source.csvPreview') }}", {
            path: path,
            page: currentImportedPage,
            show_all: currentImportedShowAll ? 1 : 0
        })
            .done(function(response) {
                currentImportedBatchId = response.batch_id || null;
                renderCsvPreview(response, {
                    sourceLabel: 'Imported data'
                });
            })
            .fail(function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Could not open CSV file.';
                $('#csvPreviewMeta').text('');
                $('#csvPreviewTable').html('<tbody><tr><td class="text-danger">' + $('<div>').text(message).html() + '</td></tr></tbody>');
                toggleImportRowActions(false);
            });
    }

    function openRawCsvPreview(path) {
        $('#rawCsvPreviewModalLabel').text('Raw CSV Preview');
        $('#rawCsvPreviewMeta').text('Loading...');
        $('#rawCsvPreviewTable').html('<tbody><tr><td class="text-muted">Parsing CSV file...</td></tr></tbody>');

        const modalElement = document.getElementById('rawCsvPreviewModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        if (!$(modalElement).hasClass('show')) {
            modal.show();
        }

        $.getJSON("{{ route('source.rawCsvPreview') }}", { path: path })
            .done(function(response) {
                renderCsvPreview(response, {
                    tableSelector: '#rawCsvPreviewTable',
                    labelSelector: '#rawCsvPreviewModalLabel',
                    metaSelector: '#rawCsvPreviewMeta',
                    sourceLabel: 'On-the-fly file preview'
                });
            })
            .fail(function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Could not preview CSV file.';
                $('#rawCsvPreviewMeta').text('');
                $('#rawCsvPreviewTable').html('<tbody><tr><td class="text-danger">' + $('<div>').text(message).html() + '</td></tr></tbody>');
            });
    }

    function updateNavigationButtons() {
        $('#sourceExplorerBack').prop('disabled', historyIndex <= 0);
        $('#sourceExplorerForward').prop('disabled', historyIndex >= historyStack.length - 1);
    }

    function updateFilterControls() {
        const onlyCsv = $('#sourceExplorerOnlyCsv').is(':checked');
        const flatten = $('#sourceExplorerFlatten').is(':checked');
        $('#sourceExplorerTypeFilter').prop('disabled', onlyCsv || flatten);
    }

    function renderBreadcrumb(path) {
        const $breadcrumb = $('#sourceExplorerBreadcrumb');
        $breadcrumb.empty();

        if (!path) {
            $breadcrumb.append('<li class="breadcrumb-item active" aria-current="page">/</li>');
            return;
        }

        const rootItem = $('<li class="breadcrumb-item"></li>');
        const rootLink = $('<a href="#" class="source-breadcrumb-link"></a>');
        rootLink.attr('data-path', '');
        rootLink.text('/');
        rootItem.append(rootLink);
        $breadcrumb.append(rootItem);

        let builtPath = '';
        const parts = path.split('/');

        parts.forEach(function(part, index) {
            builtPath = builtPath ? builtPath + '/' + part : part;

            if (index === parts.length - 1) {
                const activeItem = $('<li class="breadcrumb-item active" aria-current="page"></li>');
                activeItem.text(part);
                $breadcrumb.append(activeItem);
                return;
            }

            const item = $('<li class="breadcrumb-item"></li>');
            const link = $('<a href="#" class="source-breadcrumb-link"></a>');
            link.attr('data-path', builtPath);
            link.text(part);
            item.append(link);
            $breadcrumb.append(item);
        });
    }

    function navigateBack() {
        if (historyIndex > 0) {
            historyIndex--;
            loadExplorer(historyStack[historyIndex], { fromHistory: true });
        }
    }

    function loadExplorer(path, options) {
        options = options || {};
        $('#sourceExplorerTable tbody').html('<tr><td colspan="9" class="text-muted">Loading source files...</td></tr>');

        $.getJSON("{{ route('source.files') }}", {
            path: path || '',
            flatten: $('#sourceExplorerFlatten').is(':checked') ? 1 : 0
        })
            .done(function(response) {
                currentPath = response.path || '';
                parentPath = response.parent;

                if (!options.fromHistory && historyStack[historyIndex] !== currentPath) {
                    historyStack = historyStack.slice(0, historyIndex + 1);
                    historyStack.push(currentPath);
                    historyIndex = historyStack.length - 1;
                }

                renderBreadcrumb(currentPath);
                updateNavigationButtons();
                currentItems = response.items || [];
                updateTypeFilter(response.file_types || []);
                updateFilterControls();
                renderRows(currentItems);
            })
            .fail(function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Could not load source files.';
                $('#sourceExplorerTable tbody').html('<tr><td colspan="9" class="text-danger">' + message + '</td></tr>');
            });
    }

    $('#sourceExplorerTable').on('dblclick', '.source-file-row', function() {
        const path = $(this).data('path');

        if ($(this).data('type') === 'directory') {
            loadExplorer(path);
            return;
        }

        if (getFileType(path) === 'csv') {
            openCsvPreview(path);
        }
    });

    $('#sourceExplorerTable').on('dblclick', '.source-parent-row', function() {
        navigateBack();
    });

    $('#sourceExplorerTable').on('click', '.source-open-csv', function(event) {
        event.stopPropagation();
        openCsvPreview($(this).data('path'));
    });

    $('#sourceExplorerTable').on('click', '.source-preview-csv', function(event) {
        event.stopPropagation();
        openRawCsvPreview($(this).data('path'));
    });

    $('#sourceExplorerTable').on('click', '.source-import-csv', function(event) {
        event.stopPropagation();
        const $row = $(this).closest('tr');
        importSourceCsv($(this).data('path'), $row);
    });

    $('#sourceExplorerTable').on('click', '.source-delete-file', function(event) {
        event.stopPropagation();
        const $row = $(this).closest('tr');
        deleteSourceFileMetadata($(this).data('path'), $row);
    });

    $('#csvPreviewModal').on('change', '.source-import-select-all', function() {
        $('#csvPreviewTable .source-import-row-check').prop('checked', $(this).is(':checked'));
        updateImportRowSelectionState();
    });

    $('#csvPreviewModal').on('change', '.source-import-row-check', function() {
        updateImportRowSelectionState();
    });

    $('#csvPreviewModal').on('click', '.source-delete-import-row', function() {
        deleteImportedRows([parseInt($(this).data('row-id'), 10)]);
    });

    $('#csvPreviewModal').on('click', '.source-delete-selected-import-rows', function() {
        deleteImportedRows(getSelectedImportRowIds());
    });

    $('#csvPreviewModal').on('click', '.source-import-page-prev', function() {
        if (currentImportedPreviewPath && currentImportedPage > 1) {
            openCsvPreview(currentImportedPreviewPath, currentImportedPage - 1);
        }
    });

    $('#csvPreviewModal').on('click', '.source-import-page-next', function() {
        if (currentImportedPreviewPath) {
            openCsvPreview(currentImportedPreviewPath, currentImportedPage + 1);
        }
    });

    $('#csvPreviewModal').on('change', '.source-import-show-all', function() {
        currentImportedShowAll = $(this).is(':checked');
        if (currentImportedPreviewPath) {
            openCsvPreview(currentImportedPreviewPath, 1);
        }
    });

    $('#csvPreviewModal').on('change', '.source-import-page-select', function() {
        const page = parseInt($(this).val(), 10);
        if (currentImportedPreviewPath && page > 0) {
            openCsvPreview(currentImportedPreviewPath, page);
        }
    });

    $('#csvPreviewModal').on('click', '.source-column-map', function(event) {
        event.stopPropagation();
    });

    $('#csvPreviewModal').on('change', '.source-column-map', function() {
        saveColumnMapping($(this));
    });

    $('#csv-name-check-tab').on('shown.bs.tab', function() {
        loadNameCheck();
    });

    $('#csv-post-comm-tab').on('shown.bs.tab', function() {
        loadPostComm();
    });

    $('#csvPreviewModal').on('click', '.source-save-alt-name', function() {
        saveSourceAltName($(this));
    });

    $('#csvPreviewModal').on('click', '.source-post-comm', function() {
        postComm();
    });

    $('#csvPreviewModal').on('click', '.source-delete-posted-comm-record', function() {
        deletePostedCommRecords([parseInt($(this).data('id'), 10)]);
    });

    $('#csvPreviewModal').on('change', '.source-existing-record-select-all', function() {
        $('.source-existing-record-check').prop('checked', $(this).is(':checked'));
        updatePostedCommSelectionState();
    });

    $('#csvPreviewModal').on('change', '.source-existing-record-check', function() {
        updatePostedCommSelectionState();
    });

    $('#csvPreviewModal').on('click', '.source-delete-selected-posted-comm-records', function() {
        deletePostedCommRecords(getSelectedPostedCommRecordIds());
    });

    $('#csvPreviewModal').on('click', '.source-existing-page-prev', function() {
        const page = parseInt($(this).data('page'), 10);
        if (page > 0) {
            loadPostComm(page);
        }
    });

    $('#csvPreviewModal').on('click', '.source-existing-page-next', function() {
        const page = parseInt($(this).data('page'), 10);
        if (page > 0) {
            loadPostComm(page);
        }
    });

    $('#csvPreviewModal').on('focus input', '.source-name-check-broker', function() {
        const $search = $(this).closest('.source-broker-search');
        const brokerNames = $('#csvNameCheckContent').data('broker-names') || [];
        renderBrokerSearchOptions($search.find('.source-broker-search-menu'), brokerNames, $(this).val());
        $search.find('.source-broker-search-menu').addClass('show');
    });

    $('#csvPreviewModal').on('click', '.source-broker-option', function() {
        const $search = $(this).closest('.source-broker-search');
        $search.find('.source-name-check-broker').val($(this).data('value'));
        $search.find('.source-broker-search-menu').removeClass('show');
    });

    $(document).on('click', function(event) {
        if (!$(event.target).closest('.source-broker-search').length) {
            $('.source-broker-search-menu').removeClass('show');
        }
    });

    $('#sourceExplorerTable').on('click', '.source-file-provider, .source-file-month, .source-file-year', function(event) {
        event.stopPropagation();
    });

    $('#sourceExplorerTable').on('change', '.source-file-provider, .source-file-month, .source-file-year', function(event) {
        event.stopPropagation();
        const $row = $(this).closest('tr');
        saveSourceFileMetadata($(this).data('path'), $row);
    });

    $('#sourceExplorerBack').on('click', function() {
        navigateBack();
    });

    $('#sourceExplorerForward').on('click', function() {
        if (historyIndex < historyStack.length - 1) {
            historyIndex++;
            loadExplorer(historyStack[historyIndex], { fromHistory: true });
        }
    });

    $('#sourceExplorerRefresh').on('click', function() {
        loadExplorer(currentPath);
    });

    $('#sourceExplorerBreadcrumb').on('click', '.source-breadcrumb-link', function(event) {
        event.preventDefault();
        loadExplorer($(this).data('path') || '');
    });

    $('#sourceExplorerTypeFilter').on('change', function() {
        renderRows(currentItems);
    });

    $('#sourceExplorerLegend').on('click', '.source-legend-filter', function() {
        sourceStatusFilter = $(this).data('status') || 'all';
        $('#sourceExplorerLegend .source-legend-filter').removeClass('active');
        $(this).addClass('active');
        renderRows(currentItems);
    });

    $('#sourceExplorerHideProcessed').on('change', function() {
        renderRows(currentItems);
    });

    $('#sourceExplorerProviderFilter').on('change', function() {
        sourceProviderFilter = $(this).val() || 'all';
        renderRows(currentItems);
    });

    $('#sourceExplorerOnlyCsv').on('change', function() {
        updateFilterControls();
        renderRows(currentItems);
    });

    $('#sourceExplorerFlatten').on('change', function() {
        updateFilterControls();
        loadExplorer(currentPath);
    });

    $('button[data-bs-target="#source-files"]').on('shown.bs.tab', function() {
        loadExplorer(currentPath);
    });

    loadExplorer('');
    updateNavigationButtons();
});
</script>
@endpush

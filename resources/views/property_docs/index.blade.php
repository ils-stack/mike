@extends('layouts.app')

@section('title', 'Documents')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Documents</h4>
        <a href="/asset-library" class="btn btn-outline-secondary btn-sm">
            <i class="fa fa-folder-open me-1"></i> Asset Library
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body text-center">
            <form id="propertyDocsUploadForm" enctype="multipart/form-data">
                @csrf
                <input type="file"
                       id="propertyDocsFileInput"
                       accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                       hidden
                       multiple>

                <div id="propertyDocsDropZone" class="border border-2 border-dashed rounded p-5 bg-light">
                    <p class="mb-2"><i class="fa-solid fa-upload fa-2x text-primary"></i></p>
                    <p class="mb-2">Drag & drop images, PDF, Word, Excel or PowerPoint files here</p>
                    <button type="button" class="btn btn-sm btn-primary" id="propertyDocsBrowseBtn">
                        <i class="fa fa-folder-open"></i> Browse
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Type</label>
                    <select id="filter_doc_types" class="selectpicker form-control" multiple data-actions-box="true" data-width="100%">
                        <option value="image">Images (jpg, jpeg, png, gif)</option>
                        <option value="pdf">PDF</option>
                        <option value="word">Word</option>
                        <option value="excel">Excel</option>
                        <option value="ppt">PowerPoint</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Uploaded By</label>
                    <select id="filter_doc_users" class="selectpicker form-control" multiple data-live-search="true" data-actions-box="true" data-width="100%"></select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <a href="/asset-library" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-folder-open me-1"></i> Asset Library
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="propertyDocsTable" class="table table-striped table-bordered w-100">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Type</th>
                        <th>Uploaded By</th>
                        <th>Attached To</th>
                        <th>Uploaded At</th>
                        <th style="width: 170px;">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('map-loader')
<script>
window.initMap = function () {}
</script>
@endpush

@push('page-js')
<script>
let propertyDocsTable;
let propertyDocsFiltersReady = false;

$(function () {
    propertyDocsTable = $('#propertyDocsTable').DataTable({
        ajax: {
            url: '/documents',
            data: function (d) {
                d.types = $('#filter_doc_types').val();
                d.users = $('#filter_doc_users').val();
            }
        },
        serverSide: false,
        processing: true,
        columns: [
            {
                data: 'file_name',
                render: function (name, type, row) {
                    if (type !== 'display') return name || '';
                    return `<i class="fa ${docIcon(row.doc_type)} me-2"></i>${escapeHtml(name || '')}`;
                }
            },
            { data: 'doc_type', defaultContent: 'Document' },
            { data: 'user.name', defaultContent: 'N/A' },
            {
                data: 'assignments',
                render: function (assignments, type) {
                    if (!assignments || !assignments.length) return type === 'display' ? '<span class="text-muted">Not attached</span>' : '';

                    return assignments.map(function (assignment) {
                        return assignment.module_type === 'property_doc'
                            ? 'Record #' + assignment.module_id
                            : 'Unit #' + assignment.module_id;
                    }).join(', ');
                }
            },
            { data: 'created_at_display' },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function (id, type, row) {
                    if (type !== 'display') return '';
                    return `
                        <a href="${row.public_url}" target="_blank" class="btn btn-sm btn-info me-1" title="Open">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="${row.download_url}" class="btn btn-sm btn-secondary me-1" title="Download">
                            <i class="fa fa-download"></i>
                        </a>
                        <button class="btn btn-sm btn-danger delete-doc" data-id="${id}" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    $('#propertyDocsBrowseBtn').on('click', function () {
        $('#propertyDocsFileInput').click();
    });

    $('#propertyDocsFileInput').on('change', function () {
        uploadPropertyDocFiles(this.files);
        this.value = '';
    });

    $('#propertyDocsDropZone')
        .on('dragover', function (e) {
            e.preventDefault();
            $(this).addClass('bg-secondary-subtle');
        })
        .on('dragleave drop', function (e) {
            e.preventDefault();
            $(this).removeClass('bg-secondary-subtle');
        })
        .on('drop', function (e) {
            uploadPropertyDocFiles(e.originalEvent.dataTransfer.files);
        });

    $('#filter_doc_types, #filter_doc_users').on('changed.bs.select', function () {
        propertyDocsTable.ajax.reload();
    });

    $('#propertyDocsTable').on('xhr.dt', function (e, settings, json) {
        if (!propertyDocsFiltersReady && json && json.data) {
            populatePropertyDocsUsers(json.data);
            propertyDocsFiltersReady = true;
        }
    });

    $('#propertyDocsTable').on('click', '.delete-doc', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this document?')) return;

        $.ajax({
            url: `/documents/${id}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function () {
                propertyDocsTable.ajax.reload(null, false);
                notifyPropertyDocs('success', 'Document deleted successfully');
            },
            error: function (xhr) {
                notifyPropertyDocs('danger', xhr.responseJSON?.message || 'Delete failed');
            }
        });
    });
});

function uploadPropertyDocFiles(files) {
    Array.from(files).forEach(function (file) {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('file', file);

        $.ajax({
            url: '/documents/upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                propertyDocsTable.ajax.reload(null, false);
                notifyPropertyDocs('success', 'Document uploaded successfully');
            },
            error: function (xhr) {
                notifyPropertyDocs('danger', xhr.responseJSON?.message || 'Upload failed');
            }
        });
    });
}

function populatePropertyDocsUsers(rows) {
    const users = new Map();
    rows.forEach(function (row) {
        if (row.user && row.user.id) users.set(row.user.id, row.user.name || 'User #' + row.user.id);
    });

    const $users = $('#filter_doc_users');
    $users.empty();
    [...users.entries()].sort((a, b) => a[1].localeCompare(b[1])).forEach(function ([id, name]) {
        $users.append(`<option value="${id}">${escapeHtml(name)}</option>`);
    });
    $users.selectpicker('refresh');
}

function docIcon(type) {
    if (type === 'Image') return 'fa-file-image text-primary';
    if (type === 'PDF') return 'fa-file-pdf text-danger';
    if (type === 'Word') return 'fa-file-word text-info';
    if (type === 'Excel') return 'fa-file-excel text-success';
    if (type === 'PowerPoint') return 'fa-file-powerpoint text-warning';
    return 'fa-file text-secondary';
}

function escapeHtml(value) {
    return $('<div>').text(value).html();
}

function notifyPropertyDocs(level, message) {
    if (typeof showToast === 'function') {
        showToast(level, message);
        return;
    }

    window.alert(message);
}
</script>
@endpush

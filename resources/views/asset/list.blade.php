@extends('layouts.app')

@section('title', 'Asset Library')

@section('content')
<div class="container-fluid py-4">
    <h4 class="mb-4">Asset Library</h4>

    @include('common.asset_drop_upload')

    @include('common.asset_filters')

    <!-- Asset List -->
    <div class="card">
        <div class="card-body">
            <table id="assetsTable" class="table table-striped table-bordered w-100">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Type</th>
                        <th>User</th>
                        <th>Uploaded At</th>
                        <th style="width: 220px;">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

{{-- Keep map-loader for Google Maps --}}
@push('map-loader')
<script>
window.initMap = function () {
    // Intentionally empty, required for Google Maps API to load
}
</script>
@endpush

{{-- Add dynamic JS for assets --}}
@push('page-js')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
let table;
let filtersInitialized = false;
$(document).ready(function () {
    table = $('#assetsTable').DataTable({
        ajax: {
            url: '/asset-library',
            data: function (d) {
                d.modules    = $('#filter_modules').val();
                d.types      = $('#filter_types').val();
                d.users      = $('#filter_users').val();
                d.unassigned = $('#filter_unassigned').is(':checked') ? 1 : 0;
            }
        },
        serverSide: false,
        processing: true,
        columns: [
            {
                data: 'file_name',
                render: function(name, type, row) {
                    if (row.file_type && row.file_type.startsWith("image/")) {
                        return `
                            <img src="{{ asset('storage') }}/${row.file_path}"
                                 class="img-thumbnail me-2"
                                 width="50" height="50"
                                 style="object-fit:cover;"/> ${name}`;
                    }
                    return `<i class="fa fa-file text-secondary me-2"></i> ${name}`;
                }
            },
            {
                data: 'file_type',
                render: function(type) {
                    if (!type) return `<i class="fa fa-file text-secondary"></i> Unknown`;

                    type = type.toLowerCase();

                    if (type.startsWith("image/")) {
                        return `<i class="fa fa-image text-primary"></i> Image`;
                    }
                    if (type === "application/pdf") {
                        return `<i class="fa fa-file-pdf text-danger"></i> PDF`;
                    }
                    if (type.includes("word")) {
                        return `<i class="fa fa-file-word text-info"></i> Word`;
                    }
                    if (type.includes("excel") || type.includes("spreadsheet") || type.includes("xls")) {
                        return `<i class="fa fa-file-excel text-success"></i> Excel`;
                    }
                    if (type.includes("presentation") || type.includes("powerpoint") || type.includes("ppt")) {
                        return `<i class="fa fa-file-powerpoint text-warning"></i> PPT`;
                    }
                    if (type.includes("csv")) {
                        return `<i class="fa fa-file-csv text-success"></i> CSV`;
                    }
                    return `<i class="fa fa-file text-secondary"></i> Other`;
                }
            },
            { data: 'user.name', defaultContent: 'N/A' },
            { data: 'created_at' },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function (id, type, row) {
                    return `
                        <a href="{{ asset('storage') }}/${row.file_path}" target="_blank"
                           class="btn btn-sm btn-info me-1">
                            <i class="fa fa-eye"></i> Preview
                        </a>
                        <a href="{{ asset('storage') }}/${row.file_path}" download
                           class="btn btn-sm btn-secondary me-1">
                            <i class="fa fa-download"></i> Download
                        </a>
                        <button class="btn btn-sm btn-danger delete" data-id="${id}">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    `;
                }
            }
        ]
    });

    // // Browse button
    // $('#browseBtn').click(() => $('#fileInput').click());
    //
    // // File input
    // $('#fileInput').on('change', function () {
    //     uploadFile(this.files[0]);
    // });
    //
    // // Drag & drop
    // $('#dropZone').on('dragover', function (e) {
    //     e.preventDefault(); $(this).addClass('bg-secondary text-white');
    // }).on('dragleave drop', function (e) {
    //     e.preventDefault(); $(this).removeClass('bg-secondary text-white');
    // }).on('drop', function (e) {
    //     uploadFile(e.originalEvent.dataTransfer.files[0]);
    // });
    //
    // // Upload logic
    // function uploadFile(file) {
    //     let formData = new FormData($('#assetUploadForm')[0]);
    //     formData.set('file', file);
    //
    //     $.ajax({
    //         url: '/asset-library/upload',
    //         type: 'POST',
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         success: () => {
    //             table.ajax.reload();
    //             showToast('success', 'File uploaded successfully!');
    //         },
    //         error: (xhr) => {
    //             let msg = xhr.responseJSON?.message || xhr.responseText || "Unknown error";
    //             showToast('danger', 'Upload failed: ' + msg);
    //         }
    //     });
    // }

    // Browse button
    $('#browseBtn').click(() => $('#fileInput').click());

    // File input (multiple)
    $('#fileInput').on('change', function () {
        uploadFiles(this.files);
    });

    // Drag & drop
    $('#dropZone')
      .on('dragover', function (e) {
          e.preventDefault();
          $(this).addClass('bg-secondary text-white');
      })
      .on('dragleave drop', function (e) {
          e.preventDefault();
          $(this).removeClass('bg-secondary text-white');
      })
      .on('drop', function (e) {
          uploadFiles(e.originalEvent.dataTransfer.files);
      });

    // 🔁 Upload multiple files
    function uploadFiles(files) {
        Array.from(files).forEach(file => uploadFile(file));
    }

    // Upload logic (unchanged, reused)
    function uploadFile(file) {
        let formData = new FormData($('#assetUploadForm')[0]);
        formData.set('file', file);

        $.ajax({
            url: '/asset-library/upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: () => {
                table.ajax.reload(null, false);
                showToast('success', 'File uploaded successfully!');
            },
            error: (xhr) => {
                let msg = xhr.responseJSON?.message || xhr.responseText || 'Unknown error';
                showToast('danger', 'Upload failed: ' + msg);
            }
        });
    }


    // Delete logic
    $('#assetsTable').on('click', '.delete', function () {
        let id = $(this).data('id');
        if (confirm("Delete this file?")) {
            $.ajax({
                url: `/asset-library/${id}`,
                type: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                success: () => {
                    table.ajax.reload();
                    showToast('success', 'File deleted successfully!');
                },
                error: (xhr) => {
                    let msg = xhr.responseJSON?.message || xhr.responseText || "Unknown error";
                    showToast('danger', 'Delete failed: ' + msg);
                }
            });
        }
    });

    // Bootstrap Toast helper
    function showToast(type, message) {
        let toast = $(`
            <div class="toast align-items-center text-bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        $('#toast-container').append(toast);
        new bootstrap.Toast(toast[0]).show();
    }
});
</script>

<script>
/* -----------------------------
 * Asset Filters – JS wiring
 * ----------------------------- */

 function mapLogicalType(mime) {
    if (!mime) return null;

    mime = mime.toLowerCase();

    if (mime.startsWith('image/')) return 'image';
    if (mime === 'application/pdf') return 'pdf';
    if (mime.includes('word')) return 'word';
    if (mime.includes('excel') || mime.includes('spreadsheet') || mime.includes('xls')) return 'excel';
    if (mime.includes('presentation') || mime.includes('powerpoint') || mime.includes('ppt')) return 'ppt';
    if (mime.includes('csv')) return 'csv';

    return 'other';
}

function populateFilters(data) {
    const modules = new Set();
    const types   = new Set();
    const users   = new Map(); // id -> name

    data.forEach(row => {
        // File types
        // if (row.file_type) {
        //     types.add(row.file_type);
        // }

        const logicalType = mapLogicalType(row.file_type);
        if (logicalType) {
            types.add(logicalType);
        }

        // Users
        if (row.user && row.user.id) {
            users.set(row.user.id, row.user.name);
        }

        // Modules (from assignments, if present)
        if (row.assignments && row.assignments.length) {
            row.assignments.forEach(a => {
                if (a.module_type) {
                    modules.add(a.module_type);
                }
            });
        }
    });

    // Populate Module filter
    const $modules = $('#filter_modules');
    $modules.empty();
    [...modules].sort().forEach(m => {
        $modules.append(`<option value="${m}">${m}</option>`);
    });

    // Populate Type filter (normalize labels)
    // const $types = $('#filter_types');
    // $types.empty();
    // [...types].sort().forEach(t => {
    //     let label = t;
    //     if (t.startsWith('image/')) label = 'Image';
    //     else if (t === 'application/pdf') label = 'PDF';
    //     else if (t.includes('word')) label = 'Word';
    //     else if (t.includes('excel') || t.includes('spreadsheet')) label = 'Excel';
    //     else if (t.includes('presentation') || t.includes('powerpoint')) label = 'PPT';
    //     else if (t.includes('csv')) label = 'CSV';
    //
    //     $types.append(`<option value="${t}">${label}</option>`);
    // });

    const $types = $('#filter_types');
    $types.empty();

    const typeLabels = {
        image: 'Image',
        pdf: 'PDF',
        word: 'Word',
        excel: 'Excel',
        ppt: 'PPT',
        csv: 'CSV',
        other: 'Other'
    };

    [...types].sort().forEach(t => {
        $types.append(`<option value="${t}">${typeLabels[t]}</option>`);
    });


    // Populate User filter
    const $users = $('#filter_users');
    $users.empty();
    [...users.entries()].sort((a, b) => a[1].localeCompare(b[1])).forEach(([id, name]) => {
        $users.append(`<option value="${id}">${name}</option>`);
    });

    // Refresh all selectpickers
    $('.selectpicker').selectpicker('refresh');
}

// Initial population after first load
$('#assetsTable').on('xhr.dt', function (e, settings, json) {
    if (!filtersInitialized && json && json.data) {
        populateFilters(json.data);
        filtersInitialized = true;
    }
});

// Reload table on filter change
$('#filter_modules, #filter_types, #filter_users').on('changed.bs.select', function () {
    table.ajax.reload();
});

$('#filter_unassigned').on('change', function () {
    table.ajax.reload();
});
</script>

@endpush

{{-- Toast container --}}
<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

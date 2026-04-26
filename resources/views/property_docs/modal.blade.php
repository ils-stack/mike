<div class="modal fade" id="propertyDocsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="propertyDocsModalTitle">Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="propertyDocsModuleType">
                <input type="hidden" id="propertyDocsModuleId">

                <form id="propertyDocsModalUploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="file"
                           id="propertyDocsModalFileInput"
                           accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                           hidden
                           multiple>

                    <div id="propertyDocsModalDropZone" class="border border-2 border-dashed rounded p-4 bg-light text-center mb-4">
                        <p class="mb-2"><i class="fa-solid fa-upload fa-2x text-primary"></i></p>
                        <p class="mb-2">Drag & drop images, PDF, Word, Excel or PowerPoint files here</p>
                        <button type="button" class="btn btn-sm btn-primary" id="propertyDocsModalBrowseBtn">
                            <i class="fa fa-folder-open"></i> Browse
                        </button>
                    </div>
                </form>

                <h6 class="mb-2">Attached Docs</h6>
                <div id="attachedPropertyDocs" class="list-group mb-4"></div>

                <h6 class="mb-2">Available Docs <span class="text-muted fw-normal">(Unassigned)</span></h6>
                <div id="availablePropertyDocs" class="list-group"></div>
            </div>
        </div>
    </div>
</div>

@push('modal-js')
<script>
let propertyDocsModalInstance = null;

$(function () {
    propertyDocsModalInstance = new bootstrap.Modal(document.getElementById('propertyDocsModal'));

    $('#propertyDocsModalBrowseBtn').on('click', function () {
        $('#propertyDocsModalFileInput').click();
    });

    $('#propertyDocsModalFileInput').on('change', function () {
        uploadPropertyDocsFromModal(this.files);
        this.value = '';
    });

    $('#propertyDocsModalDropZone')
        .on('dragover', function (e) {
            e.preventDefault();
            $(this).addClass('bg-secondary-subtle');
        })
        .on('dragleave drop', function (e) {
            e.preventDefault();
            $(this).removeClass('bg-secondary-subtle');
        })
        .on('drop', function (e) {
            uploadPropertyDocsFromModal(e.originalEvent.dataTransfer.files);
        });

    $('#attachedPropertyDocs, #availablePropertyDocs').on('click', '.toggle-property-doc', function () {
        togglePropertyDoc($(this).data('asset-id'));
    });
});

function openPropertyDocsModal(moduleType, moduleId, title) {
    $('#propertyDocsModuleType').val(moduleType);
    $('#propertyDocsModuleId').val(moduleId);
    $('#propertyDocsModalTitle').text(title || 'Documents');
    loadPropertyDocsForModal();
    propertyDocsModalInstance.show();
}

function loadPropertyDocsForModal() {
    const moduleType = $('#propertyDocsModuleType').val();
    const moduleId = $('#propertyDocsModuleId').val();
    const endpoint = moduleType === 'unit_doc'
        ? `/ajax/unit/${moduleId}/docs`
        : `/ajax/property/${moduleId}/docs`;

    $('#attachedPropertyDocs').html('<div class="text-muted p-3">Loading...</div>');
    $('#availablePropertyDocs').html('');

    $.get(endpoint, function (docs) {
        renderPropertyDocsModalLists(docs || []);
    }).fail(function () {
        $('#attachedPropertyDocs').html('<div class="text-danger p-3">Could not load documents.</div>');
    });
}

function renderPropertyDocsModalLists(docs) {
    const attached = docs.filter(doc => doc.assigned);
    const available = docs.filter(doc => !doc.assigned);

    $('#attachedPropertyDocs').html(
        attached.length
            ? attached.map(doc => renderPropertyDocRow(doc, true)).join('')
            : '<div class="text-muted p-3 border rounded">No documents attached.</div>'
    );

    $('#availablePropertyDocs').html(
        available.length
            ? available.map(doc => renderPropertyDocRow(doc, false)).join('')
            : '<div class="text-muted p-3 border rounded">No other documents available.</div>'
    );
}

function renderPropertyDocRow(doc, attached) {
    const fileName = escapePropertyDocHtml(doc.file_name || 'Document');
    const type = escapePropertyDocHtml(doc.doc_type || 'Document');
    const user = doc.user ? escapePropertyDocHtml(doc.user.name || 'N/A') : 'N/A';
    const icon = propertyDocIcon(doc.doc_type);
    const btnClass = attached ? 'btn-outline-danger' : 'btn-outline-primary';
    const btnText = attached ? 'Remove' : 'Attach';

    return `
        <div class="list-group-item d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <i class="fa ${icon} fa-lg"></i>
                <div>
                    <div class="fw-semibold">${fileName}</div>
                    <div class="small text-muted">${type} | Uploaded by ${user}</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="${doc.public_url}" target="_blank" class="btn btn-sm btn-info">
                    <i class="fa fa-eye"></i>
                </a>
                <a href="${doc.download_url}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-download"></i>
                </a>
                <button type="button" class="btn btn-sm ${btnClass} toggle-property-doc" data-asset-id="${doc.id}">
                    ${btnText}
                </button>
            </div>
        </div>
    `;
}

function uploadPropertyDocsFromModal(files) {
    const moduleType = $('#propertyDocsModuleType').val();
    const moduleId = $('#propertyDocsModuleId').val();

    Array.from(files).forEach(function (file) {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('file', file);
        formData.append('module_type', moduleType);
        formData.append('module_id', moduleId);

        $.ajax({
            url: '/documents/upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                if (typeof showToast === 'function') {
                    showToast('success', 'Document uploaded successfully');
                }
                loadPropertyDocsForModal();
            },
            error: function (xhr) {
                if (typeof showToast === 'function') {
                    showToast('danger', xhr.responseJSON?.message || 'Upload failed');
                }
            }
        });
    });
}

function togglePropertyDoc(assetId) {
    $.post('/ajax/property-docs/toggle', {
        _token: '{{ csrf_token() }}',
        asset_id: assetId,
        module_type: $('#propertyDocsModuleType').val(),
        module_id: $('#propertyDocsModuleId').val()
    }).done(function () {
        loadPropertyDocsForModal();
    }).fail(function (xhr) {
        if (typeof showToast === 'function') {
            showToast('danger', xhr.responseJSON?.message || 'Could not update document');
        }
    });
}

function propertyDocIcon(type) {
    if (type === 'Image') return 'fa-file-image text-primary';
    if (type === 'PDF') return 'fa-file-pdf text-danger';
    if (type === 'Word') return 'fa-file-word text-info';
    if (type === 'Excel') return 'fa-file-excel text-success';
    if (type === 'PowerPoint') return 'fa-file-powerpoint text-warning';
    return 'fa-file text-secondary';
}

function escapePropertyDocHtml(value) {
    return $('<div>').text(value).html();
}
</script>
@endpush

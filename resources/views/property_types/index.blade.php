@extends('layouts.app')

@section('title', 'Property Types')

@section('content')
<div class="d-flex justify-content-between mb-2">
    <h5>Property Types</h5>
    <button class="btn btn-primary" onclick="openTypeModal()">
        <i class="fa fa-plus"></i> Add Type
    </button>
</div>

<table class="table table-bordered table-sm" id="typesTable">
    <thead class="table-light">
        <tr>
            <th width="60">#</th>
            <th>Type</th>
            <th width="120">Actions</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="typeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Property Type</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="type_id">
        <div class="mb-2">
            <label class="form-label">Type</label>
            <input type="text" id="type_name" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" onclick="saveType()">Save</button>
      </div>
    </div>
  </div>
</div>

<div id="toast-container" class="position-fixed top-0 end-0 p-3"></div>
@endsection

@push('page-js')
<script>
/* ================================
   CSRF – REQUIRED (do not remove)
================================ */
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
});

$(function () {
    loadTypes();
});

$(document).on('click', '.edit-type', function () {
    editType(
        $(this).data('id'),
        $(this).data('type')
    );
});

function loadTypes() {
    $.get('/property-types', function (data) {
        let rows = '';
        data.forEach((row, i) => {
            rows += `
              <tr>
                <td>${i + 1}</td>
                <td>${row.type}</td>
                <td>
                  <i class="fa fa-edit text-primary me-2 edit-type"
                   style="cursor:pointer"
                   data-id="${row.id}"
                   data-type="${row.type.replace(/"/g, '&quot;')}"></i>

                  <i class="fa fa-trash text-danger"
                     style="cursor:pointer"
                     onclick="deleteType(${row.id})"></i>
                </td>
              </tr>
            `;
        });
        $('#typesTable tbody').html(rows);
    });
}

function openTypeModal() {
    $('#type_id').val('');
    $('#type_name').val('');
    new bootstrap.Modal(document.getElementById('typeModal')).show();
}

function editType(id, name) {
    $('#type_id').val(id);
    $('#type_name').val(name);
    new bootstrap.Modal(document.getElementById('typeModal')).show();
}

function saveType() {
    let id = $('#type_id').val();
    let type = $('#type_name').val();

    let url = '/property-types';
    let method = 'POST';

    if (id) {
        url += '/' + id;
        method = 'PUT';
    }

    $.ajax({
        url: url,
        method: method,
        data: { type },
        success: function () {
            showToast('success', 'Saved');

            bootstrap.Modal.getInstance(
                document.getElementById('typeModal')
            ).hide();

            loadTypes();
        },
        error: function (xhr) {
            showToast('danger', xhr.responseJSON?.message || 'Error');
        }
    });
}

function deleteType(id) {
    if (!confirm('Delete this type?')) return;

    $.ajax({
        url: '/property-types/' + id,
        method: 'DELETE',
        success: function () {
            showToast('success', 'Deleted');
            loadTypes();
        }
    });
}

function showToast(type, message) {
    let toast = $(`
      <div class="toast text-bg-${type} mb-2">
        <div class="d-flex">
          <div class="toast-body">${message}</div>
          <button class="btn-close btn-close-white me-2 m-auto"
                  data-bs-dismiss="toast"></button>
        </div>
      </div>
    `);
    $('#toast-container').append(toast);
    new bootstrap.Toast(toast[0]).show();
}

/* required – do not remove */
function initMap() {}
</script>
@endpush

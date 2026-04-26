@extends('layouts.app')

@section('title', 'Property Status')

@section('content')
<div class="d-flex justify-content-between mb-2">
    <h5>Property Status</h5>
    <button class="btn btn-primary" onclick="openStatusModal()">
        <i class="fa fa-plus"></i> Add Status
    </button>
</div>

<table class="table table-bordered table-sm" id="statusTable">
    <thead class="table-light">
        <tr>
            <th width="60">#</th>
            <th>Status</th>
            <th width="120">Actions</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Property Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="status_id">
        <div class="mb-2">
            <label class="form-label">Status</label>
            <input type="text" id="status_name" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" onclick="saveStatus()">Save</button>
      </div>
    </div>
  </div>
</div>

<div id="toast-container" class="position-fixed top-0 end-0 p-3"></div>
@endsection

@push('page-js')
<script>
/* CSRF – required */
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
});

$(function () {
    loadStatus();
});

/* LIST */
function loadStatus() {
    $.get('/property-status', function (data) {
        let rows = '';
        data.forEach((row, i) => {
            rows += `
              <tr>
                <td>${i + 1}</td>
                <td>${row.status}</td>
                <td>
                  <i class="fa fa-edit text-primary me-2 edit-status"
                     style="cursor:pointer"
                     data-id="${row.id}"
                     data-status="${row.status.replace(/"/g, '&quot;')}"></i>

                  <i class="fa fa-trash text-danger delete-status"
                     style="cursor:pointer"
                     data-id="${row.id}"></i>
                </td>
              </tr>
            `;
        });
        $('#statusTable tbody').html(rows);
    });
}

/* OPEN MODAL */
function openStatusModal() {
    $('#status_id').val('');
    $('#status_name').val('');
    new bootstrap.Modal(document.getElementById('statusModal')).show();
}

/* EDIT */
$(document).on('click', '.edit-status', function () {
    $('#status_id').val($(this).data('id'));
    $('#status_name').val($(this).data('status'));

    new bootstrap.Modal(document.getElementById('statusModal')).show();
});

/* SAVE */
function saveStatus() {
    let id = $('#status_id').val();
    let status = $('#status_name').val();

    let url = '/property-status';
    let method = 'POST';

    if (id) {
        url += '/' + id;
        method = 'PUT';
    }

    $.ajax({
        url: url,
        method: method,
        data: { status },
        success: function () {
            showToast('success', 'Saved');

            bootstrap.Modal.getInstance(
                document.getElementById('statusModal')
            ).hide();

            loadStatus();
        },
        error: function (xhr) {
            showToast('danger', xhr.responseJSON?.message || 'Error');
        }
    });
}

/* DELETE */
$(document).on('click', '.delete-status', function () {
    if (!confirm('Delete this status?')) return;

    $.ajax({
        url: '/property-status/' + $(this).data('id'),
        method: 'DELETE',
        success: function () {
            showToast('success', 'Deleted');
            loadStatus();
        }
    });
});

/* TOAST */
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

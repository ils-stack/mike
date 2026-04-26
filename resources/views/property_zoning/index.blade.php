@extends('layouts.app')

@section('title', 'Property Zoning')

@section('content')
<div class="d-flex justify-content-between mb-2">
    <h5>Property Zoning</h5>
    <button class="btn btn-primary" onclick="openZoningModal()">
        <i class="fa fa-plus"></i> Add Zoning
    </button>
</div>

<table class="table table-bordered table-sm" id="zoningTable">
    <thead class="table-light">
        <tr>
            <th width="60">#</th>
            <th>Zoning</th>
            <th width="120">Actions</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="zoningModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Property Zoning</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="zoning_id">
        <div class="mb-2">
            <label class="form-label">Zoning</label>
            <input type="text" id="zoning_name" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" onclick="saveZoning()">Save</button>
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
    loadZoning();
});

/* LIST */
function loadZoning() {
    $.get('/property-zoning', function (data) {
        let rows = '';
        data.forEach((row, i) => {
            rows += `
              <tr>
                <td>${i + 1}</td>
                <td>${row.zoning}</td>
                <td>
                  <i class="fa fa-edit text-primary me-2 edit-zoning"
                     style="cursor:pointer"
                     data-id="${row.id}"
                     data-zoning="${row.zoning.replace(/"/g, '&quot;')}"></i>

                  <i class="fa fa-trash text-danger delete-zoning"
                     style="cursor:pointer"
                     data-id="${row.id}"></i>
                </td>
              </tr>
            `;
        });
        $('#zoningTable tbody').html(rows);
    });
}

/* OPEN MODAL */
function openZoningModal() {
    $('#zoning_id').val('');
    $('#zoning_name').val('');
    new bootstrap.Modal(document.getElementById('zoningModal')).show();
}

/* EDIT */
$(document).on('click', '.edit-zoning', function () {
    $('#zoning_id').val($(this).data('id'));
    $('#zoning_name').val($(this).data('zoning'));

    new bootstrap.Modal(document.getElementById('zoningModal')).show();
});

/* SAVE */
function saveZoning() {
    let id = $('#zoning_id').val();
    let zoning = $('#zoning_name').val();

    let url = '/property-zoning';
    let method = 'POST';

    if (id) {
        url += '/' + id;
        method = 'PUT';
    }

    $.ajax({
        url: url,
        method: method,
        data: { zoning },
        success: function () {
            showToast('success', 'Saved');

            bootstrap.Modal.getInstance(
                document.getElementById('zoningModal')
            ).hide();

            loadZoning();
        },
        error: function (xhr) {
            showToast('danger', xhr.responseJSON?.message || 'Error');
        }
    });
}

/* DELETE */
$(document).on('click', '.delete-zoning', function () {
    if (!confirm('Delete this zoning?')) return;

    $.ajax({
        url: '/property-zoning/' + $(this).data('id'),
        method: 'DELETE',
        success: function () {
            showToast('success', 'Deleted');
            loadZoning();
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

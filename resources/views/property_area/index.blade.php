@extends('layouts.app')

@section('title', 'Property Area')

@section('content')
<div class="d-flex justify-content-between mb-2">
    <h5>Property Area</h5>
    <button class="btn btn-primary" onclick="openAreaModal()">
        <i class="fa fa-plus"></i> Add Area
    </button>
</div>

<table class="table table-bordered table-sm" id="areaTable">
    <thead class="table-light">
        <tr>
            <th width="60">#</th>
            <th>Area</th>
            <th width="120">Actions</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="areaModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Property Area</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="area_id">
        <div class="mb-2">
            <label class="form-label">Area</label>
            <input type="text" id="area_name" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" onclick="saveArea()">Save</button>
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
    loadAreas();
});

/* LIST */
function loadAreas() {
    $.get('/property-area', function (data) {
        let rows = '';
        data.forEach((row, i) => {
            rows += `
              <tr>
                <td>${i + 1}</td>
                <td>${row.area}</td>
                <td>
                  <i class="fa fa-edit text-primary me-2 edit-area"
                     style="cursor:pointer"
                     data-id="${row.id}"
                     data-area="${row.area.replace(/"/g, '&quot;')}"></i>

                  <i class="fa fa-trash text-danger delete-area"
                     style="cursor:pointer"
                     data-id="${row.id}"></i>
                </td>
              </tr>
            `;
        });
        $('#areaTable tbody').html(rows);
    });
}

/* OPEN MODAL */
function openAreaModal() {
    $('#area_id').val('');
    $('#area_name').val('');
    new bootstrap.Modal(document.getElementById('areaModal')).show();
}

/* EDIT */
$(document).on('click', '.edit-area', function () {
    $('#area_id').val($(this).data('id'));
    $('#area_name').val($(this).data('area'));

    new bootstrap.Modal(document.getElementById('areaModal')).show();
});

/* SAVE */
function saveArea() {
    let id = $('#area_id').val();
    let area = $('#area_name').val();

    let url = '/property-area';
    let method = 'POST';

    if (id) {
        url += '/' + id;
        method = 'PUT';
    }

    $.ajax({
        url: url,
        method: method,
        data: { area },
        success: function () {
            showToast('success', 'Saved');

            bootstrap.Modal.getInstance(
                document.getElementById('areaModal')
            ).hide();

            loadAreas();
        },
        error: function (xhr) {
            showToast('danger', xhr.responseJSON?.message || 'Error');
        }
    });
}

/* DELETE */
$(document).on('click', '.delete-area', function () {
    if (!confirm('Delete this area?')) return;

    $.ajax({
        url: '/property-area/' + $(this).data('id'),
        method: 'DELETE',
        success: function () {
            showToast('success', 'Deleted');
            loadAreas();
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

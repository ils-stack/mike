@extends('layouts.app')

@section('title', 'Property Location')

@section('content')
<div class="d-flex justify-content-between mb-2">
    <h5>Property Location</h5>
    <button class="btn btn-primary" onclick="openLocationModal()">
        <i class="fa fa-plus"></i> Add Location
    </button>
</div>

<table class="table table-bordered table-sm" id="locationTable">
    <thead class="table-light">
        <tr>
            <th width="60">#</th>
            <th>Location</th>
            <th width="120">Actions</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="locationModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Property Location</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="location_id">
        <div class="mb-2">
            <label class="form-label">Location</label>
            <input type="text" id="location_name" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" onclick="saveLocation()">Save</button>
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
    loadLocations();
});

/* LIST */
function loadLocations() {
    $.get('/property-location', function (data) {
        let rows = '';
        data.forEach((row, i) => {
            rows += `
              <tr>
                <td>${i + 1}</td>
                <td>${row.location}</td>
                <td>
                  <i class="fa fa-edit text-primary me-2 edit-location"
                     style="cursor:pointer"
                     data-id="${row.id}"
                     data-location="${row.location.replace(/"/g, '&quot;')}"></i>

                  <i class="fa fa-trash text-danger delete-location"
                     style="cursor:pointer"
                     data-id="${row.id}"></i>
                </td>
              </tr>
            `;
        });
        $('#locationTable tbody').html(rows);
    });
}

/* OPEN MODAL */
function openLocationModal() {
    $('#location_id').val('');
    $('#location_name').val('');
    new bootstrap.Modal(document.getElementById('locationModal')).show();
}

/* EDIT */
$(document).on('click', '.edit-location', function () {
    $('#location_id').val($(this).data('id'));
    $('#location_name').val($(this).data('location'));

    new bootstrap.Modal(document.getElementById('locationModal')).show();
});

/* SAVE */
function saveLocation() {
    let id = $('#location_id').val();
    let location = $('#location_name').val();

    let url = '/property-location';
    let method = 'POST';

    if (id) {
        url += '/' + id;
        method = 'PUT';
    }

    $.ajax({
        url: url,
        method: method,
        data: { location },
        success: function () {
            showToast('success', 'Saved');

            bootstrap.Modal.getInstance(
                document.getElementById('locationModal')
            ).hide();

            loadLocations();
        },
        error: function (xhr) {
            showToast('danger', xhr.responseJSON?.message || 'Error');
        }
    });
}

/* DELETE */
$(document).on('click', '.delete-location', function () {
    if (!confirm('Delete this location?')) return;

    $.ajax({
        url: '/property-location/' + $(this).data('id'),
        method: 'DELETE',
        success: function () {
            showToast('success', 'Deleted');
            loadLocations();
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

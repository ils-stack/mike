@extends('layouts.app')

@section('content')

{{-- Toast container --}}
<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>

<div class = "p-3">
  @include('grids.admin_grid')
</div>



@endsection
@push('page-js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
  $(document).ready(function () {
    // alert(1);
    $('#usersTable').DataTable({
      responsive: true,
      ordering: false
    });

    $('.deactivate-btn').each(function () {
        let btn = $(this);
        let active = btn.data('active');

        if (active == 0) {
            btn.removeClass('btn-warning').addClass('btn-secondary');
        } else {
            btn.removeClass('btn-secondary').addClass('btn-warning');
        }
    });

  });

  $(document).on('click', '.deactivate-btn', function (e) {
      e.preventDefault();

      let btn = $(this);
      let userId = btn.data('id');

      $.ajax({
          url: '/admin/toggle-active/' + userId,
          type: 'POST',
          data: {
              _token: '{{ csrf_token() }}'
          },
          success: function (res) {
              if (res.success) {
                  btn.toggleClass('btn-warning btn-secondary');

                  let msg = res.active == 1
                      ? 'User activated successfully'
                      : 'User deactivated successfully';

                  showToast('success', msg);
              }
          },
          error: function (xhr) {
              let msg = xhr.responseJSON?.message || 'Action not allowed';
              showToast('danger', msg);
          }
      });
  });

  $(document).on('change', '.role-select', function () {
    let select = $(this);
    let userId = select.data('id');
    let role = select.val();

    $.ajax({
        url: '/admin/update-role/' + userId,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            role: role
        },
        success: function (res) {
            if (res.success) {
                showToast('success', 'Role updated successfully');
            }
        },
          error: function (xhr) {
              let msg = xhr.responseJSON?.message || 'Failed to update role';
              showToast('danger', msg);
          }
      });
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

    $(document).on('click', '.delete-btn', function (e) {
      e.preventDefault();

      if (!confirm('Are you sure you want to delete this user?')) return;

      let btn = $(this);
      let userId = btn.data('id');

      $.ajax({
          url: '/admin/delete/' + userId,
          type: 'POST',
          data: {
              _token: '{{ csrf_token() }}'
          },
          success: function (res) {
              if (res.success) {
                  btn.closest('tr').fadeOut();
                  showToast('success', 'User deleted successfully');
              }
          },
          error: function (xhr) {
              let msg = xhr.responseJSON?.message || 'Delete not allowed';
              showToast('danger', msg);
          }
      });
  });

</script>
@endpush

@push('map-loader')
<script>
// do not remove this
let map;
function initMap(){

}
// do not remove this
</script>
@endpush

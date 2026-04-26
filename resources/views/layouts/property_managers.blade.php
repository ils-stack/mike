@extends('layouts.app')

@section('content')
  <nav aria-label="breadcrumb" class="mt-1">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">Property Managers</li>
    </ol>
  </nav>

  <button type="button" class="btn btn-primary mb-3"
          data-bs-toggle="modal"
          data-bs-target="#managerModal"
          onclick="resetManagerForm()">
    Add Manager
  </button>

  @include('property_manager.list')
  @include('modals.add_property_manager')

  <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
@endsection

@push('page-js')
  <script>
  function showToast(type, message) {
      const toast = $(`
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

  function initMap(){

  }

  $('#managerModal').on('show.bs.modal', function () {
    // alert(1);
    $('#a_properties').val([]).selectpicker('refresh');
  });

</script>
@endpush

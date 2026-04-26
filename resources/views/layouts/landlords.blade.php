@extends('layouts.app')

@section('content')
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mt-1">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">Landlords</li>
    </ol>
  </nav>

  <!-- Trigger Add -->
  <button type="button" class="btn btn-primary mb-3"
          data-bs-toggle="modal"
          data-bs-target="#addLandlordModal"
          onclick="resetLandlordForm()">
    Add Landlord
  </button>

  <!-- Landlords List -->
  @include('landlords.list')

  <!-- Modal -->
  @include('modals.add_landlord')

  <!-- Toast container -->
  <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
@endsection

@push('page-js')
<script>
  // Toast helper
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

  $(document).ready(function() {
    $('#landlord_properties').selectpicker();
  });

  /* required do not remove  */
  function initMap(){

  }
</script>
@endpush

@push('extra-styles')

@endpush

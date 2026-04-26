@extends('layouts.app')

@section('title', 'Dashboard')

@push('extra-styles')
<style>
    #map {
        width: 100%;
        height: calc(100vh - 120px);
        min-height: 600px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    @include('common.search_address')

    <div class="row g-0">

      <!-- MAP : 10 columns -->
      <div class="col-md-10">
        <div id="map" style="width:100%; height: 80vh;"></div>
      </div>

      <!-- RIGHT WORK PANEL : 2 columns -->
      <div class="col-md-2 border-start bg-light" id="rightPanel"
           >

          <!-- RIGHT PANEL HEADER -->
          <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
            <h6 class="mb-0 text-primary p-2">Properties</h6>
            <button class="btn btn-sm btn-outline-primary me-2" onclick="resetMap()">
              Reset zoom
            </button>
          </div>

          <!-- ZOOM LEVEL -->
          <div class="m-2 pb-2 border-bottom bg-primary text-white text-center">
              <small style="font-weight:bold;">
                Zoom level: <span id="mapZoomLevel">6</span>
              </small>
            </div>


          <div id="propertyList" class="ms-2" style="height: 65vh; overflow-y:auto;"></div>


      </div>

    </div>

    <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

</div>

@include('modals.add_property')
@include('modals.map_instructions')
@endsection

@push('maps-script')
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
@endpush

@push('map-loader')
  @include('layouts.partials.dashboard_js')
@endpush

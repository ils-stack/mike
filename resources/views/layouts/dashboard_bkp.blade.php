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

    <!-- <h4 class="mb-3">Dashboard | Welcome, {{ auth()->user()->name }}</h4> -->

    @include('common.search_address')

    <!-- <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
        <div class="btn-group mb-2 mb-md-0" role="group" aria-label="Map type">
            <button id="btn-map" type="button" class="btn btn-outline-primary active">Map</button>
            <button id="btn-satellite" type="button" class="btn btn-outline-primary">Satellite</button>
        </div>

        <button id="btnAddPin" class="btn btn-warning">
            <i class="fas fa-map-pin me-1"></i> Add Pin
        </button>
        <h5>Zoom to desired location & right click map to add a new property</h5>
    </div> -->

    <div id="map"></div>

    <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

</div>

<!-- Modal Properties -->
@include('modals.add_property')
@include('modals.map_instructions')

@endsection

@push('maps-script')
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
@endpush

@push('map-loader')
<script>
window.initMap = function () {

    const locations = @json($locations);
    let activeTempMarker = null;

    const map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: -30.5595, lng: 22.9375 },
        zoom: 6,
        mapTypeId: 'hybrid',
        gestureHandling: 'greedy',
        streetViewControl: true
    });

    /* ===============================
       EXISTING PROPERTY MARKERS
    =============================== */
    const markers = locations.map(loc => {
        const marker = new google.maps.Marker({
            position: {
                lat: parseFloat(loc.latitude),
                lng: parseFloat(loc.longitude)
            },
            icon: 'http://maps.google.com/mapfiles/ms/icons/orange-dot.png',
            title: loc.building_name
        });

        marker.buildingId = loc.id;
        return marker;
    });

    new markerClusterer.MarkerClusterer({
        map,
        markers
    });

    const infoWindow = new google.maps.InfoWindow();

    markers.forEach(marker => {
        marker.addListener('click', () => {
            infoWindow.setContent(`
                <strong>${marker.getTitle()}</strong><br><br>
                <a class="btn btn-outline-primary"
                   href="property-details/${marker.buildingId}"
                   target="_blank">
                    Property Details
                </a>
            `);
            infoWindow.open(map, marker);
        });
    });

    /* ===============================
       HELPER: OPEN ADD PROPERTY MODAL
    =============================== */
    function openAddPropertyModal(lat, lng) {
        $('#add_property')[0].reset();
        $('#prop_lat').val(lat);
        $('#prop_lon').val(lng);
        $('#property_landlords_edit').selectpicker('deselectAll').selectpicker('render');
        $('#prop_id').remove();
        $('#addPropertyModal').modal('show');
    }

    /* ===============================
       SEARCH ADDRESS FLOW
    =============================== */
    const input = document.getElementById('searchBox');
    const autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.addListener('place_changed', function () {
        const place = autocomplete.getPlace();

        if (!place.geometry) {
            alert("Location not found.");
            return;
        }

        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();

        $('#fullAddress').val(place.formatted_address);
        $('#latitude').val(lat);
        $('#longitude').val(lng);

        map.setCenter(place.geometry.location);
        map.setZoom(16);

        if (activeTempMarker) {
            activeTempMarker.setMap(null);
        }

        activeTempMarker = new google.maps.Marker({
            map,
            position: place.geometry.location,
            draggable: true
        });

        activeTempMarker.addListener('dragend', e => {
            $('#latitude').val(e.latLng.lat());
            $('#longitude').val(e.latLng.lng());
        });

        activeTempMarker.addListener('click', () => {
            openAddPropertyModal(
                activeTempMarker.getPosition().lat(),
                activeTempMarker.getPosition().lng()
            );
        });
    });

    /* ===============================
       ALTERNATE FLOW: CLICK MAP
    =============================== */
    map.addListener('click', function (e) {

        if (activeTempMarker) {
            activeTempMarker.setMap(null);
        }

        activeTempMarker = new google.maps.Marker({
            map,
            position: e.latLng,
            draggable: true
        });

        const lat = e.latLng.lat();
        const lng = e.latLng.lng();

        $('#latitude').val(lat);
        $('#longitude').val(lng);

        activeTempMarker.addListener('dragend', event => {
            $('#latitude').val(event.latLng.lat());
            $('#longitude').val(event.latLng.lng());
        });

        activeTempMarker.addListener('click', () => {
            openAddPropertyModal(
                activeTempMarker.getPosition().lat(),
                activeTempMarker.getPosition().lng()
            );
        });
    });
};

function showToast(type, message) {
  // alert(`${type} ${message}`)
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
</script>
@endpush

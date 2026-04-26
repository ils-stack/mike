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
        <div id="map" style="width:100%; height: calc(100vh - 80px);"></div>
      </div>

      <!-- RIGHT WORK PANEL : 2 columns -->
      <div class="col-md-2 border-start bg-light" id="rightPanel"
           style="height: calc(100vh - 80px); overflow-y:auto;">

          <!-- RIGHT PANEL HEADER -->
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Properties</h6>
            <button class="btn btn-sm btn-outline-secondary" onclick="resetMap()">
              Reset
            </button>
          </div>

          <div id="propertyList"></div>


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
<script>
window.initMap = function () {

    // alert(1);

    const locations = @json($locations);
    let activeTempMarker = null;

    // const map = new google.maps.Map(document.getElementById('map'), {
    //     center: { lat: -30.5595, lng: 22.9375 },
    //     zoom: 6,
    //     mapTypeId: 'hybrid',
    //     gestureHandling: 'greedy',
    //     streetViewControl: true
    // });

    // map init
    const map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: -30.5595, lng: 22.9375 },
        zoom: 6,
        mapTypeId: 'hybrid',
        gestureHandling: 'greedy',
        streetViewControl: true,

        styles: [
            {
                featureType: "poi",
                stylers: [{ visibility: "off" }]
            },
            {
                featureType: "transit",
                stylers: [{ visibility: "off" }]
            },
            {
                featureType: "administrative",
                stylers: [{ visibility: "off" }]
            },
            {
                featureType: "road",
                elementType: "labels.icon",
                stylers: [{ visibility: "off" }]
            }
        ]
    });

    const markers = locations.map(loc => {

        const marker = new google.maps.Marker({
            position: {
                lat: parseFloat(loc.latitude),
                lng: parseFloat(loc.longitude)
            },
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 10,
                fillColor: loc.marker_color || "#43A047",
                fillOpacity: 0.95,
                strokeColor: "#ffffff",
                strokeWeight: 2
            },
            label: {
                text: loc.marker_letter || "N",
                color: "#ffffff",
                fontSize: "12px",
                fontWeight: "bold"
            },
            title: loc.building_name
        });

        marker.buildingId = loc.id;
        return marker;
    });

    /* ===============================
       CLUSTER (BLUE WITH COUNT)
    =============================== */
    new markerClusterer.MarkerClusterer({
        map,
        markers,
        renderer: {
            render({ count, position }) {
                return new google.maps.Marker({
                    position,
                    label: {
                        text: String(count),
                        color: "white",
                        fontSize: "13px",
                        fontWeight: "bold"
                    },
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 18,
                        fillColor: "#1E88E5",
                        fillOpacity: 0.9,
                        strokeColor: "#ffffff",
                        strokeWeight: 2
                    }
                });
            }
        }
    });

    /* ===============================
       INFO WINDOW
    =============================== */
    // const infoWindow = new google.maps.InfoWindow();
    //
    // markers.forEach(marker => {
    //     marker.addListener('click', () => {
    //         infoWindow.setContent(`
    //             <strong>${marker.getTitle()}</strong><br><br>
    //             <a class="btn btn-outline-primary"
    //                href="property-details/${marker.buildingId}"
    //                target="_blank">
    //                 Property Details
    //             </a>
    //         `);
    //         infoWindow.open(map, marker);
    //     });
    // });

    const infoWindow = new google.maps.InfoWindow();

    markers.forEach(marker => {
      marker.addListener('click', () => {

        infoWindow.setContent(
          '<div style="min-width:360px">Loading...</div>'
        );
        infoWindow.open(map, marker);

        $.get(`/ajax/property/${marker.buildingId}/infobox`, function (html) {
          infoWindow.setContent(html);
        });

      });
    });

    /* ===============================
       ADD PROPERTY MODAL
    =============================== */
    function openAddPropertyModal(lat, lng) {
        $('#add_property')[0].reset();
        $('#prop_lat').val(lat);
        $('#prop_lon').val(lng);
        $('#property_landlords_edit')
            .selectpicker('deselectAll')
            .selectpicker('render');
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
        if (!place.geometry) return;

        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();

        $('#fullAddress').val(place.formatted_address);
        $('#latitude').val(lat);
        $('#longitude').val(lng);

        map.setCenter(place.geometry.location);
        map.setZoom(16);

        if (activeTempMarker) activeTempMarker.setMap(null);

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
       MAP CLICK FLOW
    =============================== */
    map.addListener('rightclick', function (e) {

        if (activeTempMarker) activeTempMarker.setMap(null);

        activeTempMarker = new google.maps.Marker({
            map,
            position: e.latLng,
            draggable: true
        });

        $('#latitude').val(e.latLng.lat());
        $('#longitude').val(e.latLng.lng());

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

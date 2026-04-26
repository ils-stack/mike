@extends('layouts.app')

@section('content')

<style>
  #map {
    height: 86vh;      /* Required */
    width: 100%;        /* Optional, but usually desired */
  }
</style>


  @include('modals.add_property_in_maps')

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Map -->
  <div id="map"></div>

  <script>
    let map;

    $(document).ready(function () {
      // ✅ Attach initMap to the window object so Google Maps API can call it
      window.initMap = function () {
        map = new google.maps.Map(document.getElementById("map"), {
          center: { lat: -33.9249, lng: 18.4241 }, // Cape Town
          zoom: 16,
          mapTypeId: 'satellite'
        });

        // 🟡 Add click listener to show modal and add marker
        map.addListener("click", (e) => {
          new google.maps.Marker({
            position: e.latLng,
            map: map
          });

          const modal = new bootstrap.Modal(document.getElementById('propertyModal'));
          modal.show();
        });
      };
    });
  </script>

@endsection

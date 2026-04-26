@extends('layouts.app')   {{-- adjust the namespace if your layout path is different --}}

@section('title', 'Property List')

@section('content')
  <div class="container-fluid">

      @include('common.search_address')

        <div class="row">
          <div class="col-sm-12 mb-3">
            <!-- Google Map -->
            <div id="map"></div>
          </div>
        </div>


    </div>



  </div>

  <!-- Modal Properties -->
  <!-- @ include('modals.add_property') -->

  <style>
    .sidebar {
      background-color: #212529;
      min-height: 100vh;
      color: white;
    }
    .sidebar a {
      color: #ccc;
      text-decoration: none;
      display: block;
      padding: 10px 15px;
    }
    .sidebar a:hover {
      background-color: #343a40;
      color: #fff;
    }
    .topbar {
      background-color: #0d1c35;
      color: white;
      padding: 10px 20px;
    }

    #map {
     width: 100%;
     height: 72vh;
   }
  </style>

  @push('maps-script')
  <script>
    let map;
    function initMap(){
      // This runs AFTER Google Maps JS has loaded
      $(function () {
        // BA: init the maps
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: -30.5595, lng: 22.9375 }, // South-Africa centroid
            zoom  : 6,
            mapTypeId: 'roadmap',
            gestureHandling: 'greedy',
        });

        // console.log(map);

        // BA: init the search
        const input = document.getElementById('searchBox');
        const autocomplete = new google.maps.places.Autocomplete(input);

        autocomplete.addListener('place_changed', function () {
          const place = autocomplete.getPlace();

          if (!place.geometry) {
            alert("No location data available.");
            return;
          }

          const address = place.formatted_address;
          const lat = place.geometry.location.lat();
          const lng = place.geometry.location.lng();

          $('#fullAddress').val(address);
          $('#latitude').val(lat);
          $('#longitude').val(lng);

          //re-center on search
          map.setCenter(place.geometry.location);
          map.setZoom(14);

          //add a marker to the searched location
          const marker = new google.maps.Marker({
            map: map,
            position: place.geometry.location,
            draggable: true
          });

          google.maps.event.addListener(marker, 'dragend', function (event) {
            const newLat = event.latLng.lat();
            const newLng = event.latLng.lng();

            $('#latitude').val(newLat);
            $('#longitude').val(newLng);
          });


        });
      });
    }

    function populate_prop(){
      $("#add_property")[0].reset();

      $("#prop_addr").val($("#searchBox").val());
      $("#prop_lat").val($("#latitude").val());
      $("#prop_lon").val($("#longitude").val());
    }

    function save_property(){
      var formData = new FormData();

      formData.append('_token',"{{ csrf_token() }}");
      formData.append('building_name',$("#building_name").val());
      formData.append('blurb',$("#blurb").val());
      formData.append('type_id',$("#type_id").val());
      formData.append('status_type',$("#status_type").val());
      formData.append('erf_no',$("#erf_no").val());
      formData.append('erf_size',$("#erf_size").val());
      formData.append('gla',$("#gla").val());
      formData.append('zoning',$("#zoning").val());
      formData.append('property_locale',$("#property_locale").val());
      formData.append('latitude',$("#latitude").val());
      formData.append('longitude',$("#longitude").val());

      $.ajax({
        type:'POST',
        url:"{{ route('ajaxSaveProp.post') }}",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success:function(data){
          // console.log(data);
          // if(data.length>0){
            // console.log(1);

            var toastEl = document.getElementById('saveToast');
            var toast = new bootstrap.Toast(toastEl);
            toast.show();

          // }
        }
      });
    }
  </script>
  @endpush

@endsection

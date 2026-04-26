<script>

/* =========================================================
   UPDATED initMap – VIEWPORT AJAX + RIGHT PANEL + RESET
   (keeps your existing logic intact)
========================================================= */

window.initMap = function () {

    const locations = @json($locations);
    let activeTempMarker = null;
    let markers = [];
    let cluster = null;
    let debounceTimer = null;

    const defaultCenter = { lat: -30.5595, lng: 22.9375 };
    const defaultZoom   = 6;

    /* ===============================
       MAP INIT
    =============================== */
    const map = new google.maps.Map(document.getElementById('map'), {
        center: defaultCenter,
        zoom: defaultZoom,
        mapTypeId: 'hybrid',
        gestureHandling: 'greedy',

        streetViewControl: false,          // ✅ disable pegman
        clickableIcons: false,             // ✅ prevent POI / SV clicks
        disableDoubleClickZoom: true,      // ✅ stops accidental zooms

        styles: [
            { featureType: "poi", stylers: [{ visibility: "off" }] },
            { featureType: "transit", stylers: [{ visibility: "off" }] },
            { featureType: "administrative", stylers: [{ visibility: "off" }] },
            { featureType: "road", elementType: "labels.icon", stylers: [{ visibility: "off" }] }
        ]
    });

    /* ===============================
       INFO WINDOW
    =============================== */
    const infoWindow = new google.maps.InfoWindow({
        disableAutoPan: true   // ✅ CRITICAL: prevents map jump
    });

    map.setTilt(0); //avoid 3d mode
    map.getStreetView().setVisible(false);

    /* ===============================
       LOAD PROPERTIES IN VIEWPORT
    =============================== */
    function loadViewportProperties() {

        const bounds = map.getBounds();
        if (!bounds) return;

        const ne = bounds.getNorthEast();
        const sw = bounds.getSouthWest();

        $.get('/ajax/properties-in-bounds', {
            ne_lat: ne.lat(),
            ne_lng: ne.lng(),
            sw_lat: sw.lat(),
            sw_lng: sw.lng()
        }, function (res) {
            renderMarkers(res);
            renderRightPanel(res);
        });
    }

    /* ===============================
       MARKER + CLUSTER RENDER
    =============================== */
    function renderMarkers(properties) {

        if (cluster) cluster.clearMarkers();
        markers.forEach(m => m.setMap(null));
        markers = [];

        properties.forEach(loc => {

            // console.log(loc.marker_letter)

            if (!loc.latitude || !loc.longitude) return;

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
                    text: loc.marker_letter || "-",
                    color: "#ffffff",
                    fontSize: "12px",
                    fontWeight: "bold"
                },
                title: loc.building_name
            });

            marker.buildingId = loc.id;

            marker.addListener('click', (e) => {

                if (e.domEvent) e.domEvent.stopPropagation();

                infoWindow.close();

                infoWindow.setContent('<div style="min-width:360px">Loading...</div>');
                infoWindow.open({
                    map,
                    anchor: marker,
                    shouldFocus: false
                });

                $.get(`/ajax/property/${marker.buildingId}/infobox`, function (html) {
                    infoWindow.setContent(html);
                });
            });

            markers.push(marker);
        });


        cluster = new markerClusterer.MarkerClusterer({
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
    }

    /* ===============================
       RIGHT PANEL LIST
    =============================== */
    function renderRightPanel(properties) {

        let html = '';

        if (!properties.length) {
            html = '<small class="text-muted">No properties in view</small>';
        }

        properties.forEach(p => {
            html += `
              <div class="d-flex justify-content-between align-items-center border-bottom py-1"
                   style="cursor:pointer"
                   onclick="focusProperty(${p.latitude}, ${p.longitude})">

                <div>
                  <strong class="fs-7">${p.building_name}</strong><br>
                  ${p.property_locale ? `<small>${p.property_locale}</small>` : ``}
                </div>

                <div>
                  <a class="btn btn-outline-primary btn-sm"
                     href="/property-details/${p.id}"
                     target="_blank"
                     onclick="event.stopPropagation()">
                    Details
                  </a>
                </div>

              </div>
            `;
        });


        $('#propertyList').html(html);
    }


    /* ===============================
       FOCUS / RESET
    =============================== */
    const MAX_AUTO_ZOOM = 17;

    window.resetMap = function () {
        map.setZoom(defaultZoom);
        map.panTo(defaultCenter);
    };

    /* ===============================
       TRIGGER ON ZOOM / DRAG
    =============================== */
    map.addListener('idle', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(loadViewportProperties, 400);
    });

    /* ===============================
       ADD PROPERTY MODAL (UNCHANGED)
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
       SEARCH ADDRESS FLOW (UNCHANGED)
    =============================== */
    const input = document.getElementById('searchBox');
    const autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.addListener('place_changed', function () {
        const place = autocomplete.getPlace();
        if (!place.geometry) return;

        map.setCenter(place.geometry.location);
        map.setZoom(16);

        if (activeTempMarker) activeTempMarker.setMap(null);

        activeTempMarker = new google.maps.Marker({
            map,
            position: place.geometry.location,
            draggable: true
        });

        activeTempMarker.addListener('click', () => {
            openAddPropertyModal(
                activeTempMarker.getPosition().lat(),
                activeTempMarker.getPosition().lng()
            );
        });
    });

    /* ===============================
       MAP RIGHT CLICK (UNCHANGED)
    =============================== */
    map.addListener('rightclick', function (e) {

        if (activeTempMarker) activeTempMarker.setMap(null);

        activeTempMarker = new google.maps.Marker({
            map,
            position: e.latLng,
            draggable: true
        });

        activeTempMarker.addListener('click', () => {
            openAddPropertyModal(
                activeTempMarker.getPosition().lat(),
                activeTempMarker.getPosition().lng()
            );
        });
    });

    /* ===============================
   PANEL CLICK – ONLY HERE WE PAN
   =============================== */
   window.focusProperty = function (lat, lng) {
       map.panTo({
           lat: parseFloat(lat),
           lng: parseFloat(lng)
       });

       if (map.getZoom() < 14) {
           map.setZoom(14);
       }
   };



    /* ===============================
       PREVENT MAP CLICK CLOSING INFOWINDOW
    =============================== */
    map.addListener('click', function () {
        // DO NOTHING
        // (prevents Google default behaviour)
    });


    /* ===============================
       VIEWPORT AJAX – DO NOT REFRESH
       WHEN INFOWINDOW IS OPEN
    =============================== */
    map.addListener('idle', function () {

        if (infoWindow.getMap()) return; // ✅ key line

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(loadViewportProperties, 400);
    });

    /* ===============================
       UPDATE ZOOM LEVEL DISPLAY
    =============================== */
    function updateZoomLevel() {
      document.getElementById('mapZoomLevel').innerText = map.getZoom();
    }

    // initial
    updateZoomLevel();

    // on zoom change
    map.addListener('zoom_changed', function () {
      updateZoomLevel();
    });

    map.addListener('zoom_changed', function () {
        // if (map.getZoom() >= 18) {
        //     map.setZoom(17);
        // }
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

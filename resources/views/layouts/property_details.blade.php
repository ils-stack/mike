@extends('layouts.app')

@section('title', 'Property Details')

@section('content')
  <div class="container-fluid">

    <!-- BA: selected property id -->
    <input type="hidden" id="property_id" value="{{ $property->id }}">

    <div class="row">

      <!-- Main Content -->
      <div class="col-md-12">
        <div class="p-4 card shadow">
          <!-- Images and Map -->
          <div class="row mb-4 property-section">
            <div class="col-md-6">
              @include('gallery.property_gallery')
            </div>
            <div class="col-md-6">
              <div class="p-5 bg-light rounded-5 mt-2">
                <div id="map"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-12">
        <div class="row">

          <div class="col-md-6">
            <!-- Property Details -->
            @include('properties.prop_det_card')

            <!-- Property Specifications -->
            @include('properties.property_specs')

            <!-- Landlord details -->
            @include('properties.landlords')

            <!-- Property Manager -->
            @include('properties.property_manager')

            <!-- Property Tenants -->
            @include('properties.property_tenants')

            <!-- Property Agents -->
            @include('properties.property_agents')

            <!-- Property notes -->
            @include('properties.property_notes')
          </div>

          <div class="col-md-6">
            <!-- Property units -->
            @include('properties.property_units')
          </div>

        </div>
      </div>

    </div>

    <!-- Modals -->
    @include('modals.add_property')  <!-- Add/Edit Property modal -->
    <!-- @ include('modals.add_tenant') -->
    <!-- @ include('modals.add_unit') -->
    <!-- @ include('modals.add_property_manager') -->

    <!-- Assign Landlords Modal -->
    <div class="modal fade" id="assignLandlordsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Assign Landlords</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <select id="property_landlords_assign" class="selectpicker form-control" multiple
                    data-live-search="true" data-actions-box="true" data-width="100%">
              @foreach($landlords as $landlord)
                <option value="{{ $landlord->id }}">{{ $landlord->company_name }}</option>
              @endforeach
            </select>
            <small class="text-muted">Search & tick landlords to assign</small>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="savePropertyLandlords({{ $property->id }})">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast container -->
    <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

    <!-- Modal -->
    @include('modals.add_landlord')

    @include('modals.add_property_manager')

    @include('modals.add_tenant')

    @include('modals.add_agent')

    @include('modals.add_unit')

    @include('modals.property_asset_gallery')

    @include('gallery.unit_gallery')

    @include('property_docs.modal')

  </div>

  <style>
    .sidebar { background-color: #212529; min-height: 100vh; color: white; }
    .sidebar a { color: #ccc; text-decoration: none; display: block; padding: 10px 15px; }
    .sidebar a:hover { background-color: #343a40; color: #fff; }
    .topbar { background-color: #0d1c35; color: white; padding: 10px 20px; }
    .property-section img { width: 100%; border-radius: 4px; }
    .map-embed { border-radius: 4px; width: 100%; height: 250px; }
    .info-box { background: #f8f9fa; padding: 1rem; border-radius: 4px; color:#000; }
    #map { width: 100%; height: 500px; border-radius: 0.2rem; }
    .property-thumb { cursor: pointer; transition: transform 0.2s ease-in-out; }
    .property-thumb:hover { transform: scale(1.03); }

    /* stacked modal fix */
    .modal-stack {
      overflow: hidden;
    }

    #unitGalleryModal {
      z-index: 1060;
    }

    .modal-backdrop.show:nth-of-type(2) {
      z-index: 1055;
    }

  </style>

  @push('page-js')

  <!-- unit gallery -->

  <script>
  $(document).ready(function () {

    // Browse button
    $('#unitBrowseBtn').on('click', () => $('#unitFileInput').click());

    // File input
    // $('#unitFileInput').on('change', function () {
    //   if (this.files.length) uploadFile(this.files[0]);
    // });

    // File input
    $('#unitFileInput').on('change', function () {
      // if (this.files.length) unitUploadFile(this.files[0]);
      unitUploadFiles(this.files);
    });

    $('#unitDropZone')
      .on('dragover', function (e) {
        e.preventDefault();
        $(this).addClass('bg-secondary-subtle');
      })
      .on('dragleave', function () {
        $(this).removeClass('bg-secondary-subtle');
      })
      .on('drop', function (e) {
        e.preventDefault();
        $(this).removeClass('bg-secondary-subtle');
        unitUploadFiles(e.originalEvent.dataTransfer.files);
      });

    $('#unitBrowseBtn').on('click', function () {
      $('#unitFileInput').click();
    });


    // Drag & drop
    // $('#unitDropZone')
    //   .on('dragover', function (e) {
    //     e.preventDefault();
    //     $(this).addClass('bg-secondary text-white');
    //   })
    //   .on('dragleave drop', function (e) {
    //     e.preventDefault();
    //     $(this).removeClass('bg-secondary text-white');
    //   })
    //   .on('drop', function (e) {
    //     if (e.originalEvent.dataTransfer.files.length) {
    //       unitUploadFile(e.originalEvent.dataTransfer.files[0]);
    //     }
    //   });


    // BA: multi file upload function
    function unitUploadFiles(files) {
      Array.from(files).forEach(file => {
        unitUploadFile(file);
      });
    }

    // Upload logic
    function unitUploadFile(file) {
      let formData = new FormData($('#unitAssetUploadForm')[0]);
      formData.set('file', file);

      $.ajax({
        url: '/asset-library/upload',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function () {
          showToast('success', 'File uploaded successfully');

          // 🔥 refresh thumbnails immediately
          if (typeof loadUnitAssets === 'function') {
              loadUnitAssets();
          }
        },
        error: function (xhr) {
          let msg = xhr.responseJSON?.message || 'Upload failed';
          showToast('danger', msg);
        }
      });
    }


    // 🔁 Load UNIT assets when unit gallery modal opens
    $('#unitGalleryModal').on('show.bs.modal', function () {
      loadUnitAssets();
    });

    function loadUnitAssets() {
      const unitId = $('#unit_id').val();

      // console.log(unitId);

      if (!unitId) {
        $('#unit-asset-list').html(
          '<div class="col-12 text-center text-muted">Save unit first to add images.</div>'
        );
        return;
      }

      $('#unit-asset-list').html(
        '<div class="col-12 text-center text-muted">Loading images…</div>'
      );

      $.get(`/ajax/unit/${unitId}/assets`, function (assets) {
        let html = '';

        if (!assets.length) {
          html = '<div class="col-12 text-center text-muted">No images found.</div>';
        } else {
          $.each(assets, function (_, asset) {
            html += renderUnitAssetCard(asset);
          });
        }

        $('#unit-asset-list').html(html);
      });
    }

    // 🔀 Assign / Unassign UNIT image
    window.toggleUnitAsset = function (assetId) {
      $.post('/ajax/unit/asset-toggle', {
        _token: '{{ csrf_token() }}',
        asset_id: assetId,
        unit_id: $('#unit_id').val()
      }, loadUnitAssets)
      .fail(function () {
        alert('Failed to update unit image');
      });
    };

    function renderUnitAssetCard(asset) {
      return `
        <div class="col-md-3 col-sm-4 col-6">
          <div class="card shadow-sm h-100">
            <img src="${asset.public_url}"
                 class="card-img-top"
                 style="height:160px; object-fit:cover;">
            <div class="card-body p-2 text-center">
              <button class="btn btn-sm ${asset.assigned ? 'btn-danger' : 'btn-success'} w-100"
                      onclick="toggleUnitAsset(${asset.id})">
                ${asset.assigned ? 'Unassign' : 'Assign'}
              </button>
            </div>
          </div>
        </div>`;
    }

  });
  </script>




  <!-- Image gallery -->

  <script>
  $(document).ready(function () {

    // drag and drop Images

    // Load assets when modal opens
    $('#propertyAssetGalleryModal').on('show.bs.modal', function () {
      loadPropertyAssets();
    });

    // Reload page when modal closes (as agreed earlier)
    $('#propertyAssetGalleryModal').on('hidden.bs.modal', function () {
      location.reload();
    });

    // Browse button
    $('#browseBtn').on('click', () => $('#fileInput').click());

    // File input (multiple)
    $('#fileInput').on('change', function () {
      if (this.files.length) {
        uploadFiles(this.files);
      }
    });

    // Drag & drop
    $('#dropZone')
      .on('dragover', function (e) {
        e.preventDefault();
        $(this).addClass('bg-secondary text-white');
      })
      .on('dragleave drop', function (e) {
        e.preventDefault();
        $(this).removeClass('bg-secondary text-white');
      })
      .on('drop', function (e) {
        let files = e.originalEvent.dataTransfer.files;
        if (files.length) {
          uploadFiles(files);
        }
      });

    // 🔁 Upload multiple files
    function uploadFiles(files) {
      Array.from(files).forEach(file => uploadFile(file));
    }

    // Upload logic (unchanged, reused)
    function uploadFile(file) {
      let formData = new FormData($('#assetUploadForm')[0]);
      formData.set('file', file);

      $.ajax({
        url: '/asset-library/upload',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function () {
          showToast('success', 'File uploaded successfully');

          // 🔥 refresh thumbnails immediately (PROPERTIES)
          if (typeof loadPropertyAssets === 'function') {
            loadPropertyAssets();
          }
        },
        error: function (xhr) {
          let msg = xhr.responseJSON?.message || 'Upload failed';
          showToast('danger', msg);
        }
      });
    }


    // drag and drop Images

    // modal lifecycle
    $('#propertyAssetGalleryModal')
      .on('show.bs.modal', loadPropertyAssets)
      .on('hidden.bs.modal', function () {
        location.reload();
      });

    function loadPropertyAssets() {
      const propertyId = $('#property_id').val();

      $('#property-asset-list').html(
        '<div class="col-12 text-center text-muted">Loading images…</div>'
      );

      $.get(`/ajax/property/${propertyId}/assets`, function (assets) {
        let html = '';

        if (!assets.length) {
          html = '<div class="col-12 text-center text-muted">No images found.</div>';
        } else {
          $.each(assets, function (_, asset) {
            html += renderAssetCard(asset);
          });
        }

        $('#property-asset-list').html(html);
      });
    }

    window.togglePropertyAsset = function (assetId) {
      $.post('/ajax/property/asset-toggle', {
        _token: '{{ csrf_token() }}',
        asset_id: assetId,
        property_id: $('#property_id').val()
      }, loadPropertyAssets)
      .fail(function () {
        alert('Failed to update image assignment');
      });
    };

    function renderAssetCard(asset) {
      return `
        <div class="col-md-3 col-sm-4 col-6">
          <div class="card shadow-sm h-100">
            <img src="${asset.public_url}"
                 class="card-img-top"
                 style="height:160px; object-fit:cover;">
            <div class="card-body p-2 text-center">
              <button class="btn btn-sm ${asset.assigned ? 'btn-danger' : 'btn-success'} w-100"
                      onclick="togglePropertyAsset(${asset.id})">
                ${asset.assigned ? 'Unassign' : 'Assign'}
              </button>
            </div>
          </div>
        </div>`;
    }

  });
  </script>



  <!-- Image gallery -->

  <script>
    let map;

    // Google Maps init
    window.initMap = function () {

      const lat = parseFloat('{{ $property->latitude ?? '' }}') || -33.9249;
      const lng = parseFloat('{{ $property->longitude ?? '' }}') || 18.4241;

      let debounceTimer = null;
      let activeTempMarker = null;
      let markers = [];
      let cluster = null;

      const map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: lat, lng: lng },
        zoom: 16,
        mapTypeId: 'satellite',
        gestureHandling: 'greedy'
      });

    /* ===============================
       TRIGGER ON ZOOM / DRAG
    =============================== */
    map.addListener('idle', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(loadViewportProperties, 400);
    });

    /* ===============================
       INFO WINDOW
    =============================== */
    const infoWindow = new google.maps.InfoWindow({
        disableAutoPan: true   // ✅ CRITICAL: prevents map jump
    });

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
            // renderRightPanel(res);
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
    };

    $(document).ready(function () {
      // init bootstrap-select
      $('#property_landlords_assign').selectpicker();
    });

    // Open modal with pre-selected landlords
    function assignLandlords(propertyId) {
      $.get(`/ajax/property/${propertyId}`, function(data) {
        if (data.landlords && data.landlords.length > 0) {
          let ids = data.landlords.map(l => l.id.toString());
          // $('#property_landlords_assign').selectpicker('val', ids).selectpicker('refresh');
          $('#property_landlords_assign').selectpicker('val', ids).selectpicker('render');
        } else {
          // $('#property_landlords_assign').selectpicker('deselectAll').selectpicker('refresh');
          $('#property_landlords_assign').selectpicker('deselectAll').selectpicker('render');
        }
        new bootstrap.Modal(document.getElementById('assignLandlordsModal')).show();
      });
    }

    // Save landlords for property
    function savePropertyLandlords(propertyId) {
      let selected = $('#property_landlords_assign').val();
      // console.log("Selected landlords:", selected);

      $.ajax({
        url: `/ajax/property/${propertyId}/landlords`,
        type: 'POST',
        data: {
          _token: "{{ csrf_token() }}",
          landlord_ids: selected
        },
        success: function(res) {
          showToast('success', res.message || 'Landlords updated');

          if (res.landlords) {
            let html = '<ul>';
            res.landlords.forEach(function(l) {
              html += `<li>${l.company_name} (${l.contact_person || ''})</li>`;
            });
            html += '</ul>';
            $('#landlord-list').html(html);
          }

          bootstrap.Modal.getInstance(document.getElementById('assignLandlordsModal')).hide();
        },
        error: function(xhr) {
          let msg = xhr.responseJSON?.message || "Failed to assign landlords";
          showToast('danger', msg);
        }
      });
    }

    function showToast(type, message) {
      // alert(1);
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

    /* required do not remove  */
    function initMap(){

    }
  </script>

  // unit image sorting

  <script>


  function openUnitImageModal(url) {
    $('#unitImagePreview').attr('src', url);
    $('#unitImagePreviewModal').modal('show');
  }

  function unassignUnitImage(unitId, assetId) {
    $.post('/ajax/unit/asset-toggle', {
      unit_id: unitId,
      asset_id: assetId
    }, function () {
      location.reload(); // or reload section
    });
  }

  $('.sortable-unit-images').sortable({
    items: '.unit-image-row',
    handle: '.drag-handle',     // 🔥 ONLY handle drags
    axis: 'y',
    tolerance: 'pointer',
    update: function () {

      let unitId = $(this).closest('.unit-image-strip').data('unit-id');
      let order = [];

      $(this).find('.unit-image-row').each(function (index) {
        order.push({
          asset_id: $(this).data('asset-id'),
          sort_order: index + 1
        });
      });

      saveUnitImageOrder(unitId, order);
    }
  });

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
  });


  function saveUnitImageOrder(unitId, order) {



    $.ajax({
      url: '/ajax/unit/assets/sort',
      type: 'POST',
      data: {
        // _token: csrfToken,
        unit_id: unitId,
        order: order
      },
      success: function () {
        showToast('success', 'Image order saved');
      },
      error: function () {
        showToast('danger', 'Failed to save image order');
      }
    });
  }


  </script>


  @endpush

  @push('maps-script')
  <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
  @endpush

  {{-- Toast container --}}
  <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
@endsection

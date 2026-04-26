<!-- Add/Edit Property Modal -->
<div class="modal fade" id="addPropertyModal" tabindex="-1" aria-labelledby="addPropertyLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form name="add_property" id="add_property">
        <div class="modal-header">
          <h5 class="modal-title" id="addPropertyLabel">Add/Edit Property</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
              <!-- Status -->
              <div class="col-md-6 mb-2">
                <label class="form-label">Property Status *</label>
                <select class="form-select selectpicker"
                        id="status_type"
                        name="status_type"
                        data-live-search="true"
                        autocomplete="off"
                        required>
                  <option value="">-- Select --</option>
                  @foreach($propertyStatus as $status)
                    <option value="{{ $status->id }}"
                      @if(isset($property) && $property->status_type == $status->id) selected @endif>
                      {{ $status->status }}
                    </option>
                  @endforeach
                </select>
              </div>

              <!-- Zoning (ID) -->
              <div class="col-md-6">
                <label class="form-label">Zoning</label>
                <select class="form-select selectpicker"
                        id="zoning"
                        name="zoning"
                        data-live-search="true"
                        autocomplete="off">
                  <option value="">-- Select --</option>
                  @foreach($propertyZonings as $zoning)
                    <option value="{{ $zoning->id }}"
                      @if(isset($property) && $property->zoning == $zoning->id) selected @endif>
                      {{ $zoning->zoning }}
                    </option>
                  @endforeach
                </select>
              </div>
          </div>

          <div class="row g-3">
            <!-- Location (ID) -->
            <div class="col-md-6 mb-2">
              <label class="form-label">Location (City/Region)</label>
              <select class="form-select selectpicker"
                      id="location"
                      name="location"
                      data-live-search="true"
                      autocomplete="off">
                <option value="">-- Select --</option>
                @foreach($propertyLocations as $location)
                  <option value="{{ $location->id }}"
                    @if(isset($property) && $property->location == $location->id) selected @endif>
                    {{ $location->location }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- Area / Locale (ID) -->
            <div class="col-md-6">
              <label class="form-label">Area *</label>
              <select class="form-select selectpicker"
                      id="property_locale"
                      name="property_locale"
                      data-live-search="true"
                      autocomplete="off"
                      required>
                <option value="">-- Select --</option>
                @foreach($propertyAreas as $area)
                  <option value="{{ $area->id }}"
                    @if(isset($property) && $property->property_locale == $area->id) selected @endif>
                    {{ $area->area }}
                  </option>
                @endforeach
              </select>
            </div>
        </div>

        <div class="row g-3">

          <!-- Type -->
          <div class="col-md-6 mb-2">
            <label class="form-label">Property Type *</label>
            <select class="form-select selectpicker"
                    id="type_id"
                    name="type_id"
                    data-live-search="true"
                    autocomplete="off"
                    required>
              <option value="">-- Select --</option>
              @foreach($propertyTypes as $type)
                <option value="{{ $type->id }}"
                  @if(isset($property) && $property->type_id == $type->id) selected @endif>
                  {{ $type->type }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- 🔽 Sectional Title : Scheme Number -->
          <div class="col-md-6 d-none" id="scheme_number_wrap">
            <label class="form-label">Scheme Number</label>
            <input type="text"
                   class="form-control"
                   id="scheme_number"
                   name="scheme_number"
                   placeholder="Enter scheme number (optional)" autocomplete="off">
          </div>

        </div>

        <div class="col-md-12 border-primary border-bottom mt-2 mb-2">
          <!-- border   -->
        </div>

        <div class="row g-3">
            <!-- Erf -->
            <div class="col-md-6">
              <label class="form-label">ERF No</label>
              <input type="text" id="erf_no" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">ERF Size</label>
              <input type="text" id="erf_size" class="form-control">
            </div>

            <!-- GLA & Zoning -->
            <div class="col-md-6">
              <label class="form-label">GLA</label>
              <input type="text" id="gla" class="form-control">
            </div>

            <div class="col-md-12 border-primary border-bottom mb-2">
              <!-- border   -->
            </div>
        </div>

        <div class="row g-3">
            <!-- Building -->
            <div class="col-md-12">
              <label class="form-label">Building Nam</label>
              <input type="text" class="form-control" id="building_name" required>
            </div>

            <div class="col-md-12">
              <label class="form-label">Building Details</label>
              <input type="text" class="form-control" id="blurb" required>
            </div>

            <!-- Address -->
            <div class="col-md-12">
              <label class="form-label">Address</label>
              <textarea class="form-control" id="address" rows="2" required></textarea>
            </div>

            <!-- Lat/Lon -->
            <div class="col-md-6">
              <label class="form-label">Latitud</label>
              <input type="text" class="form-control" id="prop_lat" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Longitud</label>
              <input type="text" class="form-control" id="prop_lon" required>
            </div>

            <!-- 🔽 NEW: Assign Landlords -->
            <div class="col-md-12">
              <label class="form-label">Assign Landlords</label>
              <select id="property_landlords_edit" name="landlords[]" class="selectpicker form-control" multiple
                      data-live-search="true" data-actions-box="true" data-width="100%">
                @foreach($landlords as $landlord)
                  <option value="{{ $landlord->id }}">{{ $landlord->company_name }}</option>
                @endforeach
              </select>
              <small class="text-muted">Search & select multiple landlords</small>
            </div>

            <div class="col-md-12 border-primary border-bottom mb-2">
              <!-- border   -->
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <!-- <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div> -->

          <button type="button" class="btn btn-primary" onclick="save_property();">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('page-js')
<script>
  $(document).ready(function () {
    // initial state (Add Property)
    toggleSchemeNumber();

    // on type change
    $('#type_id').on('change', function () {
      toggleSchemeNumber();
    });

    $('#property_landlords_edit').selectpicker();
  });

  function editProperty(id) {
    $.get('/ajax/property/' + id, function(data) {
      if (data) {
        $('#type_id').val(data.type_id);
        $('#status_type').val(data.status_type);
        $('#erf_no').val(data.erf_no);
        $('#erf_size').val(data.erf_size);
        $('#gla').val(data.gla);
        $('#zoning').val(data.zoning);
        $('#property_locale').val(data.property_locale);
        $('#location').val(data.location);
        $('#building_name').val(data.building_name);
        $('#blurb').val(data.blurb);
        $('#address').val(data.address);
        $('#prop_lat').val(data.latitude);
        $('#prop_lon').val(data.longitude);

       $('#scheme_number').val(data.scheme_number || '');
       toggleSchemeNumber();

        // Preselect landlords
        if (data.landlords && data.landlords.length > 0) {
          let ids = data.landlords.map(l => l.id.toString());
          // $('#property_landlords_edit').selectpicker('val', ids).selectpicker('refresh');
          $('#property_landlords_edit').selectpicker('val', ids).selectpicker('render');
        } else {
          // $('#property_landlords_edit').selectpicker('deselectAll').selectpicker('refresh');
          $('#property_landlords_edit').selectpicker('deselectAll').selectpicker('render');
        }

        $('#prop_id').remove();
        $('#add_property').append('<input type="hidden" id="prop_id" value="'+data.id+'">');
      }
    });
  }

  function save_property() {
    let payload = {
      id: $('#prop_id').val() || null,
      type_id: $('#type_id').val(),
      status_type: $('#status_type').val(),
      zoning: $('#zoning').val(),
      property_locale: $('#property_locale').val(),
      location: $('#location').val(),
      erf_no: $('#erf_no').val(),
      erf_size: $('#erf_size').val(),
      gla: $('#gla').val(),
      building_name: $('#building_name').val(),
      blurb: $('#blurb').val(),
      address: $('#address').val(),
      latitude: $('#prop_lat').val(),
      longitude: $('#prop_lon').val(),
      scheme_number: $('#scheme_number').val(),
      _token: '{{ csrf_token() }}'
    };

    $.post('/ajax/property/save', payload, function(response) {
      if (response.success) {
        // Sync landlords if property saved
        let selectedLandlords = $('#property_landlords_edit').val() || [];
        $.post(`/ajax/property/${response.property.id}/landlords`, {
          _token: '{{ csrf_token() }}',
          landlord_ids: selectedLandlords
        }).done(function(res) {
          showToast('success', 'Property & landlords saved!');
          bootstrap.Modal.getInstance(document.getElementById('addPropertyModal')).hide();
        });
      } else {
        showToast('danger', 'Something went wrong while saving!');
      }
    }).fail(function(xhr) {
      let msg = xhr.responseJSON?.message || xhr.responseText || "Unknown error";
      showToast('danger', 'Save failed: ' + msg);
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

  function toggleSchemeNumber(){
    let typeId = $('#type_id').val();

    if (typeId == 2) {
      $('#scheme_number_wrap').removeClass('d-none');
    } else {
      $('#scheme_number_wrap').addClass('d-none');
      $('#scheme_number').val(''); // optional reset
    }
  }

  </script>
@endpush

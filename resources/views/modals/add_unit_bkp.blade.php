<div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="unitModalLabel">Unit Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="unitForm">
          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label">Unit Type *</label>
              <input type="text" class="form-control" id="u_unit_type" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Listing Broker *</label>
              <input type="text" class="form-control" id="u_listing_broker" required>
            </div>

            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" id="u_company_as_listing_broker">
                <label class="form-check-label" for="u_company_as_listing_broker">
                  Company as Listing Broker
                </label>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Deal File ID</label>
              <input type="text" class="form-control" id="u_deal_file_id">
            </div>

            <div class="col-md-6">
              <label class="form-label">Availability</label>
              <input type="text" class="form-control" id="u_availability">
            </div>

            <div class="col-md-6">
              <label class="form-label">Lease Expiry</label>
              <input type="date" class="form-control" id="u_lease_expiry">
            </div>

            <div class="col-md-3">
              <label class="form-label">Unit No</label>
              <input type="text" class="form-control" id="u_unit_no">
            </div>

            <div class="col-md-3">
              <label class="form-label">Unit Size</label>
              <input type="number" step="0.01" class="form-control" id="u_unit_size">
            </div>

            <div class="col-md-3">
              <label class="form-label">Gross Rental</label>
              <input type="number" step="0.01" class="form-control" id="u_gross_rental">
            </div>

            <div class="col-md-3">
              <label class="form-label">Sale Price</label>
              <input type="number" step="0.01" class="form-control" id="u_sale_price">
            </div>

            <div class="col-md-4">
              <label class="form-label">Yield %</label>
              <input type="text" class="form-control" id="u_yield_percentage">
            </div>

            <div class="col-md-4">
              <label class="form-label">Parking Bays</label>
              <input type="text" class="form-control" id="u_parking_bays">
            </div>

            <div class="col-md-4">
              <label class="form-label">Parking Rental</label>
              <input type="text" class="form-control" id="u_parking_rental">
            </div>

            {{-- 🔹 Multi-property dropdown --}}
            <div class="col-md-12">
              <label class="form-label">Assign Properties *</label>
              <select
                class="selectpicker form-control"
                id="u_property_ids"
                name="property_ids[]"
                multiple
                data-live-search="true"
                data-actions-box="true"
                title="Select properties..."
                autocomplete = "off"
                >
                @foreach($properties as $prop)
                  <option value="{{ $prop->id }}">{{ $prop->building_name }}</option>
                @endforeach
              </select>
              <small class="text-muted">Search, select multiple, or select all/unselect all</small>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="save_unit()">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('modal-js')
<script>
  // function resetUnitForm() {
  //   $('#unitForm')[0].reset();
  //   $('#unit_id').remove();
  //   // $('#u_property_ids').val([]).selectpicker('refresh');
  //   $('#u_property_ids').val([]).selectpicker('render');
  // }

  function resetUnitForm() {
      $('#unitForm')[0].reset();
      $('#unit_id').remove();

      // clear previous selections
      $('#u_property_ids').selectpicker('deselectAll');

      // 🔑 authoritative page context
      const propertyId = $('#property_id').val();

      $('#u_property_ids').selectpicker('deselectAll');

      if (propertyId) {
          // Property Details page → force single property
          $('#u_property_ids')
              .val([propertyId])
              .prop('disabled', true);
      } else {
          // Other modules → normal behaviour
          $('#u_property_ids').prop('disabled', false);
      }

      $('#u_property_ids').selectpicker('refresh');
  }

  function editUnit(id) {
    $.get('/ajax/unit/' + id, function (response) {

      if (!response || !response.unit) {
        showToast('danger', 'Could not load unit details.');
        return;
      }

      const data = response.unit;
      const selectedProps = response.property_ids || [];

      // Fill fields
      $('#u_unit_type').val(data.unit_type);
      $('#u_company_as_listing_broker').prop('checked', !!data.company_as_listing_broker);
      $('#u_listing_broker').val(data.listing_broker);
      $('#u_deal_file_id').val(data.deal_file_id);
      $('#u_availability').val(data.availability);
      $('#u_lease_expiry').val(data.lease_expiry);
      $('#u_unit_no').val(data.unit_no);
      $('#u_unit_size').val(data.unit_size);
      $('#u_gross_rental').val(data.gross_rental);
      $('#u_sale_price').val(data.sale_price);
      $('#u_yield_percentage').val(data.yield_percentage);
      $('#u_parking_bays').val(data.parking_bays);
      $('#u_parking_rental').val(data.parking_rental);

      // ✅ PRESELECT PROPERTIES (correct way)
      $('#u_property_ids').selectpicker('deselectAll');
      $('#u_property_ids')
        .val(selectedProps)
        .selectpicker('refresh');

      // Hidden ID
      $('#unit_id').remove();
      $('#unitForm').append(
        `<input type="hidden" id="unit_id" value="${data.id}">`
      );

      // Show modal
      const modal = new bootstrap.Modal(
        document.getElementById('unitModal')
      );
      modal.show();
    });
  }


  // 🔹 Save / Update Unit
  function save_unit() {
    const id = $("#unit_id").val() || '';
    const selectedProps = $("#u_property_ids").val() || [];

    const formData = new FormData();
    formData.append('_token', "{{ csrf_token() }}");
    formData.append('id', id);
    formData.append('unit_type', $("#u_unit_type").val());
    formData.append('company_as_listing_broker', $("#u_company_as_listing_broker").is(':checked') ? 1 : 0);
    formData.append('listing_broker', $("#u_listing_broker").val());
    formData.append('deal_file_id', $("#u_deal_file_id").val());
    formData.append('availability', $("#u_availability").val());
    formData.append('lease_expiry', $("#u_lease_expiry").val());
    formData.append('unit_no', $("#u_unit_no").val());
    formData.append('unit_size', $("#u_unit_size").val());
    formData.append('gross_rental', $("#u_gross_rental").val());
    formData.append('sale_price', $("#u_sale_price").val());
    formData.append('yield_percentage', $("#u_yield_percentage").val());
    formData.append('parking_bays', $("#u_parking_bays").val());
    formData.append('parking_rental', $("#u_parking_rental").val());

    // Append all selected property IDs
    selectedProps.forEach(pid => formData.append('property_ids[]', pid));

    $.ajax({
      type: 'POST',
      url: "/ajax/unit/save",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      success: function() {
        showToast('success', 'Unit saved successfully!');
        setTimeout(() => location.reload(), 1200);
      },
      error: function(xhr) {
        const msg = xhr.responseJSON?.message || "Save failed";
        showToast('danger', msg);
      }
    });
  }
  </script>
@endpush

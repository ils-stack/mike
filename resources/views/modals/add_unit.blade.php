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

            <!-- ✅ UNIT STATUS (ONLY ONCE) -->
            <div class="col-md-6">
              <label class="form-label">Unit Status *</label>
              <select class="form-select selectpicker"
                      id="u_unit_status"
                      data-live-search="true"
                      autocomplete="off"
                      required>
                <option value="">-- Select --</option>
                @foreach($propertyStatus as $status)
                  <option value="{{ $status->id }}">
                    {{ $status->status }}
                  </option>
                @endforeach
              </select>
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
                autocomplete="off">
                @foreach($properties as $prop)
                  <option value="{{ $prop->id }}">{{ $prop->building_name }}</option>
                @endforeach
              </select>
              <small class="text-muted">Search, select multiple, or select all/unselect all</small>
            </div>

            <button type="button"
                    class="btn btn-sm btn-outline-primary"
                    onclick="openUnitGallery()">
              <i class="fa-solid fa-images me-1"></i>
              Unit Gallery
            </button>


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

function openUnitGallery() {
  const unitModalEl = document.getElementById('unitModal');
  const galleryModalEl = document.getElementById('unitGalleryModal');

  // keep unit modal open
  unitModalEl.classList.add('modal-stack');

  const galleryModal = new bootstrap.Modal(galleryModalEl, {
    backdrop: 'static',
    keyboard: false
  });

  galleryModal.show();
}


/* ✅ initialise ONCE - units */
$(function () {
    $('#u_unit_status').selectpicker();
    $('#u_property_ids').selectpicker();
});

/* RESET */
function resetUnitForm() {
    $('#unitForm')[0].reset();
    $('#unit_id').remove();

    $('#u_unit_status').val('');
    $('#u_property_ids').selectpicker('deselectAll');

    const propertyId = $('#property_id').val();

    if (propertyId) {
        $('#u_property_ids').selectpicker('deselectAll');

        $('#u_property_ids')
            .val([propertyId])
            .selectpicker('refresh')
            .prop('disabled', true);
    } else {
        $('#u_property_ids').prop('disabled', false);
    }
}

/* EDIT */
function editUnit(id) {
    $.get('/ajax/unit/' + id, function (response) {

        if (!response || !response.unit) {
            showToast('danger', 'Could not load unit details.');
            return;
        }

        const data = response.unit;
        const selectedProps = response.property_ids || [];

        $('#u_unit_type').val(data.unit_type);
        $('#u_company_as_listing_broker').prop('checked', !!data.company_as_listing_broker);
        $('#u_listing_broker').val(data.listing_broker);
        $('#u_deal_file_id').val(data.deal_file_id);
        $('#u_availability').val(data.availability);

        $('#u_unit_status').selectpicker('val', String(data.unit_status));


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


        $('#u_property_ids').val(selectedProps);

        $('#unit_id').remove();
        $('#unitForm').append(
            `<input type="hidden" id="unit_id" value="${data.id}">`
        );

        new bootstrap.Modal(document.getElementById('unitModal')).show();
    });
}

/* SAVE */
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
    formData.append('unit_status', $("#u_unit_status").val());
    formData.append('lease_expiry', $("#u_lease_expiry").val());
    formData.append('unit_no', $("#u_unit_no").val());
    formData.append('unit_size', $("#u_unit_size").val());
    formData.append('gross_rental', $("#u_gross_rental").val());
    formData.append('sale_price', $("#u_sale_price").val());
    formData.append('yield_percentage', $("#u_yield_percentage").val());
    formData.append('parking_bays', $("#u_parking_bays").val());
    formData.append('parking_rental', $("#u_parking_rental").val());

    selectedProps.forEach(pid => formData.append('property_ids[]', pid));

    $.ajax({
        type: 'POST',
        url: "/ajax/unit/save",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function () {
            showToast('success', 'Unit saved successfully!');
            setTimeout(() => location.reload(), 1200);
        },
        error: function (xhr) {
            showToast('danger', xhr.responseJSON?.message || "Save failed");
        }
    });
}

// 🔹 Delete Unit
function deleteUnit(id) {
  if (!confirm("Are you sure you want to delete this unit?")) return;

  $.ajax({
    url: "/ajax/unit/" + id,
    type: "DELETE",
    data: { _token: "{{ csrf_token() }}" },
    success: function() {
      showToast('success', 'Unit deleted successfully!');
      setTimeout(() => location.reload(), 1200);
    },
    error: function(xhr) {
      const msg = xhr.responseJSON?.message || "Delete failed";
      showToast('danger', msg);
    }
  });
}
</script>
@endpush

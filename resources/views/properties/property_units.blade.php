<div class="row mt-4" id="unitsList">

  <div class="col-md-6">
    <h5>Units</h5>
  </div>

  <!-- <div class="col-md-6 text-end">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#unitDetailsModal" style="min-width:200px;" onclick="reset_unit_form();">
      <i class="fas fa-file-medical"></i>
      Add New
    </button>
  </div> -->

  <div class="col-md-6" align = "right">
    <!-- <button type="button" class="btn btn-primary"
            onclick="assignLandlords({{ $property->id }})"
            style="min-width:200px;">
      <i class="fas fa-user-plus"></i> Assign Existing Units
    </button> -->

    <button type="button" class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#unitModal"
            onclick="resetUnitForm()">
      Add Unit
    </button>
  </div>

  <div class="col-md-12 mt-3">
    @foreach($property->units as $unit)
      @include('properties.unit_list_block', ['unit' => $unit])
    @endforeach
  </div>

</div>

@push('modal-js')
<script>

/////////////////////////////////////////////////////////////////////////////////////
// 🔥 ADD UNIT TO BROCHURE (NEW WORKING CODE)
/////////////////////////////////////////////////////////////////////////////////////

$(document).on('click', '.addUnitToBrochure', function () {

    let unitId = $(this).data('id');

    $.post('/brochure/cart/add', {
        unit_id: unitId,
        _token: "{{ csrf_token() }}"
    }, function(res) {

        if (res.success) {

            // update badge
            $('#brochureCount').text(res.count);

            // disable icon
            $('.addUnitToBrochure[data-id="'+unitId+'"]')
                .css('opacity', '0.4')
                .css('pointer-events', 'none')
                .attr('title', 'Already Added');

            // toast
            var toastEl = document.getElementById('saveToast');
            if (toastEl) {
                var toast = new bootstrap.Toast(toastEl);
                document.getElementById('toast_message').innerText = "Unit added to brochure!";
                toast.show();
            }
        }

    }).fail(function() {

        var toastEl = document.getElementById('alertToast');
        if (toastEl) {
            var toast = new bootstrap.Toast(toastEl);
            document.getElementById('alert_message').innerText = "Error adding to brochure!";
            toast.show();
        }

    });
});

</script>
@endpush

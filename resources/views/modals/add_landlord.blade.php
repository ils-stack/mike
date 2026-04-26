<div class="modal fade" id="addLandlordModal" tabindex="-1" aria-labelledby="addLandlordLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="landlordForm">
        <div class="modal-header">
          <h5 class="modal-title" id="addLandlordLabel">Add/Edit Landlord</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Company Name *</label>
            <input type="text" class="form-control" id="company_name" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Entity Name</label>
            <input type="text" class="form-control" id="entity_name">
          </div>

          <!-- ✅ NEW FIELD -->
          <div class="mb-3">
            <label class="form-label">Registration/I.D. Number</label>
            <input type="text" class="form-control" id="registration_number">
          </div>

          <div class="mb-3">
            <label class="form-label">Contact Person</label>
            <input type="text" class="form-control" id="contact_person">
          </div>

          <div class="mb-3">
            <label class="form-label">Telephone</label>
            <input type="text" class="form-control" id="telephone">
          </div>

          <div class="mb-3">
            <label class="form-label">Cell Number</label>
            <input type="text" class="form-control" id="cell_number">
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" id="email">
          </div>

          <div class="mb-3">
            <label class="form-label">Assign Properties</label>
            <select
              class="selectpicker form-control"
              id="landlord_properties"
              multiple
              data-live-search="true"
              data-actions-box="true"
              title="Select properties...">
              @foreach($properties as $property)
                <option value="{{ $property->id }}">{{ $property->building_name }}</option>
              @endforeach
            </select>
            <small class="text-muted">Search, select multiple, or select all/unselect all</small>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="save_landlord();">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('modal-js')
<script>
  // // Reset modal for Add
  // function resetLandlordForm() {
  //   $('#landlordForm')[0].reset();
  //   $('#landlord_id').remove();
  //   // $('#landlord_properties').val([]).selectpicker('refresh');
  //   $('#landlord_properties').val([]).selectpicker('render');
  // }

  function resetLandlordForm() {
      $('#landlordForm')[0].reset();
      $('#landlord_id').remove();

      // clear previous selections
      $('#landlord_properties').selectpicker('deselectAll');

      // 🔑 authoritative page context
      const propertyId = $('#property_id').val();

      if (propertyId) {
          // Property Details page → force single property
          $('#landlord_properties')
              .val([propertyId])
              .prop('disabled', true);
      } else {
          // Other modules → normal behaviour
          $('#landlord_properties').prop('disabled', false);
      }

      $('#landlord_properties').selectpicker('refresh');
  }

  // Edit landlord (fetch details)
  function editLandlord(id) {
    $.get('/ajax/landlord/' + id, function(data) {
      if (data) {
        $('#company_name').val(data.company_name);
        $('#entity_name').val(data.entity_name);
        $('#registration_number').val(data.registration_number);
        $('#contact_person').val(data.contact_person);
        $('#telephone').val(data.telephone);
        $('#cell_number').val(data.cell_number);
        $('#email').val(data.email);

        // hidden field
        $('#landlord_id').remove();
        $('#landlordForm').append('<input type="hidden" id="landlord_id" value="'+data.id+'">');

        // ✅ preselect properties if landlord has them
        if (data.properties && data.properties.length > 0) {
          let ids = data.properties.map(p => p.id.toString());
          $('#landlord_properties').selectpicker('val', ids);
        } else {
          $('#landlord_properties').selectpicker('deselectAll');
        }

        // open modal
        var modal = new bootstrap.Modal(document.getElementById('addLandlordModal'));
        modal.show();
      }
    }).fail(function() {
      showToast('danger', 'Could not load landlord details.');
    });
  }


  // Save landlord (with properties)
  function save_landlord() {
    let id = $("#landlord_id").val() || '';
    let formData = new FormData();
    formData.append('_token', "{{ csrf_token() }}");
    formData.append('id', id);
    formData.append('company_name',$("#company_name").val());
    formData.append('entity_name',$("#entity_name").val());
    formData.append('registration_number', $("#registration_number").val());
    formData.append('contact_person',$("#contact_person").val());
    formData.append('telephone',$("#telephone").val());
    formData.append('cell_number',$("#cell_number").val());
    formData.append('email',$("#email").val());

    $.ajax({
      type:'POST',
      url:"/ajax/landlord/save",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      success:function(response){
        if (response.success && response.landlord?.id) {
          let selectedProps = $('#landlord_properties').val() || [];
          save_landlord_properties(response.landlord.id, selectedProps);
        }

        showToast('success', 'Landlord saved successfully!');
        location.reload();
      },
      error:function(xhr){
        let msg = xhr.responseJSON?.message || "Save failed";
        showToast('danger', msg);
      }
    });
  }

  // Assign properties
  function save_landlord_properties(id, selectedProps) {
    $.ajax({
      url: '/ajax/landlord/' + id + '/properties',
      type: 'POST',
      data: {
        _token: "{{ csrf_token() }}",
        properties: selectedProps
      },
      success: function(res) {
        showToast('success', res.message || 'Properties updated');
      },
      error: function(xhr) {
        let msg = xhr.responseJSON?.message || "Failed to assign properties";
        showToast('danger', msg);
      }
    });
  }

  function deleteLandlord(id) {
    if (!confirm("Are you sure you want to delete this landlord?")) return;

    $.ajax({
      url: "/ajax/landlord/" + id,
      type: "DELETE",
      data: { _token: "{{ csrf_token() }}" },
      success: function(response) {
        showToast('success', 'Landlord deleted successfully!');
        location.reload();
      },
      error: function(xhr) {
        let msg = xhr.responseJSON?.message || "Delete failed";
        showToast('danger', msg);
      }
    });
  }
</script>
@endpush

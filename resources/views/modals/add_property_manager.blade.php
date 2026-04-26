<div class="modal fade" id="managerModal" tabindex="-1" aria-labelledby="managerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="managerModalLabel">Property Manager Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="managerForm">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Company Name *</label>
              <input type="text" class="form-control" id="pm_company_name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Entity Name</label>
              <input type="text" class="form-control" id="pm_entity_name">
            </div>
            <div class="col-md-6">
              <label class="form-label">Manager Name</label>
              <input type="text" class="form-control" id="pm_manager_name">
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Person</label>
              <input type="text" class="form-control" id="pm_contact_person">
            </div>
            <div class="col-md-6">
              <label class="form-label">Telephone</label>
              <input type="text" class="form-control" id="pm_telephone">
            </div>
            <div class="col-md-6">
              <label class="form-label">Cell Number</label>
              <input type="text" class="form-control" id="pm_cell_number">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" id="pm_email">
            </div>

            <div class="col-12">
              <label class="form-label">Assign Properties</label>
              <select
                class="selectpicker form-control"
                id="a_properties"
                multiple
                data-live-search="true"
                data-actions-box="true"
                title="Select properties..."
                autocomplete = "off"
              >
                @foreach($properties as $property)
                  <option value="{{ $property->id }}">
                    {{ $property->building_name ?? 'Unnamed Property' }}
                  </option>
                @endforeach
              </select>
              <small class="text-muted">Search, select multiple, or select all/unselect all</small>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="save_manager()">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('modal-js')
  <script>
  // function resetManagerForm() {
  //     $('#managerForm')[0].reset();
  //     $('#manager_id').remove();
  //
  //     // 🚀 Completely destroy bootstrap-select instance
  //     $('#a_properties').selectpicker('deselectAll');
  //     $('#a_properties').selectpicker('refresh');
  // }

  function resetManagerForm() {
      $('#managerForm')[0].reset();
      $('#manager_id').remove();

      // clear previous selections
      $('#a_properties').selectpicker('deselectAll');

      // 🔑 Laravel-provided context (authoritative)
      const propertyId = $('#property_id').val();

      if (propertyId) {
          // Property details page → force single property
          $('#a_properties')
              .val([propertyId])
              .prop('disabled', true);
      } else {
          // Other modules → normal behaviour
          $('#a_properties').prop('disabled', false);
      }

      $('#a_properties').selectpicker('refresh');
  }

  function editManager(id) {
      $.get('/ajax/property-manager/' + id, function(data) {
          if (data) {

              // 🔹 Reset form fields
              $('#managerForm')[0].reset();
              $('#manager_id').remove();

              // 🔹 Fully clear dropdown BEFORE inserting data
              $('#a_properties').selectpicker('deselectAll');  // clears internal selection
              $('#a_properties').selectpicker('refresh');       // refresh UI

              // 🔹 Populate input fields
              $('#pm_company_name').val(data.company_name);
              $('#pm_entity_name').val(data.entity_name);
              $('#pm_manager_name').val(data.manager_name);
              $('#pm_contact_person').val(data.contact_person);
              $('#pm_telephone').val(data.telephone);
              $('#pm_cell_number').val(data.cell_number);
              $('#pm_email').val(data.email);

              // 🔹 Add hidden manager_id
              $('#managerForm').append('<input type="hidden" id="manager_id" value="'+data.id+'">');

              // 🔹 Pre-select assigned properties
              if (data.property_ids && Array.isArray(data.property_ids)) {
                  $('#a_properties').val(data.property_ids);
              }

              // 🔹 Refresh after setting new selections
              $('#a_properties').selectpicker('refresh');

              // 🔹 Show modal
              const modal = new bootstrap.Modal(document.getElementById('managerModal'));
              modal.show();
          }
      }).fail(function () {
          showToast('danger', 'Could not load manager details.');
      });
  }

  function save_manager() {
      const id = $("#manager_id").val() || '';

      const formData = new FormData();
      formData.append('_token', "{{ csrf_token() }}");
      formData.append('id', id);
      formData.append('company_name', $("#pm_company_name").val());
      formData.append('entity_name', $("#pm_entity_name").val());
      formData.append('manager_name', $("#pm_manager_name").val());
      formData.append('contact_person', $("#pm_contact_person").val());
      formData.append('telephone', $("#pm_telephone").val());
      formData.append('cell_number', $("#pm_cell_number").val());
      formData.append('email', $("#pm_email").val());

      // 🔹 Add property IDs
      let assignedProps = $('#a_properties').val() || [];
      assignedProps.forEach(p => formData.append('property_ids[]', p));

      $.ajax({
          type: 'POST',
          url: "/ajax/property-manager/save",
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          success: function(response) {
              showToast('success', 'Manager saved successfully!');
              setTimeout(() => location.reload(), 1200);
          },
          error: function(xhr) {
              const msg = xhr.responseJSON?.message || "Save failed";
              showToast('danger', msg);
          }
      });
  }

  function deleteManager(id) {
      if (!confirm("Are you sure you want to delete this manager?")) return;

      $.ajax({
          url: "/ajax/property-manager/" + id,
          type: "DELETE",
          data: { _token: "{{ csrf_token() }}" },
          success: function() {
              showToast('success', 'Manager deleted successfully!');
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

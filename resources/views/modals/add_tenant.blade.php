<div class="modal fade" id="tenantModal" tabindex="-1" aria-labelledby="tenantModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tenantModalLabel">Tenant Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="tenantForm">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Company Name *</label>
              <input type="text" class="form-control" id="te_tenant" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Entity Name</label>
              <input type="text" class="form-control" id="te_entity">
            </div>

            <div class="col-md-6">
              <label class="form-label">Contact Person</label>
              <input type="text" class="form-control" id="te_contact">
            </div>

            <div class="col-md-6">
              <label class="form-label">Telephone</label>
              <input type="text" class="form-control" id="te_tel">
            </div>

            <div class="col-md-6">
              <label class="form-label">Cell Number</label>
              <input type="text" class="form-control" id="te_cell">
            </div>

            <div class="col-md-6">
              <label class="form-label">Email *</label>
              <input type="email" class="form-control" id="te_email" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Assign Properties</label>
              <select
                class="selectpicker form-control"
                id="te_properties"
                multiple
                data-live-search="true"
                data-actions-box="true"
                title="Select properties..."
                autocomplete = "off"
              >
                @foreach($properties as $property)
                  <option value="{{ $property->id }}">{{ $property->building_name }}</option>
                @endforeach
              </select>
              <small class="text-muted">Search, select multiple, or select all/unselect all</small>
            </div>


            <div class="col-12">
              <label class="form-label">Note</label>
              <textarea class="form-control" id="te_note" rows="3"></textarea>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="save_tenant();">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('modal-js')
  <script>
  // function resetTenantForm() {
  //   $('#tenantForm')[0].reset();
  //   $('#tenant_id').remove();
  // }

  function resetTenantForm() {
      $('#tenantForm')[0].reset();
      $('#tenant_id').remove();

      // clear previous selections
      $('#te_properties').selectpicker('deselectAll');

      // 🔑 authoritative page context
      const propertyId = $('#property_id').val();

      if (propertyId) {
          // alert(1);
          // Property Details page → force single property
          $('#te_properties')
              .val([propertyId])
              .prop('disabled', true);
      } else {
          // Other modules → normal behaviour
          $('#te_properties').prop('disabled', false);
      }

      $('#te_properties').selectpicker('refresh');
  }

  function editTenant(id) {
    $.get('/ajax/tenant/' + id, function (data) {
      if (data) {
        $('#te_tenant').val(data.company_name);
        $('#te_entity').val(data.entity_name);
        $('#te_contact').val(data.contact_person);
        $('#te_tel').val(data.telephone);
        $('#te_cell').val(data.cell_number);
        $('#te_email').val(data.email);

        $('#tenant_id').remove();
        $('#tenantForm').append('<input type="hidden" id="tenant_id" value="' + data.id + '">');

        // 👇 fetch linked properties
        $.get('/ajax/tenant/' + id + '/properties', function (links) {
          const ids = links.map(p => String(p.property_id));
          $('#te_properties').selectpicker('val', ids);
          // $('#te_properties').selectpicker('refresh');
          $('#te_properties').selectpicker('render');
        });

        const modal = new bootstrap.Modal(document.getElementById('tenantModal'));
        modal.show();
      }
    }).fail(function () {
      showToast('danger', 'Could not load tenant details.');
    });
  }


  function save_tenant() {
    const id = $("#tenant_id").val() || '';
    const formData = new FormData();
    formData.append('_token', "{{ csrf_token() }}");
    formData.append('id', id);
    formData.append('company_name', $("#te_tenant").val());
    formData.append('entity_name', $("#te_entity").val());
    formData.append('contact_person', $("#te_contact").val());
    formData.append('telephone', $("#te_tel").val());
    formData.append('cell_number', $("#te_cell").val());
    formData.append('email', $("#te_email").val());

    // 👇 collect multi-selected property IDs
    const selectedProps = $('#te_properties').val() || [];
    for (const pid of selectedProps) formData.append('properties[]', pid);

    $.ajax({
      type: 'POST',
      url: "/ajax/tenant/save",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      success: function (response) {
        // optional: assign properties immediately after save
        if (selectedProps.length && response.tenant?.id) {
          $.post('/ajax/tenant/' + response.tenant.id + '/assign', {
            _token: "{{ csrf_token() }}",
            properties: selectedProps
          });
        }
        showToast('success', 'Tenant saved successfully!');
        setTimeout(() => location.reload(), 1200);
      },
      error: function (xhr) {
        const msg = xhr.responseJSON?.message || "Save failed";
        showToast('danger', msg);
      }
    });
  }

  function deleteTenant(id) {
    if (!confirm("Are you sure you want to delete this tenant?")) return;

    $.ajax({
      url: "/ajax/tenant/" + id,
      type: "DELETE",
      data: { _token: "{{ csrf_token() }}" },
      success: function() {
        showToast('success', 'Tenant deleted successfully!');
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

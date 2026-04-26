<div class="modal fade" id="agentModal" tabindex="-1" aria-labelledby="agentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="agentModalLabel">Agent Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="agentForm">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Company Name *</label>
              <input type="text" class="form-control" id="a_company_name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Entity Name</label>
              <input type="text" class="form-control" id="a_entity_name">
            </div>
            <div class="col-md-6">
              <label class="form-label">Manager Name</label>
              <input type="text" class="form-control" id="a_manager_name">
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Person</label>
              <input type="text" class="form-control" id="a_contact_person">
            </div>
            <div class="col-md-6">
              <label class="form-label">Telephone</label>
              <input type="text" class="form-control" id="a_telephone">
            </div>
            <div class="col-md-6">
              <label class="form-label">Cell Number</label>
              <input type="text" class="form-control" id="a_cell_number">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" id="a_email">
            </div>

            <div class="col-12">
              <label class="form-label">Assign Properties</label>
              <select
                class="selectpicker form-control"
                id="agent_properties"
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
        <button type="button" class="btn btn-primary" onclick="save_agent()">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


@push('modal-js')
<script>
  // function resetAgentForm() {
  //   $('#agentForm')[0].reset();
  //   $('#agent_id').remove();
  // }

  function resetAgentForm() {
      // alert(1);
      $('#agentForm')[0].reset();
      $('#agent_id').remove();

      // clear previous selections
      $('#agent_properties').selectpicker('deselectAll');

      // 🔑 authoritative page context
      const propertyId = $('#property_id').val();

      if (propertyId) {
          // alert(2);
          // Property Details page → force single property
          $('#agent_properties')
              .val([propertyId])
              .prop('disabled', true);
      } else {
          // Other modules → normal behaviour
          $('#agent_properties').prop('disabled', false);
      }

      $('#agent_properties').selectpicker('refresh');
  }


  function editAgent(id) {
    $.get('/ajax/agent/' + id, function(data) {
      if (data) {
        $('#a_company_name').val(data.company_name);
        $('#a_entity_name').val(data.entity_name);
        $('#a_manager_name').val(data.manager_name);
        $('#a_contact_person').val(data.contact_person);
        $('#a_telephone').val(data.telephone);
        $('#a_cell_number').val(data.cell_number);
        $('#a_email').val(data.email);

        $('#agent_id').remove();
        $('#agentForm').append('<input type="hidden" id="agent_id" value="'+data.id+'">');

        // Fetch assigned properties
        $.get('/ajax/agent/' + id + '/properties', function (props) {
          const ids = props.map(p => String(p.property_id));
          $('#agent_properties').selectpicker('val', ids);
          // $('#agent_properties').selectpicker('refresh');
          $('#agent_properties').selectpicker('render');
        });

        const modal = new bootstrap.Modal(document.getElementById('agentModal'));
        modal.show();
      }
    }).fail(function() {
      showToast('danger', 'Could not load agent details.');
    });
  }

  function save_agent() {
    const id = $("#agent_id").val() || '';
    const formData = new FormData();
    formData.append('_token', "{{ csrf_token() }}");
    formData.append('id', id);
    formData.append('company_name', $("#a_company_name").val());
    formData.append('entity_name', $("#a_entity_name").val());
    formData.append('manager_name', $("#a_manager_name").val());
    formData.append('contact_person', $("#a_contact_person").val());
    formData.append('telephone', $("#a_telephone").val());
    formData.append('cell_number', $("#a_cell_number").val());
    formData.append('email', $("#a_email").val());

    const selectedProps = $('#agent_properties').val() || [];
    for (const pid of selectedProps) formData.append('properties[]', pid);

    $.ajax({
      type: 'POST',
      url: "/ajax/agent/save",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      success: function (response) {
        showToast('success', 'Agent saved successfully!');
        setTimeout(() => location.reload(), 1200);
      },
      error: function (xhr) {
        const msg = xhr.responseJSON?.message || "Save failed";
        showToast('danger', msg);
      }
    });
  }

  function deleteAgent(id) {
    if (!confirm("Are you sure you want to delete this agent?")) return;

    $.ajax({
      url: "/ajax/agent/" + id,
      type: "DELETE",
      data: { _token: "{{ csrf_token() }}" },
      success: function() {
        showToast('success', 'Agent deleted successfully!');
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

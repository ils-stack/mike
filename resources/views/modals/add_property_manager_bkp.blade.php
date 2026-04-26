<div class="modal fade" id="addPropertyManagerModal" tabindex="-1" aria-labelledby="addPropertyManagerLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title" id="addPropertyManagerLabel">Add Property Manager</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Company Name *</label>
            <input type="text" class="form-control" id="pm_company_name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Entity Name *</label>
            <input type="text" class="form-control" id="pm_entity_name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contact *</label>
            <input type="text" class="form-control" id="pm_contact_person" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Tel</label>
            <input type="text" class="form-control" id="pm_telephone">
          </div>
          <div class="mb-3">
            <label class="form-label">Cell</label>
            <input type="text" class="form-control" id="pm_cell_number">
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" id="pm_email">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="save_property_manager();">Save</button>
          <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function save_property_manager(){
  var formData = new FormData();

  formData.append('_token', "{{ csrf_token() }}");
  formData.append('company_name', $("#pm_company_name").val());
  formData.append('entity_name', $("#pm_entity_name").val());
  formData.append('contact_person', $("#pm_contact_person").val());
  formData.append('telephone', $("#pm_telephone").val());
  formData.append('cell_number', $("#pm_cell_number").val());
  formData.append('email', $("#pm_email").val());

  $.ajax({
    type: 'POST',
    url: "{{ route('ajaxSavePropertyManager.post') }}",
    data: formData,
    cache: false,
    contentType: false,
    processData: false,
    success: function(data){
      var toastEl = document.getElementById('saveToast');
      var toast = new bootstrap.Toast(toastEl);
      toast.show();
    }
  });
}
</script>

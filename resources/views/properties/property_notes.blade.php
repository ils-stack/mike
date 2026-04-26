<div class="row mt-4">
  <div class="col-md-12">
    <h5>Notes</h5>
  </div>

  <style>
    .ck-editor__editable {
      min-height: 200px;
    }
  </style>

  <div class="col-md-12">
    <div class="info-box card shadow mb-2 mt-2">

      <textarea id="editor"></textarea>

      <div class="col-md-12" align="right">
        <div class="mt-2">
          <button id="saveNoteBtn" type="button" class="btn btn-primary" style="min-width:200px;">
            <i class="fas fa-save"></i>
            Save Note
          </button>
        </div>
      </div>

      <script>
        let propertyEditor;

        ClassicEditor.create(document.querySelector('#editor'))
          .then(editor => {
            propertyEditor = editor;
            loadExistingNote();
          })
          .catch(error => console.error(error));

        // Load existing note
        function loadExistingNote() {
          const id = $('#property_id').val();

          $.get(`/property/${id}/note`, function(res) {
            if (res.success && res.note) {
              propertyEditor.setData(res.note);
            }
          });
        }

        // Save note
        $('#saveNoteBtn').on('click', function () {
          const id = $('#property_id').val();
          const note = propertyEditor.getData();

          $.post(`/property/${id}/note/save`, {
            _token: '{{ csrf_token() }}',
            note: note
          }, function(res) {
            if (res.success) {
              alert("Note saved!");
            }
          });
        });
      </script>

    </div>
  </div>
</div>

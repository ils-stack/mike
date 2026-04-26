<!-- Upload Section -->
<div class="card mb-4">
    <div class="card-body text-center">
        <form id="assetUploadForm" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file[]" id="fileInput" hidden multiple>

            <div id="dropZone" class="border border-2 border-dashed rounded p-5 bg-light">
                <p class="mb-2"><i class="fa-solid fa-upload fa-2x text-primary"></i></p>
                <p class="mb-2">Drag & Drop file here or</p>
                <button type="button" class="btn btn-sm btn-primary" id="browseBtn">
                    <i class="fa fa-folder-open"></i> Browse
                </button>
            </div>
        </form>
    </div>
</div>

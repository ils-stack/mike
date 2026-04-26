<div class="modal fade"
     id="propertyAssetGalleryModal"
     tabindex="-1"
     aria-hidden="true"
     data-bs-backdrop="static">

  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fa-solid fa-images me-2"></i>
          Assign Images to Property
        </h5>
        <button type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"></button>
      </div>

      <div class="modal-body">

        <div class="modal-body">

          {{-- Upload new --}}
          <h6 class="fw-semibold mb-2">
            <i class="fa-solid fa-upload me-1"></i>
            Upload New Images
          </h6>
          <div class="mb-4">
            @include('common.asset_drop_upload')
          </div>

          <hr class="my-4">

          {{-- Select existing --}}
          <h6 class="fw-semibold mb-2">
            <i class="fa-solid fa-images me-1"></i>
            Select Existing Images
          </h6>
          <div id="property-asset-list" class="row g-3">
            <div class="col-12 text-muted text-center">
              Loading images…
            </div>
          </div>

        </div>


      </div>

      <div class="modal-footer">
        <button type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal">
          Done
        </button>
      </div>

    </div>
  </div>
</div>

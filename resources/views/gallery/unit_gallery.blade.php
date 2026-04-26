<style>
  #unitGalleryModal .modal-content {
    background-color: #f1f3f5;   /* slightly darker than #fff */
    border: 1px solid #111111;   /* default-ish bootstrap border */
    border-radius: 10px;
  }

  #unitGalleryModal .modal-header,
  #unitGalleryModal .modal-footer {
    border-color: #e5e7eb;
  }
</style>
<div class="modal fade"
     id="unitGalleryModal"
     tabindex="-1"
     aria-hidden="true">

  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Unit Image Gallery</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        {{-- Gallery styles --}}
        <style>
          .gallery-main-image {
            max-height: 400px;
            object-fit: cover;
            width: 100%;
          }
          .gallery-thumb {
            height: 100px;
            object-fit: cover;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.2s;
          }
          .gallery-thumb.active,
          .gallery-thumb:hover {
            opacity: 1;
            border: 2px solid #0d6efd;
          }
          .nav-arrow {
            font-size: 2rem;
            color: #333;
            cursor: pointer;
            user-select: none;
          }

        </style>

        {{-- Header --}}
        <!-- <div class="d-flex justify-content-between align-items-center mb-2"> -->
          <!-- <h5 class="mb-0">Gallery</h5> -->

          <!-- <button type="button"
                  class="btn btn-sm btn-outline-primary"
                  data-bs-toggle="modal"
                  data-bs-target="#propertyAssetGalleryModal">
            <i class="fa-solid fa-images me-1"></i>
            Add / Update Images
          </button> -->
        <!-- </div> -->

        {{-- Gallery view --}}
        @include('properties.unit_gallery_view')

      </div>

      <div class="modal-footer">
        <button type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal">
          Close
        </button>
      </div>

    </div>
  </div>
</div>

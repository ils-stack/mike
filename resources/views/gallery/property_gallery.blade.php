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

<div class="container my-4">

  <div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">Gallery</h5>

    <button type="button"
            class="btn btn-sm btn-outline-primary"
            data-bs-toggle="modal"
            data-bs-target="#propertyAssetGalleryModal">
      <i class="fa-solid fa-images me-1"></i>
      Add / Update Images
    </button>
  </div>

  @include('properties.image_gallery')

</div>

<script>
  $(document).ready(function () {
    const images = @json($propertyImages->pluck('url')->values());

    let currentIndex = 0;

    function updateMainImage(index) {
      currentIndex = index;
      $('#mainImage').attr('src', images[currentIndex]);
      $('.gallery-thumb').removeClass('active');
      $('.gallery-thumb[data-index="' + currentIndex + '"]').addClass('active');
    }

    $('.gallery-thumb').on('click', function () {
      const index = parseInt($(this).attr('data-index'));
      updateMainImage(index);
    });

    $('#prevImg').on('click', function () {
      const newIndex = (currentIndex - 1 + images.length) % images.length;
      updateMainImage(newIndex);
    });

    $('#nextImg').on('click', function () {
      const newIndex = (currentIndex + 1) % images.length;
      updateMainImage(newIndex);
    });
  });
</script>

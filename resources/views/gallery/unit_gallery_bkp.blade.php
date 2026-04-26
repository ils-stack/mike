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

  @include('properties.unit_gallery_view')

</div>

<script>
  // $(document).ready(function () {
  //   const images = @json($unitImages->pluck('url')->values());
  //
  //   let currentIndex = 0;
  //
  //   function updateMainImage(index) {
  //     currentIndex = index;
  //     $('#mainImage').attr('src', images[currentIndex]);
  //     $('.gallery-thumb').removeClass('active');
  //     $('.gallery-thumb[data-index="' + currentIndex + '"]').addClass('active');
  //   }
  //
  //   $('.gallery-thumb').on('click', function () {
  //     const index = parseInt($(this).attr('data-index'));
  //     updateMainImage(index);
  //   });
  //
  //   $('#prevImg').on('click', function () {
  //     const newIndex = (currentIndex - 1 + images.length) % images.length;
  //     updateMainImage(newIndex);
  //   });
  //
  //   $('#nextImg').on('click', function () {
  //     const newIndex = (currentIndex + 1) % images.length;
  //     updateMainImage(newIndex);
  //   });
  // });
</script>

<!-- Unit Image gallery -->

<script>
$(document).ready(function () {

  // drag and drop Images

  // Load assets when modal opens
  $('#propertyAssetGalleryModal').on('show.bs.modal', function () {
    loadPropertyAssets();
  });

  // Reload page when modal closes (as agreed earlier)
  $('#propertyAssetGalleryModal').on('hidden.bs.modal', function () {
    location.reload();
  });

  // Browse button
  $('#unitBrowseBtn').on('click', () => $('#unitFileInput').click());

  // File input
  $('#unitFileInput').on('change', function () {
    if (this.files.length) uploadFile(this.files[0]);
  });

  // Drag & drop
  $('#unitDropZone')
    .on('dragover', function (e) {
      e.preventDefault();
      $(this).addClass('bg-secondary text-white');
    })
    .on('dragleave drop', function (e) {
      e.preventDefault();
      $(this).removeClass('bg-secondary text-white');
    })
    .on('drop', function (e) {
      if (e.originalEvent.dataTransfer.files.length) {
        uploadFile(e.originalEvent.dataTransfer.files[0]);
      }
    });

  // Upload logic
  function uploadFile(file) {
    let formData = new FormData($('#assetUploadForm')[0]);
    formData.set('file', file);

    $.ajax({
      url: '/asset-library/upload',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function () {
        showToast('success', 'File uploaded successfully');

        // 🔥 refresh thumbnails immediately
        if (typeof loadPropertyAssets === 'function') {
          loadPropertyAssets();
        }
      },
      error: function (xhr) {
        let msg = xhr.responseJSON?.message || 'Upload failed';
        showToast('danger', msg);
      }
    });
  }

  // Toast helper
  // function showToast(type, message) {
  //   let toast = $(`
  //     <div class="toast align-items-center text-bg-${type} border-0" role="alert">
  //       <div class="d-flex">
  //         <div class="toast-body">${message}</div>
  //         <button type="button"
  //                 class="btn-close btn-close-white me-2 m-auto"
  //                 data-bs-dismiss="toast"></button>
  //       </div>
  //     </div>
  //   `);
  //   $('#toast-container').append(toast);
  //   new bootstrap.Toast(toast[0]).show();
  // }

  // drag and drop Images

  // modal lifecycle
  $('#propertyAssetGalleryModal')
    .on('show.bs.modal', loadPropertyAssets)
    .on('hidden.bs.modal', function () {
      location.reload();
    });

  function loadPropertyAssets() {
    const propertyId = $('#property_id').val();

    $('#property-asset-list').html(
      '<div class="col-12 text-center text-muted">Loading images…</div>'
    );

    $.get(`/ajax/property/${propertyId}/assets`, function (assets) {
      let html = '';

      if (!assets.length) {
        html = '<div class="col-12 text-center text-muted">No images found.</div>';
      } else {
        $.each(assets, function (_, asset) {
          html += renderAssetCard(asset);
        });
      }

      $('#property-asset-list').html(html);
    });
  }

  window.togglePropertyAsset = function (assetId) {
    $.post('/ajax/property/asset-toggle', {
      _token: '{{ csrf_token() }}',
      asset_id: assetId,
      property_id: $('#property_id').val()
    }, loadPropertyAssets)
    .fail(function () {
      alert('Failed to update image assignment');
    });
  };

  function renderAssetCard(asset) {
    return `
      <div class="col-md-3 col-sm-4 col-6">
        <div class="card shadow-sm h-100">
          <img src="${asset.public_url}"
               class="card-img-top"
               style="height:160px; object-fit:cover;">
          <div class="card-body p-2 text-center">
            <button class="btn btn-sm ${asset.assigned ? 'btn-danger' : 'btn-success'} w-100"
                    onclick="togglePropertyAsset(${asset.id})">
              ${asset.assigned ? 'Unassign' : 'Assign'}
            </button>
          </div>
        </div>
      </div>`;
  }

});
</script>

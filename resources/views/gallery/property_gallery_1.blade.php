@php
use Illuminate\Support\Str;


$propertyImages = [
    (object)['filename' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'], // luxury house
    (object)['filename' => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'], // cityscape
    (object)['filename' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'], // living room
];
@endphp

<div class="container mt-4">
  <div class="row g-3">
    @foreach($propertyImages as $img)
      @php
        $imgSrc = Str::startsWith($img->filename, ['http://', 'https://'])
                    ? $img->filename
                    : asset('storage/properties/' . $img->filename);
      @endphp
      <div class="col-6 col-md-4 col-lg-3">
        <img src="{{ $imgSrc }}"
             class="img-fluid rounded shadow-sm property-thumb"
             data-bs-toggle="modal"
             data-bs-target="#imageModal"
             data-img-src="{{ $imgSrc }}"
             alt="Property Image">
      </div>
    @endforeach
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark">
      <div class="modal-body text-center p-0">
        <img id="modalImage" src="" class="img-fluid rounded" alt="Preview">
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modalImage = document.getElementById('modalImage');
    const thumbs = document.querySelectorAll('.property-thumb');

    thumbs.forEach(thumb => {
      thumb.addEventListener('click', () => {
        const src = thumb.getAttribute('data-img-src');
        modalImage.setAttribute('src', src);
      });
    });
  });
</script>

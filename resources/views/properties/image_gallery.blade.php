@if($propertyImages->count())
  <div class="row align-items-center justify-content-center">
    <div class="col-auto">
      <span class="nav-arrow" id="prevImg">&#10094;</span>
    </div>

    <div class="col text-center">
      <img id="mainImage"
           src="{{ $propertyImages[0]->url }}"
           class="gallery-main-image rounded shadow-sm"
           alt="Main Image">
    </div>

    <div class="col-auto">
      <span class="nav-arrow" id="nextImg">&#10095;</span>
    </div>
  </div>

  <div class="row justify-content-center mt-3 g-2">
    @foreach($propertyImages as $idx => $img)
      <div class="col-auto">
        <img src="{{ $img->url }}"
             class="gallery-thumb rounded {{ $idx === 0 ? 'active' : '' }}"
             data-index="{{ $idx }}"
             alt="Thumb {{ $idx }}">
      </div>
    @endforeach
  </div>
@else
  <div class="text-center text-muted py-5">
    <i class="fa-solid fa-image fa-2x mb-2"></i><br>
    Images not assigned
  </div>
@endif

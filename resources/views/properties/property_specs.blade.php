<div class="row mt-4">
  <div class="col-md-6">
    <h5 class="">Property Specifications</h5>
  </div>
  <div class="col-md-6" align="right">
    <button type="button" class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addPropertyModal"
            style="min-width:200px;"
            onclick="editProperty({{ $property->id }})">
      <i class="fas fa-edit"></i> Edit
    </button>
  </div>

  <div class="col-md-12">
    <div class="info-box card shadow mt-2 mt-2">
      <p><strong>Erf No:</strong> {{ $property->erf_no ?? '' }}</p>
      <p><strong>Erf Size:</strong> {{ $property->erf_size ?? '' }}</p>
      <p><strong>GLA:</strong> {{ $property->gla ?? '' }}</p>
      <div class="border-bottom mb-3 border-primary"></div>

      <p><strong>Type:</strong> {{ $property->propertyType->type ?? '-' }}</p>
      <p><strong>Status:</strong> {{ $property->propertyStatus->status ?? '-' }}</p>
      <p><strong>Zoning:</strong> {{ $property->propertyZoning->zoning ?? '-' }}</p>
      <p><strong>Area:</strong> {{ $property->propertyArea->area ?? '-' }}</p>
      <p><strong>Location:</strong> {{ $property->propertyLocation->location ?? '-' }}</p>
    </div>
  </div>
</div>

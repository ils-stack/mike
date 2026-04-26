<div class="row mt-4">
  <div class="col-md-6">
    <h5 class="">Address</h5>
  </div>
  <div class="col-md-6" align="right">
    <button type="button" class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addPropertyModal"
            style="min-width:200px;"
            onclick="editProperty({{ $property->id }})">
      <i class="fas fa-edit"></i> Edit
    </button>
    <button type="button"
            class="btn btn-outline-primary ms-2"
            onclick="openPropertyDocsModal('property_doc', {{ $property->id }}, 'Documents')">
      <i class="fa-solid fa-file-lines me-1"></i> Documents
    </button>
  </div>
</div>

<div class="col-md-12">
  <div class="info-box card shadow mb-2 mt-2">
    <p><strong>Property Name:</strong> {{ $property->building_name ?? '' }}</p>
    <!-- <p><strong>Location:</strong> {{ $property->propertyLocation->location ?? '-' }}</p> -->
    <p><strong>Address:</strong> {{ $property->address ?? '' }}</p>
    <p><strong>Building Name:</strong> {{ $property->building_name ?? '' }}</p>
    <p><strong>Latitude:</strong> {{ $property->latitude ?? '' }}</p>
    <p><strong>Longitude:</strong> {{ $property->longitude ?? '' }}</p>

    <div>
      <strong>Meta:</strong>
      {{ $property->propertyType->type ?? '-' }} |
      {{ $property->propertyStatus->status ?? '-' }} |
      {{ $property->propertyZoning->zoning ?? '-' }} |
      {{ $property->propertyArea->area ?? '-' }} |
      {{ $property->propertyLocation->location ?? '-' }}
    </div>
  </div>
</div>

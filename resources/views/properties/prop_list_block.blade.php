<div class="col-md-6">

  <div class="card shadow p-3 mb-2">
    <div class="row">
      <div class="col-md-12">

        <div>
          <strong>Building Name:</strong>&nbsp;{{ $properties[$i]['building_name'] ?? '' }}
        </div>
        <div>
          <strong>Address:</strong>&nbsp;{{ $properties[$i]['address'] ?? '' }}
        </div>
        <div>
          <strong>Erf No:</strong>&nbsp;{{ $properties[$i]['erf_no'] ?? '' }}
        </div>
        <div>
          <strong>Erf Size:</strong>&nbsp;{{ $properties[$i]['erf_size'] ?? '' }}
        </div>
        <div>
          <strong>GLA:</strong>&nbsp;{{ $properties[$i]['gla'] ?? '' }}
        </div>

        <div>
          <strong>Meta:</strong>
          {{ $properties[$i]->propertyType->type ?? '-' }} |
          {{ $properties[$i]->propertyStatus->status ?? '-' }} |
          {{ $properties[$i]->propertyZoning->zoning ?? '-' }} |
          {{ $properties[$i]->propertyArea->area ?? '-' }} |
          {{ $properties[$i]->propertyLocation->location ?? '-' }}
        </div>

        <div class="col-md-12 mt-2 d-flex justify-content-end gap-2">
          <a class="btn btn-primary"
             href="/property-details/{{ $properties[$i]['id'] ?? '' }}"
             target="_blank">
             Property Details
          </a>
        </div>

      </div>
    </div>
  </div>

</div>

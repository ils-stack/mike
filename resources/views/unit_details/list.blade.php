<div class="card shadow">
  <div class="card-body">
    <h5 class="card-title mb-3">Property Units</h5>

    <table class="table table-striped table-bordered align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Properties</th>
          <th>Unit Type</th>
          <th>Listing Broker</th>
          <th>Availability</th>
          <th>Unit Status</th> {{-- ✅ NEW --}}
          <th>Lease Expiry</th>
          <th>Unit No</th>
          <th>Size (m²)</th>
          <th>Gross Rental</th>
          <th>Sale Price</th>
          <th style="width:150px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($units as $unit)
          @php
            // Linked properties
            $propertyNames = \App\Models\UnitProperty::where('unit_id', $unit->id)
              ->join('properties', 'properties.id', '=', 'unit_property.property_id')
              ->pluck('properties.building_name')
              ->toArray();

            // Unit status (master)
            $status = $unit->unit_status
              ? \App\Models\PropertyStatus::find($unit->unit_status)
              : null;
          @endphp
          <tr>
            <td>{{ $unit->id }}</td>
            <td>{{ implode(', ', $propertyNames) ?: '—' }}</td>
            <td>{{ $unit->unit_type }}</td>
            <td>{{ $unit->listing_broker }}</td>
            <td>{{ $unit->availability }}</td>

            {{-- ✅ STATUS WITH COLOR DOT --}}
            <td>
              @if($status)
                <span style="
                  display:inline-block;
                  width:20px;
                  height:20px;
                  border-radius:50%;
                  background:{{ $status->marker_color }};
                  margin-right:6px;
                  vertical-align:middle;
                "></span>
                <span>{{ $status->status }}</span>
              @else
                —
              @endif
            </td>

            <td>{{ $unit->lease_expiry }}</td>
            <td>{{ $unit->unit_no }}</td>
            <td>{{ $unit->unit_size }}</td>
            <td>{{ $unit->gross_rental }}</td>
            <td>{{ $unit->sale_price }}</td>
            <td>
              <button type="button" class="btn btn-sm btn-primary"
                      onclick="editUnit({{ $unit->id }})">
                <i class="fa fa-edit"></i> Edit
              </button>
              <button type="button" class="btn btn-sm btn-danger"
                      onclick="deleteUnit({{ $unit->id }})">
                <i class="fa fa-trash"></i> Delete
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="12" class="text-center">No units found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

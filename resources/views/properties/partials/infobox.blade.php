<div align = "right">
  <a class="btn btn-outline-primary btn-sm"
     href="/property-details/{{ $property->id }}"
     target="_blank">
    Property Details
  </a>
</div>

<hr>

<div style="min-width:440px;overflow-x:hidden;max-height:250px;">

  <strong style = "font-weight:bold;">{{ $property->building_name }}</strong>

  <hr>

  @forelse($property->units as $unit)
  <table class="table table-sm mb-0">
    <tbody>
      <tr><td>Unit No</td><td>{{ $unit->unit_no ?? '-' }}</td></tr>
      <tr><td>Unit Type</td><td>{{ $unit->unit_type ?? '-' }}</td></tr>
      <tr><td>Status</td><td>{{ $unit->unit_status ?? '-' }}</td></tr>
      <tr><td>Size</td><td>{{ $unit->unit_size ? number_format($unit->unit_size,2).' m²' : '-' }}</td></tr>
      <tr><td>Gross Rental</td><td>{{ $unit->gross_rental ?? '-' }}</td></tr>
      <tr><td>Sale Price</td><td>{{ $unit->sale_price ?? '-' }}</td></tr>
      <tr><td>Yield</td><td>{{ $unit->yield_percentage ?? '-' }}</td></tr>
      <tr><td>Availability</td><td>{{ $unit->availability ?? '-' }}</td></tr>
    </tbody>
  </table>

  <hr>

  @empty
    <em>No units found</em>
  @endforelse


</div>

<hr>

<div align = "right">
  <a class="btn btn-outline-primary btn-sm"
     href="/property-details/{{ $property->id }}"
     target="_blank">
    Property Details
  </a>
</div>

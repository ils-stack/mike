<div class="row mt-4">
  <div class="col-md-6">
    <h5 class="">Landlords</h5>
  </div>
  <div class="col-md-6" align = "right">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLandlordModal" style = "min-width:200px;">
      <i class="fas fa-edit"></i>
      Add Landlord
    </button>
  </div>
</div>

<div class="col-md-12">
  <div class="info-box card shadow mb-2 mt-2">
    <p><strong>Property Name:</strong> {{($locations[0]['building_name']??'')}}</p>
    <p><strong>Location:</strong> Mowbray, Southern Suburbs, Western Cape</p>
    <p><strong>Address:</strong> Durban Road</p>
    <p><strong>Building Name:</strong> {{($locations[0]['building_name']??'')}}</p>
    <p><strong>Latitude:</strong> {{($locations[0]['latitude']??'')}}</p>
    <p><strong>Longitude:</strong> {{($locations[0]['longitude']??'')}}</p>
  </div>
</div>

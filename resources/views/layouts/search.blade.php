@extends('layouts.app')

@section('content')
  <!-- Content -->
  <div class="container py-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Properties</li>
      </ol>
    </nav>

    <h4>Properties</h4>

    <form>
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Unit Status</label>
          <select class="form-select" multiple size="5">
            <option>To Let</option>
            <option>For Sale</option>
            <option>Tenanted</option>
            <option>Owner Occupied</option>
            <option>Sold - Vsure CRM</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Unit Type</label>
          <select class="form-select" multiple size="5">
            <option>Office</option>
            <option>Retail</option>
            <option>Industrial</option>
            <option>Land</option>
            <option>Residential</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Property Type</label>
          <select class="form-select" multiple size="2">
            <option>Freehold</option>
            <option>Sectional Title</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Area</label>
          <select class="form-select">
            <option>No options available</option>
          </select>
          <select class="form-select mt-2">
            <option>No options available</option>
          </select>
          <select class="form-select mt-2">
            <option>No options available</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Unit Size</label>
          <input type="text" class="form-control" placeholder="Min">
          <input type="text" class="form-control mt-2" placeholder="Max">
        </div>

        <div class="col-md-3">
          <label class="form-label">Unit Lease Expiry</label>
          <input type="date" class="form-control">
          <input type="date" class="form-control mt-2">
        </div>

        <div class="col-md-3">
          <label class="form-label">Date Sold/Let</label>
          <input type="date" class="form-control">
          <input type="date" class="form-control mt-2">
        </div>

        <div class="col-md-3">
          <label class="form-label">Contact</label>
          <input type="text" class="form-control" placeholder="Landlord/Manager/Tenant" disabled>
        </div>

        <div class="col-12">
          <label class="form-label d-block">Display</label>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="display" id="listView" checked>
            <label class="form-check-label" for="listView">List View</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="display" id="mapView">
            <label class="form-check-label" for="mapView">Map View</label>
          </div>
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-primary mt-3">Search</button>
        </div>
      </div>
    </form>
  </div>
@endsection

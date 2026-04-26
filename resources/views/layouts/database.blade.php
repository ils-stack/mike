@extends('layouts.app')

@section('content')
  <!-- Top Header -->
  <!-- <div class="Demo CRM-header d-flex justify-content-between align-items-center">
    <div>Demo CRM Properties</div>

  </div> -->

  <div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Databases</li>
      </ol>
    </nav>

    <!-- Form Section -->
    <div class="form-section">
      <h4 class="mb-4">Databases</h4>
      <form class="row g-3">
        <div class="col-md-3">
          <input type="text" class="form-control" placeholder="Contact Name">
        </div>
        <div class="col-md-3">
          <input type="text" class="form-control" placeholder="Company Name">
        </div>
        <div class="col-md-3">
          <select class="form-select">
            <option selected disabled>Contact Type</option>
            <option>Investors</option>
            <option>Tenants</option>
          </select>
        </div>
        <div class="col-md-3">
          <select class="form-select">
            <option selected disabled>Area</option>
            <option>No options available</option>
          </select>
        </div>

        <div class="col-md-3">
          <input type="text" class="form-control" placeholder="Unit Types">
        </div>
        <div class="col-md-3">
          <select class="form-select">
            <option selected disabled>Broker</option>
          </select>
        </div>
        <div class="col-md-3">
          <select class="form-select">
            <option selected>Actively looking</option>
            <option>Not looking</option>
          </select>
        </div>
        <div class="col-md-3 text-end">
          <button type="button" class="btn btn-light-gray">Add New</button>
        </div>

        <div class="col-md-3">
          <input type="text" class="form-control" placeholder="Min Size">
        </div>
        <div class="col-md-3">
          <input type="text" class="form-control" placeholder="Max Size">
        </div>
        <div class="col-md-3">
          <input type="text" class="form-control" placeholder="Min Budget">
        </div>
        <div class="col-md-3">
          <input type="text" class="form-control" placeholder="Max Budget">
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-secondary">Search</button>
        </div>
      </form>
    </div>
  </div>

  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .Demo CRM-header {
      background-color: #0c2f4e;
      color: white;
      padding: 1rem 1.5rem;
      font-weight: bold;
    }
    .form-section {
      background: white;
      padding: 2rem;
      border-radius: 8px;
      margin-top: 2rem;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    .btn-light-gray {
      background-color: #ddd;
      color: #000;
      font-weight: 600;
    }
  </style>
@endsection

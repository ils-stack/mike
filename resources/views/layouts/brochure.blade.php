@extends('layouts.app')   {{-- adjust the namespace if your layout path is different --}}

@section('title', 'Search')

@section('content')
  <div class="container-fluid">
    <div class="row">

      <!-- Main Content -->
      <div class="col-md-12">
        <div class="topbar d-flex justify-content-between align-items-center">
          <div><strong>Brochure</strong> (1)</div>
          <div>
            <span class="me-3">Welcome Ryan</span>
            <a href="#" class="btn btn-sm btn-warning">Logout</a>
          </div>
        </div>

        <div class="p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Primary Agent</label>
              <select class="form-select">
                <option>- Select -</option>
                <option>Mark Gedrych</option>
                <option>Brett Whall</option>
                <option>Ryan Perumal</option>
                <!-- Add others -->
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Secondary Agent</label>
              <select class="form-select">
                <option>- Select -</option>
              </select>
            </div>
          </div>

          <div class="mt-4 bg-dark rounded p-2">
            <h6>Durban Road <span class="badge bg-warning rounded-circle"> </span></h6>
            <p class="mb-1">Damelin<br>Mowbray, Southern Suburbs, Western Cape</p>
            <p class="mb-1">Unit no: Entire</p>
            <p class="mb-1">Unit type: Office</p>
            <p class="mb-1">Unit size: 3102 m²</p>
            <p class="mb-1">Sale price: R 45 000 000,00</p>
            <p><strong>Status: For Sale</strong></p>
            <a href="#" class="text-danger">Remove unit</a>
          </div>

          <div class="action-buttons">
            <button class="btn btn-outline-danger">Delete</button>
            <button class="btn btn-secondary">Print Brochure</button>
          </div>
        </div>
      </div>

    </div>
  </div>
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      background-color: #212529;
      min-height: 100vh;
      color: white;
    }
    .sidebar a {
      color: #ccc;
      text-decoration: none;
      display: block;
      padding: 10px 15px;
    }
    .sidebar a:hover {
      background-color: #343a40;
      color: #fff;
    }
    .topbar {
      background-color: #0d1c35;
      color: white;
      padding: 10px 20px;
    }
    .action-buttons {
      display: flex;
      gap: 10px;
      margin-top: 20px;
    }
  </style>
@endsection

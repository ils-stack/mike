@extends('layouts.app')

@section('content')
  <!-- Top Header -->
  <div class="vsure-header d-flex justify-content-between align-items-center">
    <div>Vsure CRM Properties</div>
    <div>
      Deals
    </div>
  </div>

  <div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Deals</li>
      </ol>
    </nav>

    <!-- Deals Search + Add -->
    <div class="form-section">
      <h4 class="mb-4">Deals</h4>
      <form class="row g-3">
        <div class="col-md-3">
          <input type="text" class="form-control" placeholder="Status">
        </div>
        <div class="col-md-6">
          <input type="text" class="form-control" placeholder="Landlord/Tenant/Property Name">
        </div>
        <div class="col-md-3 d-flex justify-content-end">
          <button type="button" class="btn btn-light-gray">Add New</button>
        </div>
        <div class="col-md-3">
          <select class="form-select">
            <option selected>- Select Broker -</option>
          </select>
        </div>
        <div class="col-md-3">
          <button type="submit" class="btn btn-secondary">Search</button>
        </div>
      </form>

      <!-- Deals Table -->
      <div class="table-responsive mt-4">
        <table class="table table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th>Id</th>
              <th>Landlord</th>
              <th>Tenant</th>
              <th>Property</th>
              <th>Sale Value</th>
              <th>Gross Rental p/m</th>
              <th>Lease Term</th>
              <th>Escalation</th>
              <th>Broker</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>8</td>
              <td>Greg Goodall</td>
              <td>CPT Metal Castle and Fiesta</td>
              <td>14 Sixth Street</td>
              <td>R 60,00</td>
              <td>R 60,00</td>
              <td>2</td>
              <td>8,00%</td>
              <td>Gavin Sumner</td>
              <td><a href="#" class="text-highlight">View Note</a></td>
            </tr>
            <tr>
              <td>7</td>
              <td>Greg Goodall</td>
              <td>AMMS</td>
              <td>22 Railway Road</td>
              <td>R 60,00</td>
              <td>R 60,00</td>
              <td>2</td>
              <td>8,00%</td>
              <td>Gavin Sumner</td>
              <td><a href="#" class="text-highlight">View Note</a></td>
            </tr>
            <tr>
              <td>6</td>
              <td>Greg Goodall</td>
              <td>Auto Mecca Service Centre</td>
              <td>28 Montague Drive</td>
              <td>R 55,00</td>
              <td>R 55,00</td>
              <td>1</td>
              <td>8,00%</td>
              <td>Gavin Sumner</td>
              <td><a href="#" class="text-highlight">View Note</a></td>
            </tr>
            <tr>
              <td>5</td>
              <td>Greg Goodall</td>
              <td>AC Logistics Solutions Pty Ltd</td>
              <td>28 Montague Drive</td>
              <td>R 55,00</td>
              <td>R 55,00</td>
              <td>1</td>
              <td>8,00%</td>
              <td>Gavin Sumner</td>
              <td><a href="#" class="text-highlight">View Note</a></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .vsure-header {
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
    .text-highlight {
      color: #f0ad4e;
      font-weight: 600;
    }
  </style>
@endsection

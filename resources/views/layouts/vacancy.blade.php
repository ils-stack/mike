@extends('layouts.app')

@section('content')

<!-- Top Header -->
<div class="vsure-header d-flex justify-content-between align-items-center">
  <div>Vsure CRM Properties</div>
  <div>
    Vacancy Schedules
  </div>
</div>

<div class="container">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mt-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">Vacancy Schedules</li>
    </ol>
  </nav>

  <!-- Form Section -->
  <div class="form-section">
    <h4 class="mb-4">Vacancy Schedules</h4>
    <form class="row g-3">
      <div class="col-md-6">
        <input type="text" class="form-control" placeholder="Title or description">
      </div>
      <div class="col-md-3">
        <select class="form-select">
          <option>-- Select Year --</option>
        </select>
      </div>
      <div class="col-md-3">
        <select class="form-select">
          <option>-- Select Month --</option>
        </select>
      </div>
      <div class="col-md-12">
        <button type="submit" class="btn btn-secondary">Search</button>
        <button type="button" class="btn btn-light-gray float-end">Add New</button>
      </div>
    </form>

    <!-- Vacancy Table -->
    <div class="table-responsive mt-4">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>Year</th>
            <th>Month</th>
            <th>Title</th>
            <th>Description</th>
            <th>Modified</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>2022</td>
            <td>November</td>
            <td class="text-orange">Capitalgro Property Management - Sublease</td>
            <td></td>
            <td>2022-11-11 06:23:46</td>
            <td><a href="#">Edit</a></td>
          </tr>
          <tr>
            <td>2022</td>
            <td>November</td>
            <td class="text-orange">Capitalgro Property Management - Current</td>
            <td></td>
            <td>2022-11-11 06:23:33</td>
            <td><a href="#">Edit</a></td>
          </tr>
          <tr>
            <td>2022</td>
            <td>November</td>
            <td class="text-orange">Connaught Park, Beaconvale</td>
            <td></td>
            <td>2022-11-11 06:23:20</td>
            <td><a href="#">Edit</a></td>
          </tr>
          <!-- Add more rows as needed -->
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
  .text-orange {
    color: #f0ad4e;
    font-weight: 600;
  }
  .table-striped tbody tr:nth-of-type(odd) {
    background-color: #fcfcfc;
  }
</style>
@endsection

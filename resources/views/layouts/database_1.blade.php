@extends('layouts.app')

@section('content')
  <!-- Top Header -->
  <div class="vsure-header d-flex justify-content-between align-items-center">
    <div>Vsure CRM Properties</div>
    <div>
      <span class="me-4">Brochure <span class="badge bg-warning text-dark">0</span></span>
      <span>Welcome Ryan | <a href="#" class="text-warning text-decoration-none">Logout</a></span>
    </div>
  </div>


  <div class="container-fluid">
    <div class="row">


      <!-- Main Content -->
      <div class="col-md-12">
        <div class="topbar d-flex justify-content-between align-items-center">
          <div><strong>Database Search</strong></div>
          <div>
            <span class="me-3">Welcome Ryan</span>
            <a href="#" class="btn btn-sm btn-warning">Logout</a>
          </div>
        </div>

        <div class="p-4">

          <!-- Search Filters -->
          <form class="row g-3 align-items-end mb-4">
            <div class="col-md-3">
              <label class="form-label">Unit Types</label>
              <select class="form-select">
                <option selected>- Select -</option>
                <option>Office</option>
                <option>Retail</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Broker</label>
              <select class="form-select">
                <option selected>- Select -</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Status</label>
              <select class="form-select">
                <option selected>Actively looking</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Budget Range</label>
              <div class="input-group">
                <input type="number" class="form-control" placeholder="Min" value="5000000">
                <input type="number" class="form-control" placeholder="Max" value="20000000">
              </div>
            </div>
            <div class="col-md-12">
              <button class="btn btn-secondary" type="submit">Search</button>
            </div>
          </form>

          <!-- Table -->
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead class="table-light">
                <tr>
                  <th>Rank</th>
                  <th>Name</th>
                  <th>Surname</th>
                  <th>Company Name</th>
                  <th>Office Number</th>
                  <th>Cell</th>
                  <th>Email</th>
                  <th>Size</th>
                  <th>Budget</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>0</td>
                  <td>Pascal</td>
                  <td>unknown</td>
                  <td>Acoustic Solutions</td>
                  <td>–</td>
                  <td>082 478 8343</td>
                  <td><a href="mailto:unknown22@unknown.co.za" class="email-link">unknown22@unknown.co.za</a></td>
                  <td>1 500 m²</td>
                  <td>R 10 000 000,00</td>
                </tr>
                <tr>
                  <td>0</td>
                  <td>Justin</td>
                  <td>Clark</td>
                  <td>Food Safety</td>
                  <td>–</td>
                  <td>073 252 3039</td>
                  <td><a href="mailto:justin@foodsafety.co.za" class="email-link">justin@foodsafety.co.za</a></td>
                  <td>500 m²</td>
                  <td>R 6 500 000,00</td>
                </tr>
                <!-- Add more rows as needed -->
              </tbody>
            </table>
          </div>

        </div>
      </div>

    </div>
  </div>

  <style>
    body { background-color: #f8f9fa; }
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
    .email-link {
      color: orange;
      text-decoration: none;
    }
    .email-link:hover {
      text-decoration: underline;
    }
  </style>
@endsection

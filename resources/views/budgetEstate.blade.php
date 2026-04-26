@extends('layouts.app')

@section('content')
<div class="container-fluid pt-4">
  <!-- Section: Main chart -->
  <section class="mb-4">
    <div class="card">
      <div class="card-header py-3">
        <h5 class="mb-0 text-center"><strong>Estate Budget</strong></h5>
      </div>
      <div class="card-body" style = "min-height:800px;">

        <!-- communication buttons here  -->

        <!-- CRM-only toolbar removed in export bundle for portability -->

        <!-- communication buttons here  -->

        @include('inc.budget_btns')

        <div class="row">
          <div class="col-md-12 mb-3">
            <h5 class="mb-4 m-4 p-2 rounded-5 text-center bg-primary text-white">
              <strong>Incomes</strong>
            </h5>
          </div>
        </div>
        <form class="row g-3 needs-validation" novalidate name = "users_data" method = "post" action = "/budget/update-est-budgets">
        @csrf
          @include('inc.estate_budget_grid')
          <div class="col-12" align = "right">
            <button class="btn mb-0 btn-primary" type="submit">Save Incomes Data</button>
          </div>
        </form>
        <div class="row">
          <div class="col-md-12 mb-3">
            <h5 class="mb-4 m-4 p-2 rounded-5 text-center bg-primary text-white">
              <strong>Expenses</strong>
            </h5>
          </div>
        </div>
        <form class="row g-3 needs-validation" novalidate name = "users_data" method = "post" action = "/budget/update-expenses-est">
        @csrf
          @include('inc.estate_expense_grid')
          <div class="col-12" align = "right">
            <button class="btn mb-0 btn-primary" type="submit">Save Expenses Data</button>
          </div>
        </form>

    </div>
    <div style = "margin-bottom:250px;">&nbsp;
    </div>
  </section>
@endsection

@extends('layouts.app')

@section('title','Budget')

@section('content')

<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Budget</h5>

        <button class="btn btn-sm text-white" style="background:#E34234;">
            <i class="fas fa-save me-1"></i> Save Budget
        </button>
    </div>

    <!-- TABS -->
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active"
                    data-bs-toggle="tab"
                    data-bs-target="#income">
                Income
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#expenses">
                Expenses
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#assets">
                Assets
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#summary">
                Summary
            </button>
        </li>
    </ul>

    <!-- TAB CONTENT -->
    <div class="tab-content">

        <!-- INCOME -->
        <div class="tab-pane fade show active" id="income">
            <div class="card mb-3">
                <div class="card-header fw-bold">Income</div>
                <div class="card-body">

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Primary Income</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secondary Income</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- EXPENSES -->
        <div class="tab-pane fade" id="expenses">
            <div class="card mb-3">
                <div class="card-header fw-bold">Monthly Expenses</div>
                <div class="card-body">

                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Housing</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Transport</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Utilities</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ASSETS -->
        <div class="tab-pane fade" id="assets">
            <div class="card mb-3">
                <div class="card-header fw-bold">Assets</div>
                <div class="card-body">

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Property Value</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Investments</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- SUMMARY -->
        <div class="tab-pane fade" id="summary">
            <div class="card">
                <div class="card-header fw-bold">Summary</div>
                <div class="card-body">

                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="fw-bold">Total Income</div>
                            <h5 class="text-success">R 0.00</h5>
                        </div>
                        <div class="col-md-4">
                            <div class="fw-bold">Total Expenses</div>
                            <h5 class="text-danger">R 0.00</h5>
                        </div>
                        <div class="col-md-4">
                            <div class="fw-bold">Monthly Balance</div>
                            <h5 style="color:#E34234;">R 0.00</h5>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

@endsection

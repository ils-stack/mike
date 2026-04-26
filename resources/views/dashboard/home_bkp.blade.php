@extends('layouts.app')

@section('title','Dashboard')

@section('content')

<div class="container-fluid">

    <!-- PRIMARY KPI ROW -->
    <div class="row g-2 mb-3">

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-white text-center" style="background:#E34234;">
                <div class="card-body">
                    <i class="fas fa-bullseye fa-2x mb-2"></i>
                    <div class="fw-bold">Wealth Planning</div>
                    <small>Goals · Estate · Tax</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-white text-center" style="background:#E34234;">
                <div class="card-body">
                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                    <div class="fw-bold">Investment Planning</div>
                    <small>Portfolio · Property</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-white text-center" style="background:#E34234;">
                <div class="card-body">
                    <i class="fas fa-user-clock fa-2x mb-2"></i>
                    <div class="fw-bold">Retirement Planning</div>
                    <small>Annuities · Income</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-white text-center" style="background:#E34234;">
                <div class="card-body">
                    <i class="fas fa-car-crash fa-2x mb-2"></i>
                    <div class="fw-bold">Short-Term Insurance</div>
                    <small>Personal · Business</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-white text-center" style="background:#E34234;">
                <div class="card-body">
                    <i class="fas fa-heartbeat fa-2x mb-2"></i>
                    <div class="fw-bold">Life Assurance</div>
                    <small>Life · Disability</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-white text-center" style="background:#E34234;">
                <div class="card-body">
                    <i class="fas fa-briefcase fa-2x mb-2"></i>
                    <div class="fw-bold">Business Solutions</div>
                    <small>Succession · Benefits</small>
                </div>
            </div>
        </div>

    </div>

    <!-- SECONDARY KPI ROW -->
    <div class="row g-2 mb-3">

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-calculator fa-2x text-danger mb-2"></i>
                    <div class="fw-bold">Financial Calculators</div>
                    <small>Retirement · Savings · Insurance</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x text-danger mb-2"></i>
                    <div class="fw-bold">Clients & Families</div>
                    <small>Profiles · Notes · Tasks</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-file-shield fa-2x text-danger mb-2"></i>
                    <div class="fw-bold">Policies & Compliance</div>
                    <small>Audit · Governance</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-database fa-2x text-danger mb-2"></i>
                    <div class="fw-bold">Data Intake</div>
                    <small>CSV Import · Mapping</small>
                </div>
            </div>
        </div>

    </div>

    <!-- CHART ROW -->
<div class="row g-2 mt-2">

    <!-- Performance Chart -->
    <div class="col-lg-8 col-md-12">
        <div class="card h-100">
            <div class="card-header bg-white fw-bold">
                Performance Chart
            </div>
            <div class="card-body d-flex align-items-center justify-content-center"
                 style="min-height:300px;">

                {{-- line chart placeholder --}}
                <div class="text-center text-muted">
                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                    <div>Data 1 vs Comparison 1</div>
                </div>

            </div>
        </div>
    </div>

    <!-- Portfolio Pie -->
    <div class="col-lg-4 col-md-12">
        <div class="card h-100">
            <div class="card-header bg-white fw-bold">
                Portfolio
            </div>
            <div class="card-body d-flex align-items-center justify-content-center"
                 style="min-height:300px;">

                {{-- pie chart placeholder --}}
                <div class="text-center text-muted">
                    <i class="fas fa-chart-pie fa-3x mb-3"></i>
                    <div>Asset Allocation</div>
                </div>

            </div>
        </div>
    </div>

</div>


</div>

@endsection

<div class="row g-3 mt-2">

    <!-- LEFT SIDE -->
    <div class="col-lg-9">
        @include('dashboard.partials.dash_kpi')

        <!-- ACCOUNTS TABLE -->

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="fw-bold mb-2">
                    Accounts
                </div>

                @include('dashboard.partials.datatable')

            </div>
        </div>

    </div>


    <!-- RIGHT SIDE -->
    <div class="col-lg-3">

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <canvas id="portfolioChart" height="60"></canvas>

            </div>
        </div>

    </div>

</div>

<div class="row g-3 mb-2">

    <!-- Total Value -->
    <div class="col-md-6">

        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">

                <div class="small">
                    Total Value
                </div>

                <div class="fw-bold fs-4">
                    R {{ number_format($contracts->sum('market_value'),2) }}
                </div>

            </div>
        </div>

    </div>

    <!-- Number of Accounts -->
    <div class="col-md-6">

        <div class="card border-0 shadow-sm" >
            <div class="card-body text-center">

                <div class="small">
                    Number of Accounts
                </div>

                <div class="fw-bold fs-4">
                    {{ $contracts->count() }}
                </div>

            </div>
        </div>

    </div>

</div>

@extends('layouts.app')

@section('content')

<style>
.dashnum-card{
  height: 85%;
}
.dashnum-card-stat{
  height: 101%;
}
.f-15{white-space: nowrap;}
</style>

<div>
  <div>
    <div class="col-xl-12">
      <div class = "row">
        <!-- <div class="col-xl-12 col-md-12">
          <div class="card">
            <div class="card-body">
              Welcome, {{$cdta['First Name']??''}} {{$cdta['Surname']??''}}
            </div>
          </div>
        </div> -->

        <div class="col-xl-12 col-md-12">
          <div class="card">
            <div class="card-body">
              <strong>{{ $acc_name }}: {{$acc??''}}</strong>
              <!-- @ foreach($adta['Accounts'] as $a_key => $a_val)
                <strong>{{($a_val['Account number']??0)}} | </strong>
              @ endforeach -->
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-12">
      <div class = "row">

        <div class="col-xl-12 col-md-12">
          <div class="row">
            @include('holdings.partials.infocard_det')
            @include('holdings.partials.icard_hold_det')
            @include('holdings.partials.irrcum_det')
            @include('holdings.partials.irrann_det')

          </div>
        </div>

      </div>
    </div>

    <div class="col-xl-12">
      <div class = "row">

        <div class="col-xl-12 col-md-12">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-xl-3 col-md-12 mb-3" data-bs-toggle="tooltip" title="Current holdings">
                  <strong>Current Holdings</strong>
                </div>
              </div>

              <div class="col-xl-12 col-md-12">
                <div class="row">
                  <div class="col-xl-8 col-md-8 mb-3 small">
                    @include('holdings.partials.holddetgrid')
                  </div>

                  <div class="col-xl-4 col-md-4 mb-3">
                    <canvas id="chart-pie"></canvas>

                    <script>
                    $(document).ready(function(){

                        const ctx = document.getElementById('chart-pie');

                        new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: [{!! $pol_list !!}],
                                datasets: [{
                                    data: [{!! $acc_total !!}],
                                    backgroundColor: [
                                        "rgba(234,165,60,0.8)",
                                        "rgba(65,71,76,0.8)",
                                        "rgba(126,134,140,0.8)",
                                        "rgba(216,166,63,0.8)",
                                        "rgba(60,84,95,0.8)",
                                        "rgba(168,176,177,0.8)"
                                    ]
                                }]
                            },
                            options:{
                                responsive:true,
                                plugins:{
                                    legend:{
                                        position:'bottom'
                                    }
                                }
                            }
                        });

                    });
                    </script>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

     <!-- dashnum-card -->

    <div class="col-xl-12">
      <div class = "row">
        <div class="col-xl-6">
          <div class = "row">

            <div class="col-xl-12 col-md-12">
              <div class="card dashnum-card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-xl-12 col-md-12 mb-3">
                      <strong>Previous Holdings</strong>
                    </div>
                  </div>

                  <div class="col-xl-12 col-md-12 small">
                    @include('holdings.partials.prev_holddetgrid')
                  </div>

                </div>
              </div>
            </div>

          </div>
        </div>

        <div class="col-xl-6">
          <div class = "row">

            <div class="col-xl-12 col-md-12">
              <div class="card dashnum-card">
                <div class="card-body">
                  <strong>Account Statement</strong>

                  <div class="col-xl-12 col-md-12 pt-3">
                    <div class="row">
                        @include('holdings.partials.scard_1')
                        @include('holdings.partials.scard_2')
                        @include('holdings.partials.scard_3')
                    </div>
                  </div>
                </div>
              </div>
            </div>



          </div>
        </div>
      </div>
    </div>

    @include('holdings.partials.acc_docs_holdings')
  </div>
</div>

<script>
$(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endsection

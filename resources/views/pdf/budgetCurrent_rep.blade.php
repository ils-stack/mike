@extends(isset($print) ? 'layouts.rep' : 'layouts.app')

@section('content')
<style>
.table td, .table th{
  padding: .1rem;
  font-size: 0.6rem;
  font-weight: 400;
  line-height: 1;
}
.summtbl td, .table th{
  padding: .1rem;
  font-size: 0.6rem;
  font-weight: 400;
  line-height: 1.1;
}
.table{margin-bottom: 0px;}
body {
  font-family: sans-serif;
  font-size: 14px;
}
.section-title {
  background-color: #007bff;
  color: #fff;
  padding: 6px;
  font-weight: bold;
}
.bg-primary {
  background-color: #007bff !important;
  color: #fff !important;
}
.text-white {
  color: #fff !important;
}
.border {
  border: 1px solid #ccc;
}
.text-center{text-align: center;}
.p-1{padding: 1px;}
.p-2{padding: 2px;}
.p-3{padding: 3px;}
.p-4{padding: 4px;}
.p-5{padding: 5px;}

.m-1{margin: 1px;}
.m-2{margin: 2px;}
.m-3{margin: 3px;}
.m-4{margin: 4px;}
.m-5{margin: 5px;}
</style>
<!-- <div class="container-fluid pt-1"> -->
  <!-- Section: Main chart -->
  <section class="mb-1">
    <div class="card">
      <div class="bg-primary p-2" style="margin-bottom: 5px;">

        <div style="position: relative; height: 60px; text-align: center; background-color: #007bff; color: white; margin-bottom: 2px;">

          <!-- Centered Heading -->
          <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <h5 style="margin: 0;">Current Budget [{{$client_nm}}]</h5>
          </div>

          <!-- Top-right Superimposed Logo -->
          <div style="position: absolute; top: 2px; right: 2px;">
            <img src="{{ public_path('assets/img/optimate.png') }}" height="60" alt="" />
          </div>

        </div>


      </div>


      <!-- <div class="card-body"> -->
      <div class="">
        <div class="row">
          <div class="col-md-12 mb-1" align = "center">
            <span>
              <strong>Incomes</strong>
            </span>
          </div>
        </div>

        @include('inc.curr_budget_rep')

        <div class="row">
          <div class="col-md-12 mb-1" align = "center">
            <strong>Expenses</strong>
          </div>
        </div>

        @include('inc.current_expense_rep')

        <div style="margin-top: 2px;">
          <table style="width: 100%; border-collapse: collapse;" class = "summtbl">
            <tr>
              <!-- Client Summary -->
              <td style="width: 50%; padding: 1px; border: 1px solid #ccc; vertical-align: top;">
                <strong>Client Summary</strong><br>
                <span style = "margin-left:110px;">Client taxable income: {{$budgetObj->formatRand($total_arr['c_tax']??0)}}</span><br>
                <span style = "margin-left:110px;">Client non-taxable income: {{$budgetObj->formatRand($total_arr['c_ntax']??0)}}</span><br>
                <span><strong style = "margin-left:110px;">Gross: {{$budgetObj->formatRand(($total_arr['c_tax']??0) + ($total_arr['c_ntax']??0))}}</strong></span>
              </td>

              <!-- Spouse Summary -->
              <td style="width: 50%; padding: 1px; border: 1px solid #ccc; vertical-align: top;">
                <strong>Spouse Summary</strong><br>
                <span style = "margin-left:120px;">Spouse taxable income: {{$budgetObj->formatRand($total_arr['s_tax']??0)}}</span><br>
                <span style = "margin-left:120px;">Spouse non-taxable income: {{$budgetObj->formatRand($total_arr['s_ntax']??0)}}</span><br>
                <span><strong style = "margin-left:120px;">Gross: {{$budgetObj->formatRand(($total_arr['s_tax']??0) + ($total_arr['s_ntax']??0))}}</strong></span>
              </td>
            </tr>
            <tr>
              <td colspan="1" style="width: 50%; padding: 1px; border: 1px solid #ccc; vertical-align: top;">
                <strong>Total Expenses:</strong><br>
                <span style = "margin-left:110px;">{{$budgetObj->formatRand($exp_tot??0)}}</span>
              </td>
              <td colspan="1" style="width: 50%; padding: 1px; border: 1px solid #ccc; vertical-align: top;">
                <strong>Net Income:</strong><br>
                <span style = "margin-left:110px;">{{$budgetObj->formatRand((!(empty($total_arr))?array_sum($total_arr):0)-($exp_tot??0))}}</span>
              </td>
            </tr>
          </table>
        </div>

      </div>
    </div>
  </section>
<!-- </div> -->
@endsection

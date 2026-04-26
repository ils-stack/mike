<div class="row">
  <div class="col-md-12 mb-1" align = "center">
    <table class="table table-bordered" align = "center">
    <!-- <table class="table table-bordered table-striped" align = "center"> -->
      <tr>
        <td colspan="5"><strong>Client</strong></td>
      </tr>
    @foreach($curr_incomes as $fkey => $fkel)
      <tr>
        <td style = "padding-left:25px;">{!!$fkel['incomename']!!}</td>
        <td>{!!$fkel['c_taxable']!!}</td>
        <td>{!!$fkel['c_nontaxable']!!}</td>
      </tr>
    @endforeach
    <tr>
      <td colspan="5"><strong>Spouse</strong></td>
    </tr>
    @foreach($curr_incomes as $fkey => $fkel)
      <tr>
        <td style = "padding-left:25px;">{!!$fkel['incomename']!!}</td>
        <td>{!!$fkel['s_taxable']!!}</td>
        <td>{!!$fkel['s_nontaxable']!!}</td>
      </tr>
    @endforeach
    </table>
  </div>
</div>

<table class="table table-bordered" align = "center">
  @foreach($curr_expenses as $fkey => $fkel)
    <tr>
      <td>{!!$fkel['expensename']!!}</td>
      <td>{!!$fkel['c_taxable']!!}</td>
    </tr>
  @endforeach
</table>

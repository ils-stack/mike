<div>
  <div class="section-title">{{$budget_cats[$i]['alname']}}</div>
  <table>
    <tr>
      <th>Description</th>
      <th>Value (Client)</th>
      <th>Owing (Client)</th>
      <th>Value (Spouse)</th>
      <th>Owing (Spouse)</th>
    </tr>

    @include('pdf.asset_rows')

  </table>
</div>

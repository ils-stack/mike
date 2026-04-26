<tr>
  <td>{{$p_grid['Instrument name'][$i]??''}}</td>
  <td>{{$p_grid['Unit Price'][$i]??''}}</td>
  <!-- <td>{{$p_grid['Units'][$i]??''}}</td> -->
  <td>{{$p_grid['Sum_formated'][$i]??''}}</td>
  <!-- <td>{{$p_grid['Movement'][$i]??''}}</td> -->
  <!-- <td>{{'R '.number_format(($accounts['Market value']['value']??0),2,'.',' ')}}</td>
  <td>{{$per_chg??0}}</td> -->
  <td>
    <a target = "_blank" href = "https://unum.co.za/one/api/factsheet/?dlsCode={{$p_grid['Instrument Code'][$i]}}">
      <i class="fas fa-chart-bar fa-lg me-3">&nbsp;&nbsp;</i>
      &nbsp;
    </a>
  </td>
  <td>
    <a href = "/certificate?tp={{$p_grid['Instrument Code'][$i]}}">
      <i class="fas fa-eye fa-lg me-3">&nbsp;&nbsp;</i>
      &nbsp;
    </a>
  </td>
</tr>

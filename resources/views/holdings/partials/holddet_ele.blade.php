<tr>
  <td data-bs-toggle="tooltip" title="{{$c_grid['Instrument name'][$i]??''}}">
    {{$c_grid['Instrument name'][$i]??''}}
  </td>

  <td>{{$c_grid['Unit Price'][$i]??''}}</td>

  <td>{{$c_grid['Units'][$i]??''}}</td>

  <td>{{$c_grid['Sum_formated'][$i]??''}}</td>

  <td>{{$c_grid['Movement'][$i]??''}}</td>

  <td>
    @if($c_grid['got_fs'][$i] == 1)
      <a target="_blank" href="https://unum.co.za/one/api/factsheet/?dlsCode={{$c_grid['Instrument Code'][$i]}}">
        <i class="fas fa-chart-bar fa-lg me-2"></i>
      </a>
    @else
      -
    @endif
  </td>

  <td>
    <a href="/certificate?tp={{$c_grid['Instrument Code'][$i]}}">
      <i class="fas fa-eye fa-lg me-2"></i>
    </a>
  </td>

  <td>
    @if($c_grid['got_trans'][$i] == 1)
      <a href="/transactions?tp={{$c_grid['Instrument Code'][$i]}}">
        <i class="ti ti-arrows-horizontal" style="font-size:24px;"></i>
      </a>
    @else
      -
    @endif
  </td>
</tr>

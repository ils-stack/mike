<tr>
  <td>{{$doc_arr[$i]['Type']??''}}</td>
  <td>{{$doc_arr[$i]['Label']??''}}</td>
  <td>{{$doc_arr[$i]['Date']??''}}</td>
  <td>
    <a target = "_blank" href = "/download-document?docid={{preg_replace("/[^0-9]/","",$doc_arr[$i]['Document id'])}}">
      <i class="fas fa-eye fa-lg me-3">&nbsp;&nbsp;</i>
      &nbsp;
    </a>
  </td>
</tr>

<!DOCTYPE html>
<html>
<head>
  <!-- Bootstrap 5 CDN -->
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"> -->
  <style>
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
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 6px;
      text-align: left;
    }
    .total-row td {
      border: none;
      padding-top: 10px;
      font-weight: bold;
    }
    .highlight {
      background-color: #ffff99;
      display: inline-block;
      padding: 4px 8px;
      border: 1px solid #ccc;
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
</head>
<body>
  <div class="bg-primary p-2" style="margin-bottom: 5px;">

    <div style="position: relative; height: 80px; text-align: center; background-color: #007bff; color: white; margin-bottom: 10px;">

      <!-- Centered Heading -->
      <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <h2 style="margin: 0;">Assets &amp; Liabilities [{{$client_nm}}]</h2>
      </div>

      <!-- Top-right Superimposed Logo -->
      <div style="position: absolute; top: 2px; right: 2px;">
        <img src="{{ public_path('assets/img/vsure.png') }}" height="80" alt="" />
      </div>

    </div>


  </div>

  <?php
  $last = count($budget_cats);

  //BA:segregation of budget field based on categories
  unset($sorted_rows);
  for($k=0;$k<count($user_fields);$k++){
    if(stristr($user_fields[$k]['field'],"client_description")){
      $b_id = explode("_",$user_fields[$k]['field'])[3];
      $sorted_rows[$b_id][] = 1;
    }
  }

    // echo "<pre>";
    // print_r($sorted_rows);
    // echo "</pre>";
  ?>
  @for($i=0;$i<$last;$i++)
    @include('pdf.asset_block')
  @endfor

  <div style="margin-top: 10px;">

  <table style="width: 100%; border-collapse: collapse;">
    <tr>
      <!-- Client Summary -->
      <td style="width: 50%; padding: 10px; border: 1px solid #ccc; vertical-align: top;">
        <strong>Client Summary</strong><br>
        <span style = "margin-left:110px;">Value: {{$assetObj->formatRand($client_total_summary['clientvalue']??0)}}</span><br>
        <span style = "margin-left:110px;">Owing: {{$assetObj->formatRand($client_total_summary['clientowing']??0)}}</span><br>
        <span><strong style = "margin-left:110px;">Net: {{$assetObj->formatRand(($client_total_summary['clientvalue']??0) - ($client_total_summary['clientowing']??0))}}</strong></span>
      </td>

      <!-- Spouse Summary -->
      <td style="width: 50%; padding: 10px; border: 1px solid #ccc; vertical-align: top;">
        <strong>Spouse Summary</strong><br>
        <span style = "margin-left:120px;">Value: {{$assetObj->formatRand($client_total_summary['spousevalue']??0)}}</span><br>
        <span style = "margin-left:120px;">Owing: {{$assetObj->formatRand($client_total_summary['spouseowing']??0)}}</span><br>
        <span><strong style = "margin-left:120px;">Net: {{$assetObj->formatRand(($client_total_summary['spousevalue']??0) - ($client_total_summary['spouseowing']??0))}}</strong></span>
      </td>
    </tr>
  </table>

</div>

</body>
</html>

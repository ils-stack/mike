<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Redirect;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Http\Controllers\SecureRequest;
use App\Http\Controllers\CapRequest;

// use App\Models\BrokerMaster as BrokerModel;
// use App\Models\Products as ProductModel;

class StatementController extends Controller
{
    protected SecureRequest $api;

    public function __construct()
    {
        $this->api = new SecureRequest();
    }

    public function getScreen(Request $request){
      $dta = array();

      // //BA: cycle api urls
      // $api_urls = config('globals.global_api_urls');
      // $api_client_id = config('globals.global_api_client_id');
      // $api_base_url = config('globals.global_api_base_url');
      //
      // $url = str_replace("[CLIENTID]",$api_client_id,$api_base_url.$api_urls[1]);

      // BA: fetch json directly by setting raw para false
      // $c_obj = new SecureRequest();
      // $cdta = $c_obj->get($url,$dta,false);

      $entityId = session('investor_entity_unique_id');

      $cdta = $this->api->get('/api/entities/' . $entityId);

      // echo "<pre>";
      // print_r($cdta);
      // echo "</pre>";
      // exit;

      $idnum = $cdta['ID Number']??'';

      // echo "<pre>";
      // print_r($cdta);
      // echo "</pre>";
      // exit;

      $tp = $request->get('tp')??3;

      $api_urls = config('globals.global_api_urls');
      $api_client_id = config('globals.global_api_client_id');
      $api_base_url = config('globals.global_api_base_url');

      $acc = $request->get('acc')??"";

      if($acc === 'combined'){
        //BA search is entiry id from the account info
        $url = $api_base_url."api/accounts/statement?&search_key=$idnum&start_date=20240201&end_date=20240423&add_password=false";
        // exit;
      }else{
        $url = str_replace("[CLIENTID]",$api_client_id,$api_base_url.$api_urls[6]);
        $url = str_replace("[ACNUM]",$acc,$url);
        $url .= '&add_password=false';

        if($tp>0){
          switch($tp){
            case 12:
              $start = date('Y/m/d',strtotime('-12 months'));
              $end = date('Y/m/d');

              $url = str_replace("[STARTDT]",$start,$url);
              $url = str_replace("[ENDDT]",$end,$url);
            break;
            case 6:
              $start = date('Y/m/d',strtotime('-6 months'));
              $end = date('Y/m/d');

              $url = str_replace("[STARTDT]",$start,$url);
              $url = str_replace("[ENDDT]",$end,$url);
            break;
            case 3:
            default:
              $start = date('Ymd',strtotime('-3 months'));
              $end = date('Ymd');
              // $end = date('Y-m-d',strtotime('-1 days'));

              $url = str_replace("[STARTDT]",$start,$url);
              $url = str_replace("[ENDDT]",$end,$url);
            break;
          }
        }
      }

      // echo $start." ".$end;
      // exit;

      $cdta = $this->api->getPdf(
          '/api/accounts/VIP8000060/statement',
          [
              'start_date' => $start,
              'end_date' => $end,
              'add_password' => 'false'
          ]
      );

      // echo "<pre>";
      // print_r($cdta);
      // echo "</pre>";
      // exit;

      // unset($s_obj,$sret,$sdta);
      // $s_obj = new CapRequest();
      // $sdta = $s_obj->get($url,$dta,false);

      // echo ('/' . $url);
      // exit;

      // $sdta = $this->api->get1('/' . $url);

      // echo $url;
      // exit;
      // echo "<pre>";
      // print_r($cdta);
      // echo "</pre>";
      // exit;

      // $cdta = $this->api->get('/api/entities/' . $entityId);
      //
      // api/accounts/VIP800354/statement?start_date=20251213&end_date=20251213&add_password=false

      // if(isset($sdta['errors'])){
      //   unset($s_obj,$sdta);
      //   $s_obj = new PrimeRequest();
      //   $sdta = $s_obj->get($url,$dta,true);
      // }else{
      //   // BA: if the error message was not returned then, fetch the raw pdf again
      //
      //   unset($s_obj,$sdta);
      //   $s_obj = new CapRequest();
      //   $sdta = $s_obj->get($url,$dta,true);
      // }

      $filename = 'Report';

      header("Content-type:application/pdf");
      header("Content-Disposition:inline;filename=$filename");
      echo $cdta;

      unset($param_array);
      $param_array['broker_cnt'] = 0;
      $param_array['prod_cnt'] = 0;
      $param_array['cdta'] = $cdta;

      return view('statement',$param_array);
    }
}

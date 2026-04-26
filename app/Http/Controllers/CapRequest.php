<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Redirect;
use Session;
use Illuminate\Support\Facades\Auth;

class CapRequest extends Controller
{
    private static $m_publicCertPath; //"/public_cert.pem"
    private static $m_privateCertPath; //"/private.pem")
    // private $m_caInfoPAth; //"/etc/ssl/certs/ca-certificates.crt"

    public static function init() {
      // $tp = config('globals.global_api_tp');
      // // exit;
      //
      // // BA: the session API section via select-profile
      // if(Session::get('global_api_tp')){
      //   $tp = Session::get('global_api_tp');
      // }
      //
      // switch($tp){
      //   case 1:
      //   default:
          // self::$m_publicCertPath = resource_path('livecrt/').'mycrt.crt';
          // self::$m_privateCertPath = resource_path('livecrt/').'server.pem';

          self::$m_publicCertPath = base_path(env('API_LIVE_CERT'));
          self::$m_privateCertPath = base_path(env('API_LIVE_KEY'));

      //   break;
      //   case 2:
      //     self::$m_publicCertPath = resource_path('certs/').'prime.crt';
      //     self::$m_privateCertPath = resource_path('certs/').'combined.pem';
      //   break;
      // }
    }

    public static function post($url, $data) {
      return $this->sendRequest("POST", $url, $data);
    }

    // BA: parameters
    // $url: curl get url, $data: params, $raw: send raw or as json
    public function get($url, $data,$raw=true) {
      self::init();

      $queryData = http_build_query($data);
      // $urlWithData = $url . '?' . $queryData;
      $urlWithData = $url;

      // echo var_dump($urlWithData);
      // exit;

      if($raw){
        return $this->sendRequest("GET", $urlWithData, array());
      }
      else {
        return json_decode($this->sendRequest("GET", $urlWithData, array()),true);
      }
    }

    private function sendRequest($method, $url, $data,$debug=false) {

        $url = "https://secure.thecycle.co.za/".$url;

        // echo $url;
        // exit;

        //TODO: enforce https://-url
        //$data = array('key'=>'value');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT , 443);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSLCERT, self::$m_publicCertPath);
        curl_setopt($ch, CURLOPT_SSLKEY, self::$m_privateCertPath);
        // curl_setopt($ch, CURLOPT_CAINFO, $this->m_caInfoPath);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if( strtoupper($method) == "POST" )
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $response = curl_exec($ch);
        $info = curl_errno($ch)>0 ? array("curl_error_".curl_errno($ch)=>curl_error($ch)) : curl_getinfo($ch);

        // echo "<pre>";
        // print_r($response);
        // print_r($info);
        // echo "</pre>";
        // echo $url;

        if($debug){
          echo "<pre>";
          print_r($response);
          print_r($info);
          echo "</pre>";
          echo $url;
        }
        curl_close($ch);

        return $response;
    }
}
?>

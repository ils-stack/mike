<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Http\Controllers\SecureRequest;

use App\Models\Investor;

class EntitiesController extends Controller
{
    protected SecureRequest $api;

    public function __construct()
    {
        $this->api = new SecureRequest();
    }

    public function getHome(Request $request)
    {
        // BA: do not remove this comment
        // https://secure.thecycle.co.za/api/entities/17615975681/accounts

        // echo session('investor_id');

        if (session()->has('investor_id')) {
          $entityId = Investor::where('id', session('investor_id'))
              ->value('entity_unique_id');

          session(['investor_entity_unique_id' => $entityId]);
        }

        $api_client_id = $entity_number = session('investor_entity_unique_id');

        try {
            $acc_total = array();
            $pol_list = array();
            $pol_det = array();
            $acc_names = array();
            $holdings_arr = array();
            $transaction_arr = array();
            $cumm_arr = array();

            $acc = $request->get('acc')??'VIP800354';
            $api_urls = config('globals.global_api_urls');
            $api_base_url = '';
            $api_client_id = '';

            // BA: static info till the api in enabled
            $cdta = [
                'id' => 2,
                'firstname' => 'Demo',
                'surname' => 'User',
                'password' => '$2y$10$eGLtrAkNmN6.HyE7ijHyv.fr78dBtjJI8midNirxwwtoWiRI2H1FG',
                'emailaddress' => 'demo@unum.capital',
                'userstatus' => 1,
                'roleid' => 2,
                'parentid' => 0,
                'greydot_api_key' => '',
                'created_at' => '2023-09-27T19:53:02.000000Z',
                'updated_at' => '2024-10-11T11:35:47.000000Z',
                'alpha' => 1,
                'title' => 'sdfds',
                'middle_name' => 'dsfds',
                'Initials' => 'init',
                'maiden_name' => 'dsfds',
                'gender' => 0,
                'nationality' => 0,
                'id_number' => 'dsfsd',
                'dob' => '0000-00-00',
                'client_number' => 'dsfds',
                'cell_phone' => 'dsfds',
                'telephone_home' => 'sfds',
                'telephone_work' => 'dsfds',
                'res_add_line_1' => 'dsfds',
                'res_add_line_2' => 'dsfds',
                'res_add_line_3' => 'dsfds',
                'city' => 'dsfds',
                'postal_code' => 'dsfds',
                'country_code' => 'dsfds',
                'post_add_line_1' => 'dsfds',
                'post_add_line_2' => 'dsfds',
                'post_add_line_3' => 'dsfds',
                'post_city' => 'dsfdsf',
                'postal_post_code' => 'dsfds',
                'post_country_code' => 'dsfdsf',
                'id_number_api' => '',
                'entity_id' => $api_client_id,
                'company_name' => '',
                'company_reg' => '',
                'type' => 0,
                'reset_hash' => '',
                'name' => '',
                'email' => '',
                'email_verified_at' => '0000-00-00 00:00:00',
                'remember_token' => '',
                'tp' => 1,
                'Client Number' => $api_client_id,
                'First Name' => 'Demo',
                'Surname' => 'User',
                'Title' => 'sdfds',
                'ID Number' => 'dsfsd',
                'Email address' => 'demo@unum.capital',
                'Cell' => 'dsfds'
            ];

            // BA: requested leon to ask cycle to authorise
            $response = $this->api->get('/api/entities/' . $entity_number . '/accounts');

            // $adta = json_decode($response,true);
            $adta = $response;

            // echo "<pre>";
            // print_r($adta);
            // echo "</pre>";
            // exit;

            // $adta = [
            //     'Investment value' => [
            //         'type' => 'Money',
            //         'currency' => 'ZAR',
            //         'value' => 725324.71
            //     ],
            //
            //     'Investor name' => 'Judith Anne Mcleod',
            //
            //     'Accounts' => [
            //         [
            //             'Market value' => [
            //                 'type' => 'Money',
            //                 'currency' => 'ZAR',
            //                 'value' => 126648.33
            //             ],
            //             'Product' => 'The LIFECYCLE Living Annuity',
            //             'Account unique id' => 17621507585,
            //             'Contract unique id' => 17621507585,
            //             'Contract number' => 'POL900705',
            //             'Account number' => 'POL900705',
            //             'Description' => 'The LIFECYCLE Living Annuity',
            //             'Distribution Option' => 'leaveDistributionInCash',
            //             'Status' => [
            //                 'active' => true,
            //                 'identifier' => 'Existing business'
            //             ]
            //         ],
            //         [
            //             'Market value' => [
            //                 'type' => 'Money',
            //                 'currency' => 'ZAR',
            //                 'value' => 598676.38
            //             ],
            //             'Product' => 'LIFECYCLE Voluntary Investment Product',
            //             'Account unique id' => 18978694913,
            //             'Contract unique id' => 18978694913,
            //             'Contract number' => 'VIP800354',
            //             'Account number' => 'VIP800354',
            //             'Description' => 'LIFECYCLE Voluntary Investment Product',
            //             'Distribution Option' => 'leaveDistributionInCash',
            //             'Status' => [
            //                 'active' => true,
            //                 'identifier' => 'Existing business'
            //             ]
            //         ]
            //     ],
            //
            //     'Contracts' => [
            //         [
            //             'Market value' => [
            //                 'type' => 'Money',
            //                 'currency' => 'ZAR',
            //                 'value' => 126648.33
            //             ],
            //             'Product' => 'The LIFECYCLE Living Annuity',
            //             'Account unique id' => 17621507585,
            //             'Contract unique id' => 17621507585,
            //             'Contract number' => 'POL900705',
            //             'Account number' => 'POL900705',
            //             'Description' => 'The LIFECYCLE Living Annuity',
            //             'Distribution Option' => 'leaveDistributionInCash',
            //             'Status' => [
            //                 'active' => true,
            //                 'identifier' => 'Existing business'
            //             ]
            //         ],
            //         [
            //             'Market value' => [
            //                 'type' => 'Money',
            //                 'currency' => 'ZAR',
            //                 'value' => 598676.38
            //             ],
            //             'Product' => 'LIFECYCLE Voluntary Investment Product',
            //             'Account unique id' => 18978694913,
            //             'Contract unique id' => 18978694913,
            //             'Contract number' => 'VIP800354',
            //             'Account number' => 'VIP800354',
            //             'Description' => 'LIFECYCLE Voluntary Investment Product',
            //             'Distribution Option' => 'leaveDistributionInCash',
            //             'Status' => [
            //                 'active' => true,
            //                 'identifier' => 'Existing business'
            //             ]
            //         ]
            //     ],
            //
            //     'Identification number' => '4608310036088'
            // ];

            // exit;

            foreach($adta['Accounts'] as $accounts){
              // print_r($accounts);
              // exit;

              // BA: changed to get acc, rather than first element
              // if($accounts['Account number'] == $acc_no){
              if($accounts['Account number'] == $acc){

                $acc_no = $accounts['Account number']??0;
                $acc_name = $accounts['Product']??'';

                // echo "<pre>";
                // print_r($accounts);
                // // print_r($accounts['Account number']);
                // print_r($acc_no);
                // echo "</pre>";
                // exit;

                $acc_names[] = $accounts['Account number'];
                //BA: using the instrument details now 30/04/2024
                // $pol_list[] = $accounts['Product'];
                // $acc_total[] = (float) $accounts['Market value']['value'];
                $pol_det[$accounts['Account number']] = $accounts['Market value']['value'];
                $per_chg[$accounts['Account number']] = $accounts['Market value']['value'];
                // echo "<br/>";


                //BA: account information details
                unset($a_infy);
                $url = str_replace("[CLIENTID]",$api_client_id,$api_base_url.$api_urls[10]);
                $url = str_replace("[ACNUM]",$accounts['Account number'],$url);

                // unset($i_obj,$idta);
                // $i_obj = new SecureRequest();
                // $i_ret = $c_obj->get($url,$dta);
                // $idta = json_decode($i_ret,true);

                // BA: requested leon to ask cycle to authorise
                $idta = $this->api->get('/api/accounts/' . $accounts['Account number'] . '/holdings');

                // $idta = [
                //     'size' => 2,
                //     'data' => [
                //         [
                //             'Price date' => '2026-02-27',
                //             'Accrued interest' => [
                //                 'type' => 'Money',
                //                 'currency' => 'ZAR',
                //                 'value' => 0.00
                //             ],
                //             'Accrued fees' => [
                //                 'type' => 'Money',
                //                 'currency' => 'ZAR',
                //                 'value' => 0.00
                //             ],
                //             'Instrument grouping' => '-',
                //             'Market Value in Fund Currency' => [
                //                 'type' => 'Money',
                //                 'currency' => 'ZAR',
                //                 'value' => 598529.52
                //             ],
                //             'Contract number' => 'VIP800354',
                //             'Account number' => 'VIP800354',
                //             'Contract id' => 18978694913,
                //             'Units' => [
                //                 'type' => 'Unit',
                //                 'Instrument id' => 16482870785,
                //                 'value' => 1993.923975
                //             ],
                //             'Instrument code' => 'AUPPTY',
                //             'Market Value in System Currency' => [
                //                 'type' => 'Money',
                //                 'currency' => 'ZAR',
                //                 'value' => 598529.52
                //             ],
                //             'Date' => '2026-03-08',
                //             'Client account id' => 18978694657,
                //             'Latest available price' => [
                //                 'type' => 'Price',
                //                 'currency' => 'ZAR',
                //                 'Instrument id' => 16482870785,
                //                 'value' => 300.1767000000
                //             ],
                //             'Instrument id' => 16482870785,
                //             'Instrument account number' => '',
                //             'Instrument name' => 'Alpha Upgrade Fund',
                //             'Withholding tax' => [
                //                 'type' => 'Money',
                //                 'currency' => 'ZAR',
                //                 'value' => 0.00
                //             ]
                //         ],
                //         [
                //             'Price date' => '2026-03-08',
                //             'Accrued interest' => [
                //                 'type' => 'Money',
                //                 'currency' => 'ZAR',
                //                 'value' => 0.00
                //             ],
                //             'Accrued fees' => [
                //                 'type' => 'Money',
                //                 'currency' => 'ZAR',
                //                 'value' => 0.00
                //             ],
                //             'Instrument grouping' => '-',
                //             'Market Value in Fund Currency' => [
                //                 'type' => 'Money',
                //                 'currency' => 'ZAR',
                //                 'value' => 146.86
                //             ],
                //             'Contract number' => 'VIP800354',
                //             'Account number' => 'VIP800354',
                //             'Contract id' => 18978694913,
                //             'Units' => [
                //                 'type' => 'Money',
                //                 'currency' => 'ZAR',
                //                 'value' => 146.86
                //             ],
                //             'Instrument code' => 'ZAR',
                //             'Market Value in System Currency' => [
                //                 'type' => 'Money',
                //                 'currency' => 'ZAR',
                //                 'value' => 146.86
                //             ],
                //             'Date' => '2026-03-08',
                //             'Client account id' => 18978694657,
                //             'Latest available price' => [
                //                 'type' => 'Price',
                //                 'currency' => 'ZAR',
                //                 'Instrument id' => 2936084993,
                //                 'value' => 1.0000000
                //             ],
                //             'Instrument id' => 2936084993,
                //             'Instrument account number' => '',
                //             'Instrument name' => 'Wallet (Transaction Account)',
                //             'Withholding tax' => [
                //                 'type' => 'Money',
                //                 'currency' => 'ZAR',
                //                 'value' => 0.00
                //             ]
                //         ]
                //     ]
                // ];

                // echo $url;
                // echo "<pre>";
                // print_r($idta);
                // echo "</pre>";
                // exit;

                // BA: name check
                unset($cli_cad_insnm);
                foreach($idta['data'] as $each_row){
                  foreach($each_row as $each_idx => $each_ele){
                    if($each_idx == 'Client account id'){
                      $cli_cad_insnm[$each_ele] = ($each_row['Instrument name']??'');
                    }
                  }
                }

                // echo $url;
                // echo "<pre>";
                // print_r($cli_cad_insnm);
                // echo "</pre>";
                // exit;

                // BA: account summary
                unset($url);
                $url = str_replace("[CLIENTID]",$api_client_id,$api_base_url.$api_urls[13]);
                $url = str_replace("[ACNUM]",$accounts['Account number'],$url);

                // echo $url;
                // echo "<br/>";

                // echo $url;
                // exit;

                $hdta = $this->api->get('/api/accounts/' . $accounts['Account number']);

                // echo "<pre>";
                // print_r($hdta);
                // echo "</pre>";
                // exit;

                // unset($h_obj,$hdta);
                // $h_obj = new SecureRequest();
                // $h_ret = $c_obj->get($url,$dta);
                // $hdta = json_decode($h_ret,true);

                // $hdta = [
                //     'Distribution Option' => 'leaveDistributionInCash',
                //
                //     'Communication Options' => [
                //         'communicationPreference' => 'Email',
                //         'transactionNotificationPreferences' => [
                //             'SubmitInvestment' => 'None',
                //             'CompleteUnitTransferOutInstruction' => 'None',
                //             'CompleteWithdrawal' => 'None',
                //             'SuccessfulLogin' => 'None',
                //             'SubmitSwitchInstruction' => 'None',
                //             'CompleteInvestment' => 'None',
                //             'SubmitWithdrawal' => 'None',
                //             'SubmitUnitTransferInInstruction' => 'None',
                //             'SendAutomaticStatement' => 'Email',
                //             'CompleteInternalUnitTransferInstruction' => 'None',
                //             'CompleteReinvestmentSwitchInstruction' => 'None',
                //             'CompleteSwitchInstruction' => 'None',
                //             'SubmitReinvestmentSwitchInstruction' => 'None',
                //             'CompleteUnitTransferInInstruction' => 'None',
                //             'SubmitUnitTransferOutInstruction' => 'None',
                //             'SubmitInternalUnitTransferInstruction' => 'None',
                //             'ApprovedChangeRequest' => 'None'
                //         ],
                //         'automaticallyEmailStatement' => 1
                //     ],
                //
                //     'Beneficiaries' => [],
                //     'LivesAssured' => [],
                //
                //     'Product id' => 19764663553,
                //
                //     'Owners' => [
                //         [
                //             'Entity' => '/api/entities/$api_client_id',
                //             'Entity Id' => $api_client_id,
                //             'Name' => 'Judith Anne Mcleod'
                //         ]
                //     ],
                //
                //     'Contract Number' => 'VIP800354',
                //
                //     'Instructions' => [
                //         [
                //             'Instruction Unique Id' => 5817931777,
                //             'Instruction Id' => '/api/instructions/5817931777',
                //             'Type' => 'Once Off Investment',
                //             'Status' => 'Completed',
                //             'Amount' => 'R 230,000.00'
                //         ],
                //         [
                //             'Instruction Unique Id' => 38745134849,
                //             'Instruction Id' => '/api/instructions/38745134849',
                //             'Type' => 'Recurring Withdrawal',
                //             'Status' => 'Payment cancelled',
                //             'Amount' => 'R 20,000.00'
                //         ]
                //         // (rest of instructions follow same structure)
                //     ],
                //
                //     'Product' => 'LIFECYCLE Voluntary Investment Product',
                //
                //     'Client Accounts' => [
                //         [
                //             'Market value' => [
                //                 'type' => 'Money',
                //                 'currency' => 'ZAR',
                //                 'value' => 598676.38
                //             ],
                //             'Unique Id' => 18978694657,
                //             'Accrued interest' => [
                //                 'type' => 'Money',
                //                 'currency' => 'ZAR',
                //                 'value' => 0.00
                //             ],
                //             'Accrued fees' => [
                //                 'type' => 'Money',
                //                 'currency' => 'ZAR',
                //                 'value' => 0.00
                //             ],
                //             'Model Portfolio Name' => '',
                //             'Name' => 'LifeCycle Voluntary Investment Product',
                //             'Model Portfolio Id' => ''
                //         ]
                //     ],
                //
                //     'Policy Number' => 'VIP800354',
                //     'Unique Id' => 18978694913,
                //
                //     'Documents' => [
                //         [
                //             'Label' => 'CapitalGainsCertificate - February 2024',
                //             'Type' => 'Capital Gains Certificate',
                //             'Document Id' => '/api/documents/60445754625',
                //             'Document Unique Id' => 60445754625
                //         ],
                //         [
                //             'Label' => 'ShareCertificate - October 2024',
                //             'Type' => 'Share Certificate',
                //             'Document Id' => '/api/documents/58519497729',
                //             'Document Unique Id' => 58519497729
                //         ],
                //         [
                //             'Label' => 'CapitalGainsCertificate - February 2025',
                //             'Type' => 'Capital Gains Certificate',
                //             'Document Id' => '/api/documents/65055553793',
                //             'Document Unique Id' => 65055553793
                //         ]
                //     ],
                //
                //     'Description' => 'LIFECYCLE Voluntary Investment Product',
                //
                //     'Status' => [
                //         'active' => true,
                //         'identifier' => 'Existing business'
                //     ],
                //
                //     'Market value' => [
                //         'currency' => 'ZAR',
                //         'amount' => 598676.38,
                //         'date' => '2026-03-08'
                //     ],
                //
                //     'Account Number' => 'VIP800354',
                //
                //     'Advisor' => [
                //         'Brokerage Entity Unique Id' => 7214763009,
                //         'Advisor Name' => 'Mark Howard Weetman',
                //         'Advisor Entity Unique Id' => 8369597697,
                //         'Brokerage Code' => 564,
                //         'Advisor Code' => 5641,
                //         'Brokerage Name' => 'Unum Capital (Pty) Ltd'
                //     ],
                //
                //     'Advisor Mandate' => [
                //         'changeDetails' => 1,
                //         'invest' => 1,
                //         'switch' => 1,
                //         'withdraw' => 1
                //     ]
                // ];

                // name check

                // BA: cycle name
                // echo $url;
                // echo "<pre>";
                // print_r($hdta);
                // echo "</pre>";
                // exit;

                unset($ins_uid_name_arr);
                // BA: client id matches unique id is e.g https://secure.thecycle.co.za/api/accounts/VIP800179
                foreach($hdta['Client Accounts'] as $each_row){
                  $ins_uid_name_arr[$each_row['Unique Id']] = $each_row['Name'];
                }

                // echo "<pre>";
                // print_r($ins_uid_name_arr);
                // echo "</pre>";
                // exit;

                if(isset($idta)){
                  foreach ($idta['data'] as $ikey => $ival) {
                    // echo ($ikey);
                    // echo $ival['Instrument code'];
                    // echo $ival['Unique Id'];

                    // BA: overwrite the name if value exists, as per client requirements
                    // foreach($cli_cad_insnm as $cliId => $insnm){
                    //   // if(isset($ins_uid_name_arr[$cliId])){
                    //     // $ival['Instrument name'] = $insnm." - ".$ins_uid_name_arr[$cliId];
                    //     $ival['Instrument name'] .= " - ".$insnm;
                    //   // }
                    // }

                    //BA: instrument name from
                    // https://secure.thecycle.co.za/api/accounts/POL900501/holdings
                    // to be matched with
                    // https://secure.thecycle.co.za/api/accounts/POL900501
                    // where Client account matches to later Unique Id name element is appended
                    foreach($ins_uid_name_arr as $cliId => $insnm){
                      // if(isset($ins_uid_name_arr[$cliId])){
                        // $ival['Instrument name'] = $insnm." - ".$ins_uid_name_arr[$cliId];
                      $ival['Instrument name'] .= " - ".$insnm;
                      // }
                    }

                    if($ival['Instrument code'] == 'ZAR'){
                      // $transaction_arr['Instrument code'] = $ival['Instrument code'];
                      $transaction_arr['total'][] = (float)$ival['Market Value in System Currency']['value'];
                      // $cumm_arr['total'][] = (float)$ival['Market Value in System Currency']['value'];
                      $cumm_arr[$accounts['Account number']]['Movement'][] = '-';
                    }else{
                      // $holdings_arr['Instrument code'][] = $ival['Instrument code'];
                      $holdings_arr['total'][] = (float)$ival['Market Value in System Currency']['value'];

                      //BA: price movement for the instrument
                      unset($movement);
                      // $movement = self::getData('https://unum.co.za/one/api/factsheet/latestPrice/?dlsCode='.($ival['Instrument code']??'-'),array());
                      $movement = [];
                      $cumm_arr[$accounts['Account number']]['Movement'][] =  self::sARand(round(str_replace(",",".",((float)($movement[5]??0)))*100??0,4),2,2,false).'%';
                      // $cumm_arr['Movement'][] = $movement[3]??0;
                    }
                    $cumm_arr[$accounts['Account number']]['Unit Price'][] = self::sARand(round((float)$ival['Latest available price']['value']??0,4),4,4);
                    $cumm_arr[$accounts['Account number']]['Units'][] = self::sARand(round((float)$ival['Units']['value']??0,4),4,4,false);
                    $cumm_arr[$accounts['Account number']]['Units'][] = self::sARand(round((float)$ival['Units']['value']??0,4),4,4,false);
                    // $cumm_arr[$accounts['Account number']]['Sum'][] = self::sARand(round(trim($ival['Units']['value'])*trim($ival['Latest available price']['value']),2),4,4);
                    // if(Session::get('global_api_tp') != 2){
                      $cumm_arr[$accounts['Account number']]['Sum'][] = round(trim($ival['Units']['value'])*trim($ival['Latest available price']['value']),2);
                      $cumm_arr[$accounts['Account number']]['Sum_formated'][] = self::sARand(($ival['Units']['value'])*trim($ival['Latest available price']['value']));
                    // }
                    $cumm_arr[$accounts['Account number']]['Instrument name'][] = $ival['Instrument name']??'-';
                    $cumm_arr[$accounts['Account number']]['Instrument Code'][] = $ival['Instrument code']??'-';
                  }
                }

                //BA: account summary
                unset($a_infy);
                // $url = str_replace("[CLIENTID]",$api_client_id,$api_base_url.$api_urls[11]);
                // $url = str_replace("[ACNUM]",$accounts['Account number'],$url);
                // unset($s_obj,$sdta);
                // $s_obj = new SecureRequest();
                // $s_ret = $c_obj->get($url,$dta);
                // $sdta = json_decode($s_ret,true);

                $sdta = $this->api->get('/api/accounts/' . $accounts['Account number'] .'/investmentSummarySinceInception');

              //   $sdta = [
              //     'Withdrawals / Transfers Out' => [
              //         'type' => 'Money',
              //         'currency' => 'ZAR',
              //         'value' => -1017587.55
              //     ],
              //
              //     'End date' => '2026-03-08',
              //
              //     'IRR Cumulative' => [
              //         'type' => 'Percentage',
              //         'value' => 113.58
              //     ],
              //
              //     'Start date' => '2022-04-30',
              //
              //     'Closing balance' => [
              //         'type' => 'Money',
              //         'currency' => 'ZAR',
              //         'value' => 598676.38
              //     ],
              //
              //     'Opening balance' => [
              //         'type' => 'Money',
              //         'currency' => 'ZAR',
              //         'value' => 0.00
              //     ],
              //
              //     'Gain/Loss' => [
              //         'type' => 'Money',
              //         'currency' => 'ZAR',
              //         'value' => 531014.61
              //     ],
              //
              //     'Investments / Transfers In' => [
              //         'type' => 'Money',
              //         'currency' => 'ZAR',
              //         'value' => 1085249.32
              //     ],
              //
              //     'IRR Annualised' => [
              //         'type' => 'Percentage',
              //         'value' => 21.74
              //     ]
              // ];

                $irr_a[$accounts['Account number']] = $sdta['IRR Cumulative']['value']??0;
                $irr_cum[$accounts['Account number']] = $sdta['IRR Annualised']['value']??0;
              }
            }

            unset($p_grid,$c_grid);
            if($cumm_arr){
              foreach($cumm_arr as $ckey => $carr){
                foreach ($carr['Sum'] as $skey => $sval) {
                  // echo $sval;
                  // echo "<br/>";
                  if($sval == 0){
                    //BA: Previous holdings

                    // $p_grid[$ckey]['Sum'][] = $sval;
                    //
                    // $pol_list[$carr['Instrument name'][$skey]] = $carr['Instrument name'][$skey];
                    // $acc_total[$carr['Instrument name'][$skey]] = $sval;

                    //BA: the below 3 columns not required as per design

                    // $p_grid[$ckey]['Movement'][] = $carr['Movement'][$skey];
                    $p_grid[$ckey]['Unit Price'][] = self::sARand($carr['Unit Price'][$skey],4,4);
                    $p_grid[$ckey]['Units'][] = $carr['Units'][$skey];
                    $p_grid[$ckey]['Sum_formated'][] = $carr['Sum_formated'][$skey];
                    $p_grid[$ckey]['Instrument name'][] = $carr['Instrument name'][$skey];
                    $p_grid[$ckey]['Instrument Code'][] = $carr['Instrument Code'][$skey];
                  }else{
                    //BA: current holdings

                    // echo "<pre>";
                    // print_r($carr);
                    // echo "</pre>";
                    // exit;

                    $c_grid[$ckey]['Sum'][] = $sval;

                    if($ckey == $acc){
                      $pol_list[] = $carr['Instrument name'][$skey];
                      $acc_total[] = $sval;
                    }
                    $c_grid[$ckey]['Movement'][] = $carr['Movement'][$skey];
                    $c_grid[$ckey]['Unit Price'][] = self::sARand($carr['Unit Price'][$skey],4,4);
                    $c_grid[$ckey]['Units'][] = self::sARand($carr['Units'][$skey],4,4,false);
                    $c_grid[$ckey]['Sum_formated'][] = self::sARand($carr['Sum_formated'][$skey],2,2);
                    $c_grid[$ckey]['Instrument name'][] = $carr['Instrument name'][$skey];
                    $c_grid[$ckey]['Instrument Code'][] = $carr['Instrument Code'][$skey];

                    // BA: check if there are transactions, then show the transaction icon
                    unset($certdta);
                    $url = "https://unum.co.za/one/api/certificates/?dlsEntity=$api_client_id&dlsCode=".$carr['Instrument Code'][$skey];
                    // $certdta = self::getData($url,array());
                    $certdta = '';

                    // echo "<pre>";
                    // print_r($certdta);
                    // echo "</pre>";

                    if(!empty($certdta)){
                      $c_grid[$ckey]['got_trans'][] = 1;
                    }else{
                      $c_grid[$ckey]['got_trans'][] = 0;
                    }

                    // BA: show fact sheet only if exists,
                    // 1. read the external pdf
                    // 2. save it locally
                    // 3. covert pdf to text
                    // 4. read the text file and check for error text
                    $url = "https://unum.co.za/one/api/factsheet/?dlsCode=".$carr['Instrument Code'][$skey];
                    // $sret = self::getPdfDta($url,array());
                    $sret = null;

                    // $file_path = self::write_file($sret,strtolower($carr['Instrument Code'][$skey]));
                    $file_path = '';

                    $command_en = "pdftotext ".$file_path." ".$file_path.".txt";
                    exec($command_en, $output, $return_var);

                    // echo $file_path.".txt";
                    // echo "<br/>";
                    // exit;

                    // $pdftotxt = file_get_contents($file_path.".txt");
                    //
                    // if(strstr($pdftotxt,"err0002")){
                      // BA: not found, only error pdf file returned
                      $c_grid[$ckey]['got_fs'][] = 0;
                    // }else{
                    //   // BA: factsheet present
                    //   $c_grid[$ckey]['got_fs'][] = 1;
                    // }

                    // echo "<pre>";
                    // var_dump($sret);
                    // echo "</pre>";
                    // exit;
                  }
                }
              }
            }

            // echo "<pre>";
            // print_r($c_grid);
            // echo "</pre>";

            // BA: account documents
            // unset($url,$ddta,$c_obj,$ret);
            // $url = str_replace("[POLID]",$acc,$api_base_url.$api_urls[12]);

            // $c_obj = new SecureRequest();
            // $ret = $c_obj->get($url,$dta);
            // $ddta = json_decode($ret,true);

            $ddta = $this->api->get('/api/accounts/' . $accounts['Account number'] .'/documents');

            // echo "<pre>";
            // print_r($ddta);
            // echo "</pre>";
            // exit;

            // $ddta = [
            //     'size' => 3,
            //     'data' => [
            //         [
            //             'Type' => 'Capital Gains Certificate',
            //             'Document id' => '/api/documents/60445754625',
            //             'Document Unique Id' => 60445754625,
            //             'Label' => 'CapitalGainsCertificate - February 2024',
            //             'Date' => '2024/02/29'
            //         ],
            //         [
            //             'Type' => 'Share Certificate',
            //             'Document id' => '/api/documents/58519497729',
            //             'Document Unique Id' => 58519497729,
            //             'Label' => 'ShareCertificate - October 2024',
            //             'Date' => '2024/10/22'
            //         ],
            //         [
            //             'Type' => 'Capital Gains Certificate',
            //             'Document id' => '/api/documents/65055553793',
            //             'Document Unique Id' => 65055553793,
            //             'Label' => 'CapitalGainsCertificate - February 2025',
            //             'Date' => '2025/02/28'
            //         ]
            //     ]
            // ];

            unset($doc_arr);
            if(is_array($ddta['data'])){
              foreach ($ddta['data'] as $dkey => $dval) {
                $doc_arr[] = $dval;
              }
            }

            // echo "<pre>";
            // print_r($doc_arr);
            // echo "</pre>";
            // exit;

            unset($param_array);
            $param_array['broker_cnt'] = 0;
            $param_array['prod_cnt'] = 0;
            $param_array['cdta'] = $cdta;
            $param_array['adta'] = $adta;
            // $param_array['acc_sum'] = array_sum($acc_total);
            $param_array['acc_sum'] = ($pol_det[$acc]??0);
            if(is_array($pol_list)){
              $param_array['pol_list'] = '"'.implode('","',$pol_list).'"';
              $param_array['acc_total'] = '"'.implode('","',$acc_total).'"';
            }else{
              $param_array['pol_list'] = "";
              $param_array['acc_total'] = 0;
            }
            // $param_array['pol_share'] = '"'.implode('","',$pol_share).'"';
            $param_array['three_start'] = date('Y/m/d',strtotime('-3 months'));
            $param_array['three_end'] = date('Y/m/d');
            $param_array['six_start'] = date('Y/m/d',strtotime('-6 months'));
            $param_array['six_end'] = date('Y/m/d');
            $param_array['twl_start'] = date('Y/m/d',strtotime('-12 months'));
            $param_array['twl_end'] = date('Y/m/d');
            $param_array['acc'] = $acc;
            $param_array['acc_name'] = $acc_name;
            $param_array['doc_arr'] = $doc_arr??array();
            $param_array['h_sum'] = self::sARand((array_sum($holdings_arr['total'])??0),2,2);
            if(empty($transaction_arr)){
              $param_array['t_sum'] = self::sARand(0,2,2);
            }else{
              $param_array['t_sum'] = self::sARand((array_sum($transaction_arr['total'])??0),2,2);
            }
            $param_array['irr_a'] = self::sARand(($irr_a[$acc]??0),2,2,false);
            $param_array['irr_cum'] = self::sARand(($irr_cum[$acc]??0),2,2,false);
            // $param_array['cumm_arr'] = $cumm_arr[$acc]??array(); //BA: phased out by p_grid array and c_grid array
            $param_array['p_grid'] = $p_grid[$acc]??array();
            $param_array['c_grid'] = $c_grid[$acc]??array();
            // $param_array['api_tp'] = Session::get('global_api_tp'); // BA: Unum and Prime indicator
            $param_array['api_tp'] = 1; // BA: Unum and Prime indicator

            return view('holdings.list',$param_array);

        } catch (Exception $e) {
          // return abort(500, $e->getMessage());

          echo $e->getMessage();
          exit;
        }
    }

    public function sARand($amt=0,$acc=2,$pad=2,$add_cur=true){
      $amt = trim($amt);
      $amt = preg_replace("/[^0-9|.]/","",$amt);

      if($amt !== ""){
        $amt = round($amt,$acc);
        // $amt = sprintf('%08d', $amt);
        // $amt = str_pad($amt, 4, '0', STR_PAD_RIGHT);
        if($add_cur){
          $amt = 'R '.number_format($amt,$pad,".",",");
        }
        else {
          $amt = number_format($amt,$pad,".","");
        }
      }

      return $amt;
    }

    private function getPdfDta($url, $data, $method = "GET") {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT , 443);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
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
        // print_r($info);
        // echo "</pre>";
        // exit;

        curl_close($ch);
        return $response;
    }
}

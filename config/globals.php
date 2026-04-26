<?php

$api_urls[1] = "api/entities/[CLIENTID]"; //A client’s full details:
$api_urls[2] = "api/entities/[CLIENTID]/documents"; //A client’s documents:
$api_urls[3] = "api/documents/[DOCID]/contents"; //And retrieving a document:
$api_urls[4] = "api/entities/[CLIENTID]/accounts"; //A client’s accounts:
$api_urls[5] = "api/accounts/[ACNUM]/holdings"; //An account’s holdings:
$api_urls[6] = "api/accounts/[ACNUM]/statement?start_date=[STARTDT]&end_date=[ENDDT]"; //An account’s statement:
$api_urls[7] = "api/accounts/statement?search_key=[IDNUM]&start_date=[STARTDT]&end_date=[ENDDT]"; //A client’s combined statement of all accounts:
$api_urls[8] = "api/accounts/[ACNUM]/investmentSummarySinceInception"; //The investment summary since an account’s inception:
$api_urls[9] = "api/accounts/[ACNUM]/instructions"; //Instructions received from the client, per account:
$api_urls[10] = "api/accounts/[ACNUM]/holdings"; //account details, same as array 5 - duplicate
$api_urls[11] = "/api/accounts/[ACNUM]/investmentSummarySinceInception"; //account summary since inception
$api_urls[12] = "api/accounts/[POLID]/documents"; //documents related to a particular account
$api_urls[13] = "api/accounts/[ACNUM]"; //account summary of details of element 10

return [

    /*
    |--------------------------------------------------------------------------
    | Default API Provider
    |--------------------------------------------------------------------------
    | 1 = TEST
    | 2 = LIVE
    |--------------------------------------------------------------------------
    */

    'global_api_tp' => env('GLOBAL_API_TP', 1),
    'global_api_urls' => $api_urls,

];

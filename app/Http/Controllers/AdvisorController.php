<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\SecureRequest;
use App\Models\Advisor;
use App\Models\Investor;
use App\Models\Contract;
use Exception;

class AdvisorController extends Controller
{
    protected SecureRequest $api;

    public function __construct()
    {
        $this->api = new SecureRequest();
    }

    public function sync()
    {
        try {

            $response = $this->api->get('/api/advisors');

            if (!isset($response['data'])) {
                return response()->json([
                    'error' => 'Invalid API response'
                ], 500);
            }

            foreach ($response['data'] as $item) {

                Advisor::updateOrCreate(
                    ['advisor_code' => $item['Advisor Code']],
                    [
                        'advisor_name' => $item['Advisor Name'],
                        'advisor_entity_unique_id' => $item['Advisor Entity Unique Id'],
                        'brokerage_code' => $item['Brokerage Code'],
                        'brokerage_name' => $item['Brokerage Name'],
                        'brokerage_entity_unique_id' => $item['Brokerage Entity Unique Id'],
                    ]
                );
            }

            return response()->json([
                'status' => 'Sync completed successfully'
            ]);

        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function details($advisorCode)
    {
        try {

            $response = $this->api->get("/api/advisors/{$advisorCode}");

            return response()->json($response);

        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function clientDetails($advisorCode)
    {
        try {

            $response = $this->api->get(
                "/api/advisors/{$advisorCode}/clientDetails",
                [
                    'currency' => 'ZAR',
                    'number_of_investors' => 'all'
                ]
            );

            return response()->json($response);

        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function syncAllClients()
  {
      set_time_limit(0);

      try {

          $advisors = Advisor::all();
          $totalInvestors = 0;
          $totalContracts = 0;

          foreach ($advisors as $advisor) {

              $response = $this->api->get(
                  "/api/advisors/{$advisor->advisor_code}/clientDetails",
                  [
                      'currency' => 'ZAR',
                      'number_of_investors' => 'all'
                  ]
              );

              if (!isset($response['Investor Details']) || !is_array($response['Investor Details'])) {
                  continue;
              }

              foreach ($response['Investor Details'] as $investorData) {

                  $entityId = $investorData['Entity Id'] ?? null;

                  if (!$entityId) {
                      continue; // skip malformed investor
                  }

                  $marketValue = null;
                  if (
                      isset($investorData['Market Value In Reporting Currency']) &&
                      is_array($investorData['Market Value In Reporting Currency'])
                  ) {
                      $marketValue = $investorData['Market Value In Reporting Currency']['value'] ?? null;
                  }

                  $investor = Investor::updateOrCreate(
                      ['entity_unique_id' => trim($entityId)],
                      [
                          'advisor_code'  => $advisor->advisor_code,
                          'client_number' => $investorData['Client Number'] ?? null,
                          'investor_name' => $investorData['Investor Name'] ?? null,
                          'market_value'  => $marketValue,
                      ]
                  );

                  $totalInvestors++;

                  if (!isset($investorData['Contracts']) || !is_array($investorData['Contracts'])) {
                      continue;
                  }

                  foreach ($investorData['Contracts'] as $contractData) {

                      $uniqueId = $contractData['Unique Id'] ?? null;

                      if (!$uniqueId) {
                          continue;
                      }

                      $irr = null;
                      if (isset($contractData['IRR Since Inception'])) {
                          if (is_array($contractData['IRR Since Inception'])) {
                              $irr = $contractData['IRR Since Inception']['value'] ?? null;
                          }
                      }

                      $contractMarketValue = null;
                      if (
                          isset($contractData['Market Value In Reporting Currency']) &&
                          is_array($contractData['Market Value In Reporting Currency'])
                      ) {
                          $contractMarketValue = $contractData['Market Value In Reporting Currency']['value'] ?? null;
                      }

                      Contract::updateOrCreate(
                          ['unique_id' => $uniqueId],
                          [
                              'advisor_code'       => $advisor->advisor_code,
                              'investor_entity_id' => $investor->entity_unique_id,
                              'contract_number'    => $contractData['Contract Number'] ?? null,
                              'account_number'     => $contractData['Account Number'] ?? null,
                              'account_description'=> $contractData['Account Description'] ?? null,
                              'market_value'       => $contractMarketValue,
                              'irr'                => $irr,
                          ]
                      );

                      $totalContracts++;
                  }
              }
          }

          return response()->json([
              'status' => 'Full sync completed',
              'investors_processed' => $totalInvestors,
              'contracts_processed' => $totalContracts
          ]);

      } catch (\Exception $e) {

          return response()->json([
              'error' => $e->getMessage()
          ], 500);
      }
  }


  public function feeReport($advisorCode)
  {
      try {

          $response = $this->api->get(
              "/api/advisors/{$advisorCode}/fee_report",
              [
                  'from_date' => '2025/01/01',
                  'to_date'   => '2025/12/31',
              ]
          );

          return response()->json($response);

      } catch (\Exception $e) {

          return response()->json([
              'error' => $e->getMessage()
          ], 500);
      }
  }

  public function brokerageClients($brokerageCode)
  {
      // set_time_limit(0);

      try {

          $response = $this->api->get(
              "/api/advisors/brokerages/{$brokerageCode}/clientDetails",
              [
                  'currency' => 'ZAR',
                  'number_of_investors' => 'all'
              ]
          );

          return response()->json($response);

      } catch (\Exception $e) {

          return response()->json([
              'error' => $e->getMessage()
          ], 500);
      }
  }

  public function brokerageFeeReport($brokerageCode)
  {
      try {

          $response = $this->api->get(
              "/api/advisors/brokerages/{$brokerageCode}/fee_report",
              [
                  'from_date' => '2025/01/01',
                  'to_date'   => '2025/12/31',
              ]
          );

          return response()->json($response);

      } catch (\Exception $e) {

          return response()->json([
              'error' => $e->getMessage()
          ], 500);
      }
  }

  public function brokerageFeeStatement($brokerageCode)
  {
      try {

          $response = $this->api->get(
              "/api/advisors/brokerages/{$brokerageCode}/fee_statement",
              [
                  'start_date'   => '2026-01-01',
                  'end_date'     => '2026-02-20',
                  'add_password' => 'false'
              ],
              false
          );

          return response($response)
              ->header('Content-Type', 'application/pdf');

      } catch (\Exception $e) {

          return response()->json([
              'error' => $e->getMessage()
          ], 500);
      }
  }



}

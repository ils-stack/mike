<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advisor;
use App\Models\Investor;
use App\Models\Contract;

class DashboardController extends Controller
{
    public function home(Request $request)
    {
      if($request->advisor_id){
          session(['advisor_id' => $request->advisor_id]);

          //unset investor
          session(['investor_id' => ""]);
      }

      // echo session('advisor_id');
      // exit;

      if($request->investor_id){
        session(['investor_id' => $request->investor_id]);

        // $investor = Investor::find($request->investor_id);
        //
        // if($investor){
        //     $advisor = Advisor::where('advisor_code', $investor->advisor_code)->first();
        //
        //     if($advisor){
        //         session(['advisor_id' => $advisor->id]);
        //     }
        // }
      }

      // echo session('investor_id');
      // exit;

      // BA: all advisors
      $advisors = Advisor::orderBy('advisor_name','asc')->get();

      $investors = collect();

      if(session('advisor_id')){
        $advisor = Advisor::find(session('advisor_id'));

        if($advisor){
            $investors = Investor::where('advisor_code', $advisor->advisor_code)
                ->orderBy('investor_name','asc')
                ->get();
        }
      }

      $contracts = collect();

      if(session('investor_id')){

          $investor = Investor::find(session('investor_id'));

          if($investor){
              $contracts = Contract::where(
                  'investor_entity_id',
                  $investor->entity_unique_id
              )->get();
          }

      }

      $chartData = $contracts
          ->groupBy('account_description')
          ->map(function($rows){
              return $rows->sum('market_value');
          });

        return view(
            'dashboard.home',
            compact(
                'advisors',
                'investors',
                'contracts',
                'chartData'
            )
        );
    }
}

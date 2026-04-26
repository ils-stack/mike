<div class = "row">
  <div class="col-md-5 p-2 m-1 mb-1" style = "text-align:center;">
    <div class="p-2 m-2 bg-primary text-white" style = "text-align:center;">Income Summary</div>

    <div class="row">
      <div class="col-md-5 mb-1" align = "right">&nbsp;</div>
      <div class="col-md-3 mb-1" align = "right">Present Value</div>
      <div class="col-md-3 mb-1" align = "right">Future Value</div>
    </div>

    <div class="row">
      <div class="col-md-5 mb-1" align = "right">Monthly income available</div>
      <div class="col-md-3 mb-1">
        <div class="form-outline">
          <input type="text" id="init_income_summ_pv" name="init_income_summ_pv" class="form-control" value = ""/>
          <label class="form-label" for="form12">&nbsp;</label>
        </div>
      </div>
      <div class="col-md-3 mb-1">
        <div class="form-outline">
          <input type="text" id="init_income_summ" name="init_income_summ" class="form-control" value = ""/>
          <label class="form-label" for="form12">&nbsp;</label>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5 mb-1" align = "right">Monthly income required</div>
      <div class="col-md-3 mb-1">
        <div class="form-outline">
          <input type="text" id="init_exp_summ_pv" name="init_exp_summ_pv" class="form-control" value = ""/>
          <label class="form-label" for="form12">&nbsp;</label>
        </div>
      </div>
      <div class="col-md-3 mb-1">
        <div class="form-outline">
          <input type="text" id="init_exp_summ" name="init_exp_summ" class="form-control" value = ""/>
          <label class="form-label" for="form12">&nbsp;</label>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5 mb-1" align = "right">Monthly shortfall / Excess</div>
      <div class="col-md-3 mb-1">
        <div class="form-outline">
          <input type="text" id="monthly_shortfall_pv" name="monthly_shortfall_pv" class="form-control" value = ""/>
          <label class="form-label" for="form12">&nbsp;</label>
        </div>
      </div>
      <div class="col-md-3 mb-1">
        <div class="form-outline">
          <input type="text" id="monthly_shortfall" name="monthly_shortfall" class="form-control" value = ""/>
          <label class="form-label" for="form12">&nbsp;</label>
        </div>
      </div>
    </div>

    <div class="p-2 m-2 bg-primary text-white" style = "text-align:center;">Capital Summary</div>

    <div class="row">
      <div class="col-md-8 mb-1" align = "right">Capital available at Retirement date</div>
      <div class="col-md-3 mb-1">
        <div class="form-outline">
          <input type="text" id="init_avail_cash" name="init_avail_cash" class="form-control" value = ""/>
          <label class="form-label" for="form12">&nbsp;</label>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-8 mb-1" align = "right">Capital Liabilities and expenses at Retirement date</div>
      <div class="col-md-3 mb-1 m">
        <div class="form-outline">
          <input type="text" id="init_avail_liability" name="init_avail_liability" class="form-control" value = ""/>
          <label class="form-label" for="form12">&nbsp;</label>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-8 mb-1" align = "right">Shortfall/ Excess at Retirement date</div>
      <div class="col-md-3 mb-1">
        <div class="form-outline">
          <input type="text" id="init_diff_rt" name="init_diff_rt" class="form-control" value = ""/>
          <label class="form-label" for="form12">&nbsp;</label>
        </div>
      </div>
    </div>

    <div class="p-2 m-2 bg-primary text-white" style = "text-align:center;">Assessment</div>

    <div class="row">
      <div class="col-md-7 mb-1" align = "right">Funds available will provide an income for</div>
      <div class="col-md-4 mb-1">
        <div class="form-outline">
          <input type="text" id="req_years_div" name="req_years_div" class="form-control" value = ""/>
          <label class="form-label" for="form12">&nbsp;</label>
        </div>
      </div>

      <div class="col-md-7 mb-1" align = "right">Estimated Capital Required at Retirement Date</div>
      <div class="col-md-4 mb-1 m">
        <div class="form-outline">
          <input type="text" id="fund_deficit" name="fund_deficit" class="form-control" value = ""/>
          <label class="form-label" for="form12">&nbsp;</label>
        </div>
      </div>
    </div>

  </div>

  <div class="col-md-6 p-2 m-1 mb-1" style = "text-align:left;">
    <div class="p-2 m-1 mb-1 bg-primary text-white" style = "text-align:center;">Assumptions</div>
    <ul class="notesul" style="padding-left:25px;">
      <li><b>Assumption #1:</b> If Your spouse works longer than your retirement date her salary will increase by inflation</li>
      <li><b>Assumption #2:</b>  You have estimated your families retirement income needs fairly accurately</li>
      <li><b>Assumption #3:</b> You have calculated the tax requirement accurately</li>
      <li><b>Assumption #4:</b> You realise that this is an estimation - You have to base the term on your own life expectancy assumptions</li>
      <li><b>Assumption #5:</b> The possible sale of assets is based on Future Value of assest at inflation only</li>
      <li><b>Assumption #6:</b> Manually calculate any possible liability at retirement age</li>
      <li><b>Assumption #7:</b> Investment includes bank endowment ETF Unit Trust etc</li>
      <li><b>Assumption #8:</b> Retirement Funds include Pension Provident Retirement Annuity etc</li>
      <li><b>Assumption #9:</b> Future Value of capital available is based on the generic growth rate/ term to retirement and annual automatic premium increases</li>
      <li><b>Assumption #10:</b> Future Values of investments and Retirement funds assume retirement date and ignore actual maturity dates</li>
      <li><b>Assumption #11:</b> Property rental income will be available for the full term of your life expectancy</li>
    </ul>
  </div>

</div>

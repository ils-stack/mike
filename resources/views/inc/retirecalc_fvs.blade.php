<div class="col-md-5 mb-3" align = "right">How much do you have accumulated in your investments Present Value</div>
<div class="col-md-3 mb-3">
  <div class="form-outline">
    <input type="text" id="invest_pv" name="invest_pv" class="form-control curr bg-warning" value = "" onchange="calc_fv_one();" autocomplete="off" />
    <!-- <label class="form-label" for="invest_pv">What is the present fund value of your investments</label> -->
  </div>
</div>

<div class="col-md-5 mb-3" align = "right">What are your monthly contributions to Investments</div>
<div class="col-md-3 mb-3">
  <div class="form-outline">
    <input type="text" id="monthly_cont" name="monthly_cont" class="form-control curr bg-warning" value = "" autocomplete="off" />
    <!-- <label class="form-label" for="form12">Monthly Premiums</label> -->
  </div>
</div>

<div class="col-md-5 mb-3" align = "right">What if you escalated contributions annually to retirment age, at this percentage rate</div>
<div class="col-md-3 mb-3">
  <div class="form-outline">
    <input type="text" id="es_monthly_cont" name="es_monthly_cont" class="form-control bg-warning" value = "" autocomplete="off" />
    <!-- <label class="form-label" for="es_monthly_cont">What is the present fund value of your retirement annuities pension funds etc</label> -->
  </div>
</div>
<div class="col-md-3 mb-3">
  <div class="form-outline">
    <input type="text" id="fv_monthly_cont" name="fv_monthly_cont" class="form-control curr bg-warning" value = "" autocomplete="off" />
    <!-- <label class="form-label" for="fv_monthly_cont">Future Value at your retirement age</label> -->
  </div>
</div>

<hr class="my-5" style="margin:1rem 0rem !important;">

<div class="col-md-5 mb-3" align = "right">How much do you have accumulated in your retirement funds Present Value</div>
<div class="col-md-3 mb-3">
  <div class="form-outline">
    <input type="text" id="retire_fv" name="retire_fv" class="form-control curr bg-warning" onchange = "calc_fv_two();"  autocomplete="off" value = ""/>
    <!-- <label class="form-label" for="retire_fv">What is the present fund value of your retirement annuities pension funds etc</label> -->
  </div>
</div>

<div class="col-md-5 mb-3" align = "right">What are your monthly contributions to Retirement</div>
<div class="col-md-3 mb-3">
  <div class="form-outline">
    <input type="text" id="retire_fund" name="retire_fund" class="form-control bg-warning" value = ""/>
    <!-- <label class="form-label" for="retire_fund">What if you escalated contributions annually to retirement age, at this percentage rate</label> -->
  </div>
</div>

<div class="col-md-5 mb-3" align = "right">What if you escalated contributions annually to retirement age, at this percentage rate</div>
<div class="col-md-3 mb-3">
  <div class="form-outline">
    <input type="text" id="es_capital" name="es_capital" class="form-control bg-warning" value = ""/>
    <!-- <label class="form-label" for="es_capital">What is the present fund value of your retirement annuities pension funds etc</label> -->
  </div>
</div>
<div class="col-md-3 mb-3">
  <div class="form-outline">
    <input type="text" id="fv_capital" name="fv_capital" class="form-control curr bg-warning" value = ""/>
    <!-- <label class="form-label" for="fv_capital">Future Value at your retirement age</label> -->
  </div>
</div>

<hr class="my-5" style="margin:1rem 0rem !important;">

<div class="col-md-5 mb-3" align = "right">How much does your spouse have in accumulated instruments Present Value</div>
<div class="col-md-3 mb-3">
  <div class="form-outline">
    <input type="text" id="sp_invest_val" name="sp_invest_val" class="form-control curr bg-warning" onchange = "calc_fv_three();"  autocomplete="off" value = ""/>
    <!-- <label class="form-label" for="form12">The present fund value should be used for this estimation</label> -->
  </div>
</div>

<div class="col-md-5 mb-3" align = "right">What are your spouses monthly contributions to Investments</div>
<div class="col-md-3 mb-3">
  <div class="form-outline">
    <input type="text" id="sp_monthly_cont" name="sp_monthly_cont" class="form-control curr bg-warning" value = ""/>
    <!-- <label class="form-label" for="form12">Monthly Premiums</label> -->
  </div>
</div>

<div class="col-md-5 mb-3" align = "right">What if you escalated contributions annually to retirement age, at this percentage rate</div>
<div class="col-md-3 mb-3">
  <div class="form-outline">
    <input type="text" id="sp_es_capital" name="sp_es_capital" class="form-control bg-warning" value = ""/>
    <!-- <label class="form-label" for="form12"></label> -->
  </div>
</div>
<div class="col-md-3 mb-3">
  <div class="form-outline">
    <input type="text" id="fv_es_capital" name="fv_es_capital" class="form-control curr bg-warning" value = ""/>
    <!-- <label class="form-label" for="form12">Future Value at your retirement age</label> -->
  </div>
</div>

<hr class="my-5" style="margin:1rem 0rem !important;">

<div class="col-md-5 mb-3" align = "right">How much does your spouse have in accumulated retirement funding Present Value</div>
<div class="col-md-3 mb-3">
  <div class="form-outline">
    <input type="text" id="sp_fund_pv" name="sp_fund_pv" class="form-control curr bg-warning" onchange = "calc_fv_four();"  autocomplete="off" value = ""/>
    <!-- <label class="form-label" for="form12">The present fund fund value should be used for this estimation</label> -->
  </div>
</div>

<div class="col-md-5 mb-3" align = "right">What are your spouses monthly contributions to Investments</div>
<div class="col-md-3 mb-3">
  <div class="form-outline">
    <input type="text" id="sp_monthly_inv" name="sp_monthly_inv" class="form-control curr bg-warning" value = ""/>
    <!-- <label class="form-label" for="form12">Monthly Premiums</label> -->
  </div>
</div>

<div class="col-md-5 mb-3" align = "right">What if you escalated contributions annually to retirement age, at this percentage rate</div>
<div class="col-md-3 mb-3">
  <div class="form-outline">
    <input type="text" id="es_sp_inv" name="es_sp_inv" class="form-control bg-warning" value = ""/>
    <!-- <label class="form-label" for="form12"></label> -->
  </div>
</div>
<div class="col-md-3 mb-3">
  <div class="form-outline">
    <input type="text" id="fv_sp_inv" name="fv_sp_inv" class="form-control curr bg-warning" value = ""/>
    <!-- <label class="form-label" for="form12">Future Value at your retirement age</label> -->
  </div>
</div>

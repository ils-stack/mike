<script>
var req_cap_year = 0;
var req_deficit = 0;
var init_capital = 0;
var init_expense = 0;
var m_income = 0;
var m_expense = 0;
var temp_user = 999999999999998; //dummmy user

String.prototype.parseCurrency = function(){
	// var f_data = this.replace(/[a-z]/gi,'');
	var f_data = this.replace(/[^0-9]/gi,'');
	f_data = f_data.replace(/_/gi,' ').trim();

	f_data = isNaN(parseFloat(f_data))?0:parseFloat(f_data);

	return f_data;
};

function numberWithCommas(x) {
    x = x.toString();
    var pattern = /(-?\d+)(\d{3})/;
    while (pattern.test(x))
        x = x.replace(pattern, "$1,$2");
    return x;
}

$(document).ready(function(){
	$('input[type=text]').keyup(function (){
		// $("#calculate").css('backgroundColor','#ff0000');
		// $("#calculate_1").css('backgroundColor','#ff0000');
	});
	$("input[type=text]").click(function() {
		if(!$(this).hasClass("selected")){
			// $(this).select();
			$(this).addClass("selected");
		}
	});
	$("input[type=text]").blur(function() {
		if($(this).hasClass("selected")) {
			$(this).removeClass("selected");
		}

		if($(this).hasClass("curr")){
			var f_data = $(this).val().parseCurrency();
			$(this).val("R "+numberWithCommas(f_data));

			$(this).attr('value',"R "+numberWithCommas(f_data));
		}

		if($(this).hasClass("per")){
			var f_data = $(this).val().parseCurrency();
			$(this).val(numberWithCommas(f_data)+"%");

			$(this).attr('value',numberWithCommas(f_data)+"%");
		}
	});
 });

 function do_calc(){
	var inc_t = (isNaN(parseFloat($("#income_req_period").val()))?0:parseFloat($("#income_req_period").val()));
	if(inc_t <= 0){
		$("#income_req_period").focus();
		$("#income_req_period").css('background-color','#ff0000');
		return false;
	}else{
		$("#income_req_period").css('background-color','#fff');
	}

  var formData = new FormData();

	formData.append('recId', temp_user);
	formData.append('clientId',  temp_user);
	formData.append('growth_rate', $("#growth_rate").val());
	formData.append('infrate', $("#infrate").val());
	formData.append('ins_policy', $("#ins_policy").val().parseCurrency());
	formData.append('wait_period', $("#wait_period").val());
	formData.append('annual_inc', $("#annual_inc").val().parseCurrency());
	formData.append('present_age', $("#present_age").val());
	formData.append('cease_age', $("#cease_age").val());
	formData.append('other_inc', $("#other_inc").val().parseCurrency());
	formData.append('spouse_inc', $("#spouse_inc").val().parseCurrency());
	formData.append('spouse_retire', $("#spouse_retire").val());
	formData.append('home_cash', $("#home_cash").val().parseCurrency());
	formData.append('tax_val', $("#tax_val").val().parseCurrency());
	formData.append('income_req_period', $("#income_req_period").val());
	formData.append('sell_cash', $("#sell_cash").val().parseCurrency());
	formData.append('p_liabilities', $("#p_liabilities").val().parseCurrency());
	formData.append('other_modification', $("#other_modification").val().parseCurrency());
	formData.append('dis_cover', $("#dis_cover").val().parseCurrency());
	formData.append('invest_val', $("#invest_val").val().parseCurrency());
	formData.append('retire_fund', $("#retire_fund").val().parseCurrency());
	formData.append('sp_invest_val', $("#sp_invest_val").val().parseCurrency());
	formData.append('sp_retire_fund', $("#sp_retire_fund").val().parseCurrency());

 	$.ajax({
 		 type:'POST',
 		 url:"{{ route('ajaxRequestDis.post') }}",
 		 data: formData,
 		 cache: false,
 		 contentType: false,
 		 processData: false,
 		 success:function(data){
 			console.log(data);

 	 		update_totals_to_summary(data);
 		 }
 	});

 }

 function update_totals_to_summary(data){
	 cash_flow_grid_dis();

		var req_cap_year = parseFloat(data['req_term'].toString());
		var req_deficit = data['deficit'].toString();
		var m_income = data['m_income'].toString();
		var m_expense = data['m_expense'].toString();
		var init_capital = data['init_capital'].toString();
		var init_expense = data['init_expense'].toString();

		//total initial income - A
		var initInc = (isNaN(parseFloat(m_income))?0:parseFloat(m_income));
		$("#init_income_summ").val("R "+numberWithCommas(initInc));

		//total initial expense - B
		var initExp = (isNaN(parseFloat(m_expense))?0:parseFloat(m_expense));
		$("#init_exp_summ").val("R "+numberWithCommas(initExp));

		//monthly shortfall/ Excess - A-B
		$("#monthly_shortfall").val("R "+numberWithCommas((initInc-initExp)));

		//avail cash - C
		var totAccess = (isNaN(init_capital)?0:init_capital);
		$("#init_avail_cash").val("R "+numberWithCommas(totAccess));

		//avail liabilites - D
		var totLiability = (isNaN(init_expense)?0:init_expense);
		$("#init_avail_liability").val("R "+numberWithCommas(totLiability));

		//Initial shortfall/ Excess - C-D
		$("#init_diff").val("R "+numberWithCommas((totAccess-totLiability)));

		//required term, year before the capital becomes negative
		$("#req_years_div").val(req_cap_year);

		//required term, year before the capital becomes negative
		$("#fund_deficit").val("R "+numberWithCommas(req_deficit));
	}

	function cash_flow_grid_dis(){
		resetCFtable();

		const columns = [
			{ label: 'Spouse Age', field: 'age' },
			{ label: 'Term', field: 'term' },
			{ label: 'Capital', field: 'capital' },
			{ label: 'Income', field: 'income' },
			{ label: 'Req Budget', field: 'req_budget' },
		];

		const asyncTable = new mdb.Datatable(
			document.getElementById('datatable'),
			{ columns, },
			{ loading: true }
		);

		//BA: comments re ... operator
		// https://jsonplaceholder.typicode.com/users
		//let array = [...value]
		// The spread syntax is a new addition to the set of operators in JavaScript ES6.
		// It takes in an iterable (e.g an array) and expands it into individual elements

		fetch('{{ route('ajaxCashFlowDis.post') }}')
			.then((response) => response.json())
			.then((data) => {
			asyncTable.update(
				{
				rows: data.map((ajxData) => ({
					...ajxData,
					// address: `${user.address.city}, ${user.address.street}`,
					// company: user.company.name,
				})),
				},
			{ loading: false }
			);
		});
	}

	function resetCFtable(){
		$("#datatable").html("");
	}

 $( document ).ready(function() {
 	$( ".calc-btn" ).click(function() {
 		do_calc();
 	});
 });
</script>
<div>
  <div class="row">
    <div class="col-md-12 mb-3">
      <h2 align = "center" class = "m-3"> Disability Estimator</h2>
    </div>

    <div class="col-md-11 mb-3" align = "right">
      <button type="button" class="btn calc-btn btn-primary">Calculate</button>
    </div>

    <div class="col-md-5 mb-3" align = "right">Expected Investment Growth rate</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="growth_rate" name="growth_rate" class="form-control" value = "10" autocomplete="off" />
        <!-- <label class="form-label" for="growth_rate">In Your Experience</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">Inflation Rate</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="infrate" name="infrate" class="form-control" value = "8" autocomplete="off" />
        <!-- <label class="form-label" for="infrate">In Your Experience</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">What is the initial income from your income protector insurance policy</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="ins_policy" name="ins_policy" class="form-control curr" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="ins_policy">Spouse Gross Salary</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">What is the waiting period in months</div>
    <div class="col-md-6 mb-3">
      <!-- <div class="form-outline"> -->
        <select class="form-control select" id="wait_period" name="wait_period">
          <option value="1">1 month</option>
          <option value="2">3 months</option>
          <option value="3">12 months</option>
          <option value="4">24 months</option>
        </select>
        <!-- <label class="form-label" for="wait_period1">Normally this would be the term to retirement age</label> -->
      <!-- </div> -->
    </div>

    <div class="col-md-5 mb-3" align = "right">By how much will it increase annually</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="annual_inc" name="annual_inc" class="form-control curr" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="annual_inc">Our full Assessment tool will remind you of hidden monthly costs</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">Present age</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="present_age" name="present_age" class="form-control" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="present_age">Advised to be considered</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">At what age will it cease</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="cease_age" name="cease_age" class="form-control" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="cease_age">Spouse age</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">Property rental/ other income</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="other_inc" name="other_inc" class="form-control curr" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="other_inc">Property Vehicles furniture collectables time share</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">What much does you spouse earn monthly</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="spouse_inc" name="spouse_inc" class="form-control curr" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="spouse_inc">Sale of vehicles, property or collectables to generate extra income</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">How many years will your spouse continue to work</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="spouse_retire" name="spouse_retire" class="form-control" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="spouse_retire">3.99% vat inclusive - of the Estate Assets handled by your Executor</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">What much cash does your family require as take home monthly</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="home_cash" name="home_cash" class="form-control curr" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="home_cash">What do you owe</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">Use an SA Tax Calculator for the correct Tax Figure</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="tax_val" name="tax_val" class="form-control curr" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="tax_val">Life Cover on your life payable for your families use</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">For how long in years, will your family require the income</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="income_req_period" name="income_req_period" class="form-control" value = "10" autocomplete="off" />
        <!-- <label class="form-label" for="income_req_period">What is the present fund value of your investments Savings Account Endownments etc</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">Would you sell any asset and turn it into cash</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="sell_cash" name="sell_cash" class="form-control curr" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="sell_cash">What is the present fund value of your retirement annuities pension funds etc</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">What liabilities would you want to pay off- Bond, Vehicle Loans, Credit Card etc</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="p_liabilities" name="p_liabilities" class="form-control curr" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="p_liabilities">Vehicle and Home Modification and other disability appliance cost</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">Vehicle and Home Modification and other disability appliance cost</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="other_modification" name="other_modification" class="form-control curr" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="other_modification">Extra Contingency Planning</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">How much capital disability cover do you have</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="dis_cover" name="dis_cover" class="form-control curr" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="dis_cover">Lump Sum Disability Cover on your life payable for your families use</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">How much do you have accumulated in your investments</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="invest_val" name="invest_val" class="form-control curr" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="invest_val">What is the present fund value of your retirement annuities pension funds etc</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">How much do you have accumulated in your retirement funds</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="retire_fund" name="retire_fund" class="form-control curr" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="retire_fund">How much does your spouse have in accumulated retirement funding</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">How much does your spouse have in accumulated instruments</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="sp_invest_val" name="sp_invest_val" class="form-control curr" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="sp_invest_val">How much does your spouse have in accumulated retirement funding</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">How much does your spouse have in accumulated retirement funding</div>
    <div class="col-md-6 mb-3">
      <div class="form-outline">
        <input type="text" id="sp_retire_fund" name="sp_retire_fund" class="form-control curr" value = "" autocomplete="off" />
        <!-- <label class="form-label" for="sp_retire_fund">The present fund fund value should be used for this estimation</label> -->
      </div>
    </div>

    <div class="col-md-11 mb-3" align = "right">
      <button type="button" class="btn calc-btn btn-primary">Calculate</button>
    </div>

    <div class = "row">
      <div class="col-md-5 p-2 m-1 mb-1" style = "text-align:center;">
        <div class="p-2 m-2 bg-primary text-white" style = "text-align:center;">Income Summary</div>

        <div class="row">
          <div class="col-md-8 mb-1" align = "right">Monthly income available</div>
          <div class="col-md-4 mb-1">
            <div class="form-outline">
              <input type="text" id="init_income_summ" name="init_income_summ" class="form-control" value = ""/>
              <!-- <label class="form-label" for="form12">&nbsp;</label> -->
            </div>
          </div>

          <div class="col-md-8 mb-1" align = "right">Monthly income required</div>
          <div class="col-md-4 mb-1 m">
            <div class="form-outline">
              <input type="text" id="init_exp_summ" name="init_exp_summ" class="form-control" value = ""/>
              <!-- <label class="form-label" for="form12">&nbsp;</label> -->
            </div>
          </div>

          <div class="col-md-8 mb-1" align = "right">Monthly shortfall/ Excess</div>
          <div class="col-md-4 mb-1">
            <div class="form-outline">
              <input type="text" id="monthly_shortfall" name="monthly_shortfall" class="form-control" value = ""/>
              <!-- <label class="form-label" for="form12">&nbsp;</label> -->
            </div>
          </div>
        </div>

        <div class="p-2 m-2 bg-primary text-white" style = "text-align:center;">Capital Summary</div>

        <div class="row">
          <div class="col-md-8 mb-1" align = "right">Initial available cash</div>
          <div class="col-md-4 mb-1">
            <div class="form-outline">
              <input type="text" id="init_avail_cash" name="init_avail_cash" class="form-control" value = ""/>
              <!-- <label class="form-label" for="form12">&nbsp;</label> -->
            </div>
          </div>

          <div class="col-md-8 mb-1" align = "right">Initial liabilities and expenses</div>
          <div class="col-md-4 mb-1 m">
            <div class="form-outline">
              <input type="text" id="init_avail_liability" name="init_avail_liability" class="form-control" value = ""/>
              <!-- <label class="form-label" for="form12">&nbsp;</label> -->
            </div>
          </div>

          <div class="col-md-8 mb-1" align = "right">Initial shortfall/ Excess</div>
          <div class="col-md-4 mb-1">
            <div class="form-outline">
              <input type="text" id="init_diff" name="init_diff" class="form-control" value = ""/>
              <!-- <label class="form-label" for="form12">&nbsp;</label> -->
            </div>
          </div>
        </div>

        <div class="p-2 m-2 bg-primary text-white" style = "text-align:center;">Assessment</div>

        <div class="row">
          <div class="col-md-8 mb-1" align = "right">Funds available will provide an income for</div>
          <div class="col-md-4 mb-1">
            <div class="form-outline">
              <input type="text" id="req_years_div" name="req_years_div" class="form-control" value = ""/>
              <!-- <label class="form-label" for="form12">&nbsp;</label> -->
            </div>
          </div>

          <div class="col-md-8 mb-1" align = "right">Additional cover required</div>
          <div class="col-md-4 mb-1 m">
            <div class="form-outline">
              <input type="text" id="fund_deficit" name="fund_deficit" class="form-control" value = ""/>
              <!-- <label class="form-label" for="form12">&nbsp;</label> -->
            </div>
          </div>
        </div>

      </div>

      <div class="col-md-6 p-2 m-1 mb-1" style = "text-align:left;">
        <div class="p-2 m-1 mb-1 bg-primary text-white" style = "text-align:center;">Assumptions</div>
        <ul class="notesul">
          <li>Assumption #1 : Your spouse salary will increase by inflation</li>
          <li>Assumption #2 : You have estimated your family’s income needs fairly accurately</li>
          <li>Assumption #3 : You have calculated the tax requirement accurately</li>
          <li>Assumption #4 : You realise that this is an estimation - and does not take children or dependants term of requirement into account</li>
          <li>Assumption #5 : You have taken ALL your hard assets into consideration</li>
          <li>Assumption #6 : You have added up every cent that your estate could be liable for (no nasty surprises) Including Executor's Fees</li>
          <li>Assumption #7 : You are sure that your life cover is active and not payable to a third party</li>
          <li>Assumption #8 : The fund values for your investments are up to date</li>
          <li>Assumption #9 : Your retirement instruments are divorce decree proof</li>
          <li>Assumption #10 : You understand the rules of group life cover you may have</li>
        </ul>
      </div>

      </div>
    </div>
    <div class="col-md-12 m-2 p-2 bg-primary text-white">
      <h4 align = "center">Cash Flow</h4>
    </div>
  </div>
  <div>
    <div id="datatable">

    </div>
  </div>
</div>

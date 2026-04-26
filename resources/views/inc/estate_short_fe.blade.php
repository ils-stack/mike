<script>
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

// function numberWithCommas(x) {
//     x = x.toString();
//     var pattern = /(-?\d+)(\d{3})/;
//     while (pattern.test(x))
//         x = x.replace(pattern, "$1,$2");
//     return x;
// }

var req_cap_year = 0;
var req_deficit = 0;
var init_capital = 0;
var init_expense = 0;
var m_income = 0;
var m_expense = 0;

$( document ).ready(function() {
  $( ".calc-btn" ).click(function() {
    do_calc();
      // $.ajax({
      //    type:'POST',
      //    url:"{{ route('ajaxRequest.post') }}",
      //    success:function(data){
      //       alert(data.success);
      //    }
      // });
  });

  function do_calc(){
    // alert(1);
  	inc_t = (isNaN(parseFloat($("#term").val()))?0:parseFloat($("#term").val()));
  	if(inc_t <= 0){
  		$("#term").focus();
  		$("#term").addClass('bg-danger');
  		return false;
  	}else{
  		$("#term").removeClass('bg-danger');
  	}

  	// $("#calculate").css('backgroundColor','');
  	// $("#calculate_1").css('backgroundColor','');

  	// smooth_scroll('summtitle');

  	var temp_user = 999999999999999; //dummmy user
  	init_capital = 0;

  	init_capital += $("#cashable_amt").val().parseCurrency();
  	init_capital += $("#l_cover").val().parseCurrency();
  	init_capital += $("#investment").val().parseCurrency();
  	init_capital += $("#retire_fund").val().parseCurrency();

  	init_expense = 0;
  	init_expense = $("#laibilities").val().parseCurrency();
  	init_expense += $("#ex_fees").val().parseCurrency();

  	m_income = 0;
  	m_income += $("#s_income").val().parseCurrency();

  	m_expense = 0;
  	m_expense += $("#s_expense").val().parseCurrency();
  	m_expense += $("#s_tax").val().parseCurrency();

    var form_data = {};

    // form_data['init_capital'] = init_capital;
    // form_data['init_expense'] = init_expense;
    // form_data['m_income'] = m_income;
    // form_data['m_expense'] = m_expense;

    form_data['recId'] = temp_user;
  	form_data['ins_term'] = $("#term").val().parseCurrency();
  	form_data['income_term'] = $("#s_retire").val().parseCurrency();
  	form_data['s_tax'] = $("#s_tax").val().parseCurrency();
  	form_data['ins_age'] = $("#s_age").val().parseCurrency();
  	form_data['assets'] = $("#assets").val().parseCurrency();
  	form_data['cashable_amt'] = $("#cashable_amt").val().parseCurrency();
  	form_data['ex_fees'] = $("#ex_fees").val().parseCurrency();
  	form_data['laibilities'] = $("#laibilities").val().parseCurrency();
  	form_data['l_cover'] = $("#l_cover").val().parseCurrency();
  	form_data['investment'] = $("#investment").val().parseCurrency();
  	form_data['retire_fund'] = $("#retire_fund").val().parseCurrency();
  	form_data['m_income'] = m_income;
  	// form_data['salary'] = $("#s_income").val();
  	form_data['m_expense'] = m_expense;
  	form_data['infrate'] = $("#infrate").val().parseCurrency();
  	form_data['s_income'] = $("#s_income").val().parseCurrency();
  	form_data['s_expense'] = $("#s_expense").val().parseCurrency();
  	form_data['s_page'] = 'main.calculator';
  	// form_data['total_init_income'] = parseInt($("#total_init_income").val());
  	// form_data['total_init_expenses'] = parseInt($("#total_init_expenses").val());
  	form_data['growth_rate'] = $("#growth_rate").val().parseCurrency();
  	form_data['init_capital'] = init_capital;
  	form_data['init_expense'] = init_expense;
  	form_data['clientId'] =  temp_user;
  	// form_data['incomeData'] = JSON.stringify(incomeData);
  	// form_data['expData'] = JSON.stringify(expData);
  	// form_data['assetData'] = JSON.stringify(assetData);
  	// form_data['liabilityData'] = JSON.stringify(liabilityData);
  	// form_data['infl_rate'] = $("#infl_rate").val();

    $.ajax({
       type:'POST',
       data:form_data,
       url:"{{ route('ajaxRequest.post') }}",
       success:function(data){
          // alert(data.req_term);
          // alert(data);

					$("#msg_usr").html("");
					if(data){
						req_cap_year = parseFloat(data.req_term.toString());
						req_deficit = data.deficit.toString();

						if(data.msg)
							$("#msg_usr").html(data.msg.toString());
						update_totals_to_summary();
					}
       }
    });
  }
});

function update_totals_to_summary(){
	//total initial income - A
	var initInc = (isNaN(parseFloat(m_income))?0:parseFloat(m_income));
	$("#init_income_summ").val("R "+numberWithCommas(initInc));

		// alert(initInc);

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

	//Cash flow table
	cashFlowData()
}

</script>
<div>
  <div class="row">
    <div class="col-md-12 mb-3">
      <h2 align = "center" class = "m-3"> Estate Estimator</h2>
    </div>

    <div class="col-md-11 mb-3" align = "right">
      <button type="button" id = "estate_calc_btn" name = "estate_calc_btn" class="calc-btn btn btn-primary">Calculate</button>
    </div>

    <div class="col-md-5 mb-3" align = "right">Expected Investment Growth rate</div>
    <div class="col-md-5 mb-3">
      <div class="form-outline">
        <input type="text" id="growth_rate" name="growth_rate" class="form-control" placeholder = "10%" value = "" autocomplete="false" />
        <!-- <label class="form-label" for="growth_rate">In Your Experience</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">Inflation Rate</div>
    <div class="col-md-5 mb-3">
      <div class="form-outline">
        <input type="text" id="infrate" name="infrate" class="form-control" placeholder = "6%" value = "" autocomplete="false" />
        <!-- <label class="form-label" for="infrate">In Your Experience</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">What does the surviving spouse earn per month</div>
    <div class="col-md-5 mb-3">
      <div class="form-outline">
        <input type="text" id="s_income" name="s_income" class="form-control" placeholder = "R 0" value = "" autocomplete="false" />
        <!-- <label class="form-label" for="s_income">Spouse Gross Salary</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">How many years will your spouse continue to work</div>
    <div class="col-md-5 mb-3">
      <div class="form-outline">
        <input type="text" id="s_retire" name="s_retire" class="form-control" value = ""/>
        <!-- <label class="form-label" for="s_retire">Normally this would be the term to retirement age</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">What is The Take Home Cash Required by your family</div>
    <div class="col-md-5 mb-3">
      <div class="form-outline">
        <input type="text" id="s_expense" name="s_expense" class="form-control" value = ""/>
        <!-- <label class="form-label" for="s_expense">Our full Assessment tool will remind you of hidden monthly costs</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">Use an SA Tax Calculator for the correct Tax Figure</div>
    <div class="col-md-5 mb-3">
      <div class="form-outline">
        <input type="text" id="s_tax" name="s_tax" class="form-control" value = ""/>
        <!-- <label class="form-label" for="s_tax">Advised to be considered</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">What is the age of your spouse</div>
    <div class="col-md-5 mb-3">
      <div class="form-outline">
        <input type="text" id="s_age" name="s_age" class="form-control" value = ""/>
        <!-- <label class="form-label" for="s_age">Spouse age</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">For how long will your family require the income</div>
    <div class="col-md-5 mb-3">
      <div class="form-outline">
        <input type="text" id="term" name="term" class="form-control" value = ""/>
        <!-- <label class="form-label" for="term">For how long will your family require the income</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">Estate Assets</div>
    <div class="col-md-5 mb-3">
      <div class="form-outline">
        <input type="text" id="assets" name="assets" class="form-control" value = ""/>
        <!-- <label class="form-label" for="assets">Sale of vehicles, property or collectables to generate extra income</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">Would your spouse sell any asset and turn it into cash</div>
    <div class="col-md-5 mb-3">
      <div class="form-outline">
        <input type="text" id="cashable_amt" name="cashable_amt" class="form-control" value = ""/>
        <!-- <label class="form-label" for="cashable_amt">3.99% vat inclusive - of the Estate Assets handled by your Executor</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">Exector Fee</div>
    <div class="col-md-5 mb-3">
      <div class="form-outline">
        <input type="text" id="ex_fees" name="ex_fees" class="form-control" value = ""/>
        <!-- <label class="form-label" for="ex_fees">What do you owe</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">What are your liabilities - Bond, Vehicle Loans, Credit Card etc</div>
    <div class="col-md-5 mb-3">
      <div class="form-outline">
        <input type="text" id="laibilities" name="laibilities" class="form-control" value = ""/>
        <!-- <label class="form-label" for="laibilities">Life Cover on your life payable for your families use</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">How much life cover is available for the family</div>
    <div class="col-md-5 mb-3">
      <div class="form-outline">
        <input type="text" id="l_cover" name="l_cover" class="form-control" value = ""/>
        <!-- <label class="form-label" for="l_cover">What is the present fund value of your investments Savings Account Endownments etc</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">How much does your investments funding amount to</div>
    <div class="col-md-5 mb-3">
      <div class="form-outline">
        <input type="text" id="investment" name="investment" class="form-control" value = ""/>
        <!-- <label class="form-label" for="investment">What is the present fund value of your retirement annuities pension funds etc</label> -->
      </div>
    </div>

    <div class="col-md-5 mb-3" align = "right">How much does your retirement funding amount to</div>
    <div class="col-md-5 mb-3">
      <div class="form-outline">
        <input type="text" id="retire_fund" name="retire_fund" class="form-control" value = ""/>
        <!-- <label class="form-label" for="retire_fund">What is the present fund value of your retirement annuities pension funds etc</label> -->
      </div>
    </div>

    <div class="col-md-11 mb-3" align = "right">
      <button type="button" id = "estate_calc_btn_1" name = "estate_calc_btn_1" class="calc-btn btn btn-primary">Calculate</button
    </div>

    <!-- <div class = "row">
      <div class="col-md-5 p-2 m-1 mb-1" style = "text-align:center;">&nbsp;</div>
      <div class="col-md-6 p-2 m-1 mb-1 bg-primary text-white" style = "text-align:center;">Assumptions</div>
    </div> -->

    <div class = "row">
      <div class="col-md-5 p-2 m-1 mb-1" style = "text-align:center;">
        <div class="p-2 m-2 bg-primary text-white" style = "text-align:center;">Income Summary</div>

        <div class="row">
          <div class="col-md-8 mb-1" align = "right">Monthly income available</div>
          <div class="col-md-4 mb-1">
            <div class="form-outline">
              <input type="text" id="init_income_summ" name="init_income_summ" class="form-control" value = ""/>
              <!-- <label class="form-label" for="init_income_summ">&nbsp;</label> -->
            </div>
          </div>

          <div class="col-md-8 mb-1" align = "right">Monthly income required</div>
          <div class="col-md-4 mb-1 m">
            <div class="form-outline">
              <input type="text" id="init_exp_summ" name="init_exp_summ" class="form-control" value = ""/>
              <label class="form-label" for="init_exp_summ">&nbsp;</label>
            </div>
          </div>

          <div class="col-md-8 mb-1" align = "right">Monthly shortfall/ Excess</div>
          <div class="col-md-4 mb-1">
            <div class="form-outline">
              <input type="text" id="monthly_shortfall" name="monthly_shortfall" class="form-control" value = ""/>
              <!-- <label class="form-label" for="monthly_shortfall">&nbsp;</label> -->
            </div>
          </div>
        </div>

        <div class="p-2 m-2 bg-primary text-white" style = "text-align:center;">Capital Summary</div>

        <div class="row">
          <div class="col-md-8 mb-1" align = "right">Initial available cash</div>
          <div class="col-md-4 mb-1">
            <div class="form-outline">
              <input type="text" id="init_avail_cash" name="init_avail_cash" class="form-control" value = ""/>
              <!-- <label class="form-label" for="init_avail_cash">&nbsp;</label> -->
            </div>
          </div>

          <div class="col-md-8 mb-1" align = "right">Initial liabilities and expenses</div>
          <div class="col-md-4 mb-1 m">
            <div class="form-outline">
              <input type="text" id="init_avail_liability" name="init_avail_liability" class="form-control" value = ""/>
              <!-- <label class="form-label" for="init_avail_liability">&nbsp;</label> -->
            </div>
          </div>

          <div class="col-md-8 mb-1" align = "right">Initial shortfall/ Excess</div>
          <div class="col-md-4 mb-1">
            <div class="form-outline">
              <input type="text" id="init_diff" name="init_diff" class="form-control" value = ""/>
              <!-- <label class="form-label" for="init_diff">&nbsp;</label> -->
            </div>
          </div>
        </div>

        <div class="p-2 m-2 bg-primary text-white" style = "text-align:center;">Assessment</div>

        <div class="row">
          <div class="col-md-8 mb-1" align = "right">Funds available will provide an income for</div>
          <div class="col-md-4 mb-1">
            <div class="form-outline">
              <input type="text" id="req_years_div" name="req_years_div" class="form-control" value = ""/>
              <!-- <label class="form-label" for="req_years_div">&nbsp;</label> -->
            </div>
          </div>

          <div class="col-md-8 mb-1" align = "right">Additional cover required</div>
          <div class="col-md-4 mb-1 m">
            <div class="form-outline">
              <input type="text" id="fund_deficit" name="fund_deficit" class="form-control" value = ""/>
              <!-- <label class="form-label" for="fund_deficit">&nbsp;</label> -->
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
		<div id="msg_usr" align = "center">
	</div>
    <div class="col-md-12 m-2 p-2 bg-primary text-white">
      <h4 align = "center">Cash Flow</h4>
    </div>
  </div>
  <div>
		<!-- cash flow grid here -->
		<div id="datatable" data-mdb-loading="true"></div>
  </div>
</div>
<script>
$( document ).ready(function() {
	// cashFlowData();
});

function resetCFtable(){
	$("#datatable").html("");

	// const columns = [
	// 	{ label: 'Age', field: 'age' },
	// 	{ label: 'Term', field: 'term' },
	// 	{ label: 'Capital', field: 'capital' },
	// 	{ label: 'Income', field: 'income' },
	// 	{ label: 'Req Budget', field: 'req_budget' },
	// ];
	//
	// const asyncTable = new mdb.Datatable(
	// 	document.getElementById('datatable'),
	// 	{ columns, },
	// 	{ loading: true }
	// );
}

function cashFlowData(){
	resetCFtable();

	const columns = [
		{ label: 'Age', field: 'age' },
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

	fetch('{{ route('ajaxCashFlow.post') }}')
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
</script>

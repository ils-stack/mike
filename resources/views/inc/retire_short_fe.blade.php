<script>
var req_cap_year = 0;
var req_deficit = 0;
var init_capital = 0;
var init_expense = 0;
var m_income = 0;
var m_expense = 0;

var req_deficit_pv = 0;
var init_capital_pv = 0;
var init_expense_pv = 0;
var m_income_pv = 0;
var m_expense_pv = 0;

var temp_user = 999999999999997; //dummmy user

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

$( function(){
	$( ".fvfield" ).each(function(index){
		$(this).on("change", function(){
			var formData = new FormData();

			var yearly_cont = $(this).val().parseCurrency();
			var fv_id = 'fv_' + $(this).attr('id');
			$("#"+fv_id).val('');

			formData.append('rate', $("#infrate").val());
			formData.append('growth_rate', $("#growth_rate").val());
			formData.append('present_age',  $("#present_age").val());
			formData.append('retire_age',  $("#retire_age").val());
			formData.append('pmt', 0);
			formData.append('pv', yearly_cont);

			if(check_age()){
				$.ajax({
					 type:'POST',
					 url:"{{ route('ajaxPostedFv.post') }}",
					 data: formData,
					 cache: false,
					 contentType: false,
					 processData: false,
					 success:function(data){
						 // alert(fv_id)
						 $("#"+fv_id).val("R "+numberWithCommas(data.toString()))
						 // alert(data);
					 }
				});
			}
		});
	});
});

$(document).ready(function(){
	// $('input[type=text]').keyup(function (){
	// 	$("#calculate").css('backgroundColor','#ff0000');
	// 	$("#calculate_1").css('backgroundColor','#ff0000');
	// });
	$("input[type=text]").click(function() {
		if(!$(this).hasClass("selected")){
			// $(this).select();
			$(this).addClass("selected");
		}
	});

	$("input[type=text]").change(function() {
		recalc_fv();
	});

	$("input[type=text]").blur(function() {
		if($(this).hasClass("selected")) {
			$(this).removeClass("selected");

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
		}
	});
});

/* fv calculators */

function check_age(){
	var p_age = parseInt($("#present_age").val());
	var r_age = parseInt($("#retire_age").val());

	if(p_age<=0){
		$("#present_age").focus();
		$("#present_age").css('background-color','#ff0000');

		return false;
	}else{
		$("#present_age").css('background-color','#fff');
	}

	if(r_age<=0){
		$("#retire_age").focus();
		$("#retire_age").css('background-color','#ff0000');

		return false;
	}else{
		$("#retire_age").css('background-color','#fff');
	}

	return true;
}

function calc_fv_one(){
	$("#fv_monthly_cont").val('');
	if(check_age()){
		// var monthly_cont = (isNaN(parseFloat($("#monthly_cont").val()))?0:parseFloat($("#monthly_cont").val()));
		var monthly_cont = $("#monthly_cont").val().parseCurrency();

		//yearly_cont used with posted_fv
		var yearly_cont = $("#monthly_cont").val().parseCurrency();

		var formData = new FormData();

		formData.append('rate', $("#es_monthly_cont").val());
		formData.append('growth_rate', $("#growth_rate").val());
		formData.append('present_age',  $("#present_age").val());
		formData.append('retire_age',  $("#retire_age").val());
		formData.append('infrate',  $("#infrate").val());
		formData.append('pmt', monthly_cont);
		formData.append('pv', $("#invest_pv").val().parseCurrency());

		$.ajax({
			 type:'POST',
			 url:"{{ route('ajaxPosted_simple_int.post') }}",
			 data: formData,
			 cache: false,
			 contentType: false,
			 processData: false,
			 success:function(data){
				 $("#fv_monthly_cont").val("R "+numberWithCommas(data.toString()));
				 // $("#fv_monthly_cont").focus();
			 }
		});
	}
}

function calc_fv_two(){
	$("#fv_capital").val('');
	if(check_age()){
		var monthly_cont = $("#retire_fund").val().parseCurrency();

		var formData = new FormData();

		formData.append('rate', $("#es_capital").val());
		formData.append('growth_rate', $("#growth_rate").val());
		formData.append('present_age',  $("#present_age").val());
		formData.append('retire_age',  $("#retire_age").val());
		formData.append('pmt', monthly_cont);
		formData.append('pv', $("#retire_fv").val().parseCurrency());

		$.ajax({
			 type:'POST',
			 url:"{{ route('ajaxPosted_simple_int.post') }}",
			 data: formData,
			 cache: false,
			 contentType: false,
			 processData: false,
			 success:function(data){
				 $("#fv_capital").val("R "+numberWithCommas(data.toString()));
				 // $("#fv_capital").focus();
			 }
		});
	}
}

function calc_fv_three(){
	$("#fv_es_capital").val('');
	if(check_age()){
		var monthly_cont = $("#sp_monthly_cont").val().parseCurrency();

		var formData = new FormData();

		formData.append('rate', $("#sp_es_capital").val());
		formData.append('growth_rate', $("#growth_rate").val());
		formData.append('present_age',  $("#present_age").val());
		formData.append('retire_age',  $("#retire_age").val());
		formData.append('pmt', monthly_cont);
		formData.append('pv', $("#sp_invest_val").val().parseCurrency());

		$.ajax({
			 type:'POST',
			 url:"{{ route('ajaxPosted_simple_int.post') }}",
			 data: formData,
			 cache: false,
			 contentType: false,
			 processData: false,
			 success:function(data){
				 $("#fv_es_capital").val("R "+numberWithCommas(data.toString()));
				 // $("#fv_es_capital").focus();
			 }
		});
	}
}

function calc_fv_four(){
	$("#fv_sp_inv").val('');
	if(check_age()){
		var monthly_cont = $("#sp_monthly_inv").val().parseCurrency();

		var formData = new FormData();

		formData.append('rate', $("#sp_es_capital").val());
		formData.append('growth_rate', $("#growth_rate").val());
		formData.append('present_age',  $("#present_age").val());
		formData.append('retire_age',  $("#retire_age").val());
		formData.append('pmt', monthly_cont);
		formData.append('pv', $("#sp_fund_pv").val().parseCurrency());

		$.ajax({
			 type:'POST',
			 url:"{{ route('ajaxPosted_simple_int.post') }}",
			 data: formData,
			 cache: false,
			 contentType: false,
			 processData: false,
			 success:function(data){
				 $("#fv_sp_inv").val("R "+numberWithCommas(data.toString()));
				 // $("#fv_sp_inv").focus();
			 }
		});
	}
}

function recalc_fv(){
	to_fv_calc();
	calc_fv_one();
	calc_fv_two();
	calc_fv_three();
	calc_fv_four();
}

function to_fv_calc(){
	$( ".fvfield" ).each(function(index){
		var fv_id = 'fv_' + $(this).attr('id');

		$("#"+fv_id).val('');

		if(check_age()){
			var yearly_cont = $(this).val().parseCurrency();
			var fv_id = 'fv_' + $(this).attr('id');

			var formData = new FormData();

			formData.append('rate', $("#infrate").val());
			formData.append('growth_rate', $("#growth_rate").val());
			formData.append('present_age',  $("#present_age").val());
			formData.append('retire_age',  $("#retire_age").val());
			formData.append('pmt', 0);
			formData.append('pv', yearly_cont);

			$.ajax({
			   type:'POST',
			   url:"{{ route('ajaxPosted_fv_pv.post') }}",
			   data: formData,
			   cache: false,
			   contentType: false,
			   processData: false,
			   success:function(data){
			    switch(fv_id){
			      case 'fv_p_liabilities':
			        $("#"+fv_id).val("R "+numberWithCommas(yearly_cont));

			      break;
			      default:
			        $("#"+fv_id).val("R "+numberWithCommas(data.toString()));
			      break;
			    }
					// $("#"+fv_id).focus();
			   }
			});
		}
	});
}

function do_calc(){
	inc_t = (isNaN(parseFloat($("#income_req_period").val()))?0:parseFloat($("#income_req_period").val()));
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
	formData.append('present_age', $("#present_age").val());
	formData.append('retire_age', $("#retire_age").val());
	formData.append('spouse_age', $("#spouse_age").val());
	formData.append('spouse_retire', $("#spouse_retire").val());
	formData.append('other_inc', $("#other_inc").val().parseCurrency());
	formData.append('fv_other_inc', $("#fv_other_inc").val().parseCurrency()); //FV income
	formData.append('spouse_inc', $("#spouse_inc").val().parseCurrency());
	formData.append('fv_spouse_inc', $("#fv_spouse_inc").val().parseCurrency()); //FV income
	formData.append('home_cash', $("#home_cash").val().parseCurrency());
	formData.append('fv_home_cash', $("#fv_home_cash").val().parseCurrency()); //FV liability
	formData.append('tax_val', $("#tax_val").val().parseCurrency());
	formData.append('fv_tax_val', $("#fv_tax_val").val().parseCurrency()); //FV liability
	formData.append('income_req_period', $("#income_req_period").val());
	formData.append('sell_cash', $("#sell_cash").val().parseCurrency());
	formData.append('fv_sell_cash', $("#fv_sell_cash").val().parseCurrency()); //FV income
	formData.append('fv_p_liabilities', $("#fv_p_liabilities").val().parseCurrency()); //FV liability
	formData.append('cap_liabilities', $("#cap_liabilities").val().parseCurrency()); //FV liability
	formData.append('fv_cap_liabilities', $("#fv_cap_liabilities").val().parseCurrency()); //FV liability
	formData.append('monthly_cont', $("#monthly_cont").val().parseCurrency());
	formData.append('es_monthly_cont', $("#es_monthly_cont").val().parseCurrency()); //bala
	formData.append('es_capital', $("#es_capital").val().parseCurrency()); //bala
	formData.append('es_sp_inv', $("#es_sp_inv").val().parseCurrency()); //bala
	formData.append('invest_pv', $("#invest_pv").val().parseCurrency());
	formData.append('fv_monthly_cont', $("#fv_monthly_cont").val().parseCurrency());  //FV for capital
	formData.append('retire_fv', $("#retire_fv").val().parseCurrency());
	formData.append('retire_fund', $("#retire_fund").val().parseCurrency());
	formData.append('fv_capital', $("#fv_capital").val().parseCurrency()); //FV for capital
	formData.append('sp_invest_val', $("#sp_invest_val").val().parseCurrency());
	formData.append('sp_monthly_cont', $("#sp_monthly_cont").val().parseCurrency());
	formData.append('fv_es_capital', $("#fv_es_capital").val().parseCurrency()); //FV for capital
	formData.append('sp_es_capital', $("#sp_es_capital").val());
	formData.append('sp_fund_pv', $("#sp_fund_pv").val().parseCurrency());
	formData.append('sp_monthly_inv', $("#sp_monthly_inv").val().parseCurrency());
	formData.append('fv_sp_inv', $("#fv_sp_inv").val().parseCurrency());  //FV for capital

	$.ajax({
		 type:'POST',
		 url:"{{ route('ajaxRequestRetire.post') }}",
		 data: formData,
		 cache: false,
		 contentType: false,
		 processData: false,
		 success:function(data){
			console.log(data);

			req_cap_year = parseFloat(data['req_term'].toString());

			req_deficit = data['deficit'].toString();
			m_income = data['m_income'].toString();
			m_expense = data['m_expense'].toString();
			init_capital = data['init_capital'].toString();
			init_expense = data['init_expense'].toString();

			req_deficit_pv = data['req_deficit_pv'].toString();
			m_income_pv = data['m_income_pv'].toString();
			m_expense_pv = data['m_expense_pv'].toString();
			init_capital_pv = data['init_capital_pv'].toString();
			init_expense_pv = data['init_expense_pv'].toString();

	 		update_totals_to_summary();
		 }
	});

}

function update_totals_to_summary(){
	cash_flow_grid_rt();

	//total initial income - A
	// alert(initInc_pv);
	var initInc_pv = (isNaN(parseFloat(m_income_pv))?0:parseFloat(m_income_pv));
	var initInc = (isNaN(parseFloat(m_income))?0:parseFloat(m_income));

	$("#init_income_summ_pv").val("R "+numberWithCommas(initInc_pv));
	$("#init_income_summ").val("R "+numberWithCommas(initInc));

	//total initial expense - B
	var initExp_pv = (isNaN(parseFloat(m_expense_pv))?0:parseFloat(m_expense_pv));
	var initExp = (isNaN(parseFloat(m_expense))?0:parseFloat(m_expense));

	$("#init_exp_summ_pv").val("R "+numberWithCommas(initExp_pv));
	$("#init_exp_summ").val("R "+numberWithCommas(initExp));

	//monthly shortfall/ Excess - A-B
	$("#monthly_shortfall_pv").val("R "+numberWithCommas((initInc_pv-initExp_pv)));
	$("#monthly_shortfall").val("R "+numberWithCommas((initInc-initExp)));

	//avail cash - C
	var totAccess_pv = (isNaN(init_capital_pv)?0:init_capital_pv);
	var totAccess = (isNaN(init_capital)?0:init_capital);

	$("#init_avail_cash_pv").val("R "+numberWithCommas(totAccess_pv));
	$("#init_avail_cash").val("R "+numberWithCommas(totAccess));

	//avail liabilites - D
	var totLiability_pv = (isNaN(init_expense_pv)?0:init_expense_pv);
	var totLiability = (isNaN(init_expense)?0:init_expense);

	$("#init_avail_liability_pv").val("R "+numberWithCommas(totLiability_pv));
	$("#init_avail_liability").val("R "+numberWithCommas(totLiability));

	//Initial shortfall/ Excess - C-D
	$("#init_diff_pv").val("R "+numberWithCommas((totAccess_pv-totLiability_pv)));
	$("#init_diff").val("R "+numberWithCommas((totAccess-totLiability)));

	//required term, year before the capital becomes negative
	$("#req_years_div").val(req_cap_year+" years");

	//required term, year before the capital becomes negative
	$("#fund_deficit_pv").val("R "+numberWithCommas(req_deficit_pv));
	$("#fund_deficit").val("R "+numberWithCommas(req_deficit));
}

function load_calc(dataArr,fnVars){
	// alert(dataArr.toString());
	// return;
	if(dataArr != 'empty'){
		req_cap_year = parseFloat(dataArr['req_term'].toString());

		req_deficit = dataArr['deficit'].toString();
		m_income = dataArr['m_income'].toString();
		m_expense = dataArr['m_expense'].toString();
		init_capital = dataArr['init_capital'].toString();
		init_expense = dataArr['init_expense'].toString();

		req_deficit_pv = dataArr['req_deficit_pv'].toString();
		m_income_pv = dataArr['m_income_pv'].toString();
		m_expense_pv = dataArr['m_expense_pv'].toString();
		init_capital_pv = dataArr['init_capital_pv'].toString();
		init_expense_pv = dataArr['init_expense_pv'].toString();

		update_totals_to_summary();
	}
}

$( document ).ready(function() {
	$( ".calc-btn" ).click(function() {
		do_calc();
	});
});

function cash_flow_grid_rt(){
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

	fetch('{{ route('ajaxCashFlowRet.post') }}')
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
</script>

<div>
  <div class="row">
      <div class="col-md-12 mb-3">
        <h2 align = "center" class = "m-3"> Retirement Estimator</h2>
      </div>

      <div class="col-md-11 mb-3" align = "right">
        <button type="button" class="btn calc-btn btn-primary">Calculate</button>
      </div>

      @include('inc.retirecalc_fields')

      <hr class="my-5" style="margin:1rem 0rem !important;">

      @include('inc.retirecalc_fvs')

      <hr class="my-5" style="margin:1rem 0rem !important;">

      <div class="col-md-11 mb-3" align = "right">
        <button type="button" class="btn calc-btn btn-primary">Calculate</button>
      </div>

      @include('inc.retire_summ')

    </div>
    <div class="col-md-12 m-2 p-2 bg-primary text-white">
      <h4 align = "center">Cash Flow</h4>
    </div>
		<div>
			<div id = "datatable"></div>
		</div>
  </div>
  <div>

  </div>
</div>

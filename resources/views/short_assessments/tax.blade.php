@extends('layouts.app')

@section('title', 'Tax Calculator')

@section('content')
<div class="container-fluid pt-4"><form name = "snapp_calc_form" id = "snapp_calc_form" method = "post">
  @csrf
  <!-- Section: Main chart -->
  <section class="mb-4">
    <div class="card">

      <div class="card-header py-3">
        <h5 class="mb-0 text-center"><strong>Tax Calcuator</strong></h5>
      </div>
      <div class="card-body" style = "min-height:800px;">
        @include('inc.short_assess_btns_fe')
        <div class="row">
          <div class="col-md-12 mb-2 btn-primary rounded-5 p-2" align="center">
            <strong>Vsure Tax Calculator Using the SA <span id="fyear">{{date('Y')}} - {{date('Y')+1}}</span> Tax Tables</strong>
          </div>
        </div>

        <div class="row">
          <div class="col-md-2 mb-2 text-white rounded-5 p-2" align="center" style="background-color:#212121">
            <div class="p-2"><<</div>
          </div>

          <div class="col-md-8 mb-2" align = "center">
            <div class="text-white rounded-5 p-3" style="background-color:#212121">
              <strong>Year Selector</strong>
            </div>
          </div>

          <div class="col-md-2 mb-2 text-white rounded-5 p-2" align = "center" style="background-color:#212121">
            <div class="p-2">>></div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-1 btn-primary rounded-5 p-2" align="center">
            <div class="row">
              <div class="col-md-12" align = "left">1) Age Related Rebates and Thresholds</div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-1 rounded-5 p-2" align="center">
            <div class="row">
              <div class="col-md-4" align = "left">Age Next</div>
              <div class="col-md-4" align = "left">
                <div class="form-outline mb-2">
                  <input type="text" class="form-control active bg-info text-white p-4 brown-box" id="age" name="age" value="" placeholder="Age" required="" autocomplete="off">
                </div>
              </div>
              <div class="col-md-4 alert" role = "alert" align = "left" data-mdb-color="info">Auto calculated @ at age 66 and 76 next</div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 btn-primary mb-1 rounded-5 p-2" align="left">
              2) Gross Income
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-1 rounded-5 p-2" align="center">
            <div class="row">
              <div class="col-md-4" align = "left">Gross Income</div>
              <div class="col-md-4" align = "left">
                <div class="form-outline mb-2">
                  <input type="text" class="form-control bg-info text-white active p-4 brown-box curr" id="income" name="income" value="" placeholder="R0" required="" autocomplete="off">
                </div>
              </div>
              <div class="col-md-4 alert" role = "alert" align = "left" data-mdb-color="info">Salary, Overtime Rental, Child Support etc</div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-1 rounded-5 p-2" align="center">
            <div class="row">
              <div class="col-md-4" align = "left">Gross Taxable Income</div>
              <div class="col-md-4" align = "left">
                <div class="form-outline mb-2">
                  <input type="text" class="form-control active p-4 curr" id="gr_tax_inc" name="gr_tax_inc" value=""
                   placeholder="R0" readonly required="" autocomplete="off"
                   style = "background-color:#F5F5F5;"
                   >
                </div>
              </div>
              <div class="col-md-4 alert" role = "alert" align = "left" data-mdb-color="info">Adjusted Income When/If Applicable</div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 btn-primary mb-1 rounded-5 p-2" align="left">
            3) Additional Deductables and Exemptions If Applicable
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-1 rounded-5 p-2" align="center">
            <div class="row">
              <div class="col-md-4" align = "left">Non Taxable</div>
              <div class="col-md-4" align = "left">
                <div class="form-outline mb-2">
                  <input type="text" class="form-control active bg-info text-white p-4 brown-box curr" id="nt_income" name="nt_income" value="" placeholder="R0" required="" autocomplete="off">
                </div>
              </div>
              <div class="col-md-4 alert" role = "alert" align = "left" data-mdb-color="info">Tax Free Income If applicable</div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-1 rounded-5 p-2" align="center">
            <div class="row">
              <div class="col-md-4" align = "left">Deductable Contributions</div>
              <div class="col-md-4" align = "left">
                <div class="form-outline mb-2">
                  <input type="text" class="form-control active bg-info text-white p-4 brown-box curr" id="d_cont" name="d_cont" value="" placeholder="R0" required="" autocomplete="off">
                </div>
              </div>
              <div class="col-md-4 alert" role = "alert" align = "left" data-mdb-color="info">Example: Retirement Funding</div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 btn-primary mb-1 rounded-5 p-2" align="left">
            4) Tax Figures
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-1 rounded-5 p-2" align="center">
            <div class="row">
              <div class="col-md-4" align = "left">Net Taxable Income</div>
              <div class="col-md-4" align = "left">
                <div class="form-outline mb-2">
                  <input type="text" class="form-control active p-4 curr" id="net_tax_income" name="net_tax_income" value="" placeholder="R0" readonly required="" autocomplete="off" style = "background-color:#F5F5F5;">
                </div>
              </div>
              <div class="col-md-4 alert" role = "alert" align = "left" data-mdb-color="info">After Deductions</div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-1 rounded-5 p-2" align="center">
            <div class="row">
              <div class="col-md-4" align = "left">Tax Payable</div>
              <div class="col-md-4" align = "left">
                <div class="form-outline mb-2">
                  <input type="text" class="form-control active p-4 curr" id="tax_payable" name="tax_payable" value="" placeholder="R0" readonly required="" autocomplete="off" style = "background-color:#F5F5F5;">
                </div>
              </div>
              <div class="col-md-4 alert" role = "alert" align = "left" data-mdb-color="info">Average Tax Rate <span id = "tax_rate">0%</span></div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 btn-primary mb-1 rounded-5 p-2" align="left">
            5) Take Home Calculation
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-1 rounded-5 p-2" align="center">
            <div class="row">
              <div class="col-md-4" align = "left">Available After Tax</div>
              <div class="col-md-4" align = "left">
                <div class="form-outline mb-2">
                  <input type="text" class="form-control active p-4 curr" readonly id="avl_aft_tax" name="avl_aft_tax" value="" placeholder="R0" required="" autocomplete="off" style = "background-color:#F5F5F5;">
                </div>
              </div>
              <div class="col-md-4 alert" role = "alert" align = "left" data-mdb-color="info">After Tax - Take Home Available</div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-1 rounded-5 p-2" align="center">
            <div class="row">
              <div class="col-md-4" align = "left">Budgeted After Tax</div>
              <div class="col-md-4" align = "left">
                <div class="form-outline mb-2">
                  <input type="text" class="form-control active bg-info text-white p-4 brown-box curr" id="budget_aft_tax" name="budget_aft_tax" value="" placeholder="R0" required="" autocomplete="off">
                </div>
              </div>
              <div class="col-md-4 alert" role = "alert" align = "left" data-mdb-color="info">After Tax - Take Home Required</div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-1 rounded-5 p-2" align="center">
            <div class="row">
              <div class="col-md-4" align = "left">Shortfall/Excess</div>
              <div class="col-md-4" align = "left">
                <div class="form-outline mb-2">
                  <input type="text" class="form-control active p-4 curr" readonly id="short_fall" name="short_fall" value="" placeholder="R0" required="" autocomplete="off" style = "background-color:#F5F5F5;">
                </div>
              </div>
              <div class="col-md-4 alert" role = "alert" align = "left" data-mdb-color="info">Balance if shortfall / Invest If In excess</div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 btn-primary mb-1 rounded-5 p-2" align="left">
            6) Use the Adjuster to balance Budget Shortfall against Available Income
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-1 rounded-5 p-2" align="center">
            <div class="row">
              <div class="col-md-4" align = "left">&nbsp;</div>
              <div class="col-md-4" align = "left">
                <div class="form-outline mb-2">
                  <input type="text" class="form-control active bg-info text-white p-4" id="adjuster" name="adjuster" value="" placeholder="Adjuster" required="" autocomplete="off">
                </div>
              </div>
              <div class="col-md-4 alert" role = "alert" align = "left" data-mdb-color="info">Blue fields are input fields</div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12" align="right">
              <div class="col-md-4 alert" role = "alert" align = "left" data-mdb-color="" style = "background-color:#F5F5F5;">Grey fields are output fields</div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12" align="right">
            <button type="button" class="btn btn-primary mb-2">Calculate</button>
            <button type="button" class="btn btn-primary mb-2" onclick = "clear_fields();">Clear Fields</button>
          </div>
        </div>

      <input type = "hidden" name = "cid" id = "cid" value = "1" autocomplete="off" />
      <input type = "hidden" name = "module" id = "module" value = "fe" autocomplete="off" />
      <input type = "hidden" name = "rid" id = "rid" value = "1649821429" autocomplete="off" />
      <input type = "hidden" name = "year_id" id = "year_id" value = "2024" autocomplete="off" />

    </form></div>

    <script>
    $(document).ready(function(){
    	$( ".brown-box" ).each(function(index){
    		$(this).on("blur", function(){
    			tax_calc();
    		});
    	});

    	// $("input[type=text]").click(function() {
    	// 	if(!$(this).hasClass("selected")){
    	// 		$(this).select();
    	// 		$(this).addClass("selected");
    	// 	}
    	// });

    	$("input[type=text]").blur(function(){
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

    	// tax_calc();
    });


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

    function clear_fields(){
    	var fmObj = document.forms['snapp_calc_form'];
    	for(var i=0;i<fmObj.elements.length;i++){
    		switch(fmObj.elements[i].type){
    			case 'text':
    				fmObj.elements[i].value = 0;
    			break;
    		}
    	}
    	tax_calc();
    }

    function tax_calc(){
      var frmObj = $('#snapp_calc_form');

      var form_data = {};

      form_data['income'] = $("#income").val().parseCurrency();
      form_data['nt_income'] = $("#nt_income").val().parseCurrency();
      form_data['adjuster'] = $("#adjuster").val().parseCurrency();
      form_data['d_cont'] = $("#d_cont").val().parseCurrency();
      form_data['age'] = $("#age").val();
      form_data['budget_aft_tax'] = $("#budget_aft_tax").val().parseCurrency();

      //variables to store in the temp table
      //form_data['json_dump'] = frmObj.serialize().toString();
      form_data['json_dump'] = JSON.stringify($(frmObj).serializeArray());
      form_data['cid'] = $("#cid").val().parseCurrency();
      form_data['module'] = $("#module").val();
      form_data['rid'] = $("#rid").val().parseCurrency();
      form_data['year_id'] = $("#year_id").val();

      $.ajax({
         type:'POST',
         data:form_data,
         url:"{{ route('ajaxTaxCalc.post') }}",
         success:function(data){
  					$("#msg_usr").html("");
  					if(data){
              console.log(data);

              for(i in data){
                $("#"+i).val(data[i]);
          			$("#"+i).html(data[i]);
              }
  					}
         }
      });
    }

    function tax_disp(dataArr,fnVars){
      if(typeof dataArr == 'object'){
        for(i in dataArr){
          // alert(dataArr[i]);
          $("#"+i).val(dataArr[i]);
          $("#"+i).html(dataArr[i]);
        }
      }
    }
    </script>


  </section>
@endsection

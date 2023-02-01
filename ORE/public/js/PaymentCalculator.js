$(document).ready(function () {
	paycalc.init();
});

var paycalc = {
	init: function () {

		var appurl = $('#APP_URL').val();
		var tier = $('#tier').val();
		var year = $('#year').val();
		var model = $('#model').val();


		if ($('#vehicle_type').val() == 'new' && $('#year').val() >= 2019) {
			paycalc.payment_lease('lease', 'onload');
		}
		else {
			paycalc.payment_finance('finance', 'onload');
		}
		// TAB LEASE 


		var dp = $('.ore_lease_msrp_price').text();
		var da = dp.replace('$', '').replace(',', '');
		$('.stickerPrice').text('$' + paycalc.addCommas(parseInt(da)));
		$('#l_MSRPHidden').val(parseInt(da));
		$('#ore_txtbox_lease_downpayment').val(parseInt(da / 10));


		$('#tab_lease').on('click', function () {
			$('.offer_ccap_tab_span').show();
			$('.offer_ccap_tab').html('lease');
		});
		$('#tab_finance').on('click', function () {
			$('.offer_ccap_tab_span').show();
			$('.offer_ccap_tab').html('finance');
		});
		$('#tab_cash').on('click', function () {
			$('.offer_ccap_tab_span').hide();
		});

		/* $(document).on('click','#modalLeaseCalc', function(){  
			paycalc.payment_lease('lease','click');
		});    */
		// ********* available program change events ********** 
		$('#contpop').on('click', function () {

			var linkText = $('.tabsContainer>.nav-tabs li.active a').text();
			if (linkText == 'Lease') {
				$('#leaseCalc').modal('show');

				$('.ore_lease_emi, .ore_disp_lease_estimate, .mDestLExplore, .main_msrp_lease, .lease_main_incentive').html("<img alt='loader' src='https://d1jougtdqdwy1v.cloudfront.net/images/ajax-loader.gif' />");
				paycalc.payment_lease('lease', 'available_offers');
			} else if (linkText == 'Finance') {
				$('#paymentCalc').modal('show');
				$('.ore_finance_emi, .ore_disp_finance_estimate, .f_c_name, .chrysler_apr_in, .mDestFExplore, .main_msrp_finance, .finance_main_incentive').html("<img alt='loader' src='https://d1jougtdqdwy1v.cloudfront.net/images/ajax-loader.gif' />");
				paycalc.payment_finance('finance', 'available_offers');
			} else if (linkText == 'Cash') {
				$('#cashCalc').modal('show');
				$('.ore_cash_emi, .mDestCExplore, .main_msrp_cash, .cash_main_incentive').html("<img src='https://d1jougtdqdwy1v.cloudfront.net/images/ajax-loader.gif' alt='loader' />");
				paycalc.payment_cash('cash', 'available_offers');
			}

		});
		$('#leaseCalc, #paymentCalc, #cashCalc').on('shown.bs.modal', function (e) {
			$('body').addClass("modal-open");
		})
		// ********* available program change events **********

		$(document).on('change', '#ore_txtbox_lease_downpayment', function (e) {
			$('.ore_lease_emi, .ore_disp_lease_estimate').html("<img alt='loader' src='https://d1jougtdqdwy1v.cloudfront.net/images/ajax-loader.gif' />");
			paycalc.payment_lease_downpercent();
			paycalc.payment_lease('lease', 'click');
		});

		$(document).on('change', '.ore_txtbox_ltradein, .yearlyMilage, .ore_dropdown_lease_terms, .lease_chk, ._lease_finance_type, .lease_dealer_discount, #lease_additional_disc', function (e) {
			$('.ore_lease_emi, .ore_disp_lease_estimate, .mDestLExplore').html("<img alt='loader' src='https://d1jougtdqdwy1v.cloudfront.net/images/ajax-loader.gif' />");
			paycalc.payment_lease('lease', 'click');
		});

		// TAB FINANCE			 
		$(document).on('click', '.tab_payments', function () {
			var tab_name = $(this).attr('id');
			if (tab_name == 'tab_finance') {
				var dp2 = $('.ore_lease_msrp_price').text();
				var da2 = dp2.replace('$', '').replace(',', '');
				$('.f_stickerPrice').text('$' + paycalc.addCommas(parseInt(da2)));
				$('#F_MSRPHidden').val(parseInt(da2));
				$('#financeDownPaymentField').val(parseInt(da2 / 10));
				$('.ore_finance_emi, .ore_disp_finance_estimate, .f_c_name, .chrysler_apr_in, .mDestFExplore, .main_msrp_finance, .finance_main_incentive').html("<img alt='loader' src='https://d1jougtdqdwy1v.cloudfront.net/images/ajax-loader.gif' />");
				paycalc.payment_finance('finance', 'onload');
			} else if (tab_name == 'tab_cash') {
				var dp2 = $('.ore_lease_msrp_price').text();
				var da2 = dp2.replace('$', '').replace(',', '');
				$('.c_stickerPrice').text('$' + paycalc.addCommas(parseInt(da2)));
				$('#C_MSRPHidden').val(parseInt(da2));
				paycalc.payment_cash('cash', 'onload');
			}
		});

		$(document).on('change', '#financeDownPaymentField', function () {
			$('.ore_finance_emi, .ore_disp_finance_estimate, .f_c_name').html("<img alt='loader' src='https://d1jougtdqdwy1v.cloudfront.net/images/ajax-loader.gif' />");
			paycalc.payment_finance_downpercent();
			paycalc.payment_finance('finance', 'click');
		});

		$(document).on('change', ' .ore_dropdown_finance_terms, .finance_chk, ._f_finance_type, #financeTradeInValueField, .f_dealer_discount,#finance_additional_disc', function (e) {
			$('.ore_finance_emi, .ore_disp_finance_estimate, .f_c_name, .chrysler_apr_in, .mDestFExplore').html("<img alt='loader' src='https://d1jougtdqdwy1v.cloudfront.net/images/ajax-loader.gif' />");
			paycalc.payment_finance('finance', 'click');
		});
		// TAB CASH

		// $(document).on('click','#modalCashCalc', function(){ 
		//paycalc.payment_cash('cash','click');
		//});
		$(document).on('click', '.cash_chk', function (e) {
			$('.ore_cash_emi,.mDestCExplore').html("<img src='https://d1jougtdqdwy1v.cloudfront.net/images/ajax-loader.gif' alt='loader' />");
			paycalc.payment_cash('cash', 'click');
		});

		$(document).on('change', '#cashTradeInValueField,#cash_additional_disc', function (e) {

			$('.ore_cash_emi').html("<img alt='loader' src='https://d1jougtdqdwy1v.cloudfront.net/images/ajax-loader.gif' />");
			paycalc.payment_cash('cash', 'click');
		});

		setInputFilter(document.getElementById("financeTradeInValueField"), function (value) {
			return /^[0-9,]{0,10}$/.test(value);
		});
		setInputFilter(document.getElementById("financeDownPaymentField"), function (value) {
			return /^[0-9,]{0,10}$/.test(value);
		});
		setInputFilter(document.getElementById("ore_txtbox_lease_downpayment"), function (value) {
			return /^[0-9,]{0,10}$/.test(value);
		});

		setInputFilter(document.getElementById("leaseTradeInValueField"), function (value) {
			return /^[0-9,]{0,10}$/.test(value);
		});
		setInputFilter(document.getElementById("lease_dealer_discount"), function (value) {
			return /^[0-9,]{0,10}$/.test(value);
		});
		setInputFilter(document.getElementById("lease_additional_disc"), function (value) {
			return /^[0-9,]{0,4}$/.test(value);
		});
		setInputFilter(document.getElementById("finance_additional_disc"), function (value) {
			return /^[0-9,]{0,4}$/.test(value);
		});
		setInputFilter(document.getElementById("cash_additional_disc"), function (value) {
			return /^[0-9,]{0,4}$/.test(value);
		});
		/*  setInputFilter(document.getElementById("f_dealer_discount"), function(value) {
			return /^[0-9,]{0,10}$/.test(value);
		 }); */

		setInputFilter(document.getElementById("disclaimerThirdparty_field"), function (value) {
			return /^-?\d*[.,]?\d*$/.test(value) && (value === "" || parseInt(value) <= 100);
		});

		setInputFilter(document.getElementById("cashTradeInValueField"), function (value) {
			return /^[0-9 ,]{0,10}$/.test(value);
		});


	},
	payment_lease_downpercent: function () {
		var msrp = $('#l_MSRPHidden').val();
		var destination = 0;//$('#l_destHidden').val();
		var total_price = parseInt(msrp) + parseInt(destination);
		var dpayment = $('#ore_txtbox_lease_downpayment').val();
		var percent = parseFloat((dpayment / total_price) * 100).toFixed(2);
		$('.LPaymentRange').text(percent);
		$('#downpayLeaseHidden').val(percent)
	},
	payment_lease: function (transactionType, methods) {
		var vin = $('#vin').val();
		var zipcode = localCache.get('zipCodeEntered');
		var dealer_disc = 0;//$('#lease_dealer_discount').val();  
		var lease_additional_disc=$('#lease_additional_disc').val(); 
		var down = $('#ore_txtbox_lease_downpayment').val();
		var tradein = $('#leaseTradeInValueField').val();
		var term = $("#lease_terms").val();
		var mileage = $('#yearlyMilage').val();
		var dealer_code = $('#dealer_code').val();
		var finance_type = $("input[name='lease_finance_type']:checked").val();
		$("input.lease_chk").attr("disabled", "disabled");

		if (isNaN(dealer_disc) || $.trim(dealer_disc) == '' || parseInt(dealer_disc) === NaN || parseInt(dealer_disc) === undefined) dealer_disc = 0;
		if (isNaN(lease_additional_disc) || $.trim(lease_additional_disc) == '' || parseInt(lease_additional_disc) === NaN || parseInt(lease_additional_disc) === undefined) lease_additional_disc = 0;
		if (isNaN(down) || $.trim(down) == '' || parseInt(down) === NaN || parseInt(down) === undefined) down = 0;
		if (isNaN(tradein) || $.trim(tradein) == '' || parseInt(tradein) === NaN || parseInt(tradein) === undefined) tradein = 0;
		if (zipcode == '' || zipcode == null) zipcode = $('#dealerZip').val();
		if (zipcode == '' || zipcode == null) zipcode = 48302;

		if (methods == 'onload') {
			var dp = $('.ore_lease_msrp_price').text();
			var da = dp.replace('$', '').replace(',', '')
			down = parseInt(da / 10);
		}
		var the_lease_explores = new Array();
		$("input[name='lease_explores[]']:checked").each(function () {
			the_lease_explores.push(($(this).val()));
		});

		if (methods == 'available_offers') {

			// if ($('#avail_pgm_loyalty').prop('checked')) {
			// 	the_lease_explores.push(($('#explores_Lease-conquest').val()));
			// 	$('#explores_Lease-conquest').prop('checked', true);
			// } else {
			// 	var lease_loy_index = the_lease_explores.indexOf(parseInt($('#explores_Lease-conquest').val()));
			// 	if (lease_loy_index > -1) the_lease_explores.splice(lease_loy_index, 1);
			// 	$('#explores_Lease-conquest').prop('checked', false);
			// }

			// if ($('#avail_pgm_lease_loyalty').prop('checked')) {
			// 	the_lease_explores.push(($('#explores_Lease-loyalty').val()));
			// 	$('#explores_Lease-loyalty').prop('checked', true);
			// } else {
			// 	var lease_lloy_index = the_lease_explores.indexOf(parseInt($('#explores_Lease-loyalty').val()));
			// 	if (lease_lloy_index > -1) the_lease_explores.splice(lease_lloy_index, 1);
			// 	$('#explores_Lease-loyalty').prop('checked', false);
			// }

			if ($('#avail_pgm_chrysler_capital_incentives').prop('checked')) {
				the_lease_explores.push(($('#explores_Chrysler-capital-incentives').val()));
				$('#explores_Chrysler-capital-incentives').prop('checked', true);
			} else {
				var lease_loy_index = the_lease_explores.indexOf(parseInt($('#explores_Chrysler-capital-incentives').val()));
				if (lease_loy_index > -1) the_lease_explores.splice(lease_loy_index, 1);
				$('#explores_Chrysler-capital-incentives').prop('checked', false);
			}

			//console.log('lease inc');
			// add new 
			// if ($('#avail_pgm_loyalty').prop("checked") == true && $('#avail_pgm_lease_loyalty').prop("checked") == false) {
			// 	the_lease_explores.push(($('#explores_Lease-loyalty').val()));
			// 	$('#explores_Lease-loyalty').prop('checked', true);
			// 	$('#explores_Lease-conquest').prop('checked', false);
			// 	var lcond1 = the_lease_explores.indexOf(parseInt($('#explores_Lease-conquest').val()));
			// 	if (lcond1  > -1) the_lease_explores.splice(lcond1, 1);
			// } 

			
			// if ($('#avail_pgm_loyalty').prop("checked") == false && $('#avail_pgm_lease_loyalty').prop("checked") == true) {

			// 	the_lease_explores.push(($('#explores_Lease-conquest').val()));
			// 	$('#explores_Lease-loyalty').prop('checked', false);
			// 	$('#explores_Lease-conquest').prop('checked', true);

			// 	var lcond2 = the_lease_explores.indexOf(parseInt($('#explores_Lease-loyalty').val()));
			// 	if (lcond2  > -1) the_lease_explores.splice(lcond2, 1);
			// }
				

			// if ($('#avail_pgm_loyalty').prop("checked") == true && $('#avail_pgm_lease_loyalty').prop("checked") == true) {

			// 	the_lease_explores.push(($('#explores_Lease-loyalty').val()));
			// 	$('#explores_Lease-loyalty').prop('checked', true);
			// 	$('#explores_Lease-conquest').prop('checked', false);
			// 	var lcond3 = the_lease_explores.indexOf(parseInt($('#explores_Lease-conquest').val()));
			// 	if (lcond3  > -1) the_lease_explores.splice(lcond3, 1);
			// }

			// if ($('#avail_pgm_loyalty').prop("checked") == false && $('#avail_pgm_lease_loyalty').prop("checked") == false) {

			// 	//the_lease_explores.push(($('#explores_Lease-conquest').val()));
			// 	$('#explores_Lease-loyalty').prop('checked', false);
			// 	$('#explores_Lease-conquest').prop('checked', false);
			// 	var lcond4 = the_lease_explores.indexOf(parseInt($('#explores_Lease-conquest').val()));
			// 	var lcond5 = the_lease_explores.indexOf(parseInt($('#explores_Lease-loyalty').val()));
			// 	if (lcond4  > -1) the_lease_explores.splice(lcond4, 1);
			// 	if (lcond5  > -1) the_lease_explores.splice(lcond5, 1);

			// }

			if ($('#avail_pgm_loyalty').prop("checked") == true) {
				the_lease_explores.push(($('#explores_Lease-loyalty').val()));
				$('#explores_Lease-loyalty').prop('checked', true);
				$('#explores_Lease-conquest').prop('checked', false);
				$('#explores_Conquest').prop('checked', false);
				
				var cond4 = the_lease_explores.indexOf(($('#explores_Lease-conquest').val()));
				if (cond4 > -1) the_lease_explores.splice(cond4, 1);
				var cond41 = the_lease_explores.indexOf(($('#explores_Conquest').val()));
				if (cond41 > -1) the_lease_explores.splice(cond41, 1);
			}else{
				the_lease_explores.push(($('#explores_Lease-conquest').val()));
				the_lease_explores.push(($('#explores_Conquest').val()));
				$('#explores_Lease-loyalty').prop('checked', false);
				$('#explores_Lease-conquest').prop('checked', true);
				$('#explores_Conquest').prop('checked', true);
				
				var cond4 = the_lease_explores.indexOf(($('#explores_Lease-loyalty').val()));
				if (cond4 > -1) the_lease_explores.splice(cond4, 1);
			}

			$("input[name='lease_explores[]']:not(:checked)").each(function () {
				 lease_loy_index = the_lease_explores.indexOf($(this).val());
				if (lease_loy_index > -1) the_lease_explores.splice(lease_loy_index, 1);
			});

		}

		var dealer_discs = parseInt(dealer_disc) + parseInt(down);

		ajax.promise('payment-calcultor', 'post', JSON.stringify({ 'vin': vin, 'transactionType': transactionType, 'zipcode': zipcode, 'down': dealer_discs, 'tradein': tradein, 'term': term, 'rebateIDs': the_lease_explores, 'mileage': mileage, 'methods': methods, 'finance_type': finance_type, 'dlr_code': dealer_code, 'dealer_disc': dealer_disc,'lease_additional_disc':lease_additional_disc }));

	},
	payment_finance_downpercent: function () {
		var msrp = $('#F_MSRPHidden').val();
		var destination = 0;
		var total_price = parseInt(msrp) + parseInt(destination);
		var dpayment = $('#financeDownPaymentField').val();
		var percent = parseFloat((dpayment / total_price) * 100).toFixed(2);
		$('.dPaymentRange').text(percent);
	},
	payment_finance: function (transactionType, methods) {
		var vin = $('#vin').val();
		var zipcode = localCache.get('zipCodeEntered');
		var f_dealer_disc = 0;
		var finance_additional_disc = $('#finance_additional_disc').val();
		var down = $('#financeDownPaymentField').val();
		var tradein = $('#financeTradeInValueField').val();
		var term = $("#finance_terms").val();
		var dealer_code = $('#dealer_code').val();
		var finance_type = $("input[name='f_finance_type']:checked").val();
		if (finance_type == "") finance_type = "ChryslerCapital";

		if (zipcode == '' || zipcode == null) zipcode = $('#dealerZip').val();
		if (zipcode == '' || zipcode == null) zipcode = 48302;

		if (isNaN(f_dealer_disc) || $.trim(f_dealer_disc) == '' || parseInt(f_dealer_disc) === NaN || parseInt(f_dealer_disc) === undefined) f_dealer_disc = 0;
		 if (isNaN(finance_additional_disc) || $.trim(finance_additional_disc) == '' || parseInt(finance_additional_disc) === NaN || parseInt(finance_additional_disc) === undefined) finance_additional_disc = 0;
		if (isNaN(down) || $.trim(down) == '' || parseInt(down) === NaN || parseInt(down) === undefined) down = 0;
		if (isNaN(tradein) || $.trim(tradein) == '' || parseInt(tradein) === NaN || parseInt(tradein) === undefined) tradein = 0;

		if (methods == 'onload') {
			var dp1 = $('.ore_finance_msrp_price').text();
			var da1 = dp1.replace('$', '').replace(',', '')
			down = parseInt(da1 / 10);
			term = '';
		}
		var the_finance_explores = []
		$("input[name='finance_explores[]']:checked").each(function () {
			the_finance_explores.push(($(this).val()));
		});
		//Previous Available Program carry out to Payment Calc
		if (methods == 'available_offers') {
			// if ($('#avail_pgm_loyalty').prop('checked')) {
			// 	the_finance_explores.push(($('#finace_explores_Lease-conquest').val()));
			// 	$('#finace_explores_Lease-conquest').prop('checked', true);
			// } else {
			// 	var fianance_loy_index = the_finance_explores.indexOf(parseInt($('#explores_Lease-conquest').val()));
			// 	if (fianance_loy_index > -1) the_finance_explores.splice(fianance_loy_index, 1);
			// 	$('#finace_explores_Lease-conquest').prop('checked', false);
			// }

			// if ($('#avail_pgm_lease_loyalty').prop('checked')) {
			// 	the_finance_explores.push(($('#finace_explores_Lease-loyalty').val()));
			// 	$('#finace_explores_Lease-loyalty').prop('checked', true);
			// } else {
			// 	var fianance_lloy_index = the_finance_explores.indexOf(parseInt($('#explores_Lease-loyalty').val()));
			// 	if (fianance_lloy_index > -1) the_finance_explores.splice(fianance_lloy_index, 1);
			// 	$('#finace_explores_Lease-loyalty').prop('checked', false);
			// }

			if ($('#avail_pgm_chrysler_capital_incentives').prop('checked')) {
				the_finance_explores.push(($('#finace_explores_Chrysler-capital-incentives').val()));
				$('#finace_explores_Chrysler-capital-incentives').prop('checked', true);
			} else {
				var fianance_lloy_index = the_finance_explores.indexOf(parseInt($('#finace_explores_Chrysler-capital-incentives').val()));
				if (fianance_lloy_index > -1) the_finance_explores.splice(fianance_lloy_index, 1);
				$('#finace_explores_Chrysler-capital-incentives').prop('checked', false);
			}
			///// new addedd  
           
			// if ($('#avail_pgm_loyalty').prop("checked") == true && $('#avail_pgm_lease_loyalty').prop("checked") == false) {
			// 	the_finance_explores.push(($('#finace_explores_Lease-loyalty').val()));
			// 	$('#finace_explores_Lease-loyalty').prop('checked', true);
			// 	$('#finace_explores_Lease-conquest').prop('checked', false);
			// 	var cond1 = the_finance_explores.indexOf(parseInt($('#finace_explores_Lease-conquest').val()));
			// 	if (cond1 > -1) the_finance_explores.splice(cond1, 1);
			// }
			

			// if ($('#avail_pgm_loyalty').prop("checked") == true && $('#avail_pgm_lease_loyalty').prop("checked") == true) {

			// 	the_finance_explores.push(($('#finace_explores_Lease-loyalty').val()));
			// 	$('#finace_explores_Lease-loyalty').prop('checked', true);
			// 	$('#finace_explores_Lease-conquest').prop('checked', false);
			// 	var cond3 = the_finance_explores.indexOf(parseInt($('#finace_explores_Lease-conquest').val()));
			// 	if (cond3 > -1) the_finance_explores.splice(cond3, 1);
			// }
			

			// if ($('#avail_pgm_loyalty').prop("checked") == false && $('#avail_pgm_lease_loyalty').prop("checked") == true) {
			// 	the_finance_explores.push(($('#finace_explores_Lease-conquest').val()));
			// 	$('#finace_explores_Lease-loyalty').prop('checked', false);
			// 	$('#finace_explores_Lease-conquest').prop('checked', true);
			// 	var cond4 = the_finance_explores.indexOf(parseInt($('#finace_explores_Lease-loyalty').val()));
			// 	if (cond4 > -1) the_finance_explores.splice(cond4, 1);
			// }

			// if ($('#avail_pgm_loyalty').prop("checked") == false && $('#avail_pgm_lease_loyalty').prop("checked") == false) {

			// 	the_finance_explores.push(($('#finace_explores_Lease-conquest').val()));
			// 	$('#finace_explores_Lease-loyalty').prop('checked', false);
			// 	$('#finace_explores_Lease-conquest').prop('checked', false);
			// 	var cond2 = the_finance_explores.indexOf(parseInt($('#finace_explores_Lease-loyalty').val()));
			// 	if (cond2 > -1) the_finance_explores.splice(cond2, 1);
			// 	var cond5 = the_finance_explores.indexOf(parseInt($('#finace_explores_Lease-conquest').val()));
			// 	if (cond5 > -1) the_finance_explores.splice(cond5, 1);
			// }


			if ($('#avail_pgm_loyalty').prop("checked") == true) {
				the_finance_explores.push(($('#finace_explores_Lease-loyalty').val()));
				$('#finace_explores_Lease-loyalty').prop('checked', true);
				$('#finace_explores_Lease-conquest').prop('checked', false);
				$('#finace_explores_Conquest').prop('checked', false);

				var cond4 = the_finance_explores.indexOf(($('#finace_explores_Lease-conquest').val()));
				if (cond4 > -1) the_finance_explores.splice(cond4, 1);
				var cond41 = the_finance_explores.indexOf(($('#finace_explores_Conquest').val()));
				if (cond41 > -1) the_finance_explores.splice(cond41, 1);
			}else{
				the_finance_explores.push(($('#finace_explores_Lease-conquest').val()));
				the_finance_explores.push(($('#finace_explores_Conquest').val()));
				$('#finace_explores_Lease-loyalty').prop('checked', false);
				$('#finace_explores_Lease-conquest').prop('checked', true);
				$('#finace_explores_Conquest').prop('checked', true);
				
				var cond4 = the_finance_explores.indexOf(($('#finace_explores_Lease-loyalty').val()));
				if (cond4 > -1) the_finance_explores.splice(cond4, 1);
			}

			$("input[name='finance_explores[]']:not(:checked)").each(function () {			 
				 finance_loy_index = the_finance_explores.indexOf($(this).val());
				if (finance_loy_index > -1) the_finance_explores.splice(finance_loy_index, 1);
			});

		}
		var dealer_discs = parseInt(f_dealer_disc) + parseInt(down);
		$("input.finance_chk").attr("disabled", "disabled");
		ajax.promise('payment-calcultor', 'post', JSON.stringify({ 'vin': vin, 'transactionType': transactionType, 'zipcode': zipcode, 'down': dealer_discs, 'tradein': tradein, 'term': term, 'rebateIDs': the_finance_explores, 'mileage': 0, 'methods': methods, 'finance_type': finance_type, 'dlr_code': dealer_code, 'dealer_disc': f_dealer_disc,'finance_additional_disc':finance_additional_disc  }));

	},
	payment_cash: function (transactionType, methods) {
		var vin = $('#vin').val();
		var zipcode = $('#dealerZip').val();
		var tradein = $('#cashTradeInValueField').val();
		var dealer_code = $('#dealer_code').val();
		 var cash_additional_disc =$('#cash_additional_disc').val();

		tradein = tradein.replace(/[,.]/g, '');
		if (isNaN(tradein) || $.trim(tradein) == '' || parseInt(tradein) === NaN || parseInt(tradein) === undefined) tradein = 0;
		 if (isNaN(cash_additional_disc) || $.trim(cash_additional_disc) == '' || parseInt(cash_additional_disc) === NaN || parseInt(cash_additional_disc) === undefined) cash_additional_disc = 0;

		if (zipcode == '' || zipcode == null) zipcode = $('#dealerZip').val();
		if (zipcode == '' || zipcode == null) zipcode = 48302;

		var the_cash_explores = []
		$("input[name='cash_explores[]']:checked").each(function () {
			the_cash_explores.push(($(this).val()));
		});
		//Previous Available Program carry out to Payment Calc
		if (methods == 'available_offers') {

			// if ($('#avail_pgm_loyalty').prop('checked')) {
			// 	the_cash_explores.push(($('#cash_explores_Loyalty').val()));
			// 	$('#cash_explores_Loyalty').prop('checked', true);
			// } else {

			// 	var cash_loy_index = the_cash_explores.indexOf(parseInt($('#explores_Loyalty').val()));
			// 	if (cash_loy_index > -1) the_cash_explores.splice(cash_loy_index, 1);
			// 	$('#cash_explores_Loyalty').prop('checked', false);
			// }

			// if ($('#avail_pgm_lease_loyalty').prop('checked')) {
			// 	the_cash_explores.push(($('#cash_explores_Lease-loyalty').val()));
			// 	$('#cash_explores_Lease-loyalty').prop('checked', true);
			// } else {
			// 	var cash_lloy_index = the_cash_explores.indexOf(parseInt($('#explores_Lease-loyalty').val()));
			// 	if (cash_lloy_index > -1) the_cash_explores.splice(cash_lloy_index, 1);
			// 	$('#cash_explores_Lease-loyalty').prop('checked', false);
			// }
		
		

			// add new 
			// if ($('#avail_pgm_loyalty').prop("checked") == true && $('#avail_pgm_lease_loyalty').prop("checked") == false) {

			// 	the_cash_explores.push(($('#cash_explores_Lease-loyalty').val()));
			// 	$('#cash_explores_Lease-loyalty').prop('checked', true);
			// 	$('#cash_explores_Lease-conquest').prop('checked', false);
			// 	var ccond1 = the_cash_explores.indexOf(($('#cash_explores_Lease-conquest').val()));
			// 	if (ccond1 > -1) the_cash_explores.splice(ccond1, 1);

			// }
			// if ($('#avail_pgm_loyalty').prop("checked") == false && $('#avail_pgm_lease_loyalty').prop("checked") == false) {
			// 	the_cash_explores.push(($('#cash_explores_Lease-conquest').val()));
			// 	$('#cash_explores_Lease-loyalty').prop('checked', false);
			// 	$('#cash_explores_Lease-conquest').prop('checked', false);
			// 		var ccond2 = the_cash_explores.indexOf(($('#cash_explores_Lease-loyalty').val()));
			// 	if (ccond2 > -1) the_cash_explores.splice(ccond2, 1);
			// 	var ccond5 = the_cash_explores.indexOf(($('#cash_explores_Lease-conquest').val()));
			// 	if (ccond5 > -1) the_cash_explores.splice(ccond5, 1);

			// }

			// if ($('#avail_pgm_loyalty').prop("checked") == true && $('#avail_pgm_lease_loyalty').prop("checked") == true) {
			// 	the_cash_explores.push(($('#cash_explores_Lease-loyalty').val()));
			// 	$('#cash_explores_Lease-loyalty').prop('checked', true);
			// 	$('#cash_explores_Lease-conquest').prop('checked', false);
			// 	var ccond3 = the_cash_explores.indexOf(($('#cash_explores_Lease-conquest').val()));
			// 	if (ccond3 > -1) the_cash_explores.splice(ccond3, 1);

			// }

			// if ($('#avail_pgm_loyalty').prop("checked") == false && $('#avail_pgm_lease_loyalty').prop("checked") == true) {

			// 	the_cash_explores.push(($('#cash_explores_Lease-conquest').val()));
			// 	$('#cash_explores_Lease-loyalty').prop('checked', false);
			// 	$('#cash_explores_Lease-conquest').prop('checked', true);
			// 	var ccond4 = the_cash_explores.indexOf(($('#cash_explores_Lease-loyalty').val()));
			// 	if (ccond4 > -1) the_cash_explores.splice(ccond4, 1);

			// }
			////add new 

						if ($('#avail_pgm_loyalty').prop("checked") == true) {
							the_cash_explores.push(($('#cash_explores_Lease-loyalty').val()));
							$('#cash_explores_Lease-loyalty').prop('checked', true);
							$('#cash_explores_Lease-conquest').prop('checked', false);
							$('#cash_explores_Conquest').prop('checked', false);
							
							var cond4 = the_cash_explores.indexOf(($('#cash_explores_Lease-conquest').val()));
							if (cond4 > -1) the_cash_explores.splice(cond4, 1);
							var cond41 = the_cash_explores.indexOf(($('#ecash_xplores_Conquest').val()));
							if (cond41 > -1) the_cash_explores.splice(cond41, 1);
						}else{
							the_cash_explores.push(($('#cash_explores_Lease-conquest').val()));
							the_cash_explores.push(($('#cash_explores_Conquest').val()));
							$('#cash_explores_Lease-loyalty').prop('checked', false);
							$('#cash_explores_Lease-conquest').prop('checked', true);
							$('#cash_explores_Conquest').prop('checked', true);
							
							var cond4 = the_cash_explores.indexOf(($('#cash_explores_Lease-loyalty').val()));
							if (cond4 > -1) the_cash_explores.splice(cond4, 1);
						}

						$("input[name='cash_explores[]']:not(:checked)").each(function () {
							cash_loy_index = the_cash_explores.indexOf($(this).val());
							if (cash_loy_index > -1) the_cash_explores.splice(cash_loy_index, 1);
						});

		}

		$("input.cash_chk").attr("disabled", "disabled");
		ajax.promise('payment-calcultor', 'post', JSON.stringify({ 'vin': vin, 'transactionType': transactionType, 'zipcode': zipcode, 'down': 0, 'tradein': tradein, 'term': 0, 'rebateIDs': the_cash_explores, 'mileage': 0, 'methods': methods, 'dlr_code': dealer_code,'cash_additional_disc':cash_additional_disc   }));

	},
	doAjax: function (url, requestType, data) {
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
				'X-Frame-Options': 'sameorigin',
				'X-Content-Type-Options': 'nosniff',
			}
		});
		return $.ajax({
			url: url,
			type: requestType,
			contentType: "application/json",
			dataType: "json",
			data: data
		});
	},
	addCommas: function (nStr) {
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	},
	isValidEmail: function (email) {
		if ($.trim(email) == "") return "E-mail should be required.";
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if (!regex.test(email)) return "E-Mail should be valid."; else return 'ok';
	},
	isValidName: function (name, key) {
		if ($.trim(name) == "") return key + " should be required.";
		var regex = /^[a-zA-Z .]{0,50}$/;
		if (!regex.test(name)) return key + " should be valid."; else return 'ok';
	},
	isValidZip: function (zip) {
		if ($.trim(zip) == "") return "Zip code should be required.";
		var regex = /^[0-9]{0,5}$/;
		if (!regex.test(zip)) return "Zip code be valid."; else return 'ok';

	},
} 
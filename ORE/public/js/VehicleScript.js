$(document).ready(function () {
	vehiclepage.init();

	if (location.hash == '#vehicleInfo') {
		location.hash = '#vehicleInformation';
	}

	if (location.hash) {
		setTimeout(function () {
			window.scrollTo(0, 0);
		}, 1);
	}

});
 
function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return; var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

var vehiclepage = {
	getUrlParams: function getUrlParams(search) {
    var hashes = search.slice(search.indexOf('?') + 1).split('&');
    return hashes.reduce(function (params, hash) {
      var _hash$split = hash.split('='),
          _hash$split2 = _slicedToArray(_hash$split, 2),
          key = _hash$split2[0],
          val = _hash$split2[1];

      return Object.assign(params, _defineProperty({}, key, decodeURIComponent(val)));
    }, {});
},

	init: function () {
		var appurl = $('#APP_URL').val();
		var tier = $('#tier').val();
		var year = $('#year').val();
		var model = $('#model').val();
		var getParams = vehiclepage.getUrlParams(window.location.search);
		if(getParams['zipcode'] !== undefined) localCache.set('zipCodeEntered', getParams['zipcode']); 

		/**** initial Lead ********/
		var initialPopup = $("#initialPopup").val();

		if (initialPopup == "show") {
			$('#initialPopUp').modal('show');
			$('.initial_popup_header_year').html(year);
			$('.initial_popup_header_trim').html(model);
			if(localCache.exist('zipCodeEntered')) {
				$('#init_pop_postalcode, #postalcode').val('');
				$('#init_pop_postalcode, #postalcode').val(localCache.get('zipCodeEntered'));
				
			}
			
			 
			//console.log(window.location.getParameter('zipcode'));
		}
		// vehiclepage.color_switch(); 

		$(document).on('click', '.ore_initialpopup', function () {

			var init_pop_first = $('#init_pop_first').val();
			var init_pop_last = $('#init_pop_last').val();
			var init_pop_postalcode = $('#init_pop_postalcode').val();
			var init_pop_contact_email = $('#init_pop_contact_email').val();
			var init_pop_contact_phone = $('#init_pop_contact_phone').val();
			var init_pop_tier = $('#tier').val();
			var init_pop_adobe_session = _satellite.getVisitorId().getMarketingCloudVisitorID();
			var init_chk_box_home_delivery = $('input[name="initial_chk_box_home_delivery"]:checked').val();
			init_chk_box_home_delivery = (init_chk_box_home_delivery == 'on') ? true : false;
			$(this).addClass('disabled');
			$(this).attr("disabled", "disabled");
			$(this).val('Saving...');

			canSubmit = attr_valid('init');

			if (canSubmit) {
				ajax.promise('initial_lead', 'post', JSON.stringify({ 'first': init_pop_first, 'last': init_pop_last, 'postalcode': init_pop_postalcode, 'contact_email': init_pop_contact_email, 'contact_phone': init_pop_contact_phone, 'chk_box_home_delivery' : init_chk_box_home_delivery, 'tier': init_pop_tier, 'adobe_session': init_pop_adobe_session }));
			} else {
				$('.ore_initialpopup').removeClass('disabled');
				$('.ore_initialpopup').removeAttr("disabled", "disabled");
				$('.ore_initialpopup').val('Save & Continue');
				return false;
			}

		});

		/**** initial Lead ********/
		$('.popUpScroll .dOpener').click(function () {
			$(this).siblings().toggleClass('hideMe transition-height');
			$(this).children('i').toggleClass('glyphicon-menu-down glyphicon-menu-up');
		});
		vehiclepage.navigation();
		vehiclepage.stylePicker();

		var button_common_class = 'btn size-15 rBold nav-back-btn mRight-3 gcss-button-secondary ';
		$(document).on('click', '.listBlock>.nav-tabs>li>a', function () {
			$($(this).attr('href')).addClass('active');

		});

		if (tier == 'ore') activeTier = 'Standalone'; else activeTier = tier;

		if (tier == 't1' || tier == 't3') {
			$('.top-logo-vehicle').attr("href", "javascript: void(0);");
		}

		$('body').on('click', '.ore_vehicle_vin, .footerBtns>a.dealer, .fwd-bck-btns>button, #submitBtn, .ore_leadsubmit', function () {
			var textValue = $.trim($(this).val())//vehiclepage.alias(,"");
			var linkText = activeTier + ':DriveFCA:' + $('#make').val() + ':' + $('#model').val() + ':' + $('#year').val() + ':' + $('#vehicle_type').val() + ':us:leadsubmit' + ':' + textValue;
			button_position = 'Bottom: ';
			mAnalystic.CTA(button_position + linkText);

		});

		$('body').on('click', '.tier13_closed', function () {
			vehiclepage.CloseWebPage();
		});



		$('body').on('click', '.btm_explore', function () {
			mAnalystic.CTA(activeTier + ':' + 'bottom: ' + 'DriveFCA:' + $('#make').val() + ':' + $('#model').val() + ':' + $('#year').val() + ':' + $('#vehicle_type').val() + ':us:leadsubmit' + ':' + $(this).text());
		});
		/* Disclaimer message Start*/
		$('.disclaimerMessage').hide();
		$(document).on('click', '#d_q_msrp,#d_q_chrysler,#d_q_thirdparty,#d_q_cost,#l_q_msrp,#l_q_chrysler,#l_q_thirdparty,#l_q_cost,#l_q_costSecond,#cs_q_msrp,#cs_q_cost,#l_q_financeest,#l_q_mpg,#l_q_would,#l_q_wouldreview', function () {
			var cid = $(this).attr('id');
			var replace_msg_id = cid.replace("_q_", "_m_");
			$('.disclaimerMessage').slideUp();
			$('#' + replace_msg_id).slideDown();

		});
		
		$(document).on('click', '.l_q_insentive,.l_q_fininsentive,.l_q_cashinsentive', function () {
			 
			var cid = $(this).attr('id'); 
			num=$(this).attr('data-num');
			var replace_msg_id = cid.replace("_q_", "_m_");
			var ortext='';
			// ortext =$('#'+replace_msg_id).find("p").text();
			// ortext= ortext.replace(/\d.+/,'');
			// reptext=ortext.replace("date", "12-12-2020");
			reptext=$(this).attr('data-disclamer');
			//console.log('data-disclamer',reptext);
			text= num +'.'+reptext;
			console.log('text1',text);
			$('#' + replace_msg_id).find("p").text(text);
			$('.disclaimerMessage').slideUp();
			$('#' + replace_msg_id).slideDown();
			 
		});


		$(document).on('click', '.closeDisclaimer', function () {
			var cid = $(this).attr('id');
			var replace_close_id = cid.replace("_c_", "_m_");
			console.log('replace_close_id',replace_close_id);
			$('#' + replace_close_id).slideUp();
		});
		/* Disclaimer message End*/

		$(window).on('load', function () {
			$(function () {
				$(window).bind('hashchange', function () {
					$('a[href^="' + location.hash + '"]')
						.parent()
						.siblings().removeClass('selected').end()
						.next('dd').addBack().addClass('selected');
					$('[href="' + location.hash + '"]').click();

					if (location.hash == '#explore_finance') {
						$('#review, #vehicleInfo, #tradeIn').hide();
						$('.iframe-field, .btm_submitdealer').show();
						$('.explore-review').text('Review and Submit');
						$('.explore-review').removeClass('explore-finance');
						$('.explore-review').addClass('review-submit-btn');
						$('#tab_review').parent().addClass('active selected');
					} else if (location.hash == '#tradeIn') {
						$('#review, #vehicleInfo, #serviceProtection, .btm_submitdealer').hide();
						$('#tradeIn').show();
					} else if (location.hash == '#review') {
						$('#tradeIn, .tab-pane.vehicleTable, #serviceProtection').hide();
						$('#tradeIn, #vehicleInfo, #serviceProtection').hide();
						$('#review, #review #vehicleInfo, .btm_submitdealer').show();
					} else if (location.hash == '#serviceProtection') {
						$('#serviceProtection').show();
						$('#tradeIn, #vehicleInfo, #review, .btm_submitdealer').hide();
					} else if (location.hash == '#vehicleInformation') {
						$('#vehicleInfo').show();
						$('#vehicleInfo').addClass('in');
						$('#tradeIn, #serviceProtection, #review, .btm_submitdealer').hide();
					} else if (location.hash == '') {
						$('.cn-b13-chat').click();
						window.history.back();
					}


				});
				if (location.hash == '') {
					jQuery("html, body").animate({ scrollTop: 0 }, 1500);
					location.hash = '#vehicleInformation';
				}
				$(window).trigger('hashchange');
			});
		});

		$(function () {
			var hash = window.location.hash;
			hash && $('.detrailHeader ul.nav a[href="' + hash + '"]').tab('show');

			$('.detrailHeader .nav-tabs a').click(function (e) {
				$(this).tab('show');
				var scrollmem = $('body').scrollTop() || $('html').scrollTop();
				window.location.hash = this.hash;
				$('html,body').scrollTop(scrollmem);
			});
		});

		// TRADE IN
		$('.ore_tradeIn').on('click', function () {
			$('.nav-back-btn').show();
			$('.nav-back-btn').attr('class', button_common_class + 'backto-vehicle-info');
			$('.nav-back-btn').text('PREV: Vehicle Info');
			$('#submitBtn, .explore-review, .iframe-field, #review').hide();
			vehiclepage.pageinfoUpdate('tradeIn');
			vehiclepage.saveInfoToCookie();
			vehiclepage.tradeInLoader();
		});

		// MOPHAR
		vehiclepage.mopharSelector();
		$('.ore_serviceProtection').on('click', function () {
			vehiclepage.pageinfoUpdate('serviceProtection');
			vehiclepage.saveInfoToCookie();
			$('.nav-back-btn').show();
			$('.iframe-field').hide();
			$('.nav-back-btn').text('PREV: Trade In');
			$('.nav-back-btn').attr('class', button_common_class + 'backto-trade-in');
			$('#serviceProtection .backto-trade-in').addClass('from-bottom');
			$('#submitBtn, .explore-review, #review').hide();

		});




		// REVIEW
		$(document).on('click', '.legendStyle>li>a#tab_review', function (evt) {

			$('.nav-back-btn, .hide-on-review-tab').hide();
			$('.nav-back-btn').text('PREV: Service and Protection');
			$('.nav-back-btn').attr('class', button_common_class + 'backto-service');
			$('.explore-review, #submitBtn').show();
			if ($('.explore-review').hasClass('review-submit-btn')) {
				$('.iframe-field').show();
			}

			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
					'X-Frame-Options': 'sameorigin'
				}
			});
			var appurl = $("#APP_URL").val();
			var vin = $("#vin").val();
			$.get(appurl + 'review' + "?vin=" + vin, function (returnedData) {
				$('#ore_review').html(returnedData);
				vehiclepage.setHeight();
				if ($('#submitBtn').hasClass('disabled')) {
					$('.submittodealer_wrapper .btm_submitdealer').addClass('disabled');
					$('.submittodealer_wrapper .btm_submitdealer').attr("disabled", "disabled");
				}
			});
		});

		$(document).on('click', '.fa-refresh', function () {
			var appurl = $("#APP_URL").val();
			$(".captcha-image").attr("src", appurl + 'image_color/load?' + Date.now())
		});

		// MODELS
		$(document).on('click', '.modalOpener', function () {
			var popUpName = $(this).data('target');
			var popup_vin = $(this).data('vin');
			var popup_model = $(this).data('model');
			var popup_msrp = $(this).data('msrp');
			var popup_year = $(this).data('year');

			$('.mod_title').html(popup_year + ' ' + popup_model);
			$('.mod_vin').html(popup_vin);
			$('.mod_msrp').html(popup_msrp);
			
			$(popUpName).modal('show');
		});
		$(document).on('click', '.modalOpener2', function () {
			var popUpName = $(this).data('target');
			$(popUpName).modal('show');
		});

		// NAVIGATOR
		$('body').on('click', '.vehicleInfo', function () {
			$('.explore-review, .nav-back-btn, .iframe-field, #review').hide();

		});
		$('body').on('click', '.backto-vehicle-info', function () {

			$('.vehicleInfo').click();

		});
		$('body').on('click', '.backto-trade-in', function () {
			$('.ore_tradeIn').click();
		});
		$('body').on('click', '.backto-service', function () {
			$('.ore_serviceProtection').click();
		});


		$('body').on('click', '.explore-finance', function () {
			$('#review').hide();
			$('.iframe-field').show();
			$('.explore-review').text('Review and Submit');
			$('.explore-review').removeClass('explore-finance');
			$('.explore-review').addClass('review-submit-btn');
		});
		$('body').on('click', '.review-submit-btn, #tab_review', function () {
			$('.iframe-field, #vehicleInfo').hide();
			$('#review, #review #vehicleInfo').show();
			$('.explore-review').text('Explore Financing Options');
			$('.explore-review').removeClass('review-submit-btn');
			$('.explore-review').addClass('explore-finance');
		});
		if ($(window).width() < 768) {
			jQuery("html, body").animate({ scrollTop: $('header').height() + 30 }, 2500);
		}
		// Auto scroll functionality start

		// jQuery(".legendStyle a:not(.vehicleInfo)").click(function () {
		//   jQuery("html, body").animate({scrollTop: $('.detailBody .row:first-child').height() + $('.detrailHeader').height()}, 700);
		// });

		// Auto scroll functionality end

		$('body').on('click', '.redirect-home-link', function () {
			var redirectLink = $('.thanks-click').attr('href');
			var tier_platform = $('#tier').val();
			var re = $('#APP_URL').val();
			var make =$('#make').val();
			reurl= (tier_platform == 't1') ? re+make+"/"+tier_platform : re+make;
			window.location.href = reurl;

			//window.location.replace(redirectLink);
		});

		$('header').addClass('vehiclepage');

		$('.stylePicker .floatingIcon').click(function () {
			$('.stylePicker').toggleClass('active');
			$(this).toggleClass('fa-arrow-circle-up fa-arrow-circle-down');
		});

		$('.stylePicker .floatingIcon').click(function () {
			$('.stylePicker').toggleClass('active');
			$(this).toggleClass('fa-arrow-circle-up fa-arrow-circle-down');
		});
		$('.stylePicker ul li').click(function () {
			var APP = $('#APP_URL').val();
			var cPath = APP + "css/" + $(this).attr('data-style-sheet').trim();
			$(this).parent().children('.active').removeClass('active');
			$(this).addClass('active');
			$('#styleLoader').attr('href', cPath);
		});
		$('.explore-finance').click(function () {
			$('#explore_finance').click();
			$('#tab_review').parent().addClass('active selected');
		});

		$('body').addClass('vehiclepage-body');

		$('.current-toggle-tab input[type="checkbox"]').change(function () {
			if ($(this).prop("checked") == true) {
				$('.current_tab_title, .slide-ex').text('Finance');
				$('#tab_finance').click();
				$('.current-toggle-tab input[type="checkbox"]').prop('checked', true);
			}
			else if ($(this).prop("checked") == false) {
				$('.current_tab_title, .slide-ex').text('Lease');
				$('#tab_lease').click();
				$('.current-toggle-tab input[type="checkbox"]').prop('checked', false);
			}
		});

		$('#tab_finance').click(function (e) {
			if (e.hasOwnProperty('originalEvent')) {
				if ($('#serviceProtection .slide-ex').text() == "Lease") {
					$('.serviceprot-submit-toggle input[type="checkbox"]').click();
				}
			}
		});
		$('#tab_lease').click(function (e) {
			if (e.hasOwnProperty('originalEvent')) {
				if ($('#serviceProtection .slide-ex').text() == "Finance") {
					$('.serviceprot-submit-toggle input[type="checkbox"]').click();
				}
			}
		});

		$('#reviewSubmitInitialPopUp input').on('input', function (e) {
			if ($(this).val() == '') {
				$(this).siblings('.placeholder').show();
			} else {
				$(this).siblings('.placeholder').hide();
			}
		});

		$('#init_pop_postalcode').on('focusout', function (e) {
			if ($(this).val() == '') {
				$(this).siblings('.placeholder').show();
			} else {
				$(this).siblings('.placeholder').hide();
			}
		});

		// Restricts input for each element in the set of matched elements to the given inputFilter.
		(function ($) {
			$.fn.inputFilter = function (inputFilter) {
				return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function () {
					if (inputFilter(this.value)) {
						this.oldValue = this.value;
						this.oldSelectionStart = this.selectionStart;
						this.oldSelectionEnd = this.selectionEnd;
					} else if (this.hasOwnProperty("oldValue")) {
						this.value = this.oldValue;
						this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
					} else {
						this.value = "";
					}
				});
			};
		}(jQuery));


		// Install input filters.
		$("#init_pop_postalcode").inputFilter(function (value) {
			return /^\d*$/.test(value) && (value === "" || parseInt(value) <= 99999);
		});



	},
	color_switch: function () {
		/* switch (localCache.get("oremake")) {
                case "chrysler":
                    $("body").addClass("make_chrysler");
                    break;
                case "dodge":
                    $("body").addClass("make_dodge");
                    break;
                case "fiat":
                    $("body").addClass("make_fiat");
                    break;
                case "jeep":
                    $("body").addClass("make_jeep");
                    break;
                case "ram":
                    $("body").addClass("make_ram");
                    break;
                case "alfa_romeo":
                    $("body").addClass("make_alfa_romeo");
                    break;
            } */
	},
	invChat: function (sessionid, type, attributes) {
		ajax.promise('baseCarNow', 'post', JSON.stringify("session=" + sessionid + "&type=" + type + "&attributes=" + attributes));

	},
	setHeight: function () {
		if ($(window).width() > 991) {
			$('.vehicleTable .contentBlock').each(function () {
				var height = $(this).parent().closest('.row').height();
				$(this).css('height', height);
			});
		}
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
	pageinfoUpdate: function (pagename) {
		$.when(vehiclepage.doAjax('/update_pageinfo/' + pagename, 'get'))
			.done(function (response, textStatus, jqXHR) {

			}).fail(function (response, textStatus, jqXHR) {
			});
	},
	saveInfoToCookie: function () {
		$.when(vehiclepage.doAjax('/leadinfo', 'post'))
			.done(function (response, textStatus, jqXHR) {
				OreCookie.create('OreLead', response);
			}).fail(function (response, textStatus, jqXHR) {
			});
	},
	tradeInLoader: function () {
		//mAnalystic.tradeIntoolStart("lead",  "vendor trade in tool",  "inPage", "iframe", "BlackBook");

		var TradeInVeh = {};
		document.getElementById("OreTradeIn").innerHTML = "";

		var dealer_code = $('#dealer_code').val();

		var shoppingTools = _shoppingTools({
			elementId: 'OreTradeIn',
			appUrl: 'https://app.blackbookinformation.com',
			dealerid: '10150001'
		});



		shoppingTools.listen(function (data) {
			//Storing step 1 data
			if (data.eventName == "formSubmitted" && data.pageId == 1) {
				console.log(' == STEP 01 ==');
				TradeInVeh["year"] = data.data[0].year;
				TradeInVeh["make"] = data.data[0].make;
				TradeInVeh["model"] = data.data[0].model;
				TradeInVeh["series"] = data.data[0].series;
				TradeInVeh["style"] = data.data[0].style;
				TradeInVeh["mileage"] = data.data[0].mileage;
				TradeInVeh["zip"] = data.data[0].zip;
				TradeInVeh["condition"] = data.data[0].condition;
				OreCookie.create("tradeIn", JSON.stringify(TradeInVeh));
			}

			//Storing step 1 data
			if (data.eventName == "formSubmitted" && data.pageId == 1) {
				console.log(' == STEP 02 ==');
				TradeInVeh["year"] = data.data[0].year;
				TradeInVeh["make"] = data.data[0].make;
				TradeInVeh["model"] = data.data[0].model;
				TradeInVeh["series"] = data.data[0].series;
				TradeInVeh["style"] = data.data[0].style;
				TradeInVeh["mileage"] = data.data[0].mileage;
				TradeInVeh["zip"] = data.data[0].zip;
				TradeInVeh["condition"] = data.data[0].condition;
				OreCookie.create("tradeIn", JSON.stringify(TradeInVeh));
			}

			//Storing step 2 data
			if (data.eventName == "formSubmitted" && data.pageId == 2) {
				console.log(' == STEP 02 SAVE ==');
				console.log(data);
				TradeInVeh["options"] = {};
				$.each(data.data, function (key, value) {
					TradeInVeh["options"][key] = value.name;
				});
				OreCookie.create("tradeIn", JSON.stringify(TradeInVeh));
			}

			//Storing step 3 Price and sending ajax
			if (data.eventName == "formSubmitted" && data.pageId == 5 && data.step == 0) {
				console.log(' == STEP 03 ==');
				console.log(data);
				TradeInVeh["price"] = data.data[0].results.price;
				OreCookie.create("tradeIn", JSON.stringify(TradeInVeh));
				//sending ajax
				$.when(vehiclepage.doAjax('/userinfo_tradein', 'post', JSON.stringify(OreCookie.read_JSON("tradeIn"))))
					.done(function (response, textStatus, jqXHR) {
						//vehiclepage.update_tradein_amount(TradeInVeh["price"].replace("$", "").replace(",", ""));
					}).fail(function (response, textStatus, jqXHR) {
						console.log(textStatus);
					});
			}
			//Storing step 4 Remainingvalue and sending ajax
			if (data.eventName == "formSubmitted" && data.pageId == 5 && data.step == 1) {

				TradeInVeh["remainingvalue"] = data.data[0].Remainingvalue;
				OreCookie.add("tradeIn", "remainingvalue", TradeInVeh["remainingvalue"]);
				//sending ajax
				$.when(vehiclepage.doAjax('/userinfo_tradein', 'post', JSON.stringify(OreCookie.read_JSON("tradeIn"))))
					.done(function (response, textStatus, jqXHR) {
						if (TradeInVeh["remainingvalue"] > 0) {
							vehiclepage.update_tradein_amount(TradeInVeh["remainingvalue"]);
						} else {
							vehiclepage.update_tradein_amount(0); //Zero will be updated
						}
					}).fail(function (response, textStatus, jqXHR) {
						console.log(textStatus);
					});
			}
		});
	},
	update_tradein_amount: function (trade) {

		tradeValue = OreCookie.read_JSON("tradeIn");
		if (trade == '' || trade <= 0) trade = tradeValue['price'].replace("$", "").replace(",", "");
		$('.ore_txtbox_tradein').val(trade);
		$('.ore_txtbox_ltradein').val(trade);
		$('.ore_txtbox_ctradein').val(trade);
		vehiclepage.payment_lease('lease', 'onload');
		vehiclepage.payment_lease('finance', 'click');

		mAnalystic.tradeInToolSubmit('lead', "vendor trade in tool", "inPage", "iframe", "BlackBook", tyear, tmake, tmodel, tradeValue['year'], tradeValue['make'], tradeValue['model']);
	},
	navigation: function () {
		$(document).on('click', '.cNxt-btn,.cCurrent-btn', function () {
			$('.legendStyle>li.active').next().children('a').trigger('click');
		});

		$(document).on('click', '.cPrev-btn', function () {
			$('.legendStyle>li.active').prev().children('a').trigger('click');
		});

		$(document).on('click', '.legendStyle>li>a', function () {
			$('.cNxt-btn>span').text($(this).parent().next().children('a').text());
			$('.cCurrent-btn>span').text($(this).text());
			$('.cPrev-btn>span').text($(this).parent().prev().children('a').text());

			if ($(this).parent().index() > 0) {
				$('.cCurrent-btn,.cPrev-btn,.cPrev-btn').show();
				$('.vehSel').hide();
			}
			else {
				$('.cCurrent-btn,.cPrev-btn').hide();
				$('.vehSel').show();
			}

			if ($(this).parent().is(':last-child') == true) {
				$('.cNxt-btn,.cCurrent-btn').hide();
				$('#submitBtn').show();
			}
			else {
				$('#submitBtn').hide();
				$('.cNxt-btn,.cCurrent-btn').show();
			}
		});
	},
	/*
	* MOPHAR
	*/
	mopharSelector: function () {
		$(document).on('click', '.availedPackages li .removePkg', function () {
			var pkgName = $(this).parent('li').attr('data-package-name').trim(),
				pkgType = $(this).closest('[data-pkg-type]').attr('data-pkg-type');
			$('.additionalCareBlocks [data-pkg-type =' + pkgType + '] .contentBlock [data-package-name="' + pkgName + '"] .customCheckBox :checkbox').prop('checked', false);
			if ($(this).closest('ul').children().length < 2) {
				$(this).closest('.packageDetails').hide();
			}
			$(this).parent().remove();
		});

		$(document).on('click', '.ore_finance, .ore_lease, .removePkg', function () {
			var lease = [];
			var finance = [];

			$("input[name='lease']:checked").each(function () {
				lease.push($(this).val().trim());
			});
			$("input[name='finance']:checked").each(function () {
				finance.push($(this).val());
			});
			var vin = $('#vin').val();
			$.when(vehiclepage.doAjax('/userinfo_service_protection', 'post', JSON.stringify({ 'lease': lease, 'finance': finance, 'vin': vin })))
				.done(function (response, textStatus, jqXHR) {

				}).fail(function (response, textStatus, jqXHR) {
				});
		});

		$(document).on('click', '.additionalCareBlocks .contentBlock [data-package-name]', function () {
			var pkgName = $(this).attr('data-package-name').trim(),
				pkgType = $(this).closest('[data-pkg-type]').attr('data-pkg-type');
			if ($(this).find(':checkbox').is(':checked')) {
				// add
				$('[data-pkg-type =' + pkgType + '] .availedPackages').append('<li class="col-xs-12 col-sm-6 col-md-4" data-package-name="' + pkgName + '">' + pkgName + ' <i title="Click to Remove the Package" class="fa fa-lg fa-times-circle-o removePkg centerV" aria-hidden="true"></i></li>');
				$('[data-pkg-type =' + pkgType + '] .availedPackages').closest('.packageDetails').show();
			} else {
				//remove
				$('[data-pkg-type =' + pkgType + '] .availedPackages>li[data-package-name="' + pkgName + '"').remove();
				if ($('[data-pkg-type =' + pkgType + '] .availedPackages>li').length < 1) {
					$('[data-pkg-type =' + pkgType + '] .availedPackages').closest('.packageDetails').hide();
				}
			}
		});

		$(document).on('click', '#serviceProtection .onOffSlider > label', function (e) {
			var tagetBlock = $(this).attr('data-target-block').trim();
			if (tagetBlock == 'finance') {
				$('[data-pkg-type="lease"],.leaseBtn').hide();
				$('[data-tab-name="finance"]').trigger('click');
				if (e.hasOwnProperty('originalEvent')) {
					mAnalystic.clickLink('toggle:Finance');
				}

			} else {
				$('[data-pkg-type="finance"],.financeBtn').hide();
				$('[data-tab-name="lease"]').trigger('click');
				if (e.hasOwnProperty('originalEvent')) {
					mAnalystic.clickLink('toggle:Lease');
				}

			}
			$('.additionalCareBlocks [data-pkg-type=' + tagetBlock + '],.' + tagetBlock + 'Btn').show();
		});
		$(document).on('click', '[data-tab-name]', function () {
			var tabName = $(this).attr('data-tab-name'),
				chkTxt = $(this).attr('href');
			if (tabName === "lease") {
				$('[data-pkg-type="finance"],.financeBtn').hide();
				$('.additionalCareBlocks [data-pkg-type="lease"],.leaseBtn ').show();
				if ($('[data-pkg-type="lease"] .availedPackages>li').length > 0)
					$('[data-pkg-type="lease"]').show();
			} else {
				$('[data-pkg-type="lease"],.leaseBtn ').hide();
				$('.additionalCareBlocks [data-pkg-type="finance"],.financeBtn').show();
				if ($('[data-pkg-type="finance"] .availedPackages>li').length > 0)
					$('[data-pkg-type="finance"]').show();
			}
		});
	},
	/*
	* STYLE PICKER
	*/
	stylePicker: function () {
		$('.stylePicker .floatingIcon').click(function () {
			$('.stylePicker').toggleClass('active');
			$(this).toggleClass('fa-arrow-circle-up fa-arrow-circle-down');
		});
		$('.stylePicker ul li').click(function () {
			var cPath = "css/" + $(this).attr('data-style-sheet').trim();
			$(this).parent().children('.active').removeClass('active');
			$(this).addClass('active');
			$('#styleLoader').attr('href', cPath);
		});
	},
	/*
	* GENERAL FUNCTIONS
	*/
	avoidScrollForIframe: function (iframeParent) {
		$(iframeParent).find('iframe').each(function () {
			$(this).on('load', function () {
				var height = Number($(this).attr('height')) + 200;
				$(this).attr('height', height);
			});
		});
	},
	serializeObject: function () {
		$.fn.serializeObject = function () {
			var o = {};
			var a = this.serializeArray();
			$.each(a, function () {
				if (o[this.name]) {
					if (!o[this.name].push) {
						o[this.name] = [o[this.name]];
					}
					o[this.name].push(this.value || '');
				} else {
					o[this.name] = this.value || '';
				}
			});
			return o;
		};
	},
	CloseWebPage: function () {
		var openedWindow;
		if (navigator.userAgent.indexOf("MSIE") > 0) {
			if (navigator.userAgent.indexOf("MSIE 6.0") > 0) {
				window.opener = null;
				window.close();
			} else {
				window.open('', '_top');
				window.top.close();
			}
		}
		else if (navigator.userAgent.indexOf("Firefox") > 0) {
			window.location.href = 'about:blank ';
		} else {

			window.opener = null;
			openedWindow = window.open('', '_self', '');
			openedWindow.close();
		}
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
	alias: function (str, divider) {
		return str.replace(/(?:^|\.?)([A-Z])/g, function (x, y) { return divider + y.toLowerCase() }).replace(/^_/, "");
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



//for temp
///


/* review and submit new form*/

function setInputFilter(textbox, inputFilter) {
	["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function (event) {
		textbox.addEventListener(event, function () {
			if (inputFilter(this.value)) {
				this.oldValue = this.value;
				this.oldSelectionStart = this.selectionStart;
				this.oldSelectionEnd = this.selectionEnd;
			} else if (this.hasOwnProperty("oldValue")) {
				this.value = this.oldValue;
				this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
			}
		});
	});
}


setInputFilter(document.getElementById("postalcode"), function (value) {
	str_zip_length = value.length;
    if(0 < str_zip_length && 4 == str_zip_length ){
        value = '0'+ value;
    }

	return /^[0-9]{0,5}$/.test(value) && (value === "" || parseInt(value) <= 99999);
});

setInputFilter(document.getElementById("first"), function (value) {
	return /^[a-zA-Z .]{0,30}$/.test(value);
});
setInputFilter(document.getElementById("init_pop_first"), function (value) {
	return /^[a-zA-Z .]{0,30}$/.test(value);
});
setInputFilter(document.getElementById("last"), function (value) {
	return /^[a-zA-Z .]{0,30}$/.test(value);
});


$(document).on('click', '.btm_submitdealer', function (e) {
	var csource = $(this).data('source');

	if (csource == 'submit-to-dealer') {
		$('h4.submit-header').html('Submit to Dealer');
		$('.ore_leadsubmit').val('Submit Request');
		$('#current_submit_type').val(csource);
		$('#reviewSubmitPopUp').find('.submit-header-close').removeClass('finance-close');
	} else if (csource == 'explore-finance-options') {
		$('h4.submit-header').html('Explore Finance Options');
		$('.ore_leadsubmit').val('Proceed to Credit Application');
		$('#current_submit_type').val(csource);
		$('#review').hide();
		$('#reviewSubmitPopUp').find('.submit-header-close').addClass('finance-close');
	} else {
		$('h4.submit-header').html('Submit to Dealer');
		$('.ore_leadsubmit').val('Submit Request');
		$('#current_submit_type').val('submit-to-dealer');
		$('#reviewSubmitPopUp').find('.submit-header-close').removeClass('finance-close');
	}

});

$('body').on('click', '.finance-close', function () {
	$('#review').show();
});

function attr_valid(form_type) {

	var errCount = len = 0;
	var otherVal = true;
	$('[data-form-attr]:visible').each(function () {
		attrType = $(this).data('form-attr');
		errMsg = $(this).data('error-msg');
		len = $(this).val().length;
		var otherVal = true;

		$(this).closest('.form-group').find('.errorInfo').text(errMsg);

		if (attrType == "email") {
			if (form_type == 'init') { var mail = $('#init_pop_contact_email').val(); } else { var mail = $('#contact_email').val(); }
			var pattern = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+)\.([a-zA-Z0-9]{2,4})$/;
			var res = pattern.test(mail);
			if (res) { } else {
				otherVal = false;
			}

		}

		// Final Phone number Validation with value

		if (attrType == 'phone') {
			if (form_type == 'init') { var v_phone = $('#init_pop_contact_phone').val(); } else { var v_phone = $('#contact_phone').val(); }

			var v_phone_no_length = v_phone.length;

			if (v_phone_no_length != '') {
				var v_phone_pattern = /^([\+][0-9]{1,3}[\ \.\-])?([\(]{1}[0-9]{2,6}[\)])?([0-9\ \.\-\/]{3,20})((x|ext|extension)[\ ]?[0-9]{1,4})?$/;
				var v_phone_res = v_phone_pattern.test(v_phone);
				if ((v_phone_res && v_phone_no_length === 14) || v_phone_no_length == 0) { }
				else {
					otherVal = false;
				}
			}
		}

		// Zip Code Validation

		if (attrType == 'zip') {
			if (form_type == 'init') { var v_zip = $('#init_pop_postalcode').val(); } else { var v_zip = $('#postalcode').val(); }

			var str_zip_length1 = v_zip.length;
			if(0 < str_zip_length1 && 4 == str_zip_length1 ){
				v_zip = '0'+ v_zip;
			}


			var v_zip_pattern = /^\d{5}(?:[-\s]\d{4})?$/;
			var v_zip_res = v_zip_pattern.test(v_zip);
			var v_zip_length = v_zip.length;
			if (v_zip_res && v_zip_length === 5) { } else {
				otherVal = false;
			}
		}

		if ((attrType != 'phone' && len < 1) || !otherVal) {
			$(this).closest('.form-group').addClass('error');
			if ($(this).closest('.form-group').find('.errorInfo').length < 1) {
				$(this).closest('.form-group').append('<span class="errorInfo">' + errMsg + '</span>')
			}
		} else {
			$(this).closest('.form-group').removeClass('error');
			$(this).closest('.form-group').children('.errorInfo').remove();
			errCount++;
		}
	});

	if ($('[data-form-attr]:visible').length == errCount && otherVal)
		canSubmit = true;
	else
		canSubmit = false;

	return canSubmit;
}

$(document).on('click', '.ore_leadsubmit', function (e) {


	var canSubmit, currTxt, attrType, len, errCount = 0,
		errMsg, ValidEmail = true;
	$('#responseTxt').hide();
	$(this).addClass('disabled');
	$(this).attr("disabled", "disabled");
	$(this).val('Processing');

	canSubmit = attr_valid('lead');

	var c1source = $(this).val();
	if (!canSubmit) {
		$('.ore_leadsubmit').removeClass('disabled');
		$('.ore_leadsubmit').removeAttr("disabled", "disabled");
		var current_submit_type2 = $('#current_submit_type').val();
		if (current_submit_type2 == 'explore-finance-options') {
			$('.ore_leadsubmit').val('Proceed to Credit Application');
		} else {
			$('.ore_leadsubmit').val('Submit Request');
		}
		e.preventDefault();
	} else {

		var filterParams = $("form#reviewSubmit").serializeObject();

		var review_chk_box_home_delivery = $('input[name="chk_box_home_delivery"]:checked').val();
		chk_box_home_delivery = (review_chk_box_home_delivery == 'on') ? true : false;
		filterParams['chk_box_home_delivery'] = chk_box_home_delivery;

		/******
  *  LEAD
  */
		var appurl = $('#APP_URL').val();
		$.post(appurl + 'mylead', filterParams, function (returnedData) {
			var current_submit_type = $('#current_submit_type').val();

			if (returnedData['status'] == 'success') {

				if (current_submit_type == 'explore-finance-options') {
					var leadid = returnedData['message'][0];
					if (leadid.length == 36) {
						mAnalystic.onExploreFormComplete(returnedData['message'][0], 'modal');
					}
					$('#reviewSubmitPopUp').modal('toggle');
					$('.mylead_thanks').hide();
					$('.mylead_thanks_waitlist').hide();
					$('.mylead_thanks_done').hide();
					$('.iframe-field').show();
					$('#submitBtn, .fwd-bck-btns button.btm_submitdealer:nth-child(3)').attr("disabled", "disabled");
					$('#submitBtn, .fwd-bck-btns button.btm_submitdealer:nth-child(3)').addClass('disabled');
					$('.btm_submitdealer').data('target');

					var myloc = $('#routeone-iframe', window.parent.document).attr('src');
					console.log(myloc);
					myloc += '&first_name=' + filterParams.first;
					myloc += '&last_name=' + filterParams.last;
					myloc += '&phone=' + filterParams.contact_phone;
					myloc += '&city=' + filterParams.city;
					myloc += '&zip=' + filterParams.postalcode;
					myloc += '&state=' + filterParams.regioncode;
					myloc += '&address=' + filterParams.streetline1;
					myloc += '&email=' + filterParams.contact_email;

					$('#routeone-iframe', window.parent.document).attr('src', myloc);
				} else {
					var leadid = returnedData['message'][0];
					if (leadid.length == 36 || leadid.length >= 30) {
						mAnalystic.onFormComplete(returnedData['message'][0], 'modal');
					}
					$('.ore_leadsubmit').removeClass('disabled');
					$('.ore_leadsubmit').removeAttr("disabled", "disabled");
					$('.ore_leadsubmit').val('Submit Request');

					if (returnedData['sold_type'] == 'available') {
						$('.mylead_thanks').hide();
						$('.mylead_thanks_waitlist').hide();
						$('.mylead_thanks_done').show();
						$('#reviewSubmitPopUp .close').removeAttr("data-dismiss");
						$('#reviewSubmitPopUp .close').addClass("redirect-home-link");
						$("#reviewSubmitPopUp").attr("data-backdrop", "static");
						$("#reviewSubmitPopUp").attr("data-keyboard", "false");

					} else {
						$('.mylead_thanks').hide();
						$('.mylead_thanks_waitlist').show();
						$('.mylead_thanks_done').hide();
						$('#reviewSubmitPopUp .close').removeAttr("data-dismiss");
						$('#reviewSubmitPopUp .close').addClass("redirect-home-link");
						$("#reviewSubmitPopUp").attr("data-backdrop", "static");
						$("#reviewSubmitPopUp").attr("data-keyboard", "false");
					}
					var tier_platform = $('#tier').val();
					if (tier_platform == 'ore' || tier_platform == 't1') {
						var timer2 = "0:11";
						$('.redirect_home').show();
						if(tier_platform == 't1'){
							$('.submit-header').html('<center>Thank You!</center>');
						}
						var interval = setInterval(function () {
							var timer = timer2.split(':');
							var minutes = parseInt(timer[0], 10);
							var seconds = parseInt(timer[1], 10);
							if (minutes == 0 && seconds == 0) {
								var re = $('#APP_URL').val();
								var make =$('#make').val();
								reurl= (tier_platform == 't1') ? re+make+"/"+tier_platform : re+make;
								window.location.href = reurl;
							} else {
								--seconds;
								minutes = (seconds < 0) ? --minutes : minutes;
								if (minutes < 0) clearInterval(interval);
								seconds = (seconds < 0) ? 59 : seconds;
								seconds = (seconds < 10) ? '0' + seconds : seconds;
								$('.redirect_home').html('<strong>You will be automatically redirected to Home Page in  <span style="color:#8f0c2c">' + minutes + ':' + seconds + ' seconds</span>.<strong>');
								timer2 = minutes + ':' + seconds;
							}
						}, 1000);
					} else {
						$('.closing_home').show();
						$('.submit-header').html('<center>Thank You!</center>');
						$('.submit-header-close').hide();

						var timer2 = "0:11";
						var interval = setInterval(function () {
							var timer = timer2.split(':');
							var minutes = parseInt(timer[0], 10);
							var seconds = parseInt(timer[1], 10);
							if (minutes == 0 && seconds == 0) {
								vehiclepage.CloseWebPage();
							} else {
								--seconds;
								minutes = (seconds < 0) ? --minutes : minutes;
								if (minutes < 0) clearInterval(interval);
								seconds = (seconds < 0) ? 59 : seconds;
								seconds = (seconds < 10) ? '0' + seconds : seconds;
								$('.closing_home_timer').html('<strong>The window will  automatically close in <span style="color:#8f0c2c">' + minutes + ':' + seconds + ' seconds</span>.<strong>');
								timer2 = minutes + ':' + seconds;
							}
						}, 1000);
					}
				}

			} else {
				$('.fa-refresh').click();
				$('.mylead_thanks_done').hide();
				$('.mylead_thanks_waitlist').hide();
				$('.mylead_thanks').show();
				$('#responseTxt').addClass('alert-danger').removeClass('alert-success');
				$('#responseTxt').html(returnedData['message']);
				$('.ore_leadsubmit').removeClass('disabled');
				$('.ore_leadsubmit').removeAttr("disabled", "disabled");
				var current_submit_type2 = $('#current_submit_type').val();
				if (current_submit_type2 == 'explore-finance-options') {
					$('.ore_leadsubmit').val('Proceed to Credit Application');
				} else {
					$('.ore_leadsubmit').val('Submit Request');
				}
			}
			$('#responseTxt').show();
		});
		e.preventDefault();
	}
});



$.fn.serializeObject = function () {
	var o = {};
	var a = this.serializeArray();
	$.each(a, function () {
		if (o[this.name]) {
			if (!o[this.name].push) {
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		} else {
			o[this.name] = this.value || '';
		}
	});
	return o;
};
/* $(document).ready(function () {
	if ($('#checklease').val() > 0) {
		$( ".ore_lease" ).each(function(index,val) {
			if($(this).prop("checked") == true){
				$( this ).trigger('click');
				$( this ).trigger('click');
            }

		  });

	}
	if ($('#checkfinance').val() > 0) {
		$( ".ore_finance" ).each(function(index,val) {
			if($(this).prop("checked") == true){
				$( this ).trigger('click');
				$( this ).trigger('click');
            }

		  });
	}
	$('#sliderround').trigger('click');
	$('#sliderround').trigger('click');
}) */

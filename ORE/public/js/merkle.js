javascript: (function() {
    window.digitalData = {};
    window.digitalData.page = {};
    window.digitalData.events = new Array();
    window.digitalData.version = "1.0";
    window.digitalData.debug = false; 
    window.digitalData.page.category = {
        pageType : null
    };

    // User object
    window.digitalData.user =  {
        authenticationState : null
    };
 
    
    // Utility function to create new event entries.
    window.digitalData.newEvent = function(ev) {
        var event_ = {
            type : null,
            eventName : null,
            eventAction : null,
            attributes : {},
            timeStamp : new Date()
        };
        if (ev) {
            (ev.type != undefined) ? (event_.type = ev.type) : (event_.type);
            (ev.eventName != undefined) ? (event_.eventName = ev.eventName) : (event_.eventName);
            (ev.eventAction != undefined) ? (event_.eventAction = ev.eventAction) : (event_.eventAction);
            (ev.attributes != undefined) ? (event_.attributes = ev.attributes) : (event_.attributes);
            (ev.timeStamp != undefined) ? (event_.timeStamp = ev.timeStamp) : (event_.timeStamp);
        }
        // Event array is updated and a window.trigger is fired at this point to alert the browser that a new event has occurred.
        window.digitalData.events.push(event_);
        if ( typeof jQuery != "undefined") {
            jQuery(window).trigger(event_);
        }
        return event_;
    };
})();

// Sample code for demo:
 window.digitalData.debug = true;
 

 $(document).ready(function() {   
		mAnalystic.init();
		
});

// PageAction 
var mAnalystic = {
	init: function(){
		var activeTier="standalone";
		var oPageAction = $('meta[name=pageName]').attr("content"); //home or search-new-inventory or  
		var tier 		= $('#tier').val();
		if(tier == 'ore') activeTier = 'Standalone'; else activeTier = tier; 
	 
		mAnalystic.pageInfo(oPageAction,tier);
		if(oPageAction == 'brand-specific'){
			var taxonomy = 'DriveFCA:'+activeTier+':'+$('#paramBrandName').val()+':'+':us:';
		}else if(oPageAction == 'search-new-inventory'){
			var taxonomy = 'DriveFCA:'+activeTier+':'+$('#params_make').val()+':'+$('#params_modelname').val()+':'+$('#params_year').val()+':'+$('#params_vechType').val()+':us:';
		}else if(oPageAction == 'vehicle-information'){
			var taxonomy = 'DriveFCA:'+activeTier+':'+$('#make').val()+':'+$('#model').val()+':'+$('#year').val()+':'+$('#vehicle_type').val()+':us:';
		}else{
			var taxonomy = 'DriveFCA:us:home';
		}
		
		
		$(document).on('click','.switch__label', function(){	
				var thisfor = $(this).attr('for');				 
				if(thisfor!='' && thisfor!==undefined && thisfor!=null){
					thisfor = thisfor.replace('v_type_','');
				}  
			 	mAnalystic.clickLink(taxonomy+':label:select-vehicle-type:'+thisfor);		 
		});	
		 
		/* +++++++++++ +++++++++++ 		PAGE LOAD		   +++++++++++ +++++++++++ +++++++++++ +++++++++++ */
				mAnalystic.PageLoad(oPageAction); 
				
		/* +++++++++++ +++++++++++ 		INVENTORY LISTING  +++++++++++ +++++++++++ +++++++++++ +++++++++++ */
		if(oPageAction == 'search-new-inventory'){
				mAnalystic.InventoryListing();
		}
		$(document).on('click','.ore_ext', function(){			 		
			 	mAnalystic.clickLink(activeTier+':'+'load-more:exact-match');		 
		});	
		$(document).on('click','.ore_par', function(){			 		
			 	mAnalystic.clickLink(activeTier+':'+'load-more:partial-match');		 
		});	
		$(document).on('click','.zoomIcon', function(){	
				//var zoomIconVin = $(this).data('vin');
			 	//mAnalystic.clickLink(zoomIconVin+':open:click-to-enlarge');
				mAnalystic.clickLink(activeTier+':'+'open:click-to-enlarge');				
		});
		$(document).on('click','.link_change_vehicle', function(){			 		
			 	mAnalystic.clickLink(activeTier+':'+'Change-Vehicle');	 
		});
		$(document).on('click','.how-itworks-field', function(){			 		
			 	mAnalystic.clickLink('Home:how-itworks-link');	 
		});	

		$(document).on('click','.zipcode_edit_field', function(){			 		
			 	mAnalystic.CTA(activeTier+':'+'brandspecific:zipcode');	 
		});	 
		$(document).on('click','#selectedZipCodePopupBtn', function(){			 		
			 	mAnalystic.CTA(activeTier+':'+'brandspecific:zipcode:submit');	 
		});	
		

		$(document).on('click','.change-icon-img', function(){ 
				 
				mAnalystic.CTA(activeTier+":home-zipcode:submit");		 
		});	 
		
		
		
		
		
		 
		$(document).on('click','.FilterSelectEvent', function(){ 
				
				var filter_type = $(this).data('filter-type');
				var filter_id = $(this).attr('id'); 
				if(filter_id!='' && filter_id!==undefined && filter_id!=null){
					filter_id = filter_id.replace('Code[]','');
				} 
				
				mAnalystic.clickLink('Filter:'+activeTier+':'+':'+filter_id+':'+filter_type);
				
				var dealersCode = new Array(),
					driveCode = new Array(),
					trimCode=new Array(),
					colorCode=new Array(),
					EngDescCode=new Array(),
					TransmissionCode=new Array();
				var filterHead="",
					filterValue="";
				
				$. each($("input[name='dealersCode[]']:checked"), function(){ 
					dealersCode.push($(this).next('span').text()); 
				});
				$. each($("input[name='driveCode[]']:checked"), function(){  
					driveCode.push($(this).next('span').text()); 
				});
				$. each($("input[name='trimCode[]']:checked"), function(){ 
					trimCode.push($(this).next('span').text()); 
				});
				$. each($("input[name='colorCode[]']:checked"), function(){ 
					colorCode.push($(this).next('span').text()); 
				});
				
				$. each($("input[name='EngDescCode[]']:checked"), function(){ 
					EngDescCode.push($(this).next('span').text()); 
				});
				$. each($("input[name='TransmissionCode[]']:checked"), function(){ 
					TransmissionCode.push($(this).next('span').text()); 
				}); 
				  
				if((dealersCode.length)  > 0){
					filterHead += "Dealer,";
					filterValue = "'"+dealersCode.join(',')+"'";
				}
				
				if((driveCode.length)  > 0){
					filterHead += "Drive,";
					filterValue += ','+"'"+driveCode.join(',')+"'";
				}
				
				if((trimCode.length)  > 0){
					filterHead += "Trim,";
					filterValue += ','+"'"+trimCode.join(',')+"'";
				}
				
				if((colorCode.length)   > 0){
					filterHead += "Color,";
					filterValue += ','+"'"+colorCode.join(',')+"'";
				}
				
				if((EngDescCode.length)   > 0){
					filterHead += "Engine Desc,";
					filterValue += ','+"'"+EngDescCode.join(',')+"'";
				}
				
				if((TransmissionCode.length)   > 0){
					filterHead += "Transmission,";
					filterValue += ','+"'"+TransmissionCode.join(',')+"'";
				}
				 
			 mAnalystic.InventoryFilter(filterHead.replace(/(^,)|(,$)/g, ""), filterValue.replace(/(^,)|(,$)/g, ""));
		});
		/* +++++++++++ +++++++++++ 		PAYMENT CALCULATOR +++++++++++ +++++++++++ +++++++++++ +++++++++++ */
		$('#modalLeaseCalc, #modalPaymentCalc, #modalCashCalc').on('click', function(){
			var cid = $(this).attr('id');
			var year 	= $('#year').val();
			var make 	= $('#make').val();
			var model 	= $('#model').val(); 
			var ostatus = $('#vehicle_type').val(); 
			var trim    = $.trim($('.vd_title').text());
			mAnalystic.clickLink(activeTier+':'+cid.replace("modal", "")+':Price-Details'); 
		});
		
		 
		/* +++++++++++ +++++++++++ 		CTA CLICKS 		   +++++++++++ +++++++++++ +++++++++++ +++++++++++ */
		//1. SNI1 - Model click
		//2. SNI1 - vehicle_type
		//3. SNI1 - model image to next page
		//4. SNI2 - Quick Information	 
		
		//NAVIGATION 
		$(document).on('click','#tab_review,.ore_tradeIn ,.ore_serviceProtection,.vehicleInfo', function(e){
				var linkText = $.trim($(this).text());	
				 if(e.hasOwnProperty('originalEvent')){ 
					 mAnalystic.navigationClick(linkText,'topNav');		 
				 }  else{ } 
			}); 
		
		
			
		
		
		// SNI-2 : Secondary Fitler
		$(document).on('change','.sniSortBy', function(){ 
				var sniSortBy = $( ".sniSortBy option:selected" ).text();
				mAnalystic.clickLink(activeTier+':'+"sort-by: "+sniSortBy);		 
		});	 
		
		// SNI-2 : Quick INFO	 
		$('body').on('click','.quickinfo', function(){  
			if ($(this).children('i').hasClass('glyphicon-menu-up')){
				mAnalystic.clickLink(taxonomy+"quick-info:close");
			}else{
				mAnalystic.clickLink(taxonomy+"quick-info:open");;		 
			}
		});
		// SNI-2 : Dealers Dropdown
		$(document).on('click','.filterAttrs .dOpener', function(){ 
			var filter_item = $.trim($(this).text()).toLowerCase();
			if ($(this).children('i').hasClass('glyphicon-menu-up')){
				mAnalystic.clickLink(activeTier+':'+"filter:"+filter_item+":open");		 
			}else{
				mAnalystic.clickLink(activeTier+':'+"filter:"+filter_item+":close");	
			}
		});	
		 
		// SNI-2 : tabs : Exact - partial match
		$('body').on('click','.listBlock ul.nav-tabs a', function(){ 
				var ematch = $(this).attr('href');
				mAnalystic.clickLink(taxonomy+"tabs:"+ematch.replace("#", ""));			 
		});
		//SNI-2
		 $(document).on('click','.sni2_zipcode',function() {
			mAnalystic.clickLink(activeTier+':'+'sni2:zipcode:submit');	
		 });
		 //SNI-2 zipcode change
		 $(document).on('click','#resultsPage [data-target="#zipCodePopUp"]',function() {
			mAnalystic.clickLink(activeTier+':'+'search-new-inventory:zipcode:change');	
		 });
		 //SNI-2: Window Sticker
		 $('body').on('click','.footerBtns .window-sticker', function(){  
				var window_sticker_vin = $(this).data('vin');
				mAnalystic.CTA(activeTier+':'+window_sticker_vin+":Window-Sticker");			 
		});
		//SNI-2: View Vehicle Details
		 $('body').on('click','.footerBtns .ore_vehicle_vin', function(){  
				var vehicle_details_vin = $(this).data('vin');
				mAnalystic.CTA(activeTier+':'+vehicle_details_vin+":View-Vehicle-Details");			 
		});
		//SNI-2: Vehicle Details
		 $('body').on('click','.resetFilterData', function(){   
				mAnalystic.clickLink(taxonomy+":Reset-Filters");			 
		});
		//SNI-2: Header Logo
		 $('body').on('click','.top-logo-sni', function(){ 
			mAnalystic.clickLink(activeTier+':'+'Header:SNI2-Logo');
		});		
		
		$(document).on('change','.FilterSelectEvent',function(){
				var linkText = $.trim($(this).next( "span" ).text());	
				var filterName = $.trim($(this).attr( "id" )).replace('Code[]','');	
				if($(this).prop('checked') == true){
					var ore_chk = 'Checked'; 
				} else { 
					var ore_chk = 'Unchecked'; 
				}
			console.log(taxonomy+'filter:'+filterName+':'+ore_chk+':'+linkText);
			//mAnalystic.clickLink(taxonomy+':'+ore_chk+':'+linkText);
		}); 
		
		
		//SNI-1 : Brand Selection
		$(document).on('click','.oreBrands a',function() {  
			 	var current_make_selection = $(this).attr('data-make'); 
			 	mAnalystic.clickLink('home:brand:'+current_make_selection);		 
		});
		//SNI-1: Year Selection
		$(document).on('click','.cardHeader>.nav-tabs a', function(){  
			 	var vehicle_year = $(this).attr('href');	
				var res = vehicle_year.split("_");
				var current_make_selection = 'drivefca' 
			 	mAnalystic.clickLink('vcard:'+res[1]+':'+current_make_selection.replace("_", "-")+':'+res[0].substring(1, res[0].length).toLowerCase().replace("_", "-"));		 
		});		
		//SNI-1: Vehicle Condition Selection
		 $(document).on('click','.brandList .switchwrap .onOffSlider>label',function() {
				var current_make_selection = $('.brandList>div.active>.oreBrands').attr('data-make');
				
			 	if($(this).attr('id') == "new"){
					var toggleVehicleCond = "CPOV"
				}else if($(this).attr('id') == "cpo"){
					var toggleVehicleCond = "New"
				}else var toggleVehicleCond = "New"
				
			 	mAnalystic.clickLink(activeTier+':'+"toggle:vehiclecondition:"+current_make_selection+":"+toggleVehicleCond);		 
		});	
		//SNI-1: zipcode Popup
		 $(document).on('click','#landingPage [data-target="#zipCodePopUp"]',function() {
			mAnalystic.clickLink(activeTier+':'+'home:zipcode:change');	
		 });
		 //SNI-1: zipcode click
		 $(document).on('click','.home_zipcode',function() {
			mAnalystic.clickLink(activeTier+':'+'sni1:zipcode:change');	
		 });
		 $(document).on('click','.modal-video-close-btn',function() {
			mAnalystic.clickLink('Home:video:close');	
		 });
		  $(document).on('click','#closer_videopopup',function() {
			mAnalystic.clickLink(taxonomy+':how-its-work-video:close');	
		 });
		 
		 
		 
		  $(document).on('click','.vCard .tab-content>div.active',function() {
				var active = $(this).attr('id'); 
				mAnalystic.clickLink('Home:model-selected:'+active);	
		 });
		 
		 
		 $('.initial_popup_video_field').click( function(){ 
			 	mAnalystic.clickLink('model:initial-popup:how-itworks-link');	 
		});

		 $('#initial_chk_box_home_delivery').click( function(){ 
				if($(this).is(':checked')) {
					mAnalystic.CTA(activeTier+':'+'initial_popup:homedelivery:checked');
				}else{
					mAnalystic.CTA(activeTier+':'+'initial_popup:homedelivery:unchecked');
				}
		});

		 $('#chk_box_home_delivery').click( function(){ 
				if($(this).is(':checked')) {
					mAnalystic.CTA(activeTier+':'+'review_and_submit:homedelivery:checked');
				}else{
					mAnalystic.CTA(activeTier+':'+'review_and_submit:homedelivery:unchecked');
				}
		});
		 
		$('#chk_box_home_delivery').click( function(){

			if($( this ).prop( "checked" )){
				mAnalystic.clickLink(taxonomy+':'+activeTier+':'+'initial:lead:homedelivery:checked');	
			}else{
				mAnalystic.clickLink(taxonomy+':'+activeTier+':'+'initial:lead:homedelivery:unchecked');
			}
		 }); 
		 $('#chk_box_home_delivery').click( function(){

			if($( this ).prop( "checked" )){
				mAnalystic.clickLink(taxonomy+':'+activeTier+':'+'initial:lead:homedelivery:checked');	
			}else{
				mAnalystic.clickLink(taxonomy+':'+activeTier+':'+'initial:lead:homedelivery:unchecked');
			}
		 }); 
		
		
		//Popup Close		 
		$(document).on('click','button.close', function(){	
				 var paymentCalcTabChosen = $('.tabsContainer>.nav-tabs li.active a').text();  
				var linkText = $.trim($(this).parents('.fade').attr('id')); 
				if(linkText == 'leaseCalc') mAnalystic.clickLink(activeTier+':'+'leasecalc:close');	
				else if(linkText == 'paymentCalc') mAnalystic.clickLink(activeTier+':'+'paymentcalc:close');
				else if(linkText == 'cashCalc') mAnalystic.clickLink(activeTier+':'+'cashcalc:close');
				else if(linkText == 'reviewSubmitPopUp') mAnalystic.clickLink(activeTier+':'+'submit-to-dealer:close');
				else if(linkText == 'popImageCont') mAnalystic.clickLink(activeTier+':'+'close:click-to-enlarge');
				else if(linkText == 'zipCodePopUp') mAnalystic.clickLink(activeTier+':'+oPageAction+':zipcode'+':close');
				else if(linkText == 'initialPopUp') mAnalystic.clickLink(activeTier+':'+oPageAction+':first-lead-submission-popup'+':close');
				else if(linkText == 'initialPopUpThanks') mAnalystic.clickLink(activeTier+':'+oPageAction+':first-lead-submission-thanks-popup'+':close');			
				else if(linkText == 'availableProgramPopup') mAnalystic.clickLink(activeTier+':'+paymentCalcTabChosen+':offers:close'); 
				else mAnalystic.clickLink(activeTier+':'+'zipcode:close');				 		 
		}); 
		$(document).on('click','.50-states', function(){	
			mAnalystic.clickLink(activeTier+':'+'50states:grand-caravan-journey:close');	
		});
		
		
		$('#contpop').on('click', function(){  
				var paymentCalcTabChosen = $('.tabsContainer>.nav-tabs li.active a').text(); 
				mAnalystic.CTA(activeTier+':offers:'+paymentCalcTabChosen+':continue');
				if($('#avail_pgm_loyalty').prop('checked')) mAnalystic.CTA(activeTier+':offers:'+paymentCalcTabChosen+':own-or-lease:yes');
				//else //mAnalystic.CTA(activeTier+':offers:'+paymentCalcTabChosen+':loyalty:no');
				
			if($('#avail_pgm_lease_loyalty').prop('checked')) mAnalystic.CTA(activeTier+':offers:'+paymentCalcTabChosen+':lease-due:yes');
				//else //mAnalystic.CTA(activeTier+':offers:'+paymentCalcTabChosen+':lease-loyalty:no');				
				 
		}); 
		 
		 $('#leaseCalc li .dOpener').click(function(){
			 var paymentCalcTabChosen = $('.tabsContainer>.nav-tabs li.active a').text(); 
			if($(this).parent().find('i').hasClass('glyphicon-menu-down')){
				if($(this).parent().find('.uLine').text()=="Incentives"){
					mAnalystic.clickLink(activeTier+':'+paymentCalcTabChosen+':incentives-close'); 
				}else mAnalystic.clickLink(activeTier+':'+paymentCalcTabChosen+':additional-offers-close'); 
				 
			}else{
				if($(this).parent().find('.uLine').text()=="Incentives"){
					mAnalystic.clickLink(activeTier+':'+paymentCalcTabChosen+':incentives-open'); 
				}else mAnalystic.clickLink(activeTier+':'+paymentCalcTabChosen+':additional-offers-open'); 
			}
		});

		$('#paymentCalc li .dOpener').click(function(){
			 var paymentCalcTabChosen = $('.tabsContainer>.nav-tabs li.active a').text(); 
			if($(this).parent().find('i').hasClass('glyphicon-menu-down')){
				if($(this).parent().find('.uLine').text()=="Incentives"){
					mAnalystic.clickLink(activeTier+':'+paymentCalcTabChosen+':incentives-close'); 
				}else mAnalystic.clickLink(activeTier+':'+paymentCalcTabChosen+':additional-offers-close'); 
				 
			}else{
				if($(this).parent().find('.uLine').text()=="Incentives"){
					mAnalystic.clickLink(activeTier+':'+paymentCalcTabChosen+':incentives-open'); 
				}else mAnalystic.clickLink(activeTier+':'+paymentCalcTabChosen+':additional-offers-open'); 
			}
		});

		$('#cashCalc li .dOpener').click(function(){
			 var paymentCalcTabChosen = $('.tabsContainer>.nav-tabs li.active a').text(); 
			if($(this).parent().find('i').hasClass('glyphicon-menu-down')){
				if($(this).parent().find('.uLine').text()=="Incentives"){
					mAnalystic.clickLink(activeTier+':'+paymentCalcTabChosen+':incentives-close'); 
				}else mAnalystic.clickLink(activeTier+':'+paymentCalcTabChosen+':additional-offers-close'); 
				 
			}else{
				if($(this).parent().find('.uLine').text()=="Incentives"){
					mAnalystic.clickLink(activeTier+':'+paymentCalcTabChosen+':incentives-open'); 
				}else mAnalystic.clickLink(activeTier+':'+paymentCalcTabChosen+':additional-offers-open'); 
			}
		});		
		 
	//Vehicle details
	
	// Initial Lead Popup
	
		$("#reviewSubmitInitialPopUp input").bind("keydown", function(){
			mAnalystic.oFormStart(oPageAction, taxonomy+"first-lead-submission", "lead", "modal");
			$("#reviewSubmitInitialPopUp input").unbind("keydown");    
		});     
		
		
	$(document).on('click','.initialPopUpContinue', function(e){ 
		mAnalystic.CTA(activeTier+':'+oPageAction+':first-lead-submission-save-and-continue');
		mAnalystic.onFirstFormComplete('modal');
		//mAnalystic.onCloudID(_satellite.getVisitorId().getMarketingCloudVisitorID(),'modal');
	});
	
	$(document).on('click','.initialPopUpThanksContinue', function(e){
		mAnalystic.CTA(activeTier+':'+oPageAction+':first-lead-submission-thanks-continue');
	});
	
			// TABS: LEASE, FINANCE AND CASH		
		$(document).on('click','.tabsContainer>.nav-tabs a', function(e){
			 if(e.hasOwnProperty('originalEvent')){
						var linkText = $.trim($(this).text());	
							mAnalystic.clickLink('vehicle-information:tabs:'+linkText);	
					} 
		}); 
		/* $('#submitBtn').on('click', function(){
			var submitBtn_taxonomy = 'DriveAlfaRomeo:'+$('#make').val()+'-'+$('#model').val()+'-'+$('#year').val()+'-'+$('#vehicle_type').val()+':US:'; 			
			 mAnalystic.CTA(submitBtn_taxonomy+'view-vehicle-details:Submit-to-dealer');
		}); */
		
		$('body').on('click','.merkle_bottom_serviceandprot', function(e){ 			 
				mAnalystic.CTA('bottom:'+activeTier+':'+'DriveFCA:'+$('#make').val()+'-'+$('#model').val()+'-'+$('#year').val()+'-'+$('#vehicle_type').val()+':us:prev:service-and-protection');	 			
		});
		
		$('body').on('click','.merkle_next_serviceandprot', function(e){ 			 
				mAnalystic.CTA('bottom:'+activeTier+':'+'DriveFCA:'+$('#make').val()+'-'+$('#model').val()+'-'+$('#year').val()+'-'+$('#vehicle_type').val()+':us:next:service-and-protection');	 
			
		});
		
		
		$('body').on('click','.merkle_bottom_submittodealer', function(){
			 mAnalystic.CTA('bottom:'+activeTier+':DriveFCA:'+$('#make').val()+'-'+$('#model').val()+'-'+$('#year').val()+'-'+$('#vehicle_type').val()+':us:leadsubmit'+':'+$.trim($('.legendStyle li.selected').text())+':'+$(this).text());
		});
		$('body').on('click','.merkle_bottom_explorefinance', function(){
			 mAnalystic.CTA('bottom:'+activeTier+':DriveFCA:'+$('#make').val()+'-'+$('#model').val()+'-'+$('#year').val()+'-'+$('#vehicle_type').val()+':us:leadsubmit'+'-'+$(this).text());
		});
		
		 
		
		$('body').on('click','.fwd-bck-btns1>.backto-vehicle-info' , function(e){  
				if(e.hasOwnProperty('originalEvent')){ 
					mAnalystic.CTA('bottom:'+activeTier+':DriveFCA:'+$('#make').val()+'-'+$('#model').val()+'-'+$('#year').val()+'-'+$('#vehicle_type').val()+':us:leadsubmit'+'-'+$(this).text());
				 }  else{ } 
			
		});
		
		$('body').on('click','.merkle_next_trade_in', function(e){  
			mAnalystic.CTA('bottom:'+activeTier+':DriveFCA:'+$('#make').val()+'-'+$('#model').val()+'-'+$('#year').val()+'-'+$('#vehicle_type').val()+':us:'+$(this).text());
		});


		 
			
		$('.serviceprot-submit-toggle input[type="checkbox"]').change(function(e){			
				if(e.hasOwnProperty('originalEvent')){ 		
						if($(this).prop("checked") == true){
							mAnalystic.CTA('bottom:'+activeTier+':DriveFCA:'+$('#make').val()+'-'+$('#model').val()+'-'+$('#year').val()+'-'+$('#vehicle_type').val()+':us:service-and-protection:toggle'+'-'+$(this).parent().find('.slide-ex').text());
						}
						else if($(this).prop("checked") == false){
							mAnalystic.CTA('bottom:'+activeTier+':DriveFCA:'+$('#make').val()+'-'+$('#model').val()+'-'+$('#year').val()+'-'+$('#vehicle_type').val()+':us:service-and-protection:toggle'+'-'+$(this).parent().find('.slide-ex').text());
						}
				}else{}
	    });
		
		$('.review-submit-toggle input[type="checkbox"]').change(function(e){
			  if(e.hasOwnProperty('originalEvent')){ 	 
					if($(this).prop("checked") == true){
						mAnalystic.CTA('bottom:'+activeTier+':DriveFCA:'+$('#make').val()+'-'+$('#model').val()+'-'+$('#year').val()+'-'+$('#vehicle_type').val()+':us:review-and-submit:toggle'+'-'+$(this).parent().find('.slide-ex').text());
					}
					else if($(this).prop("checked") == false){
						mAnalystic.CTA('bottom:'+activeTier+':DriveFCA:'+$('#make').val()+'-'+$('#model').val()+'-'+$('#year').val()+'-'+$('#vehicle_type').val()+':us:review-and-submit:toggle'+'-'+$(this).parent().find('.slide-ex').text());
					}
				}else{}	
	    });
		
		
		// Payment CALCULATOR
						
						 
		
		//Lead:Waitlist Redirect
		$('body').on('click','.link_waitlist', function(){
			mAnalystic.CTA(activeTier+':'+'Lead:Waitlist-Redirect');
		});
		//Lead:Redirect
		$('body').on('click','.link_confirm', function(){
			mAnalystic.CTA(activeTier+':'+'Lead:Confirm-Redirect');
		}); 
		//Review:Telephone Click
		$('body').on('click','.review-telephone', function(){
			var review_telephone = $(this).attr('href');
			mAnalystic.clickToCall(activeTier+':'+'review:'+review_telephone);
		});
		//Review:Email Click
		$('body').on('click','.review-email', function(){
			var review_email = $(this).attr('href');
			mAnalystic.clickToCall(activeTier+':'+'review:'+review_email);
		});
		//Review:Google Map Link
		$('body').on('click','.review_gmap', function(){
			var review_dealername = $(this).data('gmapaddress');
			mAnalystic.clickLink(activeTier+':'+'Review:GoogleMap:DealerLocation:'+review_dealername);
		});	
		//Header:Google Map Link
		$('body').on('click','.header-gmap', function(){
			var header_gmapaddress = $(this).data('gmapaddress');
			mAnalystic.clickLink(activeTier+':'+'Header:GoogleMap:DealerLocation:'+header_gmapaddress);
		});	
		
		//Header:logo
		$('body').on('click','.top-logo-vehicle', function(){ 
			mAnalystic.clickLink(activeTier+':'+'Header:Vehicle-Details-Logo');
		});	
		 
		//MOPAR : CHECKBOX		
		$(document).on('click','.ore_lease', function(e){

				if(e.hasOwnProperty('originalEvent')){ 			
				var linkText = $.trim($(this).next( "span" ).text());	
				if($(this).prop('checked') == true){
					var ore_lease_chk = 'Checked'; 
				} else { 
					var ore_lease_chk = 'Unchecked'; 
				}
				 var  pkgType = $(this).closest('[data-pkg-type]').attr('data-pkg-type');
				mAnalystic.clickLink(activeTier+':'+'mopar:'+ore_lease_chk+':'+pkgType+':'+linkText);
				}else{}
				 		 
		});
		$(document).on('click','.ore_finance', function(e){ 
			if(e.hasOwnProperty('originalEvent')){ 			
				var linkText = $.trim($(this).next( "span" ).text());	
				if($(this).prop('checked') == true){
					var ore_lease_chk = 'Checked'; 
				} else { 
					var ore_lease_chk = 'Unchecked'; 
				}
				 var  pkgType = $(this).closest('[data-pkg-type]').attr('data-pkg-type');
				mAnalystic.clickLink(activeTier+':'+'mopar:'+ore_lease_chk+':'+pkgType+':'+linkText);
				}else{}
				 		 
		});
		$(document).on('click','.availedPackages li .removePkg',function(){
					var pkgName = $(this).parent('li').attr('data-package-name').trim(),
					pkgType = $(this).closest('[data-pkg-type]').attr('data-pkg-type');
					
					mAnalystic.clickLink(activeTier+':'+'mopar:removed:'+pkgName);
		});
		
		//Captcha Refresh
		$(document).on('click','#refresh', function(){
			mAnalystic.clickLink(activeTier+':'+'lead:captcha-refresh');
		});
		
		// TOGGLE : FINANCE / LEASE -> REFER VEHICLE SCRIPT
		
		//FOOTER
		$(document).on('click','.footer-logos-field a, .footerLinks ul li a',function(){
					var flink = $(this).attr('title');  
					mAnalystic.clickLink(activeTier+':'+'footer:'+flink.replace(",", "").replace(" ", "-").replace(" ", "-").replace(" ", "-").replace(" ", "-"));
		}); 
		
		 
		/******************* FORM START **********************************************/ 
		$("#reviewSubmit input").bind("keydown", function(){
			mAnalystic.oFormStart(oPageAction, taxonomy+"lead-submission", "lead", "modal");
			$("#reviewSubmit input").unbind("keydown");    
		});                    
		$("#reviewSubmit select").bind("change", function(){
		   mAnalystic.oFormStart(oPageAction, taxonomy+"lead-submission", "lead", "modal");		
		  $("#reviewSubmit select").unbind("change");
		});
		
		/*$("#leaseCalc input").bind("keydown", function(){
				mAnalystic.oFormStart(oPageAction, taxonomy+"lease-calculator", "finance", "modal");
				$("#leaseCalc input").unbind("keydown");	
		});					
		$("#leaseCalc select").bind("change", function(){		  
				mAnalystic.oFormStart(oPageAction, taxonomy+"lease-calculator", "finance", "modal");
				$("#leaseCalc input").unbind("keydown");	
		});	    
		$("#paymentCalc input").bind("keydown", function(){
				mAnalystic.oFormStart(oPageAction, taxonomy+"payment-calculator", "finance", "modal");
				$("#paymentCalc input").unbind("keydown");	
		});					
		$("#paymentCalc select").bind("change", function(){		  
				mAnalystic.oFormStart(oPageAction, taxonomy+"payment-calculator", "finance", "modal");
				$("#paymentCalc input").unbind("keydown");	
		});	    

		$("#cashCalc input").bind("keydown", function(){
				mAnalystic.oFormStart(oPageAction, taxonomy+"cash-calculator", "finance", "modal");
				$("#cashCalc input").unbind("keydown");	
		});	*/	      
		 
        

		
	},
	isDevice: function(){
			if(mAnalystic.isMobile()) return "Mobile";
			else return "Desktop";
	},
	isMobile: function(){
		try {
					if(/Android|webOS|iPhone|iPad|iPod|pocket|psp|kindle|avantgo|blazer|midori|Tablet|Palm|maemo|plucker|phone|BlackBerry|symbian|IEMobile|mobile|ZuneWP7|Windows Phone|Opera Mini/i.test(navigator.userAgent)) {
					 return true;
					};
					return false;
				 } catch(e){ console.log("Error in isMobile"); return false; }
	},
	PageLoad: function(oPageAction){ 
		if(oPageAction == 'brand-specific'){
				var make = $('#paramBrandName').val(); 
				var pageName =	"DriveFCA:us:"+make+":brand-specific";		
				 				
				window.digitalData.newEvent({type: "CustomTagEvent", eventName: "JSpageView", eventAction: "contentView", attributes: {pageID: pageName+'_'+make+"_virtualPageView"}});
			}else 		
			if(oPageAction == 'home'){
			 
				var pageName =	"DriveFCA:US";	 			
				window.digitalData.newEvent({type: "CustomTagEvent", eventName: "JSpageView", eventAction: "contentView", attributes: {pageID: pageName+"_virtualPageView"}});
			}
			else if(oPageAction == 'search-new-inventory'){
				var make 	= $('#params_make').val();
				var ostatus = $('#params_vechType').val(); 
				var zipcode = localCache.get('zipCodeEntered');
				var model 	= $('#params_modelname').val(); 
				var year 	= $('#params_year').val();
				var tier = $('#tier').val();
				var pageName =	"DriveFCA-"+tier+"-"+make+":"+model.split(" ").join("-")+":"+year+':'+ostatus+":us:search-new-inventory"; 
			}else{
				var vin 	= $('#vin').val();			
				var dealerID = $('#dealer_code').val();
				var tier = $('#tier').val();
				var pageName =	"DriveFCA:"+tier+":"+dealerID+":"+vin+":us:vehicle-detail"; 
			} 
			window.digitalData.newEvent({type: "CustomTagEvent", eventName: "JSpageView", eventAction: "contentView", attributes: {pageID: pageName+"_virtualPageView"}});			
		 
	},
	InventoryListing: function(){
				var make 	= $('#params_make').val();
				var ostatus = $('#params_vechType').val(); 
				var zipcode = localCache.get('zipCodeEntered');
				var model 	= $('#params_modelname').val(); 
				var year 	= $('#params_year').val();
				
				window.digitalData.newEvent({type: "CustomTagEvent", eventName: "filtered", eventAction: "search", attributes: {year: year, make: make, model: model, trim: "", type: ostatus, zipcode: zipcode}});
	},
	InventoryFilter: function(filterHead, filterValue){
					var language = "en";
					var responsiveState = mAnalystic.isDevice(); 
					var make 	= $('#params_make').val();
					var model 	= $('#params_modelname').val();
					var oPageAction = $('meta[name=pageName]').attr("content");
					var pageName =	"DriveFCA:"+make+":"+model.split(" ").join("-")+":us:"+oPageAction.split("_").join("-");
					 
					window.digitalData.page.pageInfo = {
									pageName : pageName,   //dealer:us:2016:wrangler:vehicle-details
									language : language,
									responsiveState : responsiveState, 
									filterHead: filterHead,
									filterValue: filterValue, 
									geoRegion: "us",
									make: make,
									model: model,
									
								};
								
				window.digitalData.newEvent({type: "CustomTagEvent", eventName: "filtered", eventAction: "search", attributes: {pageName: pageName,language : language, responsiveState : responsiveState, filterHead: filterHead, filterValue: filterValue, geoRegion: "us" }});				
 
	},
	oFormStart: function(oPageAction, formType, formDescription, displayType){ 
			window.digitalData.newEvent({type: "CustomTagEvent", eventName: "start", eventAction: "form", attributes: {formDescription: formDescription, formType: formType, displayType: displayType, displayFormat: "modal"}});		 
		
	},	
	clickLink: function(linkText){ 
		window.digitalData.newEvent({type: "CustomTagEvent", eventName: "linkclick", eventAction: "linkClick", attributes: {linkText: linkText}});		
	},
	clickToCall: function(linkText){ 
		window.digitalData.newEvent({type: "CustomTagEvent", eventName: "clickToCall", eventAction: "pageElementInteraction", attributes: {linkText: linkText}});		
	},
	CTA: function(linkText){ 
		window.digitalData.newEvent({type: "CustomTagEvent", eventName: "CTA", eventAction: "buttonClick", attributes: {linkText: linkText}});		
	},
	navigationClick: function(linkText, position){ 
		window.digitalData.newEvent({type: "CustomTagEvent", eventName: "navClick", eventAction: "pageElementInteraction", attributes: {position: position, linkText: linkText}});
	},
	tradeIntoolStart: function(formDescription, formType, displayType, displayFormat, tradeInProvider){ 
		window.digitalData.newEvent({type: "CustomTagEvent", eventName: "start", eventAction: "form", attributes: {formDescription: formDescription, formType: formType, displayType: displayType, displayFormat: displayFormat, tradeInProvider: tradeInProvider}});
		
	},
	tradeInToolSubmit: function(formDescription, formType, displayType, displayFormat, tradeInProvider, tyear, tmake, tmodel, tdesiredYear, tdesiredMake, tdesignerModel ){ 
		window.digitalData.newEvent({type: "CustomTagEvent", eventName: "submit", eventAction: "form", attributes: {formDescription: formDescription, formType: formType, displayType: displayType, displayFormat: displayFormat, tradeInProvider: tradeInProvider, tradeInVehicleYear: tyear, tradeInVehicleMake: tmake, tradeInVehicleModel: tmodel, desiredVehicleYear: tdesiredYear, desiredVehicleMake: tdesiredMake, desiredVehicleModel: tdesignerModel}});
	},
	paymentCalculator: function(year, make, model, trim, ostatus){ 
		window.digitalData.newEvent({type: "CustomTagEvent", eventName: "start", eventAction: "paymentCalculator", attributes: {year: year, make: make, model: model, trim: trim, status: ostatus}});
	},
	onFormComplete: function(leadId,displayType){		 
		window.digitalData.newEvent({type: "CustomTagEvent", eventName: "submit", eventAction: "form", attributes: {formDescription: "lead", formType: "get a quote", leadId: leadId, displayType: displayType, displayFormat: "inPage"}});
		
	},
	onExploreFormComplete: function(leadId,displayType){		 
		window.digitalData.newEvent({type: "CustomTagEvent", eventName: "submit", eventAction: "form", attributes: {formDescription: "lead", formType: "Finance options", leadId: leadId, displayType: displayType, displayFormat: "inPage"}});
		
	},
	onFirstFormComplete: function(displayType){		 
		window.digitalData.newEvent({type: "CustomTagEvent", eventName: "submit", eventAction: "form", attributes: {formDescription: "lead", formType: "First Lead Form", displayType: displayType, displayFormat: "inPage"}});
		
	},
	onCloudID: function(leadId,displayType){		 
		/* window.digitalData.newEvent({type: "CustomTagEvent", eventName: "submit", eventAction: "form", attributes: {formDescription: "lead", formType: "Initial Form SessionID", leadId: leadId, displayType: displayType, displayFormat: "inPage"}}); */
		
	},
	pageObject: function(oPageAction,responsiveState,pageType){
		if(oPageAction == 'search-new-inventory' || oPageAction == 'home'){
			window.digitalData.page = {
				pageInfo: {
					pageName: oPageAction,
					language: 'EN',
					responsiveState: responsiveState,
				},
				category: {
					pageType: pageType
				  }

			};
			 
		}else{
			
		}
	},
	pageInfo: function(oPageAction, origin){
			if(origin == 'ore') mAnalystic.ore(oPageAction);
			else if(origin == 't1') mAnalystic.tier1(oPageAction);
			else if(origin == 't3') mAnalystic.tier3(oPageAction);
			else  mAnalystic.ore(oPageAction);
	},
	ore: function(oPageAction){
					var language = "en";
					var responsiveState = mAnalystic.isDevice();


					
					if(oPageAction == 'brand-specific' ){
							var make 	= $('#paramBrandName').val(); 
							
							var pageName =	"DriveFCA:"+make+":US:brand-specific";


							window.digitalData.page.pageInfo = {
									pageName : pageName,    
									language : language,
									responsiveState : responsiveState, 
									websiteTier: "standalone",
									geoRegion: "US",

								};
								window.digitalData.page.category = {
									pageType : "BrandSpecific"
								};	 
					}else if(oPageAction == 'search-new-inventory' ){
							var make 	= $('#params_make').val();
							var model 	= $('#params_modelname').val();
							var year 	= $('#params_year').val();
							var ostatus = $('#params_vechType').val();
							var zipcode = $('#dealerZipCode').val();
							var pageName =	"DriveFCA:"+make+":"+model.split(" ").join("-")+":US:"+oPageAction.split("_").join("-");
							
							window.digitalData.page.pageInfo = {
									pageName : pageName,   //dealer:us:2016:wrangler:vehicle-details
									language : language,
									responsiveState : responsiveState,
									dealerID : null,
									dealerName : null,
									websiteTier: "standalone",
									vehicleMake: make,
									vehicleModel: model,
									vehicleStatus: ostatus, 
									vehicleYear: year,
									zipcode:zipcode,
									geoRegion: "US",
								};

							window.digitalData.page.category = {
									pageType : "Inventory"
								};
								
					}else if(oPageAction == 'vehicle-information'){
								
								var year 	= $('#year').val();
								var make 	= $('#make').val();
								var model 	= $('#model').val(); 
								var ostatus = $('#vehicle_type').val();
								var vin 	= $('#vin').val();								
								var trim 	= $('.vd_title').text();
								
								var dealerID = $('#dealer_code').val(); 
								var dealerName = $('#dlr_dba_name').val(); 
								
								var pageName =	"DriveFCA:"+make+":"+dealerName.split(" ").join("-")+":US:"+oPageAction.split("_").join("-");
			
								window.digitalData.page.pageInfo = {
									pageName : pageName,   //dealer:us:2016:wrangler:vehicle-details
									language : language,
									responsiveState : responsiveState,
									dealerID : dealerID,
									dealerName : dealerName,
									vehicleMake: make,
									vehicleModel: model,
									vehicleStatus: ostatus,
									vehicleTrim: trim,
									vehicleVin: vin,
									vehicleYear: year,
									websiteTier: "standalone",
									geoRegion: "US",
								};	

								window.digitalData.page.category = {
									pageType : "Vehicle Details"
								};								
					}else{
								var pageName =	"DriveFCA:US:vehicle-details";
								window.digitalData.page.pageInfo = {
									pageName : pageName,    
									language : language,
									responsiveState : responsiveState, 
									websiteTier: "standalone",
									geoRegion: "US",

								};
								window.digitalData.page.category = {
									pageType : "Homepage"
								};	
					}
					
					//console.log(window.digitalData.page.pageInfo);
	},
	tier1: function(oPageAction){
			var language = "en";
					var responsiveState = mAnalystic.isDevice();
					
					if(oPageAction == 'brand-specific' ){
							var make 	= $('#paramBrandName').val(); 
							var pageName =	"DriveFCA:"+make+":US:brand-specific";
							
							window.digitalData.page.pageInfo = {
									pageName : pageName,    
									language : language,
									responsiveState : responsiveState, 
									websiteTier: "standalone",
									geoRegion: "US",

								};
								window.digitalData.page.category = {
									pageType : "BrandSpecific"
								};	 
					}else if(oPageAction == 'search-new-inventory' ){
							var make 	= $('#params_make').val();
							var model 	= $('#params_modelname').val();
							var year 	= $('#params_year').val();
							var ostatus = $('#params_vechType').val();
							var zipcode = $('#dealerZipCode').val();
							var pageName =	"DriveFCA:"+make+"-"+model.split(" ").join("-")+":US:"+oPageAction.split("_").join("-");
							
							window.digitalData.page.pageInfo = {
									pageName : pageName,   //dealer:us:2016:wrangler:vehicle-details
									language : language,
									responsiveState : responsiveState,
									dealerID : null,
									dealerName : null,
									websiteTier: "tier1",
									vehicleMake: make,
									vehicleModel: model,
									vehicleStatus: ostatus, 
									vehicleYear: year,
									zipcode:zipcode,
									geoRegion: "US",
								};

							window.digitalData.page.category = {
									pageType : "Inventory"
								};
								
					}else if(oPageAction == 'vehicle-information'){
								
								var year 	= $('#year').val();
								var make 	= $('#make').val();
								var model 	= $('#model').val(); 
								var ostatus = $('#vehicle_type').val();
								var vin 	= $('#vin').val();								
								var trim 	= $('.vd_title').text();
								
								var dealerID = $('#dealer_code').val(); 
								var dealerName = $('#dlr_dba_name').val(); 
								
								var pageName =	"DriveFCA:"+make+"-"+dealerName.split(" ").join("-")+":US:"+oPageAction.split("_").join("-");
			
								window.digitalData.page.pageInfo = {
									pageName : pageName,   //dealer:us:2016:wrangler:vehicle-details
									language : language,
									responsiveState : responsiveState,
									dealerID : dealerID,
									dealerName : dealerName,
									vehicleMake: make,
									vehicleModel: model,
									vehicleStatus: ostatus,
									vehicleTrim: trim,
									vehicleVin: vin,
									vehicleYear: year,
									websiteTier: "tier1",
									geoRegion: "US",
								};	

								window.digitalData.page.category = {
									pageType : "Vehicle Details"
								};								
					}else{
								var pageName =	"DriveFCA:US:"+oPageAction.split(" ").join("-");
								window.digitalData.page.pageInfo = {
									pageName : pageName,    
									language : language,
									responsiveState : responsiveState, 
									websiteTier: "tier1",
									geoRegion: "US",

								};
								window.digitalData.page.category = {
									pageType : "Homepage"
								};	
					}
	},
	tier3: function(oPageAction){
		
		
		var language = "en";
					var responsiveState = mAnalystic.isDevice();
					
					if(oPageAction == 'search-new-inventory' ){
							var make 	= $('#params_make').val();
							var model 	= $('#params_modelname').val();
							var year 	= $('#params_year').val();
							var ostatus = $('#params_vechType').val();
							var zipcode = $('#dealerZipCode').val();
							var pageName =	"DriveFCA:"+make+"-"+model.split(" ").join("-")+":US:"+oPageAction.split("_").join("-");
							
							window.digitalData.page.pageInfo = {
									pageName : pageName,   //dealer:us:2016:wrangler:vehicle-details
									language : language,
									responsiveState : responsiveState,
									dealerID : null,
									dealerName : null,
									websiteTier: "tier3",
									vehicleMake: make,
									vehicleModel: model,
									vehicleStatus: ostatus, 
									vehicleYear: year,
									zipcode:zipcode,
									geoRegion: "US",
								};

							window.digitalData.page.category = {
									pageType : "Inventory"
								};
								
					}else if(oPageAction == 'vehicle-information'){
								
								var year 	= $('#year').val();
								var make 	= $('#make').val();
								var model 	= $('#model').val(); 
								var ostatus = $('#vehicle_type').val();
								var vin 	= $('#vin').val();								
								var trim 	= $('.vd_title').text();
								
								var dealerID = $('#dealer_code').val(); 
								var dealerName = $('#dlr_dba_name').val(); 
								
								var pageName =	"DriveFCA:"+make+"-"+dealerName.split(" ").join("-")+":US:"+oPageAction.split("_").join("-");
			
								window.digitalData.page.pageInfo = {
									pageName : pageName,   //dealer:us:2016:wrangler:vehicle-details
									language : language,
									responsiveState : responsiveState,
									dealerID : dealerID,
									dealerName : dealerName,
									vehicleMake: make,
									vehicleModel: model,
									vehicleStatus: ostatus,
									vehicleTrim: trim,
									vehicleVin: vin,
									vehicleYear: year,
									websiteTier: "tier3",
									geoRegion: "US",
								};	

								window.digitalData.page.category = {
									pageType : "Vehicle Details"
								};								
					}else{
								var pageName =	"DriveFCA:US:"+oPageAction.split(" ").join("-");
								window.digitalData.page.pageInfo = {
									pageName : pageName,    
									language : language,
									responsiveState : responsiveState, 
									websiteTier: "tier3",
									geoRegion: "US",

								};
								window.digitalData.page.category = {
									pageType : "Homepage"
								};	
					}
					
					
					
			/*var language = "en";
					var responsiveState = mAnalystic.isDevice();
					
					if(oPageAction == 'search-new-inventory' ){
							var make 	= $('#params_make').val();
							var model 	= $('#params_modelname').val();
							var pageName =	"DriveFCA-"+make+"-"+model.split(" ").join("-")+":US:"+oPageAction.split("_").join("-");
							window.digitalData.page.pageInfo = {
									pageName : pageName,   //dealer:us:2016:wrangler:vehicle-details
									language : language,
									responsiveState : responsiveState,
									dealerID : null,
									dealerName : null,
									tier3_dealerState : null,
									tier3_dealerZipcode : null,
									providerName : null,
									websiteTier : "tier 3",
									tier3_trafficType : null,
									websiteTier: origin,
									geoRegion: "US",
								};

							window.digitalData.page.category = {
									pageType : "Inventory"
								};
								
					}else if(oPageAction == 'vehicle-information'){
								
								var year 	= $('#year').val();
								var make 	= $('#make').val();
								var model 	= $('#model').val(); 
								var ostatus = $('#vehicle_type').val();
								var vin 	= $('#vin').val();								
								var trim 	= $('.vd_title').text();
								
								var dealerID = $('#dealer_code').val(); 
								var dealerName = $('#dlr_dba_name').val(); 
								
								var pageName =	"DriveFCA-"+make+"-"+dealerName.split(" ").join("-")+":US:"+oPageAction.split("_").join("-");
								// window.digitalData.newEvent({type: "CustomTagEvent", eventName: "JSpageView", eventAction: "contentView", attributes: {pageID: "tier3_"+pageName+"_virtualPageView"}});
								window.digitalData.page.pageInfo = {
									pageName : pageName,   //dealer:us:2016:wrangler:vehicle-details
									language : language,
									responsiveState : responsiveState,
									dealerID : dealerID,
									dealerName : dealerName,
									tier3_dealerState : null,
									tier3_dealerZipcode : null,
									providerName : null,
									websiteTier : "tier 3",
									tier3_trafficType : null, 
									vehicleMake: make,
									vehicleModel: model,
									vehicleStatus: ostatus,
									vehicleTrim: trim,
									vehicleVin: vin,
									vehicleYear: year,
									websiteTier: origin,
									geoRegion: "US",
								};	

								window.digitalData.page.category = {
									pageType : "Vehicle Details"
								};								
					}else{
								var pageName =	"DriveFCA:US:"+oPageAction.split(" ").join("-");
								
								window.digitalData.page.pageInfo = {
									pageName : pageName,    
									language : language,
									responsiveState : responsiveState, 
									websiteTier: origin,
									geoRegion: "US",
									tier3_dealerState : null,
									tier3_dealerZipcode : null,
									providerName : null,
									websiteTier : "tier 3",
									tier3_trafficType : null,
								};
								window.digitalData.page.category = {
									pageType : "Homepage"
								};	
					}
					*/
	},
};


var localCache = { 
    remove: function (key) {
        localStorage.removeItem(key);
    },
    exist: function (key) {
        return localStorage.getItem(key) !== null;
    },
    get: function (key) {
        return localStorage.getItem(key); 
    },
    set: function (key, value) {
        localStorage.setItem(key, value); 
        return true;
    }
};

 



 
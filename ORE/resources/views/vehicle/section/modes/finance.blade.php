 @if($return_array['params_vechType'] == 'new' && $return_array['params_year'] >= 2019)
 <div id="finance" class="tab-pane fade ">
     @else
     <div id="finance" class="tab-pane fade in active">
         @endif
         <div class="marginY-3 calculator_message">
             <span class="rBold size-19 "></span>
             <span class="rBold size-19 main_msrp_finance finance_restrict_html"><img src="{{ cdn('images/ajax-loader.gif') }}" alt="loader"></span>
             <span class="size-20 marginX-1 finance_restrict month_color"> /month </span>
             <span class="price-tag price-tag-finance finance_restrict" data-env="{{ env('APP_ENV') }}" @if(env('APP_ENV') !='dev' || env('APP_ENV') !='local' ) style="display:none;" @endif>
                 <a href="javascript:void(0);" id="modalPaymentCalc" class="arrow-right modalOpener " data-target="#availableProgramPopup">Price Details</a></span>
         </div>

         <p class="finance_restrict taxesandfees_color">*Est. Monthly Payments</p>

          <p class="msrp_color size-13 finance_restrict"> 
							  National offers Reflected.  <span class="finance_main_incentive" style="display:none;"></span>
							 </p>
                             <p class="msrp_color size-13 finance_restrict"> 
                             See dealer for up to date offers.
                             </p>

       
         <h6 class="size-13 text-uppercase msrp_color">
             @if($return_array['params_vechType'] == 'new') MSRP @else Internet Price @endif
             <span class="black size-19 ore_finance_msrp_price">${{ number_format($return_array['vehicle_params']['msrp']) }}</span></h6>
         <div class="wishListBlock">
         </div>
     </div>
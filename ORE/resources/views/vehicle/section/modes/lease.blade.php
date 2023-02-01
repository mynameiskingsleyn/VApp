@if($return_array['params_vechType'] == 'new' && $return_array['params_year'] >= 2019)
<div id="lease" class="tab-pane fade in active">
@else
<div id="lease" class="tab-pane fade">
@endif

                            <div class="marginY-3 calculator_message">
								<span class="rBold size-19 lease_restrict"></span>
								<span class="rBold size-19 main_msrp_lease lease_restrict_html">
										<img alt="loader" src="{{ cdn('images/ajax-loader.gif') }}" >
								</span>
								<span class="size-20 marginX-1 lease_restrict month_color"> /month</span>
								<span class="price-tag price-tag-lease" data-env="{{ env('APP_ENV') }}"  @if(env('APP_ENV') != 'dev' || env('APP_ENV') != 'local') style="display:none;" @endif>
								<a class="arrow-right"  href="javascript:void(0);" id="modalLeaseCalc" data-toggle="modal" data-target="#availableProgramPopup" >Price Details</a></span>
    
    </div>
                           
								<p class="lease_restrict taxesandfees_color">*Est. Monthly Payments</p>

								  <p class="msrp_color size-13 lease_restrict"> 
							  National offers Reflected.  <span class="lease_main_incentive" style="display:none;"></span>
							 </p>
                             <p class="msrp_color size-13 lease_restrict"> 
                             See dealer for up to date offers.
                             </p>

 
                            <h6 class="size-13 text-uppercase msrp_color">MSRP <span class="black size-19 ore_lease_msrp_price">${{ number_format($return_array['vehicle_params']['msrp']) }}</span></h6>
                             <div class="wishListBlock">
							  </div>
                        </div>
  
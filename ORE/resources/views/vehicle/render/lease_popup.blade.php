<div class="modal fade" id="leaseCalc" role="dialog" style="display: none;">
			<div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h2 class="modal-title text-uppercase med title_lease_popup">
					
					@php
				$combine_vehicle_name =  $return_array['vehicle_params']['model'].' '.$return_array['vehicle_params']['trim_desc'];
				$combine_name_replace = strpos(strtolower($return_array['vehicle_params']['trim_desc']), strtolower( $return_array['vehicle_params']['model']), 0);
				if(trim($combine_name_replace)==0 && trim($combine_name_replace)!='') 
							$combine_vehicle_name = $return_array['vehicle_params']['trim_desc'];
			@endphp
			{{ $return_array['vehicle_params']['year'] }} {{ $combine_vehicle_name }} {{ $return_array['drive_type'] }}
					
					</h2>
                    <h5 class="text-uppercase mTop-0 rLight vin_lease_popup">Vin : <span>{{ $return_array['vehicle_params']['vin'] }}</span></h5>
                    <h2 class="modal-title med pricedetails_lease_popup">Price Details</h2>

                  </div>
                  <div class="modal-body">
                      <ul class="popUpScroll">
                      <input type="hidden" name="l_MSRPHidden" id="l_MSRPHidden" value="" />
                      <input type="hidden" name="l_destHidden" id="l_destHidden" value="0" />
                      <input type="hidden" name="l_otherCapCostsHidden" id="l_otherCapCostsHidden" value="0" />
                      <input type="hidden" name="l_dealerDiscountHidden" id="l_dealerDiscountHidden" value="0" />
                      <input type="hidden" name="l_incentivesHidden" id="l_incentivesHidden" value="0" />

                       <input type="hidden" name="l_residualHidden" id="l_residualHidden" value="1" />
                       <input type="hidden" name="l_rateHidden" id="l_rateHidden" value="0.168" />

                        <input type="hidden" name="l_estimateHidden" id="l_estimateHidden" value="" />


					@if(env('APP_ENV') == 'dev' || env('APP_ENV') == 'local')
					<!--<li>Source Name <span style="float:right;">
						<input type="radio" aria-labelledby="finance_type_CCPA" aria-describedby="finance_type_CCPA" name="lease_finance_type" id="finance_type_CCPA" class="_lease_finance_type" value="ChryslerCapital"  checked="checked" />Chrysler Capital &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<input type="radio" aria-labelledby="finance_type_ally" aria-describedby="finance_type_ally" name="lease_finance_type" id="finance_type_ally"  class="_lease_finance_type" value="AllyFinancialInc" />Ally Financial Inc
						</span>
						</li>
						-->
						<input type="hidden" name="lease_finance_type" id="finance_type_CCPA" class="_f_finance_type" value="ChryslerCapital"   />
					@else
						<input type="hidden" name="lease_finance_type" id="finance_type_CCPA" class="_f_finance_type" value="ChryslerCapital"   />
					@endif

                          <li><h4>MSRP (Sticker Price) <span id="l_q_msrp" class="badge disclaimer">1</span>  <span class="stickerPrice"></span></h4></li>
                          {{-- <li><h4>Destination Charges <span class="baseModel">$1495</span></h4></li> --}}
							  <li>
									<h4>
										Down Payment <input type="hidden" title="Down Payment" id="downpayLeaseHidden" value="10" />(<span class="dPaymentRange LPaymentRange">10</span>%)
										<input type="tel" title="Down Payment" class="form-control tradeVal" id="ore_txtbox_lease_downpayment" name="" value="0" maxlength="8" pattern="[0-9]*" inputmode="numeric">
									</h4>
							  </li>
                          <li><h4>Trade-in Value   <input type="tel" title="Trade-in Value" class="form-control tradeVal ore_txtbox_ltradein" id="leaseTradeInValueField" name="" value="{{ $tradeInfo ? $tradeInfo['price']: 0 }}" maxlength="8" pattern="[0-9]*" inputmode="numeric"></h4></li>




						  <li>
							<h4 class="dOpener IncentivesMerkle"><span class="uLine">Incentives</span> <span class="mDestCharge rBold">$0 </span><i class="pull-right glyphicon glyphicon-menu-down" ></i></h4>
							<div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
                               <span class="calc_incentives_desc">No Incentives Applicable</span>
                            </div>
						  </li>



							 <li><h4 class="dOpener expAddOffMerkle"><span class="uLine">Explore Conditional Offers</span>
								<span class="mDestLExplore rBold" style="position: absolute; right: 60px;">$0 </span>
							 <i class="pull-right glyphicon glyphicon-menu-down"></i></h4>
                            <div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
							<span class="calc_offers_desc">No Additional Offers Applicable</span>

							<input disabled="disabled" type="hidden" title="Dealer Discount" class="form-control lease_dealer_discount" id="lease_dealer_discount" name="lease_dealer_discount" value="0" maxlength="8">

                            </div>


                        </li>

						@if(config('ore.calc.additioanal_offer'))
					<li>
						<h4>Additional Discount 
						<input type="tel" title="Additional Discount" class="form-control numericval additional_discount" id="lease_additional_disc" name="lease_additional_disc" placeholder="0" value="0" maxlength="4"  style="text-align: right;"  maxlength="8" pattern="[0-9]*" inputmode="numeric">
						</h4>
					</li>
					@else
						<input type="hidden" class="form-control  numericval additional_discount" id="lease_additional_disc" name="lease_additional_disc" value="0" />
					@endif

						@if(config('ore.calc.dealer_discount'))
						  <li>
							<h4 class="dOpener DlrDiscMerkle"><span class="uLine">Dealer Discount</span>
									<span class="mDestLDlrDisc rBold" style="position: absolute; right: 60px;">$0 </span>
									<i class="pull-right glyphicon glyphicon-menu-down"></i></h4>
							</h4>
							<div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
                               <span class="dealer_disc_update">No Dealer Discounts Applicable</span>
                            </div>
						  </li>
						  @endif

						{{--	<li>
								<h4 class="dOpener"><span class="">Dealer Discount</span>
								<input type="text" title="Dealer Discount" class="form-control lease_dealer_discount" id="lease_dealer_discount" name="lease_dealer_discount" value="0" maxlength="8">
								</h4>
						</li> --}}

						{{--
						<li><h4 class="dOpener"><span class="uLine">Taxes</span> <i class="pull-right glyphicon glyphicon-menu-down"></i></h4>
                            <div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
                                <span class="calc_taxes_desc">No Taxes Applicable</span>
                            </div>
                        </li>
						--}}


                          <li class="estimatedBlock">
                          <span class="text-danger CalcLeaseError hideMe">
						  {{--   The total for your Down Payment, Trade In Value and Dealer Discount cannot exceed the Estimated amount financed. Please adjust one or all values to view estimate. --}}
                            </span>
									<h4 class="bold text-uppercase ">
										Adjusted Capitalized Costs
										<span><span class="ore_disp_lease_estimate"><img alt="loader" src="{{ cdn('images/ajax-loader.gif') }}" ></span></span>

									</h4>
									<p class="reg mTop-1 instructions ">
												Includes available offers and incentives.
									</p>

                              <div class="row">
                                <div class="col-xs-6">
                                    <div><span>Annual Mileage</span> <span id="l_q_thirdparty" class="badge disclaimer">2</span></div>
                                    <div>
                                        <div class="select-wrapper">
                                            <select class="form-control duration yearlyMilage" aria-labelledby="yearlyMilage" id="yearlyMilage">
                                                <option value="12000">12,000 Mileage</option>
                                                <option value="10000" selected="select">10,000 Mileage</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                  <div class="col-xs-6">
                                      <div><span>Term Duration</span></div>
                                      <div>
                                        <div class="select-wrapper lease_wrapper">
                                           <label style="display:none;" for="lease_terms">hide me</label>
                                            <select class="form-control duration ore_dropdown_lease_terms" id="lease_terms">
                                                <option value=""><img src="{{ cdn('images/ajax-loader.gif') }}"></option>
											</select>
                                        </div>
                                      </div>
                                  </div>
                              </div>
                          </li>
                      </ul>
                      <div class="text-right buyFor">
                          <h4 class="mBottom-0 text-uppercase med size-20 l_alter_div">Lease For
								   <span class="buyForPrice"><span class="ore_lease_emi">
								   <img alt="loader" src="{{ cdn('images/ajax-loader.gif') }}" >
								   </span>
								   <span id="l_q_cost" class="badge disclaimer">3</span>
						   </h4>

						 <p class="l_alter_div">per month for
								<span class="ore_emi_lease_month">
									<img alt="loader" src="{{ cdn('images/ajax-loader.gif') }}" >
								</span> months.
						</p>
						<span class="dummy_pcacl_lease"></span>

                      </div>

					  <div id="l_m_msrp" class="disclaimerMessage" style="display:none;">
							<p>
								1. {{ config('ore.disclaimer.msrp') }}
							</p>
							<span id="l_c_msrp" class="closeDisclaimer">X</span>
						</div>

						<div id="l_m_thirdparty" class="disclaimerMessage" style="display:none;">
							<p>
								2 . {{ config('ore.disclaimer.annual_milage') }}
							</p>
							<span id="l_c_thirdparty" class="closeDisclaimer">X</span>
						</div>



						<div id="l_m_cost" class="disclaimerMessage" style="display:none;">
							<p>
								 
								3.This value is for estimation purposes only. You may not be able to lease at this rate.
							</p>
							<span id="l_c_cost" class="closeDisclaimer">X</span>
						</div>

						<div id="l_m_costSecond" class="disclaimerMessage" style="display:none;">
							<p>
								 
							4.This value is for estimation purposes only. You may not be able to lease at this rate.
							</p>
							<span id="l_c_costSecond" class="closeDisclaimer">X</span>
						</div>

						<div id="l_m_insentive" class="disclaimerMessage" style="display:none;">
							<p>
								 {{ config('ore.disclaimer.incentive') }}
							</p>
							<span id="l_c_insentive" class="closeDisclaimer">X</span>
						</div>

						<div >
						<p class="l_alter calculator_disclaimers">Estimated monthly payment. <span id="l_q_costSecond" class="badge disclaimer">4</span> 

 A security deposit may be required. Tax, title, license and other fees are due at lease signing.</p>
 
						</div>
						<div>
						<p   class="calculator_disclaimers">Pricing and any estimated payments are based on incentives and discounts that are considered active in our system.  Additional or different incentives or rebates may apply in your area.  Your dealer will confirm final pricing based on the incentives and discounts that are available to you.  </p>
						</div>

                  </div>
				  
                </div>
				
              </div>
    </div>

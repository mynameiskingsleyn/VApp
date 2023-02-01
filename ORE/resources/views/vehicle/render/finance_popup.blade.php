<div class="modal fade" id="paymentCalc" role="dialog" style="display: none;">
				<div class="modal-dialog">
				  <div class="modal-content">
					<div class="modal-header">
					  <button type="button" class="close" data-dismiss="modal">×</button>
                      <h2 class="modal-title text-uppercase med">
					  @php
				$combine_vehicle_name =  $return_array['vehicle_params']['model'].' '.$return_array['vehicle_params']['trim_desc'];
				$combine_name_replace = strpos(strtolower($return_array['vehicle_params']['trim_desc']), strtolower( $return_array['vehicle_params']['model']), 0);
				if(trim($combine_name_replace)==0 && trim($combine_name_replace)!='') 
							$combine_vehicle_name = $return_array['vehicle_params']['trim_desc'];
			@endphp
			{{ $return_array['vehicle_params']['year'] }} {{ $combine_vehicle_name }} {{ $return_array['drive_type'] }}
					  
					  </h2>
                      <h5 class="text-uppercase mTop-0 rLight">Vin : <span>{{ $return_array['vehicle_params']['vin'] }}</span></h5>
                      <h2 class="modal-title med">Price Details</h2>
					</div>
					<div class="modal-body">
						<ul class="popUpScroll">
						<input type="hidden" name="F_MSRPHidden" id="F_MSRPHidden" value="" />
                       <input type="hidden" name="F_destHidden" id="F_destHidden" value="0" />

						@if(env('APP_ENV') == 'dev')

							<li>Source Name <span style="float:right;">
					  <label style="display:none;" for="finance_type_CCPA">hide me</label><input type="radio" name="f_finance_type" id="finance_type_CCPA" class="_f_finance_type" value="ChryslerCapital" checked="checked" />Chrysler Capital &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					  <label style="display:none;" for="finance_type_ally">hide me</label><input type="radio" name="f_finance_type" id="finance_type_ally"  class="_f_finance_type" value="AllyFinancialInc" />Ally Financial Inc
						</span>
						</li>
						@else

						<input type="hidden" name="f_finance_type" id="finance_type_CCPA" class="_f_finance_type" value="ChryslerCapital"   />
					 @endif

                            <li><h4>MSRP (Sticker Price) <span id="d_q_msrp" class="disclaimer badge">1</span>  <span class="f_stickerPrice"><span>$<span>37,945</span></h4></li>
							<li><h4>Down Payment <input type="hidden" id="downpayHidden" value="10" />(<span class="dPaymentRange ore_dPaymentRange">10</span>%) <label style="display:none;" for="financeDownPaymentField">hide me</label>
							<input type="tel" class="form-control tradeVal ore_txtbox_downpayment" id="financeDownPaymentField" name="" value="0" maxlength="8" pattern="[0-9]*" inputmode="numeric"></h4></li>
							 <li><h4>Trade-in Value
							 <label style="display:none;" for="financeTradeInValueField">hide me</label>
							 <input type="tel" class="form-control tradeVal ore_txtbox_tradein" id="financeTradeInValueField" value="{{ $tradeInfo ? $tradeInfo['price']: 0 }}" name=""  maxlength="8" pattern="[0-9]*" inputmode="numeric"></h4></li>

							 <li>
							<h4 class="dOpener 	IncentivesfMerkle"><span class="uLine">Incentives</span> <span class="fDestCharge rBold">$0 </span><i class="pull-right glyphicon glyphicon-menu-down" style="padding-left: 10px;"></i></h4>
							<div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
                               <span class="fcalc_incentives_desc">No Incentives Applicable</span>
                            </div>

						  </li>

					 	 <li><h4 class="dOpener expFAddOffMerkle"><span class="uLine">Explore Conditional Offers</span>
						 <span class="mDestFExplore rBold" style="position: absolute; right: 60px;">$0 </span>
						 <i class="pull-right glyphicon glyphicon-menu-down"></i></h4>
                            <div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
							<span class="fcalc_offers_desc">No Additional Offers Applicable</span>

                            </div>
							</li>

							@if(config('ore.calc.additioanal_offer'))
							<li>
								<h4>Additional Discount 
									<input type="tel" title="Additional Discount" class="form-control  numericval additional_discount" id="finance_additional_disc" name="finance_additional_disc" placeholder="0" value="0" maxlength="4"  style="text-align: right;" maxlength="8" pattern="[0-9]*" inputmode="numeric">
									
									</h4>
							</li>
							@else
							<input type="hidden" title="Additional Discount" class="form-control  numericval additional_discount" id="finance_additional_disc" name="finance_additional_disc" value="0">
				        	@endif



							@if(config('ore.calc.dealer_discount'))
						  <li>
							<h4 class="dOpener DlrDiscFMerkle">
									<span class="uLine">Dealer Discount</span>
									<span class="mDestFDlrDisc rBold" style="position: absolute; right: 60px;">$0 </span>
									<i class="pull-right glyphicon glyphicon-menu-down" ></i>
							</h4>
							<div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
                               <span class="fdealer_disc_update">No Dealer Discounts Applicable</span>
                            </div>
						  </li>

						@endif

							{{--	<li>
								<h4 class="dOpener"><span class="">Dealer Discount</span>
								<label style="display:none;" for="f_dealer_discount">hide me</label>
<input type="text" class="form-control f_dealer_discount" id="f_dealer_discount" name="f_dealer_discount" value="0" maxlength="8">
								</h4>
							</li>   --}}
                        </li>

						{{--<li><h4 class="dOpener"><span class="uLine">Taxes</span> <i class="pull-right glyphicon glyphicon-menu-down"></i></h4>
                            <div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
                                <span class="fcalc_taxes_desc">No Taxes Applicable</span>
                            </div>
                        </li>
						--}}

							<li class="estimatedBlock">
                            <span class="text-danger CalcError">
							{{-- The total for your Down Payment, Trade In Value and Dealer Discount cannot exceed the Estimated amount financed. Please adjust one or all values to view estimate. --}}
                            </span>
                              <h4 class="bold text-uppercase">Adjusted Capitalized Costs <span><span class="ore_disp_finance_estimate"><img alt="loader" src="{{ cdn('images/ajax-loader.gif') }}" ></span></span></h4>

                                <div class="row" style="padding-top: 20px;">
                                    <div class="col-sm-8 col-xs-6" data-behaviour="radioCheckBox">
                                        <div>

                                            <label class="">
											<h5 class="size-15 reg ">Annual Percentage Rate(APR)</h5>
												<span>
														<span class="chrysler_apr_in">4.35</span>% (<span class="f_c_name">Chrysler's Rate</span>
														<span id="d_q_chrysler" class="disclaimer badge">2</span>)
												</span>
											{{-- <input type="radio" class="ore_radio_apr" name="ore_radio_apr" value="4.35" checked>--}}
												 <input type="hidden" class="ore_radio_apr " name="ore_radio_apr" value="4.35" />

												</label>
                                        </div>
                                        <!--<div>
                                            <label class="customCheckBox">
                                                <input type="radio" class="ore_radio_apr" name="ore_radio_apr" value="third_party" ><span>&nbsp;</span></label>
                                             <input type="text" class="form-control thirdPartyVal ore_radio_apr_per" id="disclaimerThirdparty_field" value="6.47" value="0" maxlength="5">% (Third Party <span class="badge">8</span>)
                                        </div>-->
										<span id="d_q_thirdparty" class="disclaimer badge"></span>
										   <input type="hidden" class="form-control thirdPartyVal ore_radio_apr_per" id="disclaimerThirdparty_field" value="6.47" />
										   <input type="hidden" class="ore_radio_apr third_party-radio" name="ore_radio_apr" value="third_party" />
								 {{--  	<span>
                                            <label class="">
										 <label class="radio-container">
														 <input type="radio" class="ore_radio_apr third_party-radio" name="ore_radio_apr" value="third_party" disabled>

													<span class="checkmark"></span>
												</label>
                                                <span>&nbsp;</span></label>
                                           <input type="text" disabled class="form-control thirdPartyVal ore_radio_apr_per" id="disclaimerThirdparty_field" value="6.47" maxlength="5">% (Third Party <span id="d_q_thirdparty" class="disclaimer badge">7</span> )


                                        </span>  --}}


                                    </div>
                                    <div class="col-sm-4 col-xs-6 text-right">
                                        <div><span>Term Duration</span></div>
                                        <div class="finance_wrapper">
                                             <label style="display:none;" for="finance_terms">hide me</label>
											 <select class="form-control duration ore_dropdown_finance_terms" id="finance_terms">
                                                <option value=""><img src="{{ cdn('images/ajax-loader.gif') }}" ></option>
											</select>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>

						 <div class="text-right buyFor">
                          <h4 class="mBottom-0 text-uppercase med size-20 f_alter_div">Buy For
						   <span class="buyForPrice"><span class="ore_finance_emi"><img src="{{ cdn('images/ajax-loader.gif') }}" alt="loader"></span> <span id="d_q_cost" class="badge disclaimer">3</span>
							 </h4>
							
                          <p  class="f_alter_div">per month for <span class="ore_emi_finance_month"><img src="{{ cdn('images/ajax-loader.gif') }}" alt="loader"></span> months.</p>


						  <span class="dummy_pcacl_fianance"></span>
                      </div>

						<div id="d_m_msrp" class="disclaimerMessage" style="display:none;">
							<p>
								1. {{ config('ore.disclaimer.msrp') }}
							</p>
							<span id="d_c_msrp" class="closeDisclaimer">X</span>
						</div>

						<div id="d_m_chrysler" class="disclaimerMessage" style="display:none;">
							<p>
								2 . {{ config('ore.disclaimer.chrysler_rate') }}
							</p>
							<span id="d_c_chrysler" class="closeDisclaimer">X</span>
						</div>

						<div id="d_m_thirdparty" class="disclaimerMessage" style="display:none;">
							<p>
								7. {{ config('ore.disclaimer.third_party') }}
							</p>
							<span id="d_c_thirdparty" class="closeDisclaimer">X</span>
						</div>

						<div id="d_m_cost" class="disclaimerMessage" style="display:none;">
							<p>
								3. {{ config('ore.disclaimer.against_finance_cost') }}
							</p>
							<span id="d_c_cost" class="closeDisclaimer">X</span>
						</div>

						<div id="l_m_financeest" class="disclaimerMessage" style="display:none;">
							<p>
								4. {{ config('ore.disclaimer.finance_estimate') }}
							</p>
							<span id="l_c_financeest" class="closeDisclaimer">X</span>
						</div>
                        <div id="l_m_fininsentive" class="disclaimerMessage" style="display:none;">
							<p>
								 {{ config('ore.disclaimer.incentive') }}
							</p>
							<span id="l_c_fininsentive" class="closeDisclaimer">X</span>
						</div>
						<div>
						<p class="calculator_disclaimers">Estimated monthly payment.<span id="l_q_financeest" class="badge disclaimer">4</span></p>
						</div>
						<div>
                        <p class="calculator_disclaimers">Pricing and any estimated payments are based on incentives and discounts that are considered active in our system.  Additional or different incentives or rebates may apply in your area.  Your dealer will confirm final pricing based on the incentives and discounts that are available to you.  </p>
						</div>
                    </div>
				  </div>
				</div>
        </div>

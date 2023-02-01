<div class="modal fade" id="cashCalc" role="dialog" style="display: none;">
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
                        <input type="hidden" name="C_MSRPHidden" id="C_MSRPHidden" value="37945" />
                        <input type="hidden" name="C_destHidden" id="C_destHidden" value="0" />
                        <input type="hidden" name="C_incentivesHidden" id="C_incentivesHidden" value="3000" />

                          <li><h4>MSRP (Sticker Price) <span id="cs_q_msrp" class="disclaimer badge">1</span><span class="stickerPrice">$37,945</span></h4></li>
                           <li><h4>Trade-in Value
                           <label style="display:none;" for="cashTradeInValueField">hide me</label>
						   <input type="tel" class="form-control tradeVal ore_txtbox_ctradein" id="cashTradeInValueField" name="" value="{{ $tradeInfo ? $tradeInfo['price']: 0 }}" maxlength="8" pattern="[0-9]*" inputmode="numeric"></h4></li>

                     {{--   <li>
							<h4 class="dOpener"><span class="">Dealer Discount</span> <span class="cash_dealer_discount rBold" style="padding-right:30px;">$0 </span></h4>

						  </li> --}}
						 <li>
							<h4 class="dOpener IncentivescMerkle"><span class="uLine">Incentives</span> <span class="cDestCharge rBold">$0 </span><i class="pull-right glyphicon glyphicon-menu-down" style="padding-left: 10px;"></i></h4>
							<div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
                               <span class="ccalc_incentives_desc">No Incentives Applicable</span>
                            </div>
						  </li>



							 <li><h4 class="dOpener expCAddOffMerkle"><span class="uLine">Explore Conditional Offers</span>
							<span class="mDestCExplore rBold" style="position: absolute; right: 60px;">$0 </span>
							 <i class="pull-right glyphicon glyphicon-menu-down"></i></h4>
                            <div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
							<span class="ccalc_offers_desc">No Additional Offers Applicable</span>

                            </div>
                        </li>
						@if(config('ore.calc.additioanal_offer'))
						  <li><h4 class=""><span class="">Additional Discount </span>
						 
							<input type="tel" title="Additional Discount" class="form-control tradeVal ore_txtbox_ctradein  numericval additional_discount" id="cash_additional_disc" name="cash_additional_disc" placeholder="0" value="0" maxlength="4"  style="text-align: right;">
							 
						</li> 
						@else
							<input type="hidden" class="form-control tradeVal ore_txtbox_ctradein  numericval additional_discount" id="cash_additional_disc" name="cash_additional_disc" value="0" />
						@endif

						@if(config('ore.calc.dealer_discount'))
						    <li>
							<h4 class="dOpener DlrDiscCMerkle">
									<span class="uLine">Dealer Discount</span>
									<span class="mDestCDlrDisc rBold" style="position: absolute; right: 60px;">$0 </span>
									<i class="pull-right glyphicon glyphicon-menu-down" ></i>
							</h4>
							<div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
                               <span class="cdealer_disc_update">No Dealer Discounts Applicable</span>
                            </div>
						  </li>
						@endif



						{{--
						<li><h4 class="dOpener"><span class="uLine">Taxes</span> <i class="pull-right glyphicon glyphicon-menu-down"></i></h4>
                            <div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
                                <span class="ccalc_taxes_desc">No Taxes Applicable</span>
                            </div>
                        </li>
							--}}



						</ul>

					   <div class="text-right buyFor">
					   <p>Est. Net Price</p>
                          <h4 class="mBottom-0 text-uppercase med size-20">
                           <span class="buyForPrice">$<span class="ore_cash_emi"><img src="{{ cdn('images/ajax-loader.gif') }}" alt="loader"></span><span id="cs_q_cost" class="badge disclaimer">2</span>  </h4>

                      </div>


					    <div id="cs_m_msrp" class="disclaimerMessage" style="display:none;">
							<p>
								1. {{ config('ore.disclaimer.msrp') }}
							</p>
							<span id="cs_c_msrp" class="closeDisclaimer">X</span>
						</div>

						<div id="cs_m_cost" class="disclaimerMessage" style="display:none;">
							<p>
								2 . {{ config('ore.disclaimer.against_cost_cache') }}
							</p>
							<span id="cs_c_cost" class="closeDisclaimer">X</span>
						</div>
						<div id="l_m_cashinsentive" class="disclaimerMessage" style="display:none;">
							<p>
								  {{ config('ore.disclaimer.incentive') }}
							</p>
							<span id="l_m_cashinsentive" class="closeDisclaimer">X</span>
						</div>
						<div id="l_m_cashincentivebonus" class="disclaimerMessage" style="display:none;">
							<p>
								  {{ config('ore.disclaimer.incentive') }}
							</p>
							<span id="l_m_cashincentivebonus" class="closeDisclaimer">X</span>
						</div>
						<div>
                        <p class="calculator_disclaimers">Pricing and any estimated payments are based on incentives and discounts that are considered active in our system.  Additional or different incentives or rebates may apply in your area.  Your dealer will confirm final pricing based on the incentives and discounts that are available to you.</p>
						</div>
				  </div>
                </div>
              </div>
    </div>

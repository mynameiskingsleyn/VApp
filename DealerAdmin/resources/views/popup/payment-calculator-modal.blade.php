<div class="modal fade right" id="payment-calculator-modal" tabindex="-1" role="dialog" aria-labelledby="payment-calculator-modalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-full-height modal-right" role="document">
        <div class="modal-content">
            <div class="modal-header no-border d-block rift-soft">
                <button type="button" class="close" data-dismiss="modal" >×</button>
                <h3 class="modal-title text-uppercase pc_modelyear" style="font-weight: bold;"></h3>
                <h5 class="text-uppercase rLight pc_vin">Vin : <span></span></h5>
                <h3 class="modal-title pull-left pc_pricedetails" style="font-weight: bold;"><span></span> Price Details</h3>
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
                    <li>
                        <h4>MSRP (Sticker Price) <span class="pc_msrpstickerPrice"></span></h4>
                    </li>
                    <li class="li_downPayment">
                        <h4>
                            Down Payment <input type="hidden" title="Down Payment" id="downpayLeaseHidden" value="10" />(<span class="dPaymentRange LPaymentRange">10</span>%)
                            <input type="text" title="Down Payment" class="form-control tradeVal" id="pc_downpayment" name="" value="0" onkeypress='numbervalidate(event)' maxlength="8">
                        </h4>
                    </li>
                    <li>
                        <h4>Trade-in Value <input type="text" title="Trade-in Value" class="form-control tradeVal ore_txtbox_ltradein" id="pc_TradeInValueField" name="" value="0" onkeypress='numbervalidate(event)' maxlength="8"></h4>
                    </li>
                    <li>
                        <h4 class="dOpener IncentivesMerkle"><span class="uLine">Incentives</span> <span class="mDestCharge rBold" style="position: absolute; right: 60px;">$0 </span><i class="pull-right glyphicon glyphicon-menu-down"></i></h4>
                        <div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
                            <span class="calc_incentives_desc">No Incentives Applicable</span>

                        </div>
                    </li>
                    <li>
                        <h4 class="dOpener expAddOffMerkle"><span class="uLine">Explore Conditional Offers</span>
                            <span class="mDestLExplore rBold" style="position: absolute; right: 60px;">$0 </span>
                            <i class="pull-right glyphicon glyphicon-menu-down"></i></h4>
                        <div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
                            <span class="calc_offers_desc">No Additional Offers Applicable</span>
                            <input disabled="disabled" type="hidden" title="Dealer Discount" class="form-control pc_dealer_discount" id="pc_dealer_discount" name="pc_dealer_discount" value="0" maxlength="8">
                        </div>
                    </li>
                    <li>
                        <h4 class="dOpener DlrDiscMerkle"><span class="uLine">Dealer Discount</span>
                            <span class="mDestLDlrDisc rBold" style="position: absolute; right: 60px;">$0 </span>
                            <i class="pull-right glyphicon glyphicon-menu-down"></i></h4>
                        </h4>
                        <div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
                            <span class="dealer_disc_update">No Dealer Discounts Applicable</span>
                        </div>
                    </li>
                    <li class="estimatedBlock">
                          <span class="text-danger CalcLeaseError hideMe"></span>
                          <h4 class="bold text-uppercase" style="font-weight: bold;">
                            Adjusted Capitalized Costs 
                            <span><span class="ore_disp_lease_estimate rBold">$0<!-- <img alt="loader" src="https://d11p9i1nddg3dz.cloudfront.net/images/loader.gif"> --></span></span>
                             
                          </h4>
                          <p class="reg mTop-1 instructions bottomContent leaseBottomContent">
                                Includes available offers and incentives.
                          </p>
                        <div class="row bottomContent leaseBottomContent ">
                            <div class="col-md-6">
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
                              <div class="col-md-6">
                                  <div><span>Term Duration</span></div>
                                  <div>
                                    <div class="select-wrapper lease_wrapper">
                                       <label style="display:none;" for="lease_terms">hide me</label>
                                        <select class="form-control duration ore_dropdown_lease_terms" id="lease_terms">
                                            <option value="48">48 Months</option>
                                            <option value="36" selected="select">36 Months</option>  
                                            <option value="24">24 Months</option>
                                        </select>   
                                    </div>
                                  </div>
                              </div>
                          </div>
                          <div class="row bottomContent financeBottomCotent" style="padding-top: 20px;">
                            <div class="col-md-7" data-behaviour="radioCheckBox">
                                <div>                                
                                    <label class="">
                                    <h5 class="size-15 reg ">Annual Percentage Rate(APR)</h5>
                                        <span>
                                                <span class="chrysler_apr_in">0</span>% (<span class="f_c_name">Ally's Rate</span> 
                                                <span id="d_q_chrysler" class="disclaimer badge">6</span>)
                                        </span>                                    
                                         <input type="hidden" class="ore_radio_apr " name="ore_radio_apr" value="5.9">
                                        </label>
                                </div>
                                <!--<div>
                                    <label class="customCheckBox">
                                        <input type="radio" class="ore_radio_apr" name="ore_radio_apr" value="third_party" ><span>&nbsp;</span></label>
                                     <input type="text" class="form-control thirdPartyVal ore_radio_apr_per" id="disclaimerThirdparty_field" value="6.47" value="0" maxlength="5">% (Third Party <span class="badge">8</span>) 
                                </div>-->
                                <span id="d_q_thirdparty" class="disclaimer badge"></span> 
                                   <input type="hidden" class="form-control thirdPartyVal ore_radio_apr_per" id="disclaimerThirdparty_field" value="6.47">
                                   <input type="hidden" class="ore_radio_apr third_party-radio" name="ore_radio_apr" value="5.9">  
                            </div>
                            <div class="col-md-5 text-right">
                                <div><span>Term Duration</span></div>
                                <div class="select-wrapper">
                                    <select class="form-control duration ore_dropdown_finance_terms" id="finance_terms">
                                        <option value="72" selected="selected">72 Months</option>
                                        <option value="60">60 Months</option>
                                        <option value="48">48 Months</option>
                                        <option value="36">36 Months</option>
                                        <option value="24">24 Months</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="text-right buyFor" id="lease_finance_buy_for">
                  <h4 class="mBottom-0 text-uppercase med size-20 l_alter_div">
                        <span class="bottomContent leaseBottomContent">Lease For</span>
                        <span class="bottomContent financeBottomCotent">BUY FOR</span>
                           <span class="buyForPrice"><span class="ore_lease_emi">
                           <img alt="loader" src="https://d11p9i1nddg3dz.cloudfront.net/images/loader.gif" >
                           </span>
                           <span id="l_q_cost" class="badge disclaimer">3</span>
                   </h4>
                 
                 <p class="l_alter_div">per month for 
                        <span class="ore_emi_lease_month">
                            <img alt="loader" src="https://d11p9i1nddg3dz.cloudfront.net/images/loader.gif" >
                        </span> months.
                </p>
                <span class="dummy_pcacl_lease"></span>                  
              </div>
                <div class="text-right buyFor" id="cash_buy_for">
                    <p>Est. Net Price</p>
                    <h4 class="mBottom-0 text-uppercase med size-20">
                    <span class="buyForPrice"><span class="ore_cash_emi"><img src="https://d11p9i1nddg3dz.cloudfront.net/images/loader.gif" alt="loader"></span><span id="cs_q_cost" class="badge disclaimer">2</span>  </h4>                 
              </div>
            </div>
        </div>
    </div>
</div>
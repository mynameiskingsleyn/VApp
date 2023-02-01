@extends('layouts.poc-calc')
@section('title', 'Payment Calcualtor')
@section('content')
<section>
    <div id="detailPage" style="padding:150px;">
        <center style="background-color: #eeefff; padding:5px;">
            <h3>Payment Calcualtor Review Seciton</h3>
            <small>V2Soft Developed for Testing Purpose Only.</small>
        </center>
        <br><br>
        <!-- Button trigger modal -->
        <center> Click on see price details:
            <a href="#" class="link link-primary" data-toggle="modal" data-target="#exampleModalCenter">
                price details
            </a>
        </center>
        <!-- Modal -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document" style="min-width: 700px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Offers
                            <button type="button" class="close" data-dismiss="modal">&times;</button> </h3>
                    </div>
                    <div class="modal-body">
                        <div class="card mb-12 box-shadow">
                            <div class="card-header">
                                <h4 class="my-0 font-weight-normal">Available Programs</h4>
                            </div>
                            <div class="card-body">
                                {{-- <div class="can-toggle can-toggle--size-small">
                                    <input id="a" type="checkbox" checked>
                                    <label for="a">
                                        <div class="can-toggle__switch" data-checked="Yes" data-unchecked="No"></div>
                                        <div class="can-toggle__label-text">Are you a US veteran ?</div>
                                    </label>
                                </div>
                                <div class="can-toggle can-toggle--size-small">
                                    <input id="c" type="checkbox" checked>
                                    <label for="c">
                                        <div class="can-toggle__switch" data-checked="Yes" data-unchecked="No"></div>
                                        <div class="can-toggle__label-text">Are you a FCA employee ?</div>
                                    </label>
                                </div>
                                <div class="can-toggle can-toggle--size-small">
                                    <input id="d" type="checkbox" checked>
                                    <label for="d">
                                        <div class="can-toggle__switch" data-checked="Yes" data-unchecked="No"></div>
                                        <div class="can-toggle__label-text">Do you qualify for a Automobility / Drivability discount ?</div>
                                    </label>
                                </div>
                                --}}
                                <div class="can-toggle can-toggle--size-small">
                                    <input id="b" type="checkbox" checked>
                                    <label for="b">
                                        <div class="can-toggle__switch" data-checked="Yes" data-unchecked="No"></div>
                                        <div class="can-toggle__label-text">Do you currently own or lease a Alfa Romeo, Chrysler, Jeep, Dodge, Ram, Fiat or Maserati vehicle ?</div>
                                    </label>
                                </div>
                                <div class="can-toggle can-toggle--size-small">
                                    <input id="d" type="checkbox" checked>
                                    <label for="d">
                                        <div class="can-toggle__switch" data-checked="Yes" data-unchecked="No"></div>
                                        <div class="can-toggle__label-text">If you lease, is your lease due within the next 12 months?</div>
                                    </label>
                                </div>
                                <br>
                                <!-- <button type="button" class="btn btn-success" data-dismiss="modal">Apply the Offer!</button>
						  -->
                                <div class="model-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="contpop">Continue &nbsp; &nbsp; ></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="leaseCalc" tabindex="-1" role="dialog" aria-labelledby="myModelNew" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h2 class="modal-title text-uppercase med title_lease_popup">2019 Stelvio Ti Sport awd</h2>
                    <h5 class="text-uppercase mTop-0 rLight vin_lease_popup">Vin : <span>ZASPAKBN2K7C30835</span></h5>
                    <h2 class="modal-title med pricedetails_lease_popup">Price Details</h2>
                    <ul class="popUpScroll">
                        <li>
                            <h4>MSRP (Sticker Price)
                                <span id="l_q_msrp" class="disclaimer">1</span>
                                <span> $50,590.00 </span>
                            </h4>
                        </li>
                        <li>
                            <h4>
                                Down Payment (<span class="dPaymentRange LPaymentRange">10</span>%)
                                <input type="text" class="form-control" value="5059">
                            </h4>
                        </li>
                        <li>
                            <h4>Trade-in Value <input type="text" title="Trade-in Value" class="form-control tradeVal ore_txtbox_ltradein" id="leaseTradeInValueField" name="" value="0" maxlength="8"></h4>
                        </li>
                        <li>
                            <h4 class="dOpener"><span class="uLine">Incentives</span> <span class="mDestCharge rBold"><span style="color: green; weight: bold;">-</span> $1000</span><i class="pull-right glyphicon glyphicon-menu-down"></i></h4>
                            <div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
                                <span class="calc_incentives_desc">
                                    <ul class="list-group reg">
                                        <li data-incentiveid="16160080"><img alt="incentive-icon" src="/images/incentive-icon-5.png" width="20" height="20"> FCA US Alfa Lease Bonus Cash - 38LKD</li>
                                    </ul>
                                </span>
                            </div>
                        </li>
                        <li>
                            <h4 class="dOpener"><span class="uLine">Explore Additional Offers</span> <i class="pull-right glyphicon glyphicon-menu-down"></i></h4>
                            <div class="cDropDown greyBg size-14 padding-3 mTop-2 addOffers hideMe">
                                <span class="calc_offers_desc">
                                    <ul class="list-group reg">
                                        <li><label style="display:none" for="explores_Military">hide me</label><input type="checkbox" title="lease_explores" aria-labelledby="explores_Military" aria-describedby="explores_Military" class="lease_chk" name="lease_explores[]" id="explores_Military" value="15780774" data-original-groups="FCA US Military Program">&nbsp;&nbsp;<b>Military<span style="float:right">$500</span></b>
                                        </li>
                                        <li style="padding-left:20px;" class="offer_subsets" data-relation-name="explores_Military" data-original-items="FCA US Military Program" data-original-incentiveid="15780774">FCA US Military Program</li>
                                        <li style=" ">
                                            <label style="display:none" for="explores_Loyalty">hide me</label>
                                            <input type="checkbox" checked="checked" title="lease_explores" aria-labelledby="explores_Loyalty" aria-describedby="explores_Loyalty" class="lease_chk" name="lease_explores[]" id="explores_Loyalty" value="16237218" data-original-groups="FCA US Alfa FCA Loyalty Bonus Cash - 40CK1">&nbsp;&nbsp;<b>Loyalty<span style="float:right">$1000</span></b>
                                        </li>
                                        <li style="padding:10px;  font-weight: bold; color: #333; background-color: #ffc;" class="offer_subsets" data-relation-name="explores_Loyalty" data-original-items="FCA US Alfa FCA Loyalty Bonus Cash - 40CK1" data-original-incentiveid="16237218">FCA US Alfa FCA Loyalty Bonus Cash - 40CK1</li>
                                        <li>
                                            <label style="display:none" for="explores_Lease-loyalty">hide me</label>
                                            <input type="checkbox" checked="checked" title="lease_explores" aria-labelledby="explores_Lease-loyalty" aria-describedby="explores_Lease-loyalty" class="lease_chk" name="lease_explores[]" id="explores_Lease-loyalty" value="16339791" data-original-groups="FCA US 2019 Returning Lessee (NA wEP/CDI)">&nbsp;&nbsp;<b>Lease-loyalty<span style="float:right">$500</span></b></li>
                                        <li style="padding:10px; font-weight: bold; color: #333; background-color: #ffc;" class="offer_subsets" data-relation-name="explores_Lease-loyalty" data-original-items="FCA US 2019 Returning Lessee (NA wEP/CDI)" data-original-incentiveid="16339791">FCA US 2019 Returning Lessee (NA wEP/CDI)</li>
                                        <li>
                                            <label style="display:none" for="explores_Automobility">hide me</label>
                                            <input type="checkbox" title="lease_explores" aria-labelledby="explores_Automobility" aria-describedby="explores_Automobility" class="lease_chk" name="lease_explores[]" id="explores_Automobility" value="13808080" data-original-groups="FCA US Driveability / Automobility Program">&nbsp;&nbsp;<b>Automobility<span style="float:right">$1000</span></b></li>
                                        <li style="padding-left:20px;" class="offer_subsets" data-relation-name="explores_Automobility" data-original-items="FCA US Driveability / Automobility Program" data-original-incentiveid="13808080">FCA US Driveability / Automobility Program</li>
                                    </ul>
                                </span>
                            </div>
                        </li>
                        <li>
                            <h4 class="dOpener"><span class="">Dealer Discount</span>
                                <input type="text" title="Dealer Discount" class="form-control lease_dealer_discount" id="lease_dealer_discount" name="lease_dealer_discount" value="0" maxlength="8">
                            </h4>
                        </li>
                        <li class="estimatedBlock">
                            <span class="text-danger CalcLeaseError hideMe">
                            </span>
                            <h4 class="bold text-uppercase ">
                                Adjusted Capitalized Costs
                                <span><span class="ore_disp_lease_estimate">$44,531.00</span></span>
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
                                        <div class="select-wrapper lease_wrapper"><label style="display:none;" for="lease_terms">hide me</label><select class="form-control duration ore_dropdown_lease_terms" id="lease_terms">
                                                <option value="48">48 Months</option>
                                                <option value="36" selected="select">36 Months</option>
                                                <option value="24">24 Months</option>
                                            </select></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="text-right buyFor">
                        <h4 class="mBottom-0 text-uppercase med size-20 l_alter_div">Lease For
                            <span class="buyForPrice"><span class="ore_lease_emi">$539.92</span>
                                <span id="l_q_cost" class="badge disclaimer">3</span>
                            </span></h4>
                        <p class="l_alter_div">per month for
                            <span class="ore_emi_lease_month">48</span> months.
                        </p>
                        <span class="dummy_pcacl_lease" style="display: none;"></span>
                    </div>
                    <div id="l_m_msrp" class="disclaimerMessage" style="display:none;">
                        <p>
                            1. *MSRP excludes, taxes, title and registration fees. Price refers to launch vehicle selected, including destination charge. Pricing and offers may change at any time without notification. To get full pricing details, see your dealer.
                        </p>
                        <span id="l_c_msrp" class="closeDisclaimer">X</span>
                    </div>
                    <div id="l_m_thirdparty" class="disclaimerMessage" style="display:none;">
                        <p>
                            2. Lessee pays for excess wear and mileage for each mile over the annual contracted lease miles per year, if vehicle is returned at end of term.
                        </p>
                        <span id="l_c_thirdparty" class="closeDisclaimer">X</span>
                    </div>
                    <div id="l_m_cost" class="disclaimerMessage" style="display:none;">
                        <p>
                            3. This rate is for estimation purposes only. You may not be able to finance your vehicle at this rate. See dealer for details.
                        </p>
                        <span id="l_c_cost" class="closeDisclaimer">X</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>
@endsection

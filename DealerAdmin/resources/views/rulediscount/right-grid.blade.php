    <div class="col-md-1 pleaseSelect"><img class="arrowLeft" src="https://d1jougtdqdwy1v.cloudfront.net/dealeradmin/images/arrow-left.svg"></div>
    <div class="col-md-6 pleaseSelect pleaseSelectAutomated">
        <div class="dashboardInstuct text-center">
            <span class="moveRight rBold">ADD AUTOMATED DISCOUNTS:</span><br>
            <span class="moveRight">PLEASE SELECT A </span><span class="meBlue">MAKE</span><span>,</span>
            <br><span class="meBlue">MODEL YEAR</span>&nbsp;<span class="riftLight">AND</span>&nbsp;<span class="meBlue">VEHICLE</span><br>THEN CLICK "<span class="meBlue">CREATE DISCOUNTS</span>"<span>.</span></div>
    </div>
    <div class="col-md-9 gridTableWrapperLoader" style="background-color:transparent;display: none;"></div>
<!-- Vehicle Discount--->
@include('rulediscount.vehicle-discount')
<!-- Saved Discount--->
@include('rulediscount.saved-discount')
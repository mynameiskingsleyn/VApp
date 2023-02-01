<div id="serviceProtection" class="tab-pane fade">
    <div class="row header no-gutters">
        <div class="col-xs-12 col-md-3">
            <img alt="DriveFCA Service & Care" src="{{ $serviceprotection['header']->package_name ??'' }}" class="img-responsive mopar-logo">
        </div>
        <div class="col-xs-12 col-md-9">
            <p class="med size-14"> {{ $serviceprotection['header']->package_description ?? '' }}</p>

        </div>
    </div>
    <div class="row no-gutters packageDetails greyBg" data-pkg-type="lease">
        <div class="col-xs-12">
            <h5><i class="fa fa-lg themeClr fa-check-circle-o mRight-1" aria-hidden="true"></i> You have selected to recieve more information from your dealer on the:</h5>
            <ul class="availedPackages row reg"></ul>
        </div>
    </div>
    <div class="row no-gutters packageDetails greyBg hideMe" data-pkg-type="finance">
        <div class="col-xs-12">
            <h5 class="rBold"><i class="fa fa-lg themeClr fa-check-circle-o" aria-hidden="true"></i> You have selected to recieve more information from your dealer on the:</h5>
            <ul class="availedPackages row reg"></ul>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="header">
                <span class="current_tab_title">Lease</span>
                <h5 class="">Service & Protection</h5>
                <span class="current-toggle-tab serviceprot-submit-toggle">
                    <span class="toggle-tab-label">Explore options below based on your choice of Lease or Finance.</span>
                    <label class="toggle-tab-switch">
                        <input type="checkbox" id="sliderround">
                        <span class="slider round"><span class="slide-ex">Lease</span></span>
                    </label>
                </span>
            </div>
        </div>
    </div>
    <div class="row no-gutters additionalCareBlocks">
        <div class="col-xs-12" data-pkg-type="lease">
           
            <div class="row">
                @php
                $checklease= $serviceprotection['checklease'] ;
               
                $checkfinance=$serviceprotection['checkfinance'] ;

                @endphp
                <input type="hidden" name="checklease" id="checklease" value="{{count($checklease)}}">
                <input type="hidden" name="checkfinance" id="checkfinance" value="{{count($checklease)}}">
                @if(!empty($serviceprotection['lease']))
                @foreach($serviceprotection['lease'] as $key=>$value)

                <div class="col-xs-12 col-md-6 contentBlock">
                    <h4 class="mTop-1" data-package-name="{{$value->package_name ?? ''}} Package"><label class="customCheckBox">
                            <input type="checkbox" class="ore_lease" name="lease" value="{{$value->id}}" @php if(in_array($value->id,$checklease)) {echo 'checked';} @endphp> <span>{{$value->package_name ?? ''}}</span></label></h4>

                    <p class="mTop-2">{{$value->package_description ?? ''}}</p>
                </div>
                @endforeach
                @endif
            </div>


        </div>

        <div class="col-xs-12 hideMe" data-pkg-type="finance">
            <div class="row">
                @if(!empty($serviceprotection['finance']))
                @foreach($serviceprotection['finance'] as $key=>$value)
                <div class="col-xs-12 col-md-6 contentBlock">

                    <h4 class="mTop-1" data-package-name="{{$value->package_name ?? ''}} Package"><label class="customCheckBox">
                            <input type="checkbox" class="ore_finance" name="finance" value="{{$value->id}}" @php if(in_array($value->id,$checkfinance)) {echo 'checked';} @endphp><span>{{$value->package_name ?? ''}}</span></label></h4>

                    <p class="mTop-2">{{$value->package_description ?? ''}}</p>
                </div>
                @endforeach
                @endif

            </div>


        </div>
    </div>
    <div class="row no-gutters service-bottom-nav">
        <div class="col-xs-12 text-center">
            <div class="greyBg fwd-bck-btns">
                <button class="btn size-15 rBold from-bottom nav-back-btn mRight-3 gcss-button-secondary backto-trade-in">PREV: Trade In</button>
                <button class="btn gcss-button theme btm_submitdealer merkle_bottom_submittodealer" data-toggle="modal" data-target="#reviewSubmitPopUp" data-source="submit-to-dealer">Submit to Dealer</button>
                <button class="btn size-15 rBold cNxt-btn from-bottom mRight-3 gcss-button-secondary hide-on-review-tab nav-next-btn">Next: <span class="reg"> Review and Submit</span></button>
            </div>
        </div>
    </div>
</div>
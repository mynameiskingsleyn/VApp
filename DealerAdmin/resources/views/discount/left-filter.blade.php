    <div class="col-md-3 filter-section"><img class="orLogo" src="https://d1jougtdqdwy1v.cloudfront.net/dealeradmin/images/or.svg">
        <div animationduration="0ms" class="mat-tab-group mat-primary">
            <div class="mat-tab-header">
                <div aria-hidden="true" class="mat-tab-header-pagination mat-tab-header-pagination-before mat-elevation-z4 mat-ripple mat-tab-header-pagination-disabled" mat-ripple="">
                    <div class="mat-tab-header-pagination-chevron"></div>
                </div>
                <div class="mat-tab-label-container">
                    <div class="mat-tab-list" role="tablist" style="transform: translateX(0px);">
                        <div class="mat-tab-labels">
                            <div class="mat-tab-label mat-ripple mat-tab-label-active search-by-vehicle-toggle">
                                <div class="mat-tab-label-content">
                                    <span>SEARCH BY</span>
                                    VEHICLE
                                </div>
                            </div>
                            <div class="mat-tab-label mat-ripple ng-star-inserted search-by-vin-toggle">
                                <div class="mat-tab-label-content">
                                    VIN NUMBER
                                </div>
                            </div>
                        </div>
                        <div class="mat-ink-bar" style="visibility: visible; left: 0px; width: 100px;">
                        </div>
                    </div>
                </div>
                <div aria-hidden="true" class="mat-tab-header-pagination mat-tab-header-pagination-after mat-elevation-z4 mat-ripple mat-tab-header-pagination-disabled" mat-ripple="">
                    <div class="mat-tab-header-pagination-chevron"></div>
                </div>
            </div>
            <div class="mat-tab-body-wrapper">
                <div class="mat-tab-body ng-tns-c9-8 mat-tab-body-active ng-star-inserted search-by-vehicle-tab">
                    <div class="mat-tab-body-content ng-trigger ng-trigger-translateTab" style="transform: none;">
                        <div class="dropDownPanel ng-star-inserted" style="">
                            <div class="field ng-star-inserted">
                                <div class="labelDrop"> MAKE &nbsp; <span class="labelMandatory"><i aria-hidden="true" class="fa fa-asterisk"></i></span></div>
                                <select required="" class="da_event_make" id="da_event_make">
                                    <option value=""> Select </option>
                                </select>
                            </div>
                            <div class="field financeOption">
                                <div class="labelDrop"> PAYMENT MODE &nbsp; <span class="labelMandatory"><i aria-hidden="true" class="fa fa-asterisk"></i></span></div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="defaultGroupExample1" name="finance_option" value="1" checked>
                                    <label class="custom-control-label" for="defaultGroupExample1">Lease</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="defaultGroupExample2" name="finance_option" value="2">
                                    <label class="custom-control-label" for="defaultGroupExample2">Finance</label>
                                </div>
                                <div class="custom-control custom-radio last-option">
                                    <input type="radio" class="custom-control-input" id="defaultGroupExample3" name="finance_option" value="3">
                                    <label class="custom-control-label" for="defaultGroupExample3">Cash </label>
                                </div>
                            </div>
                            <div class="field">
                                <div class="labelDrop"> MODEL YEAR &nbsp; <span class="labelMandatory"><i aria-hidden="true" class="fa fa-asterisk"></i></span></div>
                                <select required="" class="da_event_model_year" id="da_event_model_year">
                                    <option value="" class="ng-star-inserted"> Select Year</option>
                                </select>
                            </div>
                            <div class="fieldOne">
                                <div class="labelDrop"> VEHICLE &nbsp; <span class="labelMandatory"><i aria-hidden="true" class="fa fa-asterisk"></i></span></div>
                                <select class="da_event_vehicle before-load-value" id="da_event_vehicle">
                                    <option value="" class="ng-star-inserted"> Choose a Vehicle </option>
                                </select>
                            </div>
                            <div class="field trimField">
                                <select class="da_event_trim" name="da_event_trim[]" multiple id="da_event_trim">
                                </select>
                            </div>
                            <!--                                <div class="fieldOne">
                                <div class="labelDrop"> MSRP &nbsp; <span class="labelMandatory"><i aria-hidden="true" class="fa fa-asterisk"></i></span></div>
                                <select class="da_event_msrp_highest" id="da_event_msrp_highest">
                                    <option value="" class="ng-star-inserted"> Highest </option>
                                </select>
                            </div>
                            <div class="field">
                                <select class="da_event_msrp_lowest" id="da_event_msrp_lowest">
                                    <option value="" class="ng-star-inserted"> Lowest </option>
                                </select>
                            </div> -->
                            <div class="field filterAttrs priceSlider">
                                <div>
                                    <h4 class="labelDrop">MSRP</h4>
                                    <div class="clearAfter size-14 med">
                                        <input type="text" id="amount" readonly style="border:0; color:#f6931f; font-weight:bold;">
                                        <div id="slider-range"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Drive field start-->
                            <div class="field filterAttrs driveBlock">
                                <h4 class="dOpener text-uppercase labelDrop">Drive <i class="pull-right glyphicon glyphicon-menu-up"></i><span class="checkbox-counter" style="display: none;"></span></h4>
                                <div class="cDropDown hideMe ore_drive" style="display: none;">
                                    <ul id="da_event_drive_type">
                                        <!-- <li>
                                      <label class="customCheckBox">
                                        <input type="checkbox"><span>RWD</span>
                                      </label>
                                    </li> -->
                                    </ul>
                                </div>
                            </div>
                            <!-- Trim field start-->
                            <div class="field filterAttrs driveBlock">
                                <h4 class="dOpener text-uppercase labelDrop">COLOR <i class="pull-right glyphicon glyphicon-menu-up"></i><span class="checkbox-counter" style="display: none;"></span></h4>
                                <div class="cDropDown hideMe ore_drive" style="display: none;">
                                    <ul id="da_event_color">
                                    </ul>
                                </div>
                            </div>
                            <!-- Trim field start-->
                            <div class="field filterAttrs driveBlock">
                                <h4 class="dOpener text-uppercase labelDrop">ENGINE DESC <i class="pull-right glyphicon glyphicon-menu-up"></i><span class="checkbox-counter" style="display: none;"></span></h4>
                                <div class="cDropDown hideMe ore_drive" style="display: none;">
                                    <ul id="da_event_engine_desc">
                                    </ul>
                                </div>
                            </div>
                            <!-- Trim field start-->
                            <div class="field filterAttrs driveBlock filterAttrs--transmission">
                                <div>
                                    <h4 class="dOpener text-uppercase labelDrop">TRANSMISSION <i class="pull-right glyphicon glyphicon-menu-up"></i><span class="checkbox-counter" style="display: none;"></span></h4>
                                    <div class="cDropDown hideMe ore_drive" style="display: none;">
                                        <ul id="da_event_transmission">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="goFilterBtn">
                                <button name="filterButton" class="disabled" id="SearchByVehicleGoBtn" onclick="SearchByAttributes()" type="button">GO</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="display: none;" class="mat-tab-body ng-tns-c9-9 ng-star-inserted mat-tab-body-active search-by-vin">
                    <div class="mat-tab-body-content ng-trigger ng-trigger-translateTab">
                        <div class="dropDownPanel dropDownPanelVin ng-star-inserted" style="">
                            <div class="field fieldVin">
                                <div class="labelDrop"> VIN NUMBER &nbsp; <span class="labelMandatoryVin">
                                        <i aria-hidden="true" class="fa fa-asterisk"></i></span>
                                </div>
                                <input name="vinNumber" type="text" id="da_event_vinNumber" class="ng-untouched ng-pristine ng-valid" placeholder="" />
                                <span class="vin-error-check" style="float: none">Selected Vin Number not found</span>
                            </div>
                            <div class="field financeOption">
                                <div class="labelDrop"> PAYMENT MODE &nbsp; <span class="labelMandatory"><i aria-hidden="true" class="fa fa-asterisk"></i></span></div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="defaultGroupExample4" name="vin_finance_option" value="1" checked>
                                    <label class="custom-control-label" for="defaultGroupExample4">Lease</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="defaultGroupExample5" name="vin_finance_option" value="2">
                                    <label class="custom-control-label" for="defaultGroupExample5">Finance</label>
                                </div>
                                <div class="custom-control custom-radio last-option">
                                    <input type="radio" class="custom-control-input" id="defaultGroupExample6" name="vin_finance_option" value="3">
                                    <label class="custom-control-label" for="defaultGroupExample6">Cash</label>
                                </div>
                            </div>
                            <div class="goFilterBtn">
                                <button class="disabled" name="searchButton" id="SearchByVinGoBtn" onclick="SearchByVin()" type="button">GO</button><span class="error-check">Please Enter a Valid VIN Number</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
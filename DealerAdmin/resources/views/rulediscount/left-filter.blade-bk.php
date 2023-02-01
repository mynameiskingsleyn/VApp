    <div class="col-md-3 filter-section">
        <div animationduration="0ms" class="mat-tab-group mat-primary">
            <div class="mat-tab-header">
                <div aria-hidden="true" class="mat-tab-header-pagination mat-tab-header-pagination-before mat-elevation-z4 mat-ripple mat-tab-header-pagination-disabled" mat-ripple="">
                    <div class="mat-tab-header-pagination-chevron"></div>
                </div>
                <div class="mat-tab-label-container">
                    <div class="mat-tab-list" role="tablist" style="transform: translateX(0px);">
                        <div class="mat-tab-labels">
                            <div class="mat-tab-label mat-ripple mat-tab-label-active create-discount-for-vehicle">
                                <div class="mat-tab-label-content">
                                    <span>Create Discount For</span>
                                    VEHICLE
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
                <div class="mat-tab-body ng-tns-c9-9 ng-star-inserted mat-tab-body-active created-automated-discount">
                    <div class="mat-tab-body-content ng-trigger ng-trigger-translateTab" style="transform: none;">
                        <div class="dropDownPanel ng-star-inserted" style="">
                            <div class="field ng-star-inserted">
                                <div class="labelDrop"> MAKE &nbsp; <span class="labelMandatory"><i aria-hidden="true" class="fa fa-asterisk"></i></span></div>
                                <select required="" class="da_auto_event_make" id="da_auto_event_make">
                                    <!-- <option value=""> Select </option> -->
                                    <option value="Y"> ALFA ROMEO </option>
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
                            </div>
                            <div class="field">
                                <div class="labelDrop"> MODEL YEAR &nbsp; <span class="labelMandatory"><!-- <i aria-hidden="true" class="fa fa-asterisk"></i> --></span></div>
                                <select required="" class="da_auto_event_model_year" id="da_auto_event_model_year">
                                    <option value="" class="ng-star-inserted"> Select Year</option>
                                </select>
                            </div>
                            <div class="fieldOne">
                                <div class="labelDrop"> VEHICLE &nbsp; <span class="labelMandatory"><!-- <i aria-hidden="true" class="fa fa-asterisk"></i> --></span></div>
                                <select class="da_auto_event_vehicle before-load-value" id="da_auto_event_vehicle">
                                    <option value="" class="ng-star-inserted"> Choose a Vehicle </option>
                                </select>
                            </div>
                            <div class="field trimField">
                                <select class="da_auto_event_trim" name="da_auto_event_trim[]" multiple id="da_auto_event_trim">
                                </select>
                            </div>
                            <div class="field discountViewOption">
                                <div class="labelDrop"> DISCOUNTS &nbsp; <span class="labelMandatory"><i aria-hidden="true" class="fa fa-asterisk"></i></span></div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="discount_type1" name="discount_view_type" value="1" checked>
                                    <label class="custom-control-label" for="discount_type1">Create</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="discount_type2" name="discount_view_type" value="2">
                                    <label class="custom-control-label" for="discount_type2">View Saved</label>
                                </div>
                            </div>
                            <div class="field howmanyField">
                                <div class="labelDrop"> HOW MANY DISCOUNTS? &nbsp; <span class="labelMandatory"><i aria-hidden="true" class="fa fa-asterisk"></i></span></div>
                                <select class="da_auto_event_hwmnydiscount" name="da_auto_event_hwmnydiscount" id="da_auto_event_hwmnydiscount">
                                    <option value="0"> Select </option>
                                    <option value="1"> 1 </option>
                                    <option value="2"> 2 </option>
                                    <option value="3"> 3 </option>
                                    <option value="4"> 4 </option>
                                    <option value="5"> 5 </option>
                                </select> 
                                <span class="error-check discount-error-check">Maximum Allowed 5 Discounts</span>
                            </div>
                            <div class="fieldOne discountListField hidden">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="goFilterBtn automatedcreateDiscountBtn">
                    <button class="disabled" name="searchButton" id="CreatedDiscountBtn" onclick="createDiscountBtn()" type="button">CREATE DISCOUNTS </button><span class="error-check">Please Enter a Valid VIN Number</span>
                     <button name="searchGOButton" id="SavedDiscountBtn" style="display: none;" onclick="savedDiscountBtn()" type="button">GO </button> 
                    <span class="error-check">Please Enter a Valid VIN Number</span>
                </div>
            </div>
        </div>
    </div>
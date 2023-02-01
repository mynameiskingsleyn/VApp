<div class="modal fade" id="add-rule-discount-model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center d-block rift-soft">
                <h5 class="modal-title"><span class="font-weight-bold">Edit Automated Discount:</span> <span id="ModalDiscountVIN"></span>
                    <span id="ModalDiscountCount" style="display: none;">0</span>
                    <span id="ModalfinanceOption" style="display: none;">1</span>
                    <span id="ModalDiscountName" style="display: none;"></span>
                    <span id="ModalDiscountUUID" style="display: none;"></span>
                    <span id="ModalDiscountID" style="display: none;"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span class="alert alert-danger" id="RulediscountStatusCodeError" style="display:none"></span>
<!--                 <div class="add-more-by-count text-center">
                    <span class="font-weight-bold">
                        <button class="btn gcss-button add-more-by-count-btn">Add More <img src="img/svg/add-white.svg"></button>
                        <div><span class="add-more-by-count-rule">(Maximum of 5 Discounts)</span></div>
                    </span>
                </div> -->
                <div class="discounts-rows-parent discountAutomatedWrapper" id="RuleDiscountRowWrapper">
                    <div class="discounts-rows">
                        <div class="discount-details">
                            <input type="hidden" name="discount_uuid" value="">
                            <div class="discount-space">
                                <span class="discount-name-field">
                                    <label>Discount Name</label>
                                    <input type="text" maxlength="60" name="discount_name" value="">
                                    <span class="discount-name-error bulk-error">Please enter discount name</span>
                                    <!-- <span class="discount-name-edit-field">Edit Name</span> -->
                                </span>
                                <span class="discount-flat-rate">
                                    <label>Flat Rate
                                        <span class="switch">
                                            <label>
                                                <input type="checkbox" name="price_switch" checked>
                                                <span class="lever"></span>
                                            </label>
                                        </span>
                                    </label>
                                    <input type="text" maxlength="4" name="flat_rate" value="" onkeypress="numbervalidate(event)">
                                    <span class="flat-rate-error bulk-error">Please enter discount amount</span>
                                </span>
                                <span class="or-text">or</span>
                                <span class="off-percent">
                                    <label class="off-percent-label">% Off</label>
                                    <input type="text" name="off_percent" maxlength="2" value="" disabled="disabled" onkeypress="numbervalidate(event)">
                                    <span class="off_percent_error bulk-error">Please enter % value for discount</span>
                                </span>
                                <span class="discount-date-field">
                                    <label class="discount-start-date-label">Start Date</label>
                                    <input type="date" name="discount_start_date" class="start_datepicker">
                                    <span class="discount_start_date_error bulk-error">Please enter Start Date</span>
                                </span>
                                <span class="discount-date-field">
                                    <label class="discount-end-date-label">End Date</label>
                                    <input type="date" name="discount_end_date" class="end_datepicker">
                                    <span class="discount_end_date_error bulk-error">Please enter End Date</span>
                                </span>
                            </div>
                            <div class="saved-discounts">
                                <label class="customCheckBox">
                                    <input type="checkbox" name="saved_discount"><span>ADD to Saved Discounts (Only 5 Allowed)</span>
                                </label>
                            </div>
                        </div>
                        <div class="discount-delete-field">
                            <span>
                                <i aria-hidden="true" class="fa fa-trash-alt"></i>
                            </span>
                        </div>
                    </div>
                </div>
               <div class="apply-automated-discounts-field text-center">
                <button class="btn rift-soft gcss-button" data-dismiss="modal">Cancel</button>
                <button class="btn rift-soft gcss-button rule-auto-save-and-apply-btn" onclick="confirmRuleVehicleAddDiscount()">Save and Apply Discounts</button>
            </div>
            </div>
            <div class="add-bulk-discount-alert" style="display: none;">
                <div class="d-block">
                    <h6>Warning!</h6>
                    <p>This will override all individual <br>vehicle discounts!</p>
                    <p><span>These discounts are over and above the FCA incentives.</span></p>
                    <button class="btn gcss-button cancel-btn" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button class="btn gcss-button apply-discounts-btn" onclick="applyRuleDiscounttoVehicle()">Apply Discounts</button>
                </div>
            </div>
        </div>
    </div>
</div>
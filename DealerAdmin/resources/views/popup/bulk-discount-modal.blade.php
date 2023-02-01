<div class="modal dealer-admin-dialogue fade" id="add-bulk-discount-model" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog " role="dialog">
        <div class="modal-content">
            <div class="modal-header no-border text-center d-block rift-soft">
                <h5 class="modal-title"><span class="font-weight-bold">add bulk Discounts</span></h5>
                <button type="button" class="close" aria-label="Close" onclick="BulkDiscountClose()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span class="alert alert-danger" id="BulkStatusCodeError" style="display:none"></span>
                <div class="add-more-by-count text-center">
                    <span class="font-weight-bold">
                        <button class="btn gcss-button add-more-by-count-btn">Add More <img src="img/svg/add-white.svg"></button>
                        <div><span class="add-more-by-count-rule">(Maximum of 5 Discounts)</span></div>
                    </span>
                </div>
                <div class="discounts-rows-parent" id="BulkDiscountRowWrapper">
                    <div class="discounts-rows">
                        <div class="discount-details">
                            <input type="hidden" name="discount_uuid" value="">
                            <div class="discount-space">
                                <span class="discount-name-field">
                                    <label>Discount Name</label>
                                    <input type="text" name="discount_name" maxlength="60" value="" placeholder="Discount Name">
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
                                    <input type="text" name="flat_rate" value="" maxlength="4" placeholder="Flate rate" onkeypress="numbervalidate(event)">
                                    <span class="flat-rate-error bulk-error">Please enter discount amount</span>
                                </span>
                                <span class="or-text">or</span>
                                <span class="off-percent">
                                    <label class="off-percent-label">% Off</label>
                                    <input type="text" name="off_percent" value="" maxlength="2" placeholder="% off" disabled="disabled" onkeypress="numbervalidate(event)">
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
                <div class="apply-to-vehicles-tabled search-result-field">
                    <h5 class="rift-soft  text-center">apply to these <span class="">selected </span>vehicles</h5>
                    <table class="table table-hover" id="gridDiscountTableContainer">
                        <thead class="rift-soft">
                            <tr align="center">
                                <th scope="col">VIN NUMBER</th>
                                <th scope="col">MAKE</th>
                                <th scope="col">MODEL</th>
                                <th scope="col">MODEL YEAR</th>
                                <th scope="col">TRIM</th>
                                <th scope="col">has discounts</th>
                                <th scope="col">Include 
                                    <span>
                                        <label class="customCheckBox "><input id="Include-select-all" type="checkbox" name="Include_all" class="auto_discount_include" value="" checked=""><span></span>
                                        </label>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr align="center">
                                <td colspan="6" align="center">Loading ...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="apply-discounts-field">
                    <span class="bulk-table-error" id="gridDiscountTableError">Please select one vehicle for the discount to be applied</span>
                    <div class="text-center">
                        <button class="btn rift-soft gcss-button bulk-clear-all-discounts-btn"> <i aria-hidden="true" class="fa fa-trash-alt"></i> Clear All Discounts</button>
                        <button class="btn rift-soft gcss-button save-and-apply-btn"> <i class="fa fa-save" aria-hidden="true"></i> Save and Apply Discounts</button>
                    </div>
                </div>
            </div>
            <div class="add-bulk-discount-alert" style="display: none;">
                <div class="d-block">
                    <h6>Warning!</h6>
                    <p>This will override all individual <br>vehicle discounts!</p>
                    <p><span>These discounts are over and above the FCA incentives.</span></p>
                    <button class="btn gcss-button cancel-btn" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button class="btn gcss-button apply-discounts-btn" onclick="applyBulkDiscounttoVehicle()">Apply Discounts </button>
                </div>
            </div>
        </div>
    </div>
</div>
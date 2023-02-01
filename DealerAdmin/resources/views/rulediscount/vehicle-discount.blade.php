   <div class="col-md-9 gridTableWrapper" style="display: none;margin-bottom: 20px;" id="vehicleDiscountWrapper">
        <div class="automated_discount_label_field">
            <span class="automated_discount_label_field_child">
                <span class="label_name rift-soft">Vehicle discounts</span>
                <span class="vehicle_field">Vehicles <span class="rounded_border_bg" id="vehicleCount">0</span></span>
                <span class="discounts_field">Discounts <span class="rounded_border_bg" id="discountCount">1</span></span>
            </span>
            <!-- <span class="saved-discounts-field-remaining">
                <span class="rift-soft">Saved Discounts</span>
                <span class="remaining_field">
                    <span class="rounded_border_bg grey-bg">2</span> (5 remaining)
                </span>  
            </span> -->
            <!-- <button class="discount_buttons view_all_discounts" onclick="view_all_discounts();">View all discounts</button> -->
            <button class="btn gcss-button add-more-by-count-btn" onclick="ruleDiscountaAddMore();">Add More <img src="img/svg/add-white.svg"></button>
                <span class="add-more-by-count-rule">(Maximum of 5 Discounts)</span>
        </div>
        <div class="search-drop-down-field">
            <span class="year-of-selection rift-soft font-weight-bold">
                <span id="modelYearText"></span> <span id="makeText">alfa romeo</span>
                <span class="model-of-selection" id="trimSelectionText"> - <span id="trimText" class="themeClr"></span>
                    <!-- <span class="rounded_border_bg" id="vehicleModelCount">0</span> -->
                </span>
            </span>
        </div>
<!--         <div class="add-more-by-count text-center">
            <span class="font-weight-bold">
                <button class="btn gcss-button add-more-by-count-btn">Add More <img src="img/svg/add-white.svg"></button>
                <span class="add-more-by-count-rule">(Maximum of 5 Discounts)</span>
            </span>
        </div> -->
        <div class="search-result-field discountAutomatedContainer" id="discountAutomatedContainer">
            <div class="discounts-rows-parent discountAutomatedWrapper" id="discountAutomatedWrapper">
                <div class="discounts-rows">
                    <div class="discount-details">
                        <input type="hidden" name="discount_uuid" value="">
                        <div class="discount-space">
                            <span class="switch discount_name_switch">
                                <label>
                                    <span class="switchLabel ActiveText"> Deactive </span>
                                    <input type="checkbox" name="discount_name_switch" checked>
                                    <span class="lever"></span>
                                </label>
                            </span>
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
                                <input type="date" name="discount_start_date">
                                <span class="discount_start_date_error bulk-error">Please enter Start Date</span>
                            </span>
                            <span class="discount-date-field">
                                <label class="discount-end-date-label">End Date</label>
                                <input type="date" name="discount_end_date">
                                <span class="discount_end_date_error bulk-error">Please enter End Date</span>
                            </span>
                            <span class="discount-inventory-field discount-date-field">
                                <label class="discount-inventory-label">Inventory</label>
                                <select class="discount-inventory" name="discount-inventory" id="da_auto_discount_inventory">
                                    <option value=""> Select </option>
                                    <option value="New"> New </option>
                                </select>
                            </span>
                        </div>
                        <div class="saved-discounts">
                            <label class="customCheckBox">
                                <input type="checkbox" name="saved_discount"><span>ADD to Saved Discounts (Only 5 Allowed)</span>
                            </label>
                        </div>
                        <div class="discounts-button text-right">
                            <button class="discount_buttons save_edits_btn">Save Edits</button>
                            <button class="discount_buttons duplicate_discount_btn">DUPLICATE DISCOUNT</button>
                            <button class="discount_buttons show_and_edit_inventory">SHOW & EDIT INVENTORY</button>
                        </div>
                    </div>
                    <div class="discount-delete-field">
                        <span>
                            <i aria-hidden="true" class="fa fa-trash-alt"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="discounts-button discount-information text-center">
                <span class="discount-over-field discount-info"> <i class="fa fa-asterisk text-danger"></i> The discount/s defined here may be overriden by changes made at the Model Year/Vehicle/Trim levels.</span>
            </div>
            <div class="discounts-button text-center">
                <span class="bulk-table-error" id="discount_include_error">Please select atleast one discount</span>
                <div class="text-center">
                    <button class="discount_buttons show_and_edit_inventory" onclick="showEditInventory();">SHOW & EDIT INVENTORY</button>
                </div>
            </div>
        </div>
        <div class="apply-to-vehicles-tabled search-result-field gridAutomatedDiscountTableWrapper">
            <h5 class="rift-soft  text-center">apply to these <span class="">selected </span>vehicles</h5>
            <table class="table table-hover gridAutomatedDiscountTableContainer" id="gridAutomatedDiscountTableContainer">
                <thead class="rift-soft">
                    <tr align="center">
                        <th scope="col">VIN NUMBER</th>
                        <th scope="col">MAKE</th>
                         <th scope="col">MODEL</th>
                        <th scope="col">MODEL YEAR</th>
                        <th scope="col">Trim</th>
                        <th scope="col">MSRP</th>
                        <th scope="col">Discount</th>
                        <th scope="col">has discounts</th>
                        <th scope="col">Include <div> 
                            <label class="customCheckBox "><input id="Include-auto-select-all" type="checkbox" name="Include_all" class="auto_discount_include" value="" checked=""><span></span></label>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr align="center">
                        <td colspan="6" align="center">Loading ...</td>
                    </tr>
                </tbody>
            </table>
            <span class="bulk-table-error" id="gridAutomatedDiscountTableError">Please select at least one vehicle for the discount to be applied</span>
            <div class="apply-automated-discounts-field text-center">
                <button class="btn rift-soft gcss-button auto-cancel-btn" onclick="closeInventory()">Cancel</button>
                <button class="btn rift-soft gcss-button auto-save-and-apply-btn" onclick="confirmapplyautoDiscounttoVehicle()">Save AND Apply Discounts</button>
            </div>
        </div>
    </div>
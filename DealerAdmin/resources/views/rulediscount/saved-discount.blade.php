   <div class="col-md-9" style="display: none;margin-bottom: 20px;" id="savedDiscountWrapper">
        <div class="automated_discount_label_field">
             <!--<span class="automated_discount_label_field_child">
                <span class="label_name rift-soft">Vehicle discounts</span>
                <span class="vehicle_field">Vehicles <span class="rounded_border_bg" id="vehicleCount">0</span></span>
                <span class="discounts_field">Discounts <span class="rounded_border_bg" id="discountCount">0</span></span>
            </span> -->
            <span class="automated_discount_label_field_child">
                <span class="rift-soft">Saved Discounts</span>
                <span class="vehicle_field">
                    <span class="rounded_border_bg" id="savedDiscountCount">2</span> {{-- (<span id="savedDiscountremCount">5</span> remaining) --}}
                </span>  
            </span>
        </div>
        <div class="search-drop-down-field">
            <span class="year-of-selection rift-soft font-weight-bold">
                <span id="SavedmodelYearText"></span> <span id="SavedmakeText">alfa romeo</span>
                <span class="model-of-selection" id="SavedtrimSelectionText"> - <span id="SavedtrimText" class="themeClr"></span>
                    <!--span class="rounded_border_bg" id="SavedvehicleModelCount">0</span-->
                </span>
            </span>
        </div>
        <div class="search-result-field discountAutomatedContainer" id="saveddiscountAutomatedContainer" >
            <div class="discounts-rows-parent discountAutomatedWrapper" id="saveddiscountAutomatedWrapper">
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
                    </div>
                    <div class="discount-delete-field">
                        <span>
                            <i aria-hidden="true" class="fa fa-trash-alt"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="discounts-button text-center">
                <button class="discount_buttons show_and_edit_inventory" onclick="SavedshowEditInventory();">SHOW & EDIT INVENTORY</button>
                <span class="bulk-table-error hidden" id="discount_include_error">Please select atleast one discount</span>
            </div>
            <div class="dashboardInstuct text-center" id="no_saved_discount">
                <span class="moveRight rBold">No Discounts Available</span>
            </div>
        </div>
        <div class="apply-to-vehicles-tabled search-result-field gridAutomatedDiscountTableWrapper">
            <h5 class="rift-soft  text-center">apply to these <span class="">selected </span>vehicles</h5>
            <table class="table table-hover gridAutomatedDiscountTableContainer" id="gridAutomatedSavedDiscountTableContainer">
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
                            <label class="customCheckBox "><input id="Include-auto-saved-select-all" type="checkbox" name="Include_all" class="auto_discount_include" value="" checked=""><span></span></label></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr align="center">
                        <td colspan="6" align="center">Loading ...</td>
                    </tr>
                </tbody>
            </table>
            <span class="bulk-table-error" id="gridAutomatedSavedDiscountTableError">Please select at least one vehicle for the discount to be applied</span>
            <div class="apply-automated-discounts-field text-center">
                <button class="btn rift-soft gcss-button auto-cancel-btn">Cancel</button>
                <button class="btn rift-soft gcss-button auto-save-and-apply-btn" onclick="confirmapplyautoSavedDiscounttoVehicle()">Save AND Apply Discounts</button>
            </div>
        </div>
    </div>
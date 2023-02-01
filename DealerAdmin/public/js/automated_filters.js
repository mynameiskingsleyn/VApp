/*
 * Global Variables
 */
globalValues.tabs = {
    dealerDiscounts: false,
    dealerAutomatedDiscounts: true
};
EmptyAutoDiscountRow = '';
var deleteCurrentObject,switchRuleViewTabs = 1;
globalValues.Trims = [];
var percentageValidationTooltip = 'your selected discount exceeds the maximum allowable limit of $5,000 and has been adjusted to meet program requirements';

/*
 * API Functions
 */
function getDiscountVehicles() {
    $('#Include-auto-select-all').prop('checked', true);
    $('#Include-auto-saved-select-all').prop('checked', true);
    dataTableOperation('.gridAutomatedDiscountTableContainer');
    var request = {
        NameOfAPI: "listAutomatedDiscountVehicle",
        DealerCode: requestData.DealerCode,
        FinanceOption: requestData.financeoption,
        MakeCode: requestData.makeCode,
        ModelYear: requestData.modelyear,
        Model: requestData.model,
        Trim: requestData.trim
    };
    var url = 'rule-discount/vehicles';
    var method = "POST";
    ajaxCall(url, method, request);
}

function addFilterGroup() {
    var request = {
        NameOfAPI: "addFilterGroup",
        DealerCode: requestData.DealerCode,
        FinanceOption: requestData.financeoption,
        MakeCode: requestData.makeCode,
        ModelYear: requestData.modelyear,
        Model: requestData.model,
        Trim: requestData.trim
    };
    var url = 'filter-group/add';
    var method = "POST";
    ajaxCall(url, method, request);
}

function getRuleDiscounts() {
    var request = {
        NameOfAPI: "listRuleDiscounts",
        DealerCode: requestData.DealerCode,
        FinanceOption: requestData.financeoption,
        MakeCode: requestData.makeCode,
        ModelYear: requestData.modelyear,
        Model: requestData.model,
        Trim: requestData.trim
    };
    var url = 'rule-discount/list';
    var method = "POST";
    ajaxCall(url, method, request);
}

function getVinRuleDiscounts(vin) {
    var request = {
        NameOfAPI: "listVinRuleDiscounts",
        DealerCode: requestData.DealerCode,
        FinanceOption: requestData.financeoption,
        MakeCode: requestData.makeCode,
        ModelYear: requestData.modelyear,
        Model: requestData.model,
        Trim: requestData.trim,
        vin: vin
    };
    var url = 'rule-discount/vin-list';
    var method = "POST";
    ajaxCall(url, method, request);
}

function getSavedDiscounts() {
    var request = {
        NameOfAPI: "listSavedAutomatedDiscounts",
        DealerCode: requestData.DealerCode,
        FinanceOption: requestData.financeoption,
        MakeCode: requestData.makeCode,
        ModelYear: requestData.modelyear,
        Model: requestData.model,
        Trim: requestData.trim
    };
    var url = 'rule-discount/saved-list';
    var method = "POST";
    ajaxCall(url, method, request);
}

function Applydiscount() {
    var savedDiscount = $('#savedDiscountWrapper').css('display');
    var request,url,method;

    if (savedDiscount == 'block') {
        var vin = getIncludeVinRowsData('#gridAutomatedSavedDiscountTableContainer');
        var discount = getDiscountRowsData('#saveddiscountAutomatedContainer');
        request = {
            NameOfAPI: "AddSavedRuleDiscount",
            filterGroupId: globalValues.FilterGroupId,
            DealerCode: requestData.DealerCode,
            VinNumber: vin,
            FinanceOption: requestData.financeoption,
            Discount: discount
        };
        url = 'rule-discount/saved-add';
        method = "POST";
        ajaxCall(url, method, request);
        return false;
    }
    var vin = getIncludeVinRowsData('#gridAutomatedDiscountTableContainer');
    var discount = getDiscountRowsData('#discountAutomatedContainer');
    var excludeVinList = getExcludeVinRowsData('#gridAutomatedDiscountTableContainer');
    request = {
        NameOfAPI: "AddRuleDiscount",
        filterGroupId: globalValues.FilterGroupId,
        DealerCode: requestData.DealerCode,
        VinNumber: vin,
        FinanceOption: requestData.financeoption,
        ExcludeVinList: excludeVinList,
        Discount: discount
    };
    url = 'rule-discount/add';
    method = "POST";
    ajaxCall(url, method, request);
}

function deleteSavedAutomatedDiscounts() {
    requestData.uuid = $('#DeleteSingleDiscountPopupUUid').val();
    requestData.discount_name = $('#DeleteDiscountPopupName').text();
    var request = {
        NameOfAPI: "DeleteSavedRuleDiscount",
        DealerCode: requestData.DealerCode,
        discount_name: requestData.discount_name,
        filterGroupId: globalValues.FilterGroupId
    };
    var url = 'rule-discount/delete/' + requestData.uuid;
    var method = "DELETE";
    ajaxCall(url, method, request);
}

function uncheckruleDeleteDiscountforVIN() {
    $('#uncheck_rule_discount_delete_popup_modal').modal('hide');
    var discount_name = $('#add-rule-discount-model #ModalDiscountName').text();
    var discount_uuid = $('#add-rule-discount-model #ModalDiscountUUID').text();
    var discount_id = $('#add-rule-discount-model #ModalDiscountID').text();
    requestData.uuid = discount_uuid;
    var request = {
        discount_id: discount_id
    };
    var url = 'rule-discount/vin-delete/' + requestData.uuid;
    var method = "DELETE";
    ajaxCall(url, method, request);
    $(deleteCurrentObject).parent('.discounts-rows').remove();
    deleteCurrentObject = undefined;
}

function deleteautoDiscount(filtergroup_id, discount_name) {
    var rule_id = globalValues.FilterGroupId;
    if (filtergroup_id != '' && filtergroup_id != null && filtergroup_id != undefined) {
        if (filtergroup_id != globalValues.FilterGroupId)
            rule_id = filtergroup_id;
    }
    requestData.discount_name = $('#autoDeleteSingleDiscountname').val();
    var request = {
        NameOfAPI: 'deleteAddedDiscount',
        DealerCode: requestData.DealerCode,
        discount_name: discount_name,
        filterGroupId: rule_id
    };
    var url = 'rule-discount/delete';
    var method = "POST";
    ajaxCall(url, method, request);
}

function applyRuleDiscounttoVehicle() {
    var vin = $('#add-rule-discount-model #ModalDiscountVIN').text();
    var pos = ExcludeVinList.indexOf(vin);
    if (pos == -1) {
        ExcludeVinList.push(vin);
    }
    var financeoption = $('#add-rule-discount-model #ModalfinanceOption').text();
    var discount = getDiscountRowsData('#add-rule-discount-model #RuleDiscountRowWrapper');
    var request, url, method;
    request = {
        NameOfAPI: "AddSingleRuleDiscount",
        DealerCode: requestData.DealerCode,
        VinNumber: vin,
        FinanceOption: requestData.financeoption,
        Discount: discount,
        filterGroupId: globalValues.FilterGroupId,
        ExcludeVinList: ExcludeVinList
    };
    url = 'rule-single-discount/add';
    method = "POST";
    ajaxCall(url, method, request);
}

/*
 ** Populate Functions
 */
function populateEmptyAutomatedDiscountRows(container, discountCount) {
    var discountHtml = '';
    for (i = 0; i < discountCount; i++) {
        discountHtml += EmptyAutoDiscountRow;
    }
    $(container).append(discountHtml);
    var len_of_row = $('#discountAutomatedWrapper .discounts-rows').length;
    $('#discountCount').html(len_of_row);
   // console.log($(container).find('input[name="discount_name_toggle_switch"]:first'));
    $(container).find('input[name="discount_name_toggle_switch"]:first').prop('checked', true);
}

function populateEditUncheckRuleDiscount(vindiscounts) {
    $('#add-rule-discount-model').modal('show');
    var discount = [];
    if (0 < vindiscounts.length) {
        discount = vindiscounts;
    }
    var current_discount = getDiscountRowsData('#discountAutomatedContainer');
    for (var idx in current_discount) {
        var start_date = current_discount[idx]['discount_start_date'];
        var end_date = current_discount[idx]['discount_end_date'];
        current_discount[idx]['discount_start_date'] = formatReverseDateString(start_date);
        current_discount[idx]['discount_end_date'] = formatReverseDateString(end_date);
        const contains = (string) =>
            discount.findIndex(
                // Is the string contained in the object keys?
                obj => Object.values(obj).includes(string)
            ) !== -1;
        var discount_name = Object.values(discount);
        if (contains(current_discount[idx]['name_of_discount']) == false) {
            discount.push(current_discount[idx]);
        }
    }
    var container = '#add-rule-discount-model #RuleDiscountRowWrapper';
    populateRuleVehicleDiscount(container, discount, requestData);
}

function populateRuleVehicleDiscount(container, data, request) {
    var tblContentHtml = '';
    if (data.length == 0) {
        tblContentHtml = EmptyAutoDiscountRow;
    } else {
        for (var i = 0; i < data.length; i++) {
            var item = data[i];
            saved_discount = (data[i].saved_discount == 0) ? false : true;
            price_switch = (data[i].flat_rate == null || data[i].flat_rate == '' || data[i].flat_rate == 0) ? false : true;
            flat_rate = (data[i].flat_rate == null || data[i].flat_rate == '' || data[i].flat_rate == 0) ? 0 : data[i].flat_rate;
            percent_offer = (data[i].percent_offer == null || data[i].percent_offer == '' || data[i].percent_offer == 0) ? 0 : data[i].percent_offer;
            flate_disable = price_switch ? '' : 'disabled = "disabled"';
            percent_disable = price_switch ? 'disabled = "disabled"' : '';
            price_switch_text = price_switch ? 'checked' : '';
            saved_discount = (data[i].saved_discount == 0) ? '' : 'checked';
            var deletedOnclick = ' onclick="confirmuncheckvinDeleteDiscount(this,\'' + data[i].name_of_discount + '\',\'' + data[i].discount_id + '\',\'' + data[i].uuid + '\');"';
            var discountNameReadonly = 'readonly';
            if (container == '#discountAutomatedWrapper') {
                deletedOnclick = ' onclick="confirmautoDeleteDiscount(this);"';
            }
            tblContentHtml += '<div class="discounts-rows">';
            if (container != '#add-rule-discount-model #RuleDiscountRowWrapper') {
                discountNameReadonly = '';
                tblContentHtml += '<label class="customCheckBox checkbox_each_discount_row"><input type="checkbox" name="rule_discount_saved" class="rule_discount" checked><span></span></label>';
            }
            tblContentHtml += '<div class="discount-details">';
            tblContentHtml += '<input type="hidden" name="discount_filter_rule" value="' + data[i].filtergroup_id + '"><input type="hidden" name="discount_id" value="' + data[i].discount_id + '"><input type="hidden" name="discount_uuid" value="' + data[i].uuid + '">';
            tblContentHtml += '<div class="discount-space">';
            tblContentHtml += '<span class="discount-name-field"><label>Discount Name</label><input type="text" name="discount_name" value="' + data[i].name_of_discount + '" maxlength="60" ' + discountNameReadonly + '><span class="discount-name-error bulk-error">Please enter discount name</span><!-- <span class="discount-name-edit-field">Edit Name</span> --></span>';
            tblContentHtml += '<span class="discount-flat-rate"> <label>Flat Rate <span class="switch"><label><input type="checkbox" class="price_switch" name="price_switch" ' + price_switch_text + '><span class="lever"></span></label></span></label>';
            tblContentHtml += '<input type="text" class="flat_rate" name="flat_rate" value="' + flat_rate + '"' + flate_disable + ' maxlength="4" onkeypress="numbervalidate(event)"><span class="flat-rate-error bulk-error">Please enter discount amount</span> </span>';
            tblContentHtml += '<span class="or-text">or</span>';
            tblContentHtml += '<span class="off-percent"><label class="off-percent-label">% Off</label><input type="text" class="off_percent offpercentage" name="off_percent" value="' + percent_offer + '" ' + percent_disable + ' maxlength="2" onkeypress="numbervalidate(event)"><span class="off_percent_error bulk-error">Please enter % value for discount</span></span>';
            tblContentHtml += '<span class="discount-date-field"><label class="discount-start-date-label">Start Date</label><input type="date" name="discount_start_date" class="start_datepicker discount_start_date" value="' + formatDateString(data[i].discount_start_date) + '" min="' + globalValues.todayDate + '" max="' + globalValues.maxDate + '"><span class="discount_start_date_error bulk-error">Please enter Start Date</span></span>';
            tblContentHtml += '<span class="discount-date-field"><label class="discount-end-date-label">End Date</label><input type="date" name="discount_end_date" class="end_datepicker discount_end_date" value="' + formatDateString(data[i].discount_end_date) + '" min="' + globalValues.todayDate + '" max="' + globalValues.maxDate + '"><span class="discount_end_date_error bulk-error">Please enter End Date</span></span>';
            tblContentHtml += '<span class="discount-inventory-field discount-date-field">  <label class="discount-inventory-label">Inventory</label><select class="discount-inventory" name="discount-inventory" id="da_auto_discount_inventory"><option value="New"> New </option></select></span>';
            tblContentHtml += '</div>';
            tblContentHtml += '<div class="saved-discounts"><label class="customCheckBox"><input type="checkbox" name="saved_discount" ' + saved_discount + '><span>ADD to Saved Discounts (Only 5 Allowed)</span></label></div><span class="discount-over-field">This Discount is over and above the FCA incentives</span>';
            var duplicateBtn = 'hidden';
            if (container != '#add-rule-discount-model #RuleDiscountRowWrapper') {
                duplicateBtn = 'visible';
            }
            tblContentHtml += '<div class="discounts-button text-right"> <button class="discount_buttons duplicate_discount_btn" onclick="duplicate_discount(this);"  style="visibility:' + duplicateBtn + '">DUPLICATE DISCOUNT</button></div></div>';
            tblContentHtml += '<div class="discount-delete-field" ' + deletedOnclick + '><span><i aria-hidden="true" class="fa fa-trash-alt"></i></span></div>';
            tblContentHtml += '</div>';
        }
    }
    $(container).html(tblContentHtml);
    var len_of_row = $('#discountAutomatedWrapper .discounts-rows').length;
    $('#discountCount').html(len_of_row);
}

/*
 * Init Functions
 */
function init() {
    $('#da_auto_event_make').addClass('processing-value');
    $('#CreatedDiscountBtn,#SavedDiscountBtn').removeClass('disabled');
    $('#CreatedDiscountBtn,#SavedDiscountBtn').addClass('disabled');
    getMake();
    requestData = {
        DealerCode: DealerCode,
        makeCode: "",
        financeoption: 1
    };
    //getModelYear();
    //initateTrimMultiSelect('#da_auto_event_trim');
    switchViewTabs(1);
    $('#discountAutomatedWrapper').html('');
    ExcludeVinList = [];
}

function switchViewTabs(discount_view_type) {
    switchRuleViewTabs = discount_view_type;
    if (discount_view_type == 1) {
        $('.gridTableWrapper,#savedDiscountWrapper').hide();
        $('.pleaseSelect,.howmanyField,#CreatedDiscountBtn,#SavedDiscountBtn').show();
        $('.gridAutomatedDiscountTableWrapper').hide();
        $('#discount_include_error').css('visibility', 'hidden');
        //$('#modelYearText,#trimText').html('');
        ExcludeVinList = [];
    }
    if (discount_view_type == 2) {
        $('.pleaseSelect').show();
        $('#discountAutomatedWrapper').html('');
        ExcludeVinList = [];
        $('.gridTableWrapper,#vehicleDiscountWrapper,.howmanyField,.gridAutomatedDiscountTableWrapper').hide();
    }
}

function createDiscountBtn() {
    updateDiscountFilterText();
    $(".dropDownPanel *").prop('disabled', true);
    addFilterGroup();
    switchViewTabs(1);
    requestData['hmdiscounts'] = 1;
    $('.discount-error-check').removeClass('active');
    $('.pleaseSelect').hide();
    $('#vehicleDiscountWrapper').show();
    $('.gridAutomatedDiscountTableWrapper').hide();
    getRuleDiscounts();
    ruleDiscountaAddMore();
}

function ruleDiscountaAddMore() {
    var len_of_row = $('#discountAutomatedWrapper .discounts-rows').length;
    if (!(len_of_row < 5)) {
        $('#maximum_discount_modal').modal('show');
        return false;
    }
    var discountCount = 1;
    populateEmptyAutomatedDiscountRows('#discountAutomatedWrapper', discountCount);
}

function savedDiscountBtn() {
    switchViewTabs(2);
    updateDiscountFilterText();
    $('.pleaseSelect').hide();
    $('#savedDiscountWrapper').show();
    $('.gridAutomatedDiscountTableWrapper').hide();
    getSavedDiscounts();
    return false;
}

function view_all_discounts() {
    var message = "<p>Under Development</p>";
    $('#auto_discount_success_modal .modal-body').html(message);
    $('#auto_discount_success_modal').modal('show');
}

function getSelectedCountofDiscountsName() {
    var discountNames = [];
    if (0 == requestData['hmdiscounts']) {
        return discountNames;
    }
    $('.created-automated-discount .discountListField input[type="text"]').each(function(index) {
        var discount_name = $(this).val();
        if (discount_name != '') {
            discountNames.push(discount_name);
        }
    });
    return discountNames;
}

function duplicate_discount(event) {
    var len_of_row = $('#discountAutomatedWrapper .discounts-rows').length;
    if (!(len_of_row < 5)) {
        $('#maximum_discount_modal').modal('show');
        return false;
    }
    var discount_name = $(event).closest('.discounts-rows').find('input[name="discount_name"]').val();
    if (discount_name == '' || discount_name == null || discount_name == undefined) {
        $('#no-discount-duplicate-modal').modal('show');
        return false;
    }
    var discount_name_list = [];
    $('.discounts-rows').find('input[name="discount_name"]').each(function() {
        if (this.value) {
            discount_name_list.push(this.value);
        }
    });
    var discount_name = $(event).closest('.discounts-rows').find('input[name="discount_name"]').val();
    var duplicate_discount_name = '';
    if (0 < discount_name_list.length) {
        var i = 1;
        duplicate_discount_name = discount_name + '(' + i + ')';
        do {
            var pos = discount_name_list.indexOf(duplicate_discount_name);
            if (pos == -1) {
                break;
            }
            i++;
            duplicate_discount_name = discount_name + '(' + i + ')';
        } while (0 < i);
    }
    var ele = $(event).closest('.discounts-rows').clone(true);
    $(ele).find('input[name="discount_name"]').val(duplicate_discount_name);
    $(ele).find('input[name="discount_name_toggle_switch"]').prop('checked', false);
    $(ele).find('input[name="discount_id"]').val('');
    $(ele).find('input[name="discount_uuid"]').val('');
    $(ele).find('input[name="discount_filter_rule"]').val('');
    $(event).closest('.discounts-rows').after(ele);
}

function resetAutomatedTable() {
    $('.pleaseSelect').show();
    $('.gridTableWrapper').hide();
    dataTableOperation('.gridAutomatedDiscountTableContainer');
    $('.gridAutomatedDiscountTableWrapper').hide();
    $('#gridAutomatedDiscountTableContainer' + '> tbody').html('<tr><td colspan="6" align="center">Loading ...</td></tr>');
}

function showEditInventory() {
    globalValues.auto_discount_validation = autoDiscountValidationCheck('#discountAutomatedContainer');
    if (globalValues.auto_discount_validation == false) {
        return false;
    }
    var showMessage = flate_rate_maximum_validation('#discountAutomatedContainer');
    if (showMessage) {
        $('#warning-max-discount-model').modal('show');
        return false;
    }
    var result = 'false';
    var result = discountflatratevalidation();
    if (result == 'true') {
        $('#warning-max-discount-model').modal('show');
        return false;
    } else {
        $('#warning-max-discount-model').modal('hide');
    }

    var discount_include_count = $('input[name="rule_discount_saved"]:checked').length;
    if (0 == discount_include_count) {
        $('#discount_include_error').css('visibility', 'visible');
        return false;
    }
    $('#discount_include_error').css('visibility', 'hidden');

    //TODO:: find duplicate names
    var discount_names = getDiscountNames('#discountAutomatedContainer');
    var duplicate_names = findDuplicatesInArray(discount_names);
    if (0 < duplicate_names.length) {
        $('#warning-duplicate-discount-names-model .discount-duplicate-names').html(duplicate_names.toString());
        $('#warning-duplicate-discount-names-model').modal('show');
        return false;
    }
    dataTableOperation('.gridAutomatedDiscountTableContainer');
    $('.gridAutomatedDiscountTableWrapper').show();
    $('#gridAutomatedDiscountTableContainer').addClass('processing');
    $('#gridAutomatedDiscountTableContainer' + '> tbody').html('<tr><td colspan="6" align="center">Loading ...</td></tr>');
    getDiscountVehicles();
}

function SavedshowEditInventory() {
    dataTableOperation('.gridAutomatedDiscountTableContainer');
    globalValues.auto_discount_validation = false;
    globalValues.auto_discount_validation = autoDiscountValidationCheck('#saveddiscountAutomatedContainer');
    if (globalValues.auto_discount_validation == false) {
        return false;
    }
    var showMessage = flate_rate_maximum_validation('#saveddiscountAutomatedContainer');
    if (showMessage) {
        $('#warning-max-discount-model').modal('show');
        return false;
    }
    //TODO:: find duplicate names
    var discount_names = getDiscountNames('#saveddiscountAutomatedContainer');
    var duplicate_names = findDuplicatesInArray(discount_names);
    if (0 < duplicate_names.length) {
        $('#warning-duplicate-discount-names-model .discount-duplicate-names').html(duplicate_names.toString());
        $('#warning-duplicate-discount-names-model').modal('show');
        return false;
    }
    $('.gridAutomatedDiscountTableWrapper').show();
    $('#gridAutomatedDiscountTableContainer').addClass('processing');
    $('#gridAutomatedDiscountTableContainer' + '> tbody').html('<tr><td colspan="6" align="center">Loading ...</td></tr>');
    getDiscountVehicles();
}

function closeInventory() {
    $('.gridAutomatedDiscountTableWrapper').hide();
    $('#discount_include_error').css('visibility', 'hidden');
}

function confirmapplyautoDiscounttoVehicle() {
    //$('#gridAutomatedDiscountTableContainer').addClass('processing')
    globalValues.auto_discount_validation = autoDiscountValidationCheck('#discountAutomatedContainer');
    if (globalValues.auto_discount_validation == false) {
        return false;
    }
    var vin = getIncludeVinRowsData('#gridAutomatedDiscountTableContainer');
    if (0 == vin.length) {
        $('#gridAutomatedDiscountTableError').addClass('active');
        globalValues.auto_discount_validation = false;
    } else {
        $('#gridAutomatedDiscountTableError').removeClass('active');
        globalValues.auto_discount_validation = globalValues.auto_discount_validation ? true : false;
    }
    if (globalValues.auto_discount_validation == false) {
        return false;
    }
    //TODO:: find duplicate names
    var discount_names = getDiscountNames('#discountAutomatedContainer');
    var duplicate_names = findDuplicatesInArray(discount_names);
    if (0 < duplicate_names.length) {
        $('#warning-duplicate-discount-names-model .discount-duplicate-names').html(duplicate_names.toString());
        $('#warning-duplicate-discount-names-model').modal('show');
        return false;
    }
    $('#auto_discount_warning_modal').modal('show');
}

function confirmapplyautoSavedDiscounttoVehicle() {
    //$('#gridAutomatedDiscountTableContainer').addClass('processing')
    globalValues.auto_discount_validation = false;
    globalValues.auto_discount_validation = autoDiscountValidationCheck('#saveddiscountAutomatedContainer');
    if (globalValues.auto_discount_validation == false) {
        return false;
    }
    var vin = getIncludeVinRowsData('#gridAutomatedSavedDiscountTableContainer');
    if (0 == vin.length) {
        $('#gridAutomatedSavedDiscountTableError').addClass('active');
        //globalValues.auto_discount_validation = false;
    } else {
        $('#gridAutomatedSavedDiscountTableError').removeClass('active');
        //globalValues.auto_discount_validation = globalValues.auto_discount_validation ? true : false;
    }
    /*    if (globalValues.auto_discount_validation == false) {
            return false;
        }*/
    //TODO:: find duplicate names
    var discount_names = getDiscountNames('#saveddiscountAutomatedContainer');
    var duplicate_names = findDuplicatesInArray(discount_names);
    if (0 < duplicate_names.length) {
        $('#warning-duplicate-discount-names-model .discount-duplicate-names').html(duplicate_names.toString());
        $('#warning-duplicate-discount-names-model').modal('show');
        return false;
    }
    $('#auto_discount_warning_modal').modal('show');
}

function processAddedAutomatedDiscounts(data) {
    var container = '#discountAutomatedContainer';
    var len_of_row = $(container + ' .discounts-rows').length;
    for (i = 1; i < len_of_row + 1; i++) {
        c_discount_name_val = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="discount_name"]');
        c_uuid_val = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="discount_id"]');
        c_discount_filter_group = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="discount_filter_rule"]');
        var discount_name = $(c_discount_name_val).val();
        var discount_id = $(c_uuid_val).val();
        for (var idx in data) {
            if (discount_name == idx) {
                $(c_uuid_val).val(data[idx]);
                $(c_discount_filter_group).val(globalValues.FilterGroupId);
                break;
            }
        }
    }
}

function autoSingleDiscountValidationCheck(ele) {
    var bulk_discount_validation = false;
    c_discount_name_val = $(ele).find('input[name="discount_name"]');
    c_flat_rate_val = $(ele).find('input[name="flat_rate"]');
    off_percent_val = $(ele).find('input[name="off_percent"]');
    c_discount_start_date = $(ele).find('input[name="discount_start_date"]');
    c_discount_end_date = $(ele).find('input[name="discount_end_date"]');
    var discount_name = $(c_discount_name_val).val();
    var flat_rate = $(c_flat_rate_val).val();
    var off_percent = $(off_percent_val).val();
    var start_date = $(c_discount_start_date).val();
    var end_date = $(c_discount_end_date).val();
    discount_name = checkValueUndefined(discount_name) ? discount_name.trim() : '';
    flat_rate = checkValueUndefined(flat_rate) ? parseInt(flat_rate.trim()) : '';
    off_percent = checkValueUndefined(off_percent) ? parseInt(off_percent.trim()) : '';
    start_date = start_date.trim();
    end_date = end_date.trim();
    if (discount_name === '') {
        $(c_discount_name_val).siblings('.bulk-error').addClass('active');
        bulk_discount_validation = false;
    } else {
        $(c_discount_name_val).siblings('.bulk-error').removeClass('active');
        bulk_discount_validation = true;
    }

    var flat_rate_checkbox = $(c_flat_rate_val).siblings('label').find('.switch input[type="checkbox"]').prop('checked');
    if (((flat_rate === '' || 0 == flat_rate) && flat_rate_checkbox) || (!$.isNumeric(flat_rate) && flat_rate_checkbox)) {
        $(c_flat_rate_val).siblings('.bulk-error').addClass('active');
        bulk_discount_validation = false;
    } else {
        $(c_flat_rate_val).siblings('.bulk-error').removeClass('active');
        bulk_discount_validation = bulk_discount_validation ? true : false;
    }
    var price_toggle = $(off_percent_val).closest('.discount-space').find('.switch input[type="checkbox"]').prop('checked');
    if (((off_percent === '' || 0 == off_percent) && !price_toggle) || (!$.isNumeric(off_percent) && !price_toggle)) {
        $(off_percent_val).siblings('.bulk-error').addClass('active');
        bulk_discount_validation = false;
    } else {
        $(off_percent_val).siblings('.bulk-error').removeClass('active');
        bulk_discount_validation = bulk_discount_validation ? true : false;
    }

    if (start_date === '') {
        bulk_discount_validation = false;
        $(c_discount_start_date).siblings('.bulk-error').addClass('active');
        $(c_discount_start_date).siblings('.bulk-error').text('Please enter Start Date');
    } else if (globalValues.todayDate > start_date) {
        bulk_discount_validation = false;
        $(c_discount_start_date).siblings('.bulk-error').addClass('active');
        $(c_discount_start_date).siblings('.bulk-error').text('Start date cannot be prior to today’s date');
    } else {
        $(c_discount_start_date).siblings('.bulk-error').removeClass('active');
        bulk_discount_validation = bulk_discount_validation ? true : false;
    }

    if (end_date === '') {
        $(c_discount_end_date).siblings('.bulk-error').addClass('active');
        bulk_discount_validation = false;
    } else if (globalValues.todayDate > end_date) {
        bulk_discount_validation = false;
        $(c_discount_end_date).siblings('.bulk-error').addClass('active');
        $(c_discount_end_date).siblings('.bulk-error').text('End date cannot be prior to today’s date');
    } else if (start_date > end_date) {
        bulk_discount_validation = false;
        $(c_discount_end_date).siblings('.bulk-error').addClass('active');
        $(c_discount_end_date).siblings('.bulk-error').text('End date cannot be prior to Start Date');
    } else {
        $(c_discount_end_date).siblings('.bulk-error').removeClass('active');
        bulk_discount_validation = bulk_discount_validation ? true : false;
    }

    return bulk_discount_validation;
}

function autoDiscountValidationCheck(container) {
    var bulk_discount_validation = false;
    var len_of_row = $(container + ' .discounts-rows-parent .discounts-rows').length;
    for (i = 1; i < (len_of_row + 1); i++) {
        c_discount_name_val = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="discount_name"]');
        c_flat_rate_val = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="flat_rate"]');
        off_percent_val = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="off_percent"]');
        c_discount_start_date = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="discount_start_date"]');
        c_discount_end_date = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="discount_end_date"]');
        var c_price_toggle = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="price_switch"]');
        c_discount_uuid = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="discount_uuid"]');
        c_discount_id = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="discount_id"]');
        var discount_name = $(c_discount_name_val).val();
        var flat_rate = $(c_flat_rate_val).val();
        var off_percent = $(off_percent_val).val();
        var start_date = $(c_discount_start_date).val();
        var end_date = $(c_discount_end_date).val();
        var price_toggle = $(c_price_toggle).prop('checked');
        var discount_uuid = $(c_discount_uuid).val();
        var discount_id = $(c_discount_id).val();
        discount_name = checkValueUndefined(discount_name) ? discount_name.trim() : '';
        flat_rate = checkValueUndefined(flat_rate) ? parseInt(flat_rate.trim()) : '';
        off_percent = checkValueUndefined(off_percent) ? parseInt(off_percent.trim()) : '';
        start_date = start_date.trim();
        end_date = end_date.trim();
        if (discount_name === '') {
            $(c_discount_name_val).siblings('.bulk-error').addClass('active');
            bulk_discount_validation = false;
        } else {
            $(c_discount_name_val).siblings('.bulk-error').removeClass('active');
            bulk_discount_validation = true;
        }

        if (((flat_rate === '' || 0 == flat_rate) && price_toggle) || (!$.isNumeric(flat_rate) && price_toggle)) {
            $(c_flat_rate_val).siblings('.bulk-error').addClass('active');
            bulk_discount_validation = false;
        } else {
            $(c_flat_rate_val).siblings('.bulk-error').removeClass('active');
            bulk_discount_validation = bulk_discount_validation ? true : false;
        }
        if (((off_percent === '' || 0 == off_percent) && !price_toggle) || (!$.isNumeric(off_percent) && !price_toggle)) {
            $(off_percent_val).siblings('.bulk-error').addClass('active');
            bulk_discount_validation = false;
        } else {
            $(off_percent_val).siblings('.bulk-error').removeClass('active');
            bulk_discount_validation = bulk_discount_validation ? true : false;
        }

        if (start_date === '') {
            bulk_discount_validation = false;
            $(c_discount_start_date).siblings('.bulk-error').addClass('active');
            $(c_discount_start_date).siblings('.bulk-error').text('Please enter Start Date');
        } else if (globalValues.todayDate > start_date && ((discount_uuid == '' || discount_uuid == null || discount_uuid == undefined) && (discount_id == '' || discount_id == null || discount_id == undefined))) {
            bulk_discount_validation = false;
            $(c_discount_start_date).siblings('.bulk-error').addClass('active');
            $(c_discount_start_date).siblings('.bulk-error').text('Start date cannot be prior to today’s date');
        } else {
            $(c_discount_start_date).siblings('.bulk-error').removeClass('active');
            bulk_discount_validation = bulk_discount_validation ? true : false;
        }

        if (end_date === '') {
            $(c_discount_end_date).siblings('.bulk-error').addClass('active');
            bulk_discount_validation = false;
        } else if (globalValues.todayDate > end_date) {
            bulk_discount_validation = false;
            $(c_discount_end_date).siblings('.bulk-error').addClass('active');
            $(c_discount_end_date).siblings('.bulk-error').text('End date cannot be prior to today’s date');
        } else if (start_date > end_date) {
            bulk_discount_validation = false;
            $(c_discount_end_date).siblings('.bulk-error').addClass('active');
            $(c_discount_end_date).siblings('.bulk-error').text('End date cannot be prior to Start Date');
        } else {
            $(c_discount_end_date).siblings('.bulk-error').removeClass('active');
            bulk_discount_validation = bulk_discount_validation ? true : false;
        }
        if (!bulk_discount_validation) {
            break;
        }
    }
    return bulk_discount_validation;
}

function flate_rate_maximum_validation(container) {
    var showMessage = false;
    var len_of_row = $(container + ' .discounts-rows-parent .discounts-rows').length;
    var total_amount = 0;
    for (i = 1; i < len_of_row + 1; i++) {
        var c_flat_rate_val = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="flat_rate"]');
        var flat_rate = $(c_flat_rate_val).val();
        flat_rate = checkValueUndefined(flat_rate) ? parseInt(flat_rate.trim()) : 0;
        var flat_rate_checkbox = $(c_flat_rate_val).siblings('label').find('.switch input[type="checkbox"]').prop('checked');
        if (0 < flat_rate && flat_rate_checkbox) {
            total_amount += flat_rate;
        }
    }
    return (5000 < total_amount) ? true : false;
}

function confirmSavedDiscountDelete(discount_name, uuid) {
    $('#DeleteDiscountPopupName').html(discount_name);
    $('#DeleteSingleDiscountPopupUUid').val(uuid);
    $('#discount_delete_popup_modal').modal('show');
}

function confirmboxofdiscount() {
    $('#conforamtion_discount_popup_modal').modal('show');
}

function confirmautodiscount(savedDiscount) {
    var chk = checkpercentageValidation(savedDiscount);
    if (chk.length > 0) {
        confirmboxofdiscount();
    } else {
        Applydiscount();
    }
}

function applyAutoDiscounttoVehicle() {
    var savedDiscount = $('#savedDiscountWrapper').css('display');
    confirmautodiscount(savedDiscount);
}

function confirmautoDeleteDiscount(ele) {
    deleteCurrentObject = ele;
    var discount_name = $(ele).parent('.discounts-rows').find('input[name="discount_name"]').val();
    var discount_id = $(ele).parent('.discounts-rows').find('input[name="discount_id"]').val();
    if (discount_id == '') {
        $('#unsaved_auto_discount_warning_modal').modal('show');
        return false;
    }
    $('#DeleteautoDiscountPopupName').html(discount_name);
    $('#autoDeleteSingleDiscountname').val(discount_name);
    $('#autoDeleteSingleDiscountPopupUUid').val(discount_id);
    $('#autodiscount_delete_popup_modal').modal('show');
}

function confirmuncheckvinDeleteDiscount(event, discount_name, discount_id, uuid) {
    deleteCurrentObject = event;
    if ((discount_id == 'undefined' || discount_id == undefined) && (uuid == 'undefined' || uuid == undefined)) {
        $('#unsaved_uncheck_auto_discount_warning_modal').modal('show');
        return false;
    }
    $('#add-rule-discount-model #ModalDiscountName').html(discount_name);
    $('#add-rule-discount-model #ModalDiscountUUID').html(uuid);
    $('#add-rule-discount-model #ModalDiscountID').html(discount_id);
    $('#uncheck_rule_discount_delete_popup_modal').modal('show');
    $('#uncheckDeleteDiscountPopupName').html(discount_name);
}

function uncheckdeleteEmptyDiscount() {
    if (deleteCurrentObject != undefined) {
        $(deleteCurrentObject).parent('.discounts-rows').remove();
    }
    $('#unsaved_uncheck_auto_discount_warning_modal').modal('hide');
    var currentLength = $('#add-rule-discount-model .discounts-rows-parent .discounts-rows').length;
    if (0 == currentLength) {
        $('#add-rule-discount-model').modal('hide');
    }
    deleteCurrentObject = undefined;
}

function resetAutomatedfilters() {
    $('input[name=finance_option][value="1"]').prop('checked', true);
    $('#da_auto_event_model_year').html('<option value=""> Select Year</option>');
    $('#da_auto_event_vehicle').html('<option value=""> Choose a Vehicle</option>');
    $('#da_auto_event_hwmnydiscount').val(0);
    $('#CreatedDiscountBtn,#SavedDiscountBtn').removeClass('disabled');
    $('#CreatedDiscountBtn,#SavedDiscountBtn').addClass('disabled');
    $('#trimSelectionText').css('visibility', 'hidden');
    $('#trimText').html('');
    $('#vehicleCount').html(0);
    $('#modelYearText').html('');
    dataTableOperation('.gridAutomatedDiscountTableContainer');
    init();
}

function deleteEmptyDiscount() {
    var discount_name = $(deleteCurrentObject).parent('.discounts-rows').find('input[name="discount_name"]').val();
    var discount_id = $(deleteCurrentObject).parent('.discounts-rows').find('input[name="discount_id"]').val();
    $(deleteCurrentObject).parent('.discounts-rows').remove();
    if (discount_name != '' && discount_id != '') {
        deleteautoDiscount(discount_name);
        return false;
    }
    $('#unsaved_auto_discount_warning_modal').modal('hide');
    var discount_len = $('#discountAutomatedWrapper .discounts-rows').length;
    if (0 < discount_len) {
        $('#discountCount').html(discount_len);
        $('#discountAutomatedWrapper .discounts-rows').find('input[name="discount_name_toggle_switch"]:first').prop('checked', true);
        dataTableOperation('.gridAutomatedDiscountTableContainer');
        $('.gridAutomatedDiscountTableWrapper').hide();
        $('#gridAutomatedDiscountTableContainer' + '> tbody').html('<tr><td colspan="6" align="center">Loading ...</td></tr>');
    } else {
        resetAutomatedTable();
        resetAutomatedfilters();
    }
}

function EditUncheckRuleDiscount(event, vin, msrp) {
    //return false;
    //console.log(event, vin,msrp)
    $("#msrp").remove();
    $('<input type="hidden" id="msrp" name="msrp" value="' + msrp + '">').insertAfter('#ModalDiscountVIN');
    $('#RulediscountStatusCodeError').css('display', 'none');
    $('#add-rule-discount-model #ModalDiscountVIN').html(vin);
    $('#add-rule-discount-model #ModalfinanceOption').html(requestData['financeoption']);
    getVinRuleDiscounts(vin);
}

function confirmRuleVehicleAddDiscount() {
    globalValues.auto_discount_validation = autoDiscountValidationCheck('#add-rule-discount-model');
    if (globalValues.auto_discount_validation == false) {
        return false;
    }
    var showMessage = flate_rate_maximum_validation('#add-rule-discount-model');
    if (showMessage) {
        $('#warning-max-discount-model').modal('show');
        return false;
    }
    var msrp = $('#msrp').val();
    var rs = Editpercentagecountvalidation(msrp);
    if (rs == 'true') {
        $('#RulediscountStatusCodeError').html('<h6 align="center">Maximum allowed discount is $5000</h6>').css('display', 'block');
        return false;
    }
    //$('#add-rule-discount-model .add-bulk-discount-alert').show();
    applyRuleDiscounttoVehicle();
}

function updateDiscountFilterText() {
    var makecode = $('#da_auto_event_make').val();
    if (makecode != '') {
        requestData['makeCode'] = makecode;
        $('#makeText,#SavedmakeText').html($('#da_auto_event_make option:selected').text());
    } else {
        requestData['modelyear'] = '';
        requestData['model'] = '';
        requestData['trim'] = '';
        $('#makeText,#SavedmakeText').html('');
    }
    var modelyear = $('#da_auto_event_model_year').val();
    if (modelyear != '') {
        requestData['modelyear'] = modelyear;
        $('#modelYearText,#SavedmodelYearText').html(modelyear);
    } else {
        requestData['modelyear'] = '';
        requestData['model'] = '';
        requestData['trim'] = '';
        $('#modelYearText,#SavedmodelYearText').html('');
    }
    var vehicle = $('#da_auto_event_vehicle').val();
    if (vehicle == '') {
        requestData['model'] = '';
        requestData['trim'] = '';
        $('#vehicleCount').html(0);
        $('#trimSelectionText,#SavedtrimSelectionText').css('visibility', 'hidden');
    } else {
        requestData['model'] = vehicle;
    }
    var trim = $('#da_auto_event_trim').val();
    if (trim != '') {
        requestData['trim'] = trim;
        $('#vehicleCount').html(1);
        $('#trimSelectionText,#SavedtrimSelectionText').css('visibility', 'visible');
        $('#trimText,#SavedtrimText').html(trim.toString());
        $('#trimText,#SavedtrimText').text().replace(",", ", ");
    } else {
        requestData['trim'] = '';
        if (0 < globalValues.Trims.length && vehicle != '') {
            $('#trimSelectionText,#SavedtrimSelectionText').css('visibility', 'visible');
            $('#vehicleCount').html(globalValues.Trims.length);
            $('#trimText,#SavedtrimText').html(globalValues.Trims.toString());
        }
    }
    console.log('requestData', requestData);
}

/*
 * Sathyendra COde
 */
function checkpercentageValidation(savedDiscount) {
    var chk = [];
    var addTooltip = false;
    var containerId = (savedDiscount == 'none') ? '#gridAutomatedDiscountTableContainer' : '#gridAutomatedSavedDiscountTableContainer' ;
    var datatable = $(containerId).DataTable();
    var data = datatable
        .rows()
        .data();
    var rows = datatable
        .rows();
    for (let index = 0; index < data.length; index++) {
        var datavalue = 0
        const element = data[index];
        dataindex = index;
        //console.log(index);
        //console.log("checkbox", datatable.$('input[name="auto_discount_include"]').eq(index).prop("checked"));
        if (datatable.$('input[name="auto_discount_include"]').eq(index).prop("checked")) {
            var datavalue = datatable.$("tr").eq(dataindex).attr('data-msrp-value');

            if (datavalue != undefined) {
                if (savedDiscount == 'none') {
                    rs = percentagediscountflatratevalidation(datavalue);
                } else {
                    rs = percentagediscountvalidation('#saveddiscountAutomatedWrapper',datavalue);
                }

                datatable.rows(index).nodes().to$().css('background-color', '');
                datatable.rows(index).nodes().to$().removeClass('highlighted_tr');
                if (rs == 'true') {
                    datatable.rows(index).nodes().to$().css('background-color', '#d8c3c7'); //debac2
                    datatable.rows(index).nodes().to$().addClass('highlighted_tr');
                    var trdiscount = datatable.$("tr").eq(index).find(".discount-msrp-span");
                    trdiscount.text('-$5,000');
                    // if (!addTooltip) {
                    //     $('#gridAutomatedDiscountTableContainer_wrapper table tr td:first-child').addClass('relative');
                    //     $('#gridAutomatedDiscountTableContainer_wrapper table tr td:first-child').append('<div class="highlighted_tr_info"><span class="info_text_left">' + percentageValidationTooltip + '</span><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></div>');

                    //     $('#gridAutomatedDiscountTableContainer_wrapper table tr td:first-child .highlighted_tr_info').hover(function () {
                    //         datatable.rows().nodes().to$().find(".info_text_left").addClass('showme');
                    //     }, function () {
                    //         datatable.rows().nodes().to$().find(".info_text_left").removeClass('showme');
                    //     });
                    //     addTooltip = true;
                    // }
                    chk[index] = 1;
                }
            }
        }
    }
    return chk;
}

function validatecheckPercentage(chk) {
    var addTooltip = false;
    datavalue = chk.closest("tr").attr('data-msrp-value');
    var tr = chk.closest("tr");
    $(tr).removeClass('highlighted_tr');
    $(tr).removeAttr('style');
    if (chk.is(":checked")) {
        rs = percentagediscountflatratevalidation(datavalue);
        if (rs == 'true') {
            $(tr).css('background-color', '#d8c3c7');
            $(tr).addClass('highlighted_tr');
            $(tr).find('td:first-child').prepend("<i class='fas fa-exclamation-triangle' ></i>");
            $(tr).find('td:first-child').addClass('highlighted_td');
        }
    } else {
        $(tr).css('background-color', '');
        $(tr).removeClass('highlighted_tr');
        $(tr).find('td:first-child').removeClass('highlighted_td');
        $(tr).find('td:first-child').find("i").remove();
    }
}

function discountflatratevalidation() {
    var row = [], dates = [], obj = [], obj2 = [];
    ///getting index of check 
    $(".rule_discount").each(function(key, val) {
        if ($(this).is(":checked")) {
            row.push(key);
        }
    });

    $(".flat_rate").each(function(key, val) {
        if ($.inArray(key, row) != '-1') {
            var start_date = $(".discount_start_date").eq(key).val();
            //console.log(start_date);
            if (start_date != '') {
                var flat = Number($(this).val());
                var end_date = $(".discount_end_date").eq(key).val();
                var percent = $(".off_percent").eq(key).val();
                if (percent == '') { percent = 0 }
                obj[key] = [flat, start_date, end_date, percent];
                obj2[key] = [flat, start_date, end_date, percent];
                dates[key] = [start_date, end_date];
            }
        }
    });
    // console.log('date', dates);
    //console.log('obj', obj);
    total = 0;
    var group = [], group2 = [], single = [], Index = {}, Index2 = [];
    if (!$.isEmptyObject(obj)) {
        $.each(obj, function(index, value) {
            if (Array.isArray(value)) {
                check_date = value[1];
                var temp = [];
                $.each(obj2, function(ind, val) {
                    if (Array.isArray(val)) {
                        start_date = val[1];
                        end_date = val[2];
                        if (Date.parse(check_date) <= Date.parse(end_date) && Date.parse(check_date) >= Date.parse(start_date)) {
                            temp.push(ind);
                        } else {}
                    }
                });
                group[index] = temp;
                total += value[0];
            }
        });
    }
    ///validating 5000
    //console.log('group', group);
    var max = 0
    $.each(group, function(key, value) {
        var max = 0
        if (Array.isArray(value) && value.length) {
            $.each(value, function(key, index) {
                max += obj[index][0]
            });
            //console.log('max', max);
            status = (max > 5000) ? 'true' : 'false';
        }
    });
    return status;
}

function percentagediscountflatratevalidation(datavalue) {
    var row = [], dates = [], obj = [], obj2 = [];
    ///getting index of check 
    $(".rule_discount").each(function(key, val) {
        if ($(this).is(":checked")) {
            row.push(key);
        }
    });
    console.log('row', row);
    $(".flat_rate").each(function(key, val) {
        if ($.inArray(key, row) != '-1') {
            var start_date = $(".discount_start_date").eq(key).val();
            if (start_date != '') {
                var flat = Number($(this).val());
                var end_date = $(".discount_end_date").eq(key).val();
                var percent = $(".off_percent").eq(key).val();
                if (percent == '') { percent = 0 }
                obj[key] = [flat, start_date, end_date, percent];
                obj2[key] = [flat, start_date, end_date, percent];
                dates[key] = [start_date, end_date];
            }
        }
    });
    total = 0;
    var group = [], group2 = [], single = [], Index = {}, Index2 = [];
    if (!$.isEmptyObject(obj)) {
        $.each(obj, function(index, value) {
            if (Array.isArray(value)) {
                check_date = value[1];
                var temp = [];
                $.each(obj2, function(ind, val) {
                    if (Array.isArray(val)) {
                        start_date = val[1];
                        end_date = val[2];
                        if (Date.parse(check_date) <= Date.parse(end_date) && Date.parse(check_date) >= Date.parse(start_date)) {
                            temp.push(ind);
                        } else {}
                    }
                });
                group[index] = temp;
                total += value[0];
            }
        });
    }
    ///validating 5000
    //console.log('group', group);
    var max = 0
    $.each(group, function(key, value) {
        var max = 0
        if (Array.isArray(value) && value.length) {
            $.each(value, function(key, index) {
                max += (obj[index][3] == 0) ? obj[index][0] : ((datavalue * obj[index][3]) / 100);
            });
            //console.log('datavalue', datavalue);
            //console.log('percentage value', max);
            status = (max > 5000) ? 'true' : 'false';
        }
    })
    return status;
}

function trimselection() {
    requestData['trim'] = $('#da_auto_event_trim').val();
    var clone = $($('#discounts-rows-trim').html());
    $("#discounts-rows-trim").clone().appendTo("#discountAutomatedWrapper");
    console.log('clone', clone);
}

function Editpercentagecountvalidation(datavalue) {
    var dates = [], obj = [], obj2 = [];
    $('#RuleDiscountRowWrapper').find(".flat_rate").each(function(key, val) {
        //console.log($('#RuleDiscountRowWrapper').find('.off_percent').eq(key).val());
        var start_date = $('#RuleDiscountRowWrapper').find(".discount_start_date").eq(key).val();
        var end_date = $('#RuleDiscountRowWrapper').find(".discount_end_date").eq(key).val();
        if (start_date != '' && end_date != '') {
            var flat = Number($('#RuleDiscountRowWrapper').find(".flat_rate").eq(key).val());
            var percent = $('#RuleDiscountRowWrapper').find('.off_percent').eq(key).val();
            if (percent == '') { percent = 0 }
            obj[key] = [flat, start_date, end_date, percent];
            obj2[key] = [flat, start_date, end_date, percent];
            dates[key] = [start_date, end_date];
        }
    });
    // console.log('date', dates);
    console.log('obj', obj);
    total = 0;
    var group = [];
    if (!$.isEmptyObject(obj)) {
        $.each(obj, function(index, value) {
            if (Array.isArray(value)) {
                check_date = value[1];
                var temp = [];
                $.each(obj2, function(ind, val) {
                    if (Array.isArray(val)) {
                        start_date = val[1];
                        end_date = val[2];
                        if (Date.parse(check_date) <= Date.parse(end_date) && Date.parse(check_date) >= Date.parse(start_date)) {
                            temp.push(ind);
                        }
                    }
                })
                group[index] = temp;
                total += value[0];
            }
        });
    }
    ///validating 5000
    //console.log('group', group);
    var max = 0
    $.each(group, function(key, value) {
        var max = 0
        if (Array.isArray(value) && value.length) {
            $.each(value, function(key, index) {
                 max += (obj[index][3] == 0) ? obj[index][0] : ((datavalue * obj[index][3]) / 100);
            });
            //console.log('percentage', max);
            status = (max > 5000) ? 'true' : 'false';
        }
    })
    return status;
}
/*
 * Satyendra code ends
 */


/*
 * document ready Functions
 */
$(document).ready(function() {
    requestData = {
        DealerCode: DealerCode,
        makeCode: "",
        financeoption: 1
    };
    /*<span class="switch discount_name_switch"><label><input type="checkbox" name="discount_name_toggle_switch"><span class="lever"></span><span class="switchLabel ActiveText"> Active </span></label></span>*/
    EmptyAutoDiscountRow = '<div class="discounts-rows discounts-rows-trim">';
    EmptyAutoDiscountRow += '<label class="customCheckBox checkbox_each_discount_row"><input type="checkbox" name="rule_discount_saved" class="rule_discount" checked> <span> </span> </label>';
    EmptyAutoDiscountRow += '<div class="discount-details"><input type="hidden" name="discount_filter_rule" value="0"><input type="hidden" name="discount_uuid" value=""><input type="hidden" name="discount_id" value="">';
    EmptyAutoDiscountRow += '<div class="discount-space">';
    EmptyAutoDiscountRow += '<span class="discount-name-field"><label>Discount Name</label><input type="text" name="discount_name" maxlength="60" value="" placeholder="Discount Name"><span class="discount-name-error bulk-error">Please enter discount name</span><!-- <span class="discount-name-edit-field">Edit Name</span> --></span>';
    EmptyAutoDiscountRow += '<span class="discount-flat-rate"><label>Flat Rate<span class="switch"><label><input type="checkbox" name="price_switch" class="price_switch"  checked><span class="lever"></span></label></span></label><input type="text" class="flat_rate" name="flat_rate" value="" maxlength="4" placeholder="Flate rate" onkeypress="numbervalidate(event)"><span class="flat-rate-error bulk-error">Please enter discount amount</span></span><span class="or-text">or</span>';
    EmptyAutoDiscountRow += '<span class="off-percent"><label class="off-percent-label">% Off</label><input type="text" name="off_percent" class="off_percent offpercentage" value="" maxlength="2" placeholder="% off" disabled="disabled" onkeypress="numbervalidate(event)"> <span class="off_percent_error bulk-error">Please enter % value for discount</span></span>';
    EmptyAutoDiscountRow += '<span class="discount-date-field">    <label class="discount-start-date-label">Start Date</label><input type="date" name="discount_start_date" class="discount_start_date" min="' + globalValues.todayDate + '" max="' + globalValues.maxDate + '"><span class="discount_start_date_error bulk-error">Please enter Start Date</span></span>';
    EmptyAutoDiscountRow += '<span class="discount-date-field"> <label class="discount-end-date-label">End Date</label><input type="date" class="discount_end_date" name="discount_end_date" min="' + globalValues.todayDate + '" max="' + globalValues.maxDate + '"><span class="discount_end_date_error bulk-error">Please enter End Date</span></span>';
    EmptyAutoDiscountRow += '<span class="discount-inventory-field discount-date-field">  <label class="discount-inventory-label">Inventory</label><select class="discount-inventory" name="discount-inventory" id="da_auto_discount_inventory"><option value="New"> New </option> </select></span>';
    EmptyAutoDiscountRow += '</div>';
    EmptyAutoDiscountRow += '<div class="saved-discounts"><label class="customCheckBox"><input type="checkbox" name="saved_discount" checked><span>ADD to Saved Discounts (Only 5 Allowed)</span></label></div>';
    EmptyAutoDiscountRow += '<span class="discount-over-field">This Discount is over and above the FCA incentives</span>';
    EmptyAutoDiscountRow += '<div class="discounts-button text-right"><button class="discount_buttons duplicate_discount_btn" onclick="duplicate_discount(this);">DUPLICATE DISCOUNT</button> </div>';
    EmptyAutoDiscountRow += '</div>';
    EmptyAutoDiscountRow += '<div class="discount-delete-field" onclick="confirmautoDeleteDiscount(this)"><span><i aria-hidden="true" class="fa fa-trash-alt"></i></span></div>';
    EmptyAutoDiscountRow += '</div>';

    $('.drop_down_discount_list').click(function() {
        $(this).toggleClass('closed');
        $('#discountAutomatedWrapper').toggleClass('closed');
    });

    $('.gridAutomatedDiscountTableWrapper').hide();

    $('#Include-auto-select-all').on('click', function() {
        // Check/uncheck all checkboxes in the table
        var table = $('#gridAutomatedDiscountTableContainer').DataTable();
        var rows = table.rows({ 'search': 'applied' }).nodes();
        if (!this.checked) {
            $('.gcss-button.vinAutoDiscountuncheckBtn').css('display', 'none');
        }
        console.log(this.checked);
        $('.gridAutomatedDiscountTableContainer tr').removeClass('highlighted_tr');
        $('.gridAutomatedDiscountTableContainer tr').css('backgroud-color', 'transparent !important');
        $('.gridAutomatedDiscountTableContainer tr').removeAttr('style');
        $('input[name="auto_discount_include"]', rows).prop('checked', this.checked);
    });

    $('#Include-auto-saved-select-all').on('click', function() {
        // Check/uncheck all checkboxes in the table
        var table = $('#gridAutomatedSavedDiscountTableContainer').DataTable();
        var rows = table.rows({ 'search': 'applied' }).nodes();
        console.log(this.checked);
        $('.gridAutomatedDiscountTableContainer tr').removeClass('highlighted_tr');
        $('.gridAutomatedDiscountTableContainer tr').css('backgroud-color', 'transparent !important');
        $('.gridAutomatedDiscountTableContainer tr').removeAttr('style');
        $('input[name="auto_discount_include"]', rows).prop('checked', this.checked);
    });

    // Handle click on checkbox to set state of "Select all" control
    $('#gridAutomatedDiscountTableContainer tbody').on('change', 'input[type="checkbox"]', function() {
        // If checkbox is not checked
        if (!this.checked) {
            var el = $('#Include-auto-select-all').get(0);
            // If "Select all" control is checked and has 'indeterminate' property
            if (el && el.checked && ('indeterminate' in el)) {
                // Set visual state of "Select all" control 
                // as 'indeterminate'
                //el.indeterminate = true;
                el.checked = false;
            }
        }
    });

    // Handle click on checkbox to set state of "Select all" control
    $('#gridAutomatedSavedDiscountTableContainer tbody').on('change', 'input[type="checkbox"]', function() {
        // If checkbox is not checked
        if (!this.checked) {
            var el = $('#Include-auto-saved-select-all').get(0);
            // If "Select all" control is checked and has 'indeterminate' property
            if (el && el.checked && ('indeterminate' in el)) {
                // Set visual state of "Select all" control 
                // as 'indeterminate'
                //el.indeterminate = true;
                el.checked = false;
            }
        }
    });

    /*
     * document on Functions
     */
    $(document).on('change', '#da_auto_event_make', function() {
        var makeCode = $('#da_auto_event_make').val();
        requestData['makeCode'] = $('#da_auto_event_make').val();
        //$('#makeText,#SavedmakeText').html($('#da_auto_event_make option:selected').text());
        if (makeCode != '') {
            $('#CreatedDiscountBtn,#SavedDiscountBtn').removeClass('disabled');
            changefooter(makeCode);
            // $('#CreatedDiscountBtn').addClass('disabled');
            getModelYear();
        } else {
            $('#da_auto_event_model_year').html('<option value=""> Select Year</option>');
            $('#da_auto_event_vehicle').html('<option value=""> Choose a Vehicle</option>');
            $('#da_auto_event_trim').html('<option value=""> Choose Trim Level</option>');
            //resetTrimMultiSelect();
            $('#CreatedDiscountBtn,#SavedDiscountBtn').removeClass('disabled');
            $('#CreatedDiscountBtn,#SavedDiscountBtn').addClass('disabled');
            resetGridTable();
            /*requestData['modelyear'] = '';
            requestData['model'] = '';
            requestData['trim'] = '';*/
        }
    });

    $(document).on('change', '#da_auto_event_model_year', function() {
        var modelyear = $('#da_auto_event_model_year').val();
        //requestData['modelyear'] = $('#da_auto_event_model_year').val();
        //$('#modelYearText,#SavedmodelYearText').html(requestData['modelyear']);
        if (modelyear != '') {
            //$('#CreatedDiscountBtn').removeClass('disabled');
            //$('#CreatedDiscountBtn').addClass('disabled');
            getVehicle();
        } else {
            $('#da_auto_event_vehicle').html('<option value=""> Choose a Vehicle</option>');
            $('#da_auto_event_trim').html('<option value=""> Choose Trim Level</option>');
            //resetTrimMultiSelect();
            //resetGridTable();
            /*            requestData['modelyear'] = '';
                        requestData['model'] = '';
                        requestData['trim'] = '';*/
        }
    });

    $(document).on('change', '#da_auto_event_vehicle', function() {
        var model = $('#da_auto_event_vehicle').val();
        //requestData['model'] = $('#da_auto_event_vehicle').val();
        if (model != '') {
            //$('#CreatedDiscountBtn').removeClass('disabled');
            //$('#CreatedDiscountBtn').addClass('disabled');
            getTrims();
        } else {
            //$('#trimSelectionText,#SavedtrimSelectionText').css('visibility', 'hidden');
            //$('#vehicleCount').html(0);
            $('#da_auto_event_trim').html('<option value=""> Choose Trim Level</option>');
            //resetTrimMultiSelect();
            $('#CreatedDiscountBtn,#SavedDiscountBtn').removeClass('disabled');
            $('#CreatedDiscountBtn,#SavedDiscountBtn').addClass('disabled');
            //resetGridTable();
            /*requestData['model'] = '';
            requestData['trim'] = '';*/
        }
    });

    $(document).on('change', '#da_auto_event_trim', function() {
        var trim = $('#da_auto_event_trim').val();
        //requestData['trim'] = $('#da_auto_event_trim').val();
        //if (0 < requestData['trim'].length) {
        if (trim != '') {
            /*$('#vehicleCount').html(1);
            $('#trimSelectionText,#SavedtrimSelectionText').css('visibility', 'visible');
            $('#trimText,#SavedtrimText').html(requestData['trim'].toString());
            $('#trimText,#SavedtrimText').text().replace(",", ", ");*/
            //$('div#ms-list-1 button').removeClass('disabled');
        } else {
            /*            $('#trimSelectionText,#SavedtrimSelectionText').css('visibility', 'visible');
                        $('#vehicleCount').html(globalValues.Trims.length);
                        $('#trimText,#SavedtrimText').html(globalValues.Trims.toString());*/
            ///requestData['trim'] = '';
        }
    });

    $(document).on('change', '#da_auto_event_hwmnydiscount', function() {
        requestData['hmdiscounts'] = $('#da_auto_event_hwmnydiscount').val();
        if (requestData['hmdiscounts'] > 0) {
            //$('#discountCount').html(requestData['hmdiscounts']);
            /*var discountHtml="";
            for (i=0;i<requestData['hmdiscounts'];i++){
                var count = i+1;
                discountHtml +='<div class="labelDrop"> Discount Name '+count+'</div><input name="discount_name'+count+'" type="text" id="da_auto_discountTxt'+i+'" placeholder="Discount Name '+count+'" />'
            }
            discountHtml+='<span class="error-check discount-error-check">Please Enter a Discount Names</span>';
            $('.discountListField').html(discountHtml);
            $('.discountListField').removeClass('hidden');*/
            if (requestData['hmdiscounts'] <= 5) {
                $('.discount-error-check').removeClass('active');
                //if (requestData['makeCode'] != '' && requestData['modelyear'] != '' && requestData['model'] != '' && 0 < requestData['trim'].length) {
                if (requestData['makeCode'] != '') {
                    $('#CreatedDiscountBtn').removeClass('disabled');
                }
            } else {
                $('.discount-error-check').removeClass('active');
                $('.discount-error-check').addClass('active');
            }
        } else {
            $('.discount-error-check').removeClass('active');
            $('.discount-error-check').addClass('active');
            //$('.discountListField').addClass('hidden');
            $('#CreatedDiscountBtn').removeClass('disabled');
            $('#CreatedDiscountBtn').addClass('disabled');
        }
    });

    $('.auto-save-and-apply-btn').click(function() {
        var table = $('#gridAutomatedDiscountTableContainer').dataTable();
        table.fnPageChange(0, true);
        confirmapplyautoDiscounttoVehicle();
    });

    $('#discountAutomatedWrapper').on('change', 'input[name="discount_name_switch"]', function() {
        console.log(this.checked);
        var checked = (this.checked) ? 'Active' : 'Inactive';
        var addClass = (this.checked) ? 'ActiveText' : 'DeactiveText';
        var removeClass = (this.checked) ? 'DeactiveText' : 'ActiveText';
        $(this).siblings('.switchLabel').text(checked);
        //$(this).siblings('.switchLabel').removeClass(removeClass);
        //$(this).siblings('.switchLabel').addClass(addClass);
    });

    $(document).on('change', '#gridAutomatedDiscountTableContainer input[name="auto_discount_include"]', function() {
        var $box = $(this).parent().siblings();
        console.log($box);
        if (!this.checked) {
            $(this).parent().siblings().show();
        } else {
            $(this).parent().siblings().hide();
        }
        var check = $(this);
        validatecheckPercentage(check);

    });


    $(document).on('change', 'input[name="discount_name_toggle_switch"]', function() {
        // in the handler, 'this' refers to the box clicked on
        var $box = $(this);
        if ($box.is(":checked")) {
            // the name of the box is retrieved using the .attr() method
            // as it is assumed and expected to be immutable
            var group = "input:checkbox[name='" + $box.attr("name") + "']";
            // the checked state of the group/box on the other hand will change
            // and the current value is retrieved using .prop() method
            $(group).prop("checked", false);
            $box.prop("checked", true);
        } else {
            $box.prop("checked", false);
        }
    });

    /*$(document).on('change', 'input[name=discount_view_type]:radio', function() {
        requestData['discount_view_type'] = this.value;
        console.log(this.value);        
        switchViewTabs(this.value);
        //resetGridTable();
    });*/

    $('#add-rule-discount-model .add-bulk-discount-alert .cancel-btn').click(function() {
        $('#add-rule-discount-model .add-bulk-discount-alert').hide();
    });
    $(document).on('click', '#discount_delete_popup_modal .confirm-discount-delete-btn', function() {
        deleteSavedAutomatedDiscounts();
    });
    $(document).on('click', '#autodiscount_delete_popup_modal .confirm-autodiscount-delete-btn', function() {
        var discount_name = $(deleteCurrentObject).parent('.discounts-rows').find('input[name="discount_name"]').val();
        var filtergroup_id = $(deleteCurrentObject).parent('.discounts-rows').find('input[name="discount_filter_rule"]').val();
        deleteautoDiscount(filtergroup_id, discount_name);
    });

    $(document).on('change', 'input[name=auto_finance_option]:radio', function() {
        requestData['financeoption'] = this.value;
        PaymentCalcEventName = 'onload';
        console.log(this.value);
        if(requestData.makeCode){
            if (switchRuleViewTabs == 2) {
                savedDiscountBtn();
            } else {
                createDiscountBtn();
            }
        }
    });
    $(document).on('click', '.auto-cancel-btn', function() {
        closeInventory();
    });

    $(document).on('mouseenter', '.highlighted_td', function(event) {
        $(this).tooltip({
            content: "<small >Your selected discount exceeds the maximum allowable limit of $5,000 and has been adjusted to meet program requirements</small>",
            track: true
        });
    }).on('mouseleave', '.highlighted_td', function() {
        $(this).css('cursor', 'pointer').attr('title', '');
        $('.highlighted_td *[title]').tooltip('disable');
    });

    $('#confirm-discount-btn').on('click', function() {
        Applydiscount();
    });

    $(document).on('change', '.price_switch', function() {
        $(this).parent().parent().parent().siblings().val(0);
        $(this).parent().parent().parent().parent().siblings().find('input[name=off_percent]').val(0);
    });

    init();
});

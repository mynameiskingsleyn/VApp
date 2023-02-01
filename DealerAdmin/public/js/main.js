/*
 * Global Variables
 */
var globalValues = {};
var DataTableOptions = {
    responsive: true,
    oLanguage: {
        sInfo: "Showing _START_ to _END_ of _TOTAL_ vehicles",
        sLengthMenu: "Show _MENU_ vehicles",
        sInfoEmpty: "Showing 0 to 0 of 0 vehicles",
        sInfoFiltered: "(filtered from _MAX_ total vehicles)",
        sZeroRecords: "No matching vehicles found",
        sEmptyTable: "No vehicles available",
    }
};
var percentageValidationTooltip = 'your selected discount exceeds the maximum allowable limit of $5,000 and has been adjusted to meet program requirements';
var baseURL, baseAPIURL, DealerCode, DealerZipCode, requestData, EmptyDiscountRow, EmptyAutoDiscountRow;
var SearchTabActive = {
    SearchByVehicle: true,
    SearchByVin: false
};
globalValues.tabs = {
    dealerDiscounts: true,
    dealerAutomatedDiscounts: false
};
globalValues.maxAmount5000Apply = true;
EmptyAutoDiscountRow = '';
var paymentCalculatorrequest = [];
var ExcludeVinList = [];
var PaymentCalcEventName = 'onload';
/*
 * General Functions
 */
function numbervalidate(evt) {
    var theEvent = evt || window.event;

    // Handle paste
    if (theEvent.type === 'paste') {
        key = event.clipboardData.getData('text/plain');
    } else {
        // Handle key press
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode(key);
    }
    var regex = /[0-9]|\./;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault) theEvent.preventDefault();
    }
}

function onlyUnique(value, index, self) {
    return self.indexOf(value) === index;
    // usage example:
    /*var a = ['a', 1, 'a', 2, '1'];
    var unique = a.filter( onlyUnique );*/ // returns ['a', 1, 2, '1']
}

function checkValueUndefined(value) {
    if (value == '' || value == null || value == undefined) {
        return false;
    }
    return true;
}

function paymentFormat(nStr) {
    if (nStr == undefined || nStr == '' || nStr == 0) {
        return '0';
    }
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

function calculatePercentage(obtained, total) {
    var percent = obtained * 100 / total;
    console.log(percent);
    return Math.round(percent);
}

function removeArrayElement(element, array) {
    var index = array.indexOf(5);
    if (index > -1) {
        array.splice(index, 1);
    }
    return array;
}

function dataTableOperation(IdContainer) {
    if ($.fn.DataTable.isDataTable(IdContainer)) {
        $(IdContainer).DataTable().clear().destroy();
    }
    $(IdContainer + ' tbody').html('<tr><td colspan="6" align="center">Loading ...</td></tr>');
}

var entityMap = {
  '&': '&amp;',
  //'<': '&lt;',
  //'>': '&gt;',
  '"': '&quot;',
  "'": '&#39;',
 // '/': '&#x2F;',
 // '`': '&#x60;',
 // '=': '&#x3D;'
};
function escapeHtml (string) {
  return String(string).replace(/[&<>"'`=\/]/g, function (s) {
    return entityMap[s];
  });
}


function getTodayDate(max) {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }
    if (max) {
        yyyy = parseInt(yyyy) + 100;
    }
    return yyyy + '-' + mm + '-' + dd;
}

function getCheckboxValues(name) {
    var array = [];
    $.each($("input[name='" + name + "']:checked"), function() {
        array.push($(this).val());
    });
    return array;
}

function formatDateString(date) {
    var date_string = date.split("/");
    return date_string[2] + '-' + date_string[0] + '-' + date_string[1];
}

function formatReverseDateString(date) {
    var date_string = date.split("-");
    return date_string[1] + '/' + date_string[2] + '/' + date_string[0];
}

function findDuplicatesInArray(arr) {
    var sorted_arr = arr.slice().sort(); // You can define the comparing function here.
    // JS by default uses a crappy string compare.
    // (we use slice to clone the array so the
    // original array won't be modified)
    var results = [];
    for (var i = 0; i < sorted_arr.length - 1; i++) {
        if (sorted_arr[i + 1] == sorted_arr[i]) {
            results.push(sorted_arr[i]);
        }
    }
    return results;
}

/*
 *  Populate HTML Functions
 */
function populateDropDown(container, data, key, valueKey) {
    var optionHtml = '<option value=""> Select </option>';
    if (globalValues.tabs.dealerDiscounts) {
        if (container == "#da_event_trim") {
            optionHtml = '';
        } else if (container == "#da_event_vehicle") {
            optionHtml = '<option value=""> Choose a Vehicle</option>';
        } else if (container == "#da_event_model_year") {
            optionHtml = '<option value=""> Select Year</option>';
        }
    }
    if (globalValues.tabs.dealerAutomatedDiscounts) {
        if (container == "#da_auto_event_trim") {
            optionHtml = '<option value=""> Choose a Trim Level</option>';
        } else if (container == "#da_auto_event_vehicle") {
            optionHtml = '<option value=""> Choose a Vehicle</option>';
        } else if (container == "#da_auto_event_model_year") {
            optionHtml = '<option value=""> Select Year</option>';
        }
    }
    console.log('in loop');
    console.log(data);
    var first = data[0];
    if(Array.isArray(first)){
        alert('first item it is array!!!');
        for(var i = 0; i < data.length; i++){
            console.log(data[i]);
            optionHtml += '<option value="' + data[i][0] + '">' + data[i][1] + '</option>';
        }
    }else{
        for (var i = 0; i < data.length; i++) {

            if (key != undefined && valueKey != undefined) {
                optionHtml += '<option value="' + data[i][key] + '">' + data[i][valueKey] + '</option>';
            } else {
                optionHtml += '<option value="' + data[i] + '">' + data[i] + '</option>';
            }
        }
    }



    $(container).html(optionHtml);
}

function populateCheckBox(container, name, data) {
    var listHtml = '';
    console.log('we have data');
    console.log(data);
    for (var key in data) {
        if (data[key]) {
            listHtml += '<li><label class="customCheckBox">';
            if (container == '#da_event_color') {
                listHtml += '<input type="checkbox" name="' + name + '[]" value="' + key + '" class="da_event_chkbox chk_' + name + '" data-type=' + container + '/>';
            } else {
                listHtml += '<input type="checkbox" name="' + name + '[]" value="' + data[key] + '" class="da_event_chkbox chk_' + name + '" data-type=' + container + '/>';
            }
            listHtml += '<span>' + data[key] + '</span>';
            listHtml += '</label></li>';
        }
    }
    $(container).html(listHtml);
}

function populateTable(container, data) {
    dataTableOperation(container);
    $(container + '> tbody').html('<tr><td colspan="6" align="center">Loading ...</td></tr>');
    $('.gridTableWrapper').show();
    var tblContentHtml = '';
    for (var i = 0; i < data.length; i++) {
        var item = data[i];
        console.log(data[i]);
        var switchText = (item['vin_active'] == 0) ? 'Activated' : 'Deactivated';
        var switchTextClass = (item['vin_active'] == 0) ? 'ActiveText' : 'DeActiveText';
        var rowDisable = (item['vin_active'] == 0) ? '' : 'gridRowDisable';
        var checkbox_value = (item['vin_active'] == 0) ? '' : 'checked';
        tblContentHtml += '<tr align="center" class="gridRow ' + rowDisable + '" id="gridRow' + i + '">';
        tblContentHtml += '<td class="text-center">';
        tblContentHtml += '<div>' + item['vin'] + '</div>';
        tblContentHtml += '<span class="switch"><label> <span class="switchLabel ' + switchTextClass + '"> ' + switchText + ' </span><input type="checkbox" name="toggle_Vin" value="" ' + checkbox_value + ' onclick="SearchTableVinActivation(\'gridRow' + i + '\',this,\'' + item['vin'] + '\')"><span class="lever"></span> </label></span>';
        tblContentHtml += '</td>';
        tblContentHtml += '<td>' + item['make'] + '</td>';
         tblContentHtml += '<td>' + item['model'] + '</td>';
        tblContentHtml += '<td>' + item['year'] + '</td>';
        tblContentHtml += '<td>' + item['trim_desc'] + '</td>';
        tblContentHtml += '<td>' + item['msrp_format'] + '</td>';
        tblContentHtml += '<td>';
        if (item.discount_count > 0) {
            tblContentHtml += '<span class="editable-discount-field">';
            tblContentHtml += '<span class="discounted-rate">-' + item['total_amount_format'] + '</span>';
            tblContentHtml += '<div class="editButtonWrapper">';
            tblContentHtml += '<span class="delete-discount" onclick="confirmVinDiscountDelete(\'' + item['vin'] + '\')">DELETE</span>';
            tblContentHtml += '<span class="edit-discount"  data-toggle="modal" data-target="#add-discount-model" onclick="ShowVinNumberinModal(\'' + item['vin'] + '\',' + item['discount_count'] + ',' + item['finance_option'] + ',' + item['msrp'] + ');getDiscountforVin(\'' + item['vin'] + '\');" data-toggle="modal" data-target="#add-discount-model">EDIT</span>';
            tblContentHtml += '<img src="https://d1jougtdqdwy1v.cloudfront.net/dealeradmin/images/small_check.svg"></div></span>';
        } else {
            tblContentHtml += '<span class="add-discount-field themeClr font-weight-bold" onclick="ShowVinNumberinModal(\'' + item['vin'] + '\',' + item['discount_count'] + ',' + item['finance_option'] + ');" data-toggle="modal" data-target="#add-discount-model">ADD<img src="https://d1jougtdqdwy1v.cloudfront.net/dealeradmin/images/add.svg"></span>';
        }
        tblContentHtml += '<div class="viewDetailsWrapper"><span class="view-details"><a class="arrow-right" href="javascript:void(0);" id="payment-calculator-btn" onclick="OpenPaymentCalculator(\'' + item['vin'] + '\',\'' + item['make'] + '\',\'' + item['year'] + '\',\'' + item['model'] + '\',\'' + item['trim_desc'] + '\',\'' + item['msrp'] + '\',\'' + item['msrp_format'] + '\',\'' + item['total_amount'] + '\',\'' + item['total_amount_format'] + '\');">View Price Details</a></span></div>';
        tblContentHtml += '</td>';
        tblContentHtml += '</tr>';
    }
    DataTableOptions = {
        responsive: true,
        oLanguage: {
            sInfo: "Showing _START_ to _END_ of _TOTAL_ vehicles",
            sLengthMenu: "Show _MENU_ vehicles",
            sInfoEmpty: "Showing 0 to 0 of 0 vehicles",
            sInfoFiltered: "(filtered from _MAX_ total vehicles)",
            sZeroRecords: "No matching vehicles found",
            sEmptyTable: "No vehicles available",
        }
    };
    $(container + '> tbody').html(tblContentHtml);
    $(container).dataTable(DataTableOptions);

    var datatable = $(container).DataTable();
    for (var i = 0; i < data.length; i++) {
        var item = data[i];
        if (item['total_amount'] >= 5000) {
            trid = 'gridRow' + i;
            highlightvin(datatable, trid, i);
        }
        console.log(i);
    }
}

function populateDiscountTable(container, data) {
    dataTableOperation(container);
    $(container + '> tbody').html('<tr><td colspan="6" align="center">Loading ...</td></tr>');
    var tblContentHtml = '';
    for (var i = 0; i < data.length; i++) {
        var item = data[i];
        var has_discount_img = (item['has_discount']) ? 'https://d1jougtdqdwy1v.cloudfront.net/dealeradmin/images/small_check.svg' : 'https://d1jougtdqdwy1v.cloudfront.net/dealeradmin/images/close.svg';
        var rowDisable = '';
        if (globalValues.tabs.dealerAutomatedDiscounts) {
            rowDisable = (item['vin_active'] == 0) ? '' : 'gridRowDisable';
        }
        tblContentHtml += '<tr align="center" class="gridRow discountRow ' + rowDisable + '" id="discountRow' + i + '" data-msrp-value="' + item['msrp'] + '">';
        if (globalValues.tabs.dealerAutomatedDiscounts) {
            var switchText = (item['vin_active'] == 0) ? 'Activated' : 'Deactivated';
            var switchTextClass = (item['vin_active'] == 0) ? 'ActiveText' : 'DeActiveText';
            var checkbox_value = (item['vin_active'] == 0) ? '' : 'checked';
            tblContentHtml += '<td class="text-center">';
            tblContentHtml += '<div>' + item['vin'] + '</div>';
            tblContentHtml += '<span class="switch"><label> <span class="switchLabel ' + switchTextClass + '"> ' + switchText + ' </span><input type="checkbox" name="toggle_Vin" value="" ' + checkbox_value + ' onclick="SearchTableVinActivation(\'discountRow' + i + '\',this,\'' + item['vin'] + '\')"><span class="lever"></span> </label></span>';
            tblContentHtml += '</td>';
        } else {
            tblContentHtml += '<td>' + item['vin'] + '</td>';
        }
        tblContentHtml += '<td>' + item['make'] + '</td>';
         tblContentHtml += '<td>' + item['model'] + '</td>';
        tblContentHtml += '<td>' + item['year'] + '</td>';
        tblContentHtml += '<td>' + item['trim_desc'] + '</td>';
        if (globalValues.tabs.dealerAutomatedDiscounts) {
            tblContentHtml += '<td>' + item['msrp_format'] + '</td>';
            tblContentHtml += '<td discount-msrp-td="' + item['total_amount'] + '"><span class="discount-msrp-span">-' + item['total_amount_format'] + '</span><div class="viewDetailsWrapper"><span class="view-details"><a class="arrow-right" href="javascript:void(0);" id="payment-calculator-btn" onclick="OpenPaymentCalculator(\'' + item['vin'] + '\',\'' + item['make'] + '\',\'' + item['year'] + '\',\'' + item['model'] + '\',\'' + item['trim_desc'] + '\',\'' + item['msrp'] + '\',\'' + item['msrp_format'] + '\',\'' + item['total_amount'] + '\',\'' + item['total_amount_format'] + '\');">View Price Details</a></span></div></td>';
        }
        tblContentHtml += '<td><span class="has-discounts-field"><img src="' + has_discount_img + '"></span></td>';
        if (globalValues.tabs.dealerDiscounts) {
            tblContentHtml += '<td><div class="include-discount"><label class="customCheckBox "><input type="checkbox" class="bulk_discount_include" name="bulk_discount_include" value="' + item['vin'] + '"><span></span></label></div></td>';
        }
        if (globalValues.tabs.dealerAutomatedDiscounts) {
            var include = 'checked'
            var includeEdit = '';
            if (0 < item['total_amount']) {
                var pos = ExcludeVinList.indexOf(item['vin']);
                if (-1 < pos) {
                    include = '';
                    includeEdit = 'style="display:inline-block;"';
                }
            }
            tblContentHtml += '<td><div class="include-discount"><label class="customCheckBox "><input type="checkbox" name="auto_discount_include" class="auto_discount_include" value="' + item['vin'] + '" ' + include + '><span></span></label><button class="btn btn-sm gcss-button vinAutoDiscountuncheckBtn" onclick="EditUncheckRuleDiscount(this,\'' + item['vin'] + '\',\'' + item['msrp'] + '\')" ' + includeEdit + '>Edit</button></div></td>';
        }
        tblContentHtml += '</tr>';
    }
    $(container + '> tbody').html(tblContentHtml);
    var columnNo = 6;
    if (globalValues.tabs.dealerAutomatedDiscounts) {
        columnNo = 7;
    }
    let tableOptions = DataTableOptions;
    tableOptions['columnDefs'] = [{
        'targets': columnNo,
        'searchable': false,
        'orderable': false,
        'className': 'dt-body-center'
    }];
    $(container).dataTable(tableOptions);
    //console.log('container',container);
    gridloadcheckpercentageValidation(container);
    unchecktr(container);
}

function populateDiscount(container, data, request) {
    $('#ModalDiscountVin').html(request.VinNumber);
    var tblContentHtml = '';
    if (data.length == 0) {
        tblContentHtml = EmptyDiscountRow;
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
            var deletedOnclick = ' onclick="confirmSingleDiscountDelete(\'' + data[i].name_of_discount + '\',\'' + data[i].uuid + '\');"';;
            if (container == '#BulkDiscountRowWrapper') {
                deletedOnclick = ' onclick="confirmBulkDiscountDelete(\'' + data[i].name_of_discount + '\');"';
            };
            tblContentHtml += '<div class="discounts-rows">';
            tblContentHtml += '<div class="discount-details">';
            tblContentHtml += '<input type="hidden" name="discount_uuid" value="' + data[i].uuid + '">';
            tblContentHtml += '<div class="discount-space">';
            tblContentHtml += '<span class="discount-name-field"><label>Discount Name</label><input type="text" name="discount_name" value="' + data[i].name_of_discount + '" maxlength="60"><span class="discount-name-error bulk-error">Please enter discount name</span><!-- <span class="discount-name-edit-field">Edit Name</span> --></span>';
            tblContentHtml += '<span class="discount-flat-rate"> <label>Flat Rate <span class="switch"><label><input type="checkbox" class="price_switch" name="price_switch" ' + price_switch_text + '><span class="lever"></span></label></span></label>';
            tblContentHtml += '<input type="text" name="flat_rate" class="flat_rate" value="' + flat_rate + '"' + flate_disable + ' maxlength="4" onkeypress="numbervalidate(event)"><span class="flat-rate-error bulk-error">Please enter discount amount</span> </span>';
            tblContentHtml += '<span class="or-text">or</span>';
            tblContentHtml += '<span class="off-percent"><label class="off-percent-label">% Off</label><input type="text" name="off_percent" class="off_percent" value="' + percent_offer + '" ' + percent_disable + ' maxlength="2" onkeypress="numbervalidate(event)"><span class="off_percent_error bulk-error">Please enter % value for discount</span></span>';
            tblContentHtml += '<span class="discount-date-field"><label class="discount-start-date-label">Start Date</label><input type="date" name="discount_start_date" class="start_datepicker discount_start_date" value="' + formatDateString(data[i].discount_start_date) + '" min="' + globalValues.todayDate + '" max="' + globalValues.maxDate + '"><span class="discount_start_date_error bulk-error">Please enter Start Date</span></span>';
            tblContentHtml += '<span class="discount-date-field"><label class="discount-end-date-label">End Date</label><input type="date" name="discount_end_date" class="end_datepicker discount_end_date" value="' + formatDateString(data[i].discount_end_date) + '" min="' + globalValues.todayDate + '" max="' + globalValues.maxDate + '"><span class="discount_end_date_error bulk-error">Please enter End Date</span></span>';
            tblContentHtml += '</div>';
            tblContentHtml += '<div class="saved-discounts"><label class="customCheckBox"><input type="checkbox" name="saved_discount" ' + saved_discount + '><span>ADD to Saved Discounts (Only 5 Allowed)</span></label></div>';
            tblContentHtml += '</div>';
            tblContentHtml += '<div class="discount-delete-field" ' + deletedOnclick + '><span><i aria-hidden="true" class="fa fa-trash-alt"></i></span></div>';
            tblContentHtml += '</div>';
        }
    }
    $(container).html(tblContentHtml);
}

function populateSavedDiscount(container, data, request) {
    $('#saveddiscountAutomatedContainer #no_saved_discount').hide();
    $('#savedDiscountCount').html(data.length)
    $('#savedDiscountremCount').html((5 - parseInt(data.length)));
    var tblContentHtml = '';
    if (data.length == 0) {
        $('#saveddiscountAutomatedContainer .discounts-button').hide();
        $('#saveddiscountAutomatedContainer #no_saved_discount').show();
        tblContentHtml = '';
    } else {
        $('#saveddiscountAutomatedContainer .discounts-button').show();
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
            var deletedOnclick = ' onclick="confirmSavedDiscountDelete(\'' + data[i].name_of_discount + '\',\'' + data[i].uuid + '\');"';;
            tblContentHtml += '<div class="discounts-rows">';
            tblContentHtml += '<div class="discount-details">';
            tblContentHtml += '<input type="hidden" name="discount_uuid" value="' + data[i].uuid + '"><input type="hidden" name="discount_filter_rule" value="' + data[i].filtergroup_id + '"><input type="hidden" name="discount_id" value="' + data[i].discount_id + '">';
            tblContentHtml += '<div class="discount-space">';
            tblContentHtml += '<span class="discount-name-field"><label>Discount Name</label><input type="text" name="discount_name" value="' + data[i].name_of_discount + '" maxlength="60" readonly><span class="discount-name-error bulk-error">Please enter discount name</span><!-- <span class="discount-name-edit-field">Edit Name</span> --></span>';
            tblContentHtml += '<span class="discount-flat-rate"> <label>Flat Rate <span class="switch"><label><input type="checkbox" name="price_switch" ' + price_switch_text + '><span class="lever"></span></label></span></label>';
            tblContentHtml += '<input type="text" class="flat_rate" name="flat_rate" value="' + flat_rate + '"' + flate_disable + ' maxlength="4" onkeypress="numbervalidate(event)"><span class="flat-rate-error bulk-error">Please enter discount amount</span> </span>';
            tblContentHtml += '<span class="or-text">or</span>';
            tblContentHtml += '<span class="off-percent"><label class="off-percent-label">% Off</label><input type="text" class="off_percent offpercentage" name="off_percent" value="' + percent_offer + '" ' + percent_disable + ' maxlength="2" onkeypress="numbervalidate(event)"><span class="off_percent_error bulk-error">Please enter % value for discount</span></span>';
            tblContentHtml += '<span class="discount-date-field"><label class="discount-start-date-label">Start Date</label><input type="date" name="discount_start_date" class="start_datepicker discount_start_date" value="' + formatDateString(data[i].discount_start_date) + '" min="' + globalValues.todayDate + '" max="' + globalValues.maxDate + '" ><span class="discount_start_date_error bulk-error">Please enter Start Date</span></span>';
            tblContentHtml += '<span class="discount-date-field"><label class="discount-end-date-label">End Date</label><input type="date" name="discount_end_date" class="end_datepicker discount_end_date" value="' + formatDateString(data[i].discount_end_date) + '" min="' + globalValues.todayDate + '" max="' + globalValues.maxDate + '" ><span class="discount_end_date_error bulk-error">Please enter End Date</span></span>';
            tblContentHtml += '<span class="discount-inventory-field discount-date-field">  <label class="discount-inventory-label">Inventory</label><select class="discount-inventory" name="discount-inventory" id="da_auto_discount_inventory" disabled><option value="New"> New </option></select></span>';
            tblContentHtml += '</div>';
            tblContentHtml += '<div class="saved-discounts"><label class="customCheckBox"><input type="checkbox" name="saved_discount" ' + saved_discount + ' onclick="return false;"><span>ADD to Saved Discounts (Only 5 Allowed)</span></label></div><span class="discount-over-field">This Discount is over and above the FCA incentives</span>';
            tblContentHtml += '<div class="discounts-button text-right"> <button class="discount_buttons duplicate_discount_btn" style="visibility:hidden">DUPLICATE DISCOUNT</button></div></div>';
            tblContentHtml += '<div class="discount-delete-field" ' + deletedOnclick + '><span><i aria-hidden="true" class="fa fa-trash-alt"></i></span></div>';
            tblContentHtml += '</div>';
        }
    }
    $(container).html(tblContentHtml);
}

function getIncludeVinRowsData(container) {
    var discount = [];
    var table = $(container).DataTable();
    var rows = table.rows({ 'search': 'applied' }).nodes();
    $("input:checked", rows).each(function() {
        var vinnumber = $(this).val();
        if (vinnumber != '' && vinnumber != null && vinnumber != undefined) {
            console.log(vinnumber);
            discount.push(vinnumber);
        }
    });
    return discount;
}

function getExcludeVinRowsData(container) {
    var discount = [];
    var table = $(container).DataTable();
    var rows = table.rows({ 'search': 'applied' }).nodes();
    $("input:checkbox:not(:checked)", rows).each(function() {
        var vinnumber = $(this).val();
        if (vinnumber != '' && vinnumber != null && vinnumber != undefined) {
            console.log(vinnumber);
            discount.push(vinnumber);
        }
    });
    return discount;
}

function getDiscountNames(container) {
    var discount = [];
    $(container + " .discounts-rows").each(function(index) {
        var name_of_discount = $(this).find('input[name="discount_name"]').val();
        name_of_discount = name_of_discount.toLowerCase();
        if (globalValues.tabs.dealerAutomatedDiscounts) {
            var discount_include = $(this).find('input[name="rule_discount_saved"]:checked').val();
            if (container != '#add-rule-discount-model #RuleDiscountRowWrapper' && container != '#saveddiscountAutomatedContainer') {
                if (discount_include == 'on') {
                    discount.push(name_of_discount.trim());
                }
            } else {
                discount.push(name_of_discount.trim());
            }
        } else {
            discount.push(name_of_discount.trim());
        }
    });
    return discount;
}

function getDiscountRowsData(container) {
    var discount = [];
    $(container + " .discounts-rows").each(function(index) {
        //console.log(this);
        var data = {};
        data['name_of_discount'] = $(this).find('input[name="discount_name"]').val();
        var price_switch = $(this).find('input[name="price_switch"]:checked').val();
        data['flat_rate'] = $(this).find('input[name="flat_rate"]').val();
        data['percent_offer'] = $(this).find('input[name="off_percent"]').val();
        data['discount_start_date'] = $(this).find('input[name="discount_start_date"]').val();
        data['discount_start_date'] = $(this).find('input[name="discount_start_date"]').val();
        data['discount_end_date'] = $(this).find('input[name="discount_end_date"]').val();
        data['inventory'] = $(this).find('select[name="discount-inventory"]').val();
        var uuid = $(this).find('input[name="discount_uuid"]').val();
        var discount_id = $(this).find('input[name="discount_id"]').val();
        var saved_discount = $(this).find('input[name="saved_discount"]:checked').val();
        var discount_filter_rule = $(this).find('input[name="discount_filter_rule"]').val();
        if (price_switch == 'on') {
            data['percent_offer'] = 0;
        } else {
            data['flat_rate'] = 0;
        }
        if (!(uuid == null || uuid == '' || uuid == 0 || uuid == undefined)) {
            data['uuid'] = uuid;
        }
        if (!(discount_id == null || discount_id == '' || discount_id == 0 || discount_id == undefined)) {
            data['discount_id'] = discount_id;
        }
        if (!(discount_filter_rule == null || discount_filter_rule == '' || discount_filter_rule == 0 || discount_filter_rule == undefined)) {
            data['discount_filter_id'] = discount_filter_rule;
        }
        data['saved_discount'] = (saved_discount == 'on') ? 1 : 0;
        if (globalValues.tabs.dealerAutomatedDiscounts) {
            console.log($(this).find('input[name="rule_discount_saved"]:checked'));
            //console.log( $(this).find('input[name="discount_name_toggle_switch"]:checked'));
            var discount_include = $(this).find('input[name="rule_discount_saved"]:checked').val();
            var discount_name_toggle_switch = $(this).find('input[name="discount_name_toggle_switch"]:checked').val();
            data['is_active'] = 0;
            if (container != '#add-rule-discount-model #RuleDiscountRowWrapper' && container != '#saveddiscountAutomatedContainer') {
                if (discount_name_toggle_switch == 'on') {
                    data['is_active'] = 1;
                }
                if (discount_include == 'on') {
                    discount.push(data);
                }
            } else {
                discount.push(data);
            }
        } else {
            discount.push(data);
        }
        console.log('DiscountRow: ' + index + ' ==> ', data);
    });
    return discount;
}

/*
 *  Ajax Call Functions
 */
function ajaxCall(url, requestType, data) {
    if (globalValues.tabs.dealerDiscounts) {
        $('#discountStatusCodeError,#BulkStatusCodeError,#RulediscountStatusCodeError').css('display', 'none');
    }

    $(".dropDownPanel *").prop('disabled', true);
    var APIURL = baseAPIURL + url;
    if ('ssologout' == url) {
        APIURL = baseURL + url;
    }
    loadingIndicator(url);
    console.log('Ajax Call::', APIURL, requestType, data);
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Frame-Options': 'sameorigin',
            'X-Content-Type-Options': 'nosniff'
        },
        url: APIURL,
        type: requestType,
        data: data,
        datatype: 'json',
        success: function(response) {
            $(".dropDownPanel *").prop('disabled', false);
            if (response && response.StatusCode == 1000) {
                successResponseHandler(url, response, data);
            } else {
                errorStatusCodeHandler(url, response, data);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            $(".dropDownPanel *").prop('disabled', false);
            console.log(textStatus)
        }
    });
}

function loadingIndicator(url) {
    switch (url) {
        case "getModelYear":
            if (globalValues.tabs.dealerDiscounts) {
                $('.da_event_model_year').removeClass('after-load-value');
                $('.da_event_model_year').addClass('processing-value');
                $('#da_event_vehicle').html('<option value=""> Choose a Vehicle</option>');
                $('#da_event_drive_type,#da_event_color,#da_event_engine_desc,#da_event_transmission').html('');
                $('div#ms-list-1 button').addClass('disabled');
                 $('#ms-list-1 button span').html('Choose Trim Level');
            }
            if (globalValues.tabs.dealerAutomatedDiscounts) {
                $('.da_auto_event_model_year').removeClass('after-load-value');
                $('.da_auto_event_model_year').addClass('processing-value');
                $('#da_auto_event_vehicle').html('<option value=""> Choose a Vehicle</option>');
                $('#da_auto_event_trim').html('<option value=""> Choose Trim Level</option>');
            }
            break;
        case "getVehicleSelection":
            if (globalValues.tabs.dealerDiscounts) {
                $('#da_event_vehicle').html('<option value=""> Choose a Vehicle</option>');
                $('#da_event_drive_type,#da_event_color,#da_event_engine_desc,#da_event_transmission').html('');
                $('.da_event_vehicle').removeClass('after-load-value');
                $('.da_event_vehicle').addClass('processing-value');
                $('#ms-list-1 button span').html('Choose Trim Level');
                $('div#ms-list-1 button').addClass('disabled');
            }
            if (globalValues.tabs.dealerAutomatedDiscounts) {
                $('#da_auto_event_vehicle').html('<option value=""> Choose a Vehicle</option>');
                $('#da_auto_event_trim').html('<option value=""> Choose Trim Level</option>');
                $('.da_auto_event_vehicle').removeClass('after-load-value');
                $('.da_auto_event_vehicle').addClass('processing-value');
            }
            break;
        case "FilterTrimSelection":
            if (globalValues.tabs.dealerDiscounts) {
                $('#da_event_drive_type,#da_event_color,#da_event_engine_desc,#da_event_transmission').html('');
                $('.da_event_trim').removeClass('after-load-value');
                $('.da_event_trim,#ms-list-1 button').addClass('processing-value');
                $('#ms-list-1 button span').css('visibility', 'hidden');
                $('div#ms-list-1 button').addClass('disabled');
            }
            if (globalValues.tabs.dealerAutomatedDiscounts) {
                $('.da_auto_event_trim').removeClass('after-load-value');
                $('.da_auto_event_trim').addClass('processing-value');
            }
            break;
        case "FilterMsrpSelection":
            $('#da_event_drive_type,#da_event_color,#da_event_engine_desc,#da_event_transmission').html('');
            $('div#ms-list-1 button').addClass('disabled');
            break;
        case "SearchByAttributes":
        case "SearchByVIN":
            $('.pleaseSelect').hide();
            $('.gridTableWrapper').show().addClass('processing');
            break;
        case 'vin-activation':
            $('.gridTableWrapper').addClass('processing');
            break;
        case "bulk-discount/list":
        case "bulk-discount/remove":
        case "bulk-discount/add":
        case "clear-all-discount":
            $('#BulkDiscountRowWrapper').addClass('processing');
            break;
        case "discount/add":
        case "discount/edit/" + requestData.DealerCode:
        case "discount/delete/" + requestData.uuid:
            $('#add-discount-model .modal-body').addClass('processing');
            break;

        case "rule-discount/delete/" + requestData.uuid:
            $('#saveddiscountAutomatedContainer').addClass('processing');
            break;
        case 'rule-discount/vin-delete/' + requestData.uuid:
            $('#add-rule-discount-model .modal-body').addClass('processing');
            break;
        case 'paymentcalc':
            $('#payment-calculator-modal .modal-content').addClass('processing');
            break;
        case 'rule-discount/add':
            $('#vehicleDiscountWrapper').addClass('processing');
            break;
        case 'rule-single-discount/add':
            $('#add-rule-discount-model .modal-body').addClass('processing');
            break;
        case "rule-discount/vehicles":
            $('.gridAutomatedDiscountTableWrapper').addClass('processing');
            break;
        case "rule-discount/saved-list":
            $('#saveddiscountAutomatedContainer').addClass('processing');
            break;
        case "rule-discount/list":
        case "rule-discount/delete":
            $('#vehicleDiscountWrapper').addClass('processing');
            break;
    }
}

function successResponseHandler(url, response, request) {
    //debugger;
    var data = [];
    var idContainer = '';
    var classContainer = '';
    switch (url) {
        case "getAllMakes":
            data = response['data']['make'];
            if (globalValues.tabs.dealerDiscounts) {
                idContainer = "#da_event_make";
            }
            if (globalValues.tabs.dealerAutomatedDiscounts) {
                idContainer = "#da_auto_event_make";
            }
            $(idContainer).removeClass('processing-value');
            $(idContainer).addClass('after-load-value');
            populateDropDown(idContainer, data, 'MakeCode', 'MakeName');
            break;
        case "getModelYear":
            data = response['data']['ModelYears'];
            if (globalValues.tabs.dealerDiscounts) {
                idContainer = "#da_event_model_year";
                classContainer = '.da_event_model_year';
            }
            if (globalValues.tabs.dealerAutomatedDiscounts) {
                idContainer = "#da_auto_event_model_year";
                classContainer = '.da_auto_event_model_year';
            }
            populateDropDown(idContainer, data);
            $(classContainer).removeClass('processing-value');
            $(classContainer).addClass('after-load-value');
            break;
        case "getVehicleSelection":
            data = response['data']['Models'];
            if (globalValues.tabs.dealerDiscounts) {
                idContainer = "#da_event_vehicle";
                classContainer = '.da_event_vehicle';
            }
            if (globalValues.tabs.dealerAutomatedDiscounts) {
                idContainer = "#da_auto_event_vehicle";
                classContainer = '.da_auto_event_vehicle';
            }
            populateDropDown(idContainer, data);
            $(classContainer).removeClass('processing-value');
            $(classContainer).addClass('after-load-value');
            console.log('idContainer',idContainer);
            $('select[multiple]' + idContainer).multiselect('reload');
            break;
        case "FilterTrimSelection":
            data = response['data']['Trims'];
            if (globalValues.tabs.dealerDiscounts) {
                idContainer = "#da_event_trim";
                classContainer = '.da_event_trim';
            }
            if (globalValues.tabs.dealerAutomatedDiscounts) {
                idContainer = "#da_auto_event_trim";
                classContainer = '.da_auto_event_trim';
                globalValues.Trims = data;
            }
            var trims = [];
            console.log('original data below');
            console.log(data);
            Object.keys(data).forEach(function(key) {
                var one = []
                one.push(key);
                one.push(data[key]);
                console.log("one is under!!");
                console.log(one);
                trims.push(one);
            });
            console.log(trims);
            console.log("right before dropDown population");
            console.log(data);
            alert('working for now');
            populateDropDown(idContainer, trims);
            if (globalValues.tabs.dealerDiscounts) {
                $('select[multiple]' + idContainer).multiselect('reload');
                $(classContainer + ',#ms-list-1 button').removeClass('processing-value');
                $('#ms-list-1 button span').css('visibility', 'visible');
                $('div#ms-list-1 button').removeClass('disabled');
            }
            if (globalValues.tabs.dealerAutomatedDiscounts) {
                $(classContainer).removeClass('processing-value');
            }
            $(classContainer).addClass('after-load-value');
            break;
        case "FilterMsrpSelection":
            data = response['data']['Msrp'];
            requestData['msrp_lowest'] = data['Lowest'];
            requestData['msrp_highest'] = data['Highest'];
            $('#slider-range').slider("option", "max", data['Highest']);
            $('#slider-range').slider("option", "min", data['Lowest']);
            $('#slider-range').slider("option", "values", [data['Lowest'], data['Highest']]);
            $("#amount").val("$" + data['Lowest'] + " - $" + data['Highest']);
            break;
        case "FilterSecondarySelection":
            data = response['data']['Attributes'];
            populateCheckBox('#da_event_drive_type', 'drive_type', data['drive_type']);
            populateCheckBox('#da_event_color', 'interior_colour_desc', data['exterior_color_code']);
            populateCheckBox('#da_event_engine_desc', 'eng_desc', data['eng_desc']);
            populateCheckBox('#da_event_transmission', 'transmission_desc', data['transmission_desc']);
            break;
        case "SearchByAttributes":
            data = response['vehicles'];
            populateTable('#gridTableContainer', data['list']);
            $('.gridTableWrapper').removeClass('processing');
            break;
        case "SearchByVIN":
            data = response['vehicles'];
            populateTable('#gridTableContainer', data);
            $('.gridTableWrapper').removeClass('processing');
            break;
        case "bulk-discount/vehicles":
            data = response['vehicles'];
            if (globalValues.tabs.dealerDiscounts) {
                idContainer = "#gridDiscountTableContainer";
                classContainer = '#add-bulk-discount-model .modal-body';
            }
            if (globalValues.tabs.dealerAutomatedDiscounts) {
                idContainer = "#gridAutomatedDiscountTableContainer";
                classContainer = '.gridAutomatedDiscountTableWrapper';
            }
            populateDiscountTable(idContainer, data);
            $(classContainer).removeClass('processing');
            break;
        case "rule-discount/vehicles":
            data = response['vehicles'];
            if (globalValues.tabs.dealerDiscounts) {
                idContainer = "#gridDiscountTableContainer";
                classContainer = '#add-bulk-discount-model .modal-body';
            }
            if (globalValues.tabs.dealerAutomatedDiscounts) {
                $('#Include-auto-select-all').prop('checked', true);
                idContainer = ".gridAutomatedDiscountTableContainer";
                classContainer = '.gridAutomatedDiscountTableWrapper';
            }
            populateDiscountTable(idContainer, data);
            $(classContainer).removeClass('processing');
            break;
        case "discount/list":
            data = response['discounts'];
            populateDiscount('#DiscountRowWrapper', data, request);
            $('#add-discount-model .modal-body').removeClass('processing');
            break;
        case "bulk-discount/list":
            data = response['discounts'];
            populateDiscount('#BulkDiscountRowWrapper', data, request);
            $('#BulkDiscountRowWrapper').removeClass('processing');
            preventSingleDiscountDeletion('#BulkDiscountRowWrapper');
            break;
        case "rule-discount/list":
            data = response['discounts'];
            globalValues.auto_discount_count = response['counts'];
            if (0 == data.length) {
                $('.gridAutomatedDiscountTableWrapper').hide();
            }
            populateRuleVehicleDiscount('#discountAutomatedWrapper', data, request);
            $('#vehicleDiscountWrapper').removeClass('processing');
            break;
        case "clear-all-discount":
            $('#BulkDiscountRowWrapper').removeClass('processing');
            getBulkDiscountVehicles();
            $('#add-bulk-discount-model').modal('hide');
            if (requestData['makeCode'] != '' && requestData['modelyear'] != '' && requestData['model'] != '' && 0 < requestData['trim'].length) {
                SearchByAttributes();
            }
            break;
        case "bulk-discount/remove":
            getBulkDiscountList();
            $('#BulkDiscountRowWrapper').removeClass('processing');
            break;
        case "discount/add":
        case "discount/edit/" + requestData.DealerCode:
            $('#add-discount-model .modal-body').removeClass('processing');
        case "bulk-discount/add":
            $('#BulkDiscountRowWrapper,#add-bulk-discount-model .modal-body').removeClass('processing');
            $('#add-discount-model,#add-bulk-discount-model').modal('hide');
            handleTabs();
            break;
        case "discount/delete/" + requestData.uuid:
            $('#add-discount-model .modal-body').removeClass('processing');
            $('#add-discount-model').modal('hide');
            handleTabs();
            break;
        case "vin-discount/delete/" + requestData.DealerCode + '/' + requestData.VinNumber + '/' + requestData.financeoption:
            // case "vin-activation":
            handleTabs();
            break;
        case 'paymentcalc':
            $('#payment-calculator-modal .modal-content').removeClass('processing');
            console.log(response);
            populatePaymentCalculatorDetails(response);
            break;
        case 'rule-discount/add':
            data = response['Discount'];
            ExcludeVinList = request['ExcludeVinList'];
            var message = "<h6>Success!</h6><p>Discounts applied successfully!</p>";
            $('#auto_discount_success_modal .modal-body').html(message);
            $('#auto_discount_success_modal').modal('show');
            $('#vehicleDiscountWrapper').removeClass('processing');
            processAddedAutomatedDiscounts(data);
            getDiscountVehicles();
            console.log(response);
            break;
        case 'rule-single-discount/add':
            data = response['Discount'];
            processAddedAutomatedDiscounts(data);
            $('#add-rule-discount-model .modal-body').removeClass('processing');
            $('#add-rule-discount-model').modal('hide');
            getDiscountVehicles();
            break;
        case 'rule-discount/saved-add':
            getDiscountVehicles();
            break;
        case "rule-discount/saved-list":
            data = response['discounts'];
            if (0 == data.length) {
                $('.gridAutomatedDiscountTableWrapper').hide();
            }
            datafilterGroup = response['filterGroup'];
            globalValues.FilterGroupId = datafilterGroup['id'];
            $('#saveddiscountAutomatedContainer').removeClass('processing');
            $('#Include-auto-saved-select-all').prop('checked', true);
            populateSavedDiscount('#saveddiscountAutomatedWrapper', data, request)
            break;
        case "rule-discount/delete/" + requestData.uuid:
            getSavedDiscounts();
            $('#saveddiscountAutomatedContainer').removeClass('processing');
            break;
        case 'rule-discount/vin-delete/' + requestData.uuid:
            $('#add-rule-discount-model .modal-body').removeClass('processing');
            getDiscountVehicles();
            var currentLength = $('#RuleDiscountRowWrapper .discounts-rows').length;
            if (0 == currentLength) {
                $('#add-rule-discount-model').modal('hide');
            }
            break;
        case "rule-discount/delete":
            $('#vehicleDiscountWrapper').removeClass('processing');
            getRuleDiscounts();
            getDiscountVehicles();
            break;
        case "filter-group/add":
            data = response['filterGroup'];
            globalValues.FilterGroupId = data['id'];
            ExcludeVinList = [];
            if (data['excludevins'] != '' && data['excludevins'] != null && data['excludevins'] != undefined) {
                ExcludeVinList = data['excludevins'].split(",");
            }
            break;
        case "rule-discount/vin-list":
            data = response['discounts'];
            populateEditUncheckRuleDiscount(data);
            break;
        case 'vin-activation':
            $('.gridTableWrapper').removeClass('processing');
            break;
        case 'feedback':
            element = document.getElementById("feedbackform");
            element.classList.remove("bulk-error");
             $('#feedbackform')[0].reset();
             //$('#place_fedbackname').html('NAME<span>*</span>');
             $('#feedback_success_modal').modal('show');

            break;
    }
}
$(document).on('click', '#feedback_ok', function() {
    window.location.reload();
});
function errorStatusCodeHandler(url, response, request) {
    var data = [];
    switch (url) {
        case "discount/add":
        case "discount/edit/" + requestData.DealerCode:
        case "discount/delete/" + requestData.uuid:
            $('#add-discount-model .modal-body').removeClass('processing');
            $('#discountStatusCodeError').css('display', 'block');
            $('#discountStatusCodeError').html(response['Message']);
            break;
        case "clear-all-discount":
            $('#BulkDiscountRowWrapper').removeClass('processing');
            break;
        case "bulk-discount/add":
            $('#BulkDiscountRowWrapper,#add-bulk-discount-model .modal-body').removeClass('processing');
            $('#BulkStatusCodeError').css('display', 'block');
            $('#BulkStatusCodeError').html(response['Message']);
            break;
        case "SearchByVIN":
            $('.gridTableWrapper').hide().removeClass('processing');
            $('.search-by-vin .vin-error-check').addClass('active');
            //$('.search-by-vin .vin-error-check').html("Invalid Vin Number");
            $('.pleaseSelect').show();
        case "SearchByAttributes":
            $('.gridTableWrapper').removeClass('processing');
            break;
        case 'paymentcalc':
            $('#payment-calculator-modal .modal-content').removeClass('processing');
            break;
        case 'rule-discount/add':
            var message = '<h6>Error!</h6><p>' + ((response['Message'] == undefined) ? '' : response['Message']) + '</p>';
            $('#auto_discount_success_modal .modal-body').html(message);
            $('#auto_discount_success_modal').modal('show');
            $('#vehicleDiscountWrapper').removeClass('processing');
            break;
        case "rule-discount/vehicles":
            $('.gridAutomatedDiscountTableWrapper').removeClass('processing');
            var message = '<h6>Error!</h6><p>' + ((response['Message'] == undefined) ? '' : response['Message']) + '</p>';
            $('#auto_discount_success_modal .modal-body').html(message);
            $('#auto_discount_success_modal').modal('show');
            break;
        case "rule-discount/saved-list":
            $('#saveddiscountAutomatedContainer').removeClass('processing');
            var message = '<h6>Error!</h6><p>' + ((response['Message'] == undefined) ? '' : response['Message']) + '</p>';
            $('#auto_discount_success_modal .modal-body').html(message);
            $('#auto_discount_success_modal').modal('show');
            break;
        case "rule-discount/list":
        case "rule-discount/delete":
            $('#vehicleDiscountWrapper').removeClass('processing');
            var message = '<h6>Error!</h6><p>' + ((response['Message'] == undefined) ? '' : response['Message']) + '</p>';
            $('#auto_discount_success_modal .modal-body').html(message);
            $('#auto_discount_success_modal').modal('show');
            break;
        case 'rule-discount/vin-delete/' + requestData.uuid:
            $('#add-rule-discount-model .modal-body').removeClass('processing');
            var message = '<h6>Error!</h6><p>' + ((response['Message'] == undefined) ? '' : response['Message']) + '</p>';
            $('#auto_discount_success_modal .modal-body').html(message);
            $('#auto_discount_success_modal').modal('show');
            break;
        case 'rule-single-discount/add':
            $('#add-rule-discount-model .modal-body').removeClass('processing');
            $('#RulediscountStatusCodeError').css('display', 'block');
            $('#RulediscountStatusCodeError').html(((response['Message'] == undefined) ? '' : response['Message']));
            break;
        case 'vin-activation':
            $('.gridTableWrapper').removeClass('processing');
            break;
        case 'feedback':
            var message = response['Message'];
            $('#feedbackmessage_error').html(message);
            $('#feedback_error_modal').modal('show');
            break;
    }
}

/*
 *  API Call Functions
 */
function getMake() {
    var url = 'getAllMakes';
    var data = {
        NameOfAPI: "getAllMakes",
        DealerCode: requestData.DealerCode
    };
    var method = "POST";
    ajaxCall(url, method, data);
}

function getModelYear() {
    var url = 'getModelYear';
    var data = {
        NameOfAPI: "FilterModelYear",
        DealerCode: requestData.DealerCode,
        MakeCode: requestData.makeCode
    };
    var method = "POST";
    ajaxCall(url, method, data);
}

function getVehicle() {
    var url = 'getVehicleSelection';
    var data = {
        NameOfAPI: "FilterVehicleSelection",
        DealerCode: requestData.DealerCode,
        MakeCode: requestData.makeCode,
        ModelYear: requestData.modelyear
    };
    if (globalValues.tabs.dealerAutomatedDiscounts) {
        data['ModelYear'] = $('#da_auto_event_model_year').val();
    }
    var method = "POST";
    ajaxCall(url, method, data);
}

function getTrims() {
    var url = 'FilterTrimSelection';
    var data = {
        NameOfAPI: "FilterTrimSelection",
        DealerCode: requestData.DealerCode,
        MakeCode: requestData.makeCode,
        ModelYear: requestData.modelyear,
        Model: requestData.model
    };
    if (globalValues.tabs.dealerAutomatedDiscounts) {
        data['ModelYear'] = $('#da_auto_event_model_year').val();
        data['Model'] = $('#da_auto_event_vehicle').val();
    }
    var method = "POST";
    ajaxCall(url, method, data);
}

function SearchTableVinActivation(rowId, element, vinnumber) {
    console.log(element.checked);
    var operation = (element.checked) ? 1 : 0;
    if (operation) {
        $('#' + rowId).addClass('gridRowDisable');
        $('#' + rowId + ' .switchLabel').html('Deactivated');
        $('#' + rowId + ' .switchLabel').addClass('DeActiveText');
        $('#' + rowId + ' .switchLabel').removeClass('ActiveText');
    } else {
        $('#' + rowId).removeClass('gridRowDisable');
        $('#' + rowId + ' .switchLabel').html('Activated');
        $('#' + rowId + ' .switchLabel').addClass('ActiveText');
        $('#' + rowId + ' .switchLabel').removeClass('DeActiveText');
    }
    var request, url, method;
    request = {
        NameOfAPI: "vinActivation",
        DealerCode: requestData.DealerCode,
        VinNumber: vinnumber,
        Operation: operation
    };
    url = 'vin-activation';
    method = "POST";
    ajaxCall(url, method, request);
}

function paymentCalculator() {
    var container = '#payment-calculator-modal';
    if(requestData.financeoption == 3){
        requestData.transactionType = 'cash';
    }else if (requestData.financeoption == 2){
       requestData.transactionType = 'finance';
    }else{
        requestData.transactionType = 'lease';
    }
    var tradeIn = $(container + ' #pc_TradeInValueField').val();
    var vin = $(container + ' .pc_vin span').text();
    var downValue = $(container + ' #pc_downpayment').val();
    var the_lease_explores = new Array();
    $("input[name='lease_explores[]']:checked").each(function() {
        the_lease_explores.push($(this).val());
    });
    var mileage = $(container + ' #yearlyMilage').val();
    var term = (requestData.financeoption == 1) ? $(container + ' #lease_terms').val() : $(container + ' #finance_terms').val();
    /*Request*/
    var request = {
        NameOfAPI: "paymentcalc",
        zipcode: DealerZipCode,
        vin: vin,
        down: downValue,
        tradein: tradeIn,
        transactionType: requestData.transactionType,
        mileage: mileage,
        term: (PaymentCalcEventName == 'onload') ? '' : term,
        rebateIDs: the_lease_explores,
        method: "onload",
        filter_info: paymentCalculatorrequest
    };
    var url = 'paymentcalc';
    var method = "POST";
    ajaxCall(url, method, request);
}

/*
 * Reset Functions
 */
function resetTrimMultiSelect() {
    var container = '';
    requestData = {
        DealerCode: DealerCode,
        makeCode: "",
        financeoption: 1,
        trim: []
    };
    if (globalValues.tabs.dealerDiscounts) {
        container = '#da_event_trim';
    }
    if (globalValues.tabs.dealerAutomatedDiscounts) {
        container = '#da_auto_event_trim';
    }
    $(container).multiselect('reset');
    $(container).multiselect('reload');
    $('div#ms-list-1 button').addClass('disabled');
}

function resetGridTable() {
    $('.gridTableWrapper').hide();
    if (globalValues.tabs.dealerDiscounts) {
        $('#gridTableContainer tbody').html('');
    }
    $('.pleaseSelect').show();
}

function resetSearchAttributesForm() {
    $('input[name=finance_option][value="1"]').prop('checked', true);
    if (globalValues.tabs.dealerDiscounts) {
        $('#da_event_model_year').html('<option value=""> Select Year</option>');
        $('#da_event_vehicle').html('<option value=""> Choose a Vehicle</option>');
        //$('#da_event_trim').html('<option value=""> Choose Trim Level</option>');
        //$('#da_event_msrp_highest').html('<option value=""> Highest</option>');
        //$('#da_event_msrp_lowest').html('<option value=""> Lowest</option>');
        $('#da_event_drive_type,#da_event_color,#da_event_engine_desc,#da_event_transmission,#gridTableContainer tbody').html('');
        $('#SearchByVehicleGoBtn').addClass('disabled');
        dataTableOperation('#gridTableContainer');
    }
    resetTrimMultiSelect();
}

function dateInputKeyDown(event) {
    if (event.keyIdentifier == "Down") {
        event.preventDefault();
        return false;
    }
}

//OpenPaymentCalaculator(\''+item['vin']+'\',\''+item['make']+'\',\''+item['year']+'\',\''+item['model']+'\',\''+item['msrp']+'\',\''+item['msrp_format']+'\',\''+item['total_amount']+'\',\''+item['total_amount_format']+'\');
function OpenPaymentCalculator(vin, make, year, model, trim, msrp, msrp_format, total_amount, total_amount_format) {
    var paymentText = 'Lease';
    var transactionType = '';
    if(requestData.financeoption == 3){
        transactionType = 'cash';
        paymentText = 'Cash';
    }else if (requestData.financeoption == 2){
       transactionType = 'finance';
       paymentText = 'Finance';
    }else{
        transactionType = 'lease';
        paymentText = 'Lease';
    }
    paymentCalculatorrequest = {
        DealerCode: $('#dealer_code').val(),
        makeCode: requestData.makeCode,
        financeoption: requestData.financeoption,
        modelyear: year,
        model: model,
        transactionType: transactionType,
        trim: trim

    };
    var container = '#payment-calculator-modal';
    var modelYear = year + ' ' + model + ' ' + trim;
    var hideBottomClass = (requestData.financeoption == 1) ? '.leaseBottomContent' : '.financeBottomCotent';
    $(container + ' #yearlyMilage').val(10000);
    $(container + ' #lease_terms').val(36);
    $(container + ' #finance_terms').val(72);
    $(container + ' .bottomContent').hide();
    $(container + ' ' + hideBottomClass).show();
    $(container + ' .pc_modelyear').html(modelYear);
    $(container + ' .pc_vin span').html(vin);
    $(container + ' .pc_msrpstickerPrice').html(msrp_format);
    $(container + ' #pc_downpayment').val((msrp * 0.1)); // 10% of MSRP
    $(container + ' .pc_pricedetails span').html(paymentText);
    $(container).modal('show');
    $(document).on('blur', '#pc_downpayment', function() {
        var dp = $('#payment-calculator-modal .pc_msrpstickerPrice').text();
        var da = dp.replace('$', '').replace(',', '');
        var percent = calculatePercentage(this.value, da);
        $('#payment-calculator-modal .dPaymentRange').text(percent);
        PaymentCalcEventName = 'click';
        paymentCalculator();
        console.log('downpayment:: ' + this.value);
    });
    $(document).on('change', '#pc_TradeInValueField,input[name="lease_explores[]"],#finance_terms,#lease_terms,#yearlyMilage', function() {
        PaymentCalcEventName = 'click';
        paymentCalculator();
    });
    paymentCalculator();
}

function populatePaymentCalculatorDetails(response) {
    var container = '#payment-calculator-modal';
    var tradeIn = response['developer']['arraybuilder']['tradeInValue'];
    var paymentwithTaxVal = response['paymentWithTaxesVal'];
    $(container + ' .pc_msrpstickerPrice').text(response.msrp_results);
    var dp = $(container + ' .pc_msrpstickerPrice').text();
    var da = dp.replace('$', '').replace(',', '');
    $(container + ' .pc_msrpstickerPrice').text('$' + paymentFormat(parseInt(da)));
    if(response.transactionType != 'cash'){
        $(container + ' .li_downPayment,'+container + ' .estimatedBlock,'+container + ' #lease_finance_buy_for').show();
        $(container + ' #cash_buy_for').hide();
        var cashDown = response['developer']['arraybuilder']['cashDown'];
        // $('#l_MSRPHidden').val( parseInt(da) );
        var percent = calculatePercentage(cashDown, da);
        $(container + ' #pc_downpayment').val(parseInt(cashDown));
        $(container + ' .dPaymentRange').text(percent);

        if(response.transactionType == 'finance'){
            $(container + ' .ore_emi_lease_month').html(response.default_finance);
        }else{
            $(container + ' .ore_emi_lease_month').html(response.default_lease);
        }
    }else{
        $(container + ' .li_downPayment,'+container + ' .estimatedBlock,'+container + ' #lease_finance_buy_for').hide();
        $(container + ' #cash_buy_for').show();
        $(container + ' #pc_downpayment').val(0);
        $(container + ' .dPaymentRange').text(0);
        $(container + ' .ore_cash_emi').html('$' + response.outTheDoorPrice);
    }
    $(container + ' #pc_TradeInValueField').val(tradeIn);
    $(container + ' .mDestCharge').text('-$' + paymentFormat(parseInt(response.incentiveAmount)));
    $(container + ' .mDestLExplore').text('-$' + paymentFormat(parseInt(response.explore_amount)));
    $(container + ' .mDestLDlrDisc').text('-$' + paymentFormat(parseInt(response.dlrDiscAmount)));
    if (response.dlrDiscLists) {
        $(container + ' .dealer_disc_update').html(response.dlrDiscLists);
    } else {
        $(container + ' .dealer_disc_update').html('No Dealer Discounts Applicable');
    }
    if(response.transactionType != 'cash' && paymentwithTaxVal != undefined && paymentwithTaxVal != 0){
        var estimate = paymentwithTaxVal.amountFinanced.replace('$', '').replace(',', '');
        var monthlyPayment = paymentwithTaxVal.monthlyPayment.replace('$', '').replace(',', '');
        $(container + ' .ore_disp_lease_estimate').html('$' + paymentFormat(parseInt(estimate)));
        $(container + ' .ore_lease_emi').html('$' + paymentFormat(parseInt(monthlyPayment)));
    }

    if (PaymentCalcEventName == 'onload' && response.transactionType != 'cash') {
        var result_terms = response.terms;
        var termsHtml = '';
        if (result_terms) {
            for (var idx in result_terms) {
                var month = result_terms[idx];
                var def = '';
                if (requestData.financeoption == 2) {
                    if (response['default_finance'] == month)
                        def = 'selected = "selected"';
                } else {
                    if (response['default_lease'] == month)
                        def = 'selected = "selected"';
                }
                termsHtml += '<option value="' + month + '" ' + def + ' >' + month + ' Months</option>';
            }
            if (requestData.financeoption == 2) {
                $(container + ' #finance_terms').html(termsHtml);
            } else {
                $(container + ' #lease_terms').html(termsHtml);
            }
        }
    }else{

    }

    if (requestData.financeoption == 2 && paymentwithTaxVal != undefined && paymentwithTaxVal != 0) {
        $(container + ' .chrysler_apr_in').html(paymentwithTaxVal.apr);
    }
    var array_rebateDetailsid = (response['rebateDetailsid'] != undefined) ? response['rebateDetailsid'].split(',') : response['AllIncentiveIds'];
    var explores = response.explores;
    var exploreHtml = 'No Additional Offers Applicable';
    var Calc_explore_amount = 0;
    if (explores) {
        exploreHtml = '<ul class="list-group reg">'
        for (var idx in explores) {
            var item = explores[idx];
            var ids = item['ids'].split(',');
            var checked = '';
            for (var x in ids) {
                if (checked == '') {
                    checked = (array_rebateDetailsid.indexOf(ids[x]) !== -1) ? 'checked' : '';
                }
            }
            exploreHtml += '<li>';
            exploreHtml += '<label for="explores_' + item['feature'] + '" class="customCheckBox">';
            exploreHtml += '<input type="checkbox" ' + checked + ' title="lease_explores" aria-labelledby="explores_' + item['feature'] + '" aria-describedby="explores_' + item['feature'] + '" class="lease_chk" name="lease_explores[]" id="explores_' + item['feature'] + '" value="' + item['ids'] + '" data-original-groups="' + item['name'][0] + '">';
            exploreHtml += '<span></span><b style="color:#000">' + item['feature'] + '</b></label>';
            exploreHtml += '<span style="float:right"><b> $' + paymentFormat(item['amount']) + '</b></span>';
            exploreHtml += '</li>';
            for (var i in item['name']) {
                if (checked == 'checked') {
                    Calc_explore_amount += parseInt(item['inv_amount'][i]);
                }
                exploreHtml += '<li style="padding-left:30px;" class="offer_subsets" data-relation-name="explores_' + item['feature'] + '" data-original-items="' + item['name'][i] + '" data-original-incentiveid="' + ids[i] + '">' + item['name'][i] + '&nbsp;&nbsp; <b>$' + item['inv_amount'][i] + '</b></li>';
                i++;
            }

        }
        exploreHtml += '</ul>';
    }
    $(container + ' .calc_offers_desc').html(exploreHtml);
    $(container + ' .mDestLExplore').text('-$' + paymentFormat(Calc_explore_amount));
    var incentiveNames = response.incentiveNames;
    exploreHtml = 'No Incentives Applicable';
    var incentiveHtml = '';
    if (incentiveNames.length > 0) {
        var incentiveHtml = '';
        for (var idx in incentiveNames) {
            var item = incentiveNames[idx];
            if (response.man_incentives_sin_name[idx] != null && response.man_incentives_sin_name[idx] != undefined && response.man_incentives_sin_name[idx] != '') {
                incentiveHtml += '<li>';
                incentiveHtml += '<label for="explores_' + item + '"><img alt="incentive-icon" src="/img/incentive-icon-5.png" width="20" height="20">';
                incentiveHtml += '<span style="color:#000">' + item + '</span></label>';
                incentiveHtml += '<span style="float:right"><b> $' + paymentFormat(response.man_incentives_sin_name[idx]) + '</b></span>';
                incentiveHtml += '</li>';
            }
        }
    }

    if(response.incentivesBonusCash_available){
        if (response['incentivesBonusCashList'].length > 0) {
            $.each(response['incentivesBonusCashList'], function (key, value) {
                var num = 20;
                incentiveHtml += '<li><span><img alt="incentive-icon" src="/images/incentive-icon-5.png" width=\"20\" height=\"20\" />' + key + '&nbsp;';
                if(value.disclaimer != ''){
                    incv +='<span data-disclamer="' + value.disclaimer + '" id="l_q_insentive" class="l_q_insentive badge disclaimer" data-num="' + num + '">' + num + '</span>';
                    num++;
                }
                incentiveHtml += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span style="float:right; "><b>$' + value.discount + '</b></span></span></li>';
            });
        }
    }

    if(incentiveHtml != ''){
        var ulexploreHtml = '<ul class="list-group reg">' + incentiveHtml + '</ul>';
        exploreHtml = ulexploreHtml;
    }


    $(container + ' .calc_incentives_desc').html(exploreHtml);
    var totalIncen = response['incentiveAmount'];
    var totalExplore = response['explores'];
    var totalDlrDisc = response['dlrDiscAmount'];
    if (totalIncen == null || totalIncen == 0 || totalIncen == undefined) {
        $(container + ' .IncentivesMerkle').addClass('pointer-event-none');
        $(container + ' .IncentivesMerkle').next('div.addOffers').hide();
    } else {
        $(container + ' .IncentivesMerkle').removeClass('pointer-event-none');
    }
    if (totalExplore.length == 0) {
        $(container + ' .expAddOffMerkle').addClass('pointer-event-none');;
        $(container + ' .expAddOffMerkle').next('div.addOffers').hide();
    } else {
        $(container + ' .expAddOffMerkle').removeClass('pointer-event-none');
    }
    if (totalDlrDisc == null || totalDlrDisc == 0 || totalDlrDisc == undefined) {
        $(container + ' .DlrDiscMerkle').addClass('pointer-event-none');;
        $(container + ' .DlrDiscMerkle').next('div.addOffers').hide();
    } else {
        $(container + ' .DlrDiscMerkle').removeClass('pointer-event-none');
    }
}

function gridloadcheckpercentageValidation(iddatatable) {
    var chk = [];
    var addTooltip = false;
    var datatable = $(iddatatable).DataTable();
    var data = datatable
        .rows()
        .data();
    var rows = datatable
        .rows();
    var removetrindex = [];
    for (let index = 0; index < rows[0].length; index++) {
        var datavalue = 0
        const element = data[index];
        dataindex = index;
        var datavalue = datatable.$("tr").eq(dataindex).attr('data-msrp-value');
        if (datavalue != undefined) {
            if (iddatatable == '.gridAutomatedDiscountTableContainer') {
                rs = reloadpercentagediscountflatratevalidation(datavalue);
            } else {
                rs = percentagediscountvalidation('#BulkDiscountRowWrapper', datavalue);
            }
            datatable.rows(index).nodes().to$().css('background-color', '');
            datatable.rows(index).nodes().to$().removeClass('highlighted_tr');
            if (rs == 'true') {
                datatable.rows(index).nodes().to$().addClass('highlighted_tr');
                datatable.rows(index).nodes().to$().find('td:first-child').prepend("<i class='fas fa-exclamation-triangle' ></i>");
                datatable.rows(index).nodes().to$().find('td:first-child').addClass('highlighted_td');
                chk[index] = 1;
            }
        }
    }
    return chk;
}

function unchecktr(iddatatable) {
    var datatable = $(iddatatable).DataTable();
    var vinarray = [];
    var rowcollection = datatable.$(".auto_discount_include", { "page": "all" });
    rowcollection.each(function(index, elem) {
        var checkbox_value = $(elem).val();
        if (!$(elem).prop("checked")) {
            var checkbox_value = $(elem).val();
            if (vinarray.indexOf(checkbox_value) !== -1) {} else {
                $(elem).parents().eq(3).find('td:first-child').removeClass('highlighted_td');
                $(elem).parents().eq(3).find('td:first-child').find("i").remove();
            }
            vinarray.push($(elem).val());
        }
    });
}

function highlightvin(datatable, trid, index) {
    console.log('trid', trid);
    datatable.rows(index).nodes().to$().addClass('highlighted_tr');
    datatable.rows(index).nodes().to$().find('td:first-child').prepend("<i class='fas fa-exclamation-triangle' ></i>");
    datatable.rows(index).nodes().to$().find('td:first-child').addClass('highlighted_td');
}


/*
 ** Validation Function
 */

function percentagediscountvalidation(container, datavalue) {
    var dates = [],
        obj = [],
        obj2 = [];
    $(container).find(".flat_rate").each(function(key, val) {
        var start_date = $(".discount_start_date").eq(key).val();
        var end_date = $(".discount_end_date").eq(key).val();
        if (start_date != '' && end_date != '') {
            var flat = Number($(this).val());
            var percent = $(".off_percent").eq(key).val();
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
                        } else {}
                    }
                });
                group[index] = temp;
                total += value[0];
            }
        });
    }
    ///validating 5000
    var max = 0
    $.each(group, function(key, value) {
        var max = 0
        if (Array.isArray(value) && value.length) {
            $.each(value, function(key, index) {
                max += (obj[index][3] == 0) ? obj[index][0] : ((datavalue * obj[index][3]) / 100);
            });
            console.log('percentage', max);
            status = (max > 5000) ? 'true' : 'false';
        }
    });
    return status;
}

function reloadpercentagediscountflatratevalidation(datavalue) {
    var row = [],
        dates = [],
        obj = [],
        obj2 = [];
    ///getting index of check
    $(".rule_discount").each(function(key, val) {
        if ($(this).is(":checked")) {
            row.push(key);
        }
    });
    if (row.length > 0) {
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
    } else {
        $(".flat_rate").each(function(key, val) {
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
        });
    }

    total = 0;
    var group = [],
        group2 = [],
        single = [],
        Index = {},
        Index2 = [];
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
    var max = 0
    $.each(group, function(key, value) {
        var max = 0
        if (Array.isArray(value) && value.length) {
            $.each(value, function(key, index) {
                max += (obj[index][3] == 0) ? obj[index][0] : ((datavalue * obj[index][3]) / 100);
            });
            status = (max > 5000) ? 'true' : 'false';
        }
    })
    return status;
}

/*
 * Init Functions
 */
function initateTrimMultiSelect(container) {
    $(container).multiselect({
        columns: 1,
        search: false,
        texts: {
            selectAll: 'Select All',
            unselectAll: 'Deselect All'
        },
        placeholder: 'Choose Trim Level',
        selectAll: true,
        header: false,
    });
    $('div#ms-list-1 button').addClass('disabled');
}

 function changefooter(footerbrand) {
    switch (footerbrand) {
       case 'C':
          $('.footercontent').hide();
          $('#chrysler').show();
          globalValues.anchor = "https://fcacommunity.force.com/Chrysler/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility";
          break;
       case 'D':
          $('.footercontent').hide();
          $('#dodge').show();
          globalValues.anchor = "https://fcacommunity.force.com/Dodge/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility";
          break;
       case 'J':
          $('.footercontent').hide();
          $('#jeep').show();
          globalValues.anchor = "https://fcacommunity.force.com/Jeep/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility";
          break;
       case 'T':
          $('.footercontent').hide();
          $('#ram').show();
          globalValues.anchor = "https://fcacommunity.force.com/RAM/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility";
          break;
       case 'X':
          $('.footercontent').hide();
          $('#fiat').show();
          globalValues.anchor = "https://fcacommunity.force.com/FIAT/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility";
          break;
       case 'Y':
       default:
          $('.footercontent').hide();
          $('#alfa_romeo').show();
          globalValues.anchor = "https://fcacommunity.force.com/Chrysler/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility";
    }
    if('Y' != footerbrand){
        $('#alfa_romeo_links').hide();
        $('#non_alfa_romeo_links').show();
        $('#Accessibility').attr('href', globalValues.anchor);
    }else{
        $('#non_alfa_romeo_links').hide();
        $('#alfa_romeo_links').show();
    }
 }

/*
 * document ready Functions
 */
$(document).ready(function() {
    baseURL = $('#APP_URL').val();
    baseAPIURL = baseURL + 'api/v1/';
    DealerCode = $('#dealer_code').val();
    DealerZipCode = $('#dealer_zip_code').val();
    globalValues.todayDate = getTodayDate(false);
    globalValues.maxDate = getTodayDate(true);
    globalValues.bulk_discount_validation = false;
    globalValues.discount_validation = false;
    globalValues.auto_discount_validation = false;
    requestData = {
        DealerCode: DealerCode,
        makeCode: "",
        financeoption: 1,
        trim: []
    };
    globalValues.anchor = "https://fcacommunity.force.com/Chrysler/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility";
     $('.footercontent,#non_alfa_romeo_links').hide();
     changefooter('Y');
    EmptyDiscountRow = '<div class="discounts-rows"><div class="discount-details"><input type="hidden" name="discount_uuid" value=""><div class="discount-space"><span class="discount-name-field"><label>Discount Name</label><input type="text" name="discount_name" maxlength="60" value=""><span class="discount-name-error bulk-error">Please enter discount name</span><!-- <span class="discount-name-edit-field">Edit Name</span> --></span><span class="discount-flat-rate"> <label>Flat Rate <span class="switch"><label><input type="checkbox" name="price_switch" checked><span class="lever"></span></label></span></label><input type="text" name="flat_rate" value="" onkeypress="numbervalidate(event)" maxlength="4"> <span class="flat-rate-error bulk-error">Please enter discount amount</span></span><span class="or-text">or</span><span class="off-percent"><label class="off-percent-label">% Off</label><input type="text" name="off_percent" value="" onkeypress="numbervalidate(event)" maxlength="2" disabled="disabled"><span class="off_percent_error bulk-error">Please enter % value for discount</span></span><span class="discount-date-field"><label class="discount-start-date-label">Start Date</label><input type="date" name="discount_start_date" value="" min="' + globalValues.todayDate + '" max="' + globalValues.maxDate + '"><span class="discount_start_date_error bulk-error">Please enter Start Date</span> </span><span class="discount-date-field"><label class="discount-end-date-label">End Date</label><input type="date" name="discount_end_date" value="" min="' + globalValues.todayDate + '" max="' + globalValues.maxDate + '"><span class="discount_end_date_error bulk-error">Please enter End Date</span></span></div><div class="saved-discounts"><label class="customCheckBox"><input type="checkbox" name="saved_discount"><span>ADD to Saved Discounts (Only 5 Allowed)</span></label></div></div><div class="discount-delete-field" onclick="deleteEmptyDiscount(this);"><span><i aria-hidden="true" class="fa fa-trash-alt"></i></span></div></div>';
    $('.gridTableWrapper').hide();
    $('.pleaseSelect').show();

    $('.modal').on("hidden.bs.modal", function(e) { //fire on closing modal box
        if ($('.modal:visible').length) { // check whether parent modal is opend after child modal close
            $('body').addClass('modal-open'); // if open mean length is 1 then add a bootstrap css class to body of the page
        }
    });

    $(document).on('click', '.dOpener', function() {
        if ($(this).next(".cDropDown").is(":visible") != true) {
            $(this).addClass("menu-up").removeClass('menu-down');
            $(this).next(".cDropDown").slideDown();

        } else {
            $(this).addClass("menu-down").removeClass('menu-up');
            $(this).next(".cDropDown").slideUp();
        }
    });

    // Custom placeholder for feedback form to get red color star '*'
    $('#feedbackform input').on('input',function(e){
        if($(this).val() == '' ){
         $(this).siblings('.placeholder').show();
        }else{
           $(this).siblings('.placeholder').hide();
        }
    });

    $(document).on('click', '#logout-anchor-dc', function() {
        var url = 'ssologout';
        var data = {};
        var method = "GET";
        ajaxCall(url, method, data);
        try {
            setTimeout(function() {
                window.open('', '_self', '');
                window.top.close();
                window.close();
            }, 100);
        } catch (ex) {
            setTimeout(function() {
                window.location.href = '/';
            }, 400);
        }
        setTimeout(function() {
            window.location.href = '/';
        }, 400);
    });

    $('.search-result-buttons .btn').click(function() {
        $('.search-result-buttons .btn').removeClass('gcss-colors-element-primary');
        $(this).addClass('gcss-colors-element-primary');
        $(this).removeClass('gcss-colors-element-subdued');
        var checkbox = $(this).find('input[name="discount_options"]')[0];
        $('input[name=discount_options]').removeAttr('checked');
        $('input[name=discount_options][value="' + checkbox.value + '"]').attr('checked', true);
        if (checkbox.value == 1) {
            globalValues.tabs.dealerDiscounts = true;
            globalValues.tabs.dealerAutomatedDiscounts = false;
        } else {
            globalValues.tabs.dealerAutomatedDiscounts = true;
            globalValues.tabs.dealerDiscounts = false;
        }
    });

    $('.search-result-buttons .tab-discount-label').click(function() {
        setTimeout(function() {
            window.location.href = baseURL + 'inventory';
        }, 100);
    });

    $('.search-result-buttons .tab-autodiscount-label').click(function() {
        setTimeout(function() {
            window.location.href = baseURL + 'automated-inventory';
        }, 100);
    });

    $('body').on('change', '.discount-flat-rate input[type="checkbox"]', function() {
        if ($(this).prop("checked") == true) {
            $(this).closest('.discounts-rows').find('.discount-flat-rate input[type="text"]').removeAttr("disabled", "disabled");
            $(this).closest('.discounts-rows').find('.off-percent input[type="text"]').attr("disabled", "disabled");
        } else {
            $(this).closest('.discounts-rows').find('.off-percent input[type="text"]').removeAttr("disabled", "disabled");
            $(this).closest('.discounts-rows').find('.discount-flat-rate input[type="text"]').attr("disabled", "disabled");
        }
    });
    $(document).on('change', 'input[name=finance_option]:radio,input[name=vin_finance_option]:radio', function() {
        requestData['financeoption'] = this.value;
        PaymentCalcEventName = 'onload';
        console.log(this.value);
        handleTabs();
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

    $(document).on('change', '[name="price_switch"]', function() {
        $(this).parent().parent().parent().siblings().val(0);
        $(this).parent().parent().parent().parent().siblings().find('input[name=off_percent]').val(0);
    });

    $('.panel-title a').click(function() {
        if ($(this).find('i').hasClass('fa-plus')) {
            $(this).find('i').removeClass('fa-plus');
            $(this).find('i').addClass('fa-minus');

            $(this).addClass('open');
        } else {
            $(this).find('i').removeClass('fa-minus');
            $(this).find('i').addClass('fa-plus');
            $(this).removeClass('open');
        }
    });
    $('#feedbackphone').mask('(000) 000-0000');
});

if($('#feadbackdealership').val()!='')
{
    $('#dealershipplaceholder').html('');
}

function faqsubmit() {
    validation = true;
    combinevalidation = false;
    var name = $('#fedbackname').val();
    console.log('name', name);
    if (name == '') {
        $('.fedbackname').addClass('active_error');
        document.getElementById('fedbackname_error').innerHTML = "Please enter the Name";
        validation = false;
    } else {
        $('.fedbackname').removeClass('active_error');
        document.getElementById('fedbackname_error').innerHTML = " ";
    }
    var dealership = $('#feadbackdealership').val();
    if (dealership == '') {
        $('.feadbackdealership').addClass('active_error');
        document.getElementById('feadback_error').innerHTML = "Please enter the Dealership Name/ID";
        validation = false;
    } else {
        $('.feadbackdealership').removeClass('active_error');
    }


    var phone = $('#feedbackphone').val();
    var email = $('#feadbackemail').val();
    if (email == '' && phone == '') {
        $('.feadbackemail').addClass('active_error');
        document.getElementById('feadbackemail_error').innerHTML = "Please enter a valid email";
        validation = false;
        combinevalidation = true;
    } else {
        $('.feadbackemail').removeClass('active_error');
        document.getElementById('feadbackemail_error').innerHTML = "";
    }
    if (!ValidateEmail(email) && email != '') {
        $('.feadbackemail').addClass('active_error');
        document.getElementById('feadbackemail_error').innerHTML = "Please enter a valid email";
        validation = false;
    } else {
        $('.validemail').removeClass('active_error');

    }

    if (phone == '' && email == '' && combinevalidation == false) {
        if (!phone_validate(phone)) {
            $('.validphone').addClass('active_error');
            document.getElementById('feedbackphone_error').innerHTML = "Please enter a valid Phone Number";
            validation = false;
        } else {
            $('.validphone').removeClass('active_error');

        }
        if (phone.length > 14 || phone.length < 14) {
            $('.validphone').addClass('active_error');
            document.getElementById('feedbackphone_error').innerHTML = "Please enter a valid Phone Number";
        }
    }
    var message = $('#fedbackmessage').val();

    if ($.trim(message) == '') {
        $('.fedbackmessage').addClass('active_error');
        document.getElementById('fedbackmessage_error').innerHTML = "Please enter the Message";
        validation = false;
    } else {
        $('.fedbackmessage').removeClass('active_error');
    }
    request = {
        NameOfAPI: "feedback",
        name: name,
        dealerid: dealership,
        message: message
    };
    if ($('#feedbackphone').val() != '') {
        request.phone = $('#feedbackphone').val()
    }

    if ($('#feadbackemail').val() != '') {
        request.email = $('#feadbackemail').val()
    }

    url = 'feedback';
    method = "POST";
    if (validation) {
        ajaxCall(url, method, request);
    }

}

function ValidateEmail(email) {
    // Validate email format
    var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    return expr.test(email);
};

function phone_validate(v_phone) {
    /*    if (v_phone.length < 10) {
            return false;
        }
        var regexPattern = new RegExp(/^[0-9-+]+$/);
        return regexPattern.test(v_phone);*/
    v_phone_no_length = v_phone.length;
    var v_phone_pattern = /^([\+][0-9]{1,3}[\ \.\-])?([\(]{1}[0-9]{2,6}[\)])?([0-9\ \.\-\/]{3,20})((x|ext|extension)[\ ]?[0-9]{1,4})?$/;
    var v_phone_res = v_phone_pattern.test(v_phone);
    if ((v_phone_res && v_phone_no_length === 14) || v_phone_no_length == 0) { return true; } else {
        return false;
    }
}

/*$("#feedbackphone").keypress(function(e) {
    var length = $(this).val().length;
    if (length > 14) {
        return false;
    } else if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    } else if ((length == 0) && (e.which == 48)) {
        return false;
    }

});*/

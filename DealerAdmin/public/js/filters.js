/*
 * Global Variables
 */
globalValues.tabs = {
    dealerDiscounts: true,
    dealerAutomatedDiscounts: false
};
EmptyDiscountRow = '';
var bulkDeteleCurrentObject;
var deleteCurrentObject;

/*
 *  API Call Functions
 */
function getMsrpValues() {
    var url = 'FilterMsrpSelection';
    var data = {
        NameOfAPI: "FilterMsrpSelection",
        DealerCode: requestData.DealerCode,
        MakeCode: requestData.makeCode,
        ModelYear: requestData.modelyear,
        Model: requestData.model,
        Trim: requestData.trim
    };
    var method = "POST";
    ajaxCall(url, method, data);
}

function getSecondarySelection() {
    var url = 'FilterSecondarySelection';
    requestData.msrp_highest = $('#slider-range').slider("values")[1];
    requestData.msrp_lowest = $('#slider-range').slider("values")[0];
    var data = {
        NameOfAPI: "FilterSecondarySelection",
        DealerCode: requestData.DealerCode,
        MakeCode: requestData.makeCode,
        ModelYear: requestData.modelyear,
        Model: requestData.model,
        Trim: requestData.trim,
        MsrpHighest: requestData.msrp_highest,
        MsrpLowest: requestData.msrp_lowest
    };
    var method = "POST";
    ajaxCall(url, method, data);
}

function SearchByAttributes() {
    $('.error-check').removeClass('active');
    var url = 'SearchByAttributes';
    requestData['drive_type'] = getCheckboxValues('drive_type[]');
    requestData['interior_colour_desc'] = getCheckboxValues('interior_colour_desc[]');
    requestData['eng_desc'] = getCheckboxValues('eng_desc[]');
    requestData['transmission_desc'] = getCheckboxValues('transmission_desc[]');
    requestData.msrp_highest = $('#slider-range').slider("values")[1];
    requestData.msrp_lowest = $('#slider-range').slider("values")[0];
    var data = {
        NameOfAPI: "SearchByAttributes",
        DealerCode: requestData.DealerCode,
        FinanceOption: requestData.financeoption,
        MakeCode: requestData.makeCode,
        ModelYear: requestData.modelyear,
        Model: requestData.model,
        Trim: requestData.trim
    };
    if (requestData.msrp_highest != undefined && requestData.msrp_lowest != undefined) {
        data['MsrpHighest'] = requestData.msrp_highest;
        data['MsrpLowest'] = requestData.msrp_lowest;
    }
    if (0 < requestData['drive_type'].length) {
        data['DriveNames'] = requestData['drive_type'].toString();
        requestData['drive_type'] = data['DriveNames'];
    }
    if (0 < requestData['interior_colour_desc'].length) {
        data['ColorNames'] = requestData['interior_colour_desc'].toString();
        requestData['interior_colour_desc'] = data['ColorNames'];
    }
    if (0 < requestData['transmission_desc'].length) {
        data['TransmissionNames'] = requestData['transmission_desc'].toString();
        requestData['transmission_desc'] = data['TransmissionNames'];
    }
    if (0 < requestData['eng_desc'].length) {
        data['EngineDescNames'] = requestData['eng_desc'].toString();
        requestData['eng_desc'] = data['EngineDescNames'];
    }
    var method = "POST";
    ajaxCall(url, method, data);
    $('.pleaseSelect').hide();
}

function SearchByVin() {
    var vin = $('#da_event_vinNumber').val();
    requestData.VinNumber = vin;
    if ($('#da_event_vinNumber').val() == '' || $('#da_event_vinNumber').val().length != 17) {
        console.log('please enter a valid vin number');
        $('.error-check').html('Please Enter a Valid VIN Number');
        $('.error-check').addClass('active');
    } else {
        $('.error-check').removeClass('active');
        console.log('valid vin');
        var url = 'SearchByVIN';
        var data = {
            NameOfAPI: "SearchByVIN",
            DealerCode: requestData.DealerCode,
            VinNumber: vin,
            FinanceOption: requestData.financeoption
        };
        var method = "POST";
        ajaxCall(url, method, data);
        $('.pleaseSelect').hide();
    }
}

function getDiscountforVin(VinNumber) {
    var request = {
        NameOfAPI: "listDiscount",
        DealerCode: requestData.DealerCode,
        VinNumber: VinNumber,
        FinanceOption: requestData.financeoption
    };
    var url = 'discount/list';
    var method = "POST";
    ajaxCall(url, method, request);
}

function getBulkDiscountList() {
    var request = {
        NameOfAPI: "listBulkDiscount",
        DealerCode: requestData.DealerCode,
        FinanceOption: requestData.financeoption,
    };
    var url = 'bulk-discount/list';
    var method = "POST";
    ajaxCall(url, method, request);
}

function deleteSingleDiscountforVin() {
    requestData.uuid = $('#DeleteSingleDiscountPopupUUid').val();
    var request = {};
    var url = 'discount/delete/' + requestData.uuid;
    var method = "DELETE";
    ajaxCall(url, method, request);
}

function deleteBulkDiscount() {
    var discount_name = $('#BulkDeleteDiscountPopupName').val();
    var request = {
        NameOfAPI: "DeleteBulkDiscount",
        DealerCode: requestData.DealerCode,
        NameOfDiscount: discount_name
    };
    var url = 'bulk-discount/remove';
    var method = "POST";
    ajaxCall(url, method, request);
}

function deleteDiscountsforVin() {
    var request = {};
    var url = 'vin-discount/delete/' + requestData.DealerCode + '/' + requestData.VinNumber + '/' + requestData.financeoption;
    var method = "DELETE";
    ajaxCall(url, method, request);
}

function applyDiscounttoVehicle() {
    $('#add-discount-model .modal-body').addClass('processing');
    globalValues.discount_validation = bulkValidationCheck('#add-discount-model');
    if (globalValues.discount_validation == false) {
        $('#add-discount-model .modal-body').removeClass('processing');
        return false;
    }

    var msrp = $('#msrp').val();
    var rs = percentagediscountvalidation('#DiscountRowWrapper',msrp);

    $('#discountStatusCodeError').html('');
    if (rs == 'true') {
        $('#discountStatusCodeError').html('Maximum allowed discount is $5000').css('display', 'block');
        $('#add-discount-model .modal-body').removeClass('processing');
        return false;
    }
    var data = getDiscountRowsData('#add-discount-model');
    var discount_count = $('#ModalDiscountCount').text();
    var finance_option = $('#ModalfinanceOption').text();
    var request, url, method;
    var vin = $('#ModalDiscountVIN').text();
    requestData.VinNumber = vin;
    if (discount_count == 0) {
        request = {
            NameOfAPI: "AddDiscount",
            DealerCode: requestData.DealerCode,
            VinNumber: requestData.VinNumber,
            FinanceOption: requestData.financeoption,
            Discount: data
        };
        url = 'discount/add';
        method = "POST";
    } else {
        request = {
            NameOfAPI: "EditDiscount",
            DealerCode: requestData.DealerCode,
            VinNumber: requestData.VinNumber,
            FinanceOption: requestData.financeoption,
            Discount: data
        };
        url = 'discount/edit/' + requestData.DealerCode;
        method = "PUT";
    }
    ajaxCall(url, method, request);
}

function BulkdiscountApply() {
    $('.add-bulk-discount-alert').hide();
    var vin = getIncludeVinRowsData('#add-bulk-discount-model #gridDiscountTableContainer');
    var discount = getDiscountRowsData('#add-bulk-discount-model');
    var request;
    var url;
    var method;
    request = {
        NameOfAPI: "AddBulkDiscount",
        DealerCode: requestData.DealerCode,
        VinNumber: vin,
        FinanceOption: requestData.financeoption,
        Discount: discount
    };
    url = 'bulk-discount/add';
    method = "POST";
    ajaxCall(url, method, request);
}

function clearalldiscounts() {
    var vin = getIncludeVinRowsData('#add-bulk-discount-model #gridDiscountTableContainer');
    var request;
    var url;
    var method;
    request = {
        NameOfAPI: "ClearAllDiscounts",
        DealerCode: requestData.DealerCode,
        FinanceOption: requestData.financeoption,
        VinNumber: vin
    };
    url = 'clear-all-discount';
    method = "POST";
    ajaxCall(url, method, request);
}

function getBulkDiscountVehicles() {
    $('#add-bulk-discount-model .modal-body').addClass('processing');
    $('#gridDiscountTableError').html('');
    $('#gridDiscountTableError').removeClass('active');
    $('#add-bulk-discount-model .discounts-rows-parent').html(EmptyDiscountRow);
    $('#Include-select-all').prop('checked', false);
    dataTableOperation('#gridDiscountTableContainer');
    getBulkDiscountList();
    var request;
    if (SearchTabActive.SearchByVehicle) {
        request = {
            NameOfAPI: "listBulkDiscountVehicle",
            DealerCode: requestData.DealerCode,
            MakeCode: requestData.makeCode,
            ModelYear: requestData.modelyear,
            Model: requestData.model,
            Trim: requestData.trim,
            MsrpHighest: requestData.msrp_highest,
            MsrpLowest: requestData.msrp_lowest,
            FinanceOption: requestData.financeoption,
        };

        if (requestData['drive_type']) {
            request['DriveNames'] = requestData['drive_type'];
        }
        if (requestData['interior_colour_desc']) {
            request['ColorNames'] = requestData['interior_colour_desc'];
        }
        if (requestData['transmission_desc']) {
            request['TransmissionNames'] = requestData['transmission_desc'];
        }
        if (requestData['eng_desc']) {
            request['EngineDescNames'] = requestData['eng_desc'];
        }
    }
    if (SearchTabActive.SearchByVin) {
        request = {
            NameOfAPI: "listBulkDiscountVehicle",
            DealerCode: requestData.DealerCode,
            VinNumber: requestData.VinNumber,
            FinanceOption: requestData.financeoption,
        };
    }
    var url = 'bulk-discount/vehicles';
    var method = "POST";
    ajaxCall(url, method, request);
}


/*
 ** Validation Function
 */

function checkpercentage_bulkValidation() {
    var chk = [];
    var datatable = $('#gridDiscountTableContainer').DataTable();
    var data = datatable
        .rows()
        .data();
    var rows = datatable
        .rows();
    for (let index = 0; index < data.length; index++) {
        const element = data[index];
        console.log('check ', datatable.$('input[name="bulk_discount_include"]').eq(index).prop("checked"));
        if (datatable.$('input[name="bulk_discount_include"]').eq(index).prop("checked")) {
            var datavalue = datatable.$("tr").eq(index).attr('data-msrp-value');
            if (datavalue != undefined) {
                rs = percentagediscountvalidation('#BulkDiscountRowWrapper',datavalue);
                if (rs == 'true') {
                    datatable.$("tr").eq(index).css('background-color', '#d8c3c7');
                    datatable.$("tr").eq(index).addClass('highlighted_tr');
                    datatable.rows(index).nodes().to$().addClass('highlighted_tr');
                    datatable.rows(index).nodes().to$().find('td:first-child').prepend("<i class='fas fa-exclamation-triangle' ></i>");
                    datatable.rows(index).nodes().to$().find('td:first-child').addClass('highlighted_td');
                    $('.add-bulk-discount-alert').hide();
                    chk[index] = 1;
                } else {
                    datatable.$("tr").eq(index).eq(index).attr('style', '');
                    datatable.$("tr").eq(index).removeClass('highlighted_tr');
                }
            }
        }
    }
    return chk;
}

function validatebulkdiscountPercentage(chk) {
    datavalue = chk.closest("tr").attr('data-msrp-value');
    tr = chk.closest("tr");
    if (chk.is(":checked")) {
        rs = percentagediscountvalidation('#BulkDiscountRowWrapper',datavalue);
        console.log('rs', rs);
        console.log('datavalue', datavalue);
        if (rs == 'true') {
            $(tr).css('background-color', '#d8c3c7');
            $(tr).addClass('highlighted_tr');
            $(tr).css('background-color', '#d8c3c7');
            $(tr).addClass('highlighted_tr');
            $(tr).find('td:first-child').prepend("<i class='fas fa-exclamation-triangle' ></i>");
            $(tr).find('td:first-child').addClass('highlighted_td');
        }
    } else {
        $(tr).css('background-color', '');
        $(tr).css('background-color', '');
        $(tr).removeClass('highlighted_tr');
        $(tr).find('td:first-child').removeClass('highlighted_td');
        $(tr).find('td:first-child').find("i").remove();
    }
}

function confirmbulkdiscountmodel() {
    $('#conforamtion_bulk-discount_popup_modal').modal('show');
}

function confirmbulkdiscount() {
    var chk = checkpercentage_bulkValidation();
    if (chk.length > 0) {
        confirmbulkdiscountmodel();
    } else {
        BulkdiscountApply();
    }
}

function applyBulkDiscounttoVehicle() {
    confirmbulkdiscount();
}

function confirmEmptyDeleteDiscount(ele) {
    deleteCurrentObject = ele;
    $('#unsaved_auto_discount_warning_modal').modal('show');
}

/*
 * Other Functions
 */
function deleteEmptyDiscount() {
    if (deleteCurrentObject == undefined) {
        $('#add-discount-model').modal('hide');
        $('#add-bulk-discount-model').modal('hide');
        return false;
    }
    var container = $(deleteCurrentObject).parent('.discounts-rows').parent('.discounts-rows-parent').attr('id');
    var containerId = '';
    if (container == 'BulkDiscountRowWrapper') {
        containerId = 'add-bulk-discount-model';
    } else {
        containerId = 'add-discount-model';
    }
    $(deleteCurrentObject).parent('.discounts-rows').remove();
    if ($(containerId + ' .discounts-rows').length < 5) {
        $(containerId + ' .add-more-by-count-btn').removeClass('disabled');
    }
}

function confirmapplyBulkDiscounttoVehicle() {
    $('#add-bulk-discount-model .modal-body').addClass('processing');
    globalValues.bulk_discount_validation = bulkValidationCheck('#add-bulk-discount-model');
    if (globalValues.bulk_discount_validation == false) {
        $('#add-bulk-discount-model .modal-body').removeClass('processing');
        return false;
    }
    var showMessage = flate_rate_maximum_validation('#add-bulk-discount-model');
    if (showMessage) {
        $('#warning-max-discount-model').modal('show');
        $('#add-bulk-discount-model .modal-body').removeClass('processing');
        return false;
    }
    $('#add-bulk-discount-model .modal-body').removeClass('processing');
    $('.add-bulk-discount-alert').show();
}

function confirmSingleDiscountDelete(discount_name, uuid) {
    $('#DeleteDiscountPopupName').html(discount_name);
    $('#DeleteSingleDiscountPopupUUid').val(uuid);
    $('#discount_delete_popup_modal').modal('show');
}

function confirmBulkDiscountDelete(discount_name) {
    var result = preventSingleDiscountDeletion('#BulkDiscountRowWrapper');
    if (!result) {
        return false;
    }
    $('#BulkDeleteDiscountPopupName').val(discount_name);
    $('#BulkDeleteDiscountPopupText').html(discount_name);
    $('#bulk_delete_popup_modal').modal('show');
}

function confirmVinDiscountDelete(vinnumber) {
    requestData.VinNumber = vinnumber;
    $('#row_delete_popup_modal').modal('show');
}

function ShowVinNumberinModal(VinNumber, discount_count, finance_option, msrp) {
    $('#discountStatusCodeError').css('display', 'none');
    $("#msrp").remove();
    $('<input type="hidden" id="msrp" name="msrp" value="' + msrp + '">').insertAfter('#ModalDiscountVIN');
    $('#ModalDiscountVIN').html(VinNumber);
    $('#ModalDiscountCount').html(discount_count);
    $('#ModalfinanceOption').html(finance_option);
    if (discount_count != undefined) {
        if (discount_count == 0) {
            $('#DiscountRowWrapper').html(EmptyDiscountRow);
        }
    }
}

/*
 * Validations Functions
 */
// validation of bulk discounts
function bulkDiscountValidation() {
    len_of_row = $('#BulkDiscountRowWrapper .discounts-rows').length
    for (i = 1; i < len_of_row + 1; i++) {
        if ($('#add-bulk-discount-model .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="discount_name"]').val() === '') {
            $('.apply-bulk-discounts-button').addClass('disabled');
        } else {
            $('.apply-bulk-discounts-button').removeClass('disabled');
        }
    }
}

function bulkValidationCheck(container) {
    var bulk_discount_validation = false;
    if (container == '#add-bulk-discount-model') {
        len_of_row = $('#BulkDiscountRowWrapper .discounts-rows').length;
    } else {
        len_of_row = $('#DiscountRowWrapper .discounts-rows').length;
    }
    for (i = 1; i < len_of_row + 1; i++) {
        c_discount_name_val = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="discount_name"]');
        c_flat_rate_val = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="flat_rate"]');
        off_percent_val = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="off_percent"]');
        c_discount_start_date = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="discount_start_date"]');
        c_discount_end_date = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="discount_end_date"]');
        c_discount_uuid = $(container + ' .discounts-rows-parent .discounts-rows:nth-child(' + (i) + ') input[name="discount_uuid"]');
        var discount_name = $(c_discount_name_val).val();
        var flat_rate = $(c_flat_rate_val).val();
        var off_percent = $(off_percent_val).val();
        var start_date = $(c_discount_start_date).val();
        var end_date = $(c_discount_end_date).val();
        var discount_uuid = $(c_discount_uuid).val();
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
        } else if (globalValues.todayDate > start_date && (discount_uuid == '' || discount_uuid == null || discount_uuid == undefined)) {
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
    }
    if (container == '#add-bulk-discount-model') {
        var vin = getIncludeVinRowsData('#add-bulk-discount-model #gridDiscountTableContainer');
        if (0 == vin.length) {
            $('#gridDiscountTableError').html('Please Select One Vehicle For The Discount To Be Applied');
            $('#gridDiscountTableError').addClass('active');
            bulk_discount_validation = false;
        } else {
            $('#gridDiscountTableError').html('');
            $('#gridDiscountTableError').removeClass('active');
            bulk_discount_validation = bulk_discount_validation ? true : false;
        }
    }
    return bulk_discount_validation;
}

function flate_rate_maximum_validation(container) {
    var showMessage = false;
    if (container == '#add-bulk-discount-model') {
        len_of_row = $('#BulkDiscountRowWrapper .discounts-rows').length;
    } else {
        len_of_row = $('#DiscountRowWrapper .discounts-rows').length;
    }
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

function singleDiscountClose() {
    var data = getDiscountRowsData('#add-discount-model');
    if (data.length > 0) {
        deleteCurrentObject = undefined;
        $('#unsaved_auto_discount_warning_modal').modal('show');
    } else {
        $('#add-discount-model').modal('hide');
    }
}

function BulkDiscountClose() {
    var data = getDiscountRowsData('#add-bulk-discount-model');
    if (data.length > 0) {
        deleteCurrentObject = undefined;
        $('#unsaved_auto_discount_warning_modal').modal('show');
    } else {
        $('#add-bulk-discount-model').modal('hide');
    }
}

/*
 * Init Functions
 */

function msrpSlider() {
    $("#slider-range").slider({
        range: true,
        min: 24000,
        max: 90000,
        values: [20000, 90000],
        slide: function(event, ui) {
            $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
        }
    });
    $("#amount").val("$" + 20000 + " - $" + 90000);
}

function handleTabs() {
    if (SearchTabActive.SearchByVehicle) {
        if (requestData['makeCode'] != '' && requestData['modelyear'] != '' && requestData['model'] != '' && 0 < requestData['trim'].length) {
            SearchByAttributes();
        }
    }
    if (SearchTabActive.SearchByVin) {
        if ($('#da_event_vinNumber').val() != '') {
            SearchByVin();
        }
    }
}

function init() {
    msrpSlider();
    getMake();
    requestData = {
        DealerCode: DealerCode,
        makeCode: "",
        financeoption: 1,
        trim: []
    };
    //getModelYear();
    initateTrimMultiSelect('#da_event_trim');
}

function preventSingleDiscountDeletion(container) {
    var data = {};
    var container_discount_count = $(container + " .discounts-rows").length;
    console.log('container_discount_count: ', container_discount_count);
    if (container_discount_count == 1) {
        var uuid = $(container + " .discounts-rows:nth-child(1)").find('input[name="discount_uuid"]').val();
        if (!(uuid == null || uuid == '' || uuid == 0 || uuid == undefined)) {
            $(container + " .discounts-rows").find(".discount-delete-field").removeClass('disable-bulk-discount-delete-btn');
            $(container + " .discounts-rows:nth-child(1)").find(".discount-delete-field").addClass('disable-bulk-discount-delete-btn');
            return false;
        }
    }
    $(container + " .discounts-rows").find(".discount-delete-field").removeClass('disable-bulk-discount-delete-btn');
    return true;
}

/*
 * document ready Functions
 */
$(document).ready(function() {
    requestData = {
        DealerCode: DealerCode,
        financeoption: 1,
        trim: []
    };
    EmptyDiscountRow = '<div class="discounts-rows"><div class="discount-details"><input type="hidden" name="discount_uuid" value=""><div class="discount-space"><span class="discount-name-field"><label>Discount Name</label><input type="text" name="discount_name" maxlength="60" value=""><span class="discount-name-error bulk-error">Please enter discount name</span><!-- <span class="discount-name-edit-field">Edit Name</span> --></span><span class="discount-flat-rate"> <label>Flat Rate <span class="switch"><label><input type="checkbox" name="price_switch" checked><span class="lever"></span></label></span></label><input type="text" name="flat_rate" class="flat_rate" value="" onkeypress="numbervalidate(event)" maxlength="4"> <span class="flat-rate-error bulk-error">Please enter discount amount</span></span><span class="or-text">or</span><span class="off-percent"><label class="off-percent-label">% Off</label><input type="text" name="off_percent" class="off_percent" value="" onkeypress="numbervalidate(event)" maxlength="2" disabled="disabled"><span class="off_percent_error bulk-error">Please enter % value for discount</span></span>';
    EmptyDiscountRow += '<span class="discount-date-field"><label class="discount-start-date-label">Start Date</label><input type="date" name="discount_start_date" class="start_datepicker  discount_start_date" value="" min="' + globalValues.todayDate + '" max="' + globalValues.maxDate + '"><span class="discount_start_date_error bulk-error">Please enter Start Date</span> </span>';
    EmptyDiscountRow += '<span class="discount-date-field"><label class="discount-end-date-label">End Date</label><input type="date" name="discount_end_date" value="" class="end_datepicker discount_end_date" min="' + globalValues.todayDate + '" max="' + globalValues.maxDate + '"><span class="discount_end_date_error bulk-error">Please enter End Date</span></span></div>';
    EmptyDiscountRow += '<div class="saved-discounts"><label class="customCheckBox"><input type="checkbox" name="saved_discount"><span>ADD to Saved Discounts (Only 5 Allowed)</span></label></div></div><div class="discount-delete-field" onclick="confirmEmptyDeleteDiscount(this);"><span><i aria-hidden="true" class="fa fa-trash-alt"></i></span></div></div>';
    $('.gridTableWrapper').hide();
    $('.pleaseSelect').show();

    $('.search-by-vin-toggle').click(function() {
        dataTableOperation('#gridTableContainer');
        $('.mat-tab-label').removeClass('mat-tab-label-active');
        $(this).addClass('mat-tab-label-active');
        $('.search-by-vin').show();
        $('.search-by-vehicle-tab').hide();
        $('#add-bulk-discounts-button').css('display', 'none');
        if (SearchTabActive['SearchByVin'] == false) {
            $('#da_event_vinNumber').val('');
            requestData = {
                DealerCode: DealerCode,
                makeCode: "",
                financeoption: 1,
                trim: []
            };
            $('.dealeradmin-body').css('overflow', 'hidden');
            $('input[name=vin_finance_option][value="1"]').prop('checked', true);
            SearchTabActive['SearchByVin'] = true;
            SearchTabActive['SearchByVehicle'] = false;
            $('.gridTableWrapper').hide();
            $('.pleaseSelect').show();
        }
    });

    $('.search-by-vehicle-toggle').click(function() {
        dataTableOperation('#gridTableContainer');
        $('.search-by-vehicle-tab').show();
        $('#add-bulk-discounts-button').css('display', 'block');
        $('.search-by-vin').hide();
        $('.mat-tab-label').removeClass('mat-tab-label-active');
        $(this).addClass('mat-tab-label-active');
        if (SearchTabActive['SearchByVehicle'] == false) {
            requestData = {
                DealerCode: DealerCode,
                makeCode: "",
                financeoption: 1,
                trim: []
            };
            $('.dealeradmin-body').css('overflow', 'auto');
            resetSearchAttributesForm();
            init();
            SearchTabActive['SearchByVin'] = false;
            SearchTabActive['SearchByVehicle'] = true;
            $('.gridTableWrapper').hide();
            $('.pleaseSelect').show();
        }
    });

    $('#add-discount-model .add-more-by-count-btn').click(function() {
        $('#add-discount-model .discounts-rows-parent').append(EmptyDiscountRow);
        if ($('#add-discount-model .discounts-rows').length == 5) {
            $(this).addClass('disabled');
        }
    });

    $('#add-bulk-discount-model .add-more-by-count-btn').click(function() {
        $('#add-bulk-discount-model .discounts-rows-parent').append(EmptyDiscountRow);
        if ($('#add-bulk-discount-model .discounts-rows').length == 5) {
            $(this).addClass('disabled');
        }
        //preventSingleDiscountDeletion('#BulkDiscountRowWrapper');
    });

    $('#Include-select-all').on('click', function() {
        // Check/uncheck all checkboxes in the table
        var table = $('#gridDiscountTableContainer').DataTable();
        var rows = table.rows({ 'search': 'applied' }).nodes();
        console.log(this.checked);
        $('input[name="bulk_discount_include"]', rows).prop('checked', this.checked);
    });

    // Handle click on checkbox to set state of "Select all" control
    $('#gridDiscountTableContainer tbody').on('change', 'input[type="checkbox"]', function() {
        // If checkbox is not checked
        if (!this.checked) {
            var el = $('#Include-select-all').get(0);
            // If "Select all" control is checked and has 'indeterminate' property
            if (el && el.checked && ('indeterminate' in el)) {
                // Set visual state of "Select all" control 
                // as 'indeterminate'
                //el.indeterminate = true;
                el.checked = false;
            }
        }
    });

    $('.save-and-apply-btn').click(function() {
        var table = $('#gridDiscountTableContainer').dataTable();
        table.fnPageChange(0, true);
        confirmapplyBulkDiscounttoVehicle();
    })

    $('.add-bulk-discount-alert .cancel-btn, #add-bulk-discounts-button').click(function() {
        $('.add-bulk-discount-alert').hide();
    });

    /*
     * document on Functions
     */
    $(document).on('change', '#da_event_make', function() {
        requestData['makeCode'] = $('#da_event_make').val();
        if (requestData['makeCode'] != '') {
            changefooter(requestData['makeCode']);
            $('#SearchByVehicleGoBtn').removeClass('disabled');
            $('#SearchByVehicleGoBtn').addClass('disabled');
            getModelYear();
        } else {
            $('#da_event_model_year').html('<option value=""> Select Year</option>');
            $('#da_event_vehicle').html('<option value=""> Choose a Vehicle</option>');
            //$('#da_event_trim').html('<option value=""> Trim Level - All</option>');
            $('#da_event_drive_type,#da_event_color,#da_event_engine_desc,#da_event_transmission').html('');
            resetTrimMultiSelect();
            $('#SearchByVehicleGoBtn').addClass('disabled');
            requestData['modelyear'] = '';
            requestData['model'] = '';
            requestData['trim'] = [];
            requestData['drive_type'] = '';
            requestData['interior_colour_desc'] = '';
            requestData['eng_desc'] = '';
            requestData['transmission_desc'] = '';
            requestData['msrp_highest'] = '';
            requestData['msrp_lowest'] = '';
        }
    });

    $(document).on('change', '#da_event_model_year', function() {
        requestData['modelyear'] = $('#da_event_model_year').val();
        if (requestData['modelyear'] != '') {
            $('#SearchByVehicleGoBtn').removeClass('disabled');
            $('#SearchByVehicleGoBtn').addClass('disabled');
            getVehicle();
        } else {
            resetTrimMultiSelect();
            $('#da_event_vehicle').html('<option value=""> Choose a Vehicle</option>');
            //$('#da_event_trim').html('<option value=""> Trim Level - All</option>');
            $('#da_event_drive_type,#da_event_color,#da_event_engine_desc,#da_event_transmission').html('');
            $('#SearchByVehicleGoBtn').addClass('disabled');
            requestData['modelyear'] = '';
            requestData['model'] = '';
            requestData['trim'] = [];
            requestData['drive_type'] = '';
            requestData['interior_colour_desc'] = '';
            requestData['eng_desc'] = '';
            requestData['transmission_desc'] = '';
            requestData['msrp_highest'] = '';
            requestData['msrp_lowest'] = '';
        }
    });

    $(document).on('change', '#da_event_vehicle', function() {
        requestData['model'] = $('#da_event_vehicle').val();
        if (requestData['model'] != '') {
            $('#SearchByVehicleGoBtn').removeClass('disabled');
            $('#SearchByVehicleGoBtn').addClass('disabled');
            getTrims();
        } else {
            //$('#da_event_trim').html('<option value=""> Trim Level - All</option>');
            $('#da_event_drive_type,#da_event_color,#da_event_engine_desc,#da_event_transmission').html('');
            resetTrimMultiSelect();
            $('#SearchByVehicleGoBtn').addClass('disabled');
            requestData['model'] = '';
            requestData['trim'] = [];
        }
    });

    $(document).on('change', '#da_event_trim', function() {
        requestData['trim'] = $('#da_event_trim').val();
        if (0 < requestData['trim'].length) {
            $('div#ms-list-1 button').removeClass('disabled');
            getMsrpValues();
        }
        if (requestData['makeCode'] != '' && requestData['modelyear'] != '' && requestData['model'] != '' && 0 < requestData['trim'].length) {
            $('#SearchByVehicleGoBtn').removeClass('disabled');
            $('div#ms-list-1 button').removeClass('disabled');
        } else {
            $('#da_event_drive_type,#da_event_color,#da_event_engine_desc,#da_event_transmission').html('');
            $('#SearchByVehicleGoBtn').removeClass('disabled');
            $('#SearchByVehicleGoBtn').addClass('disabled');
            requestData['trim'] = [];
            requestData['drive_type'] = '';
            requestData['interior_colour_desc'] = '';
            requestData['eng_desc'] = '';
            requestData['transmission_desc'] = '';
            requestData['msrp_highest'] = '';
            requestData['msrp_lowest'] = '';
        }
    });

    $(document).on('change', '#da_event_vinNumber', function() {
        requestData['VinNumber'] = $('#da_event_vinNumber').val();
        if (requestData['VinNumber'] != '') {
            $('#SearchByVinGoBtn').removeClass('disabled');
        } else {
            $('#SearchByVehicleGoBtn').removeClass('disabled');
            $('#SearchByVehicleGoBtn').addClass('disabled');
        }
    });

    $('#slider-range').on("slidechange", function(event, ui) {
        var values = ui.values;
        if (requestData['trim'] != undefined) {
            console.log(values);
            $('#da_event_drive_type,#da_event_color,#da_event_engine_desc,#da_event_transmission').html('');
            getSecondarySelection();
        }
    });

    $(document).on('click', '#discount_delete_popup_modal .confirm-discount-delete-btn', function() {
        deleteSingleDiscountforVin();
    });

    $(document).on('click', '#add-bulk-discount-model .bulk-clear-all-discounts-btn', function() {
        var vin = getIncludeVinRowsData('#add-bulk-discount-model #gridDiscountTableContainer');
        if (0 == vin.length) {
            $('#gridDiscountTableError').html('Please select the vehicle/s for which the discount needs to be removed.');
            $('#gridDiscountTableError').addClass('active');
        } else {
            $('#gridDiscountTableError').html('');
            $('#gridDiscountTableError').removeClass('active');
            $('#clear-all-delete-popup-modal').modal('show');
        }
    });
    $(document).on('click', '#clear-all-delete-popup-modal .clear-all-delete-btn', function() {
        $('#clear-all-delete-popup-modal').modal('hide');
        clearalldiscounts();
    });

    // for discount validation
    $(document).on('change', '[name="Include_all"]', function() {
        $('tr').css('background-color', '');
    });

    $(document).on('change', '[name="price_switch"]', function() {
        $(this).parent().parent().parent().siblings().val(0);
        $(this).parent().parent().parent().parent().siblings().find('input[name=off_percent]').val(0);
    });

    $(document).on('change', '[name="bulk_discount_include"]', function() {
        var checkbox = $(this); // Selected or current checkbox
        validatebulkdiscountPercentage(checkbox);
    });

    $('#confirm-bulk-discount-btn').on('click', function() {
        BulkdiscountApply();
    });

    init();
});

/* init */
$(document).ready(function() {
    lpage.init();
});


/**
 *  Landing page Setup
 * 
 * @var lpage
 *  
 * */
var lpage = {
    init: function() {

        if (localCache.exist('oremake')) {
            localCache.set('oremake', localCache.get('oremake'));
            localCache.set('orevtype', localCache.get('orevtype'));

            $('.brandList .col-sm-4').removeClass('active');
            $('*[data-make="' + localCache.get('oremake') + '"]').parent().addClass('active');
            lpage.color_switch();
        } else {
            localCache.set('oremake', 'chrysler');
            localCache.set('orevtype', 'new');
            lpage.color_switch();
        }

        $('.zipcodePopup_errorlocationInfo span,.zipcodePopup_errorInfo span').text('');
        $('#selectedZipCode,#selectedZipCodePopup').val('');
        //First page onload 
        if (localCache.get('zipCodeEntered') && localCache.get('zipCodeEntered')!='48326') {
            var localZipcode = localCache.get('zipCodeEntered');
            localZipcode = AddZeroPrefixZipcode(localZipcode);
            $('#selectedZipCode').val(localZipcode);
        } else {
            localCache.remove('zipCodeEntered');
            $('#selectedZipCode,#selectedZipCodePopup').val('');
            setTimeout(function () {
                $('#zipCodePopup').modal('show');
            }, 500);            
        }

        $(document).on('click', '.oreBrands', function() {
            var oreBrandsMakes = $(this).data('make');
            localCache.set('oremake', oreBrandsMakes);
            $('.info-img').hide();
            $('.brandList .col-sm-4').removeClass('active');
            $('*[data-make="' + localCache.get('oremake') + '"]').parent().addClass('active');

            $("body").removeClass(function(index, className) {
                return (className.match(/(^|\s)make_\S+/g) || []).join(" ");
            });
            lpage.color_switch();

            lpage.landingRequest();
        });
        // default cache

        if (!localCache.exist('orevtype')) localCache.set('orevtype', 'new');

        var cache_vehicle = localCache.get('oremake');
        var cache_vtype = localCache.get('orevtype');

        //zipcode
        $(document).on('click', '#find_location', function() {
            var zipcode = $('#selectedZipCode').val();
            zipcode = AddZeroPrefixZipcode(zipcode);
            if (zipcode !== '') {
                $('.zip-loader-img').show();
                $('.change-icon-img').hide();
                $('#selectedZipCode').prop('disabled', true);
                lpage.popzipcode();
            } else {
                $('.zipcode_errorInfo').text('Please enter the valid US zip code.');
            }
        });

        //Popup zipcode
        $(document).on('click', '#selectedZipCodePopupBtn', function() {
            $('.zipcodePopup_errorlocationInfo span,.zipcodePopup_errorInfo span').text('');
            var zipcode = $('#selectedZipCodePopup').val();
            zipcode = AddZeroPrefixZipcode(zipcode);
            if (zipcode !== '') {
                $('#zipCodePopup .modal-body').addClass('processing');
                lpage.popzipcode();
            } else {
                $('.zipcodePopup_errorInfo span').text('Please enter the valid US zip code.');
            }
        });

         $(document).on('click', '.ZipCodeDivCls,#selectedZipCode', function() {
            $('.zipcodePopup_errorlocationInfo span,.zipcodePopup_errorInfo span').text('');
            var zipcode =$('#selectedZipCode').val();            
            zipcode = AddZeroPrefixZipcode(zipcode);
            if ( zipcode !== '') {
                $('#selectedZipCodePopup').val(zipcode);
                $('#zipCodePopup .close').removeClass('hidden');
                $('#zipCodePopup').modal('show');
            }
        });

        $(document).on('click', '#selectedZipCodePopup_currentLocation', function() {
            $('.zipcodePopup_errorlocationInfo span').text('');
           // $('#selectedZipCodePopup_currentLocation').addClass('disabled');
            //$('#selectedZipCodePopup_currentLocation').html('<i class="fa fa-map-marker" aria-hidden="true"></i>Processing<i class="fa fa-angle-right"></i>');
            lpage.findGeoPosition();
            //$('#zipCodePopup').modal('hide');
        });

        // Slider
        $(document).on('change', '.land_v_type', function() {
            console.log('.land_v_type');
            cnd_value = $('input[name=v_type]:checked').val();
            if (cnd_value == 'cpo') {
                $('.condition-button-left').attr("target", "v_type_new");
                $('.condition-button-right').attr("target", "v_type_used");
            } else if (cnd_value == 'new') {
                $('.condition-button-left').attr("target", "0");
                $('.condition-button-right').attr("target", "v_type_cpo");
            } else if (cnd_value == 'used') {
                $('.condition-button-left').attr("target", "v_type_cpo");
                $('.condition-button-right').attr("target", "0");
            }
        });

        $('.condition-button-right').click(function() {
            target_value = $(this).attr('target');
            $('label.switch__label[for~=' + target_value + ']').click();
            lpage.new_cpo_used_selection(target_value.replace('v_type_', ''));
        });
        $('.condition-button-left').click(function() {
            target_value = $(this).attr('target');
            $('label.switch__label[for~=' + target_value + ']').click();
            lpage.new_cpo_used_selection(target_value.replace('v_type_', ''));
        });

        // Default vehicle type Checked 
        $("#v_type_" + cache_vtype).prop("checked", true);

        //new/cpo/used Selection        
        $(document).on('click', '.land_v_type', function() {
            // var cond_v_type = $(this).val();
            //   lpage.new_cpo_used_selection(cond_v_type);
        });

        /* To Hide any Displayed Element */
        $(document).on('click', '[data-hide]', function() {
            $($(this).data('hide')).hide();
        });
        /* To display any Hidden Element */
        $(document).on('click', '[data-show]', function() {
            $($(this).data('show')).show();
        });

        $(window).scroll(function() {
            var sc = $(window).scrollTop()
            if (sc > 0) {
                $(".header-scroll").addClass("small")
            } else {
                $(".header-scroll").removeClass("small")
            }
        });

        $('.switch form label').keypress(function(e) {
            if (e.keyCode == 13) {
                $('#' + $(this).attr('for')).click();
            }
        });

        $('a, label, button').on('focus', function(e) {
            var this_class = $(this);
            $(window).keyup(function(e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if (code == 9) {
                    $(this_class).addClass('focus_by_tab');
                }
            });
        });
        $('a, label, button').on('focusout', function(e) {
            var this_class = $(this);
            $(window).keyup(function(e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if (code == 9) {
                    $(this_class).removeClass('focus_by_tab');
                }
            });
        });
    },
	color_switch: function(){
		switch (localCache.get("oremake")) {
                case "chrysler":
                    $("body").addClass("make_chrysler");
                    break;
                case "dodge":
                    $("body").addClass("make_dodge");
                    break;
                case "fiat":
                    $("body").addClass("make_fiat");
                    break;
                case "jeep":
                    $("body").addClass("make_jeep");
                    break;
                case "ram":
                    $("body").addClass("make_ram");
                    break;
                case "alfa_romeo":
                    $("body").addClass("make_alfa_romeo");
                    break;
            }
	},
    new_cpo_used_selection: function(cond_v_type) {
        if (cond_v_type == "new") {
            localCache.set('orevtype', cond_v_type);
            $('.cardHeader, .cardFooter').show();
            $('.info-img').hide();
        } else if (cond_v_type == "cpo") {
            localCache.set('orevtype', cond_v_type);
            $('.info-img').show();
            $('.cardHeader, .cardFooter').hide();
        } else {
            localCache.set('orevtype', cond_v_type);
            $('.info-img').show();
            $('.cardHeader, .cardFooter').hide();
        }
        lpage.landingRequest();
    },
    zipcodefilled: function() {
        $('.ore_user_zipcode, #zipCodeInfo').html(localCache.get('zipCodeEntered'));
        $('#dealerZipCode').val(localCache.get('zipCodeEntered'));
        lpage.findDealers();
    },
    loaderShow: function(divid) {
        // $(divid).show();
        $('.ore_landing_models').addClass('tempo');
        $('.listCont').addClass('tempo');
    },
    loaderHide: function(divid) {
        // $(divid).hide();
        $('.ore_landing_models').removeClass('tempo');
        $('.listCont').removeClass('tempo');
    },
    isZip: function() {
        if (localCache.exist('zipCodeEntered')) return 'AVAIL';
        else return 'UNAVAIL'
    },
    dd: function(str) {
        console.log(str);
    },
    /***
     * Zipcode Provided in Landing Page
     * 
     *  */
    popzipcode: function() {
        //validation.digitValidate(5, '#selectedZipCode', '.zipcode_errorInfo', 'Please Enter a Valid Zip Code');
        validation.digitValidate(5, '#selectedZipCodePopup', '.zipcodePopup_errorInfo span', 'Please Enter a Valid Zip Code');

    },
    invChat: function(sessionid, type, attributes) {
        ajax.promise('baseCarNow', 'post', JSON.stringify("session=" + sessionid + "&type=" + type + "&attributes=" + attributes));

    },
    /***
     *  Geolocation in Landing Page
     * 
     *  */
    findGeoPosition: function() {
        $('.zipcodePopup_errorlocationInfo span').html('');
        var currentLocation = {}
        if (navigator.geolocation) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(lpage.showPosition, lpage.showError);
            } else {
                lpage.dd("Geolocation is not supported by this browser.");
                // alert('Geolocation is not supported by this browser so please enter Zip Code to search the latest inventory in your area.'); 
                var title = 'Alert!';
                var contents = 'Geolocation is not supported by this browser so please enter Zip Code to search the latest inventory in your area.';
                //AlertMessage(title, contents);
                $('.zipcodePopup_errorlocationInfo span').html(contents);
                //lpage.landingRequest();
            }
        }
        //$('#selectedZipCodePopup_currentLocation').removeClass('disabled');
        //$('#selectedZipCodePopup_currentLocation').html('<i class="fa fa-map-marker" aria-hidden="true"></i>Use current location<i class="fa fa-angle-right"></i>');            
                
    },
    showError: function(error) {
        var err = ' so please enter Zip Code to search the latest inventory in your area.';
        switch (error.code) {
            case error.PERMISSION_DENIED:
                lpage.dd("You denied the request for Geolocation.");
                //alert('Location is disabled. Please enter Zip Code to search the latest inventory in your area.'); 
                var title = 'Alert!';
                var contents = 'Location is disabled. Please enter Zip Code to search the latest inventory in your area.';
                //AlertMessage(title, contents);
                $('.zipcodePopup_errorlocationInfo span').html(contents);
                //lpage.landingRequest();
                break;
            case error.POSITION_UNAVAILABLE:
                lpage.dd("Location information is unavailable.");
                var title = 'Alert!';
                var contents = 'Location information is unavailable ' + err;
                // alert('Location information is unavailable '+err); 
                //AlertMessage(title, contents);
                $('.zipcodePopup_errorlocationInfo span').html(contents);
                //lpage.landingRequest();
                break;
            case error.TIMEOUT:
                lpage.dd("The request to get user location timed out.");
                //alert('The request to get user location timed out '+err);
                title = 'Alert!';
                contents = 'The request to get user location timed out ' + err;
                //AlertMessage(title, contents);
                $('.zipcodePopup_errorlocationInfo span').html(contents);
                //lpage.landingRequest();
                break;
            case error.UNKNOWN_ERROR:
                lpage.dd("An unknown error occurred.");
                //  alert('An unknown error occurred '+err); 
                var title = 'Alert!';
                var contents = 'An unknown error occurred ' + err;
                //AlertMessage(title, contents);
                $('.zipcodePopup_errorlocationInfo span').html(contents);
                //lpage.landingRequest();
                break;
        }
    },
    showPosition: function(position) {
        var location = [];
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;

        localCache.set('latitude', latitude);
        localCache.set('longitude', longitude);
        ajax.promise('find_zip_by_cord', 'post', JSON.stringify({ latitude: latitude, longitude: longitude }));
        return true;

    },
    /***
     *  Result Loaded in Landing Page
     * 
     *  */
    landingRequest: function() {

        if (localCache.exist('oremake')) {
            var make = localCache.get('oremake');
            var vehicle_condition = localCache.get('orevtype');
        } else {
            var make = $('.brandList>div.active>.oreBrands').attr('data-make');
            var vehicle_condition = $('.brandFrame .onOffSlider>label:visible').attr('id');
        }


        if (!localCache.exist('orevtype')) var vehicle_condition = localCache.set('orevtype', 'new');
        else var vehicle_condition = localCache.get('orevtype');

        // For Geolocation Validation 
        if (!localCache.exist('zipCodeEntered')) {
            //localCache.set('zipCodeEntered', '48326');
            //$('#selectedZipCode').val(48326);
            //$('#dealerZipCode').val(48326);
            $('.zipcodePopup_errorlocationInfo span,.zipcodePopup_errorInfo span').text('');
            $('#selectedZipCodePopup').val('');
            setTimeout(function () {
                $('#zipCodePopup').modal('show');
            }, 2500); 
            return true;
        } else {
            $('#selectedZipCode').val(localCache.get('zipCodeEntered'));
            $('#dealerZipCode').val(localCache.get('zipCodeEntered'));
        }

        //Getting Values
        zipcode = $('#selectedZipCode').val();

        lpage.vehicleTypeChoser(make, vehicle_condition, zipcode);
    },
    /***
     *  CPO / NEW
     * 
     *  */
    vehicleTypeChoser: function(make, vechType, zipcode) {
        if (vechType == 'new') {
            //lpage.NewVehicleLoader(make, vechType, zipcode);
        } else if (vechType == 'cpo' || vechType == 'used') {
            if (lpage.isZip() == 'AVAIL') {
                zipCodeEntered = localCache.get('zipCodeEntered');
                if (!localCache.exist('latitude') || !localCache.exist('longitude')) {
                    lpage.zip_to_cord();
                    lpage.CpoVehicleLoader(make, vechType);
                } else {
                    lpage.CpoVehicleLoader(make, vechType);
                }
                $('.ore_user_zipcode').html(zipCodeEntered);
            } else {
                lpage.findGeoPosition();
                lpage.CpoVehicleLoader(make, vechType);
            }
        }
    },
    /***
     *  FindDealers in Landing Page
     * 
     *  */
    findDealers: function() {
        latitude = localCache.get('latitude');
        longitude = localCache.get('longitude');
        zipcode = localCache.get('zipCodeEntered');

        if (!localCache.exist('dealers_' + zipcode)) {
            if ((latitude != "" || latitude != undefined) && (longitude != "" || longitude != undefined)) {
                ajax.promise('find_cpo_dealers', 'post', JSON.stringify({ latitude: latitude, longitude: longitude, zipcode: zipcode, radius: 25 }));
            } else {
                lpage.findGeoPosition();
                ajax.promise('find_cpo_dealers', 'post', JSON.stringify({ latitude: latitude, longitude: longitude, zipcode: zipcode, radius: 25 }));
            }
        }
    },
    /***
     *  New Dealers in Landing Page
     * 
     *  */
    NewVehicleLoader: function(make, vechType, zipcode) {
        //$('.cardHeader, .cardFooter').show();
        lpage.findDealers();
        zipcode = zipcode.replace(/['"]+/g, '');
        ajax.promise('landing_new_vehicle/' + make + '/' + vechType + '/' + zipcode, 'get', JSON.stringify({}));
    },
    /***
     *  CPO Dealers in Landing Page
     * 
     *  */
    CpoVehicleLoader: function(make, vechType) {
        //$('.cardHeader, .cardFooter').show();
        latitude = localCache.get('latitude');
        longitude = localCache.get('longitude');
        zipcode = localCache.get('zipCodeEntered');
        //result = localCache.get('dealers_'+zipcode); 
        //ajax.promise('landing_new_vehicle/'+make+'/'+vechType,'get',JSON.stringify({}));

        // ajax.promise('landing_cpo_vehicle','post',JSON.stringify({make:make,vechType:vechType,zipcode:zipcode}));
    },
    /***
     *  using Zipcode to cordinated find in Landing Page
     * 
     *  */
    zip_to_cord: function() {
        if (localCache.exist('zipCodeEntered')) {
            zipcode = localCache.get('zipCodeEntered');
            ajax.promise('find_cord_by_zip', 'post', JSON.stringify({ zipcode: zipcode }));
        }
    },
    number_format: function(number, decimals, dec_point, thousands_sep) {
        // Strip all characters but numerical ones.
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    },

    imgLoader: function() {
        var errorURL = "https://d11p9i1nddg3dz.cloudfront.net/jellybeans/noimage.png";

        $('[data-original]').each(function() {
            var $this = $(this),
                src = $this.attr('data-original');

            var img = new Image();
            img.onload = function() { $this.attr('src', src); }
            img.onerror = function() { $this.attr('src', errorURL); }
            img.src = src;
        });
    }
}

/**
 *  Ajax Setup
 * 
 * @var ajax
 *  
 * */
var ajax = {
    promise: function(url, requestType, data) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var tempurl = url;
        if (tempurl != 'baseCarNow') { lpage.loaderShow('.loader'); } else { lpage.loaderHide('.loader'); }
        if (tempurl == 'sniRightSideLazy') url = 'sniRightSide';

        if (requestType == 'get') {
            var jqxhr = $.get(url, function(result) {
                $('.ore_landing_models').html(result);
                $('h5.availableModels span.brandName').html(localCache.get('oremake'));
                lpage.zipcodefilled();
                lpage.imgLoader();
                lpage.loaderHide('.loader');
            }).fail(function(response, textStatus, jqXHR) {
                ajax.failure(url, textStatus);
                lpage.loaderHide('.loader');
            });
        } else {
            $.when($.ajax({ url: url, type: requestType, xhrFields: { withCredentials: true }, data: JSON.parse(data) }))
                .done(function(result, textStatus, jqXHR) {
                    if (tempurl == 'sniRightSideLazy') ajax.success(tempurl, result);
                    else ajax.success(url, result);
                }).fail(function(response, textStatus, jqXHR) {
                    ajax.failure(url, textStatus);
                    lpage.loaderHide('.loader');
                });
        }
    },
    success: function(url, result) {
        switch (url) {
            case "find_cord_by_zip":
                localCache.set('latitude', result['latitude']);
                localCache.set('longitude', result['longitude']);
                lpage.zipcodefilled();
                break;
            case "validate_zipcode":
                if (result['status'] == 'available') {
                    var zipCodeEntered = result['zipcode'];
                    localCache.set('zipCodeEntered', zipCodeEntered);
                    localCache.remove('latitude');
                    localCache.remove('longitude');
                    $('.change-icon-img').show();
                    $('.zip-loader-img').hide();
                    $('#selectedZipCode').prop('disabled', false);
                    $('.zipcode_errorInfo').text('');
                    $('#selectedZipCode').val(localCache.get('zipCodeEntered'));
                    $('.ore_user_zipcode').html(zipCodeEntered);
                    $('#zipCodePopUp .custom-close').show();
                    $('#zipCodePopup').modal('hide');
                    $('#zipCodePopUp').removeData();
                    $('.container.body').removeClass('blur');
                    //snipage.zip_to_cord();
                    //snipage.zipcodefilled();
                    //lpage.landingRequest();
                } else {
                    $('.change-icon-img').show();
                    $('.zip-loader-img').hide();
                    $('#selectedZipCode').prop('disabled', false);
                    $('.zipcodePopup_errorInfo span').text('Please enter the valid US zip code.');
                    return;
                }
                break;
            case "find_zip_by_cord":
                if (result['status'] == 'success') {
                    $('#zipCodePopup').modal('hide');
                    var zipcode = result['message'];
                    zipcode = zipcode.replace('"', '');
                    zipcode = zipcode.replace('"', '');
                    localCache.set('zipCodeEntered', zipcode);
                    $('#selectedZipCode').val(zipcode);
                    //lpage.landingRequest();
                } else {
                    //alert('This application supports US locations only. Please enter a valid zip code');
                    var title = "Alert!";
                    var contents = 'This application supports US locations only. Please enter a valid zip code';
                    //AlertMessage(title, contents);
                    $('.zipcodePopup_errorlocationInfo span').html(contents);
                   // lpage.landingRequest();
                }
                break;
            case "landing_cpo_vehicle":
                $('.ore_landing_models').html(result);
                $('.cardHeader, .cardFooter').hide();
                $('h5.availableModels span.brandName').html(localCache.get('oremake'));
                lpage.zipcodefilled();
                lpage.loaderHide('.loader');
                break;
            case "landing_new_vehicle":
                $('.ore_landing_models').html(result);
                $('h5.availableModels span.brandName').html(localCache.get('oremake'));
                lpage.zipcodefilled();
                lpage.imgLoader();
                lpage.loaderHide('.loader');
                break;
            case "find_cpo_dealers":
                localCache.set('dealers_' + localCache.get('zipCodeEntered'), 'available');
                break;
            case "find_dealers_by_zipcode_radius":
                leftFilterLoad(result);
                break;
            case "baseCarNow":
                console.log("baseCarNow Submit");
                lpage.loaderHide('.loader');
                break;
            case "leadSubmit":
                console.log("Lead Succesfully Submit");
                break;
        }
    },
    failure: function(url, textStatus) {
        switch (url) {
            case "validate_zipcode":
                $('.zipcodePopup_errorInfo span').text('Please enter the valid US zip code.');
            break;
        }
        lpage.dd("Error: " + url);
        lpage.dd("Status: " + textStatus);
    },
}

/**
 *  validation Setup
 * 
 * @var validation
 *  
 * */
var validation = {
    digitValidate: function(digit_counts, field_id, err_msg_span, err_msg) {
        $cf = $(field_id);
        ValidateField = $cf.val().trim();
        ValidateField = AddZeroPrefixZipcode(ValidateField);
        ValidateField = ValidateField.replace(/[^0-9]/g, '');
        if (ValidateField.length != digit_counts) {
            $(err_msg_span).html(err_msg);
            $(field_id).val('');
            $(field_id).focus();
            return false;
        } else {
            $(err_msg_span).html('');
            var zipCodeEntered = $(field_id).val().trim();
            zipCodeEntered = AddZeroPrefixZipcode(zipCodeEntered);
             localCache.set('zipCodeEntered', zipCodeEntered);
                    localCache.remove('latitude');
                    localCache.remove('longitude');
                    $('.change-icon-img').show();
                    $('.zip-loader-img').hide();
                    $('#selectedZipCode').prop('disabled', false);
                    $('.zipcode_errorInfo').text('');
                    $('#selectedZipCode').val(localCache.get('zipCodeEntered'));
                    $('.ore_user_zipcode').html(zipCodeEntered);
                    $('#zipCodePopUp .custom-close').show();
                    $('#zipCodePopup').modal('hide');
                    $('#zipCodePopUp').removeData();
                    $('.container.body').removeClass('blur');
                   lpage.loaderHide('.loader'); 

           // ajax.promise('validate_zipcode', 'post', JSON.stringify("zipcode=" + zipCodeEntered));
           // lpage.loaderHide('.loader');

            
        }
    }
}

/**
 *  Cache Setup
 * 
 * @var localCache
 *  
 * */
var localCache = {
    remove: function(key) {
        localStorage.removeItem(key);
    },
    exist: function(key) {
        return localStorage.getItem(key) !== null;
    },
    get: function(key) {
            if(key == 'zipCodeEntered'){
                return localStorage.getWithExpiry(key);
            } else{
                return localStorage.getItem(key);
            }        
    },
    set: function(key, value) { 
           if(key == 'zipCodeEntered'){ 
               localCache.setWithExpiry(key, value, 1800000); 
               // 1000 * 5 (5 seconds) 
               // 1800000 - 30 mintues
           } else{
                 localStorage.setItem(key, value);
           }
       
        return true;
    },
    setWithExpiry: function(key, value, ttl) {
            const now = new Date()
            const item = {
                value: value,
                expiry: now.getTime() + ttl
            }
            localStorage.setItem(key, JSON.stringify(item))
    }, 
    getWithExpiry: function(key){
            const itemStr = localStorage.getItem(key)

            // if the item doesn't exist, return null
            if (!itemStr) {
                return null
            }

            const item = JSON.parse(itemStr)
            const now = new Date()

            // compare the expiry time of the item with the current time
            if (now.getTime() > item.expiry) {
                // If the item is expired, delete the item from storage
                // and return null
                localStorage.removeItem(key)
                return null
            }
            return item.value
    }
};


function AddZeroPrefixZipcode(zipcode){
    str_zip_length = zipcode.length;
    if(0 < str_zip_length && 4 == str_zip_length ){
        zipcode = '0'+ zipcode;
    }
    return zipcode;
}


function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function AlertMessage(title, contents) {
    $.alert({
        buttons: {
            OK: {
                btnClass: 'alert-button',
                action: function() {}
            }
        },
        title: '<span style="font-size: 18px !important; font-family:Helvetica Neue,Helvetica,Arial,sans-serif;">' + title + '</span>',
        boxWidth: '35%',
        backgroundDismiss: false,
        bgOpacity: .1,
        useBootstrap: false,
        content: contents,
        draggable: false,
    });
}

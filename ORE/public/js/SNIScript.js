/* init */
$(document).ready(function() {
    snipage.init();
    initSliders.init();
});


var initSliders = {
    init: function() {
        /* if($('#params_vechType').val() == 'cpo' || $('#params_vechType').val() == 'used'){
            var yearInfo_hidden = parseInt($('#yearInfo_hidden').val());
            var syearInfo_hidden = parseInt($('#syear_hidden').val());
            snipage.rangesliders('.yearInfo',yearInfo_hidden,syearInfo_hidden,yearInfo_hidden,1, 'onload');
        } */
        snipage.rangesliders('.searchWithIn', 25, 25, 150, 25, 'onload');
        //snipage.rangesliders('.Towing',50,0,50000,1, 'onload');
    }
}

$.urlParam = function(name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results == null) {
        return null;
    } else {
        return decodeURI(results[1]) || 0;
    }
}
/**
 *  Landing page Setup
 *
 * @var lpage
 *
 * */

var snipage = {

    init: function() {
        //snipage.color_switch();
        $('.ore_ext_button').hide();
        var current_tier = $('#tier').val();

        var hiddealerZipCode = $('#dealerZipCode').val();
        hiddealerZipCode = AddZeroPrefixZipcode(hiddealerZipCode);
        if (hiddealerZipCode == '') {
            if (localCache.get('zipCodeEntered')) {
                var zipcode = localCache.get('zipCodeEntered');
                zipcode = AddZeroPrefixZipcode(zipcode);
                $('#dealerZipCode').val(zipcode);
                $('#zipCodeInfo').html(zipcode);
                $('#selectedZipCode').val(zipcode);
            } else {
                $('#zipCodePopUp #selectedZipCode').val('');
                $('#zipCodePopUp .zipcode_errorInfo').html('');
                setTimeout(function() {
                    $('#zipCodePopUp .custom-close').removeClass('hidden');
                    $('#zipCodePopUp .custom-close').addClass('hidden');
                    $('#zipCodePopUp').modal('show');
                }, 2500);
            }
        } else {
            localCache.set('zipCodeEntered', hiddealerZipCode)
            $('#dealerZipCode').val(hiddealerZipCode);
            $('#selectedZipCode').val(hiddealerZipCode);
            $('#zipCodeInfo').html(hiddealerZipCode);
            $('#selectedZipCode').val(hiddealerZipCode);
        }

        $(document).on('click', '#zipCodePopupAnchor', function() {
            $('#zipCodePopUp .custom-close').removeClass('hidden');
            $('#zipCodePopUp #selectedZipCode').val($('#zipCodeInfo').text());
            $('#zipCodePopUp .zipcode_errorInfo').html('');
        });

        $(window).on('load resize scroll', function() {
            isScrolledIntoView('footer');
        });

        $(document).on('click', '.l_q_mpg', function() {
            $('.disclaimerMessage').css("display", "block");
        });
        $(document).on('click', '.closeDisclaimer', function() {
            $('.disclaimerMessage').css("display", "none");
        });
        $(document).on('click', '.quickinfo', function() {
            $('.disclaimerMessage').css("display", "none");
        });



        //scrolltop_bottom
        $(".down").click(function() {
            $('html, body').animate({
                scrollTop: $("footer").offset().top
            }, 1500);
        });
        $(".up").click(function() {
            $('html, body').animate({
                scrollTop: $("header").offset().top
            }, 1000);
        });
        $(window).scroll(function() {
            if ($(document).scrollTop() <= 200) {
                $('.up').hide();
                $('.down').show();
            } else {
                $('.down').hide();
                $('.up').show();
            }
        });

        if (current_tier == 't3') {
            $('#resultsPage header').hide();
            $('.dealersBlock').hide();
            $('.searchWithInBlock').hide();
            $('.link_change_vehicle').hide();
            $('.top-logo-vehicle').attr("href", "javascript: void(0);");
        }
        if (current_tier == 't1') {
            $('.link_change_vehicle').hide();
            $('.top-logo-vehicle').attr("href", "javascript: void(0);");
        }

        // SNI initial Loader
        snipage.left_filter_reset();
        snipage.sniLoader('onload', 25);

        //Zipcode
        $(document).on('click', '#find_location', function() {
            snipage.popzipcode();
        });

        snipage.blockToggle();
        snipage.scrollTopBottom();
        snipage.showhide();
        window.resetFilters = function() {
            snipage.left_filter_reset();
            snipage.resetFilterURL();
            snipage.sniLoader('onload', 25);
            snipage.scrollTopBottom();
        }

        var params_dealercode = '';
        var dealercode = $.urlParam('dealercode'); ///urlParams.get('dealercode');
        if (dealercode != '' && dealercode !== undefined && dealercode != null) params_dealercode = dealercode;


        // All Left Controls
        $(document).on('click', '.formFilter .FilterSelectEvent', function() {
            var lazy_data = $('.formFilter').serialize();
            var zipcode = localCache.get('zipCodeEntered');
            $('#lazyExactLimit').val(0);
            $('#lazyPartialLimit').val(0);
            $('#lazyType').val('e');


            var params_year = $('#params_year').val(),
                params_catid = $('#params_catid').val(),
                params_subcatid = $('#params_subcatid').val(),
                params_vechType = $('#params_vechType').val(),
                params_modelname = $('#params_modelname').val(),
                params_make = $('#params_make').val(),
                dealerZipCode = localCache.get('zipCodeEntered'),
                searchWithIn_hidden = $('#searchWithIn_hidden').val(),
                sortBy = $('#LowToHigh_Sni').val(),
                startprice_digit = $('#startPrice_hidden').val(),
                tier = $('#tier').val(),
                maxprice_digit = $('#maxPrice_hidden').val();



            var priceRange_hidden = parseInt(startprice_digit) + ',' + parseInt(maxprice_digit);

            lazy_data += '&lazy_type=e&lazy_limit=0';
            lazy_data += '&area1';
            lazy_data += '&params_year=' + params_year;
            lazy_data += '&params_catid=' + params_catid;
            lazy_data += '&params_subcatid=' + params_subcatid;
            lazy_data += '&params_vechType=' + params_vechType;
            lazy_data += '&params_modelname=' + params_modelname;
            lazy_data += '&params_make=' + params_make;
            lazy_data += '&dealerZipCode=' + dealerZipCode;
            lazy_data += '&searchWithIn_hidden=' + searchWithIn_hidden;
            lazy_data += '&priceRange_hidden=' + priceRange_hidden;
            lazy_data += '&tier=' + tier;
            lazy_data += '&name=firstround';
            lazy_data += '&maxprice=' + parseInt(maxprice_digit);
            lazy_data += '&startprice=' + parseInt(startprice_digit);
            lazy_data += '&zipcode=' + dealerZipCode;
            ajax.promise('sniRightSide', 'post', JSON.stringify($('.formFilter').serialize(lazy_data) + "&sortBy=" + sortBy));
        });

        // All Secondary Controls
        $(document).on('change', '.sniSortBy', function() {
            var sortBy = $(this).val();
            var lazy_data = $('.formFilter').serialize();
            $('#lazyExactLimit').val(0);
            $('#lazyPartialLimit').val(0);
            $('#lazyType').val('e');

            var params_year = $('#params_year').val(),
                params_catid = $('#params_catid').val(),
                params_subcatid = $('#params_subcatid').val(),
                params_vechType = $('#params_vechType').val(),
                params_modelname = $('#params_modelname').val(),
                params_make = $('#params_make').val(),
                dealerZipCode = localCache.get('zipCodeEntered'),
                searchWithIn_hidden = $('#searchWithIn_hidden').val(),
                startprice_digit = $('#startPrice_hidden').val(),
                tier = $('#tier').val(),
                maxprice_digit = $('#maxPrice_hidden').val();

            var priceRange_hidden = parseInt(startprice_digit) + ',' + parseInt(maxprice_digit);

            lazy_data += '&lazy_type=e&lazy_limit=0';
            lazy_data += '&area2';
            lazy_data += '&params_year=' + params_year;
            lazy_data += '&params_catid=' + params_catid;
            lazy_data += '&params_subcatid=' + params_subcatid;
            lazy_data += '&params_vechType=' + params_vechType;
            lazy_data += '&params_modelname=' + params_modelname;
            lazy_data += '&params_make=' + params_make;
            lazy_data += '&dealerZipCode=' + dealerZipCode;
            lazy_data += '&searchWithIn_hidden=' + searchWithIn_hidden;
            lazy_data += '&priceRange_hidden=' + priceRange_hidden;
            lazy_data += '&tier=' + tier;
            lazy_data += '&dealercode=' + params_dealercode;
            lazy_data += '&name=secondround';
            lazy_data += '&maxprice=' + parseInt(maxprice_digit);
            lazy_data += '&startprice=' + parseInt(startprice_digit);
            lazy_data += '&zipcode=' + dealerZipCode;

            ajax.promise('sniRightSide', 'post', JSON.stringify($('.formFilter').serialize(lazy_data) + "&sortBy=" + sortBy));
        });

        // Load More
        $(document).on('click', '.ore_lazyload', function() {
            var lazy_data = $('.formFilter').serialize();
            var lazy_limit = 0;
            var lazy_type = 'e'; // Default Value
            lazy_type = $(this).data('lazytype');
            $('#lazyType').val(lazy_type);



            if (lazy_type == 'e') {
                lazy_limit = $('#lazyExactLimit').val();
                $('#lazyExactLimit').val(parseInt(lazy_limit) + 10);
            } else {
                lazy_limit = $('#lazyPartialLimit').val();
                $('#lazyPartialLimit').val(parseInt(lazy_limit) + 10);
            }



            var params_year = $('#params_year').val(),
                params_catid = $('#params_catid').val(),
                params_subcatid = $('#params_subcatid').val(),
                params_vechType = $('#params_vechType').val(),
                params_modelname = $('#params_modelname').val(),
                params_make = $('#params_make').val(),
                dealerZipCode = localCache.get('zipCodeEntered'),
                searchWithIn_hidden = $('#searchWithIn_hidden').val(),
                startprice_digit = $('#startPrice_hidden').val(),
                tier = $('#tier').val(),
                maxprice_digit = $('#maxPrice_hidden').val();
            maxprice_digit = $('#maxPrice_hidden').val();
            sortBy = $('.sniSortBy').val();

            var priceRange_hidden = parseInt(startprice_digit) + ',' + parseInt(maxprice_digit);

            lazy_data += '&lazy_type=' + lazy_type;
            lazy_data += '&lazy_limit=' + lazy_limit;
            lazy_data += '&sortBy=' + sortBy;
            lazy_data += '&name=thirdround';
            lazy_data += '&maxprice=' + parseInt(maxprice_digit);
            lazy_data += '&startprice=' + parseInt(startprice_digit);
            lazy_data += '&zipcode=' + dealerZipCode;

            ajax.promise('sniRightSideLazy', 'post', JSON.stringify(lazy_data));
        });
        $(window).scroll(function() {
            var sc = $(window).scrollTop()
            if (sc > 0) {
                $(".header-scroll").addClass("small")
            } else {
                $(".header-scroll").removeClass("small")
            }
        });

        $('[data-toggle="tooltip"]').tooltip();

        // Image Zoom
        $(document).on('click', '.zoomIcon', function() {
            snipage.ZoomImgLoader($(this));
        });

        $('.captch_loader .btn, .ore_ext_button .ore_lazyload').addClass('theme');

        $(window).scroll(function() {
            var sc = $(window).scrollTop()
            if (sc > 0) {
                $(".header-scroll").addClass("small")
            } else {
                $(".header-scroll").removeClass("small")
            }
        });

        $('.stylePicker .floatingIcon').click(function() {
            $('.stylePicker').toggleClass('active');
            $(this).toggleClass('fa-arrow-circle-up fa-arrow-circle-down');
        });
        $('.stylePicker ul li').click(function() {
            var APP = $('#APP_URL').val();
            //if($(this).is(':first-child') || $(this).is(':last-child') || $(this).hasClass('done'))
            var cPath = APP + "css/" + $(this).attr('data-style-sheet').trim();
            /*else{
                var siteName = $(this).attr('data-style-sheet').trim(),
                cPath = 'https://www.' + siteName + '.com/etc/designs/fca-brands/clientlibs/' + siteName + '/global.css';
            }*/
            $(this).parent().children('.active').removeClass('active');
            $(this).addClass('active');
            $('#styleLoader').attr('href', cPath);
        });

        $('.searchWithIn').append('<span class="dragging-span"></span>');
        $('.imageFrame i').after('<span class="zoomIcon-clone"></span>');
        $('body').on('click', '.zoomIcon-clone', function() {
            $(this).siblings('i').click();
        });

        $('body').on('click', '.resetFilterData', function() {
            $('.checkbox-counter').text(0);
            $('.checkbox-counter').hide();
        });


        var params_year1 = $('#params_year').val(),
            params_modelname1 = $('#params_modelname').val();
        if ((params_modelname1 == 'grand-caravan' && params_year1 == 2020) || (params_modelname1 == 'journey' && params_year1 == 2020)) {
            $('#notAvailable-popup').modal('show');
        } else {
            $('#notAvailable-popup').modal('hide');
        }
    },

    left_filter_reset: function() {
        $('#allVehicles, #chrysler, .filterAttrs .customCheckBox input[type="checkbox"]:checked').trigger('click');
        $('.ore_tabmiles').html('25');
        $('#searchWithIn_hidden').val('25');
        snipage.rangesliders('.searchWithIn', 25, 25, 150, 25, 'onload');
        $('form.formFilter').find('.FilterSelectEvent').prop('checked', false);
    },

    color_switch: function() {
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
    invChat: function(sessionid, type, attributes) {
        ajax.promise('baseCarNow', 'post', JSON.stringify("session=" + sessionid + "&type=" + type + "&attributes=" + attributes));

    },
    /***
     * Zipcode Provided in Landing Page
     *
     *  */
    popzipcode: function() {
        validation.digitValidate(5, '#selectedZipCode', '.zipcode_errorInfo', 'Please Enter a Valid Zip Code')

    },
    addCommas: function(nStr) {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    },
    /***
     *  using Zipcode to cordinated find in Landing Page
     *
     *  */
    zip_to_cord: function() {
        if (localCache.exist('zipCodeEntered')) {
            zipcode = localCache.get('zipCodeEntered');
            zipcode = AddZeroPrefixZipcode(zipcode);
            ajax.promise('find_cord_by_zip', 'post', JSON.stringify({ zipcode: zipcode }));
        }
    },
    scrollTopBottom: function() {
        /* Scroll Up */
        window.scrollUp = function() {
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        }
        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                $('.scrollUpBtn').fadeIn();
            } else {
                $('.scrollUpBtn').fadeOut();
            }
        });
        /* Scroll Up */
    },
    showhide: function() {

        /* To Hide any Displayed Element */
        $(document).on('click', '[data-hide]', function() {
            $($(this).data('hide')).hide();
        });
        /* To display any Hidden Element */
        $(document).on('click', '[data-show]', function() {
            $($(this).data('show')).show();
        });
    },
    blockToggle: function() {
        /* DropDown Script */
        $(document).on('click', '.dOpener', function() {
            if ($(this).next(".cDropDown").is(":visible") != true) {
                $(this).find("i").addClass("glyphicon-menu-up").removeClass('glyphicon-menu-down');
                $(this).next(".cDropDown").slideDown();

            } else {
                $(this).find("i").addClass("glyphicon-menu-down").removeClass('glyphicon-menu-up');
                $(this).next(".cDropDown").slideUp();
            }
        });
        $(document).on('click', '.see-all-block .see-less-toggle', function() {
            $('.cDropDown.ore_dealers ul').toggleClass('active');
            $('.see-all-block .see-all-toggle').removeClass('hidden');
            $('.see-all-block .see-less-toggle').addClass('hidden');
        });

        $(document).on('click', '.see-all-block .see-all-toggle', function() {
            $('.cDropDown.ore_dealers ul').toggleClass('active');
            $('.see-all-block .see-less-toggle').removeClass('hidden');
            $('.see-all-block .see-all-toggle').addClass('hidden');
        });

        $(document).on('click', '.dealersBlock .dOpener', function() {
            $('.dealersBlock .dOpener').addClass('active');
            $('.see-all-block').removeClass('hidden');
        });

        $(document).on('click', '.dealersBlock .dOpener.active', function() {
            $('.dealersBlock .dOpener').removeClass('active');
            $('.see-all-block').addClass('hidden');
        });
        $('.filter-button').click(function() {
            $('.SideBar-left').addClass('open');
        });
        $('.cllc-modal-button').click(function() {
            $('.SideBar-left').removeClass('open');
        });


    },
    zipcodefilled: function() {
        $('.ore_user_zipcode, #zipCodeInfo').html(localCache.get('zipCodeEntered'));
        $('#dealerZipCode').val(localCache.get('zipCodeEntered'));
        //snipage.findDealers();
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
     * Add the sortby to data
     */
    addSortBy: function(data) {
        var sortBy = $('#LowToHigh_Sni').val();
        if (typeof(data) == 'string') {
            data = data + "&sortBy=" + sortBy;
            return data;
        } else {
            data.sortBy = sortBy;
        }
    },
    /***
     *  From Landing page to SNI Page
     *
     *  */
    sniLoader: function(action, searchWithIn) {

        var color = $.urlParam('color');
        var drivetype = $.urlParam('drivetype');
        var trim = $.urlParam('trim');
        var engine = $.urlParam('engine');
        var transmission = $.urlParam('transmission');
        var zipcode = $.urlParam('zipcode');
        var radius = $.urlParam('radius');
        var vehicle_type = $.urlParam('vehicle_type');

        var dealercode = $.urlParam('dealercode');;

        var zipsErr = true;
        var hiddealerZipCode = $('#dealerZipCode').val();

        if (!$.isNumeric(radius) || radius <= 0) {
            radius = 150;
            $('#searchWithIn_hidden').val(radius);
        }
        if (radius > 150) {
            radius = 150;
            $('#searchWithIn_hidden').val(radius);
        }

        if (!$.isNumeric(zipcode) || hiddealerZipCode == '' || !localCache.exist('zipCodeEntered')) {
            zipsErr = false;
            var cache_zipcode = localCache.get('zipCodeEntered');
            if (cache_zipcode == '' || cache_zipcode == null || cache_zipcode == undefined) {
                $('#zipCodePopUp #selectedZipCode').val('');
                $('#zipCodePopUp .zipcode_errorInfo').html('');
                setTimeout(function() {
                    $('#zipCodePopUp .custom-close').removeClass('hidden');
                    $('#zipCodePopUp .custom-close').addClass('hidden');
                    $('#zipCodePopUp').modal('show');
                }, 2500);
                return true;
            }
        }
        zipcode = hiddealerZipCode;
        if (zipcode == '' || zipcode === null) {
            $('#zipCodePopUp #selectedZipCode').val('');
            $('#zipCodePopUp .zipcode_errorInfo').html('');
            setTimeout(function() {
                $('#zipCodePopUp .custom-close').removeClass('hidden');
                $('#zipCodePopUp .custom-close').addClass('hidden');
                $('#zipCodePopUp').modal('show');
            }, 2500);
            return true;
            console.log('EMPTY ZIPCODE');
        };

        if (radius == '' || radius === null) { radius = 150; }

        zipcode = AddZeroPrefixZipcode(zipcode);

        params_driver = params_trim = params_color = params_engine = params_transmission = params_zipcode = params_radius = params_dealercode = '';
        var arr = [25, 50, 75, 100, 125, 150];

        if (drivetype !== null && drivetype !== '' && drivetype !== undefined) {
            $("input:checkbox[name='driveCode[]']").each(function() {
                if (drivetype != '') {
                    if ($(this).attr('data-filter-type').toLowerCase() == drivetype.toLowerCase()) {
                        params_driver = $(this).val();
                        $(this).prop("checked", true);
                    }
                }
            });
        }

        $("input:checkbox[name='trimCode[]']").each(function() {
            if (trim != '' && trim !== null) {
                if ($(this).attr('data-filter-type').toLowerCase() == trim.toLowerCase()) {
                    params_trim = $(this).val();
                    $(this).prop("checked", true);
                }
            }
        });
        $("input:checkbox[name='colorCode[]']").each(function() {
            if (color != '' && color !== null) {
                if ($(this).attr('data-filter-type').toLowerCase() == color.toLowerCase()) {
                    params_color = $(this).val();
                    $(this).prop("checked", true);
                }
            }
        });
        $("input:checkbox[name='EngDescCode[]']").each(function() {
            if (engine != '' && engine !== null) {
                if ($(this).attr('data-filter-type').toLowerCase() == engine.toLowerCase()) {
                    params_engine = $(this).val();
                    $(this).prop("checked", true);
                }
            }
        });
        $("input:checkbox[name='TransmissionCode[]']").each(function() {
            if (transmission != '' && transmission !== null) {
                if ($(this).attr('data-filter-type').toLowerCase() == transmission.toLowerCase()) {
                    params_transmission = $(this).val();
                    $(this).prop("checked", true);
                }
            }
        });

        $("input:checkbox[name='dealersCode[]']").each(function() {
            if (dealercode != '' && dealercode !== undefined && dealercode != null) {
                var current_val = $(this).val();
                if (current_val == dealercode) {
                    $(this).prop("checked", true);
                }
            }
        });

        $('#dealerZipCode').val(zipcode);
        $('#selectedZipCode').val(zipcode);
        localCache.set('zipCodeEntered', zipcode);
        localCache.set('dealerZipCode', zipcode);

        if (radius != '' && action == 'onload') {
            if (jQuery.inArray(radius, arr) == -1) $('#searchWithIn_hidden').val(radius);
            else $('#searchWithIn_hidden').val(radius);
        }


        var year = $('#params_year').val(),
            catid = $('#params_catid').val(),
            subcatid = $('#params_subcatid').val(),
            vechType = $('#params_vechType').val(),
            maxprice = $('#maxPrice_hidden').val(),
            startprice = $('#startPrice_hidden').val(),
            zipcode = localCache.get('zipCodeEntered'),
            make = $('#params_make').val(),
            tier = $('#tier').val(),
            radius = $('#searchWithIn_hidden').val();

        var modelname = $('#params_modelname').val(),
            priceRange_hidden = $('#maxPrice_hidden').val(),
            startprice_digit = $('#startPrice_hidden').val(),
            maxprice_digit = $('#maxPrice_hidden').val(),
            isTowing = $('#Towing_hidden').val();

        if ($('#dealerZipCode').val() == '') {
            zipcode = localCache.get('zipCodeEntered');
        } else {
            zipcode = $('#dealerZipCode').val();
        }

        if (dealercode != '' && dealercode !== undefined && dealercode != null) {
            params_dealercode = dealercode;
            if (tier == 't3') {
                $('#dealercode').val(params_dealercode);
            }
        }

        if (startprice_digit == maxprice_digit) startprice_digit = maxprice_digit - Math.ceil(maxprice_digit / 2);

        /*if(isTowing > 0){ $('.towSlider').hide(); $('.Towing_hidden').val(0); }else { $('.towSlider').show(); $('.Towing_hidden').val('Any');}
         */
        var priceRange_hidden = parseInt(startprice_digit) + ',' + parseInt(maxprice_digit);

        if (radius != '' && action == 'onload') {
            snipage.rangesliders('.priceRange', parseInt(startprice_digit), parseInt(startprice_digit), parseInt(maxprice_digit), 1, 'filter');
        }

        $('#priceRange_hidden').val(priceRange_hidden);
        $('#startPrice_hidden').val(parseInt(startprice_digit));
        //var dataObject = {'radius':radius, 'zipcode': zipcode, 'params_make':make, 'params_model':modelname, 'params_year':year, 'params_vechType':vechType, 'pageload': 'onload' };
        //snipage.addSortBy(dataObject);
        ajax.promise('find_dealers_by_zipcode_radius', 'post', JSON.stringify({ 'radius': radius, 'zipcode': zipcode, 'params_make': make, 'params_model': modelname, 'params_year': year, 'params_vechType': vechType, 'pageload': 'onload' }));
        //ajax.promise('find_dealers_by_zipcode_radius','post',JSON.stringify(dataObject));

        if (vechType == 'new') {
            $('.yearBlock').hide();
            $('.trimBlock').show();
        } else {
            // snipage.rangesliders('.yearInfo',year, 2015,2019,1, 'filter');
            $('.yearBlock').show();
            $('.trimBlock').hide();
        }
        $("#params_year").val(year);
        $("#params_catid").val(catid);
        $("#params_subcatid").val(subcatid);
        $("#params_vechType").val(vechType);
        $("#params_make").val(make);
        $("#params_modelname").val(modelname);

        //Reset Filter Update
        $('.resetFilterData').attr("data-catid", catid);
        $('.resetFilterData').attr("data-subcatid", subcatid);
        $('.resetFilterData').attr("data-year", year);
        $('.resetFilterData').attr("data-startprice", startprice);
        $('.resetFilterData').attr("data-maxprice", maxprice);
        $('.resetFilterData').attr("data-towing", isTowing);

        // RESERT
        $('.filterAttrs .dOpener').append('<span class="checkbox-counter" style="display: none;"></span>');
        $('body').on('change', '.filterAttrs :checkbox', function() {
            current_checked_items = $(this).closest('.filterAttrs').find('li input:checkbox:checked').length;
            $(this).closest('.filterAttrs').find('.checkbox-counter').text(current_checked_items);
            if (!current_checked_items == 0) {
                $(this).closest('.filterAttrs').find('.checkbox-counter').show()
            } else {
                $(this).closest('.filterAttrs').find('.checkbox-counter').hide();
            }
        });

        $('.filterAttrs').get().forEach(function(ItmVar) {
            let filterItemVar = $(ItmVar)
            let filter_count = filterItemVar.find('li input:checkbox:checked').length

            if (filter_count) {
                filterItemVar.find('.checkbox-counter').text(filter_count);
                filterItemVar.find('.checkbox-counter').show()
            }
        });


        maxprice = $('#maxPrice_hidden').val();
        startprice = $('#startPrice_hidden').val();

        var filter_params_session = { 'params_year': year, 'params_catid': catid, 'params_subcatid': subcatid, 'params_vechType': vechType, 'maxprice': maxprice, 'startprice': startprice, 'zipcode': zipcode, 'dealerZipCode': zipcode, 'params_make': make, 'searchWithIn_hidden': radius, 'params_modelname': modelname, 'priceRange_hidden': priceRange_hidden, 'startPrice_hidden': startprice_digit, 'tier': tier };

        if (params_color != '') filter_params_session.colorCode = { '': params_color };
        if (params_driver != '') filter_params_session.driveCode = { '': params_driver };
        if (params_trim != '') filter_params_session.trimCode = { '': params_trim };
        if (params_engine != '') filter_params_session.EngDescCode = { '': params_engine };
        if (params_transmission != '') filter_params_session.TransmissionCode = { '': params_transmission };
        if (params_transmission != '') filter_params_session.TransmissionCode = { '': params_transmission };

        filter_params_session.dealercode = params_dealercode;
        if(params_dealercode != '') {
            filter_params_session.dealersCode = [params_dealercode];
        }
        snipage.addSortBy(filter_params_session);
        var filter_params = JSON.stringify(filter_params_session);
        //, 'colorCode[]':params_color, 'driveCode[]':params_driver, 'trimCode[]':params_trim, 'EngDescCode[]':params_engine, 'TransmissionCode[]':params_transmission

        if (action == 'onload') ajax.promise('sniLeftFilter', 'post', JSON.stringify({ 'zipcode': zipcode, 'params_year': year, 'params_catid': catid, 'params_subcatid': subcatid, 'params_vechType': vechType, 'params_make': make, 'params_model': modelname, 'tier': tier }));

        ajax.promise('sniRightSide', 'post', filter_params);
        $('.dealersBlock .checkbox-counter').text(0);
    },

    /***
     *
     *   Left Side Filter Loader
     *
     ****/
    leftFilterLoad_cpo: function(data) {
        var checkFilter = ["dealers"];
        $.each(checkFilter, function(key, val) {
            if (data[val]) {
                if (val == 'dealers') {
                    $('.ore_dcount').html(Object.keys(data[val]).length)
                }
                snipage.generateFilterUL(val, data);
            }
        });
    },
    leftFilterLoad: function(data) {
        var checkFilter = ["dealers"];

        $.each(checkFilter, function(key, val) {
            if (data[val]) {
                if (val == 'dealers') {
                    $('.ore_dcount').html(Object.keys(data[val]["dealers"]).length);
                }
                snipage.generateFilterUL(val, data[val]);
            }
        });
    },
    generateFilterUL: function(fname, values) {
        var count = Object.keys(values['dealers']).length;
        if (count > 0) {
            var dropHtml = "<ul>";
            if (values['dealers'] != undefined) {
                vals = values['dealers_list'];
            } else vals = values;
            var isdealer;
            var params_dealercode = '';
            if(fname == 'dealers'){
                var dealercode = $.urlParam('dealercode'); ///urlParams.get('dealercode');
                if (dealercode != '' && dealercode !== undefined && dealercode != null){
                    params_dealercode = dealercode;
                }
            }
            for (var idx in vals) {
                $.each(vals[idx], function(key, data) {
                    isdealer = '';
                    if (data != '') {
                        isdealer = data.toLowerCase();
                        isdealer = isdealer.replace('-', ' ');
                        // isdealer = isdealer.replace('and fiat', ' ');
                        // isdealer = isdealer.replace('  ', '');
                        // isdealer = isdealer.replace('fiat', ' ');
                        // isdealer = isdealer.replace('and of', ' of');
                        // isdealer = isdealer.replace('and  of', ' of');
                        // isdealer = isdealer.replace('romeo-', 'romeo');
                        //isdealer = isdealer.toUpperCase();

                        //isdealer =  isdealer.toLowerCase().split(' ').map((s) => s.charAt(0).toUpperCase() + s.substring(1)).join(' ');
                        isdealer = isdealer.charAt(0).toUpperCase() + isdealer.slice(1);
                    }
                    var checked = '';
                    if(fname == 'dealers' && key == params_dealercode){
                        checked = 'checked';
                        $('.dealersBlock .checkbox-counter').text(1);
                        $('.dealersBlock .checkbox-counter').show();
                    }
                    dropHtml += "<li><label class='customCheckBox'>\
                        <input type='checkbox' id='" + fname + "Code[]' name='" + fname + "Code[]' class='FilterSelectEvent'  value='" + key + "' data-filter-type='" + isdealer + "' "+ checked +" /><span>" + isdealer + "\</span></label></li>";
                });
            }
            dropHtml += "</ul>";

        } else {
            $('.see-all-toggle').hide();
            var dropHtml = "<ul><li>There are no local " + fname + " near ZIP Code " + values['params_zipcode'] + ". Please ensure that your ZIP Code is entered correctly or try increasing your search radius </li></ul>";
        }
        $(".ore_" + fname).html(dropHtml);
    },

    rangesliders: function(slider, val, min, max, step, action) {

        $(function() {
            $('#lazyExactLimit').val(0);
            $('#lazyPartialLimit').val(0);
            $('#lazyType').val('e');

            if (slider == '.priceRange') {

                $(slider).slider({
                    values: [min, max],
                    range: true,
                    min: min,
                    max: max,
                    step: step,
                    slide: function(event, ui) {
                        var ddthis = $(this);
                        var slider_value = ui.values;
                        var find_rangeVal = ddthis.prev().find('.rangeVal');
                        var progressInfo = ddthis.children('.progressInfo');

                        find_rangeVal.text("$" + snipage.number_format(slider_value[0]) + " - $" + snipage.number_format(slider_value[1]));

                        $(slider + '_hidden').val(slider_value);
                        if (ui.values === 0) { ddthis.append('<p class="progressInfo"></p>'); }
                        progressInfo.css('width', ddthis.children('span').css('left'));
                        find_rangeVal.next().show();
                    },
                    change: function(event, ui) {
                        var slider_value2 = ui.values;

                        var ddthis = $(this);
                        $(this).children('.progressInfo').css('width', $(this).children('span').css('left'));

                        if (event.originalEvent === undefined) {} else {
                            if (action != 'onload') {
                                var tier = $('#tier').val();
                                if (tier == 'ore') activeTier = 'Standalone';
                                else activeTier = tier;
                                var merkle_action = tier + ':filter:silder:' + slider.replace('.', '') + ':' + slider_value2[0] + "-" + slider_value2[1];
                                mAnalystic.clickLink(merkle_action);
                            }
                            var dataString = $('.formFilter').serialize();
                            dataString = snipage.addSortBy(dataString);
                            console.log('Right post pushed');
                            console.log($('.formFilter').serialize());
                            //ajax.promise('sniRightSide','post',JSON.stringify($('.formFilter').serialize()));
                            ajax.promise('sniRightSide', 'post', JSON.stringify(dataString));
                        }
                    },
                });
            } else {

                $(slider).slider({
                    value: val,
                    min: min,
                    max: max,
                    step: step,
                    slide: function(event, ui) {
                        var ddthis = $(this);
                        var slider_value = ui.value;
                        var find_rangeVal = ddthis.prev().find('.rangeVal');
                        var progressInfo = ddthis.children('.progressInfo');

                        $(slider + '_hidden').val(slider_value);
                        if (progressInfo.length > 25) { ddthis.append('<p class="progressInfo"></p>'); }
                        progressInfo.css('width', ddthis.children('span').css('left'));
                        find_rangeVal.next().show();
                    },

                    change: function(event, ui) {

                        $(this).children('.progressInfo').css('width', $(this).children('span').css('left'));

                        if (event.originalEvent === undefined) {} else {
                            var tier = $('#tier').val();
                            if (tier == 'ore') activeTier = 'Standalone';
                            else activeTier = tier;
                            var merkle_action = activeTier + ':filter:silder:' + slider.replace('.', '') + ':' + ui.value;
                            mAnalystic.clickLink(merkle_action);

                            if (slider == '.searchWithIn') {
                                var zipcde = localCache.get('zipCodeEntered');
                                var params_make = $('#params_make').val();
                                var params_modelname = $('#params_modelname').val();
                                var params_year = $('#params_year').val();
                                var params_vechType = $('#params_vechType').val();

                                ajax.promise('find_dealers_by_zipcode_radius', 'post', JSON.stringify({ 'radius': ui.value, 'zipcode': zipcde, 'params_make': params_make, 'params_model': params_modelname, 'params_year': params_year, 'params_vechType': params_vechType, 'pageload': 'miles' }));
                                $('#searchWithIn_hidden').val(ui.value);
                                $('.tabMiles').html(validateRadiusValue(ui.value));
                                $('.ore_tabmiles').html(ui.value);
                                $('.ore_dmile').html(ui.value);
                                snipage.sniLoader('slider', $('#searchWithIn_hidden').val());

                            }

                            // CPO YEAR MODIFIED
                            else if (slider == '.yearInfo') {
                                $(slider).prev().find('.rangeVal').text(ui.value);
                                $('#params_year').val(ui.value);


                                /*var opt = $(this).data().uiSlider.options;
                                console.log(opt);
                                var vals = opt.max - opt.min;
                                for (var i = 0; i <= vals; i++) {
                                    var el = $('<label>' + (i + opt.min) + '</label>').css('left', (i/vals*100) + '%');
                                    console.log(e1);
                                    $(slider).append(el);
                                }*/


                                var params_make = $('#params_make').val();
                                var params_modelname = $('#params_modelname').val();
                                var params_year = $('#params_year').val();
                                var params_vechType = $('#params_vechType').val();
                                //
                                ajax.promise('cpo_catid_replacer', 'post', JSON.stringify({ 'params_make': params_make, 'params_modelname': params_modelname, 'params_year': ui.value, 'params_vechType': params_vechType }));


                            }


                        }
                    },
                });
            } // ELSE SLIDERS


            var towing_range = $(slider).prev().find('.rangeVal');
            if (slider == '.Towing') {
                towing_range.text($('#Towing_hidden').val())
                // towing_range.next().hide();
            } else towing_range.text($('#Towing_hidden').val());

            if (slider == '.priceRange') {
                var spl = $('#priceRange_hidden').val().split(',');
                $(slider).prev().find('.rangeVal').text('$' + snipage.number_format(spl[0]) + ' - $' + snipage.number_format(spl[1]));
            } else if (slider == '.searchWithIn') {
                $(slider).prev().find('.rangeVal').text($('#searchWithIn_hidden').val());
            } else if (slider == '.yearInfo') {

                $(slider).prev().find('.rangeVal').text($('#yearInfo_hidden').val());

            } else {

            }

        });
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
    ZoomImgLoader: function(dthis) {
        var imgSrc = dthis.prev('img').attr('src');
        imgSrc = imgSrc.replace('width=320&height=180&', 'width=1124&height=568&');
        console.log('ZoomImgLoader');
        console.log(imgSrc);
        console.log(imgSrc.indexOf('width=320&height=180&'));

        if (imgSrc.indexOf('fronthero&width=300&height=300') < 0)
            imgSrc = imgSrc.replace('width=300&height=300&', 'width=1124&height=568&');
        else
            imgSrc = imgSrc.replace('fronthero&width=300&height=300&', 'fronthero&width=1124&height=568&');

        console.log(imgSrc);
        $('#popUpImage').attr('src', imgSrc);
    },

    resetFilterURL:function(){
        var zipCodeEntered = localCache.get('zipCodeEntered');
        var baseUrl = document.location.origin;
        var pathArray = window.location.pathname.split('/');
        var myurl = baseUrl + '/' + pathArray[1] + '/' + pathArray[2] + '/' + pathArray[3] + '/' + pathArray[4];
        myurl += '?vehicle_type=new&color=&drivetype=&zipcode=' + zipCodeEntered;
        myurl += '&trim=&radius=150&engine=&transmission=';
        console.log('resetFilterURL:::');
        console.log(myurl);
        document.location.href = myurl;
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
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Frame-Options': 'sameorigin',
                'X-Content-Type-Options': 'nosniff',
            }
        });
        var tempurl = url;
        if (tempurl != 'baseCarNow') { snipage.loaderShow('.loader'); } else { snipage.loaderHide('.loader'); }
        if (tempurl == 'sniRightSideLazy') url = 'sniRightSide';

        if (url == 'sniRightSide') {
            $('form.formFilter').find('input, textarea, button, select').attr('disabled', 'disabled');
            $(".searchWithIn,  .Towing, .yearInfo").slider({ disabled: true });
        }
        var appurl = $('#APP_URL').val();
        if (requestType == 'get') {
            var jqxhr = $.get(appurl + url, function(result) {
                $('.ore_landing_models').html(result);
                $('h5.availableModels span.brandName').html(localCache.get('oremake'));
                snipage.zipcodefilled();
                snipage.imgLoader();
                snipage.loaderHide('.loader');
            }).fail(function(response, textStatus, jqXHR) {
                ajax.failure(url, textStatus);
                snipage.loaderHide('.loader');
            });
        } else {
            $.when($.ajax({ url: appurl + url, cache: true, xhrFields: { withCredentials: true }, type: requestType, data: JSON.parse(data) }))
                .done(function(result, textStatus, jqXHR) {
                    if (tempurl == 'sniRightSideLazy') ajax.success(tempurl, result);
                    else ajax.success(url, result);
                }).fail(function(response, textStatus, jqXHR) {
                    ajax.failure(url, textStatus);
                    snipage.loaderHide('.loader');
                });
        }
    },
    getSearchParams: function(k) {
        var p = {};
        location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(s, k, v) { p[k] = v })
        return k ? p[k] : p;
    },
    success: function(url, result) {
        switch (url) {
            case "find_cord_by_zip":
                localCache.set('latitude', result['latitude']);
                localCache.set('longitude', result['longitude']);
                snipage.zipcodefilled();
                break;
            case "cpo_catid_replacer":

                $('#params_catid').val(result[0]);
                $('#params_subcatid').val(result[1]);

                //snipage.rangesliders('.searchWithIn',25,25,150,25, 'onload');
                //snipage.sniLoader('slider', $('#searchWithIn_hidden').val());
                break;
            case "validate_zipcode":
                if (result['status'] == 'available') {
                    var zipCodeEntered = result['zipcode'];
                    localCache.set('zipCodeEntered', zipCodeEntered);
                    localCache.set('dealerZipCode', zipCodeEntered);
                    localCache.remove('latitude');
                    localCache.remove('longitude');

                    $('#dealerZipCode').val(zipCodeEntered);
                    $('#selectedZipCode').val(zipCodeEntered);
                    $('.ore_user_zipcode').html(zipCodeEntered);
                    $('#zipCodeInfo').html(zipCodeEntered);

                    $('#zipCodePopUp .custom-close').show();
                    $('#zipCodePopUp').modal('hide');
                    $('#zipCodePopUp').removeData();
                    $('.container.body').removeClass('blur');
                    //snipage.zip_to_cord();
                    snipage.zipcodefilled();
                    var baseUrl = document.location.origin;
                    var pathArray = window.location.pathname.split('/');

                    var myurl = baseUrl + '/' + pathArray[1] + '/' + pathArray[2] + '/' + pathArray[3] + '/' + pathArray[4];
                    myurl += '?vehicle_type=new&color=&drivetype=&zipcode=' + zipCodeEntered;
                    myurl += '&trim=&radius=150&engine=&transmission=';

                    document.location.href = myurl;
                } else {
                    $('.zipcode_errorInfo').text('Please enter the valid US zip code.');
                    return;
                }
                break;
            case "find_zip_by_cord":
                if (result['status'] == 'success') {
                    localCache.set('dealerZipCode', result['message']);
                    $('#dealerZipCode').val(result['message']);
                    $('#zipCodeInfo').html(result['message']);
                    $('#selectedZipCode').val(result['message']);

                } else {
                    alert('Alert: ' + result['message'] + ' so vehicles shows based on default zipcode.');
                }
                snipage.init();
                initSliders.init();
                break;
            case "find_zip_by_cord":
                localCache.set('zipCodeEntered', result);
                break;
            case "find_cpo_dealers":
                localCache.set('dealers_' + localCache.get('zipCodeEntered'), 'available');
                break;
            case "sniLeftFilter":
                snipage.leftFilterLoad(result['getAllFilterRows']);
                break;
            case "sniRightSide":
                $('form.formFilter').find('input, textarea, button, select').attr('disabled', false);
                $(".searchWithIn, .Towing, .yearInfo").slider({ disabled: false });
                $(".listBlock").replaceWith(result);
                snipage.imgLoader();
                snipage.loaderHide('.loader');
                $('.ore_ext_button').show();
                if ($('[href="#exactMatch"] span').text() == '0') {
                    $('.no-data-img').removeClass('hidden');
                    $('#exactMatch .ore_ext_button .btn.ore_ext').hide();
                } else {
                    $('.no-data-img').addClass('hidden');
                    $('#exactMatch .ore_ext_button .btn.ore_ext').show();
                }
                var exactMatch_count = $('[href="#exactMatch"] span').text();

                $('#exactMatch ul.list_exact.listCont>li:gt(' + (exactMatch_count - 1) + ')').hide();
                if ($('[href="#exactMatch"] span').text() <= $('#exactMatch ul.list_exact.listCont>li').length || exactMatch_count <= 10) {
                    $('.ore_ext').addClass('hide');
                    $('#exactMatch ul.list_exact.listCont>li:gt(' + (exactMatch_count - 1) + ')').hide();
                }
                $('#zipCodeInfo').html($('#dealerZipCode').val());

                var searchIn = $('#searchIn').val();
                var searchAgain = $('#searchAgain').val();
                if (searchIn) {
                    $('#searchWithIn_hidden').val(searchIn);
                    $('.tabMiles').html(validateRadiusValue(searchIn));
                    $('.ore_tabmiles').html(searchIn);
                    $('.ore_dmile').html(searchIn);
                    snipage.rangesliders('.searchWithIn', parseInt(searchIn), 25, 150, 25, 'onload');
                }
                if (searchAgain) {
                    var zipcde = localCache.get('zipCodeEntered');
                    var params_make = $('#params_make').val();
                    var params_modelname = $('#params_modelname').val();
                    var params_year = $('#params_year').val();
                    var params_vechType = $('#params_vechType').val();

                    ajax.promise('find_dealers_by_zipcode_radius', 'post', JSON.stringify({ 'radius': searchIn, 'zipcode': zipcde, 'params_make': params_make, 'params_model': params_modelname, 'params_year': params_year, 'params_vechType': params_vechType, 'pageload': 'miles' }));
                }


                break;
            case "sniRightSideLazy":
                var lazytype = $('#lazyType').val();
                if (lazytype == 'e') {
                    if (result == "") {
                        $('.ore_ext_button').html('No Vehicles found.');
                    } else {
                        $(".list_exact").append(result).slideDown("slow");
                        $('.ore_ext_button').show();
                    }

                } else {
                    if (result == "") {
                        $('.ore_par_button').html('No Vehicles found.');
                    } else {
                        $(".list_partial").append(result).slideDown("slow");
                        $('.ore_par_button').show();
                    }
                }



                snipage.imgLoader();
                snipage.loaderHide('.loader');
                $('form.formFilter').find('input, textarea, button, select').attr('disabled', false);
                $(".searchWithIn, .priceRange, .yearInfo").slider({ disabled: false });


                if (lazytype == 'e') {
                    if ($('[href="#exactMatch"] span').text() == '0') {
                        $('.no-data-img').removeClass('hidden');
                        $('#exactMatch .ore_ext_button .btn.ore_ext').hide();
                    } else {
                        $('.no-data-img').addClass('hidden');
                        $('#exactMatch .ore_ext_button .btn.ore_ext').show();
                    }
                    var exactMatch_count = $('[href="#exactMatch"] span').text();
                    $('#exactMatch ul.list_exact.listCont>li:gt(' + (exactMatch_count - 1) + ')').hide();
                    if ($('[href="#exactMatch"] span').text() <= $('#exactMatch ul.list_exact.listCont>li').length || exactMatch_count <= 10) {
                        $('.ore_ext').addClass('hide');
                        $('#exactMatch ul.list_exact.listCont>li:gt(' + (exactMatch_count - 1) + ')').hide();
                    }
                } else {
                    if ($('[href="#partialMatch"] span').text() == '0') {
                        $('.no-data-img-par').removeClass('hidden');
                        $('#partialMatch .ore_par_button .btn.ore_par').hide();
                    } else {
                        $('.no-data-img-par').addClass('hidden');
                        $('#partialMatch .ore_par_button .btn.ore_par').show();
                    }
                    var partialMatch_count = $('[href="#partialMatch"] span').text();
                    $('#partialMatch_count ul.list_partial.listCont>li:gt(' + (partialMatch_count - 1) + ')').hide();
                    if ($('[href="#partialMatch"] span').text() <= $('#partialMatch ul.list_partial.listCont>li').length || partialMatch_count <= 10) {
                        $('.ore_ext').addClass('hide');
                        $('#partialMatch ul.list_partial.listCont>li:gt(' + (partialMatch_count - 1) + ')').hide();
                    }
                }


                break;
            case "find_dealers_by_zipcode_radius":
                console.log('find_dealers_by_zipcode_radius');
                console.log(result);
                $('#searchWithIn_hidden').val(result['radius']);
                $('.tabMiles').text(validateRadiusValue(result['radius']));
                $('.ore_dmile').text(result['radius']);
                $('.no_dealer').text(result['params_zipcode']);

                var startprice_digit = result['msrp_price'];
                var maxprice_digit = result['maxs_msrp'];
                var pageload = result['pageload'];

                $('#priceRange_hidden').val(startprice_digit + ',' + maxprice_digit);
                $('#startPrice_hidden').val(startprice_digit);
                $('#maxPrice_hidden').val(maxprice_digit);
                $('.priceSlider .rangeVal').html('$' + snipage.addCommas(startprice_digit) + ' - $' + snipage.addCommas(maxprice_digit));

                if (pageload == 'onload') {
                    snipage.rangesliders('.searchWithIn', parseInt(result['radius']), 25, 150, 25, 'onload');
                } else {
                    snipage.sniLoader('slider', result['radius']);
                }
                //snipage.rangesliders('.priceRange',parseInt(startprice_digit), parseInt(startprice_digit),parseInt(maxprice_digit),1, 'filter');
                //snipage.rangesliders('.searchWithIn',parseInt(result['radius']),25,150,25, 'onload');
                snipage.leftFilterLoad_cpo(result);

                break;
            case "baseCarNow":
                snipage.loaderHide('.loader');
                break;
            case "leadSubmit":
                console.log("Lead Succesfully Submit");
                break;
        }

    },
    failure: function(url, textStatus) {
        snipage.dd("Error: " + url);
        snipage.dd("Status: " + textStatus);
    }


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
            var zipCodeEntered = $('#selectedZipCode').val().trim();
            zipCodeEntered = AddZeroPrefixZipcode(zipCodeEntered);


            localCache.set('zipCodeEntered', zipCodeEntered);
            localCache.set('dealerZipCode', zipCodeEntered);
            localCache.remove('latitude');
            localCache.remove('longitude');

            $('#dealerZipCode').val(zipCodeEntered);
            $('#selectedZipCode').val(zipCodeEntered);
            $('.ore_user_zipcode').html(zipCodeEntered);
            $('#zipCodeInfo').html(zipCodeEntered);

            $('#zipCodePopUp .custom-close').show();
            $('#zipCodePopUp').modal('hide');
            $('#zipCodePopUp').removeData();
            $('.container.body').removeClass('blur');
            //snipage.zip_to_cord();
            snipage.zipcodefilled();
            var baseUrl = document.location.origin;
            var pathArray = window.location.pathname.split('/');

            var myurl = baseUrl + '/' + pathArray[1] + '/' + pathArray[2] + '/' + pathArray[3] + '/' + pathArray[4];
            myurl += '?vehicle_type=new&color=&drivetype=&zipcode=' + zipCodeEntered;
            myurl += '&trim=&radius=150&engine=&transmission=';

            document.location.href = myurl;


            //   var zipCodeEntered = result['zipcode']; 
            // ajax.promise('validate_zipcode', 'post', JSON.stringify("zipcode=" + zipCodeEntered));
            snipage.loaderHide('.loader');

        }
    }
}

var isScrolledIntoView = function(elem) {
    var $elem = $(elem);
    var $window = $(window);

    var docViewTop = $window.scrollTop();
    var docViewBottom = docViewTop + $window.height();

    var elemTop = $elem.offset().top;
    var elemBottom = elemTop + $elem.height();

    $('.leftMenu').width($('.SideBar-left').width());

    if (elemTop <= docViewBottom) {
        $('.leftMenu').addClass('sticky--bottom');
    } else {
        $('.leftMenu').removeClass('sticky--bottom');
    }
    $('.leftMenu').show();
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
        return localStorage.getItem(key);
    },
    set: function(key, value) {
        localStorage.setItem(key, value);
        return true;
    }
};

function SniAjax(url, requestType, data) {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    return $.ajax({
        url: url,
        type: requestType,
        data: JSON.parse(data)
    });
}

function AddZeroPrefixZipcode(zipcode) {
    str_zip_length = zipcode.length;
    if (0 < str_zip_length && 4 == str_zip_length) {
        zipcode = '0' + zipcode;
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


function validateRadiusValue(value) {
    if (0 < value && value > 150) {
        value = 150;
    }
    console.log(value);
    return value
}

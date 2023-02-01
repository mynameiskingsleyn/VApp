var global = {
    init: function () {
        $(window).scroll(function () {
            var sc = $(window).scrollTop()
            if (sc > 0) {
                $(".header-scroll").addClass("small")
            } else {
                $(".header-scroll").removeClass("small")
            }
        });
    },
    loaderShow: function (divid) {
        // $(divid).show();
        $('.ore_landing_models').addClass('tempo');
        $('.listCont').addClass('tempo');
    },
    loaderHide: function (divid) {
        // $(divid).hide();
        $('.ore_landing_models').removeClass('tempo');
        $('.listCont').removeClass('tempo');
    },
    isZip: function () {
        if (localCache.exist('zipCodeEntered')) return 'AVAIL';
        else return 'UNAVAIL'
    },
    dd: function (str) {
        console.log(str);
    },
    number_format: function (number, decimals, dec_point, thousands_sep) {
        // Strip all characters but numerical ones.
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
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
    imgLoader: function () {
        var errorURL = "https://d1jougtdqdwy1v.cloudfront.net/jellybeans/noimage.png";

        $('[data-original]').each(function () {
            var $this = $(this),
                src = $this.attr('data-original');

            var img = new Image();
            img.onload = function () { $this.attr('src', src); }
            img.onerror = function () { $this.attr('src', errorURL); }
            img.src = src;
        });
    },
    loadCookieIntoMemory: function () {
        var oldSession = OreCookie.read_JSON('OreLead');
        $.when(doAjax('loadPrevSession', 'post', JSON.stringify(oldSession)))
            .done(function (response, textStatus, jqXHR) { }).fail(function (response, textStatus, jqXHR) { });
    },
    ZoomImgLoader: function (dthis) {
        var imgSrc = dthis.prev('img').attr('src');
        if (imgSrc.indexOf('fronthero&width=300&height=300') < 0)
            imgSrc = imgSrc.replace('width=300&height=300&', '');
        else
            imgSrc = imgSrc.replace('fronthero&width=300&height=300&', 'fronthero&');

        $('#popUpImage').attr('src', imgSrc);
    },
    CheckIncentives: function (IncentivesID, IncentivesArray) {
        if (jQuery.inArray(IncentivesID, IncentivesArray) != '-1') {

            //console.log(name + ' is in the array!');
            return true;
        } else {
            //console.log(name + ' is NOT in the array...');
            return false;
        }
    }
}

/**
 *  Ajax Setup
 * 
 * @var ajax
 *  
 * */
var ajax = {
    promise: function (url, requestType, data) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Frame-Options': 'sameorigin',
                'X-Content-Type-Options': 'nosniff',
            }
        });

        var tempurl = url;
        if (tempurl != 'baseCarNow') { global.loaderShow('.loader'); } else { global.loaderHide('.loader'); }

        var appurl = $('#APP_URL').val();
        if (requestType == 'get') {
            var jqxhr = $.get(appurl + url, function (result) {
                $('.ore_landing_models').html(result);
                $('h5.availableModels span.brandName').html(localCache.get('oremake'));
                global.zipcodefilled();
                global.imgLoader();
                global.loaderHide('.loader');
            }).fail(function (response, textStatus, jqXHR) {
                ajax.failure(url, response, textStatus, jqXHR);
                global.loaderHide('.loader');
            });
        } else {
            $.when($.ajax({ url: appurl + url, cache: true, type: requestType, data: JSON.parse(data) }))
                .done(function (result, textStatus, jqXHR) {
                    ajax.success(url, result);
                }).fail(function (response, textStatus, jqXHR) {
                    global.loaderHide('.loader');
                    ajax.failure(url, response, textStatus, jqXHR);

                });
        }
    },

    success: function (url, result) {

        switch (url) {
            case "payment-calcultor":
                var transactionType = result['transactionType'];
                var finance_type = result['finance_type'];

                var methods = result['methods'];
                /* LEASE */
                if (transactionType == 'lease') {
                    $("input.lease_chk").removeAttr("disabled");
                    $('.price-tag-lease').show();

                    if (result['restrict']) {
                        $('.lease_restrict, .alter_div, .price-tag-lease').hide();
                        $('.lease_restrict_html').html('Price details are currently unavailable. Please try again later.');

                        $('.dummy_pcacl_lease').show();
                        $('.dummy_pcacl_lease, .ore_disp_lease_estimate').html('N/A');

                    } else {
                        $('.dummy_pcacl_lease').hide();
                        $('.lease_restrict, .alter_div').show();
                    }

                    //if ($('.price-tag-lease').data('env') == 'local' || $('.price-tag-lease').data('env') == 'dev') {
                      //  $('.lease_restrict').show();
                    //}

                    if (result['isCCAPAvailale']) {
                        $('.offer_ccap_tab_span').show();
                    } else {
                        $('.offer_ccap_tab_span').hide();
                    }
                    /* LEASE */
                    //var totalIncen = parseInt(result['incentiveAmount']);
                    var incentivesBonusCash_available = result['incentivesBonusCash_available'];
                    var incentivesBonusCash_amount = parseInt(result['incentivesBonusCash_amount']);
                    var totalIncen1 = parseInt(result['incentiveAmount']) + incentivesBonusCash_amount;
                    var totalIncen = parseInt(result['rebateDetailsfinalamount']);

                    var totalExplore = parseInt(result['explore_amount']);
                    var totalDlrDisc = parseInt(result['dlrDiscAmount']);
                    var onlyIncentives = totalIncen - parseInt(result['explore_amount']);
                    var totalIncenDiscounts = totalIncen + parseInt(totalDlrDisc);

                    $('.mDestLExplore').html('<span style="color: green; weight: bold;">-</span> $' + totalExplore);
                    if (totalIncen1 == null || totalIncen1 == 0) {
                        $('.IncentivesMerkle > i').hide();
                        $('.IncentivesMerkle').removeClass('noDropdownHover');
                        $('.IncentivesMerkle').addClass('noDropdownHover');
                        $('.IncentivesMerkle').next('div.addOffers').hide();
                    } else { 
                        $('.IncentivesMerkle').removeClass('noDropdownHover');
                    }

                    if (totalDlrDisc == null || totalDlrDisc == 0) {
                        $('.mDestLDlrDisc').html('<span style="color: green; weight: bold;">-</span> $' + totalDlrDisc);
                        $('.DlrDiscMerkle > i').hide();
                        $('.DlrDiscMerkle').removeClass('noDropdownHover');
                        $('.DlrDiscMerkle').addClass('noDropdownHover');
                        $('.DlrDiscMerkle').next('div.addOffers').hide();

                        //	$('.dealer_disc_update').html('<ul class="list-group reg"><li>No Dealer Discounts Available.</li></ul>');
                    } else {
                        $('.DlrDiscMerkle > i').show();
                        $('.DlrDiscMerkle').removeClass('noDropdownHover');
                        $('.DlrDiscMerkle').next('div.addOffers').removeAttr('style');
                        $('.mDestLDlrDisc').html('<span style="color: green; weight: bold;">-</span> $' + totalDlrDisc);
                        $('.dealer_disc_update').html(result['dlrDiscLists']);
                    }
                    $('.lease_main_incentive').html('<span style="color: green; weight: bold;">-</span> $' + (totalIncenDiscounts));

                    /* LEASE */
                    if (methods == 'onload') {
                        //Incentives
                        if (totalIncen1 == null || totalIncen1 == 0) {
                            $('.IncentivesMerkle > i').hide();
                            $('.IncentivesMerkle').removeClass('noDropdownHover');
                            $('.IncentivesMerkle').addClass('noDropdownHover');
                            $('.IncentivesMerkle').next('div.addOffers').hide();
                            $('.lease_main_incentive').html('<span style="color: green; weight: bold;">-</span> $' + totalIncenDiscounts);
                            $('.mDestCharge').html('<span style="color: green; weight: bold;">-</span> $' + onlyIncentives);

                            //$('.calc_incentives_desc').html('<ul class="list-group reg"><li>No Incentives Applicable.</li></ul>');

                        } else {
                            $('.IncentivesMerkle').removeClass('noDropdownHover');
                            /** investigation satyendra */
                            //  console.log('incentiveIdsfff', result['incentiveIds']);
                            // console.log('rebateDetailsidffff', result['rebateDetailsid']);
                            var rebateDetailsfinalamount = result['rebateDetailsfinalamount'];
                            var rebateDetailsid_string = result['rebateDetailsid']
                            var rebateDetailsid_arr = rebateDetailsid_string.split(',');

                            $('.lease_main_incentive').html('<span style="color: green; weight: bold;">-</span> $' + totalIncenDiscounts);
                            $('.mDestCharge').html('<span style="color: green; weight: bold;">-</span> $' + onlyIncentives);
                            var incv = '<ul class="list-group reg">';
                            $.each(result['incentiveNames'], function (index, item) {
                                var temp_price = result["man_incentives_sin_name"][index];
                                if (result["man_incentives_sin_name"][index] != null && result["man_incentives_sin_name"][index] != undefined && result["man_incentives_sin_name"][index] != '') {
                                    var num = index + 11;
                                    var disclaimer = '';
                                    if (result['incentive_disclaimer'].length > 0) {
                                        disclaimer = result['incentive_disclaimer'][index];
                                    }

                                    var status = global.CheckIncentives(result["incentiveIds"][index], rebateDetailsid_arr);
                                    if (status) {
                                        incv += '<li data-incentiveid="' + result["incentiveIds"][index] + '" class="inv_add_offers"><img alt="incentive-icon" src="/images/incentive-icon-5.png" width=\"20\" height=\"20\" /> ' + item + '&nbsp; ' + '<span data-disclamer="' + disclaimer + '" id="l_q_insentive" class="l_q_insentive badge disclaimer" data-num="' + num + '">' + num + '</span>' + ' <span style="float:right; "><b>$' + temp_price + '</b></span></li>';
                                    }


                                }
                            });

                            if (incentivesBonusCash_available) {
                                // console.log(result['incentivesBonusCashList']);
                                $.each(result['incentivesBonusCashList'], function (key, value) {
                                    var num = 20;
                                    incv += '<li><span><img alt="incentive-icon" src="/images/incentive-icon-5.png" width=\"20\" height=\"20\" />' + key + '&nbsp;';
                                    if(value.disclaimer != ''){
                                        incv +='<span data-disclamer="' + value.disclaimer + '" id="l_q_insentive" class="l_q_insentive badge disclaimer" data-num="' + num + '">' + num + '</span>';
                                        num++;
                                    }
                                    incv += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span style="float:right; "><b>$' + value.discount + '</b></span></span></li>';
                                });
                            }
                            incv += '</ul>';
                            $('.calc_incentives_desc').html(incv);
                        }
                        /* LEASE */
                        //Explore 	

                        if (result['explores'].length == 0) {
                            explore = "";
                            $('.expAddOffMerkle > i').hide();
                            $('.expAddOffMerkle').next('div.addOffers').hide();
                        } else {
                            var countname = 0;
                            var amt = 0;
                            var explore = '<ul class="list-group reg">';
                            var num = 13;
                            $.each(result['explores'], function (index, item) {
                                var groupName = '';
                                num = num + 1;
                                $.each(item['name'], function (index1, item1) {
                                    groupName += item1 + ',';
                                });
                                
                                //console.log('item1',item['disclaimer']);
                                 var disclaimer = item['disclaimer'][0];
                                    var item_feature = item['feature'] ;
                                    if(item['feature'] == 'Engine') item_feature = 'Engine Level Incentive';
                                    if(item['feature'] == 'V6') item_feature = 'Engine Level Incentive';
                                    if(item['feature'] == 'V8') item_feature = 'Engine Level Incentive';
                                    if(item['feature'] == 'Package') item_feature = 'Package Level Incentive';
                                    if(item['feature'] == 'Hemi') item_feature = 'Engine Level Incentive';
                                    if(item['feature'] == 'Transmission') item_feature = 'Transmission Level Incentive';
                                    
                                    if(item['feature'] == 'Chrysler-capital-incentives') item_feature = 'Chrysler Capital Incentive';


                                explore += '<li>';
                                explore += '<label for="explores_' + item['feature'] + '" class="customCheckBox"><input type="checkbox" title="lease_explores" aria-labelledby="explores_' + item['feature'] + '" aria-describedby="explores_' + item['feature'] + '" class="lease_chk lease_chk  ' + item['feature'] + '" name="lease_explores[]" id="explores_' + item['feature'] + '" value="' + item['ids'] + '" data-original-groups = "' + groupName.replace(/,\s*$/, "") + '" data-original-amount = "' + item['amount'] + '">';

                                explore += '<span></span><b>' + item_feature + '</b>' + '</label><span style="float:right"><b>$' + item['amount'] + '</b></span>' + ' &nbsp;<span data-disclamer="' + disclaimer + '" id="l_q_insentive" class="l_q_insentive badge disclaimer" data-num="' + num + '">' + num + '</span>   ';

                                explore += '</li>';
                                countname = parseInt(item['name'].length);
                                amt = parseInt(item['amount']) / countname;

                                $.each(item['name'], function (index1, item1) {
                                    explore += '<li style="padding-left:30px;" class="offer_subsets" data-relation-name="explores_' + item['feature'] + '" data-original-items="' + item1 + '" data-original-items-class="' + item['feature'] + '" data-original-incentiveid="' + item['ids'] + '" data-original-incentive-count="' + countname + '" data-original-incentive-amount="' + item['inv_amount'][index1] + '">' + item1 + '&nbsp;&nbsp;<b> $' + item['inv_amount'][index1] + '</b>' + '</li>';
                                });

                            });
                            explore += '</ul>';
                        }
                        $('.dealer_disc_update').html(result['dlrDiscLists']);
                        $('.calc_offers_desc').html(explore);
                        /* LEASE */
                        //Terms Dropdown
						
						 var sele = '<label style="display:none;" for="lease_terms">hide me</label><select class="form-control duration ore_dropdown_lease_terms" id="lease_terms">';
                        var def = '';
                        /* LEASE */
                        for (var i = 0; i < result['terms'].length; i++) {
							
							  if(result['default_lease'] == result['terms'][i]) 
								def = 'selected = "select"';
							else 
								def = ''; 
							
                            sele += '<option value="' + result['terms'][i] + '" ' + def + '>' + result['terms'][i] + ' Months</option>';
                        }
                        sele += '</select>';
						
                       /*  var sele = '<label style="display:none;" for="lease_terms">hide me</label><select class="form-control duration ore_dropdown_lease_terms" id="lease_terms">';
                        var def = '';
                        for (var i = 0; i < result['terms'].length; i++) {
                            if (result['terms'][i] == 36) {
                                sele += '<option value="' + result['terms'][i] + '" selected = "select">' + result['terms'][i] + ' Months</option>';
                            } else {
                                sele += '<option value="' + result['terms'][i] + '">' + result['terms'][i] + ' Months</option>';
                            }
                        }
                        sele += '</select>'; */

                        $('.lease_wrapper').html(sele);
                    }

                    /*****  BEGIN: Non-Incentives Records Shows alert *****/

                    // Incentive Amount Updated
                    //$('.mDestLExplore').html('<span style="color: green; weight: bold;">-</span> $'+totalExplore);	
                    //$('.mDestCharge, .lease_main_incentive').html('<span style="color: green; weight: bold;">-</span> $'+(  parseInt(result['dlrDiscAmount']) + parseInt(result['rebateDetailsfinalamount']) )); 
                    //Incentive text hid
                    //var incentive_fav = result['rebateDetailsid'].split(',');
                    //var incentive_fav_unique = incentive_fav.filter(function(itm, i, incentive_fav) { return i == incentive_fav.indexOf(itm); }); 

                    /*var current_ince_id ;
                    $.each($(".calc_incentives_desc>ul>li"), function(e){  
                    	current_ince_id = $(this).attr('data-incentiveid'); 
                    	 if(jQuery.inArray( current_ince_id, incentive_fav_unique ) !== -1){ 
                    			$(this).show(); 
                    		 }  else{
                    			 $(this).hide(); 
                    		}
                    });*/

                    // Explore Feature hide
                    /***
                     *
                     *  in the explore feaures, some incentives doesn't applicable
                     * then unnchecked it and shows the message
                     * then update the incentives, explores and main incentive values.
                     * 
                     */
                    var acceptedpartialamt = 0; 
                    var favorite = result['rebateDetails'].split(',');
                    var favorite_id = [];
                    $.each($(".lease_chk:checked"), function () {
                        favorite_id.push($(this).attr('id'));
                    });

                    var favorite_id_unique = favorite_id.filter(function (itm, i, favorite_id) { return i == favorite_id.indexOf(itm); });

                    $('.offer_subsets').removeClass('incentiveMatches');
                    var partialamt = 0;

                    var lesspartial_status = false;
                    var chkleaseclass = [];

                      var dataIncentiveID=0;
                      
                      /* LEASE */
                    /* Verify all Man incentive stack ability */
                     var rebateDetailsID = result['rebateDetailsid'].split(','); 
                     $.each($(".inv_add_offers"), function () {
                         $(this).removeClass('crosstext');                   
                          dataIncentiveID = $(this).attr('data-incentiveid'); 
                          if (jQuery.inArray(dataIncentiveID, rebateDetailsID) === -1) { // FALSE
                              $(this).addClass('crosstext');
                          }
                     });  
                    
                    
                    $.each($(".offer_subsets"), function () {
                        dataRelationName = $(this).attr('data-relation-name');
                        dataOriginalItems = $(this).attr('data-original-items');
                        // console.log('dataRelationName',dataRelationName);
                        //console.log('dataOriginalItems',dataOriginalItems);
                        $(this).removeClass('crosstext');
                        if (jQuery.inArray(dataRelationName, favorite_id_unique) !== -1) { // TRUE 

                            if (jQuery.inArray(dataOriginalItems, favorite) !== -1) { // TRUE 
                                $(this).removeClass('incentiveMatches');
                                acceptedpartialamt += parseInt($(this).attr('data-original-incentive-amount'));
                                chkleaseclass.push($(this).attr('data-original-items-class'));

                            } else {
                                $(this).addClass('crosstext');
                                $(this).addClass('incentiveMatches');
                                lesspartial_status = true;
                                partialamt += parseInt($(this).attr('data-original-incentive-amount'));
                            }
                        }
                    });

                    ////
                    var mflagindexvalue = [];

                    $.each($(".lease_chk:checked"), function (index, value) {
                        mflagindexvalue[index] = $(this).attr('data-original-amount');

                    });
                    /// 

                    var mFeatureDeduction = 0;
                    var subval = 0;
                    $.each($(".lease_chk:checked"), function (ind, val) {
                        var dor = $(this).attr('data-original-groups');
                        var mflag = 0;
                        var dorArray = dor.split(',');
                        $.each(dorArray, function (index, value) {
                            if (jQuery.inArray(value, favorite) == -1) { mflag = parseInt(mflag) + 1; }
                        });

                        if (mflag > 0) {

                           // $(this).prop('checked', false);
                            // mFeatureDeduction = $(this).attr('data-original-amount');
                            // totalExplore = parseInt(result['explore_amount']) - mFeatureDeduction;
                            // onlyIncentives = totalIncen - parseInt(totalExplore);
                            // subval += parseInt(mflagindexvalue[ind]);


                            mFeatureDeduction = $(this).attr('data-original-amount'); 

                            totalExplore = parseInt(result['explore_amount']) - mFeatureDeduction;

                            // onlyIncentives = totalIncen  - ( parseInt(totalExplore))

                        }

                    });

                    //if(acceptedpartialamt > 0){
                    onlyIncentives = parseInt(totalIncen) - parseInt(acceptedpartialamt);
                    // }
                    totalExplore = parseInt(acceptedpartialamt);



                    $.each(chkleaseclass, function (index, value) {
                        $("." + value).prop("checked", "true");
                    });
                    // if (lesspartial_status) {

                    //     expamt=result['explore_amount']-acceptedpartialamt;
                    //     var onlyIncentives = totalIncen - expamt;
                    //     console.log('checked',chkleaseclass);
                    //     $.each( chkleaseclass, function( index, value ){
                    //         $("."+value).prop("checked", "true");
                    //     });

                    // } else {
                    //     expamt = result['explore_amount'] - subval;
                    //     var onlyIncentives = totalIncen - expamt;
                    // }

                    /*****  END: Non-Incentives Records Shows alert *****/
                    /* LEASE */
                    // $('.lease_main_incentive').html('<span style="color: green; weight: bold;">-</span> $' + (totalIncenDiscounts));
                    // $('.mDestLExplore').html('<span style="color: green; weight: bold;">-</span> $' + expamt);
                    // $('.mDestCharge').html('<span style="color: green; weight: bold;">-</span> $' + onlyIncentives);

                    $('.lease_main_incentive').html('<span style="color: green; weight: bold;">-</span> $' + (totalIncenDiscounts));
                    $('.mDestLExplore').html('<span style="color: green; weight: bold;">-</span> $' + totalExplore);
                    $('.mDestCharge').html('<span style="color: green; weight: bold;">-</span> $' + onlyIncentives);


                    $('.ore_lease_emi, .main_msrp_lease').text('$' + result['paymentWithTaxesVal']['monthlyPayment']);
                    $('.ore_disp_lease_estimate').text('$' + result['paymentWithTaxesVal']['amountFinanced']);
                    $('.ore_emi_lease_month').text(result['terms'][0]);
                    //Taxes 
                    /*var tax = '<ul class="list-group reg">';
                    	tax += '<li><b>Monthly Sales tax</b> <span style="float: right;">$'+result['paymentWithTaxesVal']['monthlySalesTax']+'</span><hr></li>';
                    	tax += '<li><b>Inception Fees	 </b> <span style="float: right;">$'+result['paymentWithTaxesVal']['inceptionFees']+'</span><hr></li>';
                    	tax += '<li><b>Sales Tax		 </b> <span style="float: right;">$'+result['paymentWithTaxesVal']['capitalizedTaxes']['salesTaxAmount']+'</span><hr></li>';
                    	tax += '<li><b>Sales Tax Terms	 </b> <span style="float: right;">'+result['paymentWithTaxesVal']['capitalizedTaxes']['salesTax']['taxParameters']['rate']+' Months</span><hr></li>';
                    	tax += '</ul>'; 
                    	
                     $('.calc_taxes_desc').html(tax); 
                     */



                } else if (transactionType == 'finance') {
                    $("input.finance_chk").removeAttr("disabled");
                    $('.price-tag-finance').show();
                    if (result['restrict']) {
                        $('.finance_restrict .price-tag-finance').hide();
                        $('.finance_restrict_html').html('Price details are currently unavailable. Please try again later.');

                    } else {
                        $('.finance_restrict').show();
                    }

                    /* finance */
                    if (result['restrict']) {
                        $('.finance_restrict, .f_alter_div .price-tag-finance').hide();
                        $('.finance_restrict_html').html('Price details are currently unavailable. Please try again later.');

                        $('.dummy_pcacl_fianance').show();
                        $('.dummy_pcacl_fianance, .ore_disp_finance_estimate').html('N/A');

                    } else {
                        $('.dummy_pcacl_fianance').hide();
                        $('.finance_restrict, .f_alter_div').show();
                    }

                    if (result['isCCAPAvailale']) {
                        $('.offer_ccap_tab_span').show();
                    } else {
                        $('.offer_ccap_tab_span').hide();
                    }

                    /* finance */
                    if (finance_type == 'ChryslerCapital') $('.f_c_name').html('Chrysler\'s Rate');
                    else $('.f_c_name').html('Ally\'s Rate');
                    /* finance */
                    var incentivesBonusCash_available = result['incentivesBonusCash_available'];
                      var incentivesBonusCash_amount = parseInt(result['incentivesBonusCash_amount']);
                     
                    //var totalIncen = parseInt(result['incentiveAmount']);
                    var totalIncen = parseInt(result['rebateDetailsfinalamount']);
                    var totalIncen1 = parseInt(result['incentiveAmount']) + incentivesBonusCash_amount;  
                    var totalExplore = parseInt(result['explore_amount']);
                    var totalDlrDisc = parseInt(result['dlrDiscAmount']);
                    var totalIncenDiscounts = totalIncen + parseInt(totalDlrDisc);
                    var onlyIncentives = totalIncen - parseInt(result['explore_amount']);
                    if (totalIncen1 == null || totalIncen1 == 0) {
                        $('.IncentivesfMerkle > i').hide();
                        $('.IncentivesfMerkle').removeClass('noDropdownHover');
                        $('.IncentivesfMerkle').addClass('noDropdownHover');
                        $('.IncentivesfMerkle').next('div.addOffers').hide();
                    }else{
                        $('.IncentivesfMerkle').removeClass('noDropdownHover');
                    }

                    $('.mDestFExplore').html('<span style="color: green; weight: bold;">-</span> $' + totalExplore);

                    if (totalDlrDisc == null || totalDlrDisc == 0) {
                        $('.mDestFDlrDisc').html('<span style="color: green; weight: bold;">-</span> $' + totalDlrDisc);
                        //$('.fdealer_disc_update').html('<ul class="list-group reg"><li>No Dealer Discounts Available.</li></ul>');
                        $('.DlrDiscFMerkle > i').hide();
                        $('.DlrDiscFMerkle').removeClass('noDropdownHover');
                        $('.DlrDiscFMerkle').addClass('noDropdownHover');
                        $('.DlrDiscFMerkle').next('div.addOffers').hide();
                    } else {
                        $('.DlrDiscFMerkle > i').show();
                        $('.DlrDiscFMerkle').removeClass('noDropdownHover');
                        $('.DlrDiscFMerkle').next('div.addOffers').removeAttr('style');
                        $('.mDestFDlrDisc').html('<span style="color: green; weight: bold;">-</span> $' + totalDlrDisc);
                        $('.fdealer_disc_update').html(result['dlrDiscLists']);
                    }

                    $('.finance_main_incentive').html('<span style="color: green; weight: bold;">-</span> $' + (totalIncenDiscounts));

                    /* finance */
                    if (methods == 'onload') {
                        //Incentives
                          if (totalIncen1 == null || totalIncen1 == 0) {
                            $('.IncentivesfMerkle > i').hide();
                            $('.IncentivesfMerkle').removeClass('noDropdownHover');
                            $('.IncentivesfMerkle').addClass('noDropdownHover');
                            $('.IncentivesfMerkle').next('div.addOffers').hide();
                            $('.fDestCharge').html('<span style="color: green; weight: bold;">-</span> $' + onlyIncentives);
                            //$('.fcalc_incentives_desc').html('<ul class="list-group reg"><li>No Incentives Applicable.</li></ul>');
                        } else {
                            $('.IncentivesfMerkle').removeClass('noDropdownHover');
                            var rebateDetailsfinalamount = result['rebateDetailsfinalamount'];
                            var rebateDetailsid_string = result['rebateDetailsid']
                            var rebateDetailsid_arr = rebateDetailsid_string.split(',');

                            $('.fDestCharge').html('<span style="color: green; weight: bold;">-</span> $' + onlyIncentives);
                            var incv = '<ul class="list-group reg">';
                            $.each(result['incentiveNames'], function (index, item) {
                                var temp_finance_price = result["man_incentives_sin_name"][index];
                                if (result["man_incentives_sin_name"][index] != null && result["man_incentives_sin_name"][index] != undefined && result["man_incentives_sin_name"][index] != '') {
                                    var num = index + 11;
                                    var disclaimer = '';
                                    if (result['incentive_disclaimer'].length > 0) {
                                        disclaimer = result['incentive_disclaimer'][index];
                                    }


                                    var status = global.CheckIncentives(result["incentiveIds"][index], rebateDetailsid_arr);
                                    if (status) {
                                        incv += '<li data-incentiveid="' + result["incentiveIds"][index] + '" class="finv_add_offers"><img alt="incentive-icon" src="/images/incentive-icon-5.png" width=\"20\" height=\"20\" /> ' + item + '&nbsp;&nbsp;' + '<span id="l_q_fininsentive" data-disclamer="' + disclaimer + '" class=" l_q_fininsentive badge disclaimer" data-num="' + num + '">' + num + '</span>' + '<span style="float:right; "><b>$' + temp_finance_price + '</b></span></li>';
                                  }

                                }
                            });


                            
                            if (incentivesBonusCash_available) {
                                 console.log(' ====    incentivesBonusCashList   Entered =====');
                                $.each(result['incentivesBonusCashList'], function (key, value) {
                                    var num = 20;
                                    incv += '<li><span><img alt="incentive-icon" src="/images/incentive-icon-5.png" width=\"20\" height=\"20\" />' + key + '&nbsp;';
                                    if(value.disclaimer != ''){
                                        incv +='<span data-disclamer="' + value.disclaimer + '" id="l_q_fininsentive" class="l_q_insentive badge disclaimer" data-num="' + num + '">' + num + '</span>';
                                        num++;
                                    }
                                    incv += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span style="float:right; "><b>$' + value.discount + '</b></span></span></li>';
                                });
                            }
                            incv += '</ul>';

                            $('.fcalc_incentives_desc').html(incv);
                        }

                        //Explore 	
                        if (result['explores'].length == 0) {
                            explore = "";
                            $('.expFAddOffMerkle > i').hide();
                            $('.expFAddOffMerkle').next('div.addOffers').hide();
                        } else {
                            var explore = '<ul class="list-group reg">';
                            var num = 13;
                            $.each(result['explores'], function (index, item) {
                                num = num + 1;
                                var groupName = '';
                                $.each(item['name'], function (index1, item1) {
                                    groupName += item1 + ',';
                                });

                               var disclaimer = item['disclaimer'][0];
                                    var item_feature = item['feature'] ;
                                    if(item['feature'] == 'Engine') item_feature = 'Engine Level Incentive';
                                    if(item['feature'] == 'V6') item_feature = 'Engine Level Incentive';
                                    if(item['feature'] == 'V8') item_feature = 'Engine Level Incentive';
                                    if(item['feature'] == 'Package') item_feature = 'Package Level Incentive';
                                    if(item['feature'] == 'Hemi') item_feature = 'Engine Level Incentive';
                                    if(item['feature'] == 'Transmission') item_feature = 'Transmission Level Incentive';
                                    
                                    if(item['feature'] == 'Chrysler-capital-incentives') item_feature = 'Chrysler Capital Incentive';


                                explore += '<li>';
                                explore += '<label class="customCheckBox"><input type="checkbox" class="finance_chk ' + item['feature'] + '" name="finance_explores[]" id="finace_explores_' + item['feature'] + '" value="' + item['ids'] + '" data-original-groups = "' + groupName.replace(/,\s*$/, "") + '" data-original-amount = "' + item['amount'] + '">';
                                explore += '<span></span><b>' + item_feature + '</b></label><span style="float:right"><b>$' + item['amount'] + '</b></span>' + ' &nbsp;<span id="l_q_fininsentive" data-disclamer="' + disclaimer + '" class=" l_q_fininsentive badge disclaimer" data-num="' + num + '">' + num + '</span>';
                                explore += '</li>';
                                var countnamef = parseInt(item['name'].length);
                                var amtf = parseInt(item['amount']) / countnamef;
                                $.each(item['name'], function (index1, item1) {
                                    explore += '<li style="padding-left:30px;" class="fin_offer_subsets" data-relation-name="finace_explores_' + item['feature'] + '" data-original-items="' + item1 + '" data-original-fin-incentive-count="' + countnamef + '" data-original-items-class="' + item['feature'] + '" data-original-fin-incentive-amount="' + item['inv_amount'][index1] + '">' + item1 + '&nbsp;&nbsp;<b> $' + item['inv_amount'][index1] + '</b>' + '</li>';
                                });
                            });
                            explore += '</ul>';
                        }
                        $('.fdealer_disc_update').html(result['dlrDiscLists']);
                        $('.fcalc_offers_desc').html(explore);


                        // $('.f_dealer_discount').html('<span style="color: green; weight: bold;">-</span> $'+result['explores_sum_amount']);

                        //Terms Dropdown
                       /*  var sele = '<select class="form-control duration ore_dropdown_finance_terms" id="finance_terms">';
                        for (var i = 0; i < result['terms'].length; i++) {
                            sele += '<option value="' + result['terms'][i] + '">' + result['terms'][i] + ' Months</option>';
                        }
                        sele += '</select>'; */
						
                        def = '';
						  var sele_f = '<label style="display:none;" for="finance_terms">hide me</label>';
                          sele_f += '<select class="form-control duration ore_dropdown_finance_terms" id="finance_terms">';
                        for (var i = 0; i < result['terms'].length; i++) {
							
							// if(result['default_finance'] == result['terms'][i]) 
							// 	def = 'selected = "select"';
							// else 
							// 	def = '';
                            sele_f += '<option value="' + result['terms'][i] + '"  '+def+' >' + result['terms'][i] + ' Months</option>';
                        }
                        sele_f += '</select>';

                        $('.finance_wrapper').html(sele_f);
                    }else{
                          if (totalIncen1 == null || totalIncen1 == 0) {
                            $('.IncentivesfMerkle > i').hide();
                            $('.IncentivesfMerkle').removeClass('noDropdownHover');
                            $('.IncentivesfMerkle').addClass('noDropdownHover');
                            $('.IncentivesfMerkle').next('div.addOffers').hide();
                            $('.fDestCharge').html('<span style="color: green; weight: bold;">-</span> $' + onlyIncentives);
                            //$('.fcalc_incentives_desc').html('<ul class="list-group reg"><li>No Incentives Applicable.</li></ul>');
                        } else {
                                $('.IncentivesfMerkle').removeClass('noDropdownHover');
                                    var rebateDetailsfinalamount = result['rebateDetailsfinalamount'];
                                    var rebateDetailsid_string = result['rebateDetailsid']
                                    var rebateDetailsid_arr = rebateDetailsid_string.split(',');

                                    $('.fDestCharge').html('<span style="color: green; weight: bold;">-</span> $' + onlyIncentives);
                                    var incv = '<ul class="list-group reg">';
                                    $.each(result['incentiveNames'], function (index, item) {
                                        var temp_finance_price = result["man_incentives_sin_name"][index];
                                        if (result["man_incentives_sin_name"][index] != null && result["man_incentives_sin_name"][index] != undefined && result["man_incentives_sin_name"][index] != '') {
                                            var num = index + 11;
                                            var disclaimer = '';
                                            if (result['incentive_disclaimer'].length > 0) {
                                                disclaimer = result['incentive_disclaimer'][index];
                                            }

                                            
                                            var status = global.CheckIncentives(result["incentiveIds"][index], rebateDetailsid_arr);
                                            if (status) {
                                                incv += '<li data-incentiveid="' + result["incentiveIds"][index] + '"><img alt="incentive-icon" src="/images/incentive-icon-5.png" width=\"20\" height=\"20\" /> ' + item + '&nbsp;&nbsp;' + '<span id="l_q_fininsentive" data-disclamer="' + disclaimer + '" class=" l_q_fininsentive badge disclaimer" data-num="' + num + '">' + num + '</span>' + '<span style="float:right; "><b>$' + temp_finance_price + '</b></span></li>';
                                        }

                                        }
                                    });
                                    if (incentivesBonusCash_available) {
                                        console.log(' ====    incentivesBonusCashList   Entered =====');
                                        $.each(result['incentivesBonusCashList'], function (key, value) {
                                            var num = 20;
                                            incv += '<li><span><img alt="incentive-icon" src="/images/incentive-icon-5.png" width=\"20\" height=\"20\" />' + key + '&nbsp;';
                                            if(value.disclaimer != ''){
                                                incv +='<span data-disclamer="' + value.disclaimer + '" id="l_q_fininsentive" class="l_q_insentive badge disclaimer" data-num="' + num + '">' + num + '</span>';
                                                num++;
                                            }
                                            incv += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span style="float:right; "><b>$' + value.discount + '</b></span></span></li>';
                                        });
                                    }
                                    incv += '</ul>';

                                    $('.fcalc_incentives_desc').html(incv);    
                            }
                    }


                    /* FINANCE */
                    /*****  BEGIN: Non-Incentives Records Shows alert *****/
                    $('.finance_main_incentive').html('<span style="color: green; weight: bold;">-</span> $' + (totalIncenDiscounts));
                    $('.mDestFExplore').html('<span style="color: green; weight: bold;">-</span> $' + totalExplore);
                    // Incentive Amount Updated

                    //$('.mDestFExplore').html('<span style="color: green; weight: bold;">-</span> $'+( totalExplore )); 

                    //Incentive text hid
                    var incentive_fav = result['rebateDetailsid'].split(',');
                    var incentive_fav_unique = incentive_fav.filter(function (itm, i, incentive_fav) { return i == incentive_fav.indexOf(itm); });

                    var current_ince_id;
                    $.each($(".fcalc_incentives_desc>ul>li"), function (e) {
                        current_ince_id = $(this).attr('data-incentiveid');
                        if(current_ince_id != undefined){
                            if (jQuery.inArray(current_ince_id, incentive_fav_unique) !== -1) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        }
                    });

                    /* FINANCE */
                    // Explore Feature hide
                    var favorite = result['rebateDetails'].split(',');
                    var favorite_id = [];
                    $.each($(".finance_chk:checked"), function () {

                        favorite_id.push($(this).attr('id'));
                    });

                    var favorite_id_unique = favorite_id.filter(function (itm, i, favorite_id) { return i == favorite_id.indexOf(itm); });
                    var finpartialamt = 0;
                    var finance_partial_status = false;
                    var finanace_accepted_partial = 0;
                    var chkfinclass = [];

                     /* FINANCE */
                    /* Verify all Man incentive stack ability */
                     var frebateDetailsID = result['rebateDetailsid'].split(',');
                     
                     $.each($(".finv_add_offers"), function () {
                         $(this).removeClass('crosstext');                      
                          dataIncentiveFID = $(this).attr('data-incentiveid'); 
                          if (jQuery.inArray(dataIncentiveFID, frebateDetailsID) === -1) { // FALSE
                              $(this).addClass('crosstext');                          }
                     }); 
                      /* FINANCE */
                      /* Verify Explore incentive stack ability */
                    $('.fin_offer_subsets').removeClass('incentiveMatches');
                    $.each($(".fin_offer_subsets"), function () {

                        dataRelationName = $(this).attr('data-relation-name');
                        dataOriginalItems = $(this).attr('data-original-items');
                        $(this).removeClass('crosstext');
                        if (jQuery.inArray(dataRelationName, favorite_id_unique) !== -1) { // TRUE 

                            if (jQuery.inArray(dataOriginalItems, favorite) !== -1) { // TRUE 

                                $(this).removeClass('incentiveMatches');
                                finanace_accepted_partial += parseInt($(this).attr('data-original-fin-incentive-amount'));
                                chkfinclass.push($(this).attr('data-original-items-class'));
                            } else {
                                $(this).addClass('incentiveMatches');
                                finpartialamt += parseInt($(this).attr('data-original-fin-incentive-amount'));
                                finance_partial_status = true;
                                $(this).addClass('crosstext');
                            }
                        }
                    });
                    ////
                    var financeflagindexvalue = [];
                    var subval = 0;
                    $.each($(".finance_chk:checked"), function (index, value) {
                        financeflagindexvalue[index] = $(this).attr('data-original-amount');

                    });
                    console.log('financeflagindexvalue', financeflagindexvalue);
                    /// 
                    var mFeatureDeduction = 0;
                    $.each($(".finance_chk:checked"), function (ind, val) {
                        var dor = $(this).attr('data-original-groups');
                        var mflag = 0;
                        var dorArray = dor.split(',');
                        $.each(dorArray, function (index, value) {
                            if (jQuery.inArray(value, favorite) == -1) { mflag = parseInt(mflag) + 1; }
                        });

                        if (mflag > 0) {
                            //$(this).prop('checked', false);
                            // mFeatureDeduction = $(this).attr('data-original-amount');
                            // totalExplore = parseInt(result['explore_amount']) - mFeatureDeduction;
                            // onlyIncentives = totalIncen - parseInt(totalExplore);
                            //subval += parseInt(financeflagindexvalue[ind]);
                            mFeatureDeduction = $(this).attr('data-original-amount');

                            totalExplore = parseInt(result['explore_amount']) - mFeatureDeduction;

                        }

                    });
                    onlyIncentives = parseInt(totalIncen) - parseInt(finanace_accepted_partial);
                    totalExplore = parseInt(finanace_accepted_partial);
                    // $.each(chkfinclass, function (index, value) {
                    //     $("." + value).prop("checked", "true");
                    // });

                    // if (finance_partial_status) {
                    //     expamt = parseInt(result['explore_amount']) - finanace_accepted_partial;
                    //     onlyIncentives = totalIncen - expamt;
                    //     $.each(chkfinclass, function (index, value) {
                    //         $("." + value).prop("checked", "true");
                    //     });
                    // } else {
                    //     //console.log('finpartialamt',finpartialamt);
                    //     expamt = parseInt(result['explore_amount']) - subval;
                    //     onlyIncentives = totalIncen - expamt;
                    // }

                    /*****  END: Non-Incentives Records Shows alert *****/
                    /* FINANCE */
                    // Incentives, Monthly, Capitalized Costs
                    $('.ore_finance_emi, .main_msrp_finance').text('$' + result['paymentWithTaxesVal']['monthlyPayment']);
                    $('.ore_disp_finance_estimate').text('$' + result['paymentWithTaxesVal']['amountFinanced']);
                    $('.ore_emi_finance_month').text(result['terms'][0]);
                    $('.chrysler_apr_in').html(result['paymentWithTaxesVal']['apr']);
                    $('.ore_radio_apr').val(result['paymentWithTaxesVal']['apr']);

                    $('.fDestCharge').html('<span style="color: green; weight: bold;">-</span> $' + onlyIncentives);
                    $('.mDestFExplore').html('<span style="color: green; weight: bold;">-</span> $' + totalExplore);
                    $('.finance_main_incentive').html('<span style="color: green; weight: bold;">-</span> $' + (totalIncenDiscounts));
                    // setTimeout(function () {
                    //     if (onlyIncentives == 0) {
                    //         $('.fDestCharge').siblings('i').remove();
					// 	}
                    // }, 5000);

                    //Taxes 
                    /*var tax = '<ul class="list-group reg">';
                    if(result['paymentWithTaxesVal']['capitalizedTaxes']['salesTaxAmount'] != undefined){
                    	tax += '<li><b>Sales Tax		 </b> <span style="float: right;">$' + result['paymentWithTaxesVal']['capitalizedTaxes']['salesTaxAmount'] + '</span><hr></li>';
                    }
                    if( result['paymentWithTaxesVal']['capitalizedTaxes']['salesTax']['taxParameters']['rate'] != undefined){
                    	tax += '<li><b>Sales Tax Terms	 </b> <span style="float: right;">' + result['paymentWithTaxesVal']['capitalizedTaxes']['salesTax']['taxParameters']['rate'] + ' Months</span><hr></li>';
                    }
					
                    tax += '</ul>';

                    $('.fcalc_taxes_desc').html(tax);*/

                } else if (transactionType == 'cash') {
                    $("input.cash_chk").removeAttr("disabled");
                    $('.price-tag-cash').show();
                    if (result['restrict']) {
                        $('.cash_restrict').hide();
                        $('.cash_restrict_html').html('Price details are currently unavailable. Please try again later.');

                    } else {
                        $('.cash_restrict').show();
                    }

                    if ($('.price-tag-cash').data('env') == 'local' || $('.price-tag-cash').data('env') == 'dev') {
                        $('.cash_restrict').show();
                    }
                    if (result['isCCAPAvailale']) {
                        $('.offer_ccap_tab_span').show();
                    } else {
                        $('.offer_ccap_tab_span').hide();
                    }
                    /* CASH */
                    var incentivesBonusCash_available = result['incentivesBonusCash_available'];
                    var incentivesBonusCash_amount = parseInt(result['incentivesBonusCash_amount']);

                    var totalIncen = parseInt(result['rebateDetailsfinalamount']);
                    var totalIncen1 = parseInt(result['incentiveAmount']) + incentivesBonusCash_amount;  
                    var totalExplore = parseInt(result['explore_amount']);
                    var totalDlrDisc = parseInt(result['dlrDiscAmount']);
                    var totalIncenDiscounts = totalIncen + parseInt(totalDlrDisc);
                    var onlyIncentives = totalIncen - parseInt(result['explore_amount']);

                    $('.mDestCExplore').html('<span style="color: green; weight: bold;">-</span> $' + totalExplore);

                    if (totalIncen1 == null || totalIncen1 == 0) {
                        $('.cDestCharge').html('<span style="color: green; weight: bold;">-</span> $' + totalIncen1);
                        $('.IncentivescMerkle > i').hide();
                        $('.IncentivescMerkle').removeClass('noDropdownHover');
                        $('.IncentivescMerkle').addClass('noDropdownHover');
                        $('.IncentivescMerkle').next('div.addOffers').hide();
                        //$('.cdealer_disc_update').html('<ul class="list-group reg"><li>No Dealer Discounts Available.</li></ul>');
                    } else {
                        $('.IncentivescMerkle').removeClass('noDropdownHover');
                        $('.cDestCharge').html('<span style="color: green; weight: bold;">-</span> $' + totalIncen1);
                    }

                    $('.cash_main_incentive').html('<span style="color: green; weight: bold;">-</span> $' + (totalIncen1 + totalExplore + totalDlrDisc));

                    if (totalDlrDisc == null || totalDlrDisc == 0) {
                        $('.mDestCDlrDisc').html('<span style="color: green; weight: bold;">-</span> $' + totalDlrDisc);
                        //$('.fdealer_disc_update').html('<ul class="list-group reg"><li>No Dealer Discounts Available.</li></ul>');
                        $('.DlrDiscCMerkle > i').hide();
                        $('.DlrDiscCMerkle').next('div.addOffers').hide();
                    } else {
                        $('.DlrDiscCMerkle > i').show();
                        $('.DlrDiscCMerkle').next('div.addOffers').removeAttr('style');
                        $('.mDestCDlrDisc').html('<span style="color: green; weight: bold;">-</span> $' + totalDlrDisc);                        
                        $('.cdealer_disc_update').html(result['dlrDiscLists']);
                    }

                    /* CASH */
                    if (methods == 'onload') {
                        //Incentives
                        if (totalIncen1 == null || totalIncen1 == 0) {
                            $('.cDestCharge').html('<span style="color: green; weight: bold;">-</span> $' + totalIncen1);
                            $('.IncentivescMerkle > i').hide();
                            $('.IncentivescMerkle').next('div.addOffers').hide();
                            //$('.ccalc_incentives_desc').html('<ul class="list-group reg"><li>No Incentives Applicable.</li></ul>');
                        } else {
                            $('.cDestCharge').html('<span style="color: green; weight: bold;">-</span> $' + totalIncen1);
                            var incv = '<ul class="list-group reg">';
                            $.each(result['incentiveNames'], function (index, item) {
                                var num = index + 11;
                                var disclaimer = '';
                                if (result['incentive_disclaimer'].length > 0) {
                                    disclaimer = result['incentive_disclaimer'][index];
                                }
                                var temp_cash_price = result["man_incentives_sin_name"][index];
                                if (result["man_incentives_sin_name"][index] != null && result["man_incentives_sin_name"][index] != undefined && result["man_incentives_sin_name"][index] != '') {
                                    incv += '<li data-incentiveid="' + result["incentiveIds"][index] + '" class="cinv_add_offers"><img alt="incentive-icon" src="/images/incentive-icon-5.png" width=\"20\" height=\"20\" /> ' + item + '&nbsp;&nbsp;' + '<span data-num="' + num + '" id="l_q_cashinsentive" data-disclamer="' + disclaimer + '" class=" l_q_cashinsentive badge disclaimer">' + num + '</span>' + '<span style="float:right; "><b>$' + temp_cash_price + '</b></span></li>';
                                }
                            });
                            if (incentivesBonusCash_available) {
                                // console.log(result['incentivesBonusCashList']);
                                $.each(result['incentivesBonusCashList'], function (key, value) {
                                   var num = 20;
                                    incv += '<li><span><img alt="incentive-icon" src="/images/incentive-icon-5.png" width=\"20\" height=\"20\" />' + key + '&nbsp;';
                                    if(value.disclaimer != ''){
                                        incv +='<span data-disclamer="' + value.disclaimer + '" id="l_q_cashinsentive" class="l_q_insentive badge disclaimer" data-num="' + num + '">' + num + '</span>';
                                        num++;
                                    }
                                    incv += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span style="float:right; "><b>$' + value.discount + '</b></span></span></li>';
                                });
                             }
                            incv += '</ul>';

                            $('.ccalc_incentives_desc').html(incv);
                        }
                        /* CASH */
                        //Explore 	
                        if (result['explores'].length == 0) {
                            explore = "";
                            $('.expCAddOffMerkle > i').hide();
                            $('.expCAddOffMerkle').next('div.addOffers').hide();
                        } else {
                            var explore = '<ul class="list-group reg">';
                            var num=13;
                            $.each(result['explores'], function (index, item) {
                                var groupName = '';
                                	var disclaimer = item['disclaimer'][0];
                                    var item_feature = item['feature'] ;
                                    if(item['feature'] == 'Engine') item_feature = 'Engine Level Incentive';
                                    if(item['feature'] == 'V6') item_feature = 'Engine Level Incentive';
                                    if(item['feature'] == 'V8') item_feature = 'Engine Level Incentive';
                                    if(item['feature'] == 'Package') item_feature = 'Package Level Incentive';
                                    if(item['feature'] == 'Hemi') item_feature = 'Engine Level Incentive';
                                    if(item['feature'] == 'Transmission') item_feature = 'Transmission Level Incentive';
                                    
                                    if(item['feature'] == 'Chrysler-capital-incentives') item_feature = 'Chrysler Capital Incentive';
                                 num=num+1;
                                $.each(item['name'], function (index1, item1) {
                                    groupName += item1 + ',';
                                });

                                explore += '<li>';
                                explore += '<label class="customCheckBox"><input type="checkbox" class="cash_chk" name="cash_explores[]" id="cash_explores_' + item['feature'] + '" value="' + item['ids'] + '" data-original-groups = "' + groupName.replace(/,\s*$/, "") + '">';
                                explore += '<span></span><b>' + item_feature + '</b></label><span style="float:right"><b>$' + item['amount'] + '</b></span>'+' &nbsp;<span data-num="'+num+'" id="l_q_cashinsentive" data-disclamer="'+disclaimer+'" class=" l_q_cashinsentive badge disclaimer">'+num +'</span>';
                                explore += '</li>';

                                /* explore += '<li><input type="checkbox" class="cash_chk" name="cash_explores[]" id="cash_explores_' + item['feature'] + '" value="' + item['ids'] + '">  <b>' + item['feature'] + '<span style="float:right">' + '$' + item['amount'] + '</span></b></li>'; */
								  $.each(item['name'], function (index1, item1) { 
                                    explore += '<li style="padding-left:30px;" class="cin_offer_subsets" data-relation-name="cash_explores_' + item['feature'] + '" data-original-items="' + item1 + '" data-original-items-class="' + item['feature'] + '" data-original-incentiveid="' + item['ids'] + '" data-original-incentive-count="' + countname + '" data-original-incentive-amount="' + item['inv_amount'][index1] + '">' + item1 + '&nbsp;&nbsp;<b> $' + item['inv_amount'][index1] + '</b>' + '</li>';
                                });

							   /* $.each(item['name'], function (index1, item1) {
                                    explore += '<li style="padding-left:30px;" class="cin_offer_subsets" data-relation-name="cash_explores_' + item['feature'] + '" data-original-items="' + item1 + '">' + item1 + '</li>';
                                }); */
                            });
                            explore += '</ul>';
                        }

                        $('.cdealer_disc_update').html(result['dlrDiscLists']);
                        $('.ccalc_offers_desc').html(explore);


                        /* CASH */

                        /*****  BEGIN: Non-Incentives Records Shows alert *****/
                        // Incentive Amount Updated

                        //Incentive text hid
                        /* 						var incentive_fav = result['rebateDetailsid'].split(',');
                        						var incentive_fav_unique = incentive_fav.filter(function(itm, i, incentive_fav) { return i == incentive_fav.indexOf(itm); }); 
                        						 
                        						var current_ince_id ;
                        						$.each($(".ccalc_incentives_desc>ul>li"), function(e){  
                        							current_ince_id = $(this).attr('data-incentiveid'); 
                        							 if(jQuery.inArray( current_ince_id, incentive_fav_unique ) !== -1){ 
                        									$(this).show(); 
                        								 }  else{
                        									 $(this).hide(); 
                        								}
                        						}); */


                        // Explore Feature hide
                        /* var favorite = result['rebateDetails'].split(',');
                        var favorite_id = [];
                        $.each($(".cash_chk:checked"), function(){            
                        	 
                        	favorite_id.push($(this).attr('id'));
                        });
					
                        var favorite_id_unique = favorite_id.filter(function(itm, i, favorite_id) { return i == favorite_id.indexOf(itm); }); 
					
                         $('.cin_offer_subsets').removeClass('incentiveMatches');
                        $.each($(".cin_offer_subsets"), function(){
                        	
                        	dataRelationName	 = $(this).attr('data-relation-name');
                        	dataOriginalItems 	 = $(this).attr('data-original-items');
                        	
                        	if(jQuery.inArray( dataRelationName, favorite_id_unique ) !== -1){ // TRUE 
                        	 
                        		if(jQuery.inArray( dataOriginalItems, favorite ) !== -1){ // TRUE 
                        			 $(this).removeClass('incentiveMatches');
                        		}else{  
                        			 $(this).addClass('incentiveMatches');
                        		} 
                        	}
                        });
					
					
                         $.each($(".cash_chk:checked"), function(){ 
                        		var dor = $(this).attr('data-original-groups');								
                        		var mflag = 0;
                        		 var dorArray = dor.split(','); 
                        		$.each(dorArray, function(index, value){ 
                        			if(jQuery.inArray( value, favorite ) == -1){ mflag = parseInt(mflag) + 1; } 
                        		});
                        		
                        		if(mflag > 0){
                        			$(this).prop('checked',false); 
                        		}
                        		
                        });  */

                        /* CASH */
                        /*****  END: Non-Incentives Records Shows alert *****/
                        $('.cash_main_incentive').html('<span style="color: green; weight: bold;">-</span> $' + (totalIncen1 + totalExplore + totalDlrDisc));
                        $('.mDestCExplore').html('<span style="color: green; weight: bold;">-</span> $' + totalExplore);

                        //$('.cash_dealer_discount').html('<span style="color: green; weight: bold;">-</span> $'+result['explores_sum_amount']);

                        //Terms Dropdown
                        /* var sele = '<select class="form-control duration ore_dropdown_cash_terms" id="cash_terms">';
                        for (var i = 0; i < result['terms'].length; i++) {
                        	sele += '<option value="' + result['terms'][i] + '">' + result['terms'][i] + ' Months</option>';
                        }
                        sele += '</select>';

                        $('.cash_wrapper').html(sele); */
                    }else{

                    }

                    
                    var favorite = result['rebateDetails'].split(',');
                    var favorite_id = [];
                    $.each($(".cash_chk:checked"), function(){            
                         
                        favorite_id.push($(this).attr('id'));
                    });
                
                    var favorite_id_unique = favorite_id.filter(function(itm, i, favorite_id) { return i == favorite_id.indexOf(itm); }); 
                
                     $('.cin_offer_subsets').removeClass('incentiveMatches');
                     var cashclass = [];
                     var acceptincentiv=0;
                     var rejectincentive=0;

                     /* CASH */
                    /* Verify all Man incentive stack ability */
                     var crebateDetailsID = result['rebateDetailsid'].split(',');
                     
                     $.each($(".cinv_add_offers"), function () {
                         $(this).removeClass('crosstext');                      
                          dataIncentiveCID = $(this).attr('data-incentiveid'); 
                          if (jQuery.inArray(dataIncentiveCID, crebateDetailsID) === -1) { // FALSE
                              $(this).addClass('crosstext');                          }
                     }); 


                    $.each($(".cin_offer_subsets"), function(){
                     
                        dataRelationName = $(this).attr('data-relation-name');
                        dataOriginalItems = $(this).attr('data-original-items');

                        $(this).removeClass('crosstext');
                        if(jQuery.inArray( dataRelationName, favorite_id_unique ) !== -1){ // TRUE 
                         
                            if(jQuery.inArray( dataOriginalItems, favorite ) !== -1){ // TRUE 
                                $(this).removeClass('incentiveMatches');
                                cashclass.push($(this).attr('data-original-items-class'));
                                acceptincentiv += parseInt($(this).attr('data-original-incentive-amount'));
                            }else{  
                                 $(this).addClass('incentiveMatches');
                                 $(this).addClass('crosstext');
                                 rejectincentive += parseInt($(this).attr('data-original-incentive-amount'));
                            } 
                        }
                    });
                
                
                     $.each($(".cash_chk:checked"), function(){ 
                            var dor = $(this).attr('data-original-groups');								
                            var mflag = 0;
                             var dorArray = dor.split(','); 
                            $.each(dorArray, function(index, value){ 
                                if(jQuery.inArray( value, favorite ) == -1){ mflag = parseInt(mflag) + 1; } 
                            });
                            
                            if(mflag > 0){
                               // $(this).prop('checked',false); 
                            }
                            
                    });

                   // console.log('acceptincentiv',acceptincentiv);
                   // console.log('rejectincentive',rejectincentive);
                    totalExplore = parseInt(acceptincentiv);
                    // Incentives, Monthly, Capitalized Costs
                   // console.log('outthedoor',result['outTheDoorPrice']);
                    var outTheDoorPrice=result['outTheDoorPrice'].replace(/,/g , '');

                    if(outTheDoorPrice < 1) {
                        outTheDoorPrice = 0;
                    }
                    else {
                        outTheDoorPrice = result['outTheDoorPrice'];
                    }

                    $('.ore_cash_emi, .main_msrp_cash').html(outTheDoorPrice);
                    $('.mDestCExplore').html('<span style="color: green; weight: bold;">-</span> $' + totalExplore);

                    var totalIncen = parseInt(result['rebateDetailsfinalamount']);
                    var totalIncen1 = parseInt(result['incentiveAmount']) + incentivesBonusCash_amount;  
                    var totalExplore = parseInt(result['explore_amount']);
                    var totalDlrDisc = parseInt(result['dlrDiscAmount']);
                    var totalIncenDiscounts = totalIncen + parseInt(totalDlrDisc);
                    $('.cash_main_incentive').html('<span style="color: green; weight: bold;">-</span> $' + (totalIncenDiscounts));
                    //Taxes 
                    /* var tax = '<ul class="list-group reg">';
                    if(result['paymentWithTaxesVal']['capitalizedTaxes']['salesTaxAmount'] != undefined){
                    	tax += '<li><b>Sales Tax		 </b> <span style="float: right;">$' + result['paymentWithTaxesVal']['capitalizedTaxes']['salesTaxAmount'] + '</span><hr></li>';
                    }
                    if( result['paymentWithTaxesVal']['capitalizedTaxes']['salesTax']['taxParameters']['rate'] != undefined){
                    	tax += '<li><b>Sales Tax Terms	 </b> <span style="float: right;">' + result['paymentWithTaxesVal']['capitalizedTaxes']['salesTax']['taxParameters']['rate'] + ' Months</span><hr></li>';
                    }
					
                    tax += '</ul>';

                    $('.fcalc_taxes_desc').html(tax); */

                } else {

                }
                break;
            case "baseCarNow":
                console.log("baseCarNow Submit");
                global.loaderHide('.loader');
                break;
            case "leadSubmit":
                console.log("Lead Succesfully Submit");
                break;
            case "initial_lead":
                $('.initial_popup_header_name').html(result.first + ' ' + result.last + '!');
                $('#first').val(result.first);
                $('#last').val(result.last);
                $('#contact_email').val(result.contact_email);
                $('#contact_phone').val(result.contact_phone);
                $('#postalcode').val(result.postalcode);
                $('#postalcode').val(result.postalcode);
                var review_chk_box_checked = (result.chk_box_home_delivery == 'false') ? false : true;
                $('#review_chk_box_home_delivery').prop('checked',review_chk_box_checked);
                $('#initialPopUp').modal('hide');
                $('#initialPopUp').on('hidden.bs.modal', function () {
                    $('#initialPopUpThanks').modal('show');
                });
                break;
        }

    },
    failure: function (url, response, textStatus, jqXHR) {
        switch (url) {
            case "payment-calcultor":
                if (response.status != 200) {
                    var currenttabs = $('ul.finance_tabs>li.active>a').attr("id");

                    $("input.lease_chk").removeAttr("disabled");
                    $("input.finance_chk").removeAttr("disabled");
                    $("input.cash_chk").removeAttr("disabled");

                    $('#lease>p, #lease>a').hide();
                    $('#lease>p, #lease>a').hide();
                    $('#lease > div.marginY-3').html('Price details currently unavailable. Please try again later.');

                   if(currenttabs == 'tab_lease'){
						$('#lease > div.marginY-3').html('Price details currently unavailable. Please try again later.');
					} 
					
					if(currenttabs == 'tab_finance'){
                        $('div.finance_restrict').html('Price details currently unavailable. Please try again later.');
                        $('#finance > div.marginY-3').html('Price details currently unavailable. Please try again later.');
                        $('.finance_restrict').hide();
					}
					if(currenttabs == 'tab_cash'){
						$('div.cash_restrict_html').html('Price details currently unavailable. Please try again later.'); 
						$('a.cash_restrict').hide();
					}
                }
                break;
            case "initial_lead":
                 
                json = $.parseJSON(response.responseText);
                $.each(json.errors, function (key, value) {
                    console.log('<p>' + value + '</p>');
                    $('.alert-danger').show();
                    $('.alert-danger').append('<p>' + value + '</p>');
                });
                $("#result").html('');
                 
                break;
        }
    },
    statusCode: function (url, responseObject, textStatus, jqXHR) {
        switch (url) {
            case "payment-calcultor":
                global.dd("URL: " + url);
                global.dd(responseObject.status);
                global.dd(jqXHR);
                break;
        }
    }
}
/**
 *  validation Setup
 * 
 * @var validation
 *  
 * */
var validation = {
    digitValidate: function (digit_counts, field_id, err_msg_span, err_msg) {
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
            return true;
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
    remove: function (key) {
        localStorage.removeItem(key);
    },
    exist: function (key) {
        return localStorage.getItem(key) !== null;
    },
    get: function (key) {
        return localStorage.getItem(key);
    },
    set: function (key, value) {
        localStorage.setItem(key, value);
        return true;
    }
};

function AddZeroPrefixZipcode(zipcode){
    str_zip_length = zipcode.length;
    if(0 < str_zip_length && 4 == str_zip_length ){
        zipcode = '0'+ zipcode;
    }
    return zipcode;
}

$(document).ready(function () {
    $('#contact_phone').mask('(000) 000-0000');
    $('#init_pop_contact_phone').mask('(000) 000-0000');
});



//Lead
//Tradein
//pageInfo

var OreCookie = {
    //Initiate and create one Cookie for entire website
    create: function (cookie_name, json_values) {
        Cookies.set(cookie_name, json_values, { expires: 8 });
    },
    //read cookie json
    read_JSON: function (cookie_name) {
        return JSON.parse(Cookies.get(cookie_name));
    },
    add: function (cookie_name, key, values) {
        var new_cookie = OreCookie.read_JSON(cookie_name);
        new_cookie[key] = values;
        console.log(new_cookie);
        Cookies.set(cookie_name, JSON.stringify(new_cookie));
    },
    exists: function (cookie_name) {
        if (typeof Cookies.get(cookie_name) === 'undefined') {
            console.log('No cookie.');
            return false;
        } else {
            console.log('cookie found.');
            return true;
        };
    },
    delcookie: function (cookie_name, key) {
        var new_cookie = OreCookie.read_JSON();
        delete new_cookie[key];
        Cookies.set(cookie_name, new_cookie);
    },
    //This will update the existing key value
    update: function (cookie_name, key, newValues) {
        var myObject = OreCookie.read_JSON();
        OreCookie.findAndReplace(myObject, key, newValues);
        Cookies.set(cookie_name, myObject);
    },
    addBefore: function (cookie_name, name, key, values) {
        //-1 mean before
        var myObject = OreCookie.read_JSON();
        OreCookie.addvalues(myObject, key, newValues);
        Cookies.set(cookie_name, myObject);
    },
    addAfter: function (cookie_name, name, key, value) {
        //1 mean after
        var myObject = OreCookie.read_JSON();
        OreCookie.addvalues(myObject, key, newValues);
        Cookies.set(cookie_name, myObject);
    },
    findAndReplace: function (object, key, newValues) {
        for (var x in object) {
            if (object.hasOwnProperty(x)) {

                if (typeof object[x] == 'object') {
                    OreCookie.findAndReplace(object[x], key, newValues);
                }

                if (x == key) {
                    object[x] = newValues;
                    break;
                }
            }
        }
        return 1;
    }
}
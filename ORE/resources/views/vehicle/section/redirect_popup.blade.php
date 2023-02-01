@extends('layouts.redirect')
@section('title', 'DriveFCA | Invalid Vehicle Details')
@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>


<script type="text/javascript">
var settimeinterval;

function showError(error) {
    var err = 'Please enter Zip Code to search the latest inventory in your area.';
    switch (error.code) {
        case error.PERMISSION_DENIED:
            // alert('Location is disabled. ' + err);
            var contents = 'Location is disabled. ' + err;
            var rs = T3AlertMessage(title = 'Alert!', contents);
            // console.log('rs', rs);
            //  defaultRedirect();
            break;
        case error.POSITION_UNAVAILABLE:
            // alert('Location information is unavailable. ' + err);
            var contents = 'Location information is unavailable. ' + err;
            T3AlertMessage(title = 'Alert!', contents);
            // defaultRedirect();
            break;
        case error.TIMEOUT:
            //alert('The request to get user location timed out. ' + err);
            var contents = 'The request to get user location timed out. ' + err;
            T3AlertMessage(title = 'Alert!', contents);
            //defaultRedirect();
            break;
        case error.UNKNOWN_ERROR:
            //alert('An unknown error occurred. ' + err);
            var contents = 'An unknown error occurred. ' + err;
            T3AlertMessage(title = 'Alert!', contents);
            //defaultRedirect();
            break;
    }
}

function showPosition(position) {
    var location = [];
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;

    localCache.set('latitude', latitude);
    localCache.set('longitude', longitude);
    ajax.promise('find_zip_by_cord', 'post', JSON.stringify({
        latitude: latitude,
        longitude: longitude
    }));
    return false;

}

function pop() {
    if (navigator.geolocation) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError, {
                enableHighAccuracy: false,
                timeout: 20000
            });
        } else {
            // alert('Geolocation is not supported by this browser so we shows vehicles with default zipcode.');

            var contents = 'Geolocation is not supported by this browser so we shows vehicles with default zipcode.';
            T3AlertMessage(title = 'Alert!', contents);

        }
    } else {
        //alert("Geolocation is not supported by this browser.");
        var contents = 'Geolocation is not supported by this browser.';
        T3AlertMessage(title = 'Alert!', contents);
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
        return localStorage.getItem(key);
    },
    set: function(key, value) {
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


        var appurl = $('#APP_URL').val();
        if (requestType == 'get') {
            var jqxhr = $.get(appurl + url, function(result) {

            }).fail(function(response, textStatus, jqXHR) {
                ajax.failure(url, textStatus);
                global.loaderHide('.loader');
            });
        } else {
            $.when($.ajax({
                    url: appurl + url,
                    cache: true,
                    type: requestType,
                    data: JSON.parse(data)
                }))
                .done(function(result, textStatus, jqXHR) {
                    ajax.success(url, result);
                }).fail(function(response, textStatus, jqXHR) {
                    ajax.failure(url, textStatus);
                    global.loaderHide('.loader');
                });
        }
    },
    success: function(url, result) {

        switch (url) {
            case "find_zip_by_cord":
                if (result['status'] == 'success') {
                    localCache.set('zipCodeEntered', result['message']);
                    $('#selectedZipCode').val(result['message']);
                    var u = $("#recently_url").val();
                    window.location.href = u;

                } else { 
                    var contents = "Location is disabled. Please enter Zip Code to search the latest inventory in your area.";
                    T3AlertMessage(title = 'Alert!', contents); 

                }

                break;

        }

    },
    failure: function(url, textStatus) {

    }
}

function T3AlertMessage(title, contents) {
    $.confirm({
        title: '<span style="font-size: 18px !important; font-family:Helvetica Neue,Helvetica,Arial,sans-serif;">' + title + '</span>',
        content: contents,
        boxWidth: '35%',
        backgroundDismiss: false,
        bgOpacity: .1,
        useBootstrap: false,
        draggable: false,
        buttons: {

            OK: {
                keys: ['y'],
                btnClass: 'alert-button',
                action: function() {
                    defaultRedirect();
                }
            },
            no: {
                isHidden: true, // hide the button
                keys: ['N'],
                action: function() {

                }
            },
        }
    });
}

</script>


<script>
var settimeinterval = setInterval("startTime();", 1000);


function defaultRedirect() {
    var u = $("#recently_url").val();
    window.location.href = u;
    return false;
}
</script>

@if(isset($params_zipcode))
@if($params_zipcode=='')
<script>
function startTime() {
    var t = $("#starttime").val();

    if (t > 0) {
        $("#time").html("Page will be redirect in <span style=\"color:red\">" + t + "</span> seconds...");
        $("#starttime").val(parseInt(t) - 1);
    } else {
        $('#Redirect_Modal').modal('hide');
        clearTimeout(settimeinterval);
        //pop();
        var u = $("#recently_url").val();
        window.location.href = u;
    }
}
</script>
@else
<script>
function startTime() {
    var zipcode = $("#url_zipcode").val();
    zipcode = AddZeroPrefixZipcode(zipcode);
    localCache.set('zipCodeEntered', zipcode);
    localCache.set('dealerZipCode', zipcode);
    var t = $("#starttime").val();
    if (t > 0) {
        $("#time").html("Page will be redirect in <span style=\"color:red\">" + t + "</span> seconds...");
        $("#starttime").val(parseInt(t) - 1);
    } else {
        $('#Redirect_Modal').modal('hide');
        clearTimeout(settimeinterval);					 
        var u = $("#recently_url").val();
        window.location.href = u;
    }

}
</script>
@endif
@endif
<input type="hidden" id="APP_URL" value="@php echo env('APP_URL') @endphp" />
<section id="about" class="site-padding">
    <input type="hidden" id="starttime" value="5" />
    <input type="hidden" id="url_zipcode" name="url_zipcode" value="@if(isset($params_zipcode)){{ $params_zipcode }}@endif" />
    <input type="hidden" id="recently_url" value="{{ $redirect }}" />
    <div id="detailPage" style="height: 75vh;">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong">
        </button>
        <button type="button" class="btn btn-info btn-lg" id="myBtn"></button>
        <!-- Modal -->
        <div class="modal fade" id="Redirect_Modal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">VIN not available</h4>
                    </div>
                    <div class="modal-body text-justify">
                        <p>This vehicle is currently not available. Please choose some other vehicle for this model.</p>
                        <p>&nbsp;</p>
                        <div id="time" style="font-weight: bold;">Loading...</div>
                    </div>
                </div>
            </div>
        </div>
        <script>
        $(document).ready(function() {
            $("#Redirect_Modal").modal();
        });

        </script>
    </div>
    </div>
    <!-- Button trigger modal -->
    <!-- Modal -->
</section>
@endsection

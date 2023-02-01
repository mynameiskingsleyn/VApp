<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Dealer Admin') }} @yield('pageTitle')</title>
    <link rel="icon" href="https://d1jougtdqdwy1v.cloudfront.net/dealeradmin/cdjr/favicon-cross.ico" type="image/x-icon">
    {{-- @if(env('APP_ENV') == 'local' || env('APP_ENV') == 'staging')
    <!-- MDB icon -->
    @else
    <link rel="icon" href="{{ secure_asset('img/favicon.ico') }}" type="image/x-icon">
    @endif --}}
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    @if(env('APP_ENV') == 'local' || env('APP_ENV') == 'staging')
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <!-- Material Design Bootstrap -->
    <link rel="stylesheet" href="{{ asset('css/mdb.min.css') }}">
    <!-- Your custom styles (optional) -->
    <link rel="stylesheet" href="{{ asset('css/addons/datatables.min.css') }}">
    <link href="{{ asset('css/multiselect.css') }}" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/default.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/generic.css') }}">
    @else
    <link href="{{ secure_asset('css/app.css') }}" rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="{{ secure_asset('css/bootstrap.min.css') }}">
    <!-- Material Design Bootstrap -->
    <link rel="stylesheet" href="{{ secure_asset('css/mdb.min.css') }}">
    <!-- Your custom styles (optional) -->
    <link rel="stylesheet" href="{{ secure_asset('css/addons/datatables.min.css') }}">
    <link href="{{ secure_asset('css/multiselect.css') }}" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="{{ secure_asset('css/default.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/generic.css') }}">
    @endif
</head>

<body class="dealeradmin-body drivefca_generic">
    <header>
        <div class="header-scroll">
            <div class="container">
                <div class="row no-gutters">
                    <div class="col-xs-12 col-sm-4 text-sm-left left-menu-field ">
                        <div class="brandLogos logoimagewrapper rBold">
                            <ul class="list-inline d-flex">
                                <li class="nav-item"></li>
                                @if(!\Session::has('DealerAdmin'))
                                <li class="nav-item rift-soft logout-link">
                                    <a class="faq logout-anchor faq-anchor" href="{{url('help_and_faq')}}">HELP & FAQ</a>
                                </li>
                                @endif
                                <li class="nav-item"></li>
                                @if(\Session::has('DealerAdmin'))
                                <li class="nav-item rift-soft logout-link">
                                    <a class="faq logout-anchor faq-anchor" href="{{url('help_and_faq')}}">HELP & FAQ</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-4 center-icon text-center logoimagewrapper">
                        <a tabindex="2" href="{{ env('APP_URL') }}inventory" class="imageFrame alfa-sni-frame top-logo-vehicle">
                            <img class="" alt="Drive FCA" title="Drive FCA" src="https://d1jougtdqdwy1v.cloudfront.net/dealeradmin/cdjr/logo-drivefca.png">
                        </a>
                    </div>
                    <div class="col-xs-12 col-sm-4 text-right text-sm-center right-menu-field dealer_rightmenu_address">
                        <div class=" navigation   rBold">
                            <ul class="list-inline">
                                <li class="rift-soft logout-link">
                                    @if(\Session::has('DealerAdmin'))
                                    <a href="javascript:void(0)" class="logout-anchor" id="logout-anchor-dc"> {{ __('Logout') }}</a>
                                    @endif
                                </li>
                                <li class="rift-soft logout-link">
                                    @if(Session::has('DealerAdmin'))
                                    <a class="dealer-name-anchor" href="javascript:void(0)">{{ isset($dealer_name) ? $dealer_name : Session::get('DealerName') }} - {{ \Session::get('DealerAdmin')['DealerCode']}}</a>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <input type="hidden" name="APP_URL" id="APP_URL" value="{{ env('APP_URL') }}">
    @guest
    <input type="hidden" name="DealerCode" id="dealer_code" value="{{ \Session::get('DealerCode')}}">
    @else
    <input type="hidden" name="DealerCode" id="dealer_code" value="{{ \Session::get('DealerAdmin')['DealerCode']}}">
    @endguest
    @if(isset($zipcode))
    <input type="hidden" name="DealerZipCode" id="dealer_zip_code" value="{{$zipcode}}">
    @endif
    @yield('content')
    @include('layouts.footer')
    @if(env('APP_ENV') == 'local' || env('APP_ENV') == 'staging')
    <!-- jQuery -->
    <script type="text/javascript" src="{{ asset('js/jquery.min.js')}}"></script>
    <!-- jQuery Mask -->
    <script type="text/javascript" src="https://d1jougtdqdwy1v.cloudfront.net/js/vendor/jquery.mask.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="{{ asset('js/mdb.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/addons/datatables.min.js') }}"></script>
    <!-- Your custom scripts (optional) -->
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.css')}}">
    <script src="{{ asset('js/jquery-ui.js')}}"></script>
    <script src="{{ asset('js/multiselect.js') }}" defer></script>
    <!-- dealer-discount container-->
    <script type="text/javascript" src="{{ asset('js/main.js')}}"></script>
    @if(isset($dealer_name))
    @if($tabname == 'dealerdiscounts')
    <!-- dealer-discount container-->
    <script type="text/javascript" src="{{ asset('js/filters.js')}}"></script>
    @else
    <!-- dealer-automated-discount container-->
    <script type="text/javascript" src="{{ asset('js/automated_filters.js')}}"></script>
    @endif
    @endif
    <!-- <script type="text/javascript" src="{{ asset('js/jquery.validate.min.js')}}"></script> -->
    @else
    <!-- jQuery -->
    <script type="text/javascript" src="{{ secure_asset('js/jquery.min.js')}}"></script>
    <!-- jQuery Mask -->
    <script type="text/javascript" src="https://d1jougtdqdwy1v.cloudfront.net/js/vendor/jquery.mask.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="{{ secure_asset('js/popper.min.js')}}"></script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="{{ secure_asset('js/bootstrap.min.js')}}"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="{{ secure_asset('js/mdb.min.js')}}"></script>
    <script type="text/javascript" src="{{ secure_asset('js/addons/datatables.min.js') }}"></script>
    <!-- Your custom scripts (optional) -->
    <link rel="stylesheet" href="{{ secure_asset('css/jquery-ui.css')}}">
    <script src="{{ secure_asset('js/jquery-ui.js')}}"></script>
    <script src="{{ secure_asset('js/multiselect.js') }}" defer></script>
    <!-- dealer-discount container-->
    <script type="text/javascript" src="{{ secure_asset('js/main.js')}}"></script>
    @if(isset($dealer_name))
    @if($tabname == 'dealerdiscounts')
    <!-- dealer-discount container-->
    <script type="text/javascript" src="{{ secure_asset('js/filters.js')}}"></script>
    @else
    <!-- dealer-automated-discount container-->
    <script type="text/javascript" src="{{ secure_asset('js/automated_filters.js')}}"></script>
    @endif
    @endif
    <!-- <script type="text/javascript" src="{{ secure_asset('js/jquery.validate.min.js')}}"></script> -->
    @endif
    <script type="text/javascript">
    (function($) {

        $.fn.VideoPopUp = function(options) {

            var defaults = {
                backgroundColor: "#000000",
                opener: "video",
                maxweight: "640",
                pausevideo: false,
                idvideo: ""
            };

            var patter = this.attr('id');

            var settings = $.extend({}, defaults, options);

            var video = document.getElementById(settings.idvideo);

            function stopVideo() {
                video.pause();
                video.currentTime = 0;
            }

            $('#' + patter + '').css("display", "none");
            $('#' + patter + '').append('<div class="opct"></div>');
            $('.opct').css("background", settings.backgroundColor);
            $('#' + patter + '').css("z-index", "100001");
            $('#' + patter + '').css("position", "fixed")
            $('#' + patter + '').css("top", "0");
            $('#' + patter + '').css("bottom", "0");
            $('#' + patter + '').css("right", "0");
            $('#' + patter + '').css("left", "0");
            $('#' + patter + '').css("padding", "auto");
            $('#' + patter + '').css("text-align", "center");
            $('#' + patter + '').css("background", "none");
            $('#' + patter + '').css("vertical-align", "vertical-align");
            $("#videCont").css("z-index", "100002");
            $('#' + patter + '').append('<div id="closer_videopopup" class="closer_videopopup_class">&otimes;</div>');

            $("#" + settings.opener + "").on('click', function() {
                $('#' + patter + "").show();
                $('#' + settings.idvideo + '').trigger('play');

            });
            $("#closer_videopopup, .closer_videopopup_class").on('click', function() {
                if (settings.pausevideo == true) {
                    $('#' + settings.idvideo + '').trigger('pause');
                } else {
                    stopVideo();
                }
                $('#' + patter + "").hide();
            });
            return this.css({

            });
        };

    }(jQuery));

    </script>
    <script type="text/javascript">
    $(document).ready(function() {
        $(function() {

            $('#vidBox_initial1').VideoPopUp({
                backgroundColor: "#17212a",
                opener: "video-trigger_initial1",
                idvideo: "howitswork_initial1",
                pausevideo: true
            });

            $('#vidBox_initial2').VideoPopUp({
                backgroundColor: "#17212a",
                opener: "video-trigger_initial2",
                idvideo: "howitswork_initial2",
                pausevideo: true
            });

            $('#vidBox_initial3').VideoPopUp({
                backgroundColor: "#17212a",
                opener: "video-trigger_initial3",
                idvideo: "howitswork_initial3",
                pausevideo: true
            });

            $('#vidBox_initial4').VideoPopUp({
                backgroundColor: "#17212a",
                opener: "video-trigger_initial4",
                idvideo: "howitswork_initial4",
                pausevideo: true
            });

            $('#vidBox_initial5').VideoPopUp({
                backgroundColor: "#17212a",
                opener: "video-trigger_initial5",
                idvideo: "howitswork_initial5",
                pausevideo: true
            });

            //vid=document.getElementById("howitswork");
            //vid.disablePictureInPicture = true;
            //vid_howitswork_initial=document.getElementById("howitswork_initial");
            //vid_howitswork_initial.disablePictureInPicture = true;

        });

    });

    </script>
</body>

</html>

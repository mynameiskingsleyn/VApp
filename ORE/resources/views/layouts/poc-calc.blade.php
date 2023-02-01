<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="icon" href="" />
    <meta name="keywords" content="alfa romeo, alfa romeo usa, official alfa romeo website, luxury cars, sports cars, car racing cars, alfa romeo cars, alfa romeo suv, luxury suv" />
    <meta name="description" content="The Mechanics of Emotions. Alfa Romeo vehicles are crafted for performance. Explore Alfa Romeo sports cars &amp; SUVs, current offers, dealerships and more." />
    <meta name="pageName" content="vehicle-information" />
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible" />
    <meta property="og:title" content="Drive Alfa Romeo  - @yield('title')" />
    <meta property="og:description" content="The Mechanics of Emotions. Alfa Romeo vehicles are crafted for performance. Explore Alfa Romeo sports cars &amp; SUVs, current offers, dealerships and more." />
    <meta property="og:url" content="@php echo env('APP_URL') @endphp" />
    <meta name="twitter:title" content="Drive Alfa Romeo  - @yield('title')">
    <meta name="twitter:description" content="The Mechanics of Emotions. Alfa Romeo vehicles are crafted for performance. Explore Alfa Romeo sports cars &amp; SUVs, current offers, dealerships and more.">
    <meta name="twitter:site" content="@fcaore">
    <link rel="icon" type="image/vnd.microsoft.icon" href="{{ cdn('images/favicon.ico') }}" />
    <link rel="shortcut icon" type="image/vnd.microsoft.icon" href="{{ cdn('images/favicon.ico') }}" />
    <!-- Place favicon.ico in the root directory -->
    <!-- Font -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,700,600italic,700italic,800,800italic' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <!-- Font -->
    <link rel="stylesheet" href="{{ cdn('css/normalize.css') }}">
    <link rel="stylesheet" href="{{ cdn('css/main.css') }}">
    @if(env('APP_ENV') == 'local')
    <link rel="stylesheet" href="{{ secure_asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    @else
    <link id="styleLoader" rel="stylesheet" href="{{ secure_asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/bootstrap.min.css') }}">
    @endif
    <link rel="stylesheet" href="{{ cdn('css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ cdn('css/icomoon.css') }}">
    <link rel="stylesheet" href="  {{ cdn('css/jquery-confirm.min.css') }}">
    <link rel="stylesheet" href="{{ cdn('css/responsive.css') }}">
    @if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
    <link id="styleLoader" rel="stylesheet" href="{{ asset('css/alfa_romeo.css') }}">
    <link rel="stylesheet" rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
      body {
        padding: 30px;
      }
      .can-toggle {
        position: relative;
      }
      .can-toggle *, .can-toggle *:before, .can-toggle *:after {
        box-sizing: border-box;
      }
      .can-toggle input[type="checkbox"] {
        opacity: 0;
        position: absolute;
        top: 0;
        left: 0;
      }
      .can-toggle input[type="checkbox"][disabled] ~ label {
        pointer-events: none;
      }
      .can-toggle input[type="checkbox"][disabled] ~ label .can-toggle__switch {
        opacity: 0.4;
      }
      .can-toggle input[type="checkbox"]:checked ~ label .can-toggle__switch:before {
        content: attr(data-unchecked);
        left: 0;
      }
      .can-toggle input[type="checkbox"]:checked ~ label .can-toggle__switch:after {
        content: attr(data-checked);
      }
      .can-toggle label {
        user-select: none;
        position: relative;
        display: flex;
        align-items: center;
      }
      .can-toggle label .can-toggle__label-text {
        flex: 1;
        padding-left: 15px;
      }
      .can-toggle label .can-toggle__switch {
        position: relative;
      }
      .can-toggle label .can-toggle__switch:before {
        content: attr(data-checked);
        position: absolute;
        top: 0;
        text-transform: uppercase;
        text-align: center;
      }
      .can-toggle label .can-toggle__switch:after {
        content: attr(data-unchecked);
        position: absolute;
        z-index: 5;
        text-transform: uppercase;
        text-align: center;
        background: white;
        transform: translate3d(0, 0, 0);
      }
      .can-toggle input[type="checkbox"][disabled] ~ label {
        color: rgba(119, 119, 119, 0.5);
      }
      .can-toggle input[type="checkbox"]:focus ~ label .can-toggle__switch, .can-toggle input[type="checkbox"]:hover ~ label .can-toggle__switch {
        background-color: #8f0c2c;
      }
      .can-toggle input[type="checkbox"]:focus ~ label .can-toggle__switch:after, .can-toggle input[type="checkbox"]:hover ~ label .can-toggle__switch:after {
        color: #8f0c2c;
      }
      .can-toggle input[type="checkbox"]:hover ~ label {
        color: #8f0c2c;
      }
      .can-toggle input[type="checkbox"]:checked ~ label:hover {
        color: #8f0c2c;
      }
      .can-toggle input[type="checkbox"]:checked ~ label .can-toggle__switch {
        background-color: #8f0c2c;
      }
      .can-toggle input[type="checkbox"]:checked ~ label .can-toggle__switch:after {
        color: #8f0c2c;
      }
      .can-toggle input[type="checkbox"]:checked:focus ~ label .can-toggle__switch, .can-toggle input[type="checkbox"]:checked:hover ~ label .can-toggle__switch {
        background-color: #8f0c2c;
      }
      .can-toggle input[type="checkbox"]:checked:focus ~ label .can-toggle__switch:after, .can-toggle input[type="checkbox"]:checked:hover ~ label .can-toggle__switch:after {
        color: #8f0c2c;
      }
      .can-toggle label .can-toggle__label-text {
        flex: 1;
      }
      .can-toggle label .can-toggle__switch {
        transition: background-color 0.3s cubic-bezier(0, 1, 0.5, 1);
        background: #848484;
      }
      .can-toggle label .can-toggle__switch:before {
        color: rgba(255, 255, 255, 0.5);
      }
      .can-toggle label .can-toggle__switch:after {
        -webkit-transition: -webkit-transform 0.3s cubic-bezier(0, 1, 0.5, 1);
        transition: transform 0.3s cubic-bezier(0, 1, 0.5, 1);
        color: #777;
      }
      .can-toggle input[type="checkbox"]:focus ~ label .can-toggle__switch:after, .can-toggle input[type="checkbox"]:hover ~ label .can-toggle__switch:after {
        box-shadow: 0 3px 3px rgba(0, 0, 0, 0.4);
      }
      .can-toggle input[type="checkbox"]:checked ~ label .can-toggle__switch:after {
        transform: translate3d(65px, 0, 0);
      }
      .can-toggle input[type="checkbox"]:checked:focus ~ label .can-toggle__switch:after, .can-toggle input[type="checkbox"]:checked:hover ~ label .can-toggle__switch:after {
        box-shadow: 0 3px 3px rgba(0, 0, 0, 0.4);
      }
      .can-toggle label {
        font-size: 14px;
      }
      .can-toggle label .can-toggle__switch {
        height: 36px;
        flex: 0 0 134px;
        border-radius: 4px;
      }
      .can-toggle label .can-toggle__switch:before {
        left: 67px;
        font-size: 12px;
        line-height: 36px;
        width: 67px;
        padding: 0 12px;
      }
      .can-toggle label .can-toggle__switch:after {
        top: 2px;
        left: 2px;
        border-radius: 2px;
        width: 65px;
        line-height: 32px;
        font-size: 12px;
      }
      .can-toggle label .can-toggle__switch:hover:after {
        box-shadow: 0 3px 3px rgba(0, 0, 0, 0.4);
      }
      .can-toggle.can-toggle--size-small input[type="checkbox"]:focus ~ label .can-toggle__switch:after, .can-toggle.can-toggle--size-small input[type="checkbox"]:hover ~ label .can-toggle__switch:after {
        box-shadow: 0 2px 2px rgba(0, 0, 0, 0.4);
      }
      .can-toggle.can-toggle--size-small input[type="checkbox"]:checked ~ label .can-toggle__switch:after {
        transform: translate3d(44px, 0, 0);
      }
      .can-toggle.can-toggle--size-small input[type="checkbox"]:checked:focus ~ label .can-toggle__switch:after, .can-toggle.can-toggle--size-small input[type="checkbox"]:checked:hover ~ label .can-toggle__switch:after {
        box-shadow: 0 2px 2px rgba(0, 0, 0, 0.4);
      }
      .can-toggle.can-toggle--size-small label {
        font-size: 13px;
      }
      .can-toggle.can-toggle--size-small label .can-toggle__switch {
        height: 28px;
        flex: 0 0 90px;
        border-radius: 2px;
      }
      .can-toggle.can-toggle--size-small label .can-toggle__switch:before {
        left: 45px;
        font-size: 10px;
        line-height: 28px;
        width: 45px;
        padding: 0 12px;
      }
      .can-toggle.can-toggle--size-small label .can-toggle__switch:after {
        top: 1px;
        left: 1px;
        border-radius: 1px;
        width: 44px;
        line-height: 26px;
        font-size: 10px;
      }
      .can-toggle.can-toggle--size-small label .can-toggle__switch:hover:after {
        box-shadow: 0 2px 2px rgba(0, 0, 0, 0.4);
      }
      .can-toggle.can-toggle--size-large input[type="checkbox"]:focus ~ label .can-toggle__switch:after, .can-toggle.can-toggle--size-large input[type="checkbox"]:hover ~ label .can-toggle__switch:after {
        box-shadow: 0 4px 4px rgba(0, 0, 0, 0.4);
      }
      .can-toggle.can-toggle--size-large input[type="checkbox"]:checked ~ label .can-toggle__switch:after {
        transform: translate3d(78px, 0, 0);
      }
      .can-toggle.can-toggle--size-large input[type="checkbox"]:checked:focus ~ label .can-toggle__switch:after, .can-toggle.can-toggle--size-large input[type="checkbox"]:checked:hover ~ label .can-toggle__switch:after {
        box-shadow: 0 4px 4px rgba(0, 0, 0, 0.4);
      }
      .can-toggle.can-toggle--size-large label {
        font-size: 14px;
      }
      .can-toggle.can-toggle--size-large label .can-toggle__switch {
        height: 50px;
        flex: 0 0 160px;
        border-radius: 4px;
      }
      .can-toggle.can-toggle--size-large label .can-toggle__switch:before {
        left: 80px;
        font-size: 14px;
        line-height: 50px;
        width: 80px;
        padding: 0 12px;
      }
      .can-toggle.can-toggle--size-large label .can-toggle__switch:after {
        top: 2px;
        left: 2px;
        border-radius: 2px;
        width: 78px;
        line-height: 46px;
        font-size: 14px;
      }
      .can-toggle.can-toggle--size-large label .can-toggle__switch:hover:after {
        box-shadow: 0 4px 4px rgba(0, 0, 0, 0.4);
      }
      .can-toggle.demo-rebrand-1 input[type="checkbox"][disabled] ~ label {
        color: rgba(181, 62, 116, 0.5);
      }
      .can-toggle.demo-rebrand-1 input[type="checkbox"]:focus ~ label .can-toggle__switch, .can-toggle.demo-rebrand-1 input[type="checkbox"]:hover ~ label .can-toggle__switch {
        background-color: #b53e74;
      }
      .can-toggle.demo-rebrand-1 input[type="checkbox"]:focus ~ label .can-toggle__switch:after, .can-toggle.demo-rebrand-1 input[type="checkbox"]:hover ~ label .can-toggle__switch:after {
        color: #8f315c;
      }
      .can-toggle.demo-rebrand-1 input[type="checkbox"]:hover ~ label {
        color: #a23768;
      }
      .can-toggle.demo-rebrand-1 input[type="checkbox"]:checked ~ label:hover {
        color: #39916a;
      }
      .can-toggle.demo-rebrand-1 input[type="checkbox"]:checked ~ label .can-toggle__switch {
        background-color: #44ae7f;
      }
      .can-toggle.demo-rebrand-1 input[type="checkbox"]:checked ~ label .can-toggle__switch:after {
        color: #368a65;
      }
      .can-toggle.demo-rebrand-1 input[type="checkbox"]:checked:focus ~ label .can-toggle__switch, .can-toggle.demo-rebrand-1 input[type="checkbox"]:checked:hover ~ label .can-toggle__switch {
        background-color: #3d9c72;
      }
      .can-toggle.demo-rebrand-1 input[type="checkbox"]:checked:focus ~ label .can-toggle__switch:after, .can-toggle.demo-rebrand-1 input[type="checkbox"]:checked:hover ~ label .can-toggle__switch:after {
        color: #2f7757;
      }
      .can-toggle.demo-rebrand-1 label .can-toggle__label-text {
        flex: 1;
      }
      .can-toggle.demo-rebrand-1 label .can-toggle__switch {
        transition: background-color 0.3s ease-in-out;
        background: #c14b81;
      }
      .can-toggle.demo-rebrand-1 label .can-toggle__switch:before {
        color: rgba(255, 255, 255, 0.6);
      }
      .can-toggle.demo-rebrand-1 label .can-toggle__switch:after {
        -webkit-transition: -webkit-transform 0.3s ease-in-out;
        transition: transform 0.3s ease-in-out;
        color: #b53e74;
      }
      .can-toggle.demo-rebrand-2 input[type="checkbox"][disabled] ~ label {
        color: rgba(68, 68, 68, 0.5);
      }
      .can-toggle.demo-rebrand-2 input[type="checkbox"]:focus ~ label .can-toggle__switch, .can-toggle.demo-rebrand-2 input[type="checkbox"]:hover ~ label .can-toggle__switch {
        background-color: #444;
      }
      .can-toggle.demo-rebrand-2 input[type="checkbox"]:focus ~ label .can-toggle__switch:after, .can-toggle.demo-rebrand-2 input[type="checkbox"]:hover ~ label .can-toggle__switch:after {
        color: #2b2b2b;
      }
      .can-toggle.demo-rebrand-2 input[type="checkbox"]:hover ~ label {
        color: #373737;
      }
      .can-toggle.demo-rebrand-2 input[type="checkbox"]:checked ~ label:hover {
        color: #62b125;
      }
      .can-toggle.demo-rebrand-2 input[type="checkbox"]:checked ~ label .can-toggle__switch {
        background-color: #75d32d;
      }
      .can-toggle.demo-rebrand-2 input[type="checkbox"]:checked ~ label .can-toggle__switch:after {
        color: #5da924;
      }
      .can-toggle.demo-rebrand-2 input[type="checkbox"]:checked:focus ~ label .can-toggle__switch, .can-toggle.demo-rebrand-2 input[type="checkbox"]:checked:hover ~ label .can-toggle__switch {
        background-color: #69be28;
      }
      .can-toggle.demo-rebrand-2 input[type="checkbox"]:checked:focus ~ label .can-toggle__switch:after, .can-toggle.demo-rebrand-2 input[type="checkbox"]:checked:hover ~ label .can-toggle__switch:after {
        color: #52941f;
      }
      .can-toggle.demo-rebrand-2 label .can-toggle__label-text {
        flex: 1;
      }
      .can-toggle.demo-rebrand-2 label .can-toggle__switch {
        transition: background-color 0.3s cubic-bezier(0.86, 0, 0.07, 1);
        background: #515151;
      }
      .can-toggle.demo-rebrand-2 label .can-toggle__switch:before {
        color: rgba(255, 255, 255, 0.7);
      }
      .can-toggle.demo-rebrand-2 label .can-toggle__switch:after {
        -webkit-transition: -webkit-transform 0.3s cubic-bezier(0.86, 0, 0.07, 1);
        transition: transform 0.3s cubic-bezier(0.86, 0, 0.07, 1);
        color: #444;
      }
      .can-toggle.demo-rebrand-2 input[type="checkbox"]:focus ~ label .can-toggle__switch:after, .can-toggle.demo-rebrand-2 input[type="checkbox"]:hover ~ label .can-toggle__switch:after {
        box-shadow: 0 4px 4px rgba(0, 0, 0, 0.4);
      }
      .can-toggle.demo-rebrand-2 input[type="checkbox"]:checked ~ label .can-toggle__switch:after {
        transform: translate3d(58px, 0, 0);
      }
      .can-toggle.demo-rebrand-2 input[type="checkbox"]:checked:focus ~ label .can-toggle__switch:after, .can-toggle.demo-rebrand-2 input[type="checkbox"]:checked:hover ~ label .can-toggle__switch:after {
        box-shadow: 0 4px 4px rgba(0, 0, 0, 0.4);
      }
      .can-toggle.demo-rebrand-2 label {
        font-size: 13px;
      }
      .can-toggle.demo-rebrand-2 label .can-toggle__switch {
        height: 60px;
        flex: 0 0 120px;
        border-radius: 60px;
      }
      .can-toggle.demo-rebrand-2 label .can-toggle__switch:before {
        left: 60px;
        font-size: 13px;
        line-height: 60px;
        width: 60px;
        padding: 0 12px;
      }
      .can-toggle.demo-rebrand-2 label .can-toggle__switch:after {
        top: 2px;
        left: 2px;
        border-radius: 30px;
        width: 58px;
        line-height: 56px;
        font-size: 13px;
      }
      .can-toggle.demo-rebrand-2 label .can-toggle__switch:hover:after {
        box-shadow: 0 4px 4px rgba(0, 0, 0, 0.4);
      }
      .modal {
          position: absolute;  
        overflow: auto;
      }
      button#contpop { 
          background-color: #8f0c2c;
          border-radius: .07143rem;
          color: #fff;
          cursor: pointer;
          display: inline-block;
          font-size: 1.35714rem;
          line-height: 1;
          padding: 1.14286rem 3.14286rem 1.14286rem 1.78571rem;
          position: relative;
          text-decoration: none;
          text-transform: uppercase;
          -webkit-transition: background-color 250ms ease-in-out;
          transition: background-color 250ms ease-in-out;
      }

      }
    </style>
    @else
    <link id="styleLoader" rel="stylesheet" href="{{ secure_asset('css/alfa_romeo.css') }}">
    <link rel="stylesheet" rel="stylesheet" href="{{ secure_asset('css/custom.css') }}">
    @endif
    <style>
        .col-sm-2.center-icon.text-center.logowrapper {
            z-index: 9999;
            left: 0!important;
            right: 100%!important;
        }
        @media (min-width: 386px) and (max-width: 574px) {
            .col-sm-2.center-icon.text-center.logowrapper {
            left: 0!important;
            right: 100%!important;
            z-index: 9999;
            }
        
        }
        @media (max-width: 385px){
            .col-sm-2.center-icon.text-center.logowrapper {
            z-index: 9999;
            padding-top: 2px;
            left: 0!important;
            }
        
        }
    </style>
    <script type="text/javascript" src="{{ cdn('js/vendor/jquery-3.3.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ cdn('js/vendor/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ cdn('js/vendor/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ cdn('js/vendor/modernizr-2.8.3.min.js') }}"></script>
    <script type="text/javascript" src="{{ cdn('js/vendor/jquery.mask.min.js') }}"></script>
    <script type="text/javascript" src="{{ cdn('js/vendor/js.cookie.min.js') }}"></script>
    <script type="text/javascript" src="{{ cdn('js/vendor/videopopup.js') }}"> </script>
    <script src="{{ cdn('js/vendor/jquery-confirm.min.js') }}"></script>
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
</head>

<body>
    <header>
        <div class="header-scroll">
            <div class="container">
                <div class="row no-gutters">
                    <div class="col-xs-12 col-sm-5 text-sm-left left-menu-field ">
                        <div class="brandLogos logoimagewrapper">
                        </div>
                        <div class="navigation how-itworks-nav">
                        </div>
                    </div>
                    <div class="col-sm-2 center-icon text-center logoimagewrapper">
                        <a tabindex="2" href="{{  env('APP_URL') }}" class="imageFrame alfa-sni-frame top-logo-vehicle">
                            <img class="" alt="Alfa Romeo" title="Alfa Romeo" src="{{ cdn('images/logos/3x/logo-alfa-romeo.png') }}" />
                        </a>
                    </div>
                    <div class="col-xs-12 col-sm-5 text-right text-sm-center right-menu-field dealer_rightmenu_address">
                        <div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    @yield('content')
    @include('partials.footer')
    @if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
    <script type="text/javascript" src="{{ asset('js/GlobalScript.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/VehicleScript.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/merkle.js') }}"></script>
    <script>
    $('#contpop').on('click', function() {
        //  $('#exampleModalCenter').modal('hide');
        $('#leaseCalc').modal('show')
    });

    //  $('#exampleModalCenter').modal('hide');
    //$('#exampleModalCenter').on('hidden.bs.modal', function () {
    // Load up a new modal...

    //  });

    </script>
    @else
    <script type="text/javascript" src="{{ secure_asset('js/GlobalScript.js') }}"></script>
    <script type="text/javascript" src="{{ secure_asset('js/VehicleScript.js') }}"></script>
    <script type="text/javascript" src="{{ secure_asset('js/merkle.js') }}"></script>
    @endif
</body>

</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Dealer - Admin') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

</head>
<body class="dealeradmin-body">
<header>
<div class="header-scroll">
  <div class="container">
    <div class="row no-gutters">
        <div class="col-xs-12 col-sm-5 text-sm-left left-menu-field ">
          <div class="brandLogos logoimagewrapper">
          <ul class="list-inline d-flex"> 
            <li>
           
                         
            </li>     
          </ul> 
          </div>
        </div>
        <div class="col-sm-2 center-icon text-center logoimagewrapper"> 
          <a tabindex="2" href="{{ env('APP_URL') }}" class="imageFrame alfa-sni-frame top-logo-vehicle">
            <img class="" alt="Alfa Romeo" title="Alfa Romeo" src="https://d1jougtdqdwy1v.cloudfront.net/images/logos/3x/logo-alfa-romeo.png"> 
          </a>  
        </div>
       
        <div class="col-xs-12 col-sm-5 text-right text-sm-center right-menu-field dealer_rightmenu_address">
          <div class=" navigation   rBold">
            <ul class="list-inline">                               
            <li>
            <!-- <a href="{{ route('logout') }}" style="padding: 20px;" onclick="event.preventDefault();document.getElementById('logout-form').submit();">  {{ __('Logout') }}</a> -->
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
          </li>
            </ul> 
          </div>
        </div>
    </div>
  </div>
  </div>      
  </header>

    <div class="container app-container" id="app">
        <input type="hidden" name="APP_URL" id="APP_URL" value="{{ env('APP_URL') }}" >

        <!--<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    </!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links --/>
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                           </!--  @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif --/>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>-->

        <main class="">
            @yield('content')
        </main>
    </div>
</body>
</html>

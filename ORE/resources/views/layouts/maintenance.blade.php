<!doctype html>
<html class="no-js" lang="">
    <head>
	
	<base href="{{ Request::fullUrl() }}" />
	<meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">    
		<meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Search New Alfa Romeo Vehicle Inventory | @yield('title')</title>
        <link rel="icon" href=""  />
		
	<meta name="keywords" content="2020 jeep gladiator, jeep gladiator launch edition, buy jeep gladiator, buy jeep gladiator online, buy jeep truck, buy jeep truck online, jeep gladiator pre-sale, jeep gladiator pre-order, jeep truck pre-sale, jeep truck pre-order, jeep gladiator reservation" /> 
    <meta name="description" content="For the first time ever, reserve your 2020 Jeep® Gladiator Launch Edition online. Enjoy the convenient concierge pre-order experience from the comfort of your own home. Reserve your very own piece of history today!"/> 
    <meta name="pageName" content="home"/>  
	 <link rel="canonical" href="{{ Request::fullUrl() }}"/>
	 
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/> 
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible"/>
  
   <meta property="og:title" content="Drive Alfa Romeo  - @yield('title')" />
  <meta property="og:description" content="For the first time ever, reserve your 2020 Jeep® Gladiator Launch Edition online. Enjoy the convenient concierge pre-order experience from the comfort of your own home. Reserve your very own piece of history today! " />
  <meta property="og:url" content="{{ Request::fullUrl() }}" />
   
  <meta name="twitter:title" content="Drive Alfa Romeo  - @yield('title')">
  <meta name="twitter:description" content="For the first time ever, reserve your 2020 Jeep® Gladiator Launch Edition online. Enjoy the convenient concierge pre-order experience from the comfort of your own home. Reserve your very own piece of history today!">
  <meta name="twitter:site" content="@fcaore"> 
 	<!-- Font -->
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,700,600italic,700italic,800,800italic' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
        <!-- Font -->
		
        
        <link rel="stylesheet" href="{{ asset('css/normalize.css') }}">
        <link rel="stylesheet" href="{{ asset('css/main.css') }}">
        <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}"> 
        <!-- Latest compiled and minified CSS --> 

		 <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/icomoon.css') }}">
		<link rel="stylesheet" href="{{ asset('css/default.css') }}">
		<link id="styleLoader" rel="stylesheet" href="{{ asset('css/alfa_romeo.css') }}"> 
			{{-- <link id="styleLoader" rel="stylesheet" href="{{ asset('css/default.css') }}"> --}}

        <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">  
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">  
		
		<script type="text/javascript" src="{{ asset('js/vendor/jquery-3.3.1.min.js') }}"></script> 
		<script type="text/javascript" src="{{ asset('js/vendor/bootstrap.min.js') }}"></script> 
		<script type="text/javascript" src="{{ asset('js/vendor/jquery-ui.min.js') }}"></script>  
		<script type="text/javascript" src="{{ asset('js/vendor/modernizr-2.8.3.min.js') }}"></script>	  
	 
 
    <style>
        body{
        background:#000;
        }
        .modal-backdrop {
        background-color: transparent;
        }
    </style>
 </head>
  
    <body>
	
    <input type="hidden" id="APP_URL" value="@php echo env('APP_URL') @endphp" /> 
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
		@include('partials.header',['page' => 'landing']) 
            @yield('content')
        @include('partials.footer')    
		 

 
    </body>
</html>
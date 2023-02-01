<!doctype html>
<html class="no-js" lang="">
    <head> 
	<base href="@php echo env('APP_URL') @endphp" />
	<meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">    
		<meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Drive Forward | Online Vehicle Shopping | FCA</title>
        <link rel="icon" href=""  />
		
	<meta name="keywords" content="" /> 
    <meta name="description" content="Fiat Chrysler Automobiles is giving you one less thing to worry about for your transportation needs with easy online shopping, financing, and home delivery"/> 
    <meta name="pageName" content="home"/>  
	 <link rel="canonical" href="@php echo env('APP_URL') @endphp"/>
	 
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/> 
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible"/>
  
   <meta property="og:title" content="DriveFCA  - @yield('title')" />
  <meta property="og:description" content="The Mechanics of Emotions." />
  <meta property="og:url" content="@php echo env('APP_URL') @endphp" />
   
  <meta name="twitter:title" content="DriveFCA  - @yield('title')">
  <meta name="twitter:description" content="">
  <meta name="twitter:site" content="@fcaore">

  @if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training') 
		  <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TPX3R8G');</script>
<!-- End Google Tag Manager -->
	@else
	  <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TPX3R8G');</script>
<!-- End Google Tag Manager -->
@endif

@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
			<link rel="icon" type="image/vnd.microsoft.icon" href="{{ asset('images/favicon/favicon-cross.ico') }}"/>
<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="{{ asset('images/favicon/favicon-cross.ico') }}"/>
		@else
			<link rel="icon" type="image/vnd.microsoft.icon" href="{{ secure_asset('images/favicon/favicon-cross.ico') }}"/>
<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="{{ secure_asset('images/favicon/favicon-cross.ico') }}"/>
		@endif 
  
 	<!-- Font -->
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,700,600italic,700italic,800,800italic' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
        <!-- Font -->
		
		 
        
        <link rel="stylesheet" href="{{ cdn('css/normalize.css') }}">
        <link rel="stylesheet" href="{{ cdn('css/main.css') }}">
        
		@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
			<link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') }}"> 
		@else
			<link rel="stylesheet" type="text/css" href="{{ secure_asset('css/font-awesome.min.css') }}">
		@endif
        

		 <link rel="stylesheet" type="text/css" href="{{ cdn('css/bootstrap.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ cdn('css/jquery-ui.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ cdn('css/icomoon.css') }}">
		
		
		@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
			<link rel="stylesheet" type="text/css" href="{{ asset('css/alfa_romeo.css') }}"> 
			<link rel="stylesheet" type="text/css" href="{{ asset('css/default.css') }}">
		@else
			<link rel="stylesheet" type="text/css" href="{{ secure_asset('css/alfa_romeo.css') }}">
			<link rel="stylesheet" type="text/css" href="{{ secure_asset('css/default.css') }}">
		@endif 

        <link rel="stylesheet" type="text/css" href="{{ cdn('css/responsive.css') }}"> 
		 <link rel="stylesheet" type="text/css" href="{{ cdn('css/toggle-switch.css') }}">  
		

		@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
			<link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}"> 
		@else
			<link rel="stylesheet" type="text/css" href="{{ secure_asset('css/custom.css') }}" />
		@endif  
		
		<script type="text/javascript" src="{{ cdn('js/vendor/jquery-3.3.1.min.js') }}"></script> 
		<script type="text/javascript" src="{{ cdn('js/vendor/bootstrap.min.js') }}"></script> 
		<script type="text/javascript" src="{{ cdn('js/vendor/jquery-ui.min.js') }}"></script>  
		<script type="text/javascript" src="{{ cdn('js/vendor/modernizr-2.8.3.min.js') }}"></script>	 
		<script type="text/javascript" src="{{ cdn('js/vendor/videopopup.js') }}"> </script> 
			
		@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')  
			 <script src="//assets.adobedtm.com/e2b27ccc0e522eb7c0afb12eb12ee852c39ccceb/satelliteLib-5dd94bf97d100eedc2ee143eff9743a1eb65a09c-staging.js"></script>
	 
		@else
			
			<script src="https://assets.adobedtm.com/e2b27ccc0e522eb7c0afb12eb12ee852c39ccceb/satelliteLib-5dd94bf97d100eedc2ee143eff9743a1eb65a09c.js"></script>	
		 <script type="text/javascript" src="https://script.crazyegg.com/pages/scripts/0064/5474.js" async="async"></script>
		@endif
		
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>	 
 </head>
  
    <body>
	@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training') 
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TPX3R8G"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
	@else
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TPX3R8G"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@endif

    <input type="hidden" id="APP_URL" value="@php echo env('APP_URL') @endphp" /> 
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
		@include('partials.header',['page' => 'landing']) 
            @yield('content')
      
		 
		@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training') 
			<script type="text/javascript" src="{{ asset('js/LandingScript.js') }}"></script>  
		    <script type="text/javascript" src="{{ asset('js/merkle.js') }}"></script>
		@else
			<script type="text/javascript" src="{{ secure_asset('js/LandingScript.js') }}"></script>  
			<script type="text/javascript" src="{{ secure_asset('js/merkle.js') }}"></script> 
		@endif
		
 
 <script type="text/javascript">_satellite.pageBottom();</script>
  
 
 <script type="text/javascript"> 
				 try {
					  $(function() { 
								$('#vidBox').VideoPopUp({
									backgroundColor: "#17212a",
									opener: "video-trigger",
									idvideo: "howitswork",
									pausevideo: true
								});
							});
				 } catch(err) { console.log('Video Script Issues');  console.log(err.message); }	 
		</script>
		
@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')  
		@else
			 <script>/*Copyright 2011-2015 iPerceptions, Inc. All rights reserved. Do not distribute.iPerceptions provides this code 'as is' without warranty of any kind, either express or implied. */ window.iperceptionskey = '938623ea-35ab-48f8-a032-f671efb9563f';(function () { var a = document.createElement('script'),b = document.getElementsByTagName('body')[0]; a.type = 'text/javascript'; a.async = true;a.src = '//universal.iperceptions.com/wrapper.js';b.appendChild(a);})();</script>		
		@endif	
</html>
<!doctype html>
<html class="no-js" lang="">
    <head>
       
	<meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">    
		<meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        <link rel="icon" href=""  />
		
	<meta name="keywords" content="alfa romeo, alfa romeo usa, official alfa romeo website, luxury cars, sports cars, car racing cars, alfa romeo cars, alfa romeo suv, luxury suv" /> 
    <meta name="description" content="The Mechanics of Emotions. Alfa Romeo vehicles are crafted for performance. Explore Alfa Romeo sports cars &amp; SUVs, current offers, dealerships and more."/>  
    <meta name="pageName" content="search-new-inventory"/>   
	 
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/> 
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible"/>
   
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
			<link rel="icon" type="image/vnd.microsoft.icon" href="{{ asset('images/favicon/favicon-'.$params_make.'.ico') }}"/>
<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="{{ asset('images/favicon/favicon-'.$params_make.'.ico') }}"/>
		@else
			<link rel="icon" type="image/vnd.microsoft.icon" href="{{ secure_asset('images/favicon/favicon-'.$params_make.'.ico') }}"/>
<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="{{ secure_asset('images/favicon/favicon-'.$params_make.'.ico') }}"/>
		@endif 
 
        <!-- Place favicon.ico in the root directory -->
		
		<!-- Font -->
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,700,600italic,700italic,800,800italic' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
        <!-- Font -->        
		
		<link rel="stylesheet" href="{{ cdn('css/normalize.css') }}">
        <link rel="stylesheet" href="{{ cdn('css/main.css') }}">
		@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
			<link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">  
		@else
			<link rel="stylesheet" href="{{ secure_asset('css/font-awesome.min.css') }}">   	
		@endif
		
		@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
			<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
		@else
			<link rel="stylesheet" href="{{ secure_asset('css/bootstrap.min.css') }}">
		@endif
        
		
        <link rel="stylesheet" href="{{ cdn('css/jquery-ui.min.css') }}">
        <link rel="stylesheet" href="{{ cdn('css/icomoon.css') }}"> 
		
		
		@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
			<link id="styleLoader" rel="stylesheet" href="{{ asset('css/'.$params_make.'.css') }}">
		@else
			<link id="styleLoader" rel="stylesheet" href="{{ secure_asset('css/'.$params_make.'.css') }}">	
		@endif
        
		<link rel="stylesheet" href="{{ cdn('css/responsive.css') }}"> 
		
		@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
			<link rel="stylesheet" href="{{ asset('css/custom.css') }}">  
		@else
			<link rel="stylesheet" href="{{ secure_asset('css/custom.css') }}">  
		@endif
		
		<script type="text/javascript" src="{{ cdn('js/vendor/jquery-3.3.1.min.js') }}"></script> 
		<script type="text/javascript" src="{{ cdn('js/vendor/bootstrap.min.js') }}"></script> 
		<script type="text/javascript" src="{{ cdn('js/vendor/jquery-ui.min.js') }}"></script>  
		<script type="text/javascript" src="{{ cdn('js/vendor/modernizr-2.8.3.min.js') }}"></script>	
		<script type="text/javascript" src="{{ cdn('js/vendor/videopopup.js') }}"> </script> 
		<script type="text/javascript" src="{{ cdn('js/vendor/jquery.ui.touch-punch.min.js') }}"></script>	
		 
	@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')  
			 <script src="//assets.adobedtm.com/e2b27ccc0e522eb7c0afb12eb12ee852c39ccceb/satelliteLib-5dd94bf97d100eedc2ee143eff9743a1eb65a09c-staging.js"></script>
		
		@else
				@include('partials.google_tag_manager',['page' => 'landing', 'make' => $params_make])
			<script src="https://assets.adobedtm.com/e2b27ccc0e522eb7c0afb12eb12ee852c39ccceb/satelliteLib-5dd94bf97d100eedc2ee143eff9743a1eb65a09c.js"></script>	
		 <script type="text/javascript" src="https://script.crazyegg.com/pages/scripts/0064/5474.js" async="async"></script>
		@endif
			

	</head>
     <body class="make_{{ $params_make }}">
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
    <input type="hidden" id="current_page" value="landing" />
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
		@include('partials.header',['page' => 'sni', 'make' => $params_make])  
            @yield('content') 
		 @include('partials.footer',['make' => $params_make]) 
		
		
		 <div class="modal fade" id="zipCodePopUp" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<button type="button" class="rBlack size-12 close custom-close" data-dismiss="modal">Close <i class="fa fa-times" aria-hidden="true"></i></button>
			<div class="modal-body text-center reg size-14">
				<h2 class="rBold text-uppercase">Please Enter Zip Code </h2>
				<p class="marginY-4">Please enter a Valid Zip Code to customize the site based on your location.</p>

						<div class="zipCodeCont">
						<form onsubmit="return false;">
							@csrf
							<div class="input-group">
								<label style="display:none;" for="selectedZipCode">hide me</label><input type="text" maxlength="5" minlength="4" id="selectedZipCode" class="form-control" placeholder="Enter Zip Code" data-click="#mainSearch"  onkeypress="return isNumber(event);">
								
								<span class="input-group-btn">
								
								<button class="btn btn-primary sni2_zipcode" type="submit" value="button" role="button" id="find_location">
								
								<i class="fa fa-angle-right fa-lg" aria-hidden="true"></i><span style="display:none;">button</span></button></span>
							</div>
							<p class="zipcode_errorInfo" style="color:red;"></p>
							</form>
						</div>
			</div>
			
		  </div>
	</div>
</div>



<div id="notAvailable-popup" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close 50-states" data-dismiss="modal">Close <span>&times;</span></button>
      </div>
      <div class="modal-body">
        <h3 class="notA-title">Not available in all 50 states</h3>
        <p class="notA-desc">2020 Grand Caravan and 2020 Journey model year not available in California, Connecticut, Delaware, Maine, Maryland, Massachusetts, New Jersey, New York, Oregon, Pennsylvania, Rhode Island, Vermont and Washington.</p>
      </div>
    </div>
  </div>
</div>
		@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
			<script type="text/javascript" src="{{ asset('js/SNIScript.js') }}"></script>  
		    <script type="text/javascript" src="{{ asset('js/merkle.js') }}"></script>
		@else
			<script type="text/javascript" src="{{ secure_asset('js/SNIScript.js') }}"></script>  
			<script type="text/javascript" src="{{ secure_asset('js/merkle.js') }}"></script> 
			<script type="text/javascript" src="https://script.crazyegg.com/pages/scripts/0064/5474.js"></script>
		@endif
		

        @stack('scripts') 
		
		
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
  <script type="text/javascript">_satellite.pageBottom();</script>  
	@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')  
		@else
			 <script>/*Copyright 2011-2015 iPerceptions, Inc. All rights reserved. Do not distribute.iPerceptions provides this code 'as is' without warranty of any kind, either express or implied. */ window.iperceptionskey = '938623ea-35ab-48f8-a032-f671efb9563f';(function () { var a = document.createElement('script'),b = document.getElementsByTagName('body')[0]; a.type = 'text/javascript'; a.async = true;a.src = '//universal.iperceptions.com/wrapper.js';b.appendChild(a);})();</script>		
		@endif	 
    </body>
</html>
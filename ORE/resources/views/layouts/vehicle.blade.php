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
    <meta name="pageName" content="vehicle-information"/>   
	 
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/> 
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible"/>
  
    <meta property="og:title" content="Drive Alfa Romeo  - @yield('title')" />
  <meta property="og:description" content="The Mechanics of Emotions. Alfa Romeo vehicles are crafted for performance. Explore Alfa Romeo sports cars &amp; SUVs, current offers, dealerships and more." />
  <meta property="og:url" content="@php echo env('APP_URL') @endphp" />
   
  <meta name="twitter:title" content="Drive Alfa Romeo  - @yield('title')">
  <meta name="twitter:description" content="The Mechanics of Emotions. Alfa Romeo vehicles are crafted for performance. Explore Alfa Romeo sports cars &amp; SUVs, current offers, dealerships and more.">
  <meta name="twitter:site" content="@fcaore">
	 

@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
			<link rel="icon" type="image/vnd.microsoft.icon" href="{{ asset('images/favicon/favicon-'.$params_make.'.ico') }}"/>
<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="{{ asset('images/favicon/favicon-'.$params_make.'.ico') }}"/>
		@else
			<link rel="icon" type="image/vnd.microsoft.icon" href="{{ secure_asset('images/favicon/favicon-'.$params_make.'.ico') }}"/>
<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="{{ secure_asset('images/favicon/favicon-'.$params_make.'.ico') }}"/>
		@endif 
        <!-- Place favicon.ico in the root directory -->
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

		<!-- Font -->
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,700,600italic,700italic,800,800italic' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
        <!-- Font -->
		@php $c_cachebuster = date('Ymdh'); $cachebuster = md5($c_cachebuster); @endphp
          <link rel="stylesheet" href="{{ cdn('css/normalize.css') }}">
        <link rel="stylesheet" href="{{ cdn('css/main.css') }}">
			@if(env('APP_ENV') == 'local')  
			<link rel="stylesheet" href="{{ asset('css/font-awesome.css') }}"> 
				<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
		@else
		    <link rel="stylesheet" href="{{ secure_asset('css/font-awesome.min.css') }}"> 
			<link id="styleLoader" rel="stylesheet" href="{{ secure_asset('css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ secure_asset('css/bootstrap.min.css') }}">		
		@endif	
		
        
       
        <link rel="stylesheet" href="{{ cdn('css/jquery-ui.min.css') }}">
        <link rel="stylesheet" href="{{ cdn('css/icomoon.css') }}"> 
		<link rel="stylesheet" href="  {{ cdn('css/jquery-confirm.min.css') }}">  
		  <link rel="stylesheet" href="{{ cdn('css/responsive.css') }}"> 
	 
		
		@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')  
		<link id="styleLoader" rel="stylesheet" href="{{ asset('css/'.$params_make.'.css') }}">
		<link rel="stylesheet" rel="stylesheet" href="{{ asset('css/custom.css') }}?cb={{ $cachebuster }}"> 
		<link rel="stylesheet" rel="stylesheet" href="{{ asset('css/available_program.css') }}?cb={{ $cachebuster }}">  
		@else
			<link id="styleLoader" rel="stylesheet" href="{{ secure_asset('css/'.$params_make.'.css') }}">
		<link rel="stylesheet" rel="stylesheet" href="{{ secure_asset('css/custom.css') }}?cb={{ $cachebuster }}"> 
		<link rel="stylesheet" rel="stylesheet" href="{{ secure_asset('css/available_program.css') }}?cb={{ $cachebuster }}">
 <script type="text/javascript" src="https://script.crazyegg.com/pages/scripts/0064/5474.js" async="async"></script>		
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
			 <script src="{{ cdn('js/vendor/jquery-confirm.min.js') }}"></script>
	 
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
	<input type="hidden" id="session_timeout" value="{{ config('ore.session_timeout.timeout') }}" />
	<input type="hidden" id="session_mdg" value="{{ config('ore.session_timeout.message') }}" />
	<input type="hidden" id="session_hdr" value="{{ config('ore.session_timeout.message_header') }}" />
	<input type="hidden" id="popup_timeout" value="{{ config('ore.session_timeout.popup_timeout') }}" />
	<input type="hidden" id="window_close" value="open" />
	
	
    <input type="hidden" id="current_page" value="landing" />


        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
		@include('partials.header',['page' => 'vehicle', 'make' => $params_make])  
  
            @yield('content')
        @include('partials.footer',['make' => $params_make]) 

		@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
			<script type="text/javascript" src="{{ asset('js/GlobalScript.js') }}"></script>		
			<script type="text/javascript" src="{{ asset('js/VehicleScript.js') }}"></script>  
			<script type="text/javascript" src="{{ asset('js/merkle.js') }}"></script>		
			 
			 {{--	<script type="text/javascript" src="{{ asset('js/ore_vendor/carNow-staging.script.js') }}"></script>   --}}
			 <script type="text/javascript" src="{{ asset('js/PaymentCalculator.js') }}"></script>
		@else
			<script type="text/javascript" src="{{ secure_asset('js/GlobalScript.js') }}?cb={{ $cachebuster }}"></script>		
			<script type="text/javascript" src="{{ secure_asset('js/VehicleScript.js') }}"></script>  
			<script type="text/javascript" src="{{ secure_asset('js/merkle.js') }}"></script>	
 			
		 {{--	<script type="text/javascript" src="{{ secure_asset('js/ore_vendor/carNow.script.js') }}"></script> --}} 
		 <script type="text/javascript" src="{{ secure_asset('js/PaymentCalculator.js') }}?cb={{ $cachebuster }}"></script>
 
		@endif		
		


<script src="https://app.blackbookinformation.com/app/shopping-tools-js/v1.js"></script>
	
 		 
	 
 
        @stack('scripts')
	  
	<script type="text/javascript">_satellite.pageBottom();</script>  
	 
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
            $('#' + patter + '').append('<div id="opct"></div>');
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
				 try {
					  $(function() { 
								$('#vidBox').VideoPopUp({
									backgroundColor: "#17212a",
									opener: "video-trigger",
									idvideo: "howitswork",
									pausevideo: true
								});
							});
					  $(function() { 
								$('#vidBox_initial').VideoPopUp({
									backgroundColor: "#17212a",
									opener: "video-trigger_initial",
									idvideo: "howitswork_initial",
									pausevideo: true
								});
							});
				 } catch(err) { console.log('Video Script Issues');  console.log(err.message); }	 
		</script>
	  
	  
	  
	  <script>
		$(document).ready(function () {
			var idleState = false;
			var idleTimer = null;
			 
        
        $('*').bind('mousemove click mouseup mousedown keydown keypress keyup submit change mouseenter scroll resize dblclick', function () {
            clearTimeout(idleTimer);
            if (idleState == true) { 
                $("body").css('background-color','#fff');            
            }
            idleState = false;
			
            idleTimer = setTimeout(function () {  
			   var session_mdg = $('#session_mdg').val();
			   var session_hdr = $('#session_hdr').val();
			   var popup_timeout = $('#popup_timeout').val();
			   var APP_URL = $('#APP_URL').val();
			   var make = $('#make').val();
			   var tier_platform = $('#tier').val();
			   
               $.confirm({
                title: session_hdr,
                content: session_mdg,
                autoClose: 'sessionoutUser|'+popup_timeout,
                buttons: {
                    sessionoutUser: {
                        text: 'session expired',
                        action: function () { 
							$.get( APP_URL+"sessionout/mylead", function( data ) { 
									try {
										  var leadid = data['message'][0];
										if(leadid.length == 36)	{
										  mAnalystic.onFormComplete(returnedData['message'][0],'modal');
										} 
										//mAnalystic.onFormComplete(data['message'][0],'modal');
									}
									catch(err) {
									  console.log(err);
									}
								var redirect_URL = APP_URL;
								if((make != undefined && make != null && make != '') && (tier_platform != undefined && tier_platform != null && tier_platform != '')){
									if(tier_platform == 't1'){
										redirect_URL = redirect_URL + make +"/"+ tier_platform;
										window.location.replace(redirect_URL);
									}else if(tier_platform == 't3'){
										vehiclepage.CloseWebPage();
									}else{
										window.location.replace(APP_URL);									
									}
								}else{
									window.location.replace(APP_URL);
								}
							}); 
                        }
                    },
                    cancel: function () {   }
                }
            });
            }, 420000); //7 mins => (60 * 1000) * 7
        }); 
    });
 </script>
 @if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')  
		@else
			 <script>/*Copyright 2011-2015 iPerceptions, Inc. All rights reserved. Do not distribute.iPerceptions provides this code 'as is' without warranty of any kind, either express or implied. */ window.iperceptionskey = '938623ea-35ab-48f8-a032-f671efb9563f';(function () { var a = document.createElement('script'),b = document.getElementsByTagName('body')[0]; a.type = 'text/javascript'; a.async = true;a.src = '//universal.iperceptions.com/wrapper.js';b.appendChild(a);})();</script>		
		@endif	

    </body>
</html>
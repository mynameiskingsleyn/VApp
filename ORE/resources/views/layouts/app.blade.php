<!doctype html>
<html class="no-js" lang="">
    <head>
	
	 
	<meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">    
		<meta name="csrf-token" content="{{ csrf_token() }}">
        <title>ORE - @yield('title')</title>
       
   
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/> 
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible"/>
    
 	<!-- Font -->
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,700,600italic,700italic,800,800italic' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
        <!-- Font -->
		
      
        <!-- Latest compiled and minified CSS --> 

		 <link rel="stylesheet" href="{{ cdn('css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ cdn('css/jquery-ui.min.css') }}">
        <link rel="stylesheet" href="{{ cdn('css/icomoon.css') }}"> 
        <link rel="stylesheet" href="{{ cdn('css/responsive.css') }}">  
        
		  @if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
			<link rel="stylesheet" href="{{ asset('css/custom.css') }}"> 
		@else
			<link rel="stylesheet" href="{{ secure_asset('css/custom.css') }}"> 
		@endif
	  
		<script src="https://ldti.syndication.kbb.com/Scripts/LeadDriver/AutoResize.js"></script>
		<script type="text/javascript" src="https://d1jougtdqdwy1v.cloudfront.net/js/vendor/jquery-3.3.1.min.js"></script>
 </head>
    <body>
		@include('partials.header',['page' => 'landing']) 
            @yield('content')
        @include('partials.footer')   
	   
    </body>
</html>
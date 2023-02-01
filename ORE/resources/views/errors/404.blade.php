@extends('layouts.app')

@section('title', '404 Error')


@section('content')
    <section class="main"> 
        <div class="position-relative min-h-screen">            
            <a href="/" target="_self"><img class="error_img img-responsive" src="{{ cdn('images/error_404.png') }}"></a>
            <!--<a class="btn btn-primary error_btn" href="/">Back to Homepage</a>-->
        </div>
    </section>
@endsection
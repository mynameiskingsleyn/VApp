@extends('layouts.app')
@section('content')
        <title>Unauthorized</title>
        <style>

            .full-height {
                height: 60vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .code {
                border-right: 2px solid;
                font-size: 30px;
                padding: 0 15px 0 15px;
                text-align: center;
            }

            .message {
                font-size: 25px;
                text-align: center;
            }
        </style>
        <div class="container bodyContainerWrapper">
    <div class="row dealerHead">

        <div class="flex-center position-ref full-height">
            <!--div class="code">401</div-->
            <div class="message" style="padding: 10px;">
                <h2>Sorry! You are not enrolled for this program.</h2>
                <br/> 
                <!--h5>Please contact <a href="mailto:lisa.quest@carzato.com" target="_top"><u>lisa.quest@carzato.com</u></a> to enroll.</h5-->
                <h5>Please contact <a href="mailto:info@chryslerdigital.com" target="_top"><u>info@chryslerdigital.com</u></a> to enroll.</h5>
            </div>
        </div>
        </div>
        </div>
@endsection
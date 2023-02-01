
@extends('layouts.app')
@section('pageTitle' , ' - Login')
@section('content')
<div class="container bodyContainerWrapper">
    <div class="row justify-content-center">
        <div class="col-md-4 rift-soft">
            <div class="card login-card">
                <div class="card-header ">{{ __('Development Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{env('APP_URL')}}login">
                        {{ csrf_field() }}

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="USER NAME">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="PASSWORD">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                       <div class="form-group login-forgot-row row mb-0">
                            <div class="col-md-12">
                                <button type="submit" class="btn">
                                    {{ __('Login') }}
                                </button>

                              
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

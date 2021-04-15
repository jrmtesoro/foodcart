@extends('layouts.guest') 
@section('page-title', 'Login') 
@section('css')
<style>
    .navbar {
        box-shadow: 0 4px 18px 0px rgba(0, 0, 0, 0.12), 0 7px 10px -5px rgba(0, 0, 0, 0.15);
    }

    .form-control,
    .is-focused .form-control {
        background-image: linear-gradient(to top, #00bcd4 2px, rgba(156, 39, 176, 0) 2px), linear-gradient(to top, #d2d2d2 1px, rgba(210, 210, 210, 0) 1px);
    }

    .form-check .form-check-input:checked+.form-check-sign .check {
        background: #00bcd4;
    }
</style>
@endsection
 
@section('js')
    @if (!empty(session()->get('code')))
    <script>
    window.open("{{ route('customer.verification', ['code' => session()->get('code')]) }}", "_blank")
    </script>
    @endif

    @if (!empty(session()->get('forgot_code')))
    <script>
    window.open("{{ route('customer.forgot', ['code' => session()->get('forgot_code')]) }}", "_blank")
    </script>
    @endif
@endsection
 
@section('content')
<div class="page-header header-filter clear-filter" style="height: 380px; margin-top:70px; top: 10%;">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header card-header-text card-header-info" style="z-index: 999; background: #c40514 !important;">
                        <div class="card-text">
                            <h4 class="card-title m-0">Sign In</h4>
                        </div>
                    </div>
                    <div class="modal-body pb-0 bg-light">
                        @if (!empty(session()->has('verified')) && session()->get('verified') == false)
                        <div class="alert alert-warning alert-dismissible fade show">
                            <div class="text-center">
                                You need to verify your account to use our services.<br>
                                Click the button below to resend the verification mail.<br>
                            </div>
                            <div class="text-center pt-2">
                                <a href="{{ route('guest.resend') }}" class="btn btn-default">Resend Verification</a>
                                <p>or</p>
                                <a href="{{ route('guest.manual') }}" class="btn btn-default mt-0">Enter code manually</a>
                            </div>
                            <button type="button" class="close mt-1" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <p class="description text-center">Sign in with credentials</p>
                        <div class="card-body">
                            {!! Form::open(['route' => 'guest.login', 'action' => 'POST']) !!}
                            <div class="form-group pt-0">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="material-icons">email</i></div>
                                    </div>
                                    {!! Form::text('login_email', old('login_email') ?? '',[ 'class' => 'form-control', 'placeholder' => 'Email Address', 'required'
                                    => true ]) !!}
                                </div>
                            </div>
                            <div class="form-group pt-1">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="material-icons">lock_outline</i>
                                        </div>
                                    </div>
                                    <input type="password" name="login_password" placeholder="Password" class="form-control" required>
                                </div>
                            </div>
                            <div class="text-right">
                                <a class="my-auto text-warning" href="{{ route('forgot') }}">Forgot Password?</a>
                            </div>
                        </div>
                        <div class="card-footer justify-content-center bg-light">
                            <button class="btn btn-warning btn-round" type="submit" id="sign_in">Sign In</button>
                            <a href="{{ route('register') }}" class="btn btn-primary btn-round ml-3 red-color">Register</a>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
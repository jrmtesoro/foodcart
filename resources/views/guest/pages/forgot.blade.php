@extends('layouts.guest') 
@section('page-title', 'Forgot Password') 
@section('css')
<style>
    .navbar {
        box-shadow: 0 4px 18px 0px rgba(0, 0, 0, 0.12), 0 7px 10px -5px rgba(0, 0, 0, 0.15);
    }
</style>
@endsection
 
@section('js')
@endsection
 
@section('content')

<div class="container">
    <div class="row" style="margin-top:70px;">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header card-header-text card-header-warning">
                    <div class="card-text">
                        <h4 class="card-title pt-1 m-0">Forgot Password</h4>
                    </div>
                </div>
                {!! Form::open(['route' => 'guest.forgot', 'action' => 'POST']) !!}
                <div class="card-body">
                    <p class="description text-center text-muted pt-3">Please enter your email address to request a password reset.</p>
                    <div class="form-group">
                        <label>Email Address</label> {!! Form::email('forgot_email', old('forgot_email') ?? '', [ "class" => "form-control",
                        "required" => true ]) !!}
                    </div>
                </div>
                <div class="card-footer justify-content-center bg-light">
                    <button type="submit" class="btn btn-warning btn-round ml-3">Reset Password</button>
                    <a href="{{ route('login') }}" class="btn btn-info btn-round ml-3">Back to Login</a>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
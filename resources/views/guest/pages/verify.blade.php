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
@endsection
 
@section('content')
<div class="page-header header-filter clear-filter" style="height: 380px; margin-top:70px; top: 10%;">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header card-header-text card-header-info" style="z-index: 999;">
                        <div class="card-text">
                            <h4 class="card-title m-0">Sign In</h4>
                        </div>
                    </div>
                    <div class="modal-body pb-0 bg-light">
                        <div class="card-body">
                            <p class="text-center">You can enter the code we emailed to you in the text box below.</p>
                            {!! Form::open(['route' => 'guest.verify', 'method' => 'get']) !!}
                            <div class="form-group bmd-form-group">
                                <label for="verification_token" class="bmd-label-floating">Verification Token</label>
                                <input class="form-control" name="verification_token" required>
                            </div>
                        </div>
                        <div class="card-footer justify-content-center bg-light">
                            <button class="btn btn-info" type="submit">Submit</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
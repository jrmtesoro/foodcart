@extends('layouts.guest') 
@section('page-title', 'Reset Password') 
@section('css')
<style>
    .navbar {
        box-shadow: 0 4px 18px 0px rgba(0, 0, 0, 0.12), 0 7px 10px -5px rgba(0, 0, 0, 0.15);
    }
</style>
@endsection
 
@section('js')
<script type="text/javascript" src="{{ asset('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('#reset_form').validate({
        rules: {
            forgot_password: "required",
            forgot_password_confirm: "required",
            forgot_password: {
                required: true,
                passwordStrength: true,
                minlength: 8,
                maxlength: 21
            },
            forgot_password_confirm: {
                equalTo: "#forgot_password"
            }
        },
        messages: {
            forgot_password: "Please provide a password",
            forgot_password_confirm: "Please provide a password",
            forgot_password: {
                required: "Please provide a password",
                minlength: "Password must be atleast 8 characters in length",
                maxlength: "Password must not exceed 21 characters in length"
            },
            forgot_password_confirm: {
                equalTo: "Please enter the same password as above"
            }
        },
        errorElement: "div",
        errorPlacement: function(error, element) {
                error.addClass("invalid-feedback");
                error.insertAfter(element);
        },
        highlight: function(element, errorClass, validClass) {
            var $parent = $(element).parent('.form-group');
            $parent.addClass('has-danger').removeClass('has-success');

            var id = $(element).attr('name');
            $('#'+id+'-valid').remove();
            var logo = '<span class="material-icons form-control-feedback" id="'+id+'-invalid">clear</span>';
            if (!$('#'+id+'-invalid').length) {
                $(logo).insertAfter(element);
            }
        },
        unhighlight: function(element, errorClass, validClass) {
            var $parent = $(element).parent('.form-group');
            $parent.addClass('has-success').removeClass('has-danger');

            var id = $(element).attr('name');
            $('#'+id+'-invalid').remove();
            var logo = '<span class="material-icons form-control-feedback" id="'+id+'-valid">done</span>';
            if (!$('#'+id+'-valid').length) {
                $(logo).insertAfter(element);
            }
        }
    });

    jQuery.validator.addMethod("passwordStrength", function(value, element) {
        return this.optional(element) || /^(?=.*[A-Z])(?=.*[!@#$&*])(?=.*[0-9])(?=.*[a-z])/.test(value);
    },"Password must contain atleast 1 digit, lower case letter, uppercase letter and symbol.");
});

</script>
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
                {!! Form::open(['route' => 'guest.reset', 'action' => 'POST', 'id' => 'reset_form']) !!}
                <div class="card-body">
                    <p class="description text-center text-muted pt-3">Please change your password</p>
                    <div class="form-group">
                        <label>Email Address : </label>
                        <p class="pl-3 d-inline">{{ $email }}</p>
                        <input type="hidden" name="forgot_email" value="{{ $email }}">
                        <input type="hidden" name="forgot_token" value="{{ $token }}">
                    </div>
                    <div class="form-group">
                        <label>Password : </label>
                        <input type="password" name="forgot_password" id="forgot_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password Confirm : </label>
                        <input type="password" name="forgot_password_confirm" id="forgot_password_confirm" class="form-control" required>
                    </div>
                </div>
                <div class="card-footer justify-content-center bg-light">
                    <button type="submit" class="btn btn-warning btn-round ml-3">Change Password</button>
                    <a href="{{ route('login') }}" class="btn btn-info btn-round ml-3">Back to Login</a>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
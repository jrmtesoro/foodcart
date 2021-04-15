@extends('layouts.guest') 
@section('page-title', 'Register') 
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
    $('#register_form').validate({
        rules: {
            reg_fname: "required",
            reg_lname: "required",
            reg_contact_number: "required",
            reg_email: "required",
            reg_address: "required",
            reg_password: "required",
            reg_password_confirm: "required",

            reg_fname: {
                required: true
            },
            reg_lname: {
                required: true
            },
            reg_contact_number: {
                required: true,
                minlength: 7,
                maxlength: 11,
                digits: true,
                validMobile: true
            },
            reg_email: {
                required: true,
                email: true
            },
            reg_password: {
                required: true,
                passwordStrength: true,
                minlength: 8,
                maxlength: 21
            },
            reg_password_confirm: {
                required: true,
                equalTo: "#reg_password"
            }
        },
        messages: {
            reg_fname: "Please enter your First Name",
            reg_lname: "Please enter your Last Name",
            reg_contact_number: "Please enter your Contact Number",
            reg_email: "Please enter your Email Address",
            reg_password: "Please provide a password",
            reg_password_confirm: "Please provide a password",
            reg_fname: {
                required: "Please enter your First Name"
            },
            reg_lname: {
                required: "Please enter your Last Name"
            },
            reg_contact_number: {
                required: "Please enter your Contact Number",
                minlength: "Contact Number should be a landline or mobile number",
                maxlength: "Contact Number should be a landline or mobile number",
                digits: "Contact Number should be in numeric form",
                validMobile: "Invalid mobile contact number"
            },
            reg_email: {
                required: "Please enter your Email Address",
                email: "Please enter a valid Email Address"
            },
            reg_password: {
                required: "Please provide a password",
                minlength: "Password must be atleast 8 characters in length",
                maxlength: "Password must not exceed 21 characters in length"
            },
            reg_password_confirm: {
                required: "Please provide a password",
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

    jQuery.validator.addMethod("validMobile", function(value, element) {
        return this.optional(element) || /^[0][9][1-9]\d{8}$|^[1-9]\d{6}$/.test(value);
    },"Invalid mobile contact number");

    jQuery.validator.addMethod("passwordStrength", function(value, element) {
        return this.optional(element) || /^(?=.*[A-Z])(?=.*[!@#$&*])(?=.*[0-9])(?=.*[a-z])/.test(value);
    },"Password must contain atleast 1 digit, lower case letter, uppercase letter and symbol.");
});

</script>
@endsection
 
@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-text card-header-primary" style="background: #c40514 !important;">
                    <div class="card-text">
                        <h4 class="card-title pt-1 m-0">Registration Form</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card-body pb-0">
                            <h6 class="text-muted">All fields below are required</h6>
                        </div>
                    </div>
                </div>
                {!! Form::open(['route' => 'guest.register', 'action' => 'POST', 'id' => 'register_form']) !!}
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="card-body">
                            <h4 class="card-title border-bottom pb-2" style="border-color: #9c27b0 !important;">Personal Information</h4>
                            <div class="form-group ml-4">
                                <label>First Name</label>
                                {!! Form::text('reg_fname', old('reg_fname') ?? '', [
                                    "class" => "form-control",
                                    "required" => true
                                ]) !!}
                            </div>
                            <div class="form-group ml-4">
                                <label>Last Name</label>
                                {!! Form::text('reg_lname', old('reg_lname') ?? '', [
                                    "class" => "form-control",
                                    "required" => true
                                ]) !!}
                            </div>
                            <div class="form-group ml-4">
                                <label>Contact Number</label>
                                {!! Form::text('reg_contact_number', old('reg_contact_number') ?? '', [
                                    "class" => "form-control",
                                    "required" => true,
                                    "data-toggle" => "tooltip",
                                    "data-placement" => "left",
                                    "data-container" => "body",
                                    "data-original-title" => "Enter your cellphone or landline number. (eg. 09165445809 or 6583792)"
                                ]) !!}
                            </div>
                            <div class="form-group ml-4">
                                <label>Address</label>
                                {!! Form::textarea('reg_address', old('reg_address') ?? '', [
                                    "class" => "form-control",
                                    "required" => true,
                                    "rows" => 3,
                                    "data-toggle" => "tooltip",
                                    "data-placement" => "left",
                                    "data-container" => "body",
                                    "data-original-title" => "Enter your full address here"
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="card-body">
                            <h4 class="card-title border-bottom pb-2" style="border-color: #9c27b0 !important;">Account Information</h4>
                            <div class="form-group ml-4">
                                <label>Email Address</label>
                                {!! Form::email('reg_email', old('reg_email') ?? '', [
                                    "class" => "form-control",
                                    "required" => true,
                                    "data-toggle" => "tooltip",
                                    "data-placement" => "right",
                                    "data-container" => "body",
                                    "data-original-title" => "Enter a valid email address"
                                ]) !!}
                            </div>
                            <div class="form-group ml-4">
                                <label>Password</label>
                                <input type="password" class="form-control" 
                                id="reg_password" name="reg_password" 
                                required
                                data-toggle="tooltip" data-placement="right" title="" data-container="body" 
                                data-original-title="Password must contain atleast 1 digit, lower case letter, uppercase letter and symbol, It must be between 8-21 characters in length.">
                            </div>
                            <div class="form-group ml-4">
                                <label>Password Confirmation</label>
                                <input type="password" class="form-control" 
                                id="reg_password_confirm" name="reg_password_confirm" 
                                required
                                data-toggle="tooltip" data-placement="right" title="" data-container="body" 
                                data-original-title="Retype your password here">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer justify-content-center bg-light">
                    <button type="submit" class="btn btn-warning btn-round ml-3">Register</button>
                    {{-- <a href="{{ route('login') }}" class="btn btn-info btn-round ml-3">Back to Login</a> --}}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<footer class="footer mt-5 text-light w-100" style="background-color: #000;">
    <div class="container">
        <nav class="float-left">
            <ul>
                <li>
                    <a href="https://www.pinoyfoodcart.com">
                        Pinoy Food Cart
                    </a>
                </li>
            </ul>
        </nav>
        <div class="float-right">
            <img src="{{ asset('img/google_play.png') }}" width="250" height="100">
        </div>
    </div>
</footer>
@endsection
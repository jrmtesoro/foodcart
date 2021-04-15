@extends('layouts.guest') 
@section('page-title', 'Partnership') 
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
            reg_restaurant_name: "required",
            reg_fname: "required",
            reg_lname: "required",
            reg_contact_number: "required",
            reg_email: "required",
            reg_address: "required",
            reg_permit: "required",

            reg_restaurant_name: {
                required: true
            },
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
            reg_permit: {
                required: true
            },
        },
        messages: {
            reg_restaurant_name: "Please enter the Restaurant Name",
            reg_fname: "Please enter your First Name",
            reg_lname: "Please enter your Last Name",
            reg_contact_number: "Please enter your Contact Number",
            reg_email: "Please enter your Email Address",
            reg_permit: "Please select your Business Permit",
            reg_restaurant_name: {
                required: "Please enter the Restaurant Name"
            },
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
            reg_permit: {
                required: "Please select your Business Permit"
            },
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
        <div class="col-sm-12 col-md-6 mr-auto">
            <div class="card">
                <div class="card-header card-header-text card-header-primary" style="background: #c40514 !important;">
                    <div class="card-text">
                        <h4 class="card-title pt-1 m-0">Partnership Form</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card-body pb-0">
                            @if (session()->has('errors'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                                <button type="button" class="close mt-1" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif
                            <h6 class="text-muted">All fields below are required</h6>
                        </div>
                    </div>
                </div>
                {!! Form::open(['route' => 'guest.partner', 'action' => 'POST', 'id' => 'register_form', 'enctype' => 'multipart/form-data']) !!}
                <div class="row">
                    <div class="col">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Restaurant Name</label>
                                {!! Form::text('reg_restaurant_name', old('reg_restaurant_name') ?? '', [
                                    "class" => "form-control",
                                    "required" => true,
                                    "data-toggle" => "tooltip",
                                    "data-placement" => "left",
                                    "data-container" => "body",
                                    "data-original-title" => "Enter your full restaurant name here"
                                ]) !!}
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                {!! Form::textarea('reg_address', old('reg_address') ?? '', [
                                    "class" => "form-control",
                                    "required" => true,
                                    "rows" => 3,
                                    "data-toggle" => "tooltip",
                                    "data-placement" => "left",
                                    "data-container" => "body",
                                    "data-original-title" => "Enter your full restaurant address here"
                                ]) !!}
                            </div>
                            <div class="form-group">
                                <label>First Name</label>
                                {!! Form::text('reg_fname', old('reg_fname') ?? '', [
                                    "class" => "form-control",
                                    "required" => true
                                ]) !!}
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                {!! Form::text('reg_lname', old('reg_lname') ?? '', [
                                    "class" => "form-control",
                                    "required" => true
                                ]) !!}
                            </div>
                            <div class="form-group">
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
                            <div class="form-group">
                                <label>Email Address</label>
                                {!! Form::email('reg_email', old('reg_email') ?? '', [
                                    "class" => "form-control",
                                    "required" => true,
                                    "data-toggle" => "tooltip",
                                    "data-placement" => "left",
                                    "data-container" => "body",
                                    "data-original-title" => "Enter an unregistered and valid email address here"
                                ]) !!}
                            </div>
                            <div class="form-group">
                                <label>Business Documents</label>
                                <div class="card mt-0">
                                    <div class="card-body">
                                        <p>Please provide three (3) of the following business documents: </p>
                                        <li><b>Mayor's Permit</b></li>
                                        <li><b>Barangay Business Clearance or Permit</b></li>
                                        <li><b>DTI</b></li>
                                        <li><b>BIR2303</b></li>
                                        <hr>
                                        <p>Business documents should be in <b>JPEG</b>, <b>JPG</b>, or <b>PNG</b> file format.</p>
                                        <p>The maximum file size per file is <b>5MB</b>.</p>
                                        <p class="my-0">Click the buttons below to upload your scanned documents</p>
                                        <hr>
                                        <input type="file" class="form-control-file pt-2" name="reg_permit_1" accept=".png,.jpeg,.jpg" required>
                                        <input type="file" class="form-control-file pt-2" name="reg_permit_2" accept=".png,.jpeg,.jpg" required>
                                        <input type="file" class="form-control-file pt-2" name="reg_permit_3" accept=".png,.jpeg,.jpg" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer justify-content-center bg-light">
                    <button type="submit" class="btn btn-warning btn-round ml-3">Apply</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="col-sm-12 col-md-5">
            <div class="card">
                <div class="card-header card-header-text card-header-info" style="background: #c40514 !important;">
                    <div class="card-text">
                        <h4 class="card-title pt-1 m-0">Check Application Status</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card-body">
                            <p class="title mt-2 mb-0 text-center">Have you already applied to us?</p> 
                            <p class="title my-0 text-center">Check your application status here!</p>
                            <div class="form-group">
                                <label>Email Address</label>
                                {!! Form::open(['route' => 'guest.partner.status', 'action' => 'POST'])!!}
                                {!! Form::email('partner_email', old('partner_email') ?? '', [
                                    "class" => "form-control",
                                    "required" => true
                                ]) !!}
                            </div>
                            <button type="submit" class="btn btn-warning btn-block">Check Status</button>
                            {!! Form::close() !!}
                            @if (!empty($details))
                            <div class="card">
                                <div class="card-body">
                                    <h6>Email Address:</h6>{{ $details['email'] }}
                                    <hr>
                                    <h6>Status:</h6>{{ $details['status'] }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
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
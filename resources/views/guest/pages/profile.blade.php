@extends('layouts.guest') 
@section('page-title', 'Pinoy Food Cart') 
@section('css')
@endsection
 
@section('js')
<script type="text/javascript" src="{{ asset('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('#update_profile').validate({
        rules: {
            reg_fname: "required",
            reg_lname: "required",
            reg_contact_number: "required",
            reg_address: "required",

            reg_fname: {
                minlength: 3,
                maxlength: 30,
                required: true
            },
            reg_lname: {
                minlength: 3,
                maxlength: 30,
                required: true
            },
            reg_contact_number: {
                required: true,
                minlength: 7,
                maxlength: 11,
                digits: true,
                validMobile: true
            }
        },
        messages: {
            reg_fname: "Please enter your First Name",
            reg_lname: "Please enter your Last Name",
            reg_contact_number: "Please enter your Contact Number",
            reg_fname: {
                minlength: "First Name must be atleast 3 characters in length",
                maxlength: "First Name must not exceed 30 characters in length",
                required: "Please enter your First Name"
            },
            reg_lname: {
                minlength: "Last Name must be atleast 3 characters in length",
                maxlength: "Last Name must not exceed 30 characters in length",
                required: "Please enter your Last Name"
            },
            reg_contact_number: {
                required: "Please enter your Contact Number",
                minlength: "Contact Number should be a landline or mobile number",
                maxlength: "Contact Number should be a landline or mobile number",
                digits: "Contact Number should be in numeric form",
                validMobile: "Invalid mobile contact number"
            },
            reg_address : {
                required : "Please enter your address"
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

    $('#change_password').validate({
        ignore: '.ignore',
        rules: {
            user_old_password: "required",
            user_password: "required",
            user_password1: "required",

            user_old_password: {
                required: true,
            },
            user_password: {
                passwordStrength: true,
                required: true,
                minlength: 8,
                maxlength: 21
            },
            user_password1: {
                required: true,
                equalTo: "#user_password"
            }
        },
        messages: {
            user_password: "Please provide a password",
            user_password1: "Please provide a password",
            user_old_password: "Please provide a password",

            user_password: {
                required: "Please provide a password",
                minlength: "Password must be atleast 8 characters in length",
                maxlength: "Password must not exceed 21 characters in length"
            },
            user_password1: {
                required: "Please provide a password",
                equalTo: "Please enter the same password as above"
            },
            user_old_password: {
                required: "Please provide a password",
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

    $('#change_email_form').validate({
        ignore: '.ignore',
        rules: {
            request_new_email: "required",
            request_reason: "required",

            request_new_email: {
                required: true,
                email: true
            },
            request_reason: {
                required: true
            }
        },
        messages: {
            request_new_email: "Please enter your new email address.",
            request_reason: "Please provide a reason",

            request_new_email: {
                required: "Please enter your new email address",
                email: "Please enter a valid Email Address"
            },
            request_reason: {
                required: "Please provide a reason",
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
                <div class="card-body">
                    <div class="card-header card-header-text card-header-primary">
                        <div class="card-text">
                            <h4 class="card-title pt-1 m-0">Edit Profile</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card-body pb-0">
                                <h6 class="text-muted">All fields below are required</h6>
                            </div>
                        </div>
                    </div>
                    @if (session()->has('customer_error'))
                    <div class="alert alert-danger">
                        <div class="container">
                            <div class="alert-icon">
                                <i class="material-icons">error_outline</i>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="material-icons">clear</i></span>
                            </button>
                            <b>Invalid Input<b>
                            @foreach (session()->get('customer_error') as $error)
                                @foreach ($error as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    @elseif (session()->has('user_error'))
                    <div class="alert alert-danger">
                        <div class="container">
                            <div class="alert-icon">
                                <i class="material-icons">error_outline</i>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="material-icons">clear</i></span>
                            </button>
                            <b>Invalid Input<b>
                            @foreach (session()->get('user_error') as $error)
                                @foreach ($error as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @if (session()->has('change_email_error'))
                    <div class="alert alert-danger">
                        <div class="container">
                            <div class="alert-icon">
                                <i class="material-icons">error_outline</i>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="material-icons">clear</i></span>
                            </button>
                            <b>Invalid Input<b>
                            @foreach (session()->get('change_email_error') as $error)
                                @foreach ($error as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    @endif
                    {!! Form::open(['route' => 'customer.update', 'action' => 'POST', 'id' => 'update_profile']) !!}
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title border-bottom pb-2" style="border-color: #9c27b0 !important;">Personal Information</h4>
                                    <div class="form-group ml-4">
                                        <label>First Name</label>
                                        {!! Form::text('reg_fname', old('reg_fname') ?? $user['customer']['fname'], [
                                            "class" => "form-control",
                                            "required" => true
                                        ]) !!}
                                    </div>
                                    <div class="form-group ml-4">
                                        <label>Last Name</label>
                                        {!! Form::text('reg_lname', old('reg_lname') ?? $user['customer']['lname'], [
                                            "class" => "form-control",
                                            "required" => true
                                        ]) !!}
                                    </div>
                                    <div class="form-group ml-4">
                                        <label>Contact Number</label>
                                        {!! Form::text('reg_contact_number', old('reg_contact_number') ?? $user['customer']['contact_number'], [
                                            "class" => "form-control",
                                            "required" => true
                                        ]) !!}
                                    </div>
                                    <div class="form-group ml-4">
                                        <label>Address</label>
                                        {!! Form::textarea('reg_address', old('reg_address') ?? $user['customer']['address'], [
                                            "class" => "form-control",
                                            "required" => true,
                                            "rows" => 3
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary btn-block ml-3">Edit Profile</button>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}

                        <div class="col-sm-12 col-md-6">
                            {!! Form::open(['route' => 'customer.user.update', 'action' => 'POST', 'id' => 'change_password']) !!}
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title border-bottom pb-2" style="border-color: #9c27b0 !important;">Account Information</h4>
                                    <div class="form-group ml-4">
                                        <label>Email Address</label>
                                        <p class="h5 border-bottom">{{ old('reg_email') ?? $user['email'] }}</p>
                                    </div>
                                    {{-- <a class="h6 ml-4 text-primary" data-toggle="modal" data-target="#change_email" href="#"><u>Request Change Email</u></a> --}}
                                    <div class="form-group ml-4 mt-3">
                                        <label>Old Password</label>
                                        <input type="password" class="form-control" id="user_old_password" name="user_old_password">
                                    </div>
                                    <div class="form-group ml-4 mt-3">
                                        <label>New Password</label>
                                        <input type="password" class="form-control" id="user_password" name="user_password">
                                    </div>
                                    <div class="form-group ml-4">
                                        <label>New Password Confirmation</label>
                                        <input type="password" class="form-control" id="user_password1" name="user_password1">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info btn-block ml-3">Change Password</button>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="change_email">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Email Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'changerequest.store.customer', 'id' => 'change_email_form']) !!}
                <div class="form-group">
                    <label>New Email Address</label>
                    <input type="email" class="form-control" id="request_new_email" name="request_new_email">
                </div>
                <div class="form-group">
                    <label>Reason for changing email address</label>
                    {!! Form::textarea('request_reason', '', [
                        "class" => "form-control",
                        "required" => true,
                        "rows" => 4,
                        "id" => "request_reason"
                    ]) !!}
                </div>
            </div>
            <div class="modal-footer mx-auto">
                <button type="submit" class="btn btn-primary">Submit Request</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            {!! Form::close() !!}
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
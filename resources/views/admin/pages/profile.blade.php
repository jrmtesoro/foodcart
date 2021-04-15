@extends('layouts.admin') {{-- Page Title --}} 
@section('page-title', 'Admin Dashboard') {{-- Page Name --}} 
@section('page-name',
'Profile') {{-- Custom CSS --}} 
@section('css')
<style>
textarea {
    resize: none;
}
</style>
@endsection
 
@section('js')
<script type="text/javascript" src="{{ asset('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
<script>
$('#update_admin_profile').validate({
    ignore: '.ignore',
    rules: {
        fname: "required",
        lname: "required",
        contact_number: "required",
        address: "required",

        fname: {
            minlength: 3,
            maxlength: 30,
            required: true
        },
        lname: {
            minlength: 3,
            maxlength: 30,
            required: true
        },
        contact_number: {
            required: true,
            minlength: 7,
            maxlength: 11,
            digits: true,
            validMobile: true
        },
    },
    messages: {
        fname: "Please enter your First Name",
        lname: "Please enter your Last Name",
        contact_number: "Please enter your Contact Number",
        address: "Please enter your Address",

        fname: {
            minlength: "First Name must be atleast 3 characters in length",
            maxlength: "First Name must not exceed 30 characters in length",
            required: "Please enter your First Name"
        },
        lname: {
            minlength: "Last Name must be atleast 3 characters in length",
            maxlength: "Last Name must not exceed 30 characters in length",
            required: "Please enter your Last Name"
        },
        contact_number: {
            required: "Please enter your Contact Number",
            minlength: "Contact Number should be a landline or mobile number",
            maxlength: "Contact Number should be a landline or mobile number",
            digits: "Contact Number should be in numeric form",
            validMobile: "Invalid mobile contact number"
        },
        address : {
            required : "Please enter your address"
        },
    },
    errorElement: "div",
    errorPlacement: function(error, element) {
            var err_id = '#'+error.attr('id')+"-temp";
            if ($(err_id).length) {
                $(err_id).remove();
            } 

            error.addClass("invalid-feedback");
            error.insertAfter(element);
    },
    highlight: function(element, errorClass, validClass) {
        var parent = $(element).parent('.form-group');
        parent.addClass('has-danger').removeClass('has-success');
        $(element).addClass("is-invalid").removeClass("is-valid");
    },
    unhighlight: function(element, errorClass, validClass) {
        var parent = $(element).parent('.form-group');
        parent.removeClass('has-danger');
        $(element).removeClass("is-invalid");
    }
});

$('#change_password').validate({
    ignore: '.ignore',
    rules: {
        user_old_password: "required",
        user_password: "required",
        user_password1: "required",

        user_password: {
            passwordStrength: true,
            required: true,
            minlength: 8,
            maxlength: 21
        },
        user_password1: {
            required: true,
            equalTo: "#user_password"
        },
        user_old_password: {
            required: true,
        }
    },
    messages: {
        user_password: "Please provide your old password",
        user_password: "Please provide a password",
        user_password1: "Please provide a password",

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
            required: "Please provide your old password",
        }
    },
    errorElement: "div",
    errorPlacement: function(error, element) {
            var err_id = '#'+error.attr('id')+"-temp";
            if ($(err_id).length) {
                $(err_id).remove();
            } 

            error.addClass("invalid-feedback");
            error.insertAfter(element);
    },
    highlight: function(element, errorClass, validClass) {
        var parent = $(element).parent('.form-group');
        parent.addClass('has-danger').removeClass('has-success');
        $(element).addClass("is-invalid").removeClass("is-valid");
    },
    unhighlight: function(element, errorClass, validClass) {
        var parent = $(element).parent('.form-group');
        parent.removeClass('has-danger');
        $(element).removeClass("is-invalid");
    }
}); 

jQuery.validator.addMethod("notEqual", function (value, element, param) {
    return this.optional(element) || value != '0';
});

jQuery.validator.addMethod("validMobile", function(value, element) {
    return this.optional(element) || /^[0][9][1-9]\d{8}$|^[1-9]\d{6}$/.test(value);
},"Invalid mobile contact number");

jQuery.validator.addMethod("passwordStrength", function(value, element) {
    return this.optional(element) || /^(?=.*[A-Z])(?=.*[!@#$&*])(?=.*[0-9])(?=.*[a-z])/.test(value);
},"Password must contain atleast 1 digit, lower case letter, uppercase letter and symbol.");
</script>
@endsection
 
@section('content')
    @include('admin.inc.sidebar')
<div class="main-content">
    @include('admin.inc.navbar')
    <div class="header bg-gradient-info pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
        </div>
    </div>
    <div class="container-fluid mt--9">
        <div class="row">
            <div class="col-xl-8">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Admin Information</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['route' => 'admin.update', 'id' => 'update_admin_profile', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                            @if (session()->has('admin_error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                @foreach (session()->get('admin_error') as $error)
                                    @foreach ($error as $err)
                                    <li>{{ $err }}</li>
                                    @endforeach
                                @endforeach
                                <button type="button" class="close mt-1" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif
                            <div class="pl-lg-4">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <label class="form-control-label">First Name</label>
                                        <div class="form-group focused">
                                            {!! Form::text('fname', $admin['fname'], [
                                                "class" => "form-control form-control-alternative",
                                                "required" => true
                                            ]) !!}   
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label">Last Name</label>
                                            {!! Form::text('lname', $admin['lname'], [
                                                "class" => "form-control form-control-alternative",
                                                "required" => true
                                            ]) !!}                            
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label class="form-control-label">Address</label>
                                        <div class="form-group focused">
                                            <textarea rows="4" class="form-control form-control-alternative" name="address" required>{{ $admin['address'] }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-control-label">Contact Number</label>
                                        <div class="form-group focused">
                                            {!! Form::text('contact_number', $admin['contact_number'], [
                                                "class" => "form-control form-control-alternative",
                                                "required" =>true
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-6 text-right">
                                <button type="submit" class="btn btn-info">Save Changes</button>
                            </div>
                            <div class="col-6 text-left">
                                <button type="reset" class="btn btn-danger">Reset</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">User Information</h3>
                            </div>
                        </div>
                    </div>
                    {!! Form::open(['route' => 'admin.user.update', 'id' => 'change_password']) !!}
                    <div class="card-body">
                        @if (session()->has('user_error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            @foreach (session()->get('user_error') as $error)
                                @foreach ($error as $err)
                                <li>{{ $err }}</li>
                                @endforeach
                            @endforeach
                            <button type="button" class="close mt-1" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="input-username">Email Address</label>
                                    {!! Form::email('email', $user['email'], [
                                        "class" => "form-control form-control-alternative",
                                        "disabled" => true,
                                        "required" => true
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <label class="form-control-label">Old Password</label>
                                <div class="form-group focused">
                                    <input type="password" class="form-control form-control-alternative" name="user_old_password" id="user_old_password" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <label class="form-control-label">Password</label>
                                <div class="form-group focused">
                                    <input type="password" class="form-control form-control-alternative" name="user_password" id="user_password" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <label class="form-control-label">Confirm Password</label>
                                <div class="form-group focused">
                                    <input type="password" class="form-control form-control-alternative" name="user_password1" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-info btn-block">Save</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
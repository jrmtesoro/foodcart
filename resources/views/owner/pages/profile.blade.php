@extends('layouts.owner') {{-- Page Title --}} 
@section('page-title', 'Owner Dashboard') {{-- Page Name --}} 
@section('page-name',
'Profile') {{-- Custom CSS --}} 
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/timedropper/timedropper.css') }}">
<style>
textarea {
    resize: none;
}
</style>
@endsection
 
@section('js')
<script type="text/javascript" src="{{ asset('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('vendor/timedropper/timedropper.js') }}"></script>

<script>
$( "#open_time" ).timeDropper({
    meridians: true,
    setCurrentTime: false
});
$( "#close_time" ).timeDropper({
    meridians: true,
    setCurrentTime: false
});

$("#24hours").change(function() {
    if (this.checked) {
        $('input[name=open_time]').attr('disabled', 'true');
        $('input[name=open_time]').removeAttr('required');
        $('input[name=close_time]').attr('disabled', 'true');
        $('input[name=close_time]').removeAttr('required');
        $('#open_24').removeClass('text-default').addClass('text-primary');
    } else {
        $('input[name=open_time]').removeAttr('disabled');
        $('input[name=open_time]').attr('required', 'true');
        $('input[name=close_time]').removeAttr('disabled');
        $('input[name=close_time]').attr('required', 'true');
        $('#open_24').removeClass('text-primary').addClass('text-default');
    }
});
</script>
@if ($restaurant['open_time'] == $restaurant['close_time'])
<script>
    $('#24hours').attr('checked', true);
    $('input[name=open_time]').attr('disabled', 'true');
    $('input[name=open_time]').removeAttr('required');
    $('input[name=close_time]').attr('disabled', 'true');
    $('input[name=close_time]').removeAttr('required');
</script>
@endif

<script>
$('#update_restaurant_profile').validate({
    ignore: '.ignore',
    rules: {
        flat_rate: "required",
        contact_number: "required",
        address: "required",
        eta: "required",
        open_time : "required",
        close_time : "required",

        flat_rate: {
            required: true,
            digits: true,
            minlength: 1,
            maxlength: 3,
            notEqual: '0'
        },
        eta: {
            required: true,
            digits: true,
            minlength: 1,
            maxlength: 3,
            notEqual: '0'
        },
        contact_number: {
            required: true,
            digits: true,
            minlength: 7,
            maxlength: 11,
            validMobile: true
        },
        address: {
            required: true
        },
        open_time: {
            required: true
        },
        close_time: {
            required: true
        }
    },
    messages: {
        flat_rate: "Please enter the flat rate",
        contact_number: "Please enter the contact number",
        address: "Please enter the address",
        eta: "Please enter the estimated time",
        open_time: "Please enter the opening time",
        close_time: "Please enter the closing time",

        flat_rate: {
            required: "Please enter the flat rate",
            digits: "The flat rate should be in numerical form",
            maxlength: "Flat rate must not exceed 3 characters in length",
            minlength: "Flat rate must be atleast 1 characters in length"
        },
        eta: {
            required: "Please enter the estimated time",
            digits: "The estimated time should be in numerical form",
            maxlength: "Estimated time must not exceed 3 characters in length",
            minlength: "Estimated time must be atleast 1 characters in length"
        },
        contact_number: {
            required: "Please enter your contact number",
            digits: "The contact number should be in numerical form",
            maxlength: "Contact number must not exceed 11 characters in length",
            minlength: "Contact number must atleast 7 characters in length",
            validMobile: "Invalid mobile contact number"
        },
        address: {
            required: "Please enter the address"
        },
        open_time: {
            required: "Please enter the opening time"
        },
        close_time: {
            required: "Please enter the closing time"
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

$('#change_password').validate({
    ignore: '.ignore',
    rules: {
        user_password: "required",
        user_password1: "required",
        user_old_password : "required",

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
        user_old_password : {
            required: true
        }
    },
    messages: {
        user_old_password : "Please provide your old password",
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
        user_old_password : {
            required: "Please provide your old password"
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
        request_new_email : "Please provide your new email address",
        request_reason: "Please provide your reason",

        request_new_email: {
            required: "Please enter your Email Address",
            email: "Please enter a valid Email Address"
        },
        request_reason: {
            required: "Please provide your reason"
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
    @include('owner.inc.sidebar')
<div class="main-content">
    @include('owner.inc.navbar')
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
                                <h3 class="mb-0">Restaurant Information</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['route' => 'owner.update', 'id' => 'update_restaurant_profile', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                            @if (session()->has('restaurant_error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                @foreach (session()->get('restaurant_error') as $error)
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
                                    <div class="col-lg-6">
                                        <img src="{{ !empty($restaurant['image_name']) ? route('photo.restaurant', ['slug' => $restaurant['image_name']])."?size=medium" : asset('img/alt.png') }}" width="300" height="200">
                                    </div>
                                    <div class="col-lg-6 my-auto" data-toggle="tooltip" data-placement="top" title="" data-container="body" data-original-title="Pick your restaurant logo">
                                        <div class="form-group focused">
                                            <label class="form-control-label">Restaurant Image</label>
                                            <input type="file" name="image_name" class="form-control-file" accept=".png, .jpeg, .jpg"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row pt-3">
                                    <div class="col-lg-6">
                                        <div class="form-group focused">
                                            <label class="form-control-label">Restaurant Name</label>
                                            {!! Form::text('name', $restaurant['name'], [
                                                "class" => "form-control form-control-alternative",
                                                "disabled" => true
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="form-control-label">Flat Rate</label>
                                        <div class="form-group focused">
                                            {!! Form::number('flat_rate', $restaurant['flat_rate'] ?? '', [
                                                "class" => "form-control form-control-alternative",
                                                "required" => true,
                                                "min" => 1,
                                                "data-toggle" => "tooltip",
                                                "data-placement" => "bottom",
                                                "data-container" => "body",
                                                "data-original-title" => "This is the delivery charge, It must be in PHP."
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="form-control-label">Estimated Time</label>
                                        <div class="form-group focused">
                                            {!! Form::number('eta', $restaurant['eta'] ?? '', [
                                                "class" => "form-control form-control-alternative",
                                                "required" => true,
                                                "min" => 1,
                                                "data-toggle" => "tooltip",
                                                "data-placement" => "bottom",
                                                "data-container" => "body",
                                                "data-original-title" => "This is your preparation and delivery time, It should be in minutes."
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label class="form-control-label">Address</label>
                                        <div class="form-group focused">
                                            <textarea rows="4" class="form-control form-control-alternative" name="address" 
                                            required
                                            data-toggle="tooltip" data-placement="left" title="" data-container="body" data-original-title="Enter your restaurant's full address here">{{ $restaurant['address'] }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label class="form-control-label">Contact Number</label>
                                        <div class="form-group focused">
                                            {!! Form::text('contact_number', $restaurant['contact_number'], [
                                                "class" => "form-control form-control-alternative",
                                                "required" =>true,
                                                "data-toggle" => "tooltip",
                                                "data-placement" => "left",
                                                "data-container" => "body",
                                                "data-original-title" => "Enter your cellphone or landline number. (eg. 09165445809 or 6583792)"
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <label class="form-control-label">Opening Time</label>
                                                <div class="form-group">
                                                    {!! Form::text('open_time', old('open_time') ?? $restaurant['open_time'], 
                                                    [
                                                        'class' => 'form-control form-control-alternative',
                                                        'tab_index' => '3',
                                                        'id' => 'open_time',
                                                        'required' => true,
                                                        "data-toggle" => "tooltip",
                                                        "data-placement" => "top",
                                                        "data-container" => "body",
                                                        "data-original-title" => "Opening time of your restaurant"
                                                    ]) !!}
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="form-control-label">Closing Time</label>
                                                <div class="form-group">
                                                    {!! Form::text('close_time', old('close_time') ?? $restaurant['close_time'], 
                                                    [
                                                        'class' => 'form-control form-control-alternative',
                                                        'tab_index' => '3',
                                                        'id' => 'close_time',
                                                        'required' => true,
                                                        "data-toggle" => "tooltip",
                                                        "data-placement" => "top",
                                                        "data-container" => "body",
                                                        "data-original-title" => "Closing time of your restaurant"
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                    </div>
                                    <div class="col-lg-6" data-toggle="tooltip" data-placement="bottom" title="" data-container="body" data-original-title="Turn this on if your restaurant is open 24hours">
                                        <div class="d-flex">
                                        <label class="custom-toggle">
                                            <input type="checkbox" name="24hours" id="24hours">
                                            <span class="custom-toggle-slider rounded-circle"></span>
                                            <p></p>
                                        </label>
                                        <h4 class="pl-2 text-default" id="open_24">Restaurant open for 24 Hours</h6>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-4">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label">First Name</label>
                                            {!! Form::text('fname', $restaurant['owner_fname'], [
                                                "class" => "form-control form-control-alternative",
                                                "disabled" => true 
                                            ]) !!}   
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label">Last Name</label>
                                            {!! Form::text('lname', $restaurant['owner_lname'], [
                                                "class" => "form-control form-control-alternative",
                                                "disabled" => true 
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
                    <div class="card-body">
                        @if (session()->has('change_email_error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            @foreach (session()->get('change_email_error') as $error)
                                @foreach ($error as $err)
                                <li>{{ $err }}</li>
                                @endforeach
                            @endforeach
                            <button type="button" class="close mt-1" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
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
                        {!! Form::open(['route' => 'owner.user.update', 'id' => 'change_password']) !!}
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group focused mb-0">
                                    <label class="form-control-label" for="input-username">Email Address</label>
                                    {!! Form::email('email', $user['email'], [
                                        "class" => "form-control form-control-alternative",
                                        "disabled" => true,
                                        "required" => true
                                    ]) !!}
                                </div>
                                <p class="pt-2 m-0 pb-3">
                                    <a class="heading-small font-weight-bold" data-toggle="modal" data-target="#change_email" href="#"> 
                                        Request Change Email
                                    </a>
                                </p>
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
                                    <input type="password" 
                                    class="form-control form-control-alternative" name="user_password" 
                                    id="user_password" required
                                    data-toggle="tooltip" data-placement="left" title="" data-container="body" 
                                    data-original-title="Password must contain atleast 1 digit, lower case letter, uppercase letter and symbol, It must be between 8-21 characters in length.">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <label class="form-control-label">Confirm Password</label>
                                <div class="form-group focused">
                                    <input type="password" class="form-control form-control-alternative" 
                                    name="user_password1" 
                                    required
                                    data-toggle="tooltip" data-placement="left" title="" data-container="body" 
                                    data-original-title="Retype your new password here">
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
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<div class="modal fade" id="change_email" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Change Email Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
            </div>
            <div class="modal-body bg-secondary">
                {!! Form::open(['route' => 'changerequest.store', 'id' => 'change_email_form']) !!}
                    <label class="form-control-label">New Email Address</label>
                    <div class="form-group mb-3">
                        <input id="request_new_email" class="form-control form-control-alternative" placeholder="Enter your new email address here" name="request_new_email" type="text" required>
                    </div>
                    <label class="form-control-label">Reason for changing email address</label>
                    <div class="form-group mb-3">
                        <textarea class="form-control form-control-alternative" placeholder="Enter your reason here" rows="7"
                        name="request_reason" id="request_reason" cols="50" required></textarea>
                    </div>
            </div>
            <div class="modal-footer mx-auto">
                <button type="submit" class="btn btn-primary">Submit Request</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
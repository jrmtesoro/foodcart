@extends('layouts.admin')
{{-- Page Title --}}
@section('page-title', 'Admin Dashboard')

{{-- Page Name --}}
@section('page-name', 'User / Add Admin')

{{-- Custom CSS --}}
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/selectize/selectize.css') }}">
<link href="{{ asset('vendor/editable-select/jquery-editable-select.min.css') }}" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{ asset('css/owner/menu.css') }}">
@endsection

@section('js')
<script type="text/javascript" src="{{ asset('vendor/selectize/selectize.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/editable-select/jquery-editable-select.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script>
$('#store_menu_form').validate({
    ignore: '.ignore',
    rules: {
        reg_fname: "required",
        reg_lname: "required",
        reg_contact_number: "required",
        reg_email: "required",
        reg_address: "required",

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
        reg_address: {
            required: true
        }
    },
    messages: {
        reg_fname: "Please enter your First Name",
        reg_lname: "Please enter your Last Name",
        reg_contact_number: "Please enter your Contact Number",
        reg_email: "Please enter your Email Address",
        reg_address: "Please enter your address",
        reg_fname: {
            required: "Please enter your First Name"
        },
        reg_lname: {
            required: "Please enter your Last Name"
        },
        reg_address: {
            required: "Please enter your address"
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
        @if ($errors->has('reg_email'))
        $('#reg_email').removeClass('is-valid');
        $('#reg_email').addClass('is-invalid');
        @endif
        parent.addClass('has-success').removeClass('has-danger');
        $(element).addClass("is-valid").removeClass("is-invalid");
    }
});

jQuery.validator.addMethod("notEqual", function (value, element, param) {
    return this.optional(element) || value != '0';
});
jQuery.validator.addMethod("validMobile", function(value, element) {
    return this.optional(element) || /^[0][9][1-9]\d{8}$|^[1-9]\d{6}$/.test(value);
},"Invalid mobile contact number");

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
        {!! Form::open(['route' => 'admin.user.store', 'id' => 'store_menu_form', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
        @csrf
        <div class="container-fluid mt--9 bg-secondary">
            <div class="row">
                <div class="col">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                <h3 class="mb-0">Add Admin Form</h3>
                                </div>
                                <div class="col-4 text-right">
                                <a class="btn btn-primary" href="{{route('admin.user.index')}}"><span class='fas fa-arrow-left mr-1'></span> Go Back</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="heading-small text-muted mb-4"><span class="text-danger">*</span> fields are required</h6>
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label my-auto">Email Address <span class="text-danger">*</span></label>
                                <div class="col-sm-10 col-md-6 col-lg-5">
                                    {!! Form::text('reg_email', old('reg_email') ?? '',
                                    [
                                        'class' => 'form-control form-control-alternative ' . ($errors->has('reg_email') ? 'is-invalid' : ''),
                                        'placeholder' => 'Enter your email address here',
                                        'tab_index' => '2',
                                        'id' => 'reg_email'
                                    ]) !!}
                                    @if ($errors->has('reg_email'))
                                    <div id="reg_email-error-temp" class="error invalid-feedback">{{$errors->first('reg_email')}}</div>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label my-auto">First Name <span class="text-danger">*</span></label>
                                <div class="col-sm-10 col-md-6 col-lg-5">
                                    {!! Form::text('reg_fname', old('reg_fname') ?? '',
                                    [
                                        'class' => 'form-control form-control-alternative ' . ($errors->has('reg_fname') ? 'is-invalid' : ''),
                                        'placeholder' => 'Enter your first name here',
                                        'tab_index' => '1'
                                    ]) !!}
                                    @if ($errors->has('reg_fname'))
                                    <div id="reg_fname-error-temp" class="error invalid-feedback">{{$errors->first('reg_fname')}}</div>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label my-auto">Last Name <span class="text-danger">*</span></label>
                                <div class="col-sm-10 col-md-6 col-lg-5">
                                    {!! Form::text('reg_lname', old('reg_lname') ?? '',
                                    [
                                        'class' => 'form-control form-control-alternative ' . ($errors->has('reg_lname') ? 'is-invalid' : ''),
                                        'placeholder' => 'Enter your last name here',
                                        'tab_index' => '2'
                                    ]) !!}
                                    @if ($errors->has('reg_lname'))
                                    <div id="reg_lname-error-temp" class="error invalid-feedback">{{$errors->first('reg_lname')}}</div>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label my-auto">Contact Number <span class="text-danger">*</span></label>
                                <div class="col-sm-9 col-md-5 col-lg-4">
                                    {!! Form::text('reg_contact_number', old('reg_contact_number') ?? '',
                                    [
                                        'class' => 'form-control form-control-alternative ' . ($errors->has('reg_contact_number') ? 'is-invalid' : ''),
                                        'placeholder' => 'Enter your contact number here',
                                        'tab_index' => '2'
                                    ]) !!}
                                    @if ($errors->has('reg_contact_number'))
                                    <div id="reg_contact_number-error-temp" class="error invalid-feedback">{{$errors->first('reg_contact_number')}}</div>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label my-auto">Address <span class="text-danger">*</span></label>
                                <div class="col-sm-10 col-md-6 col-lg-5">
                                    {!! Form::textarea('reg_address', old('reg_address') ?? '', 
                                    [
                                        'class' => 'form-control form-control-alternative',
                                        'placeholder' => 'Enter your address here',
                                        'rows' => '4',
                                        'tab_index' => '2'
                                    ]) !!}
                                    @if ($errors->has('reg_address'))
                                    <div id="reg_address-error-temp" class="error invalid-feedback">{{$errors->first('reg_address')}}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-6 text-right">
                                    <button type="submit" class="btn btn-info">Submit</button>
                                </div>
                                <div class="col-6 text-left">
                                    <button type="reset" class="btn btn-danger">Reset</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('admin.inc.footer')
        </div>
        {!! Form::close() !!}
    </div>
@endsection
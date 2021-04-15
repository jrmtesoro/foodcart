@extends('layouts.guest')
@section('page-title', 'Pinoy Food Cart')
@section('css')
@endsection

@section('js')
<script type="text/javascript" src="{{ asset('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('#contact_us_form').validate({
        rules: {
            contact_name: "required",
            contact_email: "required",
            contact_message: "required",

            contact_name: {
                required: true
            },
            contact_email: {
                required: true,
                email: true
            },
            contact_message: {
                required: true
            }
        },
        messages: {
            contact_name: "Please enter your Name",
            contact_email: "Please enter your Email Address",
            contact_message: "Please enter your Message",

            contact_name: {
                required: "Please enter your Name"
            },
            contact_email: {
                required: "Please enter your Email Address",
                email: "Please enter a valid Email Address",
            },
            contact_message: {
                required: "Please enter your Message"
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
});

</script>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto pt-5">
            <div class="card bg-light">
                <div class="card-header card-header-text card-header-warning" style="background: #c40514 !important;">
                    <div class="card-text">
                        <h4 class="card-title pt-1 m-0">Contact Us</h4>
                    </div>
                </div>
                <div class="card-body border-bottom">
                    @if (session()->has('errors'))
                    <div class="alert alert-danger">
                        <div class="container">
                            <div class="alert-icon">
                                <i class="material-icons">error_outline</i>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="material-icons">clear</i></span>
                            </button>
                            <b>Invalid Input<b>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    {!! Form::open(['route' => 'contact_us.store', 'id' => 'contact_us_form']) !!}
                    <div class="row px-5 py-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Name</label>
                                {!! Form::text('contact_name', old('contact_name') ?? '', [
                                "class" => "form-control",
                                "required" => true
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Email Address</label>
                                {!! Form::email('contact_email', old('contact_email') ?? '', [
                                "class" => "form-control",
                                "required" => true
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Message</label>
                                {!! Form::textarea('contact_message', old('contact_message') ?? '', [
                                "class" => "form-control",
                                "required" => true,
                                "rows" => 5
                                ]) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer justify-content-center bg-light pt-3">
                    <button type="submit" class="btn btn-warning">Send Message</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
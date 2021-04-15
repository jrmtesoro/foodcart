@extends('layouts.guest') 
@section('page-title', 'Pinoy Food Cart') 
@section('css')
@endsection
 
@section('js')
<script type="text/javascript" src="{{ asset('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
<script>
$('#change_form').validate({

    rules : {!! json_encode($validation_rules, true) !!}
    ,
    message : {!! json_encode($validation_message, true) !!}
    ,
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
        var logo = '<span class="form-control-feedback"><i class="material-icons" id="'+id+'-invalid">clear</i></span>';
        if (!$('#'+id+'-invalid').length) {
            $(logo).insertAfter(element);
        }
    },
    unhighlight: function(element, errorClass, validClass) {
        var $parent = $(element).parent('.form-group');
        $parent.addClass('has-success').removeClass('has-danger');

        var id = $(element).attr('name');
        $('#'+id+'-invalid').remove();
        var logo = '<span class="form-control-feedback"><i class="material-icons" id="'+id+'-valid">done</i></span>';
        if (!$('#'+id+'-valid').length) {
            $(logo).insertAfter(element);
        }
    }
        
});
@foreach ($data as $restaurant)
$.validator.addMethod('{{$restaurant["slug"]}}', function(value, element, param) {
    return this.optional(element) || (parseInt(value) >= {{ $restaurant['total'] }} && parseInt(value) != 0);
}, "Insufficient Money");
@endforeach
</script>
@endsection


@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <h3 class="title">Your Bag</h3>
                            <hr>
                        </div>
                    </div>
                    @if (session()->has('errors'))
                    <div class="alert alert-danger">
                        <div class="container">
                            <div class="alert-icon">
                                <i class="material-icons">error_outline</i>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="material-icons">clear</i></span>
                            </button>
                            <b>Invalid Input</b>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    {!! Form::open(['route' => 'order.store', 'method' => 'post', 'id' => 'change_form']) !!}
                    @foreach ($data as $restaurant)
                    <div class='row border-bottom'>
                        <div class='col-12'>
                            <div class='d-flex'>
                                <a class='h5 title my-0 mr-auto text-info' href="{{ route('guest.restaurant', ['slug' => $restaurant['slug']]) }}">{{ $restaurant['name'] }}</a>
                                <p class='h5 title my-0'><span class="fas fa-phone mr-1"></span> {{ $restaurant['contact_number'] }}</p>
                            </div>
                        </div>
                        <div class='col-12'>
                            <div class='d-flex'>
                                <p class='h5 title mr-auto'>Delivery Time : {{ $restaurant['eta'] }}mins</p>
                                <p class="h5 title">Flat Rate : ₱ {{ $restaurant['flat_rate'] }}.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="row pt-4">
                        @foreach ($restaurant['menu'] as $menu) @php ($image = !empty($menu['image_name']) ? route('photo.menu', ['slug' => $menu['image_name']]).'?size=thumbnail' : asset('img/alt.png'))
                        <div class="col-lg-6">
                            <div class='row'>
                                <div class='col-auto'>
                                    <img src='{{ $image }}' width='100' height='100'>
                                </div>
                                <div class='col'>
                                    <div class='d-flex justify-content-between'>
                                        <p class='h6'>{{ $menu['name'] }}</p>
                                        <p class='h6'>Quantity: x{{ $menu['quantity'] }}</p>
                                    </div>
                                    <div class='d-flex justify-content-between'>
                                        <p class='h6'>Cooking Time : {{ $menu['cooking_time'] }} mins.</p>
                                    </div>
                                    <div class='d-flex justify-content-between'>
                                        <div class='mr-auto'>
                                            <button type='button' class='btn btn-warning btn-sm' data-toggle='modal' data-id='{{ $menu['id'] }}' data-quantity='{{ $menu['quantity'] }}' data-target='#editQuantity'>Edit</button>
                                            <button type='button' class='btn btn-danger btn-sm delete-btn' onclick='deleteBtn(this)' data-id='{{ $menu['id'] }}'>Delete</button>
                                        </div>
                                        <p class='h6'>Price : ₱{{ $menu['price'] }}.00</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <hr>
                    <div class="d-flex">
                        <p class="h5 title mr-auto my-0">Estimated Time : {{ $restaurant['sub_eta'] }}mins</p>
                        <p class="h5 title my-0">Total : ₱ {{ $restaurant['total'] }}.00</p>
                    </div>
                    <div class="row">
                        <div class="ml-auto col-2">
                            <div class="form-group bmd-form-group ml-auto mb-0">
                                <label class="bmd-label-floating">Change for</label>
                                <input type="text" class="form-control" name="{{ $restaurant['slug'] }}" style="width: 100px;" value="{{ old('reg_restaurant_name') ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <hr>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h3 class="title">Billing Summary</h3>
                    <hr>
                    <div class='d-flex justify-content-between font-weight-bold'>
                        <p class="h4 title mr-auto">Estimated Time</p>
                        <p class="h4 title">{{ $total_cooking_time }} mins.</p>
                    </div>
                    <div class='d-flex justify-content-between'>
                        <p class="h4 title mr-auto">Total Flat Rate</p>
                        <p class="h4 title">₱ {{ $total_flat_rate }}.00</p>
                    </div>
                    <div class='d-flex justify-content-between'>
                        <p class="h4 title mr-auto">Grand Total</p>
                        <p class="h4 title">₱ {{ $grand_total }}.00</p>
                    </div>
                    <hr>
                    <button class="btn btn-block btn-info" type="submit">Confirm</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
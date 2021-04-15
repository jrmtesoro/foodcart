@extends('layouts.guest') 
@section('page-title', 'Pinoy Food Cart') 

@section('css')
<style>
</style>
@endsection
 
@section('js')
<script>
$('#cancel_suborder').on('show.bs.modal', function (e) {
    var sub_order_id = e.relatedTarget.dataset.suborder;

    $('#sub_order_id').val(sub_order_id);
});
$('#cancel_order').on('show.bs.modal', function (e) {
    var order_code = e.relatedTarget.dataset.order;

    $('#order_code').val(order_code);
});
</script>
@endsection
 
@section('content')
<div class="container">
    <div class="row">
        <div class="offset-2 col-lg-8">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex">
                            <a href="{{ route('order.index') }}" class="title h4 btn btn-info mr-auto">Go Back</a>
                            @if ($order_details['status'] == 0)
                            <button data-toggle="modal" data-target="#cancel_order" data-order='{{$order_details['code']}}' class="title h4 btn btn-danger">Cancel All Orders</button>
                            @endif
                            </div>
                            <hr>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 border-bottom">
                                <p class="h4 title">
                                Order # <u>{{ $order_details['code'] }}</u>
                                </p>
                                @if ($order_details['status'] == 0)
                                <p class="h6 title my-0">Status : <span class="text-primary">Pending</span></p>
                                @elseif ($order_details['status'] == 1)
                                <p class="h6 title my-0">Status : <span class="text-warning">Processing</span></p>
                                @elseif ($order_details['status'] == 2)
                                <p class="h6 title my-0">Status : <span class="text-info">Delivering</span></p>
                                @elseif ($order_details['status'] == 3)
                                <p class="h6 title my-0">Status : <span class="text-success">Completed</span></p>
                                @elseif ($order_details['status'] == 4)
                                <p class="h6 title my-0">Status : <span class="text-danger">Rejected</span></p>
                                @elseif ($order_details['status'] == 5)
                                <p class="h6 title my-0">Status : <span class="text-danger">Cancelled</span></p>
                                @endif
                            </div>
                            <div class="col-lg-12">
                                <p class="h4 title">
                                    {{ $order_details['date'] }}
                                </p>
                            </div>
                            @foreach ($order_details['suborder'] as $suborder) 
                                <div class="col-lg-12">
                                    <div class="card my-2">
                                        <div class="card-body py-0">   
                                            <div class="card-title d-flex mb-0">
                                                <a class="h5 title text-info mr-auto my-0" href="{{ route('guest.restaurant', ['slug' => $suborder['restaurant']['slug']]) }}">{{ $suborder['restaurant']['name'] }}</a>
                                                @if ($suborder['status'] == 0)
                                                <p class="h5 title my-0">Status : <span class="text-primary">Pending</span></p>
                                                @elseif ($suborder['status'] == 1)
                                                <p class="h5 title my-0">Status : <span class="text-warning">Processing</span></p>
                                                @elseif ($suborder['status'] == 2)
                                                <p class="h5 title my-0">Status : <span class="text-info">Delivering</span></p>
                                                @elseif ($suborder['status'] == 3)
                                                <p class="h5 title my-0">Status : <span class="text-success">Completed</span></p>
                                                @elseif ($suborder['status'] == 4)
                                                <p class="h5 title my-0">Status : <span class="text-danger">Rejected</span></p>
                                                @elseif ($suborder['status'] == 5)
                                                <p class="h5 title my-0">Status : <span class="text-danger">Cancelled</span></p>
                                                @endif
                                            </div>
                                            <div class="d-flex">
                                                <p class='h6 title mr-auto mb-0'><span class="fas fa-phone mr-1"></span> {{ $suborder['restaurant']['contact_number'] }}</p>
                                                @if ($suborder['status'] == 0 && count($order_details['suborder']) != 1)
                                                <button class='btn btn-danger btn-sm my-auto' data-toggle="modal" data-target="#cancel_suborder" data-suborder='{{$suborder['id']}}'>Cancel Order</button>
                                                @endif
                                            </div>
                                            <div class="d-flex pt-3 border-top">
                                                <p class="h6 title my-0 mr-auto">Delivery Time : {{ $suborder['restaurant']['eta'] }}mins</p>
                                                <p class="h6 title my-0">Flat Rate : ₱{{ $suborder['restaurant']['flat_rate'] }}.00</p>
                                            </div>
                                            <div class="row">
                                                @foreach ($suborder['itemlist'] as $menu)
                                                <div class="col-lg-12">
                                                    <div class="card my-1">
                                                        <div class="card-body pb-0">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="d-flex">
                                                                        <p class="h6 my-0 title mr-auto">{{ $menu['name'] }}</p>
                                                                        <p class="h6 my-0 title">QTY: x{{ $menu['quantity'] }}</p>
                                                                    </div>
                                                                    <div class="d-flex">
                                                                        <p class="h6 my-0 title mr-auto"> {{ $menu['cooking_time'] }} mins.</p>
                                                                        <p class="h6 my-0 title">Price : ₱ {{ $menu['price'] }}.00</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach 
                                            </div>
                                            <div class="d-flex">
                                                <p class="h6 mb-0 title mr-auto">Estimated Time : {{ $suborder['cooking_time_total'] }}mins</p>
                                                <p class="h6 mb-0 title">Total Price : ₱ {{ $suborder['total']+$suborder['restaurant']['flat_rate'] }}.00</p>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <p class="h6 my-0 title ml-auto">Payment : ₱ {{ $suborder['payment'] }}.00</p>
                                                <p class="h6 my-0 title ml-auto">Change : ₱ {{ $suborder['payment']-($suborder['total']+$suborder['restaurant']['flat_rate']) }}.00</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-lg-12">
                                <hr>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="d-flex justify-content-between">
                                            <p class="h5 my-1 title">Total Cooking Time : </p>
                                            <p class="h5 my-1 title">{{ $order_details['cooking_time_total'] }} mins.</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="d-flex justify-content-between">
                                            <p class="h5 my-1 title">Grand Total : </p>
                                            <p class="h5 my-1 title">₱ {{ $order_details['total'] }}.00</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

<div class="modal fade" id="cancel_suborder" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <i class="material-icons">clear</i>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this order?
                </p>
            </div>
            <div class="modal-footer">
                {!! Form::open(['route' => 'guest.suborder.cancel', 'method' => 'post', 'class' => 'mb-0']) !!}
                <input type="hidden" value="" name="sub_order_id" id="sub_order_id">
                <button type="submit" class="btn btn-danger">Yes</button>
                {!! Form::close() !!}
                <button type="button" class="btn btn-success" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cancel_order" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <i class="material-icons">clear</i>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this order?
                </p>
            </div>
            <div class="modal-footer">
                {!! Form::open(['route' => 'guest.order.cancel', 'method' => 'post', 'class' => 'mb-0']) !!}
                <input type="hidden" value="" name="order_code" id="order_code">
                <button type="submit" class="btn btn-danger">Yes</button>
                {!! Form::close() !!}
                <button type="button" class="btn btn-success" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>
@endsection
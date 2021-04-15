@extends('layouts.admin')

{{-- Page Title --}}
@section('page-title', 'Admin Dashboard')

{{-- Page Name --}}
@section('page-name', 'Order / View Order')

{{-- Custom CSS --}}
@section('css')
@endsection

@section('js')
@endsection

@section('content')
@include('admin.inc.sidebar')
<div class="main-content">
    @include('admin.inc.navbar')
    <div class="header bg-gradient-info pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
        </div>
    </div>
    <div class="container-fluid mt--9 bg-secondary">
        <div class="row">
            <div class="col-12">
                <div class="card shadow bg-secondary">
                    <div class="card-header">
                        <div class="d-flex">
                            <h3 class="mr-auto my-auto">Order Information</h3>
                            <a class="btn btn-primary" href="{{ route('admin.order.index.web') }}"><span class='fas fa-arrow-left mr-1'></span> Go Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <h6 class="heading-small text-muted mb-4">Order Details</h6>
                            </div>
                            <div class="col-lg-3 pl-5">
                                <label class="form-control-label text-muted">Order #</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $order['code'] }}</p>
                            </div>
                            <div class="col-lg-3 pl-5">
                                <label class="form-control-label text-muted">Date Received</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $order['date'] }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <h6 class="heading-small text-muted mb-4">Sub Order Details</h6>
                            </div>
                            @foreach ($order['suborder'] as $suborder)
                            <div class="offset-1 col-lg-10">
                                <div class="card my-1 shadow">
                                    <div class="card-body">
                                        <div class="card-title border-bottom">
                                            @php($route = route('admin.restaurant.show', ['restaurant_id' => $suborder['restaurant']['id']]))
                                            <a class="h3 text-info" href="{{ $route }}">{{ $suborder['restaurant']['name'] }}</a>
                                        </div>
                                        <div class="d-flex">
                                            <h4 class="my-0 mr-auto">Delivery Time : {{ $suborder['restaurant']['eta'] }} mins</h4>
                                            <h4 class="my-0">Flat Rate : ₱{{ $suborder['restaurant']['flat_rate'] }}.00</h4>
                                        </div>
                                        <div class="row pt-3">
                                            @foreach ($suborder['itemlist'] as $menu)
                                            <div class="col-lg-6">
                                                <div class="card shadow">
                                                    <div class="card-body">
                                                        <div class="col-12">
                                                            <div class="d-flex">
                                                                <h5 class="mr-auto">{{ $menu['name'] }}</h5>
                                                                <h5>QTY: x{{ $menu['quantity'] }}</h5>
                                                            </div>
                                                            <div class="d-flex">
                                                                <h5 class="mr-auto">{{ $menu['cooking_time'] }} mins.</h5>
                                                                <h5>Price : ₱ {{ $menu['price'] }}.00</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="d-flex pt-3">
                                            <h4 class="mr-auto">Estimated Time : {{ $suborder['cooking_time_total'] }} mins</h4>
                                            <h4>Total Price : ₱ {{ $suborder['total']+$suborder['restaurant']['flat_rate'] }}.00</h4>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <h4 class="ml-auto">Payment : ₱ {{ $suborder['payment'] }}.00</h4>
                                            <h4 class="ml-auto">Change : ₱ {{ $suborder['payment']-($suborder['total']+$suborder['restaurant']['flat_rate']) }}.00</h4>
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
                                            <h4>Total Cooking Time : </p>
                                            <h4>{{ $order['cooking_time_total'] }} mins.</h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="d-flex justify-content-between">
                                            <h4>Grand Total : </p>
                                            <h4>₱ {{ $order['total'] }}.00</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.inc.footer')
    </div>
</div>
@endsection
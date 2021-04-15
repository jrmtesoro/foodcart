@extends('layouts.admin') {{-- Page Title --}} 
@section('page-title', 'Admin Dashboard') {{-- Page Name --}} 
@section('page-name', 'Restaurant / View Order') {{-- Custom CSS --}} 
@section('css')
<link rel="stylesheet" href="{{ URL::asset('vendor/flipclock/flipclock.css') }}">
<style>

textarea {
    resize: none;
}

</style>
@endsection
 {{-- Custom JS --}} 
@section('js')
@if (session()->has('errors'))
<script>
$('#report_modal').modal('show');
</script>
@endif
@endsection
 {{--
Content --}} 
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
                <div class="card shadow">
                    <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col-5">
                                <h3 class="mb-0">Order Information</h3>
                            </div>
                            <div class="col-7 text-right">
                                <a class="btn btn-primary" href="{{ route('admin.restaurant.show', ["restaurant_id" => $order['restaurant_id']]) }}">
                                <span class='fas fa-arrow-left mr-1'></span> Go Back</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-5 my-auto">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <label class="form-control-label text-muted">Status</label>
                                    </div>
                                    <div class="col-lg-7">
                                        @if ($order['status'] == 0)
                                        <p class="font-weight-bold text-warning">Pending</p>
                                        @elseif ($order['status'] == 1)
                                        <p class="font-weight-bold text-primary">Processing</p>
                                        @elseif ($order['status'] == 2)
                                        <p class="font-weight-bold text-info">Delivering</p>
                                        @elseif ($order['status'] == 3)
                                        <p class="font-weight-bold text-success">Completed</p>
                                        @elseif ($order['status'] == 4)
                                        <p class="font-weight-bold text-danger">Rejected</p>
                                        @else
                                        <p class="font-weight-bold text-danger">Cancelled</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <label class="form-control-label text-muted">Order #</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <p class="font-weight-bold">{{ $order['order']['code'] }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <label class="form-control-label text-muted">Date Received</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <p class="font-weight-bold">{{ $order['date_created'] }}</p>
                                    </div>
                                </div>
                                @if ($order['status'] != 0)
                                <div class="row">
                                    <div class="col-sm-5">
                                        <label class="form-control-label text-muted">Delivered Before</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <p class="font-weight-bold">{{ $order['date_expire'] }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-7">
                                <label class="form-control-label text-muted">Order Information</label>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Food Name</th>
                                            <th scope="col">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order['itemlist'] as $menu)
                                        <tr>
                                            <th scope="row">{{ $menu['name'] }}</th>
                                            <td>{{ $menu['quantity'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="row pt-4">
                            <div class="col-lg-5">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <label class="form-control-label text-muted">Delivered To</label>
                                    </div>
                                    <div class="col-lg-7 text-center">
                                        <p class="font-weight-bold"><a href="{{ route('customer.show', ["customer_id" => $order['customer']['id']]) }}">{{ $order['customer']['fname'].' '.$order['customer']['lname'] }}</a></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <label class="form-control-label text-muted">Address</label>
                                    </div>
                                    <div class="col-lg-7 text-center">
                                        <p class="font-weight-bold">{{ $order['customer']['address'] }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <label class="form-control-label text-muted">Contact Number</label>
                                    </div>
                                    <div class="col-lg-7 text-center">
                                        <p class="font-weight-bold">{{ $order['customer']['contact_number'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="offset-2 col-lg-5 text-right">
                                <div class="row">
                                        <div class="col-sm-5">
                                            <label class="form-control-label text-muted">Grand Total</label>
                                        </div>
                                        <div class="col-lg-7">
                                            <p class="font-weight-bold text-center">₱ {{ $order['total'] }}.00</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <label class="form-control-label text-muted">Total Cooking Time</label>
                                        </div>
                                        <div class="col-lg-7">
                                            <p class="font-weight-bold text-center">{{ $order['cooking_time_total'] }} mins</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <label class="form-control-label text-muted">Payment</label>
                                        </div>
                                        <div class="col-lg-7">
                                            <p class="font-weight-bold text-center">₱ {{ $order['payment'] }}.00</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <label class="form-control-label text-muted">Change</label>
                                        </div>
                                        <div class="col-lg-7">
                                            <p class="font-weight-bold text-center">₱ {{ $order['payment']-$order['total'] }}.00</p>
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
</div>
@endsection
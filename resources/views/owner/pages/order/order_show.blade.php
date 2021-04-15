@extends('layouts.owner') {{-- Page Title --}} 
@section('page-title', 'Owner Dashboard') {{-- Page Name --}} 
@section('page-name', 'Order / View Order') {{-- Custom CSS --}} 
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
@include('owner.inc.sidebar')
<div class="main-content">
    @include('owner.inc.navbar')
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
                                @if ($order['status'] == 0)
                                <button class="btn btn-outline-success btn-sm" data-toggle="modal" 
                                data-target="#accept_confirmation"><span class='fas fa-check mr-1'></span> Accept</button>
                                <button class="btn btn-outline-danger btn-sm" data-toggle="modal" 
                                data-target="#reject_confirmation"><span class='fas fa-times mr-1'></span> Reject</button>
                                @elseif ($order['status'] == 1)
                                <button class="btn btn-outline-success btn-sm" data-toggle="modal" 
                                data-target="#deliver_confirmation"><span class='fas fa-check mr-1'></span> Ready to Deliver</button>
                                @elseif ($order['status'] == 2)
                                <button class="btn btn-outline-success btn-sm" data-toggle="modal" 
                                data-target="#complete_confirmation"><span class='fas fa-check mr-1'></span> Complete Order</button>
                                @endif
                                <a class="btn btn-primary" href="{{route('owner.order.index')}}"><span class='fas fa-arrow-left mr-1'></span> Go Back</a>
                                @if (!$order['customer']['banned'])
                                <a class="btn btn-danger text-white" data-toggle="modal" 
                                data-target="#report_modal"><span class="fas fa-exclamation-circle mr-1"></span> Report</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if ($order['customer']['reports'] != 0)
                            <div class="offset-2 col-lg-8">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <span class="alert-inner--icon"><i class="fa fas-exclamation-triangle"></i></span>
                                    <span class="alert-inner--text"><strong>Warning!</strong> This customer has been reported <strong><u>{{ $order['customer']['reports'] }}</u></strong> time(s)</span>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                            @endif
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
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <label class="form-control-label text-muted">Delivered To</label>
                                    </div>
                                    <div class="col-lg-7 text-center">
                                        <p class="font-weight-bold">{{ ucfirst(strtolower($order['customer']['fname'])).' '.ucfirst(strtolower($order['customer']['lname'])) }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <label class="form-control-label text-muted">Address</label>
                                    </div>
                                    <div class="col-lg-7 text-center">
                                        <p class="font-weight-bold">{{ ucfirst($order['customer']['address']) }}</p>
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
                                        <label class="form-control-label text-muted">Flat Rate</label>
                                    </div>
                                    <div class="col-lg-7">
                                        <p class="font-weight-bold text-center">₱ {{ $order['flat_rate'] }}.00</p>
                                    </div>
                                </div>
                                <div class="row">
                                        <div class="col-sm-5">
                                            <label class="form-control-label text-muted">Grand Total</label>
                                        </div>
                                        <div class="col-lg-7">
                                            <p class="font-weight-bold text-center">₱ {{ $order['total']+$order['flat_rate'] }}.00</p>
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
                                            <p class="font-weight-bold text-center">₱ {{ $order['payment']-($order['total']+$order['flat_rate'])}}.00</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('owner.inc.footer')
        </div>
    </div>
</div>
<div class="modal fade" id="accept_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Accept Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to accept this order?
            </div>
            <div class="modal-footer">
                <a href="{{ route('owner.order.accept', ['code' => $order['id']]) }}" class="btn btn-primary">Yes</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reject_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to reject this order?
            </div>
            <div class="modal-footer">
                <a href="{{ route('owner.order.reject', ['code' => $order['id']]) }}" class="btn btn-primary">Yes</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deliver_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Deliver Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to deliver this order?
            </div>
            <div class="modal-footer">
                <a href="{{ route('owner.order.deliver', ['code' => $order['id']]) }}" class="btn btn-primary">Yes</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="complete_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to complete this order?
            </div>
            <div class="modal-footer">
                <a href="{{ route('owner.order.complete', ['code' => $order['id']]) }}" class="btn btn-primary">Yes</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cancel_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel this order?
            </div>
            <div class="modal-footer">
                <a href="{{ route('owner.order.cancel', ['code' => $order['id']]) }}" class="btn btn-primary">Yes</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="report_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Report Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'post', 'route' => 'report.store', 'enctype' => 'multipart/form-data']) !!}
            <div class="modal-body bg-light">
                <div class="d-flex flex-column">
                    <p class="h3">Why do you want to report this customer?</p>
                    <div class="form-group my-auto">
                        {!! Form::textarea('report_reason', old('report_reason') ?? '', 
                        [
                            'class' => 'form-control form-control-alternative',
                            'placeholder' => 'Enter your reason here',
                            'rows' => '7',
                            'required' => true
                        ]) !!}
                    </div>
                    <input type="hidden" name="customer_id" value="{{ $order['customer']['id'] }}">
                    <input type="hidden" name="sub_order_id" value="{{ $order['id'] }}">
                    <p class="h3 pt-3">Additional proof</p>
                    <input type="file" name="report_proof1" class="form-control-file" accept=".png, .jpeg, .jpg"/>
                    <input type="file" name="report_proof2" class="form-control-file pt-1" accept=".png, .jpeg, .jpg"/>
                    <input type="file" name="report_proof3" class="form-control-file pt-1" accept=".png, .jpeg, .jpg"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Report</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
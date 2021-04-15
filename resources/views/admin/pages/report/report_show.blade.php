@extends('layouts.admin')

{{-- Page Title --}}
@section('page-title', 'Admin Dashboard')

{{-- Page Name --}}
@section('page-name', 'Report / View Report')

{{-- Custom CSS --}}
@section('css')
<link rel="stylesheet" href="{{ URL::asset('vendor/fancybox/dist/fancybox.css') }}">
@endsection

@section('js')
<script src="{{ asset('vendor/fancybox/dist/jquery.fancybox.min.js') }}"></script>
<script>
var checked = false;
$('#report_ban').on('change', function() {
    var $reason_container = $('#reason');
    var $reason = $('textarea[name=ban_reason]');

    if (!checked) {
        $reason_container.removeClass('d-none')
        $reason.attr('required', 'required');
        checked = true;
    } else {
        $reason_container.addClass('d-none')
        $reason.removeAttr('required');
        checked = false;
    }
});
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
    <div class="container-fluid mt--9 bg-secondary">
        <div class="row">
            <div class="col-12">
                <div class="card shadow bg-secondary">
                    <div class="card-header">
                        <div class="d-flex">
                            <h3 class="mr-auto my-auto">Report Information</h3>
                            @if ($report['status'] == 0)
                            <button class="btn btn-warning" data-toggle="modal" 
                            data-target="#investigate_confirmation">Investigate Report</button>
                            @endif
                            @if (in_array($report['status'], [0, 1]))
                            <button class="btn btn-danger" data-toggle="modal" 
                            data-target="#close_confirmation">Close Report</button>
                            @endif
                            <a class="btn btn-primary" href="{{ route('admin.report.index') }}"><span class='fas fa-arrow-left mr-1'></span> Go Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <h6 class="heading-small text-muted mb-4">Ticket information</h6>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Ticket #</label>
                            </div>
                            
                            <div class="col-lg-8 pl-5">
                                <p>{{ $report['code'] }}</p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Reported By</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <a href="{{ route('admin.restaurant.show', ['restaurant_id' => $report['restaurant']['id']]) }}">{{ $report['restaurant']['name'] }}</a>
                                <p></p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Date Submitted</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $report['date'] }}</p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Status</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                @if ($report['status'] == 0)
                                <p class="heading text-success">Open</p>
                                @elseif ($report['status'] == 1)
                                <p class="heading text-info">Under Investigation</p>
                                @else
                                <p class="heading text-danger">Closed</p>
                                @endif
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Reason</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $report['reason'] }}</p>
                            </div>
                            @if (!empty($report['proof1']) || !empty($report['proof2']) || !empty($report['proof3']))
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Image Proof</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <div class="d-flex">
                                    @foreach (['proof1', 'proof2', 'proof3'] as $img)
                                    @if (!(empty($report[$img])))
                                    <a data-fancybox="gallery" href="{{ route('photo.report', ['slug' => $report[$img]]) }}">
                                        <img class="border" src="{{ route('photo.report', ['slug' => $report[$img]]).'?size=thumbnail' }}">
                                    </a>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @if (!empty($report['comment']))
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <h6 class="heading-small text-muted mb-4">Action made</h6>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Comment</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $report['comment'] }}</p>
                            </div>
                        </div>
                        @endif
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <h6 class="heading-small text-muted mb-4">Order information</h6>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Order #</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p><a href="{{ route('customer.order.show', ['customer_id' => $report['customer']['id'], 'order_code' => $report['suborder']['order']['code']]) }}">{{ $report['suborder']['order']['code'] }}</a></p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Date Ordered</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $report['suborder']['order']['date'] }}</p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Item List</label>
                            </div>
                            <div class="col-lg-8">
                            </div>
                            <div class="col-lg-8 pl-5">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Food Name</th>
                                            <th scope="col">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($report['suborder']['itemlist'] as $menu)
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
                            <div class="col-lg-12">
                                <h6 class="heading-small text-muted mb-4">Customer information</h6>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Name</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p><a href="{{ route('customer.show', ['customer_id' => $report['customer']['id']]) }}">{{ $report['customer']['fname'].' '.$report['customer']['lname'] }}</a></p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Contact Number</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $report['customer']['contact_number'] }}</p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Address</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $report['customer']['address'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
        @include('owner.inc.footer')
    </div>
</div>
<div class="modal fade" id="investigate_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Investigate Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to investigate this report?
            </div>
            <div class="modal-footer">
                <a href="{{ route('admin.report.investigate', ['code' => $report['code']]) }}" class="btn btn-primary">Yes</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="close_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Close Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-secondary">
                <form method="post" action="{{ route('admin.report.close', ['report_code' => $report['code']]) }}">
                @csrf
                <div class="d-flex flex-column">
                    <div class="form-group">
                        <label class="form-control-label">Why do you want to close this report?</label>
                        <textarea class="form-control form-control-alternative" name="report_comment" rows="5" style="resize:none;" required></textarea>
                    </div>
                    <div class="custom-control custom-control-alternative custom-checkbox">
                        <input class="custom-control-input" name="report_ban" id="report_ban" type="checkbox">
                        <label class="custom-control-label" for="report_ban">
                            <span class="form-control-label">Do you want to ban this customer?</span>
                        </label>
                    </div>
                    <div class="form-group pt-3 mb-0 d-none" id="reason">
                        <label class="form-control-label">Why do you want to ban this customer?</label>
                        <textarea class="form-control form-control-alternative" rows="5" name="ban_reason" style="resize:none;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Submit</a>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>
@endsection
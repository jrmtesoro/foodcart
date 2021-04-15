@extends('layouts.admin')

{{-- Page Title --}}
@section('page-title', 'Admin Dashboard')

{{-- Page Name --}}
@section('page-name', 'Ban / View Ban')

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
                            <h3 class="mr-auto my-auto">Ban Information</h3>
                            <a class="btn btn-primary" href="{{ route('ban.index') }}"><span class='fas fa-arrow-left mr-1'></span> Go Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <h6 class="heading-small text-muted mb-4">User information</h6>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">User ID</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $user['id'] }}</p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Customer Name</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <a href="{{ route('customer.show', ['customer_id' => $customer['id']]) }}">{{ $customer['fname']." ".$customer['lname'] }}</a>
                                <p></p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Email Address</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $user['email'] }}</p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Ban Reason</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $ban['reason'] }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <h6 class="heading-small text-muted mb-4">Related Reports</h6>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Tickets</label>
                            </div>
                            @php($indx = 0)
                            @foreach ($reports as $report)
                            @if ($indx == 0)
                            <div class="col-lg-8 pl-5">
                                <a href="{{ route('admin.report.show', ['report_code' => $report['code']]) }}">{{ "#".$report['code'] }}</a>
                                <p></p>
                            </div>
                            @else
                            <div class="col-lg-4 pl-5">
                            </div>
                            <div class="col-lg-8 pl-5">
                                <a href="{{ route('admin.report.show', ['report_code' => $report['code']]) }}">{{ "#".$report['code'] }}</a>
                                <p></p>
                            </div>
                            @endif
                            @php($indx++)
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.owner') {{-- Page Title --}} 
@section('page-title', 'Owner Dashboard') {{-- Page Name --}} 
@section('page-name', 'Report / Report View') {{-- Custom CSS --}} 

@section('css')
<link rel="stylesheet" href="{{ URL::asset('vendor/fancybox/dist/fancybox.css') }}">
@endsection

@section('js')
<script src="{{ asset('vendor/fancybox/dist/jquery.fancybox.min.js') }}"></script>
@endsection

@section('content')
@include('owner.inc.sidebar')
<div class="main-content">
    @include('owner.inc.navbar')
    <div class="header bg-gradient-info pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
        </div>
    </div>
    <div class="container mt--9 bg-secondary">
        <div class="row">
            <div class="col-12">
                <div class="card shadow bg-secondary">
                    <div class="card-header">
                        <div class="d-flex">
                            <h3 class="mr-auto my-auto">Report Information</h3>
                            <a class="btn btn-primary" href="{{ route('report.owner.index') }}"><span class='fas fa-arrow-left mr-1'></span> Go Back</a>
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
                                <h6 class="heading-small text-muted mb-4">Action Made</h6>
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
                                <h6 class="heading-small text-muted mb-4">Order Information</h6>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Order #</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p><a href="{{ route('owner.order.show', ['suborder' => $report['order']['id']]) }}">{{ $report['order']['code'] }}</a></p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Date Ordered</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $report['order']['date'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('owner.inc.footer')
    </div>
</div>
@endsection
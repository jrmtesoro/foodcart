@extends('layouts.admin')

{{-- Page Title --}}
@section('page-title', 'Admin Dashboard')

{{-- Page Name --}}
@section('page-name', 'Partnership Application / View Application')

{{-- Custom CSS --}}
@section('css')
<link rel="stylesheet" href="{{ URL::asset('vendor/fancybox/dist/fancybox.css') }}">
<style>
    hr {
        margin-top: 1.5rem;
        margin-bottom: 2rem;
    }

    p {
        margin-bottom: 0;
    }

</style>
@endsection

{{-- Custom JS --}}
@section('js')
<script src="{{ asset('vendor/fancybox/dist/jquery.fancybox.min.js') }}"></script>

@include('admin.pages.partnership.partnership_accept')
@include('admin.pages.partnership.partnership_reject')
@include('admin.pages.partnership.partnership_review')
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
                    <div class="card shadow">
                        <div class="card-header bg-white">
                            <div class="row align-items-center">
                                <div class="col-8">
                                <h3 class="mb-0">Application Information</h3>
                                </div>
                                <div class="col-4 text-right">
                                    @if ($restaurant_info['status'] == 0)
                                        <button class="btn btn-outline-primary btn-sm"data-toggle='modal' data-target='#review_confirmation' data-id='{{ $restaurant_info['id'] }}'>
                                            <span class='fas fa-search py-1'></span> Review
                                        </button>
                                    @elseif ($restaurant_info['status'] == 3)
                                        <button class="btn btn-outline-success btn-sm"data-toggle='modal' data-target='#accept_confirmation' data-id='{{ $restaurant_info['id'] }}'>
                                            <span class='fas fa-check py-1'></span> Accept
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" data-toggle='modal' data-target='#reject_confirmation' data-id='{{ $restaurant_info['id'] }}'>
                                            <span class='fas fa-times' style='padding: 0.3rem 0.15rem'></span> Reject
                                        </button>
                                    @endif
                                    <a class="btn btn-primary" href="{{ route('partnership.index') }}"><span class='fas fa-arrow-left mr-1'></span> Go Back</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">Restaurant Name</label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-lg-5">
                                    @if ($restaurant_info['status'] == 1)
                                    <a href="{{ route('admin.restaurant.show', ['restaurant_id' => $restaurant_info['id']]) }}" class="font-weight-bold">{{ $restaurant_info['name'] }}</a>
                                    @else
                                    <p class="font-weight-bold">{{ $restaurant_info['name'] }}</p>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">Owner Name</label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-lg-5">
                                    <p class="font-weight-bold">
                                        {{$restaurant_info['owner_fname'].' '.$restaurant_info['owner_lname'] }}
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">Address</label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-lg-5">
                                    <p class="font-weight-bold">{{ $restaurant_info['address'] }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">Contact Number</label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-lg-5">
                                    <p class="font-weight-bold">{{ $restaurant_info['contact_number'] }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">Email Address</label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-lg-5">
                                    <p class="font-weight-bold">{{ $restaurant_info['user']['email'] }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">Status</label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-lg-5">
                                    <span class="badge badge-dot font-weight-bold">
                                        @if ($restaurant_info['status'] == 0)
                                            <i class="bg-warning"></i> pending
                                        @elseif ($restaurant_info['status'] == 1)
                                            <i class="bg-success"></i> accepted
                                        @elseif ($restaurant_info['status'] == 2)
                                            <i class="bg-primary"></i> reviewing
                                        @else
                                            <i class="bg-warning"></i> rejected
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">
                                        Submitted At
                                    </label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-lg-5">
                                    <p class="font-weight-bold">{{ $restaurant_info['created_at'] }}</p>
                                </div>
                            </div>
                            <hr>
                            @if ($restaurant_info['status'] == 1 || $restaurant_info['status'] == 2)
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">
                                        @if ($restaurant_info['status'] == 1)
                                        Accepted At
                                        @elseif ($restaurant_info['status'] == 2)
                                        Rejected At
                                        @endif
                                    </label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-lg-5">
                                    <p class="font-weight-bold">{{ $restaurant_info['updated_at'] }}</p>
                                </div>
                            </div>
                            <hr>
                            @endif
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">
                                        Business Permit
                                    </label>
                                </div>
                                <div class="col-lg-9 col-md-6 col-sm-12">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-12">
                                            <div class="d-flex">
                                                @foreach ($restaurant_info['permit'] as $permit)
                                                    <a data-fancybox="gallery" href="{{ route('photo.permit', ['slug' => $permit['image_name']]) }}">
                                                        <img src="{{ route('photo.permit', ['slug' => $permit['image_name']]).'?size=medium' }}">
                                                    </a>
                                                @endforeach
                                            </div>
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
@endsection
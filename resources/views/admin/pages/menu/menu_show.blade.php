@extends('layouts.admin')

{{-- Page Title --}}
@section('page-title', 'Admin Dashboard')

{{-- Page Name --}}
@section('page-name', 'Restaurants / Menu / View Item')

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
<link rel="stylesheet" type="text/css" href="{{ asset('css/owner/menu.css') }}">
@endsection

{{-- Custom JS --}}
@section('js')
<script src="{{ asset('vendor/fancybox/dist/jquery.fancybox.min.js') }}"></script>
<script>
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
                    <div class="card shadow">
                        <div class="card-header bg-white">
                            <div class="row align-items-center">
                                <div class="col-8">
                                <h3 class="mb-0">Item Information</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <a class="btn btn-primary" href="{{ route('admin.menu.index') }}"><span class='fas fa-arrow-left mr-1'></span> Go Back</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3 my-auto">
                                    <label class="form-control-label text-muted">Item Image</label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-sm-5">
                                    @if (!empty($menu_details['image_name']))
                                    <a data-fancybox="gallery" href="{{ route('photo.menu', ['slug' => $menu_details['image_name']]) }}">
                                        <img class="border" src="{{ route('photo.menu', ['slug' => $menu_details['image_name']]).'?size=medium' }}">
                                    </a>
                                    @else
                                    <img src="{{asset('img/menu/').'/alt.png'}}" class="img-fluid img-thumbnail" height="300" width="300">
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">Restaurant Name</label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-sm-5">
                                    <p class="font-weight-bold"><a href="{{ route('admin.restaurant.show', ['restaurant_id' => $menu_details['restaurant_id']]) }}">{{ $menu_details['restaurant_name'] ?? '-' }}</a></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">Name</label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-sm-5">
                                    <p class="font-weight-bold">{{$menu_details['name'] ?? '-'}}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">Description</label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-sm-5">
                                    <p class="font-weight-bold">{{$menu_details['description'] ?? '-'}}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">Price</label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-sm-5">
                                    <p class="font-weight-bold">{{$menu_details['price'].' PHP' ?? '-'}}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">Cooking Time</label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-sm-5">
                                    <p class="font-weight-bold">{{$menu_details['cooking_time']. ' Minutes' ?? '-'}}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">Category</label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-sm-5">
                                    <p class="font-weight-bold">{{$menu_details['category'] ?? '-'}}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">Created At</label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-sm-5">
                                    <p class="font-weight-bold">{{$menu_details['created_at'] ?? '-'}}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">Updated At</label>
                                </div>
                                <div class="col-sm-9 col-md-6 col-sm-5">
                                    <p class="font-weight-bold">{{$menu_details['updated_at'] ?? '-'}}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-control-label text-muted">Tags</label><br>
                                    <span class="badge badge-pill badge-default ml-2">Pending</span>
                                    <span class="badge badge-pill badge-info">Accepted</span>
                                    <span class="badge badge-pill badge-danger">Rejected</span>
                                </div>
                                <div class="col-sm-9 col-md-6 col-sm-5">
                                    <p class="font-weight-bold">
                                        @if (!empty($menu_details['tag']))
                                            @foreach($menu_details['tag'] as $tag)
                                                <a href="{{ route('tag.index', ['search' => $tag['name']]) }}">
                                                @if ($tag['status'] == 0)
                                                <span class="badge badge-pill badge-default">{{$tag['name']}}</span>
                                                @elseif ($tag['status'] == 1)
                                                <span class="badge badge-pill badge-info">{{$tag['name']}}</span>
                                                @else
                                                <span class="badge badge-pill badge-danger">{{$tag['name']}}</span>
                                                @endif
                                                </a>
                                            @endforeach
                                        @else
                                        -
                                        @endif
                                    </p>
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
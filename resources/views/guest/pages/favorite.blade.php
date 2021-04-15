@extends('layouts.guest') 
@section('page-title', 'Pinoy Food Cart') 
@section('css')
@endsection
 
@section('js')
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card bg-light">
                <div class="card-header card-header-text card-header-warning">
                    <div class="card-text">
                        <h4 class="card-title pt-1 m-0">Favorite List</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if (!empty($favorites))
                            @foreach ($favorites as $favorite)
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <img src="{{ !empty($favorite['image_name']) ? route('photo.restaurant', ['slug' => $favorite['image_name']]).'?size=thumbnail' : asset('img/alt.png') }}" width="100" height="100">
                                                <div class="ml-4 d-flex flex-column mr-auto">
                                                    <a class="h5 title text-info my-0" href="{{ route('guest.restaurant', ['slug' => $favorite['slug']]) }}">{{ $favorite['name'] }}</a>
                                                    <div class="card-text">{{ $favorite['address'] }}</div>
                                                    <p class="h6"><span class="fas fa-star text-warning"></span> {{ $favorite['rating'] }}</p>
                                                </div>
                                                {!! Form::open(['route' => ['favorite.destroy', $favorite['slug']], 'method' => 'delete']) !!}
                                                <button type="submit" class="btn btn-danger btn-fab btn-fab-mini btn-round"><i class="material-icons">clear</i></button>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                        <div class="col-lg-12 text-center">
                            <p class="h3 title">Favorite list is empty</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="footer mt-5 text-light w-100" style="background-color: #000; position:absolute; bottom: -1;">
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
@endsection
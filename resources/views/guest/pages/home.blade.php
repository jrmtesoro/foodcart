@extends('layouts.guest') 
@section('page-title', 'Pinoy Food Cart') 
@section('css')
<link rel="stylesheet" href="{{ URL::asset('css/home.css') }}">
@endsection
 
@section('js')
<script src="{{ URL::asset('js/home.js') }}" type="text/javascript"></script>
@endsection
 
@section('content')
<div class="page-header header-filter clear-filter" style="height: 380px; margin-top:70px; background-color: #171a29;
        box-shadow: inset 0px 11px 8px -10px #000,
        inset 0px -11px 8px -10px #000; z-index: 100;">
    <div class="container">
        <div class="row">
            <div class="col-md-12 ml-auto mr-auto">
                <div class="multiple-items">
                    <img src="{{ URL::asset('material/img/bg.jpg') }}">
                    <img src="{{ URL::asset('material/img/bg2.jpg') }}">
                    <img src="{{ URL::asset('material/img/bg3.jpg') }}">
                    <img src="{{ URL::asset('material/img/bg7.jpg') }}">
                    <img src="{{ URL::asset('material/img/city.jpg') }}">
                    <img src="{{ URL::asset('material/img/city-profile.jpg') }}">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container bg-white pt-3">
    <div class="d-flex">
        <div class="px-3 w-25">
            <div id="list-example" class="list-group sticky-top border">
                @if (!empty($restaurants['newly_added']))
                <a class="list-group-item list-group-item-action" href="#list-item-3"><i
                class="material-icons circled-icon">new_releases</i>Newly Added</a>
                @endif
                @if (!empty($restaurants['whats_hot']))
                <a class="list-group-item list-group-item-action active" href="#list-item-1"><i
                class="material-icons circled-icon">whatshot</i>What's Hot</a>
                @endif
                @if (!empty($restaurants['top_rated']))
                <a class="list-group-item list-group-item-action" href="#list-item-2"><i
                class="material-icons circled-icon">star</i>Top Rated</a>
                @endif
                @if (!empty($restaurants['recommendation']))
                <a class="list-group-item list-group-item-action" href="#list-item-4"><i
                class="material-icons circled-icon">thumb_up_alt</i>Recommended</a>
                @endif
            </div>
        </div>
        <div class="px-3 w-75">
            @if (!empty($restaurants['newly_added']))
            <div id="list-item-3" class="anchor"></div>
            <h3 class="title">Newly Added</h3>
            <div class="d-flex flex-wrap justify-content-around">
                @foreach ($restaurants['newly_added'] as $restaurant)
                @php ($image = empty($restaurant['image_name']) ? asset('img/alt.png') : route('photo.restaurant', ['slug' => $restaurant['image_name']]).'?size=medium')
                <a class="card restaurant-container" href="{{ route('guest.restaurant', ['slug' => $restaurant['slug']]) }}">
                    <img class="card-img-top" src="{{ $image }}"
                        alt="Card image cap" width="300" height="200">
                    <div class="card-body">
                        <h5 class="card-title">{{ $restaurant['name'] }}</h5>
                        <div class="d-flex justify-content-between">
                            @php ($text_color = $restaurant['open'] ? "text-success" : "text-danger")
                            @php ($text = $restaurant['open'] ? "OPEN" : "CLOSED")
                            <small class="{{ $text_color }}">{{ $restaurant['times'] }}</small>
                            <small class="{{ $text_color }}">{{ $text }}</small>
                        </div>
                        <p class="h6">
                            {{ $restaurant['address'] }}
                        </p>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <div class="h6"><span class="fas fa-star text-warning"></span> {{ $restaurant['rating'] }}</div>
                            <div>•</div>
                            <div class="h6">{{ $restaurant['eta'] }} mins</div>
                            <div>•</div>
                            <div class="h6">{{ $restaurant['flat_rate'] }} PHP</div>
                        </div>
                    </div>
                </a>
                @endforeach
                <a class="card restaurant-container" href="{{ route('restaurant.search') }}">
                    <img class="card-img-top" src="{{ asset('img/view_more.jpg') }}" alt="Card image cap" width="300" height="200">
                    <div class="card-body text-center">
                        <h5 class="card-title">View More</h5>
                    </div>
                </a>
            </div>
            @endif
            @if (!empty($restaurants['whats_hot']))
            <div id="list-item-1" class="anchor"></div>
            <h3 class="title">What's Hot</h3>
            <div class="d-flex flex-wrap justify-content-around">
                @foreach ($restaurants['whats_hot'] as $restaurant)
                @php ($image = empty($restaurant['image_name']) ? asset('img/alt.png') : route('photo.restaurant', ['slug' => $restaurant['image_name']]).'?size=medium')
                <a class="card restaurant-container" href="{{ route('guest.restaurant', ['slug' => $restaurant['slug']]) }}">
                    <img class="card-img-top" src="{{ $image }}"
                        alt="Card image cap" width="300" height="200">
                    <div class="card-body">
                        <h5 class="card-title">{{ $restaurant['name'] }}</h5>
                        <div class="d-flex justify-content-between">
                            @php ($text_color = $restaurant['open'] ? "text-success" : "text-danger")
                            @php ($text = $restaurant['open'] ? "OPEN" : "CLOSED")
                            <small class="{{ $text_color }}">{{ $restaurant['times'] }}</small>
                            <small class="{{ $text_color }}">{{ $text }}</small>
                        </div>
                        <p class="h6">
                            {{ $restaurant['address'] }}
                        </p>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <div class="h6"><span class="fas fa-star text-warning"></span> {{ $restaurant['rating'] }}</div>
                            <div>•</div>
                            <div class="h6">{{ $restaurant['eta'] }} mins</div>
                            <div>•</div>
                            <div class="h6">{{ $restaurant['flat_rate'] }} PHP</div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
            @if (!empty($restaurants['top_rated']))
            <div id="list-item-2" class="anchor"></div>
            <h3 class="title">Top Rated</h3>
            <div class="d-flex flex-wrap justify-content-around">
                @foreach ($restaurants['top_rated'] as $restaurant)
                @php ($image = empty($restaurant['image_name']) ? asset('img/alt.png') : route('photo.restaurant', ['slug' => $restaurant['image_name']]).'?size=medium')
                <a class="card restaurant-container" href="{{ route('guest.restaurant', ['slug' => $restaurant['slug']]) }}">
                    <img class="card-img-top" src="{{ $image }}"
                        alt="Card image cap" width="300" height="200">
                    <div class="card-body">
                        <h5 class="card-title">{{ $restaurant['name'] }}</h5>
                        <div class="d-flex justify-content-between">
                            @php ($text_color = $restaurant['open'] ? "text-success" : "text-danger")
                            @php ($text = $restaurant['open'] ? "OPEN" : "CLOSED")
                            <small class="{{ $text_color }}">{{ $restaurant['times'] }}</small>
                            <small class="{{ $text_color }}">{{ $text }}</small>
                        </div>
                        <p class="h6">
                            {{ $restaurant['address'] }}
                        </p>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <div class="h6"><span class="fas fa-star text-warning"></span> {{ $restaurant['rating'] }}</div>
                            <div>•</div>
                            <div class="h6">{{ $restaurant['eta'] }} mins</div>
                            <div>•</div>
                            <div class="h6">{{ $restaurant['flat_rate'] }} PHP</div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
            @if (!empty($restaurants['recommendation']))
            <div id="list-item-4" class="anchor"></div>
            <h3 class="title">Recommended</h3>
            <div class="d-flex flex-wrap justify-content-around">
                @foreach ($restaurants['recommendation'] as $restaurant)
                @php ($image = empty($restaurant['image_name']) ? asset('img/alt.png') : route('photo.restaurant', ['slug' => $restaurant['image_name']]).'?size=medium')
                <a class="card restaurant-container" href="{{ route('guest.restaurant', ['slug' => $restaurant['slug']]) }}">
                    <img class="card-img-top" src="{{ $image }}"
                        alt="Card image cap" width="300" height="200">
                    <div class="card-body">
                        <h5 class="card-title">{{ $restaurant['name'] }}</h5>
                        <div class="d-flex justify-content-between">
                            @php ($text_color = $restaurant['open'] ? "text-success" : "text-danger")
                            @php ($text = $restaurant['open'] ? "OPEN" : "CLOSED")
                            <small class="{{ $text_color }}">{{ $restaurant['times'] }}</small>
                            <small class="{{ $text_color }}">{{ $text }}</small>
                        </div>
                        <p class="h6">
                            {{ $restaurant['address'] }}
                        </p>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <div class="h6"><span class="fas fa-star text-warning"></span> {{ $restaurant['rating'] }}</div>
                            <div>•</div>
                            <div class="h6">{{ $restaurant['eta'] }} mins</div>
                            <div>•</div>
                            <div class="h6">{{ $restaurant['flat_rate'] }} PHP</div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
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
@endsection
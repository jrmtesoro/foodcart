@extends('layouts.guest') 
@section('page-title', 'Pinoy Food Cart') 

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/selectize/selectize.css') }}">
<link rel="stylesheet" href="{{ URL::asset('css/home.css') }}">
@endsection
 
@section('js')
<script type="text/javascript" src="{{ asset('vendor/selectize/selectize.min.js') }}"></script>
<script>
$('#tag').selectize({
    plugins: ['remove_button'],
    delimiter: ',',
    valueField: 'name',
    labelField: 'name',
    searchField: 'name',
    persist: false,
    options: {!! json_encode($tags, true) !!},
    preload: true,
    create : false,
    placeholder: "Enter your tags here",
});

@if (!empty($input['tag']))
    var $select = $('#tag').selectize('options');  
    var selectize = $select[0].selectize;
    @foreach(explode(',', $input['tag']) as $tag)
        selectize.addOption({!! json_encode(array('name' => $tag)) !!});
    @endforeach
    selectize.setValue({!! json_encode(explode(',', $input['tag'])) !!}, false);
@endif
</script>
@endsection

@section('content')
<div class="container bg-white card" style="box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.12);">
    <div class="d-flex">
        <div class="card w-25">
            {!! Form::open(['route' => 'restaurant.search', 'method' => 'GET']) !!}
            <div class="card-body">
                <div class="d-flex flex-column border-bottom">
                    <p class="h3 title border-bottom">Search</p>
                    <div class="form-group bmd-form-group pb-3">
                        <label class="bmd-label-floating">Restaurant Name</label>
                        <input type="text" class="form-control" name="search" value="{{ $input['search'] ?? '' }}">
                    </div>
                    <div class="form-group pb-3">
                        <label>Tag</label>
                        <input type="text" name="tag" id="tag">
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-block btn-info red-color">Search</button>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="px-4 w-75">
            <p class="h2 title border-bottom">Restaurant</p>
            <div class="d-flex flex-wrap justify-content-around">
                @if (!empty($restaurants))
                @foreach ($restaurants as $restaurant)
                @php ($image = empty($restaurant['image_name']) ? asset('img/alt.png') : route('photo.restaurant', ['slug' => $restaurant['image_name']]).'?size=medium')
                <a class="card restaurant-container" href="{{ route('guest.restaurant', ['slug' => $restaurant['slug']]) }}">
                    <img class="card-img-top" src="{{ $image }}"
                        alt="Card image cap" width="300" height="200">
                    <div class="card-body">
                        <h5 class="card-title text-dark">{{ $restaurant['name'] }}</h5>
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
                @else
                <p class="h3 title text-muted mx-auto">No restaurant found</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
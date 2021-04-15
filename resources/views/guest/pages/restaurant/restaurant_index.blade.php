@extends('layouts.guest') 
@section('page-title', 'Pinoy Food Cart') 
@section('css')
<link rel="stylesheet" href="{{ URL::asset('vendor/rateyo/jquery.rateyo.min.css') }}">
<style>
    .navbar {
        margin-bottom: 0;
        border-radius: 0;
    }

    .breadcrumb {
        margin-bottom: 0;
        padding: 5px;
    }

    .breadcrumb>li>a {
        color: #c40514;
    }

    .breadcrumb>li {
        font-size: 12px;
    }

    .stick {
        position: sticky;
        top: 0;
        width: 100%;
        z-index: 101;
    }

    #content {
        padding-top: 20px;
    }

    p.h4 {
        margin-bottom: 0;
    }

    .timer-icon {
        position: absolute;
        margin-left: -30px;
    }
    .anchor {
        height: 74px;
    }
</style>
@endsection
 
@section('js')
<script src="{{ URL::asset('vendor/rateyo/jquery.rateyo.min.js') }}"></script>
<script>

$(".add_cart").submit(function(e){
    e.preventDefault;
    var slug = e.currentTarget.dataset.slug;

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
            'Accept' : 'applcation/json'
        },
        url: "{{ route('cart.store') }}",
        method: 'POST',
        data: {
            menu_slug : slug,
            quantity  : $("#"+slug).val()
        },
        success: function(result){
            getCartCount();
            if (!result.success) {
                iziToast.error({
                    title: 'Hey',
                    color: 'red',
                    theme: 'light',
                    icon: 'fa fa-exclamation-triangle',
                    title: 'Error!',
                    message: result.message
                });
            } else {
                iziToast.success({
                    title: 'Hey',
                    color: 'green',
                    theme: 'light',
                    icon: 'fa fa-cart-plus',
                    title: 'Success!',
                    message: result.message
                });
            }
        }
    });

    return false;
});

@if (session()->has('token'))
$("#rating").rateYo({
    rating: "{{ $details['rating'] }}"
});

$("#rating").click(function () {
    var rating = $("#rating").rateYo("option", "rating");

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
            'Accept' : 'applcation/json'
        },
        url: "{{ route('rating.store') }}",
        method: 'POST',
        data: {
            restaurant_slug : "{{ $details['slug'] }}",
            rating  : rating
        },
        success: function(result){
            if (!result.success) {
                iziToast.error({
                    title: 'Hey',
                    color: 'red',
                    theme: 'light',
                    icon: 'fa fa-exclamation-triangle',
                    title: 'Error!',
                    position: 'topCenter',
                    message: result.message
                });
            } else {
                iziToast.success({
                    title: 'Hey',
                    color: 'yellow',
                    theme: 'light',
                    icon: 'fa fa-star',
                    title: 'Success!',
                    position: 'topCenter',
                    message: result.message
                });
                $('#restaurant_rating').html('Rating : <span class="fas fa-star mr-1" style="color: #FF9900;"></span>'+result.data.total_rating+' - '+result.data.vote);
            }
        }
    });
});

$(".favorite-btn").click(function (e) {
    if ($(this).attr('id') == "add_favorite") {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
                'Accept' : 'applcation/json'
            },
            url: "{{ route('favorite.store') }}",
            method: 'POST',
            data: {
                restaurant_slug : "{{ $details['slug'] }}"
            },
            success: function(result){
                if (!result.success) {
                    iziToast.error({
                        title: 'Hey',
                        color: 'red',
                        theme: 'light',
                        icon: 'fa fa-exclamation-triangle',
                        title: 'Error!',
                        position: 'topCenter',
                        message: result.message
                    });
                } else {
                    iziToast.success({
                        title: 'Hey',
                        color: 'yellow',
                        theme: 'light',
                        icon: 'fa fa-star',
                        title: 'Success!',
                        position: 'topCenter',
                        message: result.message
                    });
                    var $button = $('.favorite-btn');

                    $button.attr('id', "remove_favorite");
                    $button.html('<span class="fas fa-heart-broken mr-2"></span><u>Remove From Favorite</u>');
                }
            }
        });
    } else if ($(this).attr('id') == "remove_favorite") {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
                'Accept' : 'applcation/json'
            },
            url: "{{ route('favorite.destroy', ['restaurant_slug' => $details['slug']]) }}",
            method: 'DELETE',
            data: {
                restaurant_slug : "{{ $details['slug'] }}"
            },
            success: function(result){
                if (!result.success) {
                    iziToast.error({
                        title: 'Hey',
                        color: 'red',
                        theme: 'light',
                        icon: 'fa fa-exclamation-triangle',
                        title: 'Error!',
                        position: 'topCenter',
                        message: result.message
                    });
                } else {
                    iziToast.success({
                        title: 'Hey',
                        color: 'yellow',
                        theme: 'light',
                        icon: 'fa fa-star',
                        title: 'Success!',
                        position: 'topCenter',
                        message: result.message
                    });
                    var $button = $('.favorite-btn');

                    $button.attr('id', "add_favorite");
                    $button.html('<span class="fas fa-heart mr-2"></span><u>Add to Favorite</u>');
                }
            }
        });
    }
});
@else
$("#rating").rateYo({
    rating : "{{ $details['rating'] }}",
    readOnly : true
});
@endif
</script>
@endsection
 
@section('content')
<nav aria-label="breadcrumb" role="navigation">
    <div class="no-gutter" style="background-color: #e9ecef;">
        <div class="container">
            <ol class="breadcrumb mx-auto">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $details['name'] }}</li>
            </ol>
        </div>
    </div>
</nav>
<nav class="navbar navbar-dark bg-primary" style="background-color: #171a29 !important; box-shadow: none;">
    <div class="container">
        <div class="row py-4">
            <div class="col-auto my-auto">
                <img src="{{ !empty($details['image_name']) ? route('photo.restaurant', ['slug' => $details['image_name']]).'?size=medium' : asset('img/alt.png') }}">   
            </div>
            <div class="col-auto my-auto">
                <p class="h2 mb-0">{{ $details['name'] }}</p>
                @if ($details['open'])
                <p class="h6 mt-0"><span class="fas fa-door-open mr-1"></span><span class="text-success">Open</p></p>
                @else
                <p class="h6 mt-0"><span class="fas fa-door-open mr-1"></span><span class="text-danger">Closed</p></p>
                @endif
                <p class="h6 mt-0"><span class="fas fa-clock mr-1"></span>{{ $details['times'] }}</p>
                <p class="h6"><span class="fas fa-phone mr-1"></span>{{ $details['contact_number'] }}</p>
                <p class="h6"><span class="fas fa-map-marker-alt mr-1"></span>{{ $details['address'] }}</p>
                <small id="restaurant_rating">Rating : <span class="fas fa-star mr-1" style="color: #FF9900;"></span>{{ $details['rating'] }} - {{ $details['votes'] }}</small>
                <div id="rating"></div>
                <p class="py-1"></p>
                @if (session()->has('token'))
                    @php ($id = $details['favorite'] ? "remove_favorite" : "add_favorite")
                    @php ($text = $details['favorite'] ? "Remove from Favorite" : "Add to Favorite")
                    @php ($icon = $details['favorite'] ? "heart-broken" : "heart")
                    <button id="{{ $id }}" class="btn btn-sm btn-link favorite-btn"><span class="fas fa-{{ $icon }} mr-2"></span><u>{{ $text }}</u></button>
                @endif
            </div>
        </div>
    </div>
</nav>
<nav class="navbar navbar-dark bg-dark stick" id="#list-example">
    <div class="container">
        <ul class="navbar-nav d-flex flex-row">
            @foreach ($details['category'] as $category)
            <li class="nav-item">
                <a class="nav-link" href="#{{ $category['name'] }}"><h4 class="card-title my-0 text-light"><u>{{ $category['name'] }}</u></h4></a>
            </li>
            @endforeach
        </ul>
    </div>
</nav>
<div class="container" id="content">
    @foreach ($details['category'] as $category) @if (!empty($category['menu']))
    <div class="anchor" id="{{ $category['name'] }}"></div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-header-text card-header-primary" style="background: #c40514 !important;">
                    <div class="card-text">
                        <h4 class="card-title text-center mt-0">{{ strtoupper($category['name']) }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($category['menu'] as $menu)
                        <div class="col-12 border-bottom">
                            <div class="row py-3">
                                <div class="col-auto">
                                    <img src="{{ !empty($menu['image_name']) ? route('photo.menu', ['slug' => $menu['image_name']]).'?size=thumbnail' : asset('img/alt.png') }}" height="100" width="100">
                                </div>
                                <div class="col my-auto">
                                    <form class="add_cart" data-slug="{{ $menu['slug'] }}">
                                    <div class="row">
                                        <div class="col-xl-3 my-auto">
                                            <p class="h4 title my-0">{{ $menu['name'] }}</p>
                                        </div>
                                        <div class="col-xl-3 my-auto">
                                            <p class="h5 title my-0">{{ $menu['cooking_time'] }} mins.</p>
                                        </div>
                                        <div class="col-xl-3 my-auto">
                                            <p class="title h4 my-0">₱ {{ $menu['price'] }}.00</p>
                                        </div>
                                        <div class="col-xl-3 text-xl-right">
                                            @if (session()->has('token'))
                                            <button class="btn btn-warning"><span class="material-icons">add_shopping_cart</span> Add to Cart</button>
                                            @else
                                            <a href="{{ route('login') }}" class="btn btn-warning"><span class="material-icons">add_shopping_cart</span> Add to Cart</a>
                                            @endif
                                        </div>
                                    </div>
                                    @if (!empty($menu['tag']))
                                        @foreach($menu['tag'] as $tag) 
                                            @if ($tag['status'] == "1")
                                                <a href="{{ route('restaurant.search', ['tag' => $tag['name']]) }}" class="badge badge-pill badge-info red-color">{{ $tag['name'] }}</a>
                                            @endif
                                        @endforeach
                                    @endif
                                    <p class="h6 mr-auto">{{ $menu['description'] }}</p>
                                    <div class="form-group" style="width: 50px">
                                        <label>Quantity</label>
                                        <input type="number" onKeyDown="return false" class="form-control number" min="1" value="1" id="{{ $menu['slug'] }}" required>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif @endforeach
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
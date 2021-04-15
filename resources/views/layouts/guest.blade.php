<html>

<head>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta charset="utf-8">
    <meta name="_token" content="{{ csrf_token() }}" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
        name='viewport' />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    {{-- Page Title --}}
    <title>@yield('page-title')</title>

    {{-- Main CSS --}}
    @include('guest.inc.css')

    {{-- Custom CSS --}}
    @yield('css')
    <link type="text/css" href="{{ asset('css/loading.css') }}" rel="stylesheet">
    <link type="text/css" href="{{ asset('vendor/toast/dist/css/iziToast.min.css') }}" rel="stylesheet">
    <style>
        .red-color {
            background-color: #c40514 !important;
            border-color: #c40514 !important;
        }

        a:hover {
            color: #c40514;
        }
    </style>

</head>

<body data-spy="scroll" data-target="#list-example" data-offset="100">
    <div class="d-none" id="loading_screen">
        <div class="loading_container">
            <div class="lds-ring">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>

    @include('sweetalert::alert')
    {{-- Navbar --}}
    @include('guest.inc.navbar')

    {{-- Main Content --}}
    @yield('content')
</body>

{{-- Main Java Script --}}
@include('guest.inc.js')
<script src="{{ asset('vendor/toast/dist/js/iziToast.min.js') }}" type="text/javascript"></script>

{{-- Custom Java Script --}}
@yield('js')
<script src="{{ asset('js/loading.js') }}"></script>
<script>
    function getCartCount() {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
                'Accept' : 'application/json'
            },
            url: "{{ route('guest.notification.cart') }}",
            method: 'GET',
            success: function(result){
                if (result.success) {
                    if (result.data != 0) {
                        $('#cart_count').text(result.data);
                    } else {
                        $('#cart_count').text("");
                    }
                }
            },
            global: false
        });
    }
    getCartCount();
    setInterval(function(){
        getCartCount();
    }, 5000);
</script>
@include('guest.inc.modals')
</html>
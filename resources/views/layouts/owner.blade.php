<html>

<head>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="_token" content="{{ csrf_token() }}" />
    {{-- Page Title --}}
    <title>@yield('page-title')</title>

    {{-- Main CSS --}}
    @include('owner.inc.css')

    {{-- Custom CSS --}}
    @yield('css')
    <link type="text/css" href="{{ asset('css/table.css') }}" rel="stylesheet">
    <link type="text/css" href="{{ asset('css/loading.css') }}" rel="stylesheet">

</head>
<body>
    <div class="d-none" id="loading_screen">
        <div class="loading_container">
            <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
        </div>
    </div>
    @include('sweetalert::alert')
    {{-- Main Content --}}
    
    @yield('content')

    </body>

    {{-- Main Java Script --}}
    @include('owner.inc.js')

    {{-- Custom Java Script --}}
    @yield('js')
    @include('owner.inc.menu_modal')
    @include('owner.inc.category_modal')
    <script src="{{ asset('js/argon.js?v=1.0.0') }}"></script>
    <script src="{{ asset('js/loading.js') }}"></script>
    <script>
    function getOrderCount() {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
                'Accept' : 'application/json'
            },
            url: "{{ route('owner.notification.orders') }}",
            method: 'GET',
            success: function(result){
                if (result.success) {
                    var html = '';
                    if (result.data != 0) {
                        $('.notification_count').text(result.data);
                        $('#order_count').text(result.data);
                        var link = "{{ url('owner/order') }}";
                        +"/1"
                        for (var i = 0; i < result.data; i++) {
                            var order_code = result.suborders[i].code;
                            var sub_order_link = link+"/"+result.suborders[i].id;

                            var order_status = result.suborders[i].status;
                            badge = '<i class="bg-warning"></i> pending';
                            if (order_status == 1) {
                                badge = '<i class="bg-primary"></i> processing';
                            } else if (order_status == 2) {
                                badge = '<i class="bg-info"></i> delivering';
                            }

                            html += '<a class="dropdown-item border-top pb-0" href="'+sub_order_link+'"><h5>Order #'+order_code+'</h5><h5 class="badge badge-dot">'+badge+'</h5></a>';
                        }
                    } else {
                        $('#order_count').text("");
                    }

                    if (html == "") {
                        html = '<div class="dropdown-header noti-title"><h6 class="text-overflow m-0">Notifications</h6></div><div class="dropdown-divider"></div><div class="dropdown-header noti-title"><h6 class="text-overflow text-center m-0">No Orders</h6></div>'
                    } else {
                        html = '<div class="dropdown-header noti-title"><h6 class="text-overflow m-0">Notifications</h6></div><div class="scrollable-menu">'+html+'</div>';
                    }

                    $('.notification_container').html(html);
                }
            },
            global: false
        });
    }
    getOrderCount();
    
    setInterval(function(){
        getOrderCount();
    }, 5000);
    </script>
    @yield('chart')
</html>
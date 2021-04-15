<html>

<head>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="_token" content="{{ csrf_token() }}" />
    {{-- Page Title --}}
    <title>@yield('page-title')</title>

    {{-- Main CSS --}}
    @include('admin.inc.css')

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
    @include('admin.inc.js')
    {{-- Custom Java Script --}}
    @yield('js')
    <script src="{{ asset('js/argon.js?v=1.0.0') }}"></script>
    <script src="{{ asset('js/loading.js') }}"></script>
    <script>
        function getAdminNotifications() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
                    'Accept' : 'application/json'
                },
                url: "{{ route('admin.notification.admin') }}",
                method: 'GET',
                success: function(result){
                    if (result.success) {
                        var temp = 0;
                        var html = '<div class="dropdown-header noti-title"><h6 class="text-overflow m-0">Notifications</h6></div>';
                        if (result.data.partnership != 0) {
                            temp++;
                            html += '<div class="dropdown-divider"></div><a class="dropdown-item" href="{{ route("partnership.index") }}"><span class="badge badge-primary mr-2">'+result.data.partnership+'</span> Pending Application(s)</a>';
                            $('#partnership_count').text(result.data.partnership);
                        } else {
                            $('#partnership_count').text("");
                        }

                        if (result.data.tags != 0) {
                            temp++;
                            html += '<div class="dropdown-divider"></div><a class="dropdown-item" href="{{ route("tag.index") }}"><span class="badge badge-primary mr-2">'+result.data.tags+'</span> Pending Tag(s)</a>';
                            $('#tag_count').text(result.data.tags);
                        } else {
                            $('#tag_count').text("");
                        }
                        
                        if (result.data.reports != 0) {
                            temp++;
                            html += '<div class="dropdown-divider"></div><a class="dropdown-item" href="{{ route("admin.report.index") }}"><span class="badge badge-primary mr-2">'+result.data.reports+'</span> Pending Report(s)</a>';
                            $('#report_count').text(result.data.reports);
                        } else {
                            $('#report_count').text("");
                        }

                        if (result.data.request != 0) {
                            temp++;
                            html += '<div class="dropdown-divider"></div><a class="dropdown-item" href="{{ route("changerequest.index") }}"><span class="badge badge-primary mr-2">'+result.data.request+'</span> Pending Request(s)</a>';
                            $('#request_count').text(result.data.request);
                        } else {
                            $('#request_count').text("");
                        }

                        if (temp != 0) {
                            $('.notification_count').text(temp);
                        }

                        $('.notification_container').html(html);
                    }
                },
                global: false
            });
        }

        getAdminNotifications();

        setInterval(function(){
            getAdminNotifications();
        }, 5000);
    </script>
    @yield('chart')
</html>
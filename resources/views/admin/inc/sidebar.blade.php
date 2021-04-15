<style>
        .navbar-vertical .navbar-collapse:before {
            margin-top : 5px;
        }
    
        .navbar-nav > .nav-item > .nav-link.active
        {
            width: 100%;
            background: #f6f9fc;
            color: rgba(0, 0, 0, .9);
        }
    </style>
    <nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" style="z-index: 99;" id="sidenav-main">
    <div class="container-fluid">
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Brand -->
        <a class="navbar-brand pt-0" href="{{route('admin.dashboard')}}">
            <img src="{{ URL::asset('material/img/navBrand.png') }}" class="navbar-brand-img" alt="...">
        </a>
        <!-- User -->
        <ul class="nav align-items-center d-md-none">
        <li class="nav-item dropdown">
            <a class="nav-link nav-link-icon" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="ni ni-bell-55"></i>
            <span class="badge badge-primary ml-1"><strong class="text-white notification_count"></strong></span>
            </a>
            <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right notification_container" aria-labelledby="navbar-default_dropdown_1">
            </div>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div class="media align-items-center">
                <span class="avatar avatar-sm rounded-circle">
                <img alt="Image placeholder" src="{{ URL::asset('material/img/navBrand.png') }}">
                </span>
            </div>
            </a>
            <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
            <div class=" dropdown-header noti-title">
                <h6 class="text-overflow m-0">Welcome!</h6>
            </div>
            <a href="{{ route('admin.show') }}" class="dropdown-item">
                <i class="ni ni-single-02"></i>
                <span>My profile</span>
            </a>
            <a href="{{ route('admin.logs.index') }}" class="dropdown-item">
                <i class="ni ni-calendar-grid-58"></i>
                <span>Activity</span>
            </a>
            <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item">
                    <i class="ni ni-user-run"></i>
                    <span>Logout</span>
                </a>
            </div>
        </li>
        </ul>
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="sidenav-collapse-main">
        <!-- Collapse header -->
        <div class="navbar-collapse-header d-md-none">
            <div class="row">
            <div class="col-6 collapse-brand">
                <a href="{{ route('admin.dashboard') }}">
                <img src="{{ URL::asset('material/img/navBrand.png') }}">
                </a>
            </div>
            <div class="col-6 collapse-close">
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle sidenav">
                <span></span>
                <span></span>
                </button>
            </div>
            </div>
        </div>
        <!-- Navigation -->
        <ul class="navbar-nav">
            <li class="nav-item">
            <a class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}" {{ Route::is('admin.dashboard') ? '' : 'href='.route('admin.dashboard')}}>
                <i class="ni ni-tv-2 text-primary"></i> Dashboard
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link {{ Route::is('tag.index') ? 'active' : '' }}" href="{{ route('tag.index') }}">
                <i class="fas fa-tags text-orange text-green"></i><span class="badge badge-primary mr-2" id="tag_count"></span> Tags
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link {{ Route::is('partnership.index') ? 'active' : '' }}" href="{{ route('partnership.index') }}">
                <i class="fas fa-handshake text-yellow"></i><span class="badge badge-primary mr-2" id="partnership_count"></span> Partnership Application
            </a>
            </li>

            <li class="nav-item">
            <a class="nav-link {{ Route::is('admin.restaurant.index') ? 'active' : '' }}" href="{{ route('admin.restaurant.index') }}">
                <i class="fas fa-utensils text-orange"></i> Restaurants
            </a>
            </li>

            <li class="nav-item">
            <a class="nav-link {{ Route::is('admin.menu.index') ? 'active' : '' }}" href="{{ route('admin.menu.index') }}">
                <i class="ni ni-single-copy-04 text-warning"></i> Menu
            </a>
            </li>

            <li class="nav-item">
            <a class="nav-link {{ Route::is('admin.order.index.web') ? 'active' : '' }}" href="{{ route('admin.order.index.web') }}">
                <i class="ni ni-archive-2 text-red"></i> Orders
            </a>
            </li>

            <li class="nav-item">
            <a class="nav-link {{ Route::is('customer.index') ? 'active' : '' }}" href="{{ route('customer.index') }}">
                <i class="fas fa-user text-primary"></i> Customers
            </a>
            </li>

            <li class="nav-item">
            <a class="nav-link {{ Route::is('admin.user.index') ? 'active' : '' }}" href="{{ route('admin.user.index') }}">
                <i class="fas fa-users text-yellow"></i> Users
            </a>
            </li>

            <li class="nav-item">
            <a class="nav-link {{ Route::is('admin.report.index') ? 'active' : '' }}" href="{{ route('admin.report.index') }}">
                <i class="fas fa-exclamation-circle text-red"></i><span class="badge badge-primary mr-2" id="report_count"></span> Reported Users
            </a>
            </li>

            <li class="nav-item">
            <a class="nav-link {{ Route::is('ban.index') ? 'active' : '' }}" href="{{ route('ban.index') }}">
                <i class="fas fa-minus-circle text-red"></i> Block List
            </a>
            </li>

            <li class="nav-item">
            <a class="nav-link {{ Route::is('changerequest.index') ? 'active' : '' }}" href="{{ route('changerequest.index') }}">
                <i class="fas fa-file text-black"></i><span class="badge badge-primary mr-2" id="request_count"></span> Requests
            </a>
            </li>

            <li class="nav-item">
            <a class="nav-link {{ Route::is('logs.index') ? 'active' : '' }}" href="{{ route('logs.index') }}">
                <i class="fas fa-clipboard-list text-black"></i> Logs
            </a>
            </li>

            <li class="nav-item">
            <a class="nav-link {{ Route::is('admin.sales.index') ? 'active' : '' }}" href="{{ route('admin.sales.index') }}">
                <i class="ni ni-money-coins text-green"></i> Sales
            </a>
            </li>
        </ul>
        </div>
    </div>
</nav>
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
        <a class="navbar-brand pt-0" href="{{route('owner.dashboard')}}">
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
                <img alt="Image placeholder" src="{{ route('photo.restaurant', ['slug' => session()->get('restaurant_image')])."?size=thumbnail" }}">
                </span>
            </div>
            </a>
            <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
            <div class=" dropdown-header noti-title">
                <h6 class="text-overflow m-0">Welcome!</h6>
            </div>
            <a href="{{ route('owner.profile') }}" class="dropdown-item">
                <i class="ni ni-single-02"></i>
                <span>My profile</span>
            </a>
            {{-- <a href="#" class="dropdown-item">
                <i class="ni ni-settings-gear-65"></i>
                <span>Settings</span>
            </a> --}}
            <a href="{{ route('report.owner.index') }}" class="dropdown-item">
            <i class="fas fa-exclamation-circle"></i>
            <span>Reports</span>
            </a>
            <a href="{{ route('owner.logs.index') }}" class="dropdown-item">
                <i class="ni ni-calendar-grid-58"></i>
                <span>Activity</span>
            </a>
            {{-- <a href="#" class="dropdown-item">
                <i class="ni ni-support-16"></i>
                <span>Support</span>
            </a> --}}
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
                <a href="{{ route('owner.dashboard') }}">
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
            <a class="nav-link {{ Route::is('owner.dashboard') ? 'active' : '' }}" {{ Route::is('owner.dashboard') ? '' : 'href='.route('owner.dashboard')}}>
                <i class="ni ni-tv-2 text-primary"></i> Dashboard
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link {{ Route::is('menu.*') ? 'active' : '' }}" 
            aria-expanded="{{ Route::is('menu.*') ? 'true' : 'false' }}" 
            href="#menu-sub" data-toggle="collapse" role="button">
                <i class="ni ni-single-copy-04 text-blue"></i>
                <span class="nav-link-text">Menu</span>
            </a>
            <div class="collapse {{ Route::is('menu.*') ? 'show' : '' }}" id="menu-sub">
                <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                        <a href="{{route('menu.index')}}" class="nav-link {{ Route::is('menu.index') ? 'active' : '' }}"> 
                            <i class="fas fa-list-ul"></i>Menu List
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('menu.create')}}" class="nav-link {{ Route::is('menu.create') ? 'active' : '' }}">
                            <i class="fas fa-plus-circle"></i>Add Item
                        </a>
                    </li>
                </ul>
            </div>
            </li>
            <li class="nav-item">
            <a class="nav-link {{ Route::is('category.*') ? 'active' : '' }}" 
            aria-expanded="{{ Route::is('category.*') ? 'true' : 'false' }}"
            href="#category-sub" data-toggle="collapse" role="button">
                <i class="fas fa-th text-orange"></i>
                <span class="nav-link-text">Category</span>
            </a>
            <div class="collapse {{ Route::is('category.*') ? 'show' : '' }}" id="category-sub">
                <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                        <a href="{{ route('category.index') }}" class="nav-link {{ Route::is('category.index') ? 'active' : '' }}"> 
                            <i class="fas fa-list-ul"></i>Category List
                        </a>
                    </li>
                </ul>
            </div>
            </li>
            {{-- <li class="nav-item">
            <a class="nav-link {{ Route::is('owner.profile') ? 'active' : '' }}" {{ Route::is('owner.profile') ? '' : 'href='.route('owner.profile')}}>
                <i class="ni ni-single-02 text-yellow"></i> Restaurant profile
            </a>
            </li> --}}
            <li class="nav-item">
            <a class="nav-link {{ Route::is('owner.order.index') ? 'active' : '' }}" {{ Route::is('owner.order.index') ? '' : 'href='.route('owner.order.index')}}>
                <i class="ni ni-archive-2 text-red"></i><span class="badge badge-primary mr-2" id="order_count"></span> Orders
            </a>
            <li class="nav-item">
            <a class="nav-link {{ Route::is('owner.sales.index') ? 'active' : '' }}" href="{{ route('owner.sales.index') }}">
                <i class="ni ni-money-coins text-green"></i> Sales
            </a>
            </li>
        </ul>
        {{-- <!-- Divider -->
        <hr class="my-3">
        <!-- Heading -->
        <h6 class="navbar-heading text-muted">Documentation</h6>
        <!-- Navigation -->
        <ul class="navbar-nav mb-md-3">
            <li class="nav-item">
            <a class="nav-link" href="https://demos.creative-tim.com/argon-dashboard/docs/getting-started/overview.html">
                <i class="ni ni-spaceship"></i> Getting started
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="https://demos.creative-tim.com/argon-dashboard/docs/foundation/colors.html">
                <i class="ni ni-palette"></i> Foundation
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="https://demos.creative-tim.com/argon-dashboard/docs/components/alerts.html">
                <i class="ni ni-ui-04"></i> Components
            </a>
            </li>
        </ul> --}}
        </div>
    </div>
    </nav>
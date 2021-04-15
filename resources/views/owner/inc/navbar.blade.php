<nav class="navbar navbar-top navbar-expand-md navbar-dark" id="navbar-main">
    <div class="container-fluid">
    <a class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block" href="#">@yield('page-name')</a>
    <ul class="navbar-nav align-items-center d-none d-md-flex">
        <li class="nav-item dropdown">
            <a class="nav-link nav-link-icon" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="ni ni-bell-55"></i>
            <span class="badge badge-primary ml-1"><strong class="text-white notification_count"></strong></span>
            </a>
            <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right notification_container" aria-labelledby="navbar-default_dropdown_1">
            </div>
        </li>
        <li class="nav-item dropdown">
        <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div class="media align-items-center">
                <span class="avatar avatar-sm rounded-circle">
                    <img alt="Image placeholder" src="{{ route('photo.restaurant', ['slug' => session()->get('restaurant_image')])."?size=thumbnail" }}">
                </span>
                <div class="media-body ml-2 d-none d-lg-block">
                    <span class="mb-0 text-sm font-weight-bold">{{ session()->get('restaurant_name') }}</span>
                </div>
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
    </div>
</nav>
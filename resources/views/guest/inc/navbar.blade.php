<nav class="navbar {{ Route::is('home') ? 'fixed-top' : '' }} navbar-expand-lg bg-light border-bottom" id="sectionsNav">
    <div class="container">
        <div class="navbar-translate">
            <a class="navbar-brand" href="{{ route('home') }}">
                <div class="logo-image">
                    <img src="{{ URL::asset('material/img/navBrand.png') }}" class="img-fluid">
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="sr-only">Toggle navigation</span>
                <span class="navbar-toggler-icon"></span>
                <span class="navbar-toggler-icon"></span>
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                @if (Route::is('owner.info'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}">
                        <i class="material-icons">directions_run</i> Logout
                    </a>
                </li>
                @else
                {!! Form::open(['route' => 'restaurant.search', 'method' => 'GET']) !!}
                    <div class="form-group no-border my-auto mx-auto">
                        <input type="text" class="form-control" name="search" placeholder="Search Restaurant">
                    </div>
                    <button type="submit" class="btn red-color btn-info btn-just-icon btn-round">
                        <i class="material-icons">search</i>
                    </button>
                {!! Form::close() !!}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('contact_us.index') }}">
                        <i class="material-icons pb-1">mail</i> <span class="h4 font-weight-bold">Contact Us</span>
                    </a>
                </li>
                @if (!session()->has('access_level'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('partner') }}">
                        <i class="material-icons pb-1">business_center</i> <span class="h4 font-weight-bold">Partner Us</span>
                    </a>
                </li>
                @endif
                @if (!Route::is('login') && !session()->has('access_level'))
                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#signInModal">
                        <i class="material-icons pb-1">account_circle</i> <span class="h4 font-weight-bold">Sign in</span>
                    </a>
                </li>
                @endif
                @if (session()->has('access_level'))
                @if (!Route::is('guest.checkout'))
                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#cartModal">
                        <i class="material-icons pb-1">shopping_cart</i> <span class="badge badge-danger p-1" id="cart_count"></span> <span class="h4 font-weight-bold">Cart</span>
                    </a>
                </li>
                @endif
                <li class="dropdown nav-item">
                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" aria-expanded="false">
                        <i class="material-icons pb-1">account_circle</i> <span class="h4 font-weight-bold">{{ ucfirst(session()->get('fname'))." ".ucfirst(session()->get('lname')) }}</span>
                        <div class="ripple-container"></div></a>
                    <div class="dropdown-menu dropdown-with-icons">
                        <a href="{{ route('customer.edit') }}" class="dropdown-item">
                            <i class="material-icons pb-1">border_color</i> <span class="h4 font-weight-bold">Edit Profile</span>
                        </a>
                        <a href="{{ route('order.index') }}" class="dropdown-item">
                            <i class="material-icons pb-1">update</i> <span class="h4 font-weight-bold">Order History</span>
                        </a>
                        <a href="{{ route('favorite.index') }}" class="dropdown-item">
                            <i class="material-icons pb-1">star</i> <span class="h4 font-weight-bold">Favorite List</span>
                        </a>
                        <a href="{{ route('logout') }}" class="dropdown-item">
                            <i class="material-icons pb-1">directions_run</i> <span class="h4 font-weight-bold">Logout</span>
                        </a>
                    </div>
                </li>
                @endif
            </ul>
        </div>
        @endif
    </div>
</nav>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>

    <link rel="stylesheet" href="{{ URL::asset('vendor/bootstrap-4.2/css/bootstrap.min.css') }}">
    <link href="{{ URL::asset('vendor/@fortawesome/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
</head>
<body class="bg-dark">
    @include('sweetalert::alert')
    <div class="container py-5">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center text-white mb-4">Login</h2>
                <div class="row">
                    <div class="col-md-6 mx-auto">
                        <!-- form card login -->
                        <div class="card rounded-0">
                            <div class="card-header">
                                <h3 class="mb-0">Login</h3>
                            </div>
                            <div class="card-body">
                                <form class="form" role="form" method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label>Email Address</label>
                                        <input type="text" class="form-control form-control-lg rounded-0" name="login_email" id="uname1" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" class="form-control form-control-lg rounded-0" name="login_password" required>
                                    </div>
                                    {{-- <div>
                                        <label class="custom-control custom-checkbox">
                                          <input type="checkbox" class="custom-control-input">
                                          <span class="custom-control-indicator"></span>
                                          <span class="custom-control-description small text-dark">Remember me on this computer</span>
                                        </label>
                                    </div> --}}
                                    <button type="submit" class="btn btn-success btn-lg float-right">Login</button>
                                </form>
                                <p><a href="{{route('register.customer.form')}}">Register as customer</a></p>
                                <p><a href="{{route('register.owner.form')}}">Register as owner</a></p>
                            </div>
                            <!--/card-block-->
                        </div>
                        <!-- /form card login -->
                    </div>
                </div>
                <!--/row-->
            </div>
            <!--/col-->
        </div>
        <!--/row-->
    </div>
    <!--/container-->
</body>
<script src="{{ URL::asset('vendor/jquery-3.3.1/jquery-3.3.1.min.js') }}"></script>
<script src="{{ URL::asset('vendor/popper/popper.min.js') }}"></script>
<script src="{{ URL::asset('vendor/bootstrap-4.2/js/bootstrap.min.js') }}"></script>
</html>
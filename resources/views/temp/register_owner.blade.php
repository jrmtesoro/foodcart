<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ URL::asset('vendor/bootstrap-4.2/css/bootstrap.min.css') }}">
    <link href="{{ URL::asset('vendor/@fortawesome/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
</head>
<body>
<div class="container py-3">
    <div class="row">
        <div class="mx-auto col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Restaurant Register</h4>
                </div>
                <div class="card-body">
                    <form class="form" role="form" method="POST" action={{route('register.owner')}} enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">First name</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" name="reg_fname" value="Ryan">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Last name</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" name="reg_lname" value="Tesoro">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Restaurant Name</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" name="reg_restaurant_name" value="Nena's Eatery">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Contact Number</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" name="reg_contact_number" value="6583792">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Address</label>
                            <div class="col-lg-9">
                                <textarea class="form-control" name="reg_address">Taytay, Rizal</textarea>
                            </div>
                        </div>
                        {{-- <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Picture of documents</label>
                            <div class="col-lg-9">
                                <input type="file" name="reg_images" class="form-control-file" accept=".png,.jpeg,.jpg" multiple>
                            </div>
                        </div> --}}
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Email</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="email" name="reg_email" value="ryantesoro@yahoo.com">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Password</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="password" name="reg_password" value="123456">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Password Confirm</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="password" name="reg_password_confirm" value="123456">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"></label>
                            <div class="col-lg-9">
                                <input type="reset" class="btn btn-secondary" value="Cancel">
                                <input type="submit" class="btn btn-primary" value="Save Changes">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
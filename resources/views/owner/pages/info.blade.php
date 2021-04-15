@extends('layouts.guest') 
@section('page-title', 'Pinoy Food Cart') 
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/timedropper/timedropper.css') }}">
@endsection
 
@section('js')
<script type="text/javascript" src="{{ asset('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('vendor/timedropper/timedropper.js') }}"></script>
<script>
$( "#open_time" ).timeDropper({
    meridians: true
});
$( "#close_time" ).timeDropper({
    meridians: true
});
</script>

<script>
$("#24hours").change(function() {
    if (this.checked) {
        $('input[name=open_time]').attr('disabled', 'true');
        $('input[name=open_time]').removeAttr('required');
        $('input[name=close_time]').attr('disabled', 'true');
        $('input[name=close_time]').removeAttr('required');
        $('#open_24').removeClass('text-secondary').addClass('text-primary');
    } else {
        $('input[name=open_time]').removeAttr('disabled');
        $('input[name=open_time]').attr('required', 'true');
        $('input[name=close_time]').removeAttr('disabled');
        $('input[name=close_time]').attr('required', 'true');
        $('#open_24').removeClass('text-primary').addClass('text-secondary');
    }
});
</script>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            {!! Form::open(['route' => 'owner.update.info', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <h3 class="title">Restaurant Information</h3>
                            <hr>
                        </div>
                    </div>
                    @if (session()->has('errors'))
                    <div class="alert alert-danger">
                        <div class="container">
                            <div class="alert-icon">
                                <i class="material-icons">error_outline</i>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="material-icons">clear</i></span>
                            </button>
                            <ul>
                                <b>Invalid Input</b>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row ml-2">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Restaurant Image</label>
                                        <input type="file" class="form-control-file pt-2" name="image_name" accept=".png,.jpeg,.jpg" 
                                        data-toggle="tooltip" data-placement="left" title="" data-container="body" data-original-title="Pick your restaurant logo" 
                                        required>
                                    </div>
                                </div>
                            </div>
                            <div class="row ml-2">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Flat rate</label>
                                        {!! Form::number('flat_rate', old('flat_rate') ?? '', [
                                            "class" => "form-control",
                                            "required" => true,
                                            "min" => 1,
                                            "max" => 150,
                                            "data-toggle" => "tooltip",
                                            "data-placement" => "right",
                                            "data-container" => "body",
                                            "data-original-title" => "This is the delivery charge, It must be in PHP."
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row ml-2">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Estimated time</label>
                                        {!! Form::number('eta', old('eta') ?? '', [
                                            "class" => "form-control",
                                            "required" => true,
                                            "min" => 1,
                                            "max" => 150,
                                            "data-toggle" => "tooltip",
                                            "data-placement" => "right",
                                            "data-container" => "body",
                                            "data-original-title" => "This is your preparation and delivery time, It should be in minutes."
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="form-group ml-4">
                                    <label>Opening Time</label>
                                    {!! Form::text('open_time', old('open_time') ?? null, 
                                    [
                                        'class' => 'form-control',
                                        'tab_index' => '3',
                                        'id' => 'open_time',
                                        'required' => true,
                                        "data-toggle" => "tooltip",
                                        "data-placement" => "left",
                                        "data-container" => "body",
                                        "data-original-title" => "Opening time of your restaurant"
                                    ]) !!}
                                </div>
                                <div class="form-group ml-4">
                                    <label>Closing Time</label>
                                    {!! Form::text('close_time', old('close_time') ?? null, 
                                    [
                                        'class' => 'form-control',
                                        'tab_index' => '3',
                                        'id' => 'close_time',
                                        'required' => true,
                                        "data-toggle" => "tooltip",
                                        "data-placement" => "right",
                                        "data-container" => "body",
                                        "data-original-title" => "Closing time of your restaurant"
                                    ]) !!}
                                </div>
                            </div>
                            <div class="row ml-2 pt-2">
                                <div class="col-12">
                                    <div class="togglebutton" data-toggle="tooltip" data-placement="left" title="" data-container="body" data-original-title="Turn this on if your restaurant is open 24hours">
                                        <label>
                                            <input type="checkbox" name="24hours" id="24hours">
                                            <span class="toggle"></span>
                                            <span class="text-secondary" id="open_24">Restaurant is open 24 Hours</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="card-footer">
                    <button class="btn btn-info btn-block" type="submit">Submit</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
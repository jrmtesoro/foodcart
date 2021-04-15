@extends('layouts.owner')

{{-- Page Title --}}
@section('page-title', 'Owner Dashboard')

{{-- Page Name --}}
@section('page-name', 'Menu / Edit Item')

{{-- Custom CSS --}}
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/selectize/selectize.css') }}">
<link href="{{ asset('vendor/editable-select/jquery-editable-select.min.css') }}" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{ asset('css/owner/menu.css') }}">
@endsection

@section('js')
<script type="text/javascript" src="{{ asset('vendor/selectize/selectize.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/editable-select/jquery-editable-select.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script>
    $('#menu_tag').selectize({
        plugins: ['remove_button'],
        delimiter: ',',
        valueField: 'name',
        labelField: 'name',
        searchField: 'name',
        persist: false,
        options: {!! json_encode($tag_list, true) ?? '[]' !!},
        preload: true,
        create : true,
        placeholder: "Enter your tags here",
    });
    var $select = $('#menu_tag').selectize('options');  
    var selectize = $select[0].selectize;
    @if (!empty(old('menu_tag')))
        @foreach(explode(',', old('menu_tag')) as $tag)
            selectize.addOption({!! json_encode(array('name' => $tag)) !!});
        @endforeach
        selectize.setValue({!! json_encode(explode(',', old('menu_tag'))) !!}, false);
    @elseif (!empty($menu_details['tag']))
        @foreach ($menu_details['tag'] as $tag)
             selectize.addOption({!! json_encode(array('name' => $tag['name'])) !!});
        @endforeach
        selectize.setValue({!! json_encode($menu_details['tag_list1'], true) !!}, false);
    @endif
</script>
<script>
$('#edit_menu_form').validate({
    rules: {
        menu_name: "required",
        menu_price: "required",
        menu_cooking_time: "required",
        menu_category: "required",

        menu_name: {
            required: true,
            maxlength: 50,
            minlength: 6,
        },
        menu_price: {
            required: true,
            digits: true,
            minlength: 1,
            maxlength: 5,
            notEqual: '0'
        },
        menu_cooking_time: {
            required: true,
            digits: true,
            minlength: 1,
            maxlength: 3,
            notEqual: '0'
        },
        menu_category: {
            required: true
        }
    },
    messages: {
        menu_name: "Please enter the item name",
        menu_price: "Please enter the item price",
        menu_cooking_time: "Please enter the item cooking time",
        menu_category: "Please enter the item category",

        menu_name: {
            required: "Please enter the menu name",
            maxlength: "Item name must not exceed 50 characters in length",
            minlength: "Item name must be atleast 6 characters in length"
        },
        menu_price: {
            required: "Please enter your item price",
            digits: "The price should be in numerical form",
            maxlength: "Item price must not exceed 5 digits in length",
            notEqual: "Item price cannot be zero"
        },
        menu_cooking_time: {
            required: "Please enter the cooking time",
            digits: "The cooking time should be in numerical form",
            maxlength: "Cooking time must not exceed 3 digits in length",
            notEqual: "Cooking time cannot be zero"
        },
        menu_category: {
            required: "Please enter the item category"
        }

    },
    errorElement: "div",
    errorPlacement: function(error, element) {
            error.addClass("invalid-feedback");
            error.insertAfter(element);
    },
    highlight: function(element, errorClass, validClass) {
        $(element).addClass("is-invalid").removeClass("is-valid");
    },
    unhighlight: function(element, errorClass, validClass) {
        if ($(element).hasClass('ft')) {
            $(element).removeClass("ft");
        } else {
            $(element).addClass("is-valid").removeClass("is-invalid");
        }
    }
});

jQuery.validator.addMethod("notEqual", function (value, element, param) {
    return this.optional(element) || value != '0';
});
</script>
@endsection

@section('content')
    @include('owner.inc.sidebar')
    <div class="main-content">
        @include('owner.inc.navbar')
        <div class="header bg-gradient-info pb-8 pt-5 pt-md-8">
            <div class="container-fluid">
                
            </div>
        </div>
        <div class="container-fluid mt--9 bg-secondary">
            <div class="row">
                <div class="col">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white">
                            <div class="row align-items-center">
                                <div class="col-8">
                                <h3 class="mb-0">Edit Item Form</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('menu.show', ['menu' => $menu_details['id']]) }}"><span class='fas fa-eye py-1'></span></a>
                                    <button class="btn btn-outline-danger btn-sm" data-toggle="modal" data-menu="{{ $menu_details['id'] }}" 
                                    data-target="#delete_confirmation"><span class='fas fa-minus-circle py-1' style="font-size: 1rem;"></button>
                                    <a class="btn btn-primary" href="{{route('menu.index')}}"><span class='fas fa-arrow-left mr-1'></span> Go Back</a>
                                </div>
                            </div>
                        </div>
                        {!! Form::model($menu_details, ['method' => 'post', 'route' => ['menu.update', $menu_details['id']], 'id' => 'edit_menu_form', 'enctype' => 'multipart/form-data']) !!}
                        <div class="card-body">
                            <h6 class="heading-small text-muted mb-4"><span class="text-danger">*</span> fields are required</h6>
                            <div class="form-group row mb-0">
                                <label class="col-sm-2 form-control-label">Item Image</label>
                                <div class="col-sm-10 col-md-6 col-lg-5">
                                    <input type="file" name="menu_image" id="menu_image" accept=".png, .jpeg, .jpg"/>
                                    @if ($errors->has('menu_image'))
                                    <div class="custom-invalid">{{$errors->first('menu_image')}}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                </div>
                                <div class="col-10 pt-1">
                                    <small class="text-muted">Max upload size is 5MB, We only accept JPEG, JPG, and PNG file formats.</small>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row mb-0">
                                <label class="col-sm-2 form-control-label my-auto">Name <span class="text-danger">*</span></label>
                                <div class="col-sm-10 col-md-6 col-lg-5">
                                    {!! Form::text('menu_name', old('menu_name') ?? $menu_details['name'],
                                    [
                                        'class' => 'ft form-control form-control-alternative ' . ($errors->has('menu_name') ? 'is-invalid' : ''),
                                        'placeholder' => 'Enter food name here',
                                        'tab_index' => '1'
                                    ]) !!}
                                    @if ($errors->first('menu_name'))
                                    <div id="menu_name-error-temp" class="error invalid-feedback">{{$errors->first('menu_name')}}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="row pt-1">
                                <div class="col-sm-2">
                                </div>
                                <div class="col-sm-10 col-md-6 col-lg-5">
                                    <small class="text-muted">Food name should be atleast 6 characters in length.</small>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label my-auto">Description</label>
                                <div class="col-sm-10 col-md-6 col-lg-5">
                                    {!! Form::textarea('menu_description', old('menu_description') ?? $menu_details['description'],
                                    [
                                        'class' => 'ft form-control form-control-alternative',
                                        'placeholder' => 'Enter food description here',
                                        'rows' => '4',
                                        'tab_index' => '2'
                                    ]) !!}
                                    @if ($errors->first('menu_name'))
                                    <div id="menu_name-error-temp" class="error invalid-feedback">{{$errors->first('menu_name')}}</div>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row mb-0">
                                <label class="col-sm-2 form-control-label my-auto">Price <span class="text-danger">*</span></label>
                                <div class="col-sm-6 col-md-4 col-lg-2">
                                    {!! Form::text('menu_price', old('menu_price') ?? $menu_details['price'], 
                                    [
                                        'class' => 'ft form-control form-control-alternative ' . ($errors->has('menu_price') ? 'is-invalid' : ''),
                                        'placeholder' => 'Pesos',
                                        'tab_index' => '3'
                                    ]) !!}
                                    @if ($errors->first('menu_price'))
                                    <div id="menu_price-error-temp" class="error invalid-feedback">{{$errors->first('menu_price')}}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="row pt-1">
                                <div class="col-sm-2">
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-2">
                                    <small class="text-muted">This is the price of the food you are selling, It must be in PHP</small>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row mb-0">
                                <label class="col-sm-2 form-control-label my-auto">Cooking Time <span class="text-danger">*</span></label>
                                <div class="col-sm-6 col-md-4 col-lg-2">
                                    {!! Form::text('menu_cooking_time', old('menu_cooking_time') ?? $menu_details['cooking_time'], 
                                    [
                                        'class' => 'ft form-control form-control-alternative ' . ($errors->has('menu_cooking_time') ? 'is-invalid' : ''),
                                        'placeholder' => 'in minutes',
                                        'tab_index' => '4'
                                    ]) !!}
                                    @if ($errors->first('menu_cooking_time'))
                                    <div id="menu_cooking_time-error-temp" class="error invalid-feedback">{{$errors->first('menu_cooking_time')}}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="row pt-1">
                                <div class="col-sm-2">
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-2">
                                    <small class="text-muted">This is the preparation time of the food, It must be in minutes.</small>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-2">
                                </div>
                                <div class="col-10 pb-1">
                                    <small class="text-muted">What category does this food goes to? (eg. Main Dish, Beverages, ComboMeals, etc.)</small>
                                </div>
                            </div>
                            <div class="form-group row mt-0">
                                <label class="col-sm-2 form-control-label mb-auto">Category <span class="text-danger">*</span></label>
                                <div class="col-sm-10 col-md-6 col-lg-5">
                                    {!! Form::select('menu_category', $category_list, old('menu_category') ?? $menu_details['category'][0]['id'], 
                                    [
                                        'class' => 'form-control form-control-alternative ' . ($errors->has('menu_category') ? 'is-invalid' : ''),
                                        'tab_index' => '5'
                                    ]) !!}
                                    <p class="pt-3 m-0 text-right">
                                        <a class="heading-small font-weight-bold" data-toggle="modal" data-target="#add_category" href="#"> 
                                            <span class="fas fa-plus mr-2"></span>Add Category
                                        </a>
                                    </p>
                                    @if ($errors->first('menu_category'))
                                    <div id="menu_category-error-temp" class="error invalid-feedback">{{$errors->first('menu_category')}}</div>
                                    @endif
                                    
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-2">
                                </div>
                                <div class="col-10 pb-1">
                                    <small class="text-muted">Tags will help the customers search the food they want. (eg. Beef, Pork, Silog, etc.)</small>
                                </div>
                            </div>
                            <div class="form-group row mt-0">
                                <label class="col-sm-2 form-control-label my-auto">Tags</label>
                                <div class="col-sm-10 col-md-6 col-lg-5">
                                    <input type="text" name="menu_tag" id="menu_tag" class="ignore">
                                    <small class="pt-2">New tags will be sent to the administrator for reviews</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-6 text-right">
                                    <button type="submit" class="btn btn-info">Submit</button>
                                </div>
                                <div class="col-6 text-left">
                                    <button type="reset" class="btn btn-danger">Reset</button>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
            @include('owner.inc.footer')
        </div>
    </div>
@endsection
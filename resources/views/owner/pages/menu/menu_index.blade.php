@extends('layouts.owner') {{-- Page Title --}} 
@section('page-title', 'Owner Dashboard') {{-- Page Name --}} 
@section('page-name','Menu') {{-- Custom CSS --}} 
@section('css')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/dataTables/css/dataTables.bootstrap4.mins.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/dataTables/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/dataTables/css/select.dataTables.min.css') }}">
<style>
    .fas {
        font-size: 1rem;
    }
</style>
@endsection
 {{-- Custom Java Script --}} 
@section('js')
<script type="text/javascript" src="{{ URL::asset('vendor/dataTables/js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('vendor/dataTables/js/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('vendor/dataTables/js/dataTables.select.min.js') }}"></script>

<script>
@if (Request::has('cat'))
$('#category > option').each(function() {
    if ($(this).val() == {{Request::get('cat')}}) {
        $('#category').val($(this).val());
    }
});
@endif

@if (Request::has('show'))
$('#show_trash > option').each(function() {
    if ($(this).val() == '{{Request::get('show')}}') {
        $('#show_trash').val($(this).val());
    }
});
@endif

var datatables_link = "{{ route('datatable.menu') }}";
</script>

<script src="{{ URL::asset('js/owner/menu.js') }}"></script>
@endsection
 
@section('content')
@include('owner.inc.sidebar')
<div class="main-content">
    @include('owner.inc.navbar')
    <div class="header bg-gradient-info pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
            </div>
        </div>
    </div>
    <div class="container-fluid mt--9 bg-secondary">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header">
                        <div class="row">
                            <div class="col text-left my-auto">
                                <h3>Menu List</h3>
                            </div>
                            <div class="col text-right">
                                <a href="{{route('menu.create')}}" class="btn btn-info"><span class="fas fa-plus mr-2"></span>Add Item</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-header bg-secondary p-1 border-bottom-0">
                        <button class="btn btn-link heading-small" type="button" data-toggle="collapse" data-target="#filter"><span class="fas fa-filter mr-1"></span>Filters</button>
                    </div>
                    <form id="filter_form" class="m-0" method="POST">
                    <div id="filter" class="collapse">
                    <div class="card-body border-top">
                        <div class="row">
                            <div class="col-sm-12 col-md-4 col-lg-3">
                                <div class="form-group mb-0">
                                    <label class="form-control-label" id="label">Search</label>
                                    <input type="text" class="form-control form-control-sm" id="search" name="search">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-2">
                                <div class="form-group mb-0">
                                    <label class="form-control-label">Column</label>
                                    <select class="form-control form-control-sm" name="column" id="column">
                                        <option value="all">All</option> 
                                        <option value="menu.name">Name</option> 
                                        <option value="menu.price">Price</option> 
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-2">
                                <div class="form-group mb-0">
                                    <label class="form-control-label">Category</label>
                                    <select class="form-control form-control-sm" name="category" id="category">
                                <option value="all">All</option> 
                                @if (!empty($category_list))
                                    @foreach($category_list as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                @endif
                            </select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-2">
                                <label class="form-control-label">&nbsp</label>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block btn-sm">Search</button>
                                </div>
                            </div>
                        </div>
                        </form>
                        <div class="row">
                            <div class="col-sm-12 col-md-4 col-lg-2">
                                <div class="form-group mb-0">
                                    <label class="form-control-label">Shown rows</label>
                                    <select class="form-control form-control-sm" name="length_change" id="length_change">
                                        <option value='10'>10</option>
                                        <option value='25'>25</option>
                                        <option value='50'>50</option>
                                        <option value='100'>100</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-2">
                                <div class="form-group mb-0">
                                    <label class="form-control-label">Show</label>
                                    <select class="form-control form-control-sm" name="show_trash" id="show_trash">
                                        <option value='without'>Without Hidden Items</option>
                                        <option value='trash'>Hidden Items Only</option>
                                        <option value='both'>Both</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center" id="owner_menu_table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Category</th>
                                    <th>Date Added</th>
                                    <th>Date Hidden</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @include('owner.inc.footer')
    </div>
</div>
@endsection
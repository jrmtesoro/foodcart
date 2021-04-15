@extends('layouts.admin') {{-- Page Title --}} 
@section('page-title', 'Admin Dashboard') {{-- Page Name --}} 
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
@if (Request::has('tag'))
$('#tag > option').each(function() {
    if ($(this).val() == '{{Request::get('tag')}}') {
        $('#tag').val($(this).val());
    }
});
@endif
var datatables_link = "{{ route('datatable.admin.menu') }}";
$('#loading_screen').removeClass('d-none');
var table = $("#admin_menu_table").DataTable({
    autoWidth : true,
    serverSide : true,
    order: [[ 3, "desc" ]],
    dom : 
        "<'row'<'col px-0'tr>>" +
        "<'card-footer'<'row'<'col-sm-12 col-lg-6'i><'col-sm-12 col-lg-6'p>>>",
    ajax : {
        "url" : datatables_link,
        "type" : "get",
        "contentType": 'application/json',
        'data' : function (d) {
            d.search = $('#search').val();
            d.column = $('#column').val();
            d.tag = $('#tag').val();
            d.show_trash = $('#show_trash').val();
        }
    },
    initComplete : function( settings, json ) {
        $('#loading_screen').addClass('d-none');
    },
    columns: [
        { data: 'menu_name', name:'menu_name' },
        { data: 'restaurant_name', name:'restaurant_name' },
        { data: 'tags', name: 'tags' },
        { data: 'menu_created_at', name: 'menu_created_at' },
        { data: 'deleted_at', name: 'menu.deleted_at', visible: false },
        { data: 'action', name:'action', orderable: false, searchable: false },
    ],
    oLanguage : {
        oPaginate : {
            sNext : '<i class="fas fa-angle-right"></i>',
            sPrevious : '<i class="fas fa-angle-left"></i>'
        }
    },
});

$('#length_change').val(table.page.len());

$('#length_change').change( function() { 
    table.page.len( $(this).val() ).draw();
});

$('#show_trash').change(function (){
    if (this.value == 'trash') {
        table.column(4).visible(true);
        table.column(3).visible(false);
    } else if (this.value == 'without') {
        table.column(4).visible(false);
        table.column(3).visible(true);
    } else {
        table.column(3).visible(true);
        table.column(4).visible(true);
    }
    table.columns.adjust().draw();
});

$('#filter_form').on('submit', function(e) {
    table.columns.adjust().draw();
    e.preventDefault();
});
</script>
@endsection
 
@section('content')
@include('admin.inc.sidebar')
<div class="main-content">
    @include('admin.inc.navbar')
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
                                        <option value="restaurant.name">Restaurant Name</option> 
                                        <option value="menu.name">Menu Name</option> 
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-2">
                                <div class="form-group mb-0">
                                    <label class="form-control-label">Tag</label>
                                    <select class="form-control form-control-sm" name="tag" id="tag">
                                <option value="all">All</option> 
                                @if (!empty($tag_list))
                                    @foreach($tag_list as $tag)
                                        <option value="{{ $tag['name'] }}">{{ $tag['name'] }}</option>
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
                        <table class="table table-flush align-items-center" id="admin_menu_table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Menu Name</th>
                                    <th>Restaurant Name</th>
                                    <th>Tags</th>
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
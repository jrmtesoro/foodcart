@extends('layouts.admin') {{-- Page Title --}} 
@section('page-title', 'Admin Dashboard') {{-- Page Name --}} 
@section('page-name', 'Activities') {{-- Custom CSS --}} 
@section('css')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/dataTables/css/dataTables.bootstrap4.mins.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/dataTables/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/dataTables/css/select.dataTables.min.css') }}">
@endsection

@section('js')
<script type="text/javascript" src="{{ URL::asset('vendor/dataTables/js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('vendor/dataTables/js/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('vendor/dataTables/js/dataTables.select.min.js') }}"></script>
<script>
var datatables_link = "{{ route('admin.logs.datatable') }}";
$('#loading_screen').removeClass('d-none');
var table = $("#activity_table").DataTable({
    autoWidth : true,
    serverSide : true,
    order: [[ 4, "desc" ]],
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
            d.show_trash = $('#show_trash').val();
            d.origin = $('#origin').val();
        }
    },
    initComplete : function( settings, json ) {
        $('#loading_screen').addClass('d-none');
    },
    columns: [
        { data: 'ip_address', name:'logs.ip_address' },
        { data: 'type', name:'logs.type' },
        { data: 'description', name:'logs.description' },
        { data: 'origin', name: 'logs.origin' },
        { data: 'created_at', name: 'logs.created_at' },
    ],
    oLanguage : {
        oPaginate : {
            sNext : '<i class="fas fa-angle-right"></i>',
            sPrevious : '<i class="fas fa-angle-left"></i>'
        }
    },
});

$('#show_trash, #origin').change(function (){
    table.columns.adjust().draw();
});

$('#length_change').val(table.page.len());

$('#length_change').change( function() { 
    table.page.len( $(this).val() ).draw();
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
                                <h3>Activity List</h3>
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
                                    <div class="col-sm-12 col-md-4 col-lg-3">
                                        <div class="form-group mb-0">
                                            <label class="form-control-label" id="label">Column</label>
                                            <select class="form-control form-control-sm" name="column" id="column">
                                                <option value='all'>All</option>
                                                <option value='logs.ip_address'>IP Address</option>
                                                <option value='logs.description'>Description</option>
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
                                            <option value='all'>All</option>
                                            <option value='view'>View</option>
                                            <option value='insert'>Insert</option>
                                            <option value='update'>Update</option>
                                            <option value='delete'>Delete</option>
                                            <option value='print'>Print</option>
                                            <option value='logout'>Logout</option>
                                            <option value='login'>Login</option>
                                            <option value='verify'>Verify</option>
                                            <option value='forgot'>Forgot</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4 col-lg-2">
                                    <div class="form-group mb-0">
                                        <label class="form-control-label">Origin</label>
                                        <select class="form-control form-control-sm" name="origin" id="origin">
                                            <option value='all'>All</option>
                                            <option value='web'>Web</option>
                                            <option value='app'>App</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center" id="activity_table">
                            <thead class="thead-light">
                                <tr>
                                    <th>IP Address</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>Origin</th>
                                    <th>Date Created</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @include('admin.inc.footer')
    </div>
</div>
@endsection
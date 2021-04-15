@extends('layouts.admin')

{{-- Page Title --}}
@section('page-title', 'Admin Dashboard')

{{-- Page Name --}}
@section('page-name', 'Customers / View Customer')

{{-- Custom CSS --}}
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
var datatables_link = "{{ route('datatable.customer.order') }}";
$('#loading_screen').removeClass('d-none');
var table = $("#admin_customer_order_table").DataTable({
    autoWidth : true,
    serverSide : true,
    order: [[ 1, "desc" ]],
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
            d.customer_id = "{{ $customer['id'] }}";
        }
    },
    initComplete : function( settings, json ) {
        $('#loading_screen').addClass('d-none');
    },
    columns: [
        { data: 'code', name:'orders.code' },
        { data: 'created_at', name:'orders.created_at' },
        { data: 'action', name:'action', orderable: false, searchable: false }
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

$('#filter_form').on('submit', function(e) {
    table.columns.adjust().draw();
    e.preventDefault();
});
</script>

<script>
var datatables_link = "{{ route('admin.customer.logs.datatable', ['customer_id' => $customer['id']]) }}";
var table1 = $("#activity_table").DataTable({
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
            d.search = $('#search1').val();
            d.column = $('#column1').val();
            d.show_trash = $('#show_trash1').val();
            d.origin = $('#origin1').val();
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
$('#show_trash1, #origin1').change(function (){
    table1.columns.adjust().draw();
});

$('#length_change1').val(table.page.len());

$('#length_change1').change( function() { 
    table.page.len( $(this).val() ).draw();
});

$('#filter_form1').on('submit', function(e) {
    table1.columns.adjust().draw();
    e.preventDefault();
});
</script>

<script>
var datatables_link3 = "{{ route('admin.customer.report.datatable', ['customer_id' => $customer['id']]) }}";
$('#loading_screen').removeClass('d-none');
var table3 = $("#report_table").DataTable({
    autoWidth : true,
    serverSide : true,
    order: [[ 2, "desc" ]],
    dom : 
        "<'row'<'col px-0'tr>>" +
        "<'card-footer'<'row'<'col-sm-12 col-lg-6'i><'col-sm-12 col-lg-6'p>>>",
    ajax : {
        "url" : datatables_link3,
        "type" : "get",
        "contentType": 'application/json',
        'data' : function (d) {
            d.show_trash = $('#show_trash3').val();
        }
    },
    initComplete : function( settings, json ) {
        $('#loading_screen').addClass('d-none');
    },
    columns: [
        { data: 'report_code', name:'report.code' },
        { data: 'order_code', name:'orders.code' },
        { data: 'name', name:'restaurant.name' },
        { data: 'status', name: 'report.status' },
        { data: 'created_at', name: 'orders.created_at' },
        { data: 'action', name:'action', orderable: false, searchable: false },
    ],
    oLanguage : {
        oPaginate : {
            sNext : '<i class="fas fa-angle-right"></i>',
            sPrevious : '<i class="fas fa-angle-left"></i>'
        }
    },
});

$('#length_change3').val(table.page.len());

$('#length_change3').change( function() { 
    table3.page.len( $(this).val() ).draw();
});

$('#show_trash3').change(function (){
    table3.columns.adjust().draw();
});
</script>
@endsection

@section('content')
@include('admin.inc.sidebar')
<div class="main-content">
    @include('admin.inc.navbar')
    <div class="header bg-gradient-info pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
        </div>
    </div>
    <div class="container-fluid mt--9 bg-secondary">
        <div class="row">
            <div class="col-12">
                <div class="card shadow bg-secondary">
                    <div class="card-header">
                        <div class="d-flex">
                            <h3 class="mr-auto my-auto">Customer Information</h3>
                            <a class="btn btn-primary" href="{{ route('customer.index') }}"><span class='fas fa-arrow-left mr-1'></span> Go Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($customer['banned'])
                        <h1 class="text-danger text-center">Banned</h1>
                        @endif
                        <div class="row">
                            <div class="col-lg-12">
                                <h6 class="heading-small text-muted mb-4">Customer information</h6>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">First Name</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $customer['fname'] }}</p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Last Name</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $customer['lname'] }}</p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Contact Number</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $customer['contact_number'] }}</p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Address</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $customer['address'] }}</p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Date Joined</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $customer['date'] }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <h6 class="heading-small text-muted mb-4">Order History</h6>
                            </div>
                            <div class="col-lg-12 pl-5">
                                <div class="card shadow">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col text-left my-auto">
                                                <h3>Order List</h3>
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
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-flush align-items-center" id="admin_customer_order_table">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Order #</th>
                                                    <th>Date Created</th>
                                                    <th>Options</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <h6 class="heading-small text-muted mb-4">Customer Activity</h6>
                            </div>
                            <div class="col-lg-12 pl-5">
                                <div class="card shadow">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col text-left my-auto">
                                                    <h3>Activity List</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-header bg-secondary p-1 border-bottom-0">
                                            <button class="btn btn-link heading-small" type="button" data-toggle="collapse" data-target="#filter1"><span class="fas fa-filter mr-1"></span>Filters</button>
                                        </div>
                                        <form id="filter_form1" class="m-0" method="POST">
                                            <div id="filter1" class="collapse">
                                                <div class="card-body border-top">
                                                    <div class="row">
                                                        <div class="col-sm-12 col-md-4 col-lg-3">
                                                            <div class="form-group mb-0">
                                                                <label class="form-control-label" id="label">Search</label>
                                                                <input type="text" class="form-control form-control-sm" id="search1" name="search1">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12 col-md-4 col-lg-3">
                                                            <div class="form-group mb-0">
                                                                <label class="form-control-label" id="label">Column</label>
                                                                <select class="form-control form-control-sm" name="column1" id="column1">
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
                                                            <select class="form-control form-control-sm" name="length_change1" id="length_change1">
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
                                                            <select class="form-control form-control-sm" name="show_trash1" id="show_trash1">
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
                                                            <select class="form-control form-control-sm" name="origin1" id="origin1">
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
                            <hr>
                            <div class="row">
                                <div class="col-lg-12">
                                    <h6 class="heading-small text-muted mb-4">Restaurant reports to this customer</h6>
                                </div>
                                <div class="col-lg-12 pl-5">
                                    <div class="card shadow">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col text-left my-auto">
                                                    <h3>Report List</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-header bg-secondary p-1 border-bottom-0">
                                            <button class="btn btn-link heading-small" type="button" data-toggle="collapse" data-target="#filter3"><span class="fas fa-filter mr-1"></span>Filters</button>
                                        </div>
                                        <div id="filter3" class="collapse">
                                            <div class="card-body border-top">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-4 col-lg-2">
                                                        <div class="form-group mb-0">
                                                            <label class="form-control-label">Shown rows</label>
                                                            <select class="form-control form-control-sm" name="length_change3" id="length_change3">
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
                                                            <select class="form-control form-control-sm" name="show_trash3" id="show_trash3">
                                                                <option value='all'>All</option>
                                                                <option value='0'>Open</option>
                                                                <option value='1'>Under Investigation</option>
                                                                <option value='2'>Closed</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-flush align-items-center" id="report_table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Ticket #</th>
                                                        <th>Ticket #</th>
                                                        <th>Restaurant Name</th>
                                                        <th>Status</th>
                                                        <th>Submitted</th>
                                                        <th>Options</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('admin.inc.footer')
        </div>
    </div>
</div>
@endsection
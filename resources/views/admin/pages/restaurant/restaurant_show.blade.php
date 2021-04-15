@extends('layouts.admin')

{{-- Page Title --}}
@section('page-title', 'Admin Dashboard')

{{-- Page Name --}}
@section('page-name', 'Restaurants / View Restaurant')

{{-- Custom CSS --}}
@section('css')
<link rel="stylesheet" href="{{ URL::asset('vendor/fancybox/dist/fancybox.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/dataTables/css/dataTables.bootstrap4.mins.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/dataTables/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/dataTables/css/select.dataTables.min.css') }}">
@endsection

@section('js')
<script src="{{ asset('vendor/fancybox/dist/jquery.fancybox.min.js') }}"></script>
<script src="{{ URL::asset('vendor/chart.js/dist/Chart.min.js') }}"></script>
<script src="{{ URL::asset('vendor/chart.js/dist/Chart.extension.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('vendor/dataTables/js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('vendor/dataTables/js/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('vendor/dataTables/js/dataTables.select.min.js') }}"></script>
<script>
var datatables_link = "{{ route('datatable.restaurant.menu') }}";
$('#loading_screen').removeClass('d-none');
var table = $("#owner_menu_table").DataTable({
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
            d.start_date = $('#start_date').val();
            d.end_date = $('#end_date').val();
            d.category = $('#category').val();
            d.show_trash = $('#show_trash').val();
            d.restaurant_id = '{{ $restaurant["id"] }}'
        }
    },
    initComplete : function( settings, json ) {
        $('#loading_screen').addClass('d-none');
    },
    columns: [
        { data: 'name', name:'menu.name' },
        { data: 'price', name:'menu.price' },
        { data: 'category_name', name:'category.name' },
        { data: 'created_at', name: 'menu.created_at' },
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

<script>
var datatables_link1 = "{{ route('admin.restaurant.logs.datatable', ['restaurant_id' => $restaurant['id']]) }}";
var table1 = $("#activity_table").DataTable({
    autoWidth : true,
    serverSide : true,
    order: [[ 4, "desc" ]],
    dom : 
        "<'row'<'col px-0'tr>>" +
        "<'card-footer'<'row'<'col-sm-12 col-lg-6'i><'col-sm-12 col-lg-6'p>>>",
    ajax : {
        "url" : datatables_link1,
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
    table1.page.len( $(this).val() ).draw();
});

$('#filter_form1').on('submit', function(e) {
    table1.columns.adjust().draw();
    e.preventDefault();
});
</script>

<script>
var datatables_link2 = "{{ route('admin.restaurant.order.datatable', ['restaurant_id' => $restaurant['id']]) }}";
$('#loading_screen').removeClass('d-none');
var table2 = $("#order_table").DataTable({
    autoWidth : true,
    serverSide : true,
    order: [[ 2, "desc" ]],
    dom : 
        "<'row'<'col px-0'tr>>" +
        "<'card-footer'<'row'<'col-sm-12 col-lg-6'i><'col-sm-12 col-lg-6'p>>>",
    ajax : {
        "url" : datatables_link2,
        "type" : "get",
        "contentType": 'application/json',
        'data' : function (d) {
            d.show_trash = $('#show_trash2').val();
        }
    },
    initComplete : function( settings, json ) {
        $('#loading_screen').addClass('d-none');
    },
    columns: [
        { data: 'code', name:'orders.code' },
        { data: 'status', name:'sub_orders.status' },
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

$('#length_change2').val(table.page.len());

$('#length_change2').change( function() { 
    table2.page.len( $(this).val() ).draw();
});

$('#show_trash2').change(function (){
    table2.columns.adjust().draw();
});

$('#filter_form2').on('submit', function(e) {
    table2.columns.adjust().draw();
    e.preventDefault();
});
</script>

<script>
var datatables_link3 = "{{ route('admin.restaurant.report.datatable', ['restaurant_id' => $restaurant['id']]) }}";
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

@section('chart')
<script>
'use strict';
var OrdersChart = (function() {

//
// Variables
//

var $chart = $('#total-orders');
var $ordersSelect = $('[name="ordersSelect"]');


//
// Methods
//

// Init chart
function initChart($chart) {

// Create chart
var ordersChart = new Chart($chart, {
    type: 'bar',
    options: {
    scales: {
        yAxes: [{
        ticks: {
            callback: function(value) {
            if (!(value % 5)) {
                //return '$' + value + 'k'
                return value
            }
            }
        }
        }]
    },
    tooltips: {
        callbacks: {
        label: function(item, data) {
            var label = data.datasets[item.datasetIndex].label || '';
            var yLabel = item.yLabel;
            var content = '';

            if (data.datasets.length > 1) {
            content += '<span class="popover-body-label mr-auto">' + label + '</span>';
            }

            content += '<span class="popover-body-value">' + yLabel + '</span>';
            
            return content;
        }
        }
    }
    },
    data: {
    labels: {!! json_encode($total_orders['month'], true) !!},
    datasets: [{
        label: 'Sales',
        data: {!! json_encode($total_orders['data'], true) !!}
    }]
    }
});

// Save to jQuery object
$chart.data('chart', ordersChart);
}


// Init chart
if ($chart.length) {
initChart($chart);
}

})();


var SalesChart = (function() {

// Variables

var $chart = $('#total-sales');


// Methods

function init($chart) {

var salesChart = new Chart($chart, {
    type: 'line',
    options: {
    scales: {
        yAxes: [{
        gridLines: {
            color: Charts.colors.gray[900],
            zeroLineColor: Charts.colors.gray[900]
        },
        ticks: {
            callback: function(value) {
            if (!(value % 1000)) {
                return '₱ ' + value + '.00';
            }
            }
        }
        }]
    },
    tooltips: {
        callbacks: {
        label: function(item, data) {
            var label = data.datasets[item.datasetIndex].label || '';
            var yLabel = item.yLabel;
            var content = '';

            if (data.datasets.length > 1) {
            content += '<span class="popover-body-label mr-auto">' + label + '</span>';
            }

            content += '<span class="popover-body-value">₱ ' + yLabel + '.00</span>';
            return content;
        }
        }
    }
    },
    data: {
    labels: {!! json_encode($total_sales['month'], true) !!},
    datasets: [{
        label: 'Performance',
        data: {!! json_encode($total_sales['data'], true) !!}
    }]
    }
});

// Save to jQuery object

$chart.data('chart', salesChart);

};


// Events

if ($chart.length) {
init($chart);
}

})();
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
                            <h3 class="mr-auto my-auto">Restaurant Information</h3>
                            @if (!$banned)
                            <button class="btn btn-danger" data-toggle='modal' data-target='#ban_modal'><span class='fas fa-lock mr-1'></span> Ban</button>
                            @endif
                            <a class="btn btn-warning" href="{{ route('partnership.show', ['partnership' => $restaurant['id']]) }}"><span class='fas fa-file-alt mr-1'></span> View Application</a>
                            <a class="btn btn-primary" href="{{ route('admin.restaurant.index') }}"><span class='fas fa-arrow-left mr-1'></span> Go Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($banned)
                        <h1 class="text-danger text-center">Banned</h1>
                        @endif
                        <div class="row">
                            <div class="col-lg-12">
                                <h6 class="heading-small text-muted mb-4">Basic information</h6>
                            </div>
                            <div class="col-lg-4 my-auto pl-5">
                                <label class="form-control-label text-muted">Restaurant Image</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                @if (!empty($restaurant['image_name']))
                                <a data-fancybox="gallery" href="{{ route('photo.restaurant', ['slug' => $restaurant['image_name']]) }}">
                                    <img class="border" src="{{ route('photo.restaurant', ['slug' => $restaurant['image_name']]).'?size=medium' }}">
                                </a>
                                @else
                                <img src="{{ asset('img/menu/').'/alt.png' }}" class="img-fluid img-thumbnail" width="100" height="100">
                                @endif
                            </div>
                            <div class="col-lg-4 pt-3 pl-5">
                                <label class="form-control-label text-muted">ID</label>
                            </div>
                            <div class="col-lg-8 pt-3 pl-5">
                                <p>{{ $restaurant['id'] }}</p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Name</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $restaurant['name'] }}</p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Owner Name</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $restaurant['owner_fname']." ".$restaurant['owner_lname'] }}</p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Address</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $restaurant['address'] }}</p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Contact Number</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $restaurant['contact_number'] }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <h6 class="heading-small text-muted mb-4">Restaurant Settings</h6>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Flat Rate</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ !empty($restaurant['flat_rate']) ? '₱ '.$restaurant['flat_rate'].'.00' : '-' }}</p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Delivery Time</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ !empty($restaurant['eta']) ? $restaurant['eta'].'mins' : '-' }}</p>
                            </div>
                            <div class="col-lg-4 pl-5">
                                <label class="form-control-label text-muted">Open Time</label>
                            </div>
                            <div class="col-lg-8 pl-5">
                                <p>{{ $restaurant['time'] ?? '-' }}</p>
                            </div>
                        </div>
                        <hr>
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
                        <hr>
                        <div class="card shadow">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col text-left my-auto">
                                        <h3>Order List</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="card-header bg-secondary p-1 border-bottom-0">
                                <button class="btn btn-link heading-small" type="button" data-toggle="collapse" data-target="#filter2"><span class="fas fa-filter mr-1"></span>Filters</button>
                            </div>
                            <div id="filter2" class="collapse">
                            <div class="card-body border-top">
                                <div class="row">
                                    <div class="col-sm-12 col-md-4 col-lg-2">
                                        <div class="form-group">
                                            <label class="form-control-label">Shown rows</label>
                                            <select class="form-control form-control-sm" name="length_change2" id="length_change2">
                                                <option value='10'>10</option>
                                                <option value='25'>25</option>
                                                <option value='50'>50</option>
                                                <option value='100'>100</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-4 col-lg-2">
                                        <div class="form-group">
                                            <label class="form-control-label">Show</label>
                                            <select class="form-control form-control-sm" name="show_trash2" id="show_trash2">
                                                <option value='all'>All</option>
                                                <option value='process'>On Process</option>
                                                <option value='completed'>Completed</option>
                                                <option value='rejected'>Rejected</option>
                                                <option value='cancelled'>Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-flush align-items-center" id="order_table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Code</th>
                                            <th>Status</th>
                                            <th>Date Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <hr>
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
                                            <th>Order #</th>
                                            <th>Status</th>
                                            <th>Date Submitted</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <hr>
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
                        {{-- <div class="row">
                            <div class="col-lg-12">
                                <h6 class="heading-small text-muted mb-4">Restaurant Performance</h6>
                            </div>
                            <div class="col-xl-12 pl-5">
                                <div class="card bg-gradient-default shadow">
                                    <div class="card-header bg-transparent">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="text-uppercase text-light ls-1 mb-1">Overview</h6>
                                                <h2 class="text-white mb-0">Sales value</h2>
                                            </div>
                                        </div>
                                    </div>
                                        <div class="card-body">
                                        <!-- Chart -->
                                            <div class="chart">
                                                <!-- Chart wrapper -->
                                                <canvas id="total-sales" class="chart-canvas"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <div class="col-xl-12 mt-3 pl-5">
                                <div class="card shadow">
                                    <div class="card-header bg-transparent">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="text-uppercase text-muted ls-1 mb-1">Performance</h6>
                                                <h2 class="mb-0">Total orders</h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Chart -->
                                        <div class="chart">
                                            <canvas id="total-orders" class="chart-canvas"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div> 
        </div>
        @include('owner.inc.footer')
    </div>
</div>
@if (!$banned)
<div class="modal fade" id="ban_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ban User Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
        </div>
            <div class="modal-body bg-secondary">
                {!! Form::open(['route' => 'ban.store']) !!}
                    <label class="form-control-label">Reason for banning this user</label>
                    <div class="form-group mb-3">
                    <textarea class="form-control form-control-alternative" style="resize:none;" placeholder="Enter your reason here" rows="7"
                    name="ban_reason" id="ban_reason" cols="50" required></textarea>
                    <input type="hidden" name="user_id" value="{{ $user_id }}">
                </div>
            </div>
            <div class="modal-footer mx-auto">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endif
@endsection
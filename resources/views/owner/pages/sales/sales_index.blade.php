@extends('layouts.owner') {{-- Page Title --}} 
@section('page-title', 'Sales') {{-- Page Name --}} 
@section('page-name', 'Sales') {{-- Custom CSS --}} 
@section('css')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/dataTables/css/dataTables.bootstrap4.mins.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/dataTables/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/dataTables/css/select.dataTables.min.css') }}">

<style>
.datepicker table tr td span.active{
    background: #04c!important;
    border-color: #04c!important;
}
.datepicker .datepicker-days tr td.active {
    background: #04c!important;
}
#week-picker-wrapper .datepicker .datepicker-days tr td.active~td, #week-picker-wrapper .datepicker .datepicker-days tr td.active {
    color: #fff;
    background-color: #04c;
    border-radius: 0;
}

#week-picker-wrapper .datepicker .datepicker-days tr:hover td, #week-picker-wrapper .datepicker table tr td.day:hover, #week-picker-wrapper .datepicker table tr td.focused {
    color: #000!important;
    background: #e5e2e3!important;
    border-radius: 0!important;
}
</style>

@endsection

@section('js')
<script type="text/javascript" src="{{ URL::asset('vendor/dataTables/js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('vendor/dataTables/js/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('vendor/dataTables/js/dataTables.select.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
var datatables_link = "{{ route('owner.sales.datatable') }}";
$('#loading_screen').removeClass('d-none');
var radio_filter = "specific";
var table = $("#menu_sales").DataTable({
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
            d.radio_filter = radio_filter;
            d.specific_date = $('#specific_date').val();
            d.start_range = $('#start_range').val();
            d.end_range = $('#end_range').val();
        },
        "complete" : function(response) {
            $('#menu_print_report').attr('href', response.responseJSON.export_link);
        }
    },
    "fnDrawCallback": function() {
        var api = this.api()
        var json = api.ajax.json();
        var total_sales = 0;
        var total_orders = 0;
        for (var i = 0; i < json.data.length; i++) {
            var sale = json.data[i]['sales'];
            var order = json.data[i]['orders'];
            var sales = sale.substr(2, sale.length-5);
            total_sales += parseInt(sales);
            total_orders += parseInt(order);
        }

        $(api.column(1).footer()).html("₱ "+total_sales+".00");
        $(api.column(2).footer()).html(total_orders);
    },
    initComplete : function( settings, json ) {
        $('#loading_screen').addClass('d-none');
    },
    columns: [
        { data: 'item_name', name:'item_name' },
        { data: 'sales', name:'sales' },
        { data: 'orders', name:'orders' },
        { data: 'percentage', name:'percentage' },
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

$('input[name=filter_radio]').on('change', function(e) {
    var value = this.value;
    var $container = $('#'+value);
    var $specific = $('#specific');
    var $range = $('#range');

    $specific.addClass('d-none');
    $range.addClass('d-none');
    $container.removeClass('d-none');
    radio_filter = this.value;
    table.columns.adjust().draw();
}); 

$('.datepicker').on('change', function() {
    table.columns.adjust().draw();
}); 

var datatables_link1 = "{{ route('owner.sales.restaurant.datatable') }}";
$('#loading_screen').removeClass('d-none');
var interval = "daily";
var table1 = $("#restaurant_sales").DataTable({
    autoWidth : true,
    serverSide : true,
    order: [[ 0, "asc" ]],
    dom : 
        "<'row'<'col px-0'tr>>" +
        "<'card-footer'<'row'<'col-sm-12 col-lg-6'i><'col-sm-12 col-lg-6'p>>>",
    ajax : {
        "url" : datatables_link1,
        "type" : "get",
        "contentType": 'application/json',
        'data' : function (d) {
            d.interval = interval;
            d.daily_input = $('#daily_input').val();
            d.weekly_input = $('#weekly_input').val();
            d.monthly_input = $('#monthly_input').val();
            d.yearly_input = $('#yearly_input').val();
        },
        "complete" : function(response) {
            $('#restaurant_print_report').attr('href', response.responseJSON.export_link);
        }
    },
    "fnDrawCallback": function() {
        var api = this.api()
        var json = api.ajax.json();
        var total_sales = 0;
        var total_orders = 0;
        for (var i = 0; i < json.data.length; i++) {
            var sale = json.data[i]['sales'];
            var order = json.data[i]['orders'];
            var sales = sale.substr(2, sale.length-5);
            total_sales += parseInt(sales);
            total_orders += parseInt(order);
        }

        $(api.column(1).footer()).html("₱ "+total_sales+".00");
        $(api.column(2).footer()).html(total_orders);
    },
    initComplete : function( settings, json ) {
        $('#loading_screen').addClass('d-none');
    },
    columns: [
        { data: 'date', name:'date' },
        { data: 'sales', name:'sales' },
        { data: 'orders', name:'orders' },
        { data: 'action', name:'action', orderable: false, searchable: false },
    ],
    "fnDrawCallback": function() {
        var api = this.api()
        var json = api.ajax.json();
        var total_sales = 0;
        var total_orders = 0;
        for (var i = 0; i < json.data.length; i++) {
            var sale = json.data[i]['sales'];
            var order = json.data[i]['orders'];
            var sales = sale.substr(2, sale.length-5);
            total_sales += parseInt(sales);
            total_orders += parseInt(order);
        }

        $(api.column(1).footer()).html("₱ "+total_sales+".00");
        $(api.column(2).footer()).html(total_orders);
    },
    oLanguage : {
        oPaginate : {
            sNext : '<i class="fas fa-angle-right"></i>',
            sPrevious : '<i class="fas fa-angle-left"></i>'
        }
    },
});


$('#length_change1').val(table.page.len());

$('#length_change1').change( function() { 
    table1.page.len( $(this).val() ).draw();
});

var options1 = {
    disableTouchKeyboard: true,
    autoclose: false,
    endDate: '{{ \Carbon\Carbon::now()->format("m-d-Y") }}',
    format: 'mm-dd-yyyy'
};
$(".daily").datepicker(options1);

var options2 = {
    disableTouchKeyboard: true,
    autoclose: false,
    format: 'mm-yyyy',
    minViewMode: 'months',
    startDate: '{{ \App\SubOrder::getRestaurantSubOrderDate(session()->get("restaurant_id"), "monthly") }}',
    endDate: '{{ \Carbon\Carbon::now()->format("m-Y") }}'
};
$(".monthly").datepicker(options2);

var options3 = {
    disableTouchKeyboard: true,
    autoclose: false,
    format: 'yyyy',
    viewMode: "years",
    minViewMode: 'years',
    maxViewMode: 'years',
    startDate: '{{ \App\SubOrder::getRestaurantSubOrderDate(session()->get("restaurant_id"), "yearly") }}',
    endDate: '{{ \Carbon\Carbon::now()->format("Y") }}'
};
$(".yearly").datepicker(options3);

$('input[name=interval]').on('change', function(e) {
    value = this.value;
    var $container = $('#'+value);
    var $daily = $('#daily');
    var $weekly = $('#week-picker-wrapper');
    var $monthly = $('#monthly');
    var $yearly = $('#yearly');

    $daily.addClass('d-none');
    $weekly.addClass('d-none');
    $monthly.addClass('d-none');
    $yearly.addClass('d-none');
    $container.removeClass('d-none');
    interval = value;
    table1.columns.adjust().draw();
}); 

$('.daily, .monthly, yearly').change(function (e) {
    table1.columns.adjust().draw();
});

var weekpicker, start_date, end_date;
var first_run = true;

function set_week_picker(date) {
    start_date = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay());
    end_date = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 6);
    weekpicker.datepicker('update', start_date);
    weekpicker.val((start_date.getMonth() + 1) + '/' + start_date.getDate() + '/' + start_date.getFullYear() + ' - ' + (end_date.getMonth() + 1) + '/' + end_date.getDate() + '/' + end_date.getFullYear());
    if (!first_run) {
        table1.columns.adjust().draw();
        first_run = false;
    }
}

$(document).ready(function() {
    weekpicker = $('.week-picker');
    weekpicker.datepicker({
        autoclose: true,
        forceParse: false,
        container: '#week-picker-wrapper',
    }).on("changeDate", function(e) {
        set_week_picker(e.date);
    });
    $('.week-prev').on('click', function() {
        var prev = new Date(start_date.getTime());
        prev.setDate(prev.getDate() - 1);
        set_week_picker(prev);
    });
    $('.week-next').on('click', function() {
        var next = new Date(end_date.getTime());
        next.setDate(next.getDate() + 1);
        set_week_picker(next);
    });
    set_week_picker(new Date);
});

</script>
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
            <div class="col-12">
                <div class="card shadow">     
                    <div class="card-header">
                        <div class="d-flex">
                            <div class="text-left my-auto mr-auto">
                                <h3>Menu Sales</h3>
                            </div>
                            <a class="btn btn-success" id="menu_print_report" href="#">Print</a>
                        </div>
                    </div>
                    <div class="card-header bg-secondary p-1 border-bottom-0">
                        <button class="btn btn-link heading-small" type="button" data-toggle="collapse" data-target="#filter"><span class="fas fa-filter mr-1"></span>Filters</button>
                    </div>
                    <div id="filter" class="collapse">
                            <div class="card-body bg-secondary border-top">
                    <div class="row">
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="custom-control custom-radio">
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <input name="filter_radio" class="custom-control-input" id="filter_radio1" value="specific" type="radio" checked>
                                            <label class="custom-control-label" for="filter_radio1">Specific Date</label>
                                        </div>
                                    </div>
                                    <div class="offset-1 col-auto">
                                        <div class="form-group">
                                            <input name="filter_radio" class="custom-control-input" id="filter_radio2" value="range" type="radio">
                                            <label class="custom-control-label" for="filter_radio2">Date Range</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="specific">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                    </div>
                                    <input class="form-control datepicker" name="specific_date" id="specific_date" data-date-start-date="{{ \App\Order::getOldest() ?? \Carbon\Carbon::now()->format('m-d-Y') }}"
                                        data-date-end-date="0d" placeholder="Select date" type="text" value="{{ \Carbon\Carbon::now()->format('m-d-Y') }}">
                                </div>
                            </div>
                            <div class="form-group d-none" id="range">
                                <div class="input-daterange datepicker row align-items-center" data-date-start-date="{{ \App\Order::getOldest() ?? \Carbon\Carbon::now()->format('m-d-Y') }}"
                                    data-date-end-date="0d">
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="input-group input-group-alternative">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                </div>
                                                <input class="form-control" name="start_range" id="start_range" placeholder="Start date" type="text" value="{{ \Carbon\Carbon::now()->format('m-d-Y') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="input-group input-group-alternative">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                </div>
                                                <input class="form-control" name="end_range" id="end_range" placeholder="End date" type="text" value="{{ \Carbon\Carbon::now()->format('m-d-Y') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                        <table class="table table-flush align-items-center" id="menu_sales">
                            <thead class="thead-light">
                                <tr>
                                    <th>Menu Name</th>
                                    <th>Total Sales</th>
                                    <th>Total Orders</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 pt-3">
                <div class="card shadow">     
                    <div class="card-header">
                        <div class="d-flex">
                            <div class="text-left my-auto mr-auto">
                                <h3>Restaurant Performance</h3>
                            </div>
                            <a class="btn btn-success" id="restaurant_print_report" href="#">Print</a>
                        </div>
                    </div>
                    <div class="card-header bg-secondary p-1 border-bottom-0">
                        <button class="btn btn-link heading-small" type="button" data-toggle="collapse" data-target="#filter1"><span class="fas fa-filter mr-1"></span>Filters</button>
                    </div>
                    <form id="filter_form1" class="m-0" method="POST">
                        <div id="filter1" class="collapse">
                            <div class="card-body bg-secondary border-top">
                                <div class="row">
                                </div>
                    </form>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="custom-control custom-radio">
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <input name="interval" class="custom-control-input" id="interval_daily" value="daily" type="radio" checked>
                                            <label class="custom-control-label" for="interval_daily">Day</label>
                                        </div>
                                    </div>
                                    <div class="offset-1 col-auto">
                                        <div class="form-group">
                                            <input name="interval" class="custom-control-input" id="interval_weekly" value="week-picker-wrapper" type="radio">
                                            <label class="custom-control-label" for="interval_weekly">Week</label>
                                        </div>
                                    </div>
                                    <div class="offset-1 col-auto">
                                        <div class="form-group">
                                            <input name="interval" class="custom-control-input" id="interval_monthly" value="monthly" type="radio">
                                            <label class="custom-control-label" for="interval_monthly">Month</label>
                                        </div>
                                    </div>
                                    <div class="offset-1 col-auto">
                                        <div class="form-group">
                                            <input name="interval" class="custom-control-input" id="interval_yearly" value="yearly" type="radio">
                                            <label class="custom-control-label" for="interval_yearly">Year</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group" id="daily">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                    </div>
                                    <input class="form-control daily" name="daily_input" id="daily_input"
                                        value="{{ \Carbon\Carbon::now()->format("m-d-Y") }}" placeholder="Select date" type="text">
                                </div>
                            </div>
                            <div class="form-group d-none" id="week-picker-wrapper">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                    </div>
                                    <input type="text" class="form-control week-picker" placeholder="Select a Week" name="weekly_input" id="weekly_input" 
                                    data-date-end-date="0d">
                                </div>
                            </div>
                            <div class="form-group d-none" id="monthly">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                    </div>
                                    <input type="text" class="form-control monthly" placeholder="Select Month and Year" name="monthly_input" id="monthly_input" 
                                    value="{{ \Carbon\Carbon::now()->format("m-Y") }}">
                                </div>
                            </div>
                            <div class="form-group d-none" id="yearly">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                    </div>
                                    <input type="text" class="form-control yearly" placeholder="Select a Year" name="yearly_input" id="yearly_input" 
                                    value="{{ \Carbon\Carbon::now()->format("Y") }}">
                                </div>
                            </div>
                        </div>
                    </div>
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
                    </div>
                    </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center" id="restaurant_sales">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Total Sales</th>
                                    <th>Total Orders</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @include('owner.inc.footer')
    </div>
</div>
@endsection
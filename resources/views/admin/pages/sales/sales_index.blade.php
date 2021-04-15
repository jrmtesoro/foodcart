@extends('layouts.admin') {{-- Page Title --}} 
@section('page-title', 'Sales') {{-- Page Name --}} 
@section('page-name', 'Sales') {{-- Custom CSS --}} 
@section('css')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/dataTables/css/dataTables.bootstrap4.mins.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/dataTables/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/dataTables/css/select.dataTables.min.css') }}">
@endsection

@section('js')
<script type="text/javascript" src="{{ URL::asset('vendor/dataTables/js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('vendor/dataTables/js/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('vendor/dataTables/js/dataTables.select.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
var datatables_link = "{{ route('datatable.admin.sales') }}";
$('#loading_screen').removeClass('d-none');
var radio_filter = "specific";
var table = $("#restaurant_sales").DataTable({
    autoWidth : true,
    serverSide : true,
    order: [[ 2, "desc" ]],
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
            d.radio_filter = radio_filter;
            d.specific_date = $('#specific_date').val();
            d.start_range = $('#start_range').val();
            d.end_range = $('#end_range').val();
        },
        "complete" : function(response) {
            $('#restaurant_pdf').attr('href', response.responseJSON.export_link);
        }
    },
    "fnDrawCallback": function() {
        var api = this.api()
        var json = api.ajax.json();
        var total_sales = 0;
        var total_orders = 0;
        var total_web = 0;
        var total_app = 0;
        for (var i = 0; i < json.data.length; i++) {
            var sale = json.data[i]['sales'];
            var order = json.data[i]['orders'];
            var sales = sale.substr(2, sale.length-5);
            total_sales += parseInt(sales);
            total_orders += parseInt(order);
            total_web += parseInt(json.data[i]['web_order']);
            total_app += parseInt(json.data[i]['app_order']);
        }

        $(api.column(2).footer()).html("₱ "+total_sales+".00");
        $(api.column(3).footer()).html(total_orders);
        $(api.column(4).footer()).html(total_web);
        $(api.column(5).footer()).html(total_app);
    },
    initComplete : function( settings, json ) {
        $('#loading_screen').addClass('d-none');
    },
    columns: [
        { data: 'id', name:'restaurant.id' },
        { data: 'name', name:'restaurant.name' },
        { data: 'sales', name:'sales' },
        { data: 'orders', name:'orders' },
        { data: 'web_order', name:'web_order' },
        { data: 'app_order', name:'app_order' },
        { data: 'action', name:'action', orderable: false, searchable: false },
    ],
    oLanguage : {
        oPaginate : {
            sNext : '<i class="fas fa-angle-right"></i>',
            sPrevious : '<i class="fas fa-angle-left"></i>'
        }
    },
});

var datatables_link1 = "{{ route('datatable.admin.sales1') }}";
var radio_filter1 = "specific";
var table1 = $("#menu_sales").DataTable({
    autoWidth : true,
    serverSide : true,
    order: [[ 2, "desc" ]],
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
            d.radio_filter = radio_filter1;
            d.specific_date = $('#specific_date1').val();
            d.start_range = $('#start_range1').val();
            d.end_range = $('#end_range1').val();
        },
        "complete" : function(response) {
            $('#menu_pdf').attr('href', response.responseJSON.export_link);
        }
    },
    initComplete : function( settings, json ) {
        $('#loading_screen').addClass('d-none');
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

        $(api.column(2).footer()).html("₱ "+total_sales+".00");
        $(api.column(3).footer()).html(total_orders);
    },
    columns: [
        { data: 'item_name', name:'item_name' },
        { data: 'restaurant_name', name:'restaurant_name' },
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

$('#origin').change( function() { 
    table.columns.adjust().draw();
});


$('#filter_form').on('submit', function(e) {
    table.columns.adjust().draw();
    e.preventDefault();
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


$('#length_change1').val(table.page.len());

$('#length_change1').change( function() { 
    table1.page.len( $(this).val() ).draw();
});

$('#filter_form1').on('submit', function(e) {
    table1.columns.adjust().draw();
    e.preventDefault();
});

$('input[name=filter_radio1]').on('change', function(e) {
    var value = this.value;
    var $container = $('#'+value+"1");
    var $specific = $('#specific1');
    var $range = $('#range1');

    $specific.addClass('d-none');
    $range.addClass('d-none');
    $container.removeClass('d-none');
    radio_filter1 = this.value;
    table1.columns.adjust().draw();
}); 


$('.datepicker').change(function (e) {
    var target = e.target.id;
    var restaurant_sales = ["specific_date", "start_range", "end_range"];
    if (restaurant_sales.indexOf(target) != -1) {
        table.columns.adjust().draw();
    } else {
        table1.columns.adjust().draw();
    }
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
            <div class="col-12">
                <div class="card shadow">     
                    @include('admin.pages.sales.restaurant_sales')
                </div>
            </div>
        </div>
        <div class="row pt-5">
            <div class="col-12">
                <div class="card shadow">     
                    @include('admin.pages.sales.menu_sales')
                </div>
            </div>
        </div>
    @include('owner.inc.footer')
    </div>
</div>
@endsection
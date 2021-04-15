@extends('layouts.owner') {{-- Page Title --}} 
@section('page-title', 'Owner Dashboard') {{-- Page Name --}} 
@section('page-name','Orders') {{-- Custom CSS --}} 
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
@if (Request::has('status'))
$('#show_trash > option').each(function() {
    if ($(this).val() == "{{Request::get('status')}}") {
        $('#show_trash').val($(this).val());
    }
});
@endif

@if (Request::has('sales'))
$('#url_search').prop('checked', true);
var sub_orders = "{{Request::get('sales')}}";
var checked = true;
@else
var sub_orders = null;
var checked = false;
@endif

var datatables_link = "{{ route('datatable.order') }}";
$('#loading_screen').removeClass('d-none');
var table = $("#order_table").DataTable({
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
            d.show_trash = $('#show_trash').val();
            if (checked) {
                d.sales = sub_orders;
            }
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

$('#length_change').val(table.page.len());

$('#length_change').change( function() { 
    table.page.len( $(this).val() ).draw();
});

$('#show_trash').change(function (){
    table.columns.adjust().draw();
});

$('#filter_form').on('submit', function(e) {
    table.columns.adjust().draw();
    e.preventDefault();
});

$('#url_search').change(function (e) {
    checked = $(this).prop('checked');

    if (checked) {
        $(this).attr('checked', 'checked');
    } else {
        $(this).removeAttr('checked');
    }
    table.columns.adjust().draw();
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
            <div class="col">
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
                    <div id="filter" class="collapse">
                    <div class="card-body border-top">
                        <div class="row">
                            <div class="col-sm-12 col-md-4 col-lg-2">
                                <div class="form-group">
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
                                <div class="form-group">
                                    <label class="form-control-label">Show</label>
                                    <select class="form-control form-control-sm" name="show_trash" id="show_trash">
                                        <option value='process'>On Process</option>
                                        <option value='completed'>Completed</option>
                                        <option value='rejected'>Rejected</option>
                                        <option value='cancelled'>Cancelled</option>
                                        <option value='all'>All</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-4 col-lg-2">
                                <div class="custom-control custom-checkbox mb-3">
                                    <input class="custom-control-input" id="url_search" type="checkbox">
                                    <label class="custom-control-label" for="url_search">Use URL search</label>
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
            </div>
        </div>
    @include('owner.inc.footer')
    </div>
</div>
@endsection
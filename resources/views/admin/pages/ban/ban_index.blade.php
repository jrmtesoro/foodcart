@extends('layouts.admin') {{-- Page Title --}} 
@section('page-title', 'Admin Dashboard') {{-- Page Name --}} 
@section('page-name', 'Ban') {{-- Custom CSS --}} 
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
var datatables_link = "{{ route('datatable.ban') }}";
$('#loading_screen').removeClass('d-none');
var table = $("#ban_table").DataTable({
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
            d.show_trash = $('#show_trash').val();
        }
    },
    initComplete : function( settings, json ) {
        $('#loading_screen').addClass('d-none');
    },
    columns: [
        { data: 'id', name:'user.id' },
        { data: 'email', name:'user.email' },
        { data: 'created_at', name: 'ban.created_at' },
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

$('#filter_form').on('submit', function(e) {
    table.columns.adjust().draw();
    e.preventDefault();
});
</script>

<script>
$('#lift_ban_confirmation').on('show.bs.modal', function (e) {
    var lift_route = e.relatedTarget.dataset.route;
    $('#lift_route').val(lift_route);
})
</script>
<script>
$('#lift_ban_form').submit(function (e){
    e.preventDefault;
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
            'Accept' : 'application/json'
        },
        url: $('#lift_route').val(),
        method: 'GET',
        success: function(result){
            if (!result.success) {
                Swal.fire({
                    type: 'error',
                    title: 'Lift Ban Failed',
                    text: result.message
                });
                $('#lift_ban_confirmation').modal('hide');
            } else {
                Swal.fire({
                    type: 'success',
                    title: 'Lift Ban Success',
                    text: result.message
                });
                $('#lift_ban_confirmation').modal('hide');
                table.columns.adjust().draw();
            }
        }
    });
    return false;
});
</script>

<script>
$('#view_details').on('show.bs.modal', function (e) {
    var dataset = e.relatedTarget.dataset
    var reason = dataset.reason;
    var email_address = dataset.email_address;
    var view = dataset.view;

    $('#email_address').html("<p style='word-wrap: break-word;'>"+email_address+"</p>");
    $('#reason').html("<p style='word-wrap: break-word;'>"+reason+"</p>");

    $('#view_profile').attr('href', view);
})
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
                                <h3>Ban List</h3>
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
                                                <option value='user.id'>ID</option>
                                                <option value='user.email'>Email Address</option>
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
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center" id="ban_table">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Email Address</th>
                                    <th>Date Banned</th>
                                    <th>Options</th>
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
<div class="modal fade" id="lift_ban_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lift Ban Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to lift the ban of this user?
            </div>
            <div class="modal-footer">
                <form id="lift_ban_form">
                    <input type="hidden" name="lift_route" id="lift_route">
                    <button type="submit" class="btn btn-primary">Yes</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="view_details" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">View Details</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-secondary">
                <div class="row">
                    <div class="col-md-3">
                        <h6 class="heading-small"><strong>Email Address</strong></h6>
                    </div>
                    <div class="col-md-1">
                        :
                    </div>
                    <div class="col-md-8" id="email_address">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h6 class="heading-small"><strong>Reason</strong></h6>
                    </div>
                    <div class="col-md-1">
                        :
                    </div>
                    <div class="col-md-8" id="reason">
                    </div>
                </div>
                <div class="row pt-3">
                    <div class="col-md-12 text-center">
                        <a href="#" class="heading-small" id="view_profile"><strong>View Profile</strong></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
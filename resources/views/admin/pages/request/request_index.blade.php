@extends('layouts.admin') {{-- Page Title --}} 
@section('page-title', 'Admin Dashboard') {{-- Page Name --}} 
@section('page-name', 'Request') {{-- Custom CSS --}} 
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
<script type="text/javascript" src="{{ URL::asset('vendor/jquery-validation/jquery.validate.min.js') }}"></script>

<script>
var datatables_link = "{{ route('datatable.changerequest') }}";
$('#loading_screen').removeClass('d-none');
var table = $("#request_change_table").DataTable({
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
        { data: 'old_email', name:'change_request.old_email' },
        { data: 'new_email', name:'change_request.new_email' },
        { data: 'status', name:'change_request.status' },
        { data: 'created_at', name: 'change_request.created_at' },
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
</script>

<script>
$('#show_request').on('show.bs.modal', function (e) {
    var dataset = e.relatedTarget.dataset
    var reason = dataset.reason;
    var old_email = dataset.old;
    var new_email = dataset.new;
    var status = dataset.status;
    var view = dataset.view;

    $('#old_email').html("<p style='word-wrap: break-word;'>"+old_email+"</p>");
    $('#new_email').html("<p style='word-wrap: break-word;'>"+new_email+"</p>");
    $('#reason').html("<p style='word-wrap: break-word;'>"+reason+"</p>");
    var color = 'danger';
    var title = 'rejected';

    if (status == 0) {
        color = 'primary';
        title= 'pending';
    } else if (status == 1) {
        color = 'success';
        title= 'accepted';
    }
    $('#status').html('<span class="badge badge-dot"><i class="bg-'+color+'"></i> '+title+'</span>');

    $('#request_view').attr('href', view);
})
</script>

<script>
$('#accept_confirmation').on('show.bs.modal', function (e) {
    var request_id = e.relatedTarget.dataset.id;

    $('#accept_id').val(request_id);
})
</script>
<script>
$('#accept_form').submit(function (e){
    e.preventDefault;
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
            'Accept' : 'application/json'
        },
        url: "{{ url('admin/request/') }}/"+$('#accept_id').val()+"/accept",
        method: 'GET',
        success: function(result){
            if (!result.success) {
                Swal.fire({
                    type: 'error',
                    title: 'Accept Request Failed',
                    text: result.message
                });
                $('#accept_confirmation').modal('hide');
            } else {
                Swal.fire({
                    type: 'success',
                    title: 'Accept Request Success',
                    text: result.message
                });
                $('#accept_confirmation').modal('hide');
                table.columns.adjust().draw();
            }
        }
    });
    return false;
});
</script>
<script>
$('#reject_confirmation').on('show.bs.modal', function (e) {
    var req_id = e.relatedTarget.dataset.id;

    $('#reject_id').val(req_id);
})
</script>
<script>
$('#reject_form').submit(function (e){
    e.preventDefault;
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
            'Accept' : 'application/json'
        },
        url: "{{ url('admin/request/') }}/"+$('#reject_id').val()+"/reject",
        method: 'GET',
        success: function(result){
            if (!result.success) {
                Swal.fire({
                    type: 'error',
                    title: 'Reject Tag Failed',
                    text: result.message
                });
                $('#reject_confirmation').modal('hide');
            } else {
                Swal.fire({
                    type: 'success',
                    title: 'Reject Tag Success',
                    text: result.message
                });
                $('#reject_confirmation').modal('hide');
                table.columns.adjust().draw();
            }
        }
    });
    return false;
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
                                <h3>Request List</h3>
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
                                                <option value='change_request.old_email'>Old Email Address</option>
                                                <option value='change_request.new_email'>New Address Address</option>
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
                                            <option value='0'>Pending</option>
                                            <option value='1'>Accepted</option>
                                            <option value='2'>Rejected</option>
                                            <option value='all'>All</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center" id="request_change_table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Old Email Address</th>
                                    <th>New Email Address</th>
                                    <th>Status</th>
                                    <th>Date Submitted</th>
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

<div class="modal fade" id="show_request" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">View Request</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-secondary">
                <div class="row">
                    <div class="col-md-3">
                        <h6 class="heading-small"><strong>Old</strong></h6>
                    </div>
                    <div class="col-md-1">
                        :
                    </div>
                    <div class="col-md-8" id="old_email">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h6 class="heading-small"><strong>New</strong></h6>
                    </div>
                    <div class="col-md-1">
                        :
                    </div>
                    <div class="col-md-8" id="new_email">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h6 class="heading-small"><strong>Status</strong></h6>
                    </div>
                    <div class="col-md-1">
                        :
                    </div>
                    <div class="col-md-8" id="status">
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
                        <a href="#" class="heading-small" id="request_view"><strong>View Profile</strong></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="accept_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Accept Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
            </div>
            <div class="modal-body">
                Are you sure you want to accept this request?
            </div>
            <div class="modal-footer">
                <form method="post" id="accept_form">
                    <input type="hidden" name="accept_id" id="accept_id">
                    <button type="submit" class="btn btn-primary">Yes</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reject_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
            </div>
            <div class="modal-body">
                Are you sure you want to reject this request?
            </div>
            <div class="modal-footer">
                <form method="post" id="reject_form">
                    <input type="hidden" name="reject_id" id="reject_id">
                    <button type="submit" class="btn btn-primary">Yes</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>
@endsection
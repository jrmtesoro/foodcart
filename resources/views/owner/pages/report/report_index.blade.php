@extends('layouts.owner') {{-- Page Title --}} 
@section('page-title', 'Owner Dashboard') {{-- Page Name --}} 
@section('page-name', 'Report') {{-- Custom CSS --}} 

@section('css')
<style>

a.card {
    transition: all .3s ease;
}
a.card:hover {
    background-color: #11cdef;
}
</style>
@endsection

@section('js')
@endsection

@section('content')
@include('owner.inc.sidebar')
<div class="main-content">
    @include('owner.inc.navbar')
    <div class="header bg-gradient-info pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
        </div>
    </div>
    <div class="container mt--9 bg-secondary">
        <div class="row">
            <div class="col-12">
                <div class="card shadow bg-secondary">
                    <div class="card-header">
                        <h3>Reports</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if (!empty($reports))
                                @foreach ($reports as $report)
                                <div class="col-lg-4">
                                    <a class="card shadow" href="{{ route('report.owner.show', ['report_code' => $report['code']]) }}">
                                        <div class="card-body">
                                            <p class="h3 mb-0">Ticket # {{ $report['code'] }}</p>
                                            <small>Status : 
                                                @if ($report['status'] == 0)
                                                <span class="heading-small font-weight-bold text-success">open<span>
                                                @elseif ($report['status'] == 1)
                                                <span class="heading-small font-weight-bold text-info">under investigation<span>
                                                @else
                                                <span class="heading-small font-weight-bold text-danger">closed<span>
                                                @endif
                                            </small>
                                        </div>
                                    </a>
                                </div>
                                @endforeach
                            @else
                                <div class="col-lg-12 text-center">
                                    <hr>
                                    <p class="h1">No Report Found</p>
                                    <hr>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
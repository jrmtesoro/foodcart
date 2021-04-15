@extends('layouts.guest') 
@section('page-title', 'Pinoy Food Cart') 

@section('css')
<style>
a.material-icons {
    border-radius: 50%;
    background-color: #11cdef;
    color: white;
    font-weight: 900;
    padding: 0.6rem;
    transition: transform .5s cubic-bezier(.215, .61, .355, 1);
}

a.material-icons:hover {
    box-shadow: 0 0 8px 3px #e9e9eb;
    transform: scale3d(1.2, 1.2, 1);
}

</style>
@endsection
 
@section('js')
@endsection
 
@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="card  bg-light">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <h3 class="title">Order History</h3>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        @if (empty($order_history))
                        <div class="mx-auto">
                            <h3 class="title">No Orders found</h3>
                        </div>
                        @else
                        @foreach ($order_history as $order)
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <p class="h4 title my-auto mr-auto">
                                            {{ $order['date'] }}
                                        </p>
                                        <a href="{{ route('order.show', ['code' => $order['code']]) }}" class="material-icons my-auto">arrow_forward</a>
                                    </div>
                                    @if ($order['status'] == 0)
                                    <p class="h6 title my-0">Status : <span class="text-primary">Pending</span></p>
                                    @elseif ($order['status'] == 1)
                                    <p class="h6 title my-0">Status : <span class="text-warning">Processing</span></p>
                                    @elseif ($order['status'] == 2)
                                    <p class="h6 title my-0">Status : <span class="text-info">Delivering</span></p>
                                    @elseif ($order['status'] == 3)
                                    <p class="h6 title my-0">Status : <span class="text-success">Completed</span></p>
                                    @elseif ($order['status'] == 4)
                                    <p class="h6 title my-0">Status : <span class="text-danger">Rejected</span></p>
                                    @elseif ($order['status'] == 5)
                                    <p class="h6 title my-0">Status : <span class="text-danger">Cancelled</span></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="footer mt-5 text-light w-100" style="background-color: #000;">
    <div class="container">
        <nav class="float-left">
            <ul>
                <li>
                    <a href="https://www.pinoyfoodcart.com">
                        Pinoy Food Cart
                    </a>
                </li>
            </ul>
        </nav>
        <div class="float-right">
            <img src="{{ asset('img/google_play.png') }}" width="250" height="100">
        </div>
    </div>
</footer>
@endsection
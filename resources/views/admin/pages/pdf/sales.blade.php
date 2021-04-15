<!DOCTYPE html>
<html>

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="{{ URL::asset('vendor/bootstrap-4.2/css/bootstrap.min.css') }}">
    <style>
        .chart {
            width: 900px;
            height: 500px;
            margin: 0 auto
        }
        .page-break {
            page-break-after: always;
        }
    </style>
    <script type="text/javascript" src="{{ asset('js/charts/loader.js') }}"></script>

    <script type="text/javascript">
        function init() {

            google.load("visualization", "1.1", {
                packages: ["corechart", "line"],
                callback: 'drawCharts'
            });
        }

        function drawCharts() {

            var data = google.visualization.arrayToDataTable([
                ['Origin', 'Count'],
                ['Web Orders', {!! $data['total_web'] !!}],
                ['App Orders', {!! $data['total_app'] !!}]
            ]);

            var options = {
            title: 'Restaurant Orders'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));

            chart.draw(data, options);
        }
    </script>
</head>

<body onload="init()">
    <div class="container-fluid">
        <h1>Restaurant Sales Report</h1>
        <br>
        <h3>{!! $data['header']['dates'] !!}<h3>
        <h3>Pinoy Food Cart</h3>
        <br><br><br>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Restaurant ID</th>
                    <th scope="col">Restaurant Name</th>
                    <th scope="col">Total Sales</th>
                    <th scope="col">Total Transactions</th>
                    <th scope="col">Web Orders</th>
                    <th scope="col">App Orders</th>
                </tr>
            </thead>
            <tbody>
                @php($count = 0)
                @foreach ($data['table'] as $row)
                @if ($count == 19)
                    </tbody>
                    </table>
                    <div class="page-break"></div>
                    <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">Restaurant ID</th>
                        <th scope="col">Restaurant Name</th>
                        <th scope="col">Total Sales</th>
                        <th scope="col">Total Transactions</th>
                        <th scope="col">Web Orders</th>
                        <th scope="col">App Orders</th>
                    </tr>
                    </thead>
                    <tbody>
                @endif
                <tr>
                    <th scope="row">{{ $row->id }}</th>
                    <th scope="row">{{ $row->name }}</th>
                    <td>{{ $row->sales }}</td>
                    <td>{{ $row->orders }}</td>
                    <td>{{ $row->web_order }}</td>
                    <td>{{ $row->app_order }}</td>
                </tr>
                @php($count++)
                @endforeach
                <tr>
                    <th scope="row" class="text-right">Total</th>
                    <td></td>
                    <td>{{ $data['total_sales'] }}</td>
                    <td>{{ $data['total_orders'] }}</td>
                    <td>{{ $data['total_web'] }}</td>
                    <td>{{ $data['total_app'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @if ($data['total_orders'] != 0)
    <br><br>
    <div id="piechart" class="chart"></div>
    @endif
</body>

</html>
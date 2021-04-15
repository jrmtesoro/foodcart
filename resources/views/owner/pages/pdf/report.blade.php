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
                @foreach ($data['column_chart'] as $row)
                    {!! json_encode($row, true) !!},
                @endforeach
            ]);

            var data1 = google.visualization.arrayToDataTable([
                @foreach ($data['line_chart'] as $row)
                    {!! json_encode($row, true) !!},
                @endforeach
            ]);

            var chart = new google.visualization.ColumnChart(document.getElementById("columnchart"));
            chart.draw(data, {title : 'Restaurant Orders'});

            var chart1 = new google.visualization.LineChart(document.getElementById('linechart'));
            chart1.draw(data1, {legend: 'bottom', title: 'Restaurant Sales'});

            // var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            // chart.draw(data, options);
        }
    </script>
</head>

<body onload="init()">
    <div class="container-fluid">
        <h1>{!! $data['header']['interval']." Report for ".$data['header']['restaurant_name'] !!}</h1>
        <br>
        <h3>{!! $data['header']['dates'] !!}<h3>
        <h3>Pinoy Food Cart</h3>
        <br><br><br>
        <table class="table table-bordered pt-4">
            <thead>
                <tr>
                    <th scope="col">Date</th>
                    <th scope="col">Total Sales</th>
                    <th scope="col">Total Orders</th>
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
                    <th scope="col">Date</th>
                    <th scope="col">Total Sales</th>
                    <th scope="col">Total Orders</th>
                    </tr>
                    </thead>
                    <tbody>
                @endif
                <tr>
                    <th scope="row">{{ $row['date'] }}</th>
                    <td>{{ $row['sales'] }}</td>
                    <td>{{ $row['orders'] }}</td>
                </tr>
                @php($count++)
                @endforeach
                <tr>
                    <th scope="row" class="text-right">Total</th>
                    <td>{{ $data['total_sales'] }}</td>
                    <td>{{ $data['total_orders'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="page-break"></div>
    <div class="container-fluid">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Restaurant Performance</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><div id="columnchart" class="chart"></div></td>
                </tr>
                <tr>
                    <td><div id="linechart" class="chart"></div></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
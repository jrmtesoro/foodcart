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
            @foreach ($data['pie_chart'] as $row)
                {!! json_encode($row, true) !!},
            @endforeach
            ]);

            var options = {
            title: 'Menu Orders'
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
                    <th scope="col">Item Name</th>
                    <th scope="col">Total Sales</th>
                    <th scope="col">Total Orders</th>
                    <th scope="col">%</th>
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
                    <th scope="col">Item Name</th>
                    <th scope="col">Total Sales</th>
                    <th scope="col">Total Orders</th>
                    <th scope="col">%</th>
                    </tr>
                    </thead>
                    <tbody>
                @endif
                <tr>
                    <th scope="row">{{ $row->item_name }}</th>
                    <td>{{ $row->sales }}</td>
                    <td>{{ $row->orders }}</td>
                    <td>{{ $row->percentage }}</td>
                </tr>
                @php($count++)
                @endforeach
                <tr>
                    <th scope="row" class="text-right">Total</th>
                    <td>{{ $data['total_sales'] }}</td>
                    <td>{{ $data['total_orders'] }}</td>
                    <td></td>
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
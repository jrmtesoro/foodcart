@extends('layouts.admin')

{{-- Page Title --}}
@section('page-title', 'Admin Dashboard')

{{-- Page Name --}}
@section('page-name', 'Dashboard')

{{-- Custom CSS --}}
@section('css')
@endsection

{{-- Custom Java Script --}}
@section('js')
<script src="{{ URL::asset('vendor/chart.js/dist/Chart.min.js') }}"></script>
<script src="{{ URL::asset('vendor/chart.js/dist/Chart.extension.js') }}"></script>
@endsection

@section('chart')
<script>
var o_chart = null;
var s_chart = null;

function orderChart(orders_data, labels) {
  o_chart = new Chart($('#chart-orders')[0].getContext('2d'), {
    type: 'bar',
    options: {
      scales: {
        yAxes: [{
          ticks: {
            callback: function(value) {
              if (!(value % 10)) {
                //return '$' + value + 'k'
                return value
              }
            }
          }
        }]
      },
      tooltips: {
        callbacks: {
          label: function(item, data) {
            var label = data.datasets[item.datasetIndex].label || '';
            var yLabel = item.yLabel;
            var content = '';

            if (data.datasets.length > 1) {
              content += '<span class="popover-body-label mr-auto">' + label + '</span>';
            }

            content += '<span class="popover-body-value">' + yLabel + '</span>';
            
            return content;
          }
        }
      }
    },
    data: {
      labels: labels,
      datasets: [{
        label: 'Sales',
        data: orders_data
      }]
    }
  });
}

function salesChart(sales_data, labels) {
  s_chart = new Chart($('#chart-sales')[0].getContext('2d'), {
    type: 'line',
    options: {
      scales: {
        yAxes: [{
          gridLines: {
            color: Charts.colors.gray[900],
            zeroLineColor: Charts.colors.gray[900]
          },
          ticks: {
            callback: function(value) {
              if (!(value % 10)) {
                return '₱ ' + value + '.00';
              }
            }
          }
        }]
      },
      tooltips: {
        callbacks: {
          label: function(item, data) {
            var label = data.datasets[item.datasetIndex].label || '';
            var yLabel = item.yLabel;
            var content = '';

            if (data.datasets.length > 1) {
              content += '<span class="popover-body-label mr-auto">' + label + '</span>';
            }

            content += '<span class="popover-body-value">₱ ' + yLabel + '.00</span>';
            return content;
          }
        }
      }
    },
    data: {
      labels: labels,
      datasets: [{
        label: 'Performance',
        data: sales_data
      }]
    }
  })
}
// Create chart
getChartValues("today");

$('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
  o_chart.destroy();
  s_chart.destroy();
  getChartValues(this.id)
})

function getChartValues(id) {
  $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
            'Accept' : 'application/json'
        },
        url: "{{ url('admin/charts/') }}/"+id,
        method: 'GET',
        success: function(result){
          orderChart(result.orders, result.labels);
          salesChart(result.sales, result.labels);
          $('#reports').text(result.count.reports);
          $('#users').text(result.count.users);
          $('#sales').text("₱ "+result.count.sales+".00");
          $('#orders').text(result.count.orders);
        }
    });
}
</script>
@endsection

@section('content')
@include('admin.inc.sidebar')
<div class="main-content">
  @include('admin.inc.navbar')
  <div class="header bg-gradient-info pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
      <div class="header-body">
        <ul class="nav nav-pills justify-content-end pb-3">
          <li class="nav-item mr-md-0">
            <a href="#" class="nav-link py-2 px-3 active" data-toggle="tab" id="today">
              <span class="d-none d-md-block">Today</span>
              <span class="d-md-none">T</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link py-2 px-3" data-toggle="tab" id="week">
              <span class="d-none d-md-block">Last 7 Days</span>
              <span class="d-md-none">7d</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link py-2 px-3" data-toggle="tab" id="month">
              <span class="d-none d-md-block">This Month</span>
              <span class="d-md-none">M</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link py-2 px-3" data-toggle="tab" id="year">
              <span class="d-none d-md-block">This Year</span>
              <span class="d-md-none">Y</span>
            </a>
          </li>
        </ul>
        <div class="row">
          <div class="col-xl-3 col-lg-6">
            <div class="card card-stats mb-4 mb-xl-0">
              <div class="card-body">
                <div class="row">
                  <div class="col">
                    <h5 class="card-title text-uppercase text-muted mb-0">Reports</h5>
                    <span class="h2 font-weight-bold mb-0" id="reports">0</span>
                  </div>
                  <div class="col-auto">
                    <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                      <i class="fas fa-chart-bar"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6">
            <div class="card card-stats mb-4 mb-xl-0">
              <div class="card-body">
                <div class="row">
                  <div class="col">
                    <h5 class="card-title text-uppercase text-muted mb-0">New users</h5>
                    <span class="h2 font-weight-bold mb-0" id="users">0</span>
                  </div>
                  <div class="col-auto">
                    <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                      <i class="fas fa-chart-pie"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6">
            <div class="card card-stats mb-4 mb-xl-0">
              <div class="card-body">
                <div class="row">
                  <div class="col">
                    <h5 class="card-title text-uppercase text-muted mb-0">Sales</h5>
                    <span class="h2 font-weight-bold mb-0" id="sales">0</span>
                  </div>
                  <div class="col-auto">
                    <div class="icon icon-shape bg-yellow text-white rounded-circle shadow">
                      <i class="fas fa-users"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6">
            <div class="card card-stats mb-4 mb-xl-0">
              <div class="card-body">
                <div class="row">
                  <div class="col">
                    <h5 class="card-title text-uppercase text-muted mb-0">Orders</h5>
                    <span class="h2 font-weight-bold mb-0" id="orders">0</span>
                  </div>
                  <div class="col-auto">
                    <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                      <i class="ni ni-archive-2"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid mt--7">
    <div class="row">
      <div class="col-xl-12 pb-5">
        <div class="card bg-gradient-default shadow">
          <div class="card-header bg-transparent">
            <div class="row align-items-center">
              <div class="col">
                <h6 class="text-uppercase text-light ls-1 mb-1">Overview</h6>
                <h2 class="text-white mb-0">Sales value</h2>
              </div>
            </div>
          </div>
          <div class="card-body">
            <!-- Chart -->
            <div class="chart">
              <!-- Chart wrapper -->
              <canvas id="chart-sales" class="chart-canvas"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-12">
        <div class="card shadow">
          <div class="card-header bg-transparent">
            <div class="row align-items-center">
              <div class="col">
                <h6 class="text-uppercase text-muted ls-1 mb-1">Performance</h6>
                <h2 class="mb-0">Total orders</h2>
              </div>
            </div>
          </div>
          <div class="card-body">
            <!-- Chart -->
            <div class="chart">
              <canvas id="chart-orders" class="chart-canvas"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Footer -->
    @include('owner.inc.footer')
  </div>
</div>
@endsection
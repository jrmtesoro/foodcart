<div class="card-header">
    <div class="d-flex">
        <div class="text-left my-auto mr-auto">
            <h3>Restaurant Sales</h3>
        </div>
        <a class="btn btn-success" id="restaurant_pdf" href="#">Print</a>
    </div>
</div>
<div class="card-header bg-secondary p-1 border-bottom-0">
    <button class="btn btn-link heading-small" type="button" data-toggle="collapse" data-target="#filter"><span class="fas fa-filter mr-1"></span>Filters</button>
</div>
<form id="filter_form" class="m-0" method="POST">
    <div id="filter" class="collapse">
        <div class="card-body bg-secondary border-top">
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
                            <option value='restaurant.id'>Restaurant ID</option>
                            <option value='restaurant.name'>Restaurant Name</option>
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
    <div class="col-sm-12 col-md-4 col-lg-4">
        <div class="custom-control custom-radio">
            <div class="row">
                <div class="col-auto">
                    <div class="form-group">
                        <input name="filter_radio" class="custom-control-input" id="filter_radio" value="specific" type="radio" checked>
                        <label class="custom-control-label" for="filter_radio">Specific Date</label>
                    </div>
                </div>
                <div class="offset-1 col-auto">
                    <div class="form-group">
                        <input name="filter_radio" class="custom-control-input" id="filter_radio1" value="range" type="radio">
                        <label class="custom-control-label" for="filter_radio1">Date Range</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group" id="specific">
            <div class="input-group input-group-alternative">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                </div>
                <input class="form-control datepicker" name="specific_date" id="specific_date" data-date-start-date="{{ \App\Order::getOldest() ?? \Carbon\Carbon::now()->format('m-d-Y') }}"
                    data-date-end-date="0d" placeholder="Select date" type="text" value="{{ \Carbon\Carbon::now()->format('m-d-Y') }}">
            </div>
        </div>
        <div class="form-group d-none" id="range">
            <div class="input-daterange datepicker row align-items-center" data-date-start-date="{{ \App\Order::getOldest() ?? \Carbon\Carbon::now()->format('m-d-Y') }}"
                data-date-end-date="0d">
                <div class="col">
                    <div class="form-group">
                        <div class="input-group input-group-alternative">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                            </div>
                            <input class="form-control" name="start_range" id="start_range" placeholder="Start date" type="text" value="{{ \Carbon\Carbon::now()->format('m-d-Y') }}">
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <div class="input-group input-group-alternative">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                            </div>
                            <input class="form-control" name="end_range" id="end_range" placeholder="End date" type="text" value="{{ \Carbon\Carbon::now()->format('m-d-Y') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
    <table class="table table-flush align-items-center" id="restaurant_sales">
        <thead class="thead-light">
            <tr>
                <th>Restaurant ID</th>
                <th>Restaurant Name</th>
                <th>Total Sales</th>
                <th>Total Transaction</th>
                <th>Web Orders</th>
                <th>App Orders</th>
                <th>Options</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Total</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
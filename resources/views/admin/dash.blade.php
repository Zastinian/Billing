@extends('layouts.admin')

@inject('server_model', 'App\Models\Server')
@inject('invoice_model', 'App\Models\Invoice')
@inject('ticket_model', 'App\Models\Ticket')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{!! session('currency')->symbol !!}{{ array_sum($incomes) * session('currency')->rate }} {{ session('currency')->name }}</h3>
                    <p>Income (Last 30 Days)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <a href="{{ route('admin.income') }}" class="small-box-footer">
                    More details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $server_model->where('status', 0)->count() }}</h3>
                    <p>Active Servers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-server"></i>
                </div>
                <a href="{{ route('admin.servers.active') }}" class="small-box-footer">
                    View All <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $invoice_model->where('paid', false)->count() }}</h3>
                    <p>Unpaid Invoices</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <a href="{{ route('admin.invoice.index') }}" class="small-box-footer">
                    View All <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $ticket_model->where('status', 1)->count() }}</h3>
                    <p>Open Tickets</p>
                </div>
                <div class="icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <a href="{{ route('admin.ticket.index') }}" class="small-box-footer">
                    View All <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="callout callout-info">
        <p>Currently running <b>{{ config('app.version') }}</b> and the latest 'stable' version <span id="latest-version">(loading...)</span>.</p>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Income in the last 30 days</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="incomeChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Orders in the last 30 days</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="orderChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Clients registered in the last 30 days</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="clientChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin_scripts')
    <script src="/plugins/chart.js/Chart.min.js"></script>
    <script>
        $.ajax({'url': 'https://api.github.com/repos/Zastinian/HedystiaBilling/releases/latest', 'success': function (data) {
            $('#latest-version').html((data.tag_name) ? `is <b>${data.tag_name}</b>` : `hasn't been released yet`)
        }, 'error': function () {
            $('#latest-version').html(`is unknown`)
        }})
        
        $(function() {
            var chartOptions = {
                maintainAspectRatio: false,
                responsive: true,
                legend: {display: false},
                scales: {
                    xAxes: [{
                        ticks: {fontColor: "white"},
                        gridLines: {color: "rgb(80,80,80)"},
                        scaleLabel: {
                            display: true,
                            fontColor: "white",
                            labelString: 'Days'
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            fontColor: "white",
                            beginAtZero: true
                        },
                        gridLines: {color: "rgb(80,80,80)"}
                    }]
                }
            }
            var incomeChartCanvas = $('#incomeChart').get(0).getContext('2d')
            var incomeChartData = {
                labels: [
                    @foreach (array_keys($incomes) as $day)
                        '{{ $day }}',
                    @endforeach
                ],
                datasets: [{
                    backgroundColor: 'rgba(60,141,188,0.8)',
                    data: [
                        @foreach (array_values($incomes) as $income)
                            '{{ $income }}',
                        @endforeach
                    ]
                }]
            }
            new Chart(incomeChartCanvas, {
                type: 'line',
                data: incomeChartData,
                options: chartOptions
            })
            
            var orderChartCanvas = $('#orderChart').get(0).getContext('2d')
            var orderChartData = {
                labels: [
                    @foreach (array_keys($orders) as $order)
                        '{{ $order }}',
                    @endforeach
                ],
                datasets: [{
                    backgroundColor: 'rgba(60,141,188,0.8)',
                    data: [
                        @foreach (array_values($orders) as $order)
                            '{{ $order }}',
                        @endforeach
                    ]
                }]
            }
            new Chart(orderChartCanvas, {
                type: 'line',
                data: orderChartData,
                options: chartOptions
            })
            
            var clientChartCanvas = $('#clientChart').get(0).getContext('2d')
            var clientChartData = {
                labels: [
                    @foreach (array_keys($clients) as $day)
                        '{{ $day }}',
                    @endforeach
                ],
                datasets: [{
                    backgroundColor: 'rgba(60,141,188,0.8)',
                    data: [
                        @foreach (array_values($clients) as $client)
                            '{{ $client }}',
                        @endforeach
                    ]
                }]
            }
            new Chart(clientChartCanvas, {
                type: 'line',
                data: clientChartData,
                options: chartOptions
            })
        })
    </script>
@endsection

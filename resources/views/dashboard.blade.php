@extends('layout.main')

@section('content')

    <div class="page">
        <header class="navbar navbar-expand-md d-print-none" style="background-color: #1752b8;">
            <div class="container-xl">
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="#">
                        <img src="{{ asset('assets/img/asyst.png') }}" width="110" height="32" alt="Logo"
                            class="navbar-brand-image" />
                    </a>
                </h1>
                <ul class="navbar-nav flex-row order-md-last ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar avatar-sm rounded-circle me-2" style="background-color: #e0e0e0; width: 40px; height: 40px;"></span>
                            <span class="fw-semibold text-white">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Setting</a></li>
                            <li><hr class="dropdown-divider" /></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </header>

        <div class="page-wrapper">
            <div class="container-xl">
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="true">
                            Dashboard
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs" type="button" role="tab" aria-controls="logs" aria-selected="false">
                            Logs
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="metrics-tab" data-bs-toggle="tab" data-bs-target="#metrics" type="button" role="tab" aria-controls="metrics" aria-selected="false">
                            Metrics
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="dashboardTabsContent">
                    <!-- Dashboard Tab -->
                    <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Dashboard Overview</h3>
                                    </div>
                                    <div class="card-body">
                                        <p>Welcome to the dashboard. Select a tab to view detailed information.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Logs Tab -->
                    <div class="tab-pane fade" id="logs" role="tabpanel" aria-labelledby="logs-tab">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Logs</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-vcenter card-table">
                                                <thead>
                                                    <tr>
                                                        <th>Timestamp</th>
                                                        <th>Level</th>
                                                        <th>Message</th>
                                                        <th>Details</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($logs as $log)
                                                        <tr>
                                                            <td>{{ $log->created_at }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $log->level === 'ERROR' ? 'danger' : ($log->level === 'WARN' ? 'warning' : 'info') }}">
                                                                    {{ $log->level }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $log->message }}</td>
                                                            <td>
                                                                <button class="btn btn-sm btn-info" onclick="showLogDetails({{ $log->id }})">
                                                                    <i class="bx bx-show"></i> View Details
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex justify-content-center mt-4">
                                            {{ $logs->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metrics Tab -->
                    <div class="tab-pane fade" id="metrics" role="tabpanel" aria-labelledby="metrics-tab">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">System Metrics</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-vcenter card-table">
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th>Usage (%)</th>
                                                        <th>Used (MB)</th>
                                                        <th>Total (MB)</th>
                                                        <th>Timestamp</th>
                                                        <th>Details</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr data-type="cpu">
                                                        <td>
                                                            <span class="badge bg-primary">CPU</span>
                                                        </td>
                                                        <td data-usage>
                                                            <span class="text-primary fw-bold">-</span>
                                                        </td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td data-timestamp>-</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-info" onclick="showMetricDetails(0)">
                                                                <i class="bx bx-show"></i> View Details
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr data-type="memory">
                                                        <td>
                                                            <span class="badge bg-success">MEMORY</span>
                                                        </td>
                                                        <td data-usage>
                                                            <span class="text-success fw-bold">-</span>
                                                        </td>
                                                        <td data-used>-</td>
                                                        <td data-total>-</td>
                                                        <td data-timestamp>-</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-info" onclick="showMetricDetails(0)">
                                                                <i class="bx bx-show"></i> View Details
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- Add Charts Section -->
                                        <div class="row mt-4">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h3 class="card-title">CPU Usage Over Time</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <canvas id="cpuChart"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Memory Usage Over Time</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <canvas id="memoryChart"></canvas>
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
            </div>
        </div>
    </div>

    <!-- Modal for showing log details -->
    <div class="modal modal-blur fade" id="logDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Log Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre id="logDetailsContent" class="p-3 bg-light rounded"></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for showing metric details -->
    <div class="modal modal-blur fade" id="metricDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Metric Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre id="metricDetailsContent" class="p-3 bg-light rounded"></pre>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Add ChartJS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        function showLogDetails(logId) {
            fetch(`/api/logs/${logId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('logDetailsContent').textContent = 
                        JSON.stringify(data, null, 2);
                    new bootstrap.Modal(document.getElementById('logDetailsModal')).show();
                });
        }

        function showMetricDetails(metricId) {
            fetch(`/api/metrics/${metricId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('metricDetailsContent').textContent = 
                        JSON.stringify(data, null, 2);
                    new bootstrap.Modal(document.getElementById('metricDetailsModal')).show();
                });
        }

        let cpuChart;
        let memoryChart;
        const maxDataPoints = 20; // Maximum number of data points to show

        function initializeCharts() {
            const commonOptions = {
                responsive: true,
                animation: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    },
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Time'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            };

            // Initialize CPU Chart
            const cpuCtx = document.getElementById('cpuChart').getContext('2d');
            cpuChart = new Chart(cpuCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'CPU Usage (%)',
                        data: [],
                        borderColor: '#0d6efd',
                        tension: 0.4,
                        fill: false
                    }]
                },
                options: commonOptions
            });

            // Initialize Memory Chart
            const memoryCtx = document.getElementById('memoryChart').getContext('2d');
            memoryChart = new Chart(memoryCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Memory Usage (%)',
                        data: [],
                        borderColor: '#198754',
                        tension: 0.4,
                        fill: false
                    }]
                },
                options: commonOptions
            });
        }

        function updateCharts(cpuUsage, memoryUsage, timestamp) {
            const timeLabel = new Date(timestamp).toLocaleTimeString();

            // Update CPU Chart
            if (cpuChart.data.labels.length > maxDataPoints) {
                cpuChart.data.labels.shift();
                cpuChart.data.datasets[0].data.shift();
            }
            cpuChart.data.labels.push(timeLabel);
            cpuChart.data.datasets[0].data.push(cpuUsage);
            cpuChart.update();

            // Update Memory Chart
            if (memoryChart.data.labels.length > maxDataPoints) {
                memoryChart.data.labels.shift();
                memoryChart.data.datasets[0].data.shift();
            }
            memoryChart.data.labels.push(timeLabel);
            memoryChart.data.datasets[0].data.push(memoryUsage);
            memoryChart.update();
        }

        function updateMetrics() {
            fetch('/api/metrics/latest')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Existing metrics update code
                    const cpuRow = document.querySelector('tr[data-type="cpu"]');
                    const memRow = document.querySelector('tr[data-type="memory"]');
                    
                    let cpuUsage = 0;
                    let memUsage = 0;
                    let timestamp = new Date().toISOString();

                    if (cpuRow && data.cpu) {
                        cpuUsage = parseFloat(data.cpu.cpu_usage);
                        if (!isNaN(cpuUsage)) {
                            cpuRow.querySelector('td[data-usage] span').textContent = `${cpuUsage.toFixed(2)}%`;
                            cpuRow.querySelector('td[data-timestamp]').textContent = new Date(data.cpu.timestamp).toLocaleString();
                            timestamp = data.cpu.timestamp;
                        }
                    }

                    if (memRow && data.memory) {
                        memUsage = parseFloat(data.memory.memory_usage);
                        if (!isNaN(memUsage)) {
                            memRow.querySelector('td[data-usage] span').textContent = `${memUsage.toFixed(2)}%`;
                            const memUsed = parseFloat(data.memory.raw_data['Mem.used']);
                            const memTotal = parseFloat(data.memory.raw_data['Mem.total']);
                            if (!isNaN(memUsed) && !isNaN(memTotal)) {
                                memRow.querySelector('td[data-used]').textContent = `${(memUsed / 1024).toFixed(2)} MB`;
                                memRow.querySelector('td[data-total]').textContent = `${(memTotal / 1024).toFixed(2)} MB`;
                            }
                            memRow.querySelector('td[data-timestamp]').textContent = new Date(data.memory.timestamp).toLocaleString();
                        }
                    }

                    // Update charts with new data
                    updateCharts(cpuUsage, memUsage, timestamp);
                })
                .catch(error => {
                    console.error('Error fetching metrics:', error);
                    // Existing error handling code
                });
        }

        // Initialize charts when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            updateMetrics(); // Initial update
            setInterval(updateMetrics, 5000); // Update every 5 seconds
        });
    </script>
@endpush

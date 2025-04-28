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
                <div class="row">
                    <!-- Logs Section -->
                    <div class="col-md-6">
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

                    <!-- Metrics Section -->
                    <div class="col-md-6">
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
                                            @foreach($metrics as $metric)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-{{ $metric->type === 'cpu' ? 'primary' : 'success' }}">
                                                            {{ strtoupper($metric->type) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($metric->type === 'cpu')
                                                            <span class="text-primary fw-bold">{{ number_format($metric->cpu_usage, 2) }}%</span>
                                                        @else
                                                            <span class="text-success fw-bold">{{ number_format($metric->memory_usage, 2) }}%</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($metric->type === 'memory' && isset($metric->raw_data['Mem.used']))
                                                            {{ number_format($metric->raw_data['Mem.used'] / 1024, 2) }} MB
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($metric->type === 'memory' && isset($metric->raw_data['Mem.total']))
                                                            {{ number_format($metric->raw_data['Mem.total'] / 1024, 2) }} MB
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $metric->timestamp ? \Carbon\Carbon::parse($metric->timestamp)->format('Y-m-d H:i:s') : '-' }}
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" onclick="showMetricDetails({{ $metric->id }})">
                                                            <i class="bx bx-show"></i> View Details
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
</script>
@endpush

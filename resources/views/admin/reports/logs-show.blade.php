@extends('layouts.admin')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Admin Reports - Security & System Logs
                    </div>
                    <h2 class="page-title">
                        Log Details
                    </h2>
                    <p class="text-muted">
                        Detailed view of the selected admin security log.
                    </p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.reports.logs') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1" />
                            </svg>
                            Back to Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ ucwords(str_replace('_', ' ', $logEntry->action)) }}
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Timestamp</th>
                            <td>{{ $logEntry->created_at->format('Y-m-d H:i:s') }} ({{ $logEntry->created_at->diffForHumans() }})</td>
                        </tr>
                        <tr>
                            <th>Action</th>
                            <td>{{ ucwords(str_replace('_', ' ', $logEntry->action)) }}</td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td>
                                @if($logEntry->user)
                                    <strong>{{ $logEntry->user->name }}</strong> ({{ $logEntry->user->email }})
                                    <br><small class="text-muted">User ID: {{ $logEntry->user->id }}</small>
                                @else
                                    System
                                @endif
                            </td>
                        </tr>
                        @if($logEntry->shop)
                        <tr>
                            <th>Shop</th>
                            <td>{{ $logEntry->shop->name }} (ID: {{ $logEntry->shop->id }})</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Description</th>
                            <td>{{ $logEntry->description }}</td>
                        </tr>
                        @if($logEntry->model_type)
                        <tr>
                            <th>Model Type</th>
                            <td>{{ $logEntry->model_type }}</td>
                        </tr>
                        @endif
                        @if($logEntry->model_id)
                        <tr>
                            <th>Model ID</th>
                            <td>{{ $logEntry->model_id }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>IP Address</th>
                            <td><code>{{ $logEntry->ip_address ?: 'N/A' }}</code></td>
                        </tr>
                        @if($logEntry->user_agent)
                        <tr>
                            <th>User Agent</th>
                            <td><small>{{ $logEntry->user_agent }}</small></td>
                        </tr>
                        @endif
                        <tr>
                            <th>Old Data</th>
                            <td>
                                @if($logEntry->old_data)
                                    <pre class="bg-light p-2" style="max-height: 300px; overflow-y: auto;"><code>{{ json_encode($logEntry->old_data, JSON_PRETTY_PRINT) }}</code></pre>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>New Data</th>
                            <td>
                                @if($logEntry->new_data)
                                    <pre class="bg-light p-2" style="max-height: 300px; overflow-y: auto;"><code>{{ json_encode($logEntry->new_data, JSON_PRETTY_PRINT) }}</code></pre>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

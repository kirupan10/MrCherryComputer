@extends('layouts.nexora')

@section('title', 'Log Details')

@section('content')
<div class="container-xxl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ shop_route('logs.index') }}" class="text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18"/></svg>
                        Back to Logs
                    </a>
                </div>
                <h2 class="page-title">Log Details</h2>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Log Information</h3>
                </div>
                <div class="card-body">
                    <div class="datagrid">
                        <div class="datagrid-item">
                            <div class="datagrid-title">Date & Time</div>
                            <div class="datagrid-content">
                                {{ $log->created_at->format('F d, Y h:i A') }}
                                <br><small class="text-muted">({{ $log->created_at->diffForHumans() }})</small>
                            </div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Performed By</div>
                            <div class="datagrid-content">
                                <strong>{{ $log->user->name ?? 'Unknown' }}</strong>
                                <br><small class="text-muted">{{ $log->user->email ?? '' }}</small>
                            </div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Action</div>
                            <div class="datagrid-content">
                                @if($log->action === 'delete')
                                    <span class="badge bg-danger">Delete</span>
                                @elseif($log->action === 'update')
                                    <span class="badge bg-warning">Update</span>
                                @elseif($log->action === 'create')
                                    <span class="badge bg-success">Create</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($log->action) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Model Type</div>
                            <div class="datagrid-content">
                                <span class="badge bg-azure-lt">{{ $log->model_type }}</span>
                            </div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Model ID</div>
                            <div class="datagrid-content">#{{ $log->model_id }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">IP Address</div>
                            <div class="datagrid-content">{{ $log->ip_address }}</div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h4>Description</h4>
                        <p class="text-muted">{{ $log->description }}</p>
                    </div>

                    @if($log->user_agent)
                    <div class="mt-3">
                        <h4>Browser / User Agent</h4>
                        <p class="text-muted small">{{ $log->user_agent }}</p>
                    </div>
                    @endif
                </div>
            </div>

            @if($log->old_data)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Old Data (Before {{ ucfirst($log->action) }})</h3>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($log->old_data, JSON_PRETTY_PRINT) }}</code></pre>
                </div>
            </div>
            @endif

            @if($log->new_data)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">New Data (After {{ ucfirst($log->action) }})</h3>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($log->new_data, JSON_PRETTY_PRINT) }}</code></pre>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Info</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Log ID</label>
                        <div class="text-muted">#{{ $log->id }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shop</label>
                        <div class="text-muted">{{ $log->shop->name ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Timestamp</label>
                        <div class="text-muted">{{ $log->created_at->toDateTimeString() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

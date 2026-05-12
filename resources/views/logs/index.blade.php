@extends('layouts.nexora')

@section('title', 'Logs')

@section('content')
<div class="container-xxl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Logs</h2>
                <div class="text-muted mt-1">View system activity and changes log</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filters</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ shop_route('logs.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Action</label>
                            <select name="action" class="form-select">
                                <option value="">All Actions</option>
                                <option value="delete" @selected($action === 'delete')>Delete</option>
                                <option value="update" @selected($action === 'update')>Update</option>
                                <option value="create" @selected($action === 'create')>Create</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Model Type</label>
                            <select name="model_type" class="form-select">
                                <option value="">All Types</option>
                                @foreach($modelTypes as $type)
                                    <option value="{{ $type }}" @selected($modelType === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">User</label>
                            <select name="user_id" class="form-select">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected($userId == $user->id)>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search in description..." value="{{ $search }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="{{ shop_route('logs.index') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Activity Log ({{ $logs->total() }} records)</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Model</th>
                                <th>Description</th>
                                <th>IP Address</th>
                                <th class="w-1">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td class="text-muted">
                                        {{ $log->created_at->format('M d, Y') }}<br>
                                        <small>{{ $log->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="font-weight-medium">{{ $log->user->name ?? 'Unknown' }}</div>
                                                <div class="text-muted small">{{ $log->user->email ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($log->action === 'delete')
                                            <span class="badge bg-danger">Delete</span>
                                        @elseif($log->action === 'update')
                                            <span class="badge bg-warning">Update</span>
                                        @elseif($log->action === 'create')
                                            <span class="badge bg-success">Create</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($log->action) }}</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-azure-lt">{{ $log->model_type }}</span></td>
                                    <td class="text-muted small">{{ Str::limit($log->description, 80) }}</td>
                                    <td class="text-muted small">{{ $log->ip_address }}</td>
                                    <td>
                                        <a href="{{ shop_route('logs.show', $log->id) }}" class="btn btn-ghost-primary btn-icon btn-sm" title="View Details">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="2"/><path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <p>No logs found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="card-footer d-flex justify-content-center">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

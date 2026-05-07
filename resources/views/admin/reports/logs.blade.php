@extends('layouts.admin')

@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Admin Reports - Security & System Logs
                    </div>
                    <h2 class="page-title">
                        Admin Dashboard Logs
                    </h2>
                    <p class="text-muted">
                        System-wide admin security logs: User login sessions, admin privilege actions (user/shop management)
                    </p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1" />
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="container-fluid">
            <div class="row row-deck row-cards">
                <!-- Filters Card -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Filter Logs</h3>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.reports.logs') }}">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Search</label>
                                        <input type="text" name="search" class="form-control"
                                               placeholder="Search description, action, IP..."
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Action Type</label>
                                        <select name="action" class="form-select">
                                            <option value="">All Actions</option>
                                            @foreach($actions as $action)
                                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                                    {{ ucwords(str_replace('_', ' ', $action)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">User</label>
                                        <select name="user_id" class="form-select">
                                            <option value="">All Users</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Date From</label>
                                        <input type="date" name="date_from" class="form-control"
                                               value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Date To</label>
                                        <input type="date" name="date_to" class="form-control"
                                               value="{{ request('date_to') }}">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <circle cx="10" cy="10" r="7"/>
                                                <line x1="21" y1="21" x2="15" y2="15"/>
                                            </svg>
                                            Filter
                                        </button>
                                    </div>
                                </div>
                                @if(request()->hasAny(['search', 'action', 'user_id', 'date_from', 'date_to']))
                                    <div class="mt-3">
                                        <a href="{{ route('admin.reports.logs') }}" class="btn btn-outline-secondary">
                                            Clear Filters
                                        </a>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Logs Table -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <rect x="5" y="11" width="14" height="10" rx="2"/>
                                    <circle cx="12" cy="16" r="1"/>
                                    <path d="M8 11v-4a4 4 0 0 1 8 0v4"/>
                                </svg>
                                Admin Security Logs ({{ $logs->total() }} entries)
                            </h3>
                            <div class="card-actions">
                                <div class="d-flex align-items-center">
                                    <span class="text-muted me-2">Per page:</span>
                                    <form method="GET" action="{{ route('admin.reports.logs') }}" class="d-inline">
                                        @foreach(request()->except('per_page') as $key => $value)
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endforeach
                                        <select name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                            <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-striped">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Action</th>
                                        <th>User</th>
                                        <th>Description</th>
                                        <th>Model</th>
                                        <th>IP Address</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        <tr>
                                            <td>
                                                <div class="text-nowrap">{{ $log->created_at->format('Y-m-d') }}</div>
                                                <div class="small text-muted">{{ $log->created_at->format('H:i:s') }}</div>
                                                <div class="small text-muted">{{ $log->created_at->diffForHumans() }}</div>
                                            </td>
                                            <td>
                                                @php
                                                    $actionColor = 'secondary';
                                                    if (str_contains($log->action, 'login')) $actionColor = 'success';
                                                    if (str_contains($log->action, 'logout')) $actionColor = 'info';
                                                    if (str_contains($log->action, 'create')) $actionColor = 'primary';
                                                    if (str_contains($log->action, 'update') || str_contains($log->action, 'edit')) $actionColor = 'warning';
                                                    if (str_contains($log->action, 'delete')) $actionColor = 'danger';
                                                    if (str_contains($log->action, 'suspend')) $actionColor = 'orange';
                                                    $isAdminPrivilege = in_array($log->action, $adminPrivilegeActions);
                                                @endphp
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="badge bg-{{ $actionColor }}">
                                                        {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                                    </span>
                                                    @if($isAdminPrivilege)
                                                        <span class="badge bg-purple-lt" style="font-size: 0.65rem;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <rect x="5" y="11" width="14" height="10" rx="2"/>
                                                                <circle cx="12" cy="16" r="1"/>
                                                                <path d="M8 11v-4a4 4 0 0 1 8 0v4"/>
                                                            </svg>
                                                            ADMIN
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($log->user)
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-sm me-2">{{ substr($log->user->name, 0, 2) }}</span>
                                                        <div>
                                                            <div>{{ $log->user->name }}</div>
                                                            <div class="small text-muted">{{ $log->user->email }}</div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">System</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div style="max-width: 300px;">{{ $log->description }}</div>
                                                @if($log->action === 'user_login')
                                                    <div class="small text-muted">
                                                        Login time: {{ data_get($log->new_data, 'login_time', 'N/A') }}
                                                        @if(data_get($log->new_data, 'user_role'))
                                                            · Role: {{ data_get($log->new_data, 'user_role') }}
                                                        @endif
                                                    </div>
                                                @elseif($log->action === 'user_logout')
                                                    <div class="small text-muted">
                                                        Logout time: {{ data_get($log->new_data, 'logout_time', 'N/A') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->model_type)
                                                    <div class="small">
                                                        <strong>{{ class_basename($log->model_type) }}</strong>
                                                        @if($log->model_id)
                                                            <div class="text-muted">ID: {{ $log->model_id }}</div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="font-monospace small">{{ $log->ip_address ?: '-' }}</span>
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.reports.logs.show', $log->id) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <circle cx="12" cy="12" r="2"/>
                                                        <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                                    </svg>
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-5">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-3" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <circle cx="12" cy="12" r="9"/>
                                                    <line x1="9" y1="10" x2="9.01" y2="10"/>
                                                    <line x1="15" y1="10" x2="15.01" y2="10"/>
                                                    <path d="M9.5 15.25a3.5 3.5 0 0 1 5 0"/>
                                                </svg>
                                                <div class="h3">No logs found</div>
                                                <p>Try adjusting your filters to see more results.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex align-items-center">
                            {{ $logs->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

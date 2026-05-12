@extends('layouts.nexora')

@section('title', 'Repair Jobs')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Repair Jobs
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ shop_route('tech.repairs.create') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 5l0 14"/>
                                <path d="M5 12l14 0"/>
                            </svg>
                            New Repair Job
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
                    <h3 class="card-title">Search & Filter</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ shop_route('tech.repairs.index') }}">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search by job #, customer, serial..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="diagnosing" {{ request('status') == 'diagnosing' ? 'selected' : '' }}>Diagnosing</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="in_repair" {{ request('status') == 'in_repair' ? 'selected' : '' }}>In Repair</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="priority" class="form-select">
                                    <option value="">All Priority</option>
                                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>
                            @if(currentShop()->hasFeature('technician_assign'))
                                <div class="col-md-2">
                                    <select name="technician_id" class="form-select">
                                        <option value="">All Technicians</option>
                                        @foreach($technicians ?? [] as $tech)
                                            <option value="{{ $tech->id }}" {{ request('technician_id') == $tech->id ? 'selected' : '' }}>
                                                {{ $tech->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ shop_route('tech.repairs.index') }}" class="btn btn-link">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Repair Jobs List</h3>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th>Job #</th>
                                <th>Customer</th>
                                <th>Product / Serial</th>
                                <th>Issue</th>
                                <th>Status</th>
                                <th>Priority</th>
                                @if(currentShop()->hasFeature('technician_assign'))
                                    <th>Technician</th>
                                @endif
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($repairJobs as $job)
                                <tr>
                                    <td>
                                        <a href="{{ shop_route('tech.repairs.show', $job) }}">
                                            #{{ $job->id }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($job->customer)
                                            <a href="{{ shop_route('customer.show', $job->customer) }}">
                                                {{ $job->customer->name }}
                                            </a><br>
                                            <small class="text-muted">{{ $job->customer->phone }}</small>
                                        @else
                                            <span class="text-muted">No customer</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($job->serialNumber)
                                            <a href="{{ shop_route('tech.products.show', $job->serialNumber->product) }}">
                                                {{ $job->serialNumber->product->name }}
                                            </a><br>
                                            <small class="text-muted">
                                                SN: {{ $job->serialNumber->serial_number }}
                                            </small>
                                        @else
                                            <span class="text-muted">No serial</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $job->issue_description }}">
                                            {{ $job->issue_description }}
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'pending' => 'secondary',
                                                'diagnosing' => 'info',
                                                'approved' => 'primary',
                                                'in_repair' => 'warning',
                                                'completed' => 'success',
                                                'delivered' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusClasses[$job->status] ?? 'secondary' }}">
                                            {{ str_replace('_', ' ', ucfirst($job->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $priorityClasses = [
                                                'low' => 'secondary',
                                                'medium' => 'info',
                                                'high' => 'warning',
                                                'urgent' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $priorityClasses[$job->priority] ?? 'secondary' }}">
                                            {{ ucfirst($job->priority) }}
                                        </span>
                                    </td>
                                    @if(currentShop()->hasFeature('technician_assign'))
                                        <td>
                                            @if($job->technician)
                                                {{ $job->technician->name }}
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td>
                                        {{ $job->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $job->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ shop_route('tech.repairs.show', $job) }}" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No repair jobs found.
                                        <a href="{{ shop_route('tech.repairs.create') }}">Create your first repair job</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($repairJobs->hasPages())
                    <div class="card-footer">
                        {{ $repairJobs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

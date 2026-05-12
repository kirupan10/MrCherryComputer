@extends('layouts.nexora')

@section('title', 'Edit Repair Job')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        <a href="{{ shop_route('tech.repairs.index') }}">Repair Jobs</a> /
                        <a href="{{ shop_route('tech.repairs.show', $repairJob) }}">Job #{{ $repairJob->id }}</a>
                    </div>
                    <h2 class="page-title">
                        Edit Repair Job #{{ $repairJob->id }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            <form action="{{ shop_route('tech.repairs.update', $repairJob) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Job Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Customer</label>
                                        <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                            <option value="">Select Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}"
                                                    {{ old('customer_id', $repairJob->customer_id) == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }} ({{ $customer->phone }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('customer_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Serial Number</label>
                                        <select name="tech_serial_number_id" class="form-select @error('tech_serial_number_id') is-invalid @enderror">
                                            <option value="">Select Serial Number (Optional)</option>
                                            @foreach($serialNumbers as $serial)
                                                <option value="{{ $serial->id }}"
                                                    {{ old('tech_serial_number_id', $repairJob->tech_serial_number_id) == $serial->id ? 'selected' : '' }}>
                                                    {{ $serial->product->name }} - {{ $serial->serial_number }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tech_serial_number_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label required">Issue Description</label>
                                        <textarea name="issue_description"
                                            class="form-control @error('issue_description') is-invalid @enderror"
                                            rows="4" required>{{ old('issue_description', $repairJob->issue_description) }}</textarea>
                                        @error('issue_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Status</label>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                            <option value="pending" {{ old('status', $repairJob->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="diagnosing" {{ old('status', $repairJob->status) == 'diagnosing' ? 'selected' : '' }}>Diagnosing</option>
                                            <option value="approved" {{ old('status', $repairJob->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="in_repair" {{ old('status', $repairJob->status) == 'in_repair' ? 'selected' : '' }}>In Repair</option>
                                            <option value="completed" {{ old('status', $repairJob->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="delivered" {{ old('status', $repairJob->status) == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            <option value="cancelled" {{ old('status', $repairJob->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Priority</label>
                                        <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                            <option value="low" {{ old('priority', $repairJob->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                            <option value="medium" {{ old('priority', $repairJob->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="high" {{ old('priority', $repairJob->priority) == 'high' ? 'selected' : '' }}>High</option>
                                            <option value="urgent" {{ old('priority', $repairJob->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if(currentShop()->hasFeature('technician_assign'))
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Assign Technician</label>
                                            <select name="technician_id" class="form-select @error('technician_id') is-invalid @enderror">
                                                <option value="">Unassigned</option>
                                                @foreach($technicians ?? [] as $tech)
                                                    <option value="{{ $tech->id }}"
                                                        {{ old('technician_id', $repairJob->technician_id) == $tech->id ? 'selected' : '' }}>
                                                        {{ $tech->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('technician_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif

                                    <div class="col-12 mb-3">
                                        <label class="form-label">Customer Notes</label>
                                        <textarea name="customer_notes"
                                            class="form-control @error('customer_notes') is-invalid @enderror"
                                            rows="3">{{ old('customer_notes', $repairJob->customer_notes) }}</textarea>
                                        @error('customer_notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Cost & Timeline</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Estimated Cost</label>
                                        <input type="number" name="estimated_cost"
                                            class="form-control @error('estimated_cost') is-invalid @enderror"
                                            value="{{ old('estimated_cost', $repairJob->estimated_cost) }}" step="0.01" min="0">
                                        @error('estimated_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Actual Cost</label>
                                        <input type="number" name="actual_cost"
                                            class="form-control @error('actual_cost') is-invalid @enderror"
                                            value="{{ old('actual_cost', $repairJob->actual_cost) }}" step="0.01" min="0">
                                        @error('actual_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Parts Cost</label>
                                        <input type="number" name="parts_cost"
                                            class="form-control @error('parts_cost') is-invalid @enderror"
                                            value="{{ old('parts_cost', $repairJob->parts_cost) }}" step="0.01" min="0">
                                        @error('parts_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Labor Cost</label>
                                        <input type="number" name="labor_cost"
                                            class="form-control @error('labor_cost') is-invalid @enderror"
                                            value="{{ old('labor_cost', $repairJob->labor_cost) }}" step="0.01" min="0">
                                        @error('labor_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Estimated Completion Date</label>
                                        <input type="date" name="estimated_completion_date"
                                            class="form-control @error('estimated_completion_date') is-invalid @enderror"
                                            value="{{ old('estimated_completion_date', $repairJob->estimated_completion_date?->format('Y-m-d')) }}">
                                        @error('estimated_completion_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Completed At</label>
                                        <input type="datetime-local" name="completed_at"
                                            class="form-control @error('completed_at') is-invalid @enderror"
                                            value="{{ old('completed_at', $repairJob->completed_at?->format('Y-m-d\TH:i')) }}">
                                        @error('completed_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Delivered At</label>
                                        <input type="datetime-local" name="delivered_at"
                                            class="form-control @error('delivered_at') is-invalid @enderror"
                                            value="{{ old('delivered_at', $repairJob->delivered_at?->format('Y-m-d\TH:i')) }}">
                                        @error('delivered_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Additional Notes</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Technician Notes</label>
                                        <textarea name="technician_notes"
                                            class="form-control @error('technician_notes') is-invalid @enderror"
                                            rows="3">{{ old('technician_notes', $repairJob->technician_notes) }}</textarea>
                                        <small class="form-hint">Internal notes for technicians</small>
                                        @error('technician_notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label">Resolution Summary</label>
                                        <textarea name="resolution_summary"
                                            class="form-control @error('resolution_summary') is-invalid @enderror"
                                            rows="3">{{ old('resolution_summary', $repairJob->resolution_summary) }}</textarea>
                                        <small class="form-hint">Final summary of what was done to fix the issue</small>
                                        @error('resolution_summary')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Actions</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 12l5 5l10 -10"/>
                                        </svg>
                                        Update Repair Job
                                    </button>
                                    <a href="{{ shop_route('tech.repairs.show', $repairJob) }}" class="btn">Cancel</a>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Current Status</h3>
                            </div>
                            <div class="card-body">
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
                                <span class="badge bg-{{ $statusClasses[$repairJob->status] ?? 'secondary' }} fs-3">
                                    {{ str_replace('_', ' ', ucfirst($repairJob->status)) }}
                                </span>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Timeline</h3>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item">
                                        <strong>Created:</strong><br>
                                        {{ $repairJob->created_at->format('M d, Y H:i') }}
                                    </div>
                                    @if($repairJob->completed_at)
                                        <div class="list-group-item">
                                            <strong>Completed:</strong><br>
                                            {{ $repairJob->completed_at->format('M d, Y H:i') }}
                                        </div>
                                    @endif
                                    @if($repairJob->delivered_at)
                                        <div class="list-group-item">
                                            <strong>Delivered:</strong><br>
                                            {{ $repairJob->delivered_at->format('M d, Y H:i') }}
                                        </div>
                                    @endif
                                    <div class="list-group-item">
                                        <strong>Last Updated:</strong><br>
                                        {{ $repairJob->updated_at->format('M d, Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

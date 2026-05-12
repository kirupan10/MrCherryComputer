@extends('layouts.nexora')

@section('title', 'Create Repair Job')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        <a href="{{ shop_route('tech.repairs.index') }}">Repair Jobs</a>
                    </div>
                    <h2 class="page-title">
                        Create Repair Job
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            <form action="{{ shop_route('tech.repairs.store') }}" method="POST">
                @csrf
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
                                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
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
                                                <option value="{{ $serial->id }}" {{ old('tech_serial_number_id', request('serial_number_id')) == $serial->id ? 'selected' : '' }}>
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
                                            rows="4" required>{{ old('issue_description') }}</textarea>
                                        @error('issue_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Priority</label>
                                        <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                            <option value="low" {{ old('priority', 'medium') == 'low' ? 'selected' : '' }}>Low</option>
                                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if(currentShop()->hasFeature('technician_assign'))
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Assign Technician</label>
                                            <select name="technician_id" class="form-select @error('technician_id') is-invalid @enderror">
                                                <option value="">Assign Later</option>
                                                @foreach($technicians ?? [] as $tech)
                                                    <option value="{{ $tech->id }}" {{ old('technician_id') == $tech->id ? 'selected' : '' }}>
                                                        {{ $tech->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('technician_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Estimated Cost</label>
                                        <input type="number" name="estimated_cost"
                                            class="form-control @error('estimated_cost') is-invalid @enderror"
                                            value="{{ old('estimated_cost') }}" step="0.01" min="0">
                                        @error('estimated_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Estimated Completion Date</label>
                                        <input type="date" name="estimated_completion_date"
                                            class="form-control @error('estimated_completion_date') is-invalid @enderror"
                                            value="{{ old('estimated_completion_date') }}">
                                        @error('estimated_completion_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label">Customer Notes</label>
                                        <textarea name="customer_notes"
                                            class="form-control @error('customer_notes') is-invalid @enderror"
                                            rows="3">{{ old('customer_notes') }}</textarea>
                                        <small class="form-hint">Any additional notes from the customer</small>
                                        @error('customer_notes')
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
                                        Create Repair Job
                                    </button>
                                    <a href="{{ shop_route('tech.repairs.index') }}" class="btn">Cancel</a>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Repair Workflow</h3>
                            </div>
                            <div class="card-body">
                                <ol class="list-unstyled">
                                    <li class="mb-2">
                                        <span class="badge bg-secondary">1</span>
                                        <strong>Pending</strong><br>
                                        <small class="text-muted">Initial intake</small>
                                    </li>
                                    <li class="mb-2">
                                        <span class="badge bg-info">2</span>
                                        <strong>Diagnosing</strong><br>
                                        <small class="text-muted">Finding the issue</small>
                                    </li>
                                    <li class="mb-2">
                                        <span class="badge bg-primary">3</span>
                                        <strong>Approved</strong><br>
                                        <small class="text-muted">Customer approved</small>
                                    </li>
                                    <li class="mb-2">
                                        <span class="badge bg-warning">4</span>
                                        <strong>In Repair</strong><br>
                                        <small class="text-muted">Fixing the device</small>
                                    </li>
                                    <li class="mb-2">
                                        <span class="badge bg-success">5</span>
                                        <strong>Completed</strong><br>
                                        <small class="text-muted">Ready for pickup</small>
                                    </li>
                                    <li>
                                        <span class="badge bg-success">6</span>
                                        <strong>Delivered</strong><br>
                                        <small class="text-muted">Given to customer</small>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

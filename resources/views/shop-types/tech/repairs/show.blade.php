@extends('shop-types.tech.layouts.nexora')

@section('title', 'Repair Job Details')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        <a href="{{ route('tech.repairs.index') }}">Repair Jobs</a>
                    </div>
                    <h2 class="page-title">
                        Repair Job #{{ $repairJob->id }}
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
                        <span class="badge bg-{{ $statusClasses[$repairJob->status] ?? 'secondary' }} ms-2">
                            {{ str_replace('_', ' ', ucfirst($repairJob->status)) }}
                        </span>
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        @can('update', $repairJob)
                            <a href="{{ route('tech.repairs.edit', $repairJob) }}" class="btn btn-outline-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                    <path d="M16 5l3 3"/>
                                </svg>
                                Edit
                            </a>
                        @endcan
                        <a href="{{ route('tech.repairs.print', $repairJob) }}" target="_blank" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"/>
                                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"/>
                                <rect x="7" y="13" width="10" height="8" rx="2"/>
                            </svg>
                            Print
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    {{-- Workflow Actions --}}
                    @if($repairJob->status !== 'delivered' && $repairJob->status !== 'cancelled')
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Workflow Actions</h3>
                            </div>
                            <div class="card-body">
                                <div class="btn-list">
                                    @if($repairJob->status === 'pending' && currentShop()->hasFeature('technician_assign'))
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignTechnicianModal">
                                            Assign Technician
                                        </button>
                                    @endif

                                    @if($repairJob->status === 'pending')
                                        <form action="{{ route('tech.repairs.start-diagnosis', $repairJob) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-info">Start Diagnosis</button>
                                        </form>
                                    @endif

                                    @if($repairJob->status === 'diagnosing')
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#completeDiagnosisModal">
                                            Complete Diagnosis
                                        </button>
                                    @endif

                                    @if($repairJob->status === 'approved')
                                        <form action="{{ route('tech.repairs.start-repair', $repairJob) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning">Start Repair</button>
                                        </form>
                                    @endif

                                    @if($repairJob->status === 'in_repair')
                                        <form action="{{ route('tech.repairs.complete', $repairJob) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success">Mark as Completed</button>
                                        </form>
                                    @endif

                                    @if($repairJob->status === 'completed')
                                        <form action="{{ route('tech.repairs.deliver', $repairJob) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success">Mark as Delivered</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Job Information --}}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Job Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Customer</strong></label>
                                    <p>
                                        @if($repairJob->customer)
                                            <a href="{{ route('customer.show', $repairJob->customer) }}">
                                                {{ $repairJob->customer->name }}
                                            </a><br>
                                            <small class="text-muted">{{ $repairJob->customer->phone }}</small>
                                        @else
                                            <span class="text-muted">No customer</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Priority</strong></label>
                                    <p>
                                        @php
                                            $priorityClasses = [
                                                'low' => 'secondary',
                                                'medium' => 'info',
                                                'high' => 'warning',
                                                'urgent' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $priorityClasses[$repairJob->priority] ?? 'secondary' }}">
                                            {{ ucfirst($repairJob->priority) }}
                                        </span>
                                    </p>
                                </div>

                                @if($repairJob->serialNumber)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Product</strong></label>
                                        <p>
                                            <a href="{{ route('tech.products.show', $repairJob->serialNumber->product) }}">
                                                {{ $repairJob->serialNumber->product->name }}
                                            </a><br>
                                            <small class="text-muted">
                                                SN:
                                                <a href="{{ route('tech.serial-numbers.show', $repairJob->serialNumber) }}">
                                                    {{ $repairJob->serialNumber->serial_number }}
                                                </a>
                                            </small>
                                        </p>
                                    </div>
                                @endif

                                @if(currentShop()->hasFeature('technician_assign') && $repairJob->technician)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Technician</strong></label>
                                        <p>{{ $repairJob->technician->name }}</p>
                                    </div>
                                @endif

                                <div class="col-12 mb-3">
                                    <label class="form-label"><strong>Issue Description</strong></label>
                                    <p>{{ $repairJob->issue_description }}</p>
                                </div>

                                @if($repairJob->customer_notes)
                                    <div class="col-12 mb-3">
                                        <label class="form-label"><strong>Customer Notes</strong></label>
                                        <p>{{ $repairJob->customer_notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Diagnostics --}}
                    @if(currentShop()->hasFeature('diagnostics') && $repairJob->diagnostics->isNotEmpty())
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Diagnostic Report</h3>
                            </div>
                            <div class="card-body">
                                @foreach($repairJob->diagnostics as $diagnostic)
                                    <div class="mb-3">
                                        <strong>{{ $diagnostic->diagnostic_type }}:</strong>
                                        <p>{{ $diagnostic->findings }}</p>
                                        @if($diagnostic->recommendations)
                                            <p><strong>Recommendations:</strong> {{ $diagnostic->recommendations }}</p>
                                        @endif
                                        <small class="text-muted">
                                            Performed by {{ $diagnostic->user->name ?? 'Unknown' }} on {{ $diagnostic->created_at->format('M d, Y H:i') }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Repair Parts --}}
                    @if($repairJob->parts->isNotEmpty())
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Parts Used</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table card-table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Part Name</th>
                                            <th>Part Number</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($repairJob->parts as $part)
                                            <tr>
                                                <td>{{ $part->part_name }}</td>
                                                <td>{{ $part->part_number ?? '-' }}</td>
                                                <td>{{ $part->quantity }}</td>
                                                <td>{{ number_format($part->unit_price, 2) }}</td>
                                                <td>{{ number_format($part->quantity * $part->unit_price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-end">Parts Total:</th>
                                            <th>
                                                @php
                                                    $partsTotal = $repairJob->parts->sum(function($part) {
                                                        return $part->quantity * $part->unit_price;
                                                    });
                                                @endphp
                                                {{ number_format($partsTotal, 2) }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- Cost Information --}}
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Cost Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($repairJob->estimated_cost)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Estimated Cost</strong></label>
                                        <p>{{ number_format($repairJob->estimated_cost, 2) }}</p>
                                    </div>
                                @endif

                                @if($repairJob->actual_cost)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Actual Cost</strong></label>
                                        <p>{{ number_format($repairJob->actual_cost, 2) }}</p>
                                    </div>
                                @endif

                                @if($repairJob->parts_cost)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Parts Cost</strong></label>
                                        <p>{{ number_format($repairJob->parts_cost, 2) }}</p>
                                    </div>
                                @endif

                                @if($repairJob->labor_cost)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Labor Cost</strong></label>
                                        <p>{{ number_format($repairJob->labor_cost, 2) }}</p>
                                    </div>
                                @endif

                                @if($repairJob->estimated_cost && $repairJob->actual_cost)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Variance</strong></label>
                                        <p>
                                            @php
                                                $variance = $repairJob->actual_cost - $repairJob->estimated_cost;
                                                $varianceClass = $variance > 0 ? 'text-danger' : 'text-success';
                                            @endphp
                                            <span class="{{ $varianceClass }}">
                                                {{ $variance > 0 ? '+' : '' }}{{ number_format($variance, 2) }}
                                            </span>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    {{-- Timeline --}}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Timeline</h3>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item">
                                    <strong>Created:</strong><br>
                                    {{ $repairJob->created_at->format('M d, Y H:i') }}
                                </div>

                                @if($repairJob->estimated_completion_date)
                                    <div class="list-group-item">
                                        <strong>Est. Completion:</strong><br>
                                        {{ $repairJob->estimated_completion_date->format('M d, Y') }}
                                    </div>
                                @endif

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

                    {{-- Quick Actions --}}
                    @if($repairJob->status !== 'delivered' && $repairJob->status !== 'cancelled')
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Quick Actions</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addPartModal">
                                        Add Part
                                    </button>
                                    @if(currentShop()->hasFeature('diagnostics'))
                                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#addDiagnosticModal">
                                            Add Diagnostic
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Status History --}}
                    @if($repairJob->status_history)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Status History</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    @foreach(json_decode($repairJob->status_history, true) ?? [] as $history)
                                        <li class="mb-2">
                                            <span class="badge bg-{{ $statusClasses[$history['status']] ?? 'secondary' }}">
                                                {{ str_replace('_', ' ', ucfirst($history['status'])) }}
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($history['date'])->format('M d, Y H:i') }}</small>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @if(currentShop()->hasFeature('technician_assign'))
        <!-- Assign Technician Modal -->
        <div class="modal fade" id="assignTechnicianModal" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('tech.repairs.assign', $repairJob) }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Assign Technician</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">Technician</label>
                                <select name="technician_id" class="form-select" required>
                                    <option value="">Select Technician</option>
                                    @foreach($technicians ?? [] as $tech)
                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Assign</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Complete Diagnosis Modal -->
    <div class="modal fade" id="completeDiagnosisModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('tech.repairs.approve', $repairJob) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Complete Diagnosis</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Estimated Cost</label>
                            <input type="number" name="estimated_cost" class="form-control"
                                value="{{ $repairJob->estimated_cost }}" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Diagnostic Notes</label>
                            <textarea name="diagnostic_notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Complete & Await Approval</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Part Modal -->
    <div class="modal fade" id="addPartModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('tech.repairs.parts.store', $repairJob) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Part</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Part Name</label>
                            <input type="text" name="part_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Part Number</label>
                            <input type="text" name="part_number" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Quantity</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Unit Price</label>
                            <input type="number" name="unit_price" class="form-control" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Part</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(currentShop()->hasFeature('diagnostics'))
        <!-- Add Diagnostic Modal -->
        <div class="modal fade" id="addDiagnosticModal" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('tech.repairs.diagnostics.store', $repairJob) }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Diagnostic</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">Diagnostic Type</label>
                                <select name="diagnostic_type" class="form-select" required>
                                    <option value="visual">Visual Inspection</option>
                                    <option value="hardware">Hardware Test</option>
                                    <option value="software">Software Test</option>
                                    <option value="performance">Performance Test</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Findings</label>
                                <textarea name="findings" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Recommendations</label>
                                <textarea name="recommendations" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Diagnostic</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

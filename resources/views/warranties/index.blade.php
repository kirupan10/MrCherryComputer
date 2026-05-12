@extends('layouts.nexora')

@section('title', 'Warranties')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Warranty Management') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs')
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <x-alert/>

        <div class="row row-cards">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-bulb me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 12h1m8 -9v1m8 8h1m-15.4 -6.4l.7 .7m12.1 -.7l-.7 .7" />
                                <path d="M9 16a5 5 0 1 1 6 0a3.5 3.5 0 0 0 -1 3a2 2 0 0 1 -4 0a3.5 3.5 0 0 0 -1 -3" />
                                <path d="M9.7 17l4.6 0" />
                            </svg>
                            Warranty Tips
                        </h3>

                        <div class="list-group list-group-flush mb-3">
                            <div class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar bg-success text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 5l0 14"/>
                                                <path d="M5 12l14 0"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="text-truncate">
                                            <strong>Create Warranties</strong>
                                        </div>
                                        <div class="text-muted small">Set up warranty periods</div>
                                    </div>
                                </div>
                            </div>

                            <div class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar bg-info text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                                                <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/>
                                                <path d="M9 12l.01 0"/>
                                                <path d="M13 12l2 0"/>
                                                <path d="M9 16l.01 0"/>
                                                <path d="M13 16l2 0"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="text-truncate">
                                            <strong>Assign to Products</strong>
                                        </div>
                                        <div class="text-muted small">Link warranties to items</div>
                                    </div>
                                </div>
                            </div>

                            <div class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar bg-warning text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="text-truncate">
                                            <strong>Shop Specific</strong>
                                        </div>
                                        <div class="text-muted small">Only for your shop</div>
                                    </div>
                                </div>
                            </div>

                            <div class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar bg-purple text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 8v4l3 3"/>
                                                <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="text-truncate">
                                            <strong>Track Duration</strong>
                                        </div>
                                        <div class="text-muted small">View months & years</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mb-0">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                        <path d="M12 9h.01"/>
                                        <path d="M11 12h1v4h1"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="alert-title">Quick Info</h4>
                                    <div class="text-muted">Warranties help you manage product guarantees. Common periods are 12, 24, or 36 months. You can edit or delete warranties that aren't assigned to products.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Warranty Management</h3>
                        <div class="card-actions">
                            <a href="{{ shop_route('warranties.create') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 5l0 14"/>
                                    <path d="M5 12l14 0"/>
                                </svg>
                                Add Warranty
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @if($warranties->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Duration (Months)</th>
                                            <th>Years</th>
                                            <th>Products Count</th>
                                            <th class="w-1">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($warranties as $warranty)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2" style="background-color: #206bc4; color: white;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold">{{ $warranty->name }}</div>
                                                            <div class="text-muted small">{{ $warranty->slug }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($warranty->duration)
                                                        <span class="badge bg-blue">{{ $warranty->duration }} months</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($warranty->years)
                                                        {{ $warranty->years }} {{ $warranty->years == 1 ? 'year' : 'years' }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $warranty->products()->count() }}</span>
                                                </td>
                                                <td>
                                                    <div class="btn-list flex-nowrap">
                                                        <a href="{{ shop_route('warranties.edit', $warranty) }}" class="btn btn-sm btn-white">
                                                            Edit
                                                        </a>
                                                        <form action="{{ shop_route('warranties.destroy', $warranty) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this warranty?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $warranties->links() }}
                            </div>
                        @else
                            <div class="empty">
                                <div class="empty-img">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"/>
                                    </svg>
                                </div>
                                <p class="empty-title">No warranties found</p>
                                <p class="empty-subtitle text-muted">
                                    Get started by creating your first warranty period.
                                </p>
                                <div class="empty-action">
                                    <a href="{{ shop_route('warranties.create') }}" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M12 5l0 14"/>
                                            <path d="M5 12l14 0"/>
                                        </svg>
                                        Add your first warranty
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('shop-types.tech.layouts.nexora')

@section('title', 'Permission Management')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Permissions
                </h2>
                <div class="text-muted mt-1">Control what managers and employees can do in your shop</div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="px-0 mb-0 mt-2">
            <nav class="nav nav-borders">
                @if(shop_route_exists('user.profile'))
                <a class="nav-link ms-0" href="{{ shop_route('user.profile') }}">Profile</a>
                @elseif(shop_route_exists('profile'))
                <a class="nav-link ms-0" href="{{ shop_route('profile') }}">Profile</a>
                @endif
                @if(shop_route_exists('profile.settings'))
                <a class="nav-link" href="{{ shop_route('profile.settings') }}">Settings</a>
                @elseif(shop_route_exists('settings'))
                <a class="nav-link" href="{{ shop_route('settings') }}">Settings</a>
                @endif
                @if(shop_route_exists('features'))
                <a class="nav-link" href="{{ shop_route('features') }}">Features</a>
                @endif
                <a class="nav-link active" href="{{ shop_route('permissions.index') }}">Permissions</a>
            </nav>
            <hr class="mt-0 mb-4" />
        </div>

        <x-alert/>

        <form action="{{ shop_route('permissions.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row row-cards">
                @php
                    $groupIcons = [
                        'products'  => '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5"/><path d="M12 12l8 -4.5"/><path d="M12 12l0 9"/><path d="M12 12l-8 -4.5"/>',
                        'sales'     => '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/><path d="M9 12h6"/><path d="M9 16h6"/>',
                        'customers' => '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>',
                        'finance'   => '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 0 0 4h-2a2 2 0 0 0 -1.8 -1"/><path d="M12 7v10"/>',
                        'reports'   => '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/><path d="M9 12h6"/><path d="M9 16h4"/>',
                    ];
                    $groupLabels = [
                        'products'  => 'Products',
                        'sales'     => 'Sales',
                        'customers' => 'Customers',
                        'finance'   => 'Finance',
                        'reports'   => 'Reports',
                    ];
                    $canEditManager = Auth::user()->isShopOwner() || Auth::user()->isAdmin();
                @endphp

                @foreach($definitions as $groupKey => $group)
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    {!! $groupIcons[$groupKey] ?? '' !!}
                                </svg>
                                {{ ucfirst($groupLabels[$groupKey] ?? $groupKey) }}
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Permission</th>
                                            <th class="text-center" style="width:110px;">
                                                <span class="badge bg-blue-lt">Manager</span>
                                            </th>
                                            <th class="text-center" style="width:110px;">
                                                <span class="badge bg-cyan-lt">Employee</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group as $permKey => $defaults)
                                        <tr>
                                            <td>
                                                <span class="text-body">{{ $defaults['label'] }}</span>
                                            </td>
                                            {{-- Manager --}}
                                            <td class="text-center">
                                                @if($canEditManager)
                                                <label class="form-check form-check-single form-switch d-flex justify-content-center m-0">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           name="permissions[manager][{{ $permKey }}]"
                                                           value="1"
                                                           {{ ($permissions['manager'][$permKey] ?? $defaults['manager']) ? 'checked' : '' }}>
                                                </label>
                                                @else
                                                    {{-- Managers see their own permissions as read-only --}}
                                                    @if($permissions['manager'][$permKey] ?? $defaults['manager'])
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12"/><path d="M6 6l12 12"/></svg>
                                                    @endif
                                                @endif
                                            </td>
                                            {{-- Employee --}}
                                            <td class="text-center">
                                                <label class="form-check form-check-single form-switch d-flex justify-content-center m-0">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           name="permissions[employee][{{ $permKey }}]"
                                                           value="1"
                                                           {{ ($permissions['employee'][$permKey] ?? $defaults['employee']) ? 'checked' : '' }}>
                                                </label>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="row mt-2 mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="text-muted mb-0 small">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1 text-blue" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9h.01"/><path d="M11 12h1v4h1"/><path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"/></svg>
                                        <strong>Shop Owner</strong> always has full access. These permissions apply to <strong>Managers</strong> and <strong>Employees</strong> only.
                                        @if(!$canEditManager)
                                            <br>As a <strong>Manager</strong>, you can only modify employee permissions.
                                        @endif
                                    </p>
                                </div>
                                <div class="ms-auto">
                                    <button type="submit" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"/><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M14 4l0 4l-6 0l0 -4"/></svg>
                                        Save Permissions
                                    </button>
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

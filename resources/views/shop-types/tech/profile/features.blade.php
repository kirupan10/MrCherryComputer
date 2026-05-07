@extends('shop-types.tech.layouts.nexora')

@section('title', 'Features')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Features
                </h2>
                <div class="text-muted mt-1">Manage feature settings for your shop</div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
<div class="container-xl">

    <div class="px-0 mb-0 mt-2">
        <nav class="nav nav-borders">
            <a class="nav-link ms-0" href="{{ shop_route('user.profile') }}">Profile</a>
            @if(shop_route_exists('profile.settings'))
            <a class="nav-link" href="{{ shop_route('profile.settings') }}">Settings</a>
            @endif
            <a class="nav-link active" href="{{ shop_route('features') }}">Features</a>
            @if(\Route::has('permissions.index'))
            <a class="nav-link" href="{{ shop_route('permissions.index') }}">Permissions</a>
            @endif
        </nav>
        <hr class="mt-0 mb-4" />
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M3 21h18"/>
                            <path d="M5 21v-8"/>
                            <path d="M12 21v-14"/>
                            <path d="M19 21v-5"/>
                        </svg>
                        Features
                    </h3>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/>
                            <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/>
                        </svg>
                    </div>
                    <h3 class="text-muted mb-2">No features configured yet</h3>
                    <p class="text-muted mb-0">
                        Features specific to <strong>{{ ucwords(str_replace('_', ' ', $shop->shop_type->value)) }}</strong> shops are coming soon.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title mb-1">Quick Links</h4>
                    <p class="text-muted small mb-3">Jump to related account pages.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ shop_route('user.profile') }}" class="btn btn-outline-primary btn-sm">Profile</a>
                        <a href="{{ shop_route('profile.settings') }}" class="btn btn-outline-primary btn-sm">Settings</a>
                        <a href="{{ shop_route('features') }}" class="btn btn-outline-primary btn-sm">Features</a>
                        @if(\Route::has('permissions.index'))
                        <a href="{{ shop_route('permissions.index') }}" class="btn btn-outline-primary btn-sm">Permissions</a>
                        @else
                        <a href="{{ shop_route('profile.settings') }}" class="btn btn-outline-primary btn-sm">Permissions</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-1">Shop Info</h4>
                    <p class="text-muted small mb-3">Your current shop details.</p>
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Name</dt>
                        <dd class="col-7">{{ $shop->name }}</dd>
                        <dt class="col-5 text-muted">Type</dt>
                        <dd class="col-7">{{ ucwords(str_replace('_', ' ', $shop->shop_type->value)) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
@endsection

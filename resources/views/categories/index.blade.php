@extends('layouts.nexora')

@section('content')
<div class="page-body">
    <div class="container-fluid categories-page">
        <x-alert/>

        <style>
            .categories-page .hero-card {
                position: relative;
                border: 1px solid #dbe6f3;
                border-radius: 18px;
                background:
                    radial-gradient(circle at 12% 20%, rgba(29, 78, 216, 0.08) 0%, rgba(29, 78, 216, 0) 40%),
                    radial-gradient(circle at 88% 80%, rgba(14, 116, 144, 0.08) 0%, rgba(14, 116, 144, 0) 38%),
                    linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            }

            .categories-page .hero-title {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-weight: 700;
                letter-spacing: 0.01em;
                font-size: 1.15rem;
            }

            .categories-page .hero-actions {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                flex-wrap: wrap;
            }

            .categories-page .hero-actions .btn {
                border-radius: 10px;
                font-weight: 600;
                padding-inline: 0.9rem;
            }

            .categories-page .stats-card {
                border: 1px solid #e4ebf5;
                border-radius: 14px;
                background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
                transition: transform 0.22s ease, box-shadow 0.22s ease;
            }

            .categories-page .stats-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 16px 34px rgba(16, 24, 40, 0.1);
            }

            .categories-page .stats-card .text-muted {
                font-size: 0.82rem;
            }

            .categories-page .category-table-card {
                border: 1px solid #e6ecf3;
                border-radius: 14px;
                overflow: hidden;
                box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
            }

            .categories-page .category-table-card .card-header {
                background: linear-gradient(180deg, #fcfdff 0%, #f8fbff 100%);
                border-bottom: 1px solid #e8eef6;
            }

            .categories-page .category-table-card .table thead th {
                background: #f7f9fc;
                border-bottom: 1px solid #e6ebf2;
                font-size: 0.75rem;
                letter-spacing: 0.04em;
                text-transform: uppercase;
            }

            .categories-page .tips-card {
                border: 1px solid #cfe0fb;
                border-radius: 14px;
                background: linear-gradient(180deg, #eff6ff 0%, #f8fbff 100%);
                box-shadow: 0 10px 22px rgba(30, 64, 175, 0.08);
            }

            .categories-page .tips-card ul li {
                padding: 0.65rem 0.75rem;
                border-radius: 10px;
                background: rgba(255, 255, 255, 0.55);
                border: 1px solid rgba(255, 255, 255, 0.8);
            }

            .categories-page .tips-card ul li + li {
                margin-top: 0.55rem;
            }

            .categories-page .section-label {
                border: 1px solid #dbe6f6;
                background: #f2f7ff;
                color: #1e3a8a;
                border-radius: 999px;
                font-size: 0.72rem;
                font-weight: 600;
                padding: 0.3rem 0.6rem;
            }

            @media (max-width: 767.98px) {
                .categories-page .hero-title {
                    margin-bottom: 0.5rem;
                }

                .categories-page .hero-actions .btn {
                    width: 100%;
                    justify-content: center;
                }

                .categories-page .category-table-card,
                .categories-page .tips-card,
                .categories-page .stats-card,
                .categories-page .hero-card {
                    border-radius: 12px;
                }
            }
        </style>

        <!-- Page Header Card -->
        <div class="row mb-4 categories-page">
            <div class="col-12">
                <div class="card hero-card">
                    <div class="card-header">
                        <h3 class="card-title hero-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M4 4h6v6h-6z" /><path d="M14 4h6v6h-6z" /><path d="M4 14h6v6h-6z" /><path d="M14 14h6v6h-6z" />
                            </svg>
                            Categories Management
                        </h3>
                        <div class="card-actions hero-actions">
                            <a href="{{ shop_route('products.index') }}" class="btn btn-outline-secondary me-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5"/>
                                    <path d="M12 12l8 -4.5"/>
                                    <path d="M12 12l0 9"/>
                                    <path d="M12 12l-8 -4.5"/>
                                </svg>
                                View Products
                            </a>
                            <a href="{{ shop_route('categories.create') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 5l0 14" /><path d="M5 12l14 0" />
                                </svg>
                                New Category
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <span class="section-label mb-2 d-inline-flex">Product Organization Hub</span>
                        <p class="text-muted mb-0">Organize your products into categories for better management and customer navigation. Create logical groupings that help you and your customers find products quickly.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4 categories-page">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm stats-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 4h6v6h-6z" /><path d="M14 4h6v6h-6z" /><path d="M4 14h6v6h-6z" /><path d="M14 14h6v6h-6z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    Total Categories
                                </div>
                                <div class="text-muted">
                                    @if($categories->isEmpty())
                                        0 Categories
                                    @else
                                        {{ $categories->count() }} Categories
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm stats-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-success text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    Active Categories
                                </div>
                                <div class="text-muted">
                                    @if($categories->isEmpty())
                                        0 Active
                                    @else
                                        {{ $categories->count() }} Active
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm stats-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-info text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5"/>
                                        <path d="M12 12l8 -4.5"/>
                                        <path d="M12 12l0 9"/>
                                        <path d="M12 12l-8 -4.5"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    Total Products
                                </div>
                                <div class="text-muted">
                                    @if($categories->isEmpty())
                                        0 Items
                                    @else
                                        {{ $categories->sum(function($category) { return $category->products->count(); }) }} Items
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm stats-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-warning text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                        <path d="M12 9h.01"/>
                                        <path d="M11 12h1v4h1"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    Avg. Products
                                </div>
                                <div class="text-muted">
                                    @if($categories->isEmpty())
                                        0 Items/Category
                                    @else
                                        {{ round($categories->sum(function($category) { return $category->products->count(); }) / $categories->count(), 1) }} Items/Category
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($categories->isEmpty())
            <!-- Empty State with Enhanced Tips -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="empty">
                        <div class="empty-img">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted" width="96" height="96" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M4 4h6v6h-6z" /><path d="M14 4h6v6h-6z" /><path d="M4 14h6v6h-6z" /><path d="M14 17h6" /><path d="M17 14v6" />
                            </svg>
                        </div>
                        <h3 class="empty-title">No Categories Yet</h3>
                        <p class="empty-subtitle text-muted">
                            Start organizing your products by creating your first category.<br>
                            Categories help you manage inventory and make it easier for customers to browse products.
                        </p>
                        <div class="empty-action">
                            <a href="{{ shop_route('categories.create') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 5l0 14" /><path d="M5 12l14 0" />
                                </svg>
                                Create Your First Category
                            </a>
                        </div>
                    </div>

                    <!-- Getting Started Guide -->
                    <div class="row g-3 mt-4 text-start">
                        <div class="col-md-6 offset-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h4 class="card-title">🚀 Quick Start Guide</h4>
                                    <div class="steps steps-vertical">
                                        <div class="step-item">
                                            <div class="h4 m-0">1</div>
                                            <div>
                                                <div class="font-weight-bold">Create Categories</div>
                                                <div class="text-muted small">Click the button above to create your first product category</div>
                                            </div>
                                        </div>
                                        <div class="step-item">
                                            <div class="h4 m-0">2</div>
                                            <div>
                                                <div class="font-weight-bold">Add Products</div>
                                                <div class="text-muted small">Assign products to categories for better organization</div>
                                            </div>
                                        </div>
                                        <div class="step-item">
                                            <div class="h4 m-0">3</div>
                                            <div>
                                                <div class="font-weight-bold">Manage & Track</div>
                                                <div class="text-muted small">Monitor category performance and adjust as needed</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Categories Table and Tips Side by Side -->
            <div class="row categories-page">
                <div class="col-md-7 col-lg-8"> <!-- 60% left -->
                    <div class="card category-table-card">
                        <div class="card-header">
                            <h3 class="card-title">All Categories</h3>
                            <div class="card-actions">
                                <span class="section-label">{{ $categories->count() }} total</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                @livewire('tables.category-table')
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 col-lg-4"> <!-- 40% right -->
                    <div class="card tips-card">
                        <div class="card-body">
                            <h4 class="card-title">💡 Category Management Tips</h4>
                            <ul class="list-unstyled mt-3 mb-0">
                                <li>
                                    <strong>Use Clear Names:</strong> Choose descriptive category names that customers can easily understand and search for.
                                </li>
                                <li>
                                    <strong>Keep It Simple:</strong> Avoid creating too many categories. 5-15 main categories work best for most businesses.
                                </li>
                                <li>
                                    <strong>Regular Review:</strong> Periodically review and reorganize categories based on product performance and customer behavior.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>


        @endif
    </div>
</div>
@endsection

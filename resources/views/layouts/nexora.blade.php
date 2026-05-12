<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

    <!-- CSS files -->
    <link href="{{ asset('dist/css/nexora.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('css/custom-colors.css') }}" rel="stylesheet"/>
    <link href="{{ asset('css/mobile-responsive.css') }}" rel="stylesheet"/>
    <link href="{{ asset('vendor/tom-select/tom-select.css') }}" rel="stylesheet"/>
    <style>
        @import url('https://rsms.me/inter/inter.css');

        /* Disable number input spinners */
        input[type='number']::-webkit-outer-spin-button,
        input[type='number']::-webkit-inner-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }
        input[type='number'] {
            -moz-appearance: textfield !important;
            appearance: textfield;
        }

        /* Global stability fixes */
        * {
            box-sizing: border-box;
        }

        :root {
            --nexora-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-feature-settings: "cv03", "cv04", "cv11";
            font-display: swap;
        }

        /* Prevent layout shifts globally */
        .icon, svg.icon {
            width: 18px !important;
            height: 18px !important;
            min-width: 18px;
            min-height: 18px;
            display: inline-block;
            flex-shrink: 0;
            vertical-align: middle;
        }

        .btn {
            min-height: 38px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            contain: layout style;
        }

        .btn .icon {
            margin-right: 0.375rem;
        }

        .btn-sm {
            min-height: 32px !important;
        }

        /* Form controls stability */
        .form-control, .form-select {
            min-height: 38px !important;
        }

        /* Card stability */
        .card {
            contain: layout;
        }

        .card-header {
            min-height: 50px;
            display: flex;
            align-items: center;
        }

        /* Avatar stability */
        .avatar {
            width: 2.5rem !important;
            height: 2.5rem !important;
            min-width: 2.5rem;
            min-height: 2.5rem;
            flex-shrink: 0;
            contain: layout;
        }

        .avatar-sm {
            width: 1.75rem !important;
            height: 1.75rem !important;
            min-width: 1.75rem;
            min-height: 1.75rem;
        }

        /* Badge stability */
        .badge {
            display: inline-flex;
            align-items: center;
            min-height: 20px;
            padding: 0.25rem 0.5rem;
        }

        /* Modal stability */
        .modal-dialog {
            min-height: 200px;
            contain: layout;
        }

        .modal-content {
            min-height: 150px;
        }

        .modal-body {
            min-height: 100px;
        }

        /* Spinner stability */
        .spinner-border {
            width: 2rem !important;
            height: 2rem !important;
            min-width: 2rem;
            min-height: 2rem;
            flex-shrink: 0;
        }

        .spinner-border-sm {
            width: 1rem !important;
            height: 1rem !important;
            min-width: 1rem;
            min-height: 1rem;
        }

        /* Image stability */
        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        /* Brand styling */
        .navbar-brand-text {
            color: #000000 !important;
            text-decoration: none !important;
        }

        .navbar-brand:hover .navbar-brand-text {
            color: #000000 !important;
            text-decoration: none !important;
        }

        /* Enhanced navbar link styling */
        .navbar .nav-link-title {
            font-size: 1rem !important;
            font-weight: 400 !important;
            letter-spacing: -0.02em !important;
            color: #000000 !important;
            transition: color 0.2s ease !important;
        }

        .navbar .nav-link:hover .nav-link-title {
            color: #000000 !important;
        }

        .navbar .nav-item.active .nav-link-title {
            color: #000000 !important;
            font-weight: 400 !important;
        }

        /* Enhanced dropdown text styling */
        .navbar .dropdown-item {
            font-size: 0.9375rem !important;
            font-weight: 400 !important;
            letter-spacing: -0.01em !important;
            color: #000000 !important;
            padding: 0.5rem 1rem !important;
            transition: all 0.15s ease !important;
        }

        .navbar .dropdown-item:hover {
            background-color: #f1f5f9 !important;
            color: #000000 !important;
            font-weight: 400 !important;
        }

        .navbar .dropdown-item.active {
            background-color: #dbeafe !important;
            color: #000000 !important;
            font-weight: 400 !important;
        }

        .navbar .dropdown-header {
            font-size: 0.75rem !important;
            font-weight: 400 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            color: #000000 !important;
            padding: 0.5rem 1rem !important;
        }

        /* Fix navbar dropdown visibility */
        .navbar-expand-md {
            overflow: visible !important;
        }

        .navbar-collapse {
            overflow: visible !important;
        }

        .navbar {
            overflow: visible !important;
        }

        /* Remove all space constraints from page containers */
        .page {
            overflow: visible !important;
            height: auto !important;
            min-height: 100vh !important;
        }

        .page-wrapper {
            overflow: visible !important;
            height: auto !important;
        }

        .page-body {
            overflow: visible !important;
            height: auto !important;
        }

        /* Ensure navbar and header don't have height constraints */
        header.navbar,
        header.navbar-expand-md {
            overflow: visible !important;
            height: auto !important;
            max-height: none !important;
            border: none !important;
        }

        /* Remove container constraints */
        .container-xxl {
            max-width: 100% !important;
            padding-left: 1.5rem !important;
            padding-right: 1.5rem !important;
        }

        /* Remove borders from navbar elements */
        .navbar,
        .navbar-collapse {
            border: none !important;
        }

        .navbar .dropdown-menu {
            position: absolute !important;
            max-height: none !important;
            overflow-y: visible !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15) !important;
            border: 1px solid #e6e8ea !important;
            z-index: 1030 !important;
            background-color: #ffffff !important;
            backdrop-filter: blur(10px) !important;
        }

        .navbar .dropdown-menu-columns {
            display: flex !important;
            flex-wrap: nowrap !important;
            min-width: 280px !important;
        }

        .navbar .dropdown-menu-column {
            flex: 1 1 auto !important;
            min-width: 140px !important;
            padding: 0.5rem !important;
        }

        /* Fix header user dropdown visibility */
        header.navbar .nav-item.dropdown {
            position: static !important;
        }

        header.navbar .dropdown-menu {
            z-index: 1050 !important;
        }

        header.navbar .navbar-nav {
            position: static !important;
        }

        /* Preloader Styles */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: opacity 0.3s ease-out;
        }

        #preloader.fade-out {
            opacity: 0;
            pointer-events: none;
        }

        .preloader-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .preloader-text {
            margin-top: 20px;
            color: #6b7280;
            font-size: 14px;
            font-weight: 400;
        }

        /* Prevent content from showing during load */
        body.loading .page {
            opacity: 0;
        }

        /* Fix: Ensure navbar dropdown menus are visually attached and shadowed */
        .navbar .dropdown-menu {
            margin-top: 0.5rem !important;
            left: 0 !important;
            right: auto !important;
            min-width: 220px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08), 0 1.5px 4px rgba(0,0,0,0.03);
            border-radius: 0.5rem;
            border: 1px solid #e6e8ea;
            padding: 0.5rem 0;
        }

        /* Ensure dropdown is visually attached to parent */
        .navbar .dropdown-menu:before {
            content: '';
            display: block;
            position: absolute;
            top: -8px;
            left: 24px;
            width: 16px;
            height: 16px;
            background: #fff;
            border-left: 1px solid #e6e8ea;
            border-top: 1px solid #e6e8ea;
            transform: rotate(45deg);
            z-index: 1;
        }

        /* Remove weird floating effect on open */
        .navbar .dropdown.show .dropdown-menu {
            display: block !important;
            opacity: 1 !important;
            pointer-events: auto;
        }

        /* Main navbar dropdown: keep submenu directly under each menu item */
        #navbar-menu .nav-item.dropdown {
            position: relative !important;
        }

        #navbar-menu .nav-item.dropdown > .dropdown-menu {
            top: calc(100% + 6px) !important;
            left: 0 !important;
            right: auto !important;
            margin-top: 0 !important;
            min-width: 220px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.10), 0 2px 8px rgba(0,0,0,0.04);
            border-radius: 0.5rem;
            border: 1px solid #e6e8ea;
            padding: 0.5rem 0;
            background: #fff;
            z-index: 1050;
            opacity: 1 !important;
            pointer-events: auto;
        }

        #navbar-menu .nav-item.dropdown > .dropdown-menu.dropdown-menu-end {
            right: 0 !important;
            left: auto !important;
        }

        #navbar-menu .dropdown-menu.show,
        #navbar-menu .dropdown.show > .dropdown-menu {
            display: block !important;
        }

        #navbar-menu .nav-item.dropdown > .dropdown-menu:before {
            content: '';
            display: block;
            position: absolute;
            top: -8px;
            left: 24px;
            width: 16px;
            height: 16px;
            background: #fff;
            border-left: 1px solid #e6e8ea;
            border-top: 1px solid #e6e8ea;
            transform: rotate(45deg);
            z-index: 1;
        }

        @media (max-width: 991.98px) {
            #navbar-menu .dropdown-menu {
                position: static !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                margin-top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                min-width: 100%;
                top: auto !important;
            }
            #navbar-menu .dropdown-menu:before {
                display: none;
            }
        }
    </style>

    <!-- Custom CSS for specific page.  -->
    @stack('page-styles')
    @livewireStyles
</head>
    <body class="loading">
        <!-- Preloader -->
        <div id="preloader">
            <div class="preloader-spinner"></div>
            <div class="preloader-text">Loading...</div>
        </div>

        <div class="page">

            @include('layouts.body.header')

            @include('layouts.body.navbar')

            <div class="page-wrapper">
                <div class="page-body">
                    <div class="container-xxl">
                        @yield('content')
                    </div>
                </div>

                @include('layouts.body.footer')
            </div>
        </div>

        <!-- Preloader Script -->
        <script>
            // Hide preloader when page is fully loaded
            window.addEventListener('load', function() {
                const preloader = document.getElementById('preloader');
                const body = document.body;

                // Add fade-out class
                preloader.classList.add('fade-out');
                body.classList.remove('loading');

                // Remove preloader from DOM after transition
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 300);
            });

            // Fallback: Hide preloader after 3 seconds
            setTimeout(function() {
                const preloader = document.getElementById('preloader');
                if (preloader && !preloader.classList.contains('fade-out')) {
                    preloader.classList.add('fade-out');
                    document.body.classList.remove('loading');
                    setTimeout(function() {
                        preloader.style.display = 'none';
                    }, 300);
                }
            }, 3000);
        </script>

        <!-- Nexora Core -->
        <script src="{{ asset('dist/js/nexora.min.js') }}" defer></script>

        <!-- TomSelect for product search -->
        <script src="{{ asset('vendor/tom-select/tom-select.complete.min.js') }}"></script>

        <!-- Shop Switching Script -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle shop switching
            document.querySelectorAll('.shop-switch-item').forEach(function(item) {
                item.addEventListener('click', function(e) {
                    e.preventDefault();

                    const shopId = this.getAttribute('data-shop-id');

                    // Don't switch if already active
                    if (this.classList.contains('active')) {
                        return;
                    }

                    // Show loading state
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Switching...';
                    this.style.pointerEvents = 'none';

                    // Send AJAX request to switch shop
                    fetch('{{ shop_route("shop.switch") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ shop_id: shopId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload page to apply new shop context
                            window.location.reload();
                        } else {
                            // Restore original content and show error
                            this.innerHTML = originalContent;
                            this.style.pointerEvents = '';
                            alert(data.message || 'Failed to switch shop');
                        }
                    })
                    .catch(error => {
                        // Restore original content and show error
                        this.innerHTML = originalContent;
                        this.style.pointerEvents = '';
                        console.error('Shop switch error:', error);
                        alert('An error occurred while switching shops');
                    });
                });
            });
        });
        </script>

        <!-- Force cleanup of modal state after any modal is hidden -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.modal').forEach(function(modalEl) {
                modalEl.addEventListener('hidden.bs.modal', function() {
                    // Remove lingering modal-open class and backdrops
                    document.body.classList.remove('modal-open');
                    document.querySelectorAll('.modal-backdrop').forEach(function(b) { b.remove(); });
                });
            });
        });
        </script>

        <!-- Bootstrap Bundle JS for Modal functionality -->
        <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>

        <!-- Fix: Ensure Bootstrap dropdowns work natively and are not interfered with by custom JS -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Reset only main navbar dropdown state on page load.
            document.querySelectorAll('#navbar-menu .nav-item.dropdown, #navbar-menu .dropdown-menu').forEach(function(el) {
                el.classList.remove('show');
            });

            // Deterministic click behavior for main nav dropdowns.
            var navMenu = document.getElementById('navbar-menu');
            if (!navMenu) return;

            var dropdownTriggerList = [].slice.call(
                navMenu.querySelectorAll('.nav-item.dropdown > .dropdown-toggle')
            );

            var closeAllDropdowns = function() {
                navMenu.querySelectorAll('.nav-item.dropdown.show').forEach(function(openDropdown) {
                    openDropdown.classList.remove('show');
                });
                navMenu.querySelectorAll('.dropdown-menu.show').forEach(function(openMenu) {
                    openMenu.classList.remove('show');
                });
            };

            dropdownTriggerList.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var dropdown = toggle.closest('.nav-item.dropdown');
                    if (!dropdown) return;

                    var menu = dropdown.querySelector('.dropdown-menu');
                    if (!menu) return;

                    var isOpen = dropdown.classList.contains('show');
                    closeAllDropdowns();

                    if (!isOpen) {
                        dropdown.classList.add('show');
                        menu.classList.add('show');
                    }
                });
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('#navbar-menu .nav-item.dropdown')) {
                    closeAllDropdowns();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeAllDropdowns();
                }
            });
        });
        </script>

        {{--- Page Scripts ---}}
        @stack('page-scripts')

        @livewireScripts
    </body>
</html>

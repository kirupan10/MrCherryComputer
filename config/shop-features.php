<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shop Type Features Configuration
    |--------------------------------------------------------------------------
    |
    | Define available features for each shop type. Features control what
    | functionality is available in the system for each type of shop.
    |
    */

    'tech_shop' => [
        'products',             // Product management
        'categories',          // Product categories
        'orders',              // Sales orders
        'customers',           // Customer management
        'sales_reports',       // Sales reporting
        'inventory',           // Inventory management
        'serial_numbers',      // Serial number tracking
        'warranty',            // Warranty management
        'repairs',             // Repair tracking
        'jobs',                // Job/Work orders
        'technician_assign',   // Assign technicians
        'diagnostics',         // Diagnostic tools
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Labels & Descriptions
    |--------------------------------------------------------------------------
    |
    | Human-readable labels and descriptions for each feature
    |
    */

    'feature_labels' => [
        'products' => 'Product Management',
        'services' => 'Service Management',
        'categories' => 'Categories',
        'orders' => 'Order Processing',
        'customers' => 'Customer Management',
        'sales_reports' => 'Sales Reports',
        'inventory' => 'Inventory Tracking',
        'serial_numbers' => 'Serial Number Tracking',
        'warranty' => 'Warranty Management',
        'repairs' => 'Repair Tracking',
        'jobs' => 'Job/Work Orders',
        'tables' => 'Table Management',
        'reservations' => 'Reservations',
        'appointments' => 'Appointment Booking',
        'barcode_scanning' => 'Barcode Scanning',
        'bulk_pricing' => 'Bulk Pricing',
        'supplier_management' => 'Supplier Management',
        'expiry_tracking' => 'Expiry Date Tracking',
        'prescription' => 'Prescription Management',
        'vehicle_tracking' => 'Vehicle Tracking',
        'service_history' => 'Service History',
        'technician_assign' => 'Technician Assignment',
        'kitchen_display' => 'Kitchen Display System',
        'staff_schedule' => 'Staff Scheduling',
        'service_packages' => 'Service Packages',
        'loyalty_program' => 'Loyalty Program',
        'commission_tracking' => 'Commission Tracking',
        'pos_stations' => 'Multiple POS Stations',
        'promotions' => 'Promotional Pricing',
        'size_variants' => 'Size/Color Variants',
        'returns' => 'Return Management',
        'layaway' => 'Layaway System',
        'gift_cards' => 'Gift Cards',
        'controlled_substances' => 'Controlled Substances',
        'insurance' => 'Insurance Processing',
        'patient_history' => 'Patient History',
        'demos' => 'Demo Unit Tracking',
        'trade_in' => 'Trade-in Program',
        'installation' => 'Installation Services',
        'diagnostics' => 'Diagnostic Tools',
        'parts_lookup' => 'Parts Catalog Lookup',
        'recipe_costing' => 'Recipe Costing',
        'tips_management' => 'Tips Management',
    ],
];

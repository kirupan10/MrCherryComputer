-- Barcode Database Verification and Fix Script
-- Run this in your MySQL/MariaDB console or phpMyAdmin

-- ========================================
-- 1. CHECK CURRENT BARCODE DATA
-- ========================================

-- View sample barcodes to verify format
SELECT id, code, barcode, shop_id, name
FROM products
WHERE barcode IS NOT NULL
LIMIT 10;

-- Check barcode column type (should be VARCHAR, not INT)
DESCRIBE products;

-- Count products with valid barcodes
SELECT
    COUNT(*) as total_products,
    COUNT(barcode) as products_with_barcode,
    COUNT(*) - COUNT(barcode) as products_missing_barcode
FROM products;

-- ========================================
-- 2. FIX BARCODE COLUMN TYPE (if needed)
-- ========================================

-- If barcode column is INT or BIGINT, change to VARCHAR
-- This prevents loss of leading zeros (e.g., "010000000001")
ALTER TABLE products
MODIFY COLUMN barcode VARCHAR(20) NULL;

-- ========================================
-- 3. UPDATE BARCODE SETTINGS
-- ========================================

-- Update existing barcode settings to use new dimensions
UPDATE barcode_settings
SET
    barcode_width = 3,
    barcode_height = 80
WHERE barcode_type = 'EAN13';

-- Verify barcode settings
SELECT * FROM barcode_settings;

-- ========================================
-- 4. REGENERATE MISSING BARCODES
-- ========================================

-- Find products missing barcodes
SELECT id, code, shop_id, name
FROM products
WHERE barcode IS NULL OR barcode = '';

-- Note: Instead of manually updating, use Laravel to regenerate:
-- php artisan tinker
-- Product::whereNull('barcode')->orWhere('barcode', '')->each(fn($p) => $p->save());

-- ========================================
-- 5. VERIFY BARCODE FORMAT
-- ========================================

-- Check barcode lengths (should be 12 for EAN-13)
SELECT
    LENGTH(barcode) as barcode_length,
    COUNT(*) as count,
    GROUP_CONCAT(id ORDER BY id LIMIT 5) as sample_product_ids
FROM products
WHERE barcode IS NOT NULL
GROUP BY LENGTH(barcode);

-- Barcodes should be 12 digits
-- If you see 13 digits, the check digit was stored (not ideal but works)
-- If you see length > 13 or < 12, there's a data issue

-- ========================================
-- 6. SAMPLE BARCODE VERIFICATION
-- ========================================

-- View detailed barcode info for specific product
SELECT
    id,
    code,
    barcode,
    LENGTH(barcode) as barcode_length,
    shop_id,
    name,
    SUBSTRING(barcode, 1, 2) as shop_prefix,
    SUBSTRING(barcode, 3, 10) as product_number
FROM products
WHERE id = 1; -- Change to your product ID

-- ========================================
-- 7. CHECK FOR DUPLICATE BARCODES
-- ========================================

-- Find duplicate barcodes (should not exist)
SELECT barcode, COUNT(*) as count
FROM products
WHERE barcode IS NOT NULL
GROUP BY barcode
HAVING COUNT(*) > 1;

-- ========================================
-- 8. SHOP-SPECIFIC BARCODE CHECK
-- ========================================

-- Verify barcodes start with correct shop prefix
SELECT
    p.shop_id,
    s.name as shop_name,
    COUNT(*) as total_products,
    COUNT(p.barcode) as products_with_barcode,
    MIN(p.barcode) as first_barcode,
    MAX(p.barcode) as last_barcode,
    LPAD(p.shop_id, 2, '0') as expected_prefix
FROM products p
LEFT JOIN shops s ON p.shop_id = s.id
GROUP BY p.shop_id, s.name;

-- ========================================
-- EXPECTED BARCODE FORMAT
-- ========================================
-- Format: SSNNNNNNNNNN (12 digits)
-- SS = Shop ID (2 digits, zero-padded)
-- NNNNNNNNNN = Product number from code (10 digits, zero-padded)
--
-- Example:
-- Shop ID: 1 → "01"
-- Product Code: "PRD00123" → extract "00123" → pad to "0000000123"
-- Final Barcode: "010000000123"
--
-- The barcode library (Picqer) will:
-- - Take the 12-digit barcode
-- - Calculate the 13th check digit
-- - Encode all 13 digits in the barcode image
-- ========================================

-- =====================================================
-- PROFIT DATA VALIDATION SQL QUERIES
-- =====================================================
-- Run these queries directly on your database to validate profit data

-- Query 1: Overall Statistics
-- Shows current state of profit data
SELECT
    'Total Order Details' as metric,
    COUNT(*) as value
FROM order_details
UNION ALL
SELECT
    'Products with Buying Price',
    COUNT(DISTINCT product_id)
FROM order_details
WHERE buying_price > 0
UNION ALL
SELECT
    'Products Missing Buying Price',
    COUNT(DISTINCT od.product_id)
FROM order_details od
LEFT JOIN products p ON od.product_id = p.id
WHERE (od.buying_price IS NULL OR od.buying_price = 0)
  AND (p.buying_price IS NULL OR p.buying_price = 0)
UNION ALL
SELECT
    'Total Revenue',
    SUM(quantity * selling_price)
FROM order_details
UNION ALL
SELECT
    'Total Cost (Valid)',
    SUM(quantity * COALESCE(buying_price, 0))
FROM order_details
WHERE buying_price > 0;

-- Query 2: Products Without Buying Prices
-- These products are excluded from profit calculations
SELECT
    od.id,
    p.id as product_id,
    p.name,
    p.sku,
    p.category_id,
    od.selling_price,
    od.quantity,
    (od.quantity * od.selling_price) as revenue,
    o.order_date,
    'MISSING' as status
FROM order_details od
LEFT JOIN products p ON od.product_id = p.id
LEFT JOIN orders o ON od.order_id = o.id
WHERE (od.buying_price IS NULL OR od.buying_price = 0)
  AND (p.buying_price IS NULL OR p.buying_price = 0)
ORDER BY o.order_date DESC
LIMIT 50;

-- Query 3: Suspicious Profit Margins (>30%)
-- Flag items with unusually high margins
SELECT
    p.id,
    p.name,
    p.sku,
    od.quantity,
    od.buying_price,
    od.selling_price,
    (od.selling_price - od.buying_price) as profit_per_unit,
    ((od.selling_price - od.buying_price) / od.buying_price * 100) as margin_percent,
    o.order_date,
    'SUSPICIOUS' as status
FROM order_details od
JOIN products p ON od.product_id = p.id
JOIN orders o ON od.order_id = o.id
WHERE od.buying_price > 0
  AND ROUND(((od.selling_price - od.buying_price) / od.buying_price * 100), 2) > 30
ORDER BY margin_percent DESC
LIMIT 100;

-- Query 4: Gross Profit Calculation
-- Current state of profit calculation
SELECT
    COALESCE(SUM(od.quantity * od.selling_price), 0) as total_revenue,
    COALESCE(SUM(CASE
        WHEN od.buying_price > 0 THEN od.quantity * od.buying_price
        ELSE 0
    END), 0) as total_cost,
    COALESCE(SUM(od.quantity * od.selling_price), 0) -
    COALESCE(SUM(CASE
        WHEN od.buying_price > 0 THEN od.quantity * od.buying_price
        ELSE 0
    END), 0) as gross_profit,
    ROUND(
        (COALESCE(SUM(od.quantity * od.selling_price), 0) -
        COALESCE(SUM(CASE
            WHEN od.buying_price > 0 THEN od.quantity * od.buying_price
            ELSE 0
        END), 0)) /
        COALESCE(SUM(od.quantity * od.selling_price), 1) * 100,
    2) as profit_margin_percent
FROM order_details od;

-- Query 5: Revenue Impact of Missing Buying Prices
-- Shows how much revenue is affected by missing data
SELECT
    'With Buying Price' as category,
    COUNT(DISTINCT product_id) as unique_products,
    COUNT(*) as order_items,
    COALESCE(SUM(quantity * selling_price), 0) as revenue
FROM order_details od
WHERE od.buying_price > 0
UNION ALL
SELECT
    'Missing Buying Price',
    COUNT(DISTINCT od.product_id),
    COUNT(*),
    COALESCE(SUM(od.quantity * od.selling_price), 0)
FROM order_details od
LEFT JOIN products p ON od.product_id = p.id
WHERE (od.buying_price IS NULL OR od.buying_price = 0)
  AND (p.buying_price IS NULL OR p.buying_price = 0);

-- Query 6: Backfill Missing Buying Prices (Safe Preview)
-- Shows what will be updated without making changes
SELECT
    od.id,
    od.product_id,
    p.name,
    p.buying_price,
    'TO UPDATE' as action
FROM order_details od
JOIN products p ON od.product_id = p.id
WHERE (od.buying_price IS NULL OR od.buying_price = 0)
  AND p.buying_price > 0
LIMIT 100;

-- Query 7: Profit by Date Range
-- Shows profit trend
SELECT
    DATE(o.order_date) as order_date,
    COUNT(DISTINCT o.id) as orders,
    COALESCE(SUM(od.quantity * od.selling_price), 0) as revenue,
    COALESCE(SUM(CASE
        WHEN od.buying_price > 0 THEN od.quantity * od.buying_price
        ELSE 0
    END), 0) as cost,
    COALESCE(SUM(od.quantity * od.selling_price), 0) -
    COALESCE(SUM(CASE
        WHEN od.buying_price > 0 THEN od.quantity * od.buying_price
        ELSE 0
    END), 0) as profit
FROM order_details od
JOIN orders o ON od.order_id = o.id
GROUP BY DATE(o.order_date)
ORDER BY order_date DESC
LIMIT 30;

-- =====================================================
-- FIX QUERIES (Use with caution - backup first!)
-- =====================================================

-- Fix 1: Backfill NULL or zero buying_price from products table
-- UNCOMMENT AND RUN TO EXECUTE
-- UPDATE order_details od
-- INNER JOIN products p ON od.product_id = p.id
-- SET od.buying_price = p.buying_price
-- WHERE (od.buying_price IS NULL OR od.buying_price = 0)
--   AND p.buying_price > 0;

-- Fix 2: Set default buying price for products missing it
-- UNCOMMENT AND RUN TO EXECUTE
-- UPDATE products
-- SET buying_price = selling_price * 0.70
-- WHERE buying_price IS NULL
--   AND selling_price > 0;

-- =====================================================
-- END OF VALIDATION QUERIES
-- =====================================================

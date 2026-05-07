# Quick Barcode Fix Test Script (PowerShell)
# Run this after applying the fixes

Write-Host "==================================" -ForegroundColor Cyan
Write-Host "Barcode Fix - Quick Test Script" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

# Navigate to project root (if not already there)
# cd C:\xampp\htdocs\NexoraLabs

# 1. Clear all caches
Write-Host "1. Clearing Laravel caches..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan view:clear
Write-Host "✓ Caches cleared" -ForegroundColor Green
Write-Host ""

# 2. Update barcode settings
Write-Host "2. Updating barcode settings in database..." -ForegroundColor Yellow
$tinkerCommand = @"
\App\Models\BarcodeSettings::where('barcode_type', 'EAN13')->update(['barcode_width' => 3, 'barcode_height' => 80]);
echo 'Updated barcode settings';
exit;
"@

$tinkerCommand | php artisan tinker
Write-Host "✓ Barcode settings updated" -ForegroundColor Green
Write-Host ""

# 3. Test barcode generation
Write-Host "3. Testing barcode generation..." -ForegroundColor Yellow
$testCommand = @"
`$product = \App\Models\Product::first();
if (`$product) {
    `$generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
    `$barcode = `$generator->getBarcode(`$product->barcode ?? `$product->code, `$generator::TYPE_EAN_13, 3, 80);
    echo 'Product: ' . `$product->name . PHP_EOL;
    echo 'Code: ' . `$product->code . PHP_EOL;
    echo 'Barcode: ' . `$product->barcode . PHP_EOL;
    echo 'Length: ' . strlen(`$product->barcode) . ' digits' . PHP_EOL;
    if (strlen(`$barcode) > 100) {
        echo 'SVG Generated: YES (length: ' . strlen(`$barcode) . ' chars)' . PHP_EOL;
        echo '✓ Barcode generation successful' . PHP_EOL;
    }
} else {
    echo '✗ No products found in database' . PHP_EOL;
}
exit;
"@

$testCommand | php artisan tinker
Write-Host ""

# 4. Check recent logs
Write-Host "4. Checking recent barcode logs..." -ForegroundColor Yellow
if (Test-Path "storage\logs\laravel.log") {
    Write-Host "Recent barcode-related entries:" -ForegroundColor Gray
    Get-Content "storage\logs\laravel.log" | Select-String -Pattern "barcode" -CaseSensitive:$false | Select-Object -Last 5
} else {
    Write-Host "No log file found" -ForegroundColor Red
}
Write-Host ""

# 5. Verify database
Write-Host "5. Verifying barcode data in database..." -ForegroundColor Yellow
$verifyCommand = @"
`$count = \App\Models\Product::whereNotNull('barcode')->count();
`$total = \App\Models\Product::count();
echo "Products with barcodes: `$count / `$total" . PHP_EOL;

`$sample = \App\Models\Product::whereNotNull('barcode')->first();
if (`$sample) {
    echo "Sample barcode: " . `$sample->barcode . " (length: " . strlen(`$sample->barcode) . ")" . PHP_EOL;
    if (strlen(`$sample->barcode) == 12) {
        echo "✓ Barcode format correct (12 digits)" . PHP_EOL;
    } else {
        echo "⚠ Warning: Barcode should be 12 digits" . PHP_EOL;
    }
}
exit;
"@

$verifyCommand | php artisan tinker
Write-Host ""

# 6. Summary
Write-Host "==================================" -ForegroundColor Cyan
Write-Host "TESTING INSTRUCTIONS" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "NEXT STEPS TO TEST:" -ForegroundColor Yellow
Write-Host "1. Open browser and visit:" -ForegroundColor White
Write-Host "   http://localhost/barcode/test-print" -ForegroundColor Cyan
Write-Host ""
Write-Host "2. Click 'Print Now' button" -ForegroundColor White
Write-Host ""
Write-Host "3. In print dialog:" -ForegroundColor White
Write-Host "   - Enable 'Print Background Graphics'" -ForegroundColor Gray
Write-Host "   - Set scale to 100%" -ForegroundColor Gray
Write-Host "   - Use 'Best Quality' settings" -ForegroundColor Gray
Write-Host ""
Write-Host "4. Print to physical printer (not PDF)" -ForegroundColor White
Write-Host ""
Write-Host "5. Test printed barcode with scanner" -ForegroundColor White
Write-Host ""
Write-Host "==================================" -ForegroundColor Cyan
Write-Host "TROUBLESHOOTING" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "If barcode still doesn't scan:" -ForegroundColor Yellow
Write-Host ""
Write-Host "✓ Check scanner supports EAN-13" -ForegroundColor White
Write-Host "✓ Try scanning at different angles" -ForegroundColor White
Write-Host "✓ Ensure printer resolution is 300+ DPI" -ForegroundColor White
Write-Host "✓ Use matte label stock (not glossy)" -ForegroundColor White
Write-Host "✓ Verify barcode column is VARCHAR in database" -ForegroundColor White
Write-Host ""
Write-Host "For detailed help, see:" -ForegroundColor Yellow
Write-Host "  BARCODE_FIX_GUIDE.md" -ForegroundColor Cyan
Write-Host ""
Write-Host "To check database directly, run:" -ForegroundColor Yellow
Write-Host "  database\queries\verify_barcode_data.sql" -ForegroundColor Cyan
Write-Host ""
Write-Host "==================================" -ForegroundColor Cyan

Write-Host ""
Write-Host "Press any key to open test page in browser..." -ForegroundColor Yellow
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

# Open in default browser
Start-Process "http://localhost/barcode/test-print"

#!/bin/bash

# Job Migration Deployment Script
# This script safely deploys the job/job_type migration changes to production

set -e  # Exit on any error

echo "=========================================="
echo "Job Migration Deployment Script"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${YELLOW}ℹ $1${NC}"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "Error: artisan file not found. Please run this script from the Laravel root directory."
    exit 1
fi

print_success "Found Laravel project"

# Step 1: Git pull
echo ""
echo "Step 1: Pulling latest code from repository..."
if git pull origin main; then
    print_success "Code updated successfully"
else
    print_error "Failed to pull code. Please check your git configuration."
    exit 1
fi

# Step 2: Install/Update dependencies (optional, comment out if not needed)
# echo ""
# echo "Step 2: Updating Composer dependencies..."
# if composer install --no-dev --optimize-autoloader; then
#     print_success "Dependencies updated"
# else
#     print_warning "Composer update had issues, but continuing..."
# fi

# Step 3: Check migration status before
echo ""
echo "Step 3: Checking current migration status..."
php artisan migrate:status

# Step 4: Run migrations
echo ""
echo "Step 4: Running database migrations..."
print_warning "This will migrate job_types from user_id to shop_id structure"
read -p "Continue with migrations? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    if php artisan migrate --force; then
        print_success "Migrations completed successfully!"
    else
        print_error "Migration failed! Check the error above."
        exit 1
    fi
else
    print_warning "Migration skipped by user"
    exit 0
fi

# Step 5: Clear all caches
echo ""
echo "Step 5: Clearing all caches..."

php artisan cache:clear
print_success "Application cache cleared"

php artisan config:clear
print_success "Configuration cache cleared"

php artisan view:clear
print_success "View cache cleared"

php artisan route:clear
print_success "Route cache cleared"

# Step 6: Optimize for production
echo ""
echo "Step 6: Optimizing for production..."

php artisan config:cache
print_success "Configuration cached"

php artisan route:cache
print_success "Routes cached"

php artisan view:cache
print_success "Views cached"

# Step 7: Verify database structure
echo ""
echo "Step 7: Verifying database structure..."
echo "Checking job_types table..."

# Run a simple query to check if the migration worked
if php artisan tinker --execute="echo \App\Models\JobType::count() . ' job types found';" 2>/dev/null; then
    print_success "JobType model working correctly!"
else
    print_warning "Could not verify JobType model, but this might be normal"
fi

# Step 8: Check migration status after
echo ""
echo "Step 8: Final migration status:"
php artisan migrate:status

# Step 9: Restart services (optional - uncomment what you need)
echo ""
echo "Step 9: Restarting services..."
print_warning "You may need to manually restart PHP-FPM and web server"
echo ""
echo "Run one of these commands depending on your setup:"
echo "  sudo systemctl restart php8.2-fpm"
echo "  sudo systemctl reload nginx"
echo "  sudo systemctl restart apache2"
echo ""

read -p "Would you like to restart PHP-FPM now? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    if sudo systemctl restart php8.2-fpm 2>/dev/null; then
        print_success "PHP-FPM restarted"
    elif sudo systemctl restart php8.1-fpm 2>/dev/null; then
        print_success "PHP-FPM restarted"
    elif sudo systemctl restart php-fpm 2>/dev/null; then
        print_success "PHP-FPM restarted"
    else
        print_warning "Could not restart PHP-FPM automatically. Please restart manually."
    fi
fi

# Final summary
echo ""
echo "=========================================="
echo "Deployment Complete!"
echo "=========================================="
echo ""
print_success "✓ Code pulled from repository"
print_success "✓ Migrations executed"
print_success "✓ Caches cleared"
print_success "✓ Application optimized"
echo ""
print_info "Next steps:"
echo "  1. Test the jobs page in your browser"
echo "  2. Try creating a new job type"
echo "  3. Try creating a new job"
echo "  4. Verify other users in same shop can see jobs"
echo ""
print_warning "If you see errors, check: storage/logs/laravel.log"
echo ""

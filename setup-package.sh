#!/bin/bash

# Setup script for converting boilerplate to Composer package

echo "=========================================="
echo "Laravel Vue Auth Starter Package Setup"
echo "=========================================="

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    echo "Error: composer.json not found. Please run this script from your boilerplate root directory."
    exit 1
fi

echo "1. Preparing package structure..."
php scripts/prepare-package.php

echo "2. Setting up package files..."

# Make the installation script executable
chmod +x bin/laravel-vue-auth-starter-install

echo "3. Package structure created successfully!"
echo ""
echo "Next steps:"
echo "1. Update package-composer.json with your vendor name and details"
echo "2. Update src/LaravelVueAuthStarterServiceProvider.php with your namespace"
echo "3. Test the package locally:"
echo "   - composer require ./package-composer.json"
echo "   - php artisan laravel-vue-auth-starter:install"
echo "4. Push to GitHub and submit to Packagist"
echo ""
echo "Files created:"
echo "- package-composer.json (package configuration)"
echo "- src/LaravelVueAuthStarterServiceProvider.php (service provider)"
echo "- src/Console/InstallLaravelVueAuthStarterCommand.php (installation command)"
echo "- bin/laravel-vue-auth-starter-install (installation script)"
echo "- stubs/ (boilerplate files)"
echo "- PACKAGE_README.md (package documentation)"
echo "- COMPOSER_PACKAGE_GUIDE.md (comprehensive guide)"
echo ""
echo "Happy packaging! 🚀"

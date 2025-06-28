# Client Update Guide - Laravel Vue Auth Starter

This guide explains how to safely update your Laravel Vue Auth Starter package without losing your customizations.

## **The Challenge**

When you update the package, you want to get new features and bug fixes, but you don't want to lose your custom changes to controllers, models, routes, etc.

## **Solution: Selective Updates**

The package provides different tags for different types of files, allowing you to update only what you want.

## **Available Update Tags**

### **1. Database Files (Safe to Update)**
```bash
# Update migrations and seeders (usually safe)
php artisan vendor:publish --tag=laravel-vue-auth-starter-database --force
php artisan migrate
```

### **2. Public Assets (Safe to Update)**
```bash
# Update public assets (CSS, JS, images)
php artisan vendor:publish --tag=laravel-vue-auth-starter-assets --force
```

### **3. Configuration Files (Review Before Updating)**
```bash
# Update config files (review changes first)
php artisan vendor:publish --tag=laravel-vue-auth-starter-config --force
```

### **4. Application Files (Most Risky)**
```bash
# Update controllers, models, middleware (review carefully)
php artisan vendor:publish --tag=laravel-vue-auth-starter-app --force
```

### **5. Routes (Often Customized)**
```bash
# Update route files (review carefully)
php artisan vendor:publish --tag=laravel-vue-auth-starter-routes --force
```

### **6. Frontend Resources (Often Customized)**
```bash
# Update Vue components, CSS, JS (review carefully)
php artisan vendor:publish --tag=laravel-vue-auth-starter-frontend --force
```

## **Recommended Update Workflow**

### **Step 1: Check What's New**
```bash
# Update the package
composer update joel-phaka/laravel-vue-auth-starter

# Check what files would be published
php artisan vendor:publish --tag=laravel-vue-auth-starter --list
```

### **Step 2: Backup Your Changes**
```bash
# Create a backup of your customizations
git add .
git commit -m "Backup before package update"
```

### **Step 3: Update Safely**
```bash
# Always safe to update
php artisan vendor:publish --tag=laravel-vue-auth-starter-database --force
php artisan vendor:publish --tag=laravel-vue-auth-starter-assets --force

# Review and update configs if needed
php artisan vendor:publish --tag=laravel-vue-auth-starter-config --force

# Run migrations
php artisan migrate
```

### **Step 4: Review Application Files**
```bash
# Check what would change in your app files
php artisan vendor:publish --tag=laravel-vue-auth-starter-app --list

# If you want to update specific files, copy them manually
# or use git to merge changes
```

## **Advanced Strategies**

### **1. Git-Based Updates**
```bash
# Instead of using --force, use git to merge changes
git stash
php artisan vendor:publish --tag=laravel-vue-auth-starter-app
git stash pop
# Resolve conflicts manually
```

### **2. File-by-File Updates**
```bash
# Update only specific files you know are safe
cp vendor/joel-phaka/laravel-vue-auth-starter/stubs/app/Http/Controllers/Api/Auth/AuthController.php app/Http/Controllers/Api/Auth/
```

### **3. Extending Instead of Overriding**
```php
// Instead of modifying the original controller, extend it
class CustomAuthController extends \JoelPhaka\LaravelVueAuthStarter\Http\Controllers\Api\Auth\AuthController
{
    // Add your custom methods here
}
```

## **What Files Are Usually Safe to Update?**

### **✅ Safe (Rarely Customized)**
- Database migrations
- Public assets (CSS, JS, images)
- Package configuration files
- Seeders

### **⚠️ Review Before Updating**
- Configuration files (`config/auth.php`, etc.)
- Service providers
- Middleware

### **❌ Risky (Often Customized)**
- Controllers
- Models
- Routes
- Vue components
- Custom CSS/JS

## **Best Practices**

### **1. Use Version Control**
```bash
# Always commit before updating
git add .
git commit -m "Before package update"
```

### **2. Test After Updates**
```bash
# Run your tests after any update
php artisan test
npm run test
```

### **3. Keep a Changelog**
Document what you've customized so you know what to watch out for during updates.

### **4. Use Feature Flags**
```php
// In your config
'features' => [
    'new_auth_flow' => env('USE_NEW_AUTH_FLOW', false),
]
```

## **Emergency Recovery**

If an update breaks something:

```bash
# Revert the package update
composer update joel-phaka/laravel-vue-auth-starter --prefer-dist

# Restore your files from git
git checkout HEAD -- app/Http/Controllers/
git checkout HEAD -- app/Models/
# etc.
```

## **Getting Help**

If you're unsure about an update:

1. Check the package changelog
2. Review the diff between versions
3. Test in a staging environment first
4. Contact the package maintainer

## **Example Update Session**

```bash
# 1. Update package
composer update joel-phaka/laravel-vue-auth-starter

# 2. Backup
git add . && git commit -m "Backup before update"

# 3. Safe updates
php artisan vendor:publish --tag=laravel-vue-auth-starter-database --force
php artisan vendor:publish --tag=laravel-vue-auth-starter-assets --force
php artisan migrate

# 4. Review config changes
php artisan vendor:publish --tag=laravel-vue-auth-starter-config --force

# 5. Test
php artisan test
npm run build

# 6. Commit successful update
git add . && git commit -m "Successfully updated to v1.1.0"
```

This approach ensures you get the benefits of updates while protecting your customizations! 

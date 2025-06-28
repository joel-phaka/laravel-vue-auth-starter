# Composer Package Boilerplate Management Guide

This guide explains how to convert your Laravel boilerplate into a Composer package and manage updates across client projects.

## Overview

Converting your boilerplate to a Composer package provides several advantages:

- **Easy Updates**: Client projects can update with `composer update`
- **Version Control**: Semantic versioning for releases
- **Dependency Management**: Automatic dependency resolution
- **Professional Distribution**: Publish to Packagist for public use
- **Private Repositories**: Use private Composer repositories for internal use

## Step 1: Prepare Your Boilerplate

### 1.1 Create Package Structure

Your boilerplate should be restructured as follows:

```
laravel-sanctum-passport-socialite-vue-boilerplate/
├── src/
│   ├── BoilerplateServiceProvider.php
│   └── Console/
│       └── InstallBoilerplateCommand.php
├── stubs/
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── routes/
│   ├── resources/
│   └── public/
├── bin/
│   └── boilerplate-install
├── package-composer.json
├── PACKAGE_README.md
└── scripts/
    └── prepare-package.php
```

### 1.2 Run the Preparation Script

```bash
php scripts/prepare-package.php
```

This will copy your current boilerplate files to the `stubs/` directory.

## Step 2: Publish to Packagist

### 2.1 Create GitHub Repository

1. Create a new repository on GitHub
2. Push your boilerplate code
3. Tag releases for versioning

### 2.2 Submit to Packagist

1. Go to [Packagist.org](https://packagist.org)
2. Submit your GitHub repository URL
3. Packagist will automatically sync with your GitHub releases

### 2.3 Alternative: Private Repository

For private use, you can use:

- **GitHub Packages**: `composer config repositories.boilerplate composer https://github.com/your-org/boilerplate`
- **Private Packagist**: Self-hosted Composer repository
- **Satis**: Build your own Composer repository

## Step 3: Client Project Setup

### 3.1 Install the Package

```bash
# In your client project
composer require your-vendor/laravel-sanctum-passport-socialite-vue-boilerplate
```

### 3.2 Run Installation

```bash
php artisan boilerplate:install
```

This will:
- Publish all boilerplate files
- Run migrations
- Install Passport
- Set up npm dependencies
- Build assets

## Step 4: Managing Updates

### 4.1 For Boilerplate Maintainers

When you update the boilerplate:

1. **Make Changes**: Update your boilerplate code
2. **Update Version**: Bump version in `package-composer.json`
3. **Tag Release**: Create a new Git tag
4. **Push to GitHub**: Packagist will auto-sync

```bash
# Update version
git add .
git commit -m "Add new feature X"
git tag v1.1.0
git push origin main --tags
```

### 4.2 For Client Projects

When you want to update a client project:

```bash
# Update the package
composer update your-vendor/laravel-sanctum-passport-socialite-vue-boilerplate

# Re-publish updated files (use --force to overwrite existing files)
php artisan vendor:publish --tag=boilerplate --force
php artisan vendor:publish --tag=boilerplate-config --force

# Run any new migrations
php artisan migrate

# Update npm dependencies if needed
npm install
npm run build
```

## Step 5: Customization Strategy

### 5.1 Extending vs Overriding

**Extending (Recommended)**:
```php
// app/Models/User.php
class User extends \YourVendor\LaravelSanctumPassportSocialiteVueBoilerplate\Models\User
{
    // Add custom methods
    public function customMethod()
    {
        // Your logic
    }
}
```

**Overriding**:
```php
// app/Http/Controllers/Api/Auth/AuthController.php
class AuthController extends \YourVendor\LaravelSanctumPassportSocialiteVueBoilerplate\Http\Controllers\Api\Auth\AuthController
{
    // Override methods
    public function login(LoginRequest $request)
    {
        // Custom login logic
    }
}
```

### 5.2 Configuration Files

Keep client-specific configurations separate:

```php
// config/boilerplate.php
return [
    'features' => [
        'social_login' => env('BOILERPLATE_SOCIAL_LOGIN', true),
        'api_documentation' => env('BOILERPLATE_API_DOCS', false),
    ],
    'customizations' => [
        // Client-specific settings
    ],
];
```

## Step 6: Advanced Workflows

### 6.1 Automated Updates with GitHub Actions

Create a workflow that automatically updates client repositories:

```yaml
# .github/workflows/sync-clients.yml
name: Sync to Client Repositories
on:
  release:
    types: [published]

jobs:
  sync:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Update Client Repositories
        run: |
          # Script to update all client repos
          # This would use GitHub API to create PRs
```

### 6.2 Selective Updates

Allow clients to choose which updates to apply:

```bash
# Update only specific components
php artisan vendor:publish --tag=boilerplate-auth --force
php artisan vendor:publish --tag=boilerplate-api --force
```

## Step 7: Best Practices

### 7.1 Version Management

- Use semantic versioning (MAJOR.MINOR.PATCH)
- Maintain a changelog
- Test updates thoroughly before releasing

### 7.2 Documentation

- Keep comprehensive documentation
- Provide migration guides for major versions
- Include examples and tutorials

### 7.3 Testing

- Test the package installation process
- Test updates across different Laravel versions
- Maintain backward compatibility

### 7.4 Support

- Provide clear support channels
- Respond to issues promptly
- Create FAQ sections

## Troubleshooting

### Common Issues

1. **File Conflicts**: Use `--force` flag when publishing
2. **Migration Errors**: Check for conflicting migrations
3. **Asset Build Issues**: Ensure npm dependencies are compatible

### Debug Commands

```bash
# Check package installation
composer show your-vendor/laravel-sanctum-passport-socialite-vue-boilerplate

# List published files
php artisan vendor:publish --list

# Clear caches
php artisan config:clear
php artisan cache:clear
```

## Conclusion

The Composer package approach provides a professional, maintainable solution for boilerplate management. It allows for easy updates while maintaining flexibility for customization.

Key benefits:
- ✅ Easy distribution and installation
- ✅ Version control and dependency management
- ✅ Professional package ecosystem
- ✅ Automated update workflows
- ✅ Clear separation of concerns

This approach scales well as your boilerplate evolves and your client base grows. 

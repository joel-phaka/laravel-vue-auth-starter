# Deployment Guide

This guide will help you deploy your Laravel Vue Auth Starter package to GitHub and Packagist.

## Prerequisites

1. **GitHub Account**: You need a GitHub account to host your repository
2. **Packagist Account**: You need a Packagist account to publish your package
3. **Composer**: Make sure you have Composer installed
4. **Git**: Make sure you have Git installed and configured

## Step 1: Prepare Your Repository

### 1.1 Initialize Git Repository (if not already done)

```bash
git init
git add .
git commit -m "Initial commit"
```

### 1.2 Create GitHub Repository

1. Go to [GitHub](https://github.com) and sign in
2. Click the "+" icon in the top right corner
3. Select "New repository"
4. Name your repository: `laravel-vue-auth-starter`
5. Make it public
6. Don't initialize with README, .gitignore, or license (we already have these)
7. Click "Create repository"

### 1.3 Push to GitHub

```bash
git remote add origin https://github.com/joel-phaka/laravel-vue-auth-starter.git
git branch -M main
git push -u origin main
```

## Step 2: Set Up GitHub Repository

### 2.1 Repository Settings

1. Go to your repository on GitHub
2. Click on "Settings" tab
3. Under "General", make sure:
   - Repository name is correct
   - Description is set
   - Website URL points to your repository
   - Topics include: `laravel`, `vue`, `authentication`, `boilerplate`, `starter`

### 2.2 Enable GitHub Actions

The GitHub Actions workflow is already configured in `.github/workflows/tests.yml`. It will automatically run tests on:
- Push to main branch
- Pull requests to main branch

### 2.3 Create First Release

1. Go to "Releases" in your repository
2. Click "Create a new release"
3. Tag version: `v1.0.0`
4. Release title: `v1.0.0 - Initial Release`
5. Description: Use the content from your CHANGELOG.md
6. Click "Publish release"

## Step 3: Submit to Packagist

### 3.1 Create Packagist Account

1. Go to [Packagist](https://packagist.org)
2. Click "Log in" and authenticate with GitHub
3. Complete your profile

### 3.2 Submit Package

1. Click "Submit Package" on Packagist
2. Enter your GitHub repository URL: `https://github.com/joel-phaka/laravel-vue-auth-starter`
3. Packagist will automatically detect your `package-composer.json`
4. Review the package information
5. Click "Submit"

### 3.3 Configure Auto-Update (Optional)

1. Go to your package page on Packagist
2. Click "Settings"
3. Enable "Auto-update" and connect your GitHub repository
4. This will automatically update Packagist when you push new tags

## Step 4: Version Management

### 4.1 Using the Release Script

We've created a release script to help with versioning:

```bash
# Make the script executable (if not already done)
chmod +x scripts/release.sh

# Create a new release
./scripts/release.sh 1.0.1
```

The script will:
- Update version in `package-composer.json`
- Update `CHANGELOG.md`
- Commit changes
- Create and push a new tag
- Push to GitHub

### 4.2 Manual Release Process

If you prefer to do it manually:

1. Update version in `package-composer.json`
2. Update `CHANGELOG.md`
3. Commit changes:
   ```bash
   git add package-composer.json CHANGELOG.md
   git commit -m "Release version 1.0.1"
   ```
4. Create and push tag:
   ```bash
   git tag -a v1.0.1 -m "Release version 1.0.1"
   git push origin main
   git push origin v1.0.1
   ```

## Step 5: Package Testing

### 5.1 Test Installation

Before releasing, test your package installation:

```bash
# Create a test Laravel project
composer create-project laravel/laravel test-project
cd test-project

# Install your package
composer require joel-phaka/laravel-vue-auth-starter

# Run the installation command
php artisan laravel-vue-auth-starter:install

# Test that everything works
php artisan migrate
php artisan serve
```

### 5.2 Test Publishing

Test that your package files are published correctly:

```bash
# Test publishing all files
php artisan vendor:publish --tag=laravel-vue-auth-starter

# Test publishing specific groups
php artisan vendor:publish --tag=laravel-vue-auth-starter-app
php artisan vendor:publish --tag=laravel-vue-auth-starter-config
```

## Step 6: Documentation

### 6.1 Update README

Make sure your README.md includes:
- Clear installation instructions
- Feature list
- Configuration examples
- Usage examples
- Contributing guidelines

### 6.2 Create Wiki (Optional)

Consider creating a GitHub Wiki with:
- Detailed installation guide
- Configuration options
- Troubleshooting
- FAQ

## Step 7: Maintenance

### 7.1 Regular Updates

- Keep dependencies updated
- Monitor for security vulnerabilities
- Respond to issues and pull requests
- Update documentation as needed

### 7.2 Version Strategy

Follow semantic versioning:
- **Major** (1.0.0): Breaking changes
- **Minor** (1.1.0): New features, backward compatible
- **Patch** (1.0.1): Bug fixes, backward compatible

## Troubleshooting

### Common Issues

1. **Package not found on Packagist**
   - Make sure your repository is public
   - Check that `package-composer.json` is valid
   - Verify the package name matches your GitHub username

2. **GitHub Actions failing**
   - Check the workflow file syntax
   - Ensure all dependencies are properly specified
   - Test locally first

3. **Installation issues**
   - Test in a clean Laravel installation
   - Check for conflicts with existing files
   - Verify all required dependencies

### Getting Help

- Check existing issues on GitHub
- Create a new issue with detailed information
- Include your environment details (PHP version, Laravel version, etc.)

## Next Steps

After successful deployment:

1. **Promote your package**:
   - Share on social media
   - Write blog posts
   - Present at meetups or conferences

2. **Monitor usage**:
   - Track downloads on Packagist
   - Monitor GitHub stars and forks
   - Respond to user feedback

3. **Continuous improvement**:
   - Add new features based on user requests
   - Improve documentation
   - Optimize performance

## Resources

- [Composer Documentation](https://getcomposer.org/doc/)
- [Packagist Documentation](https://packagist.org/about)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Semantic Versioning](https://semver.org/) 

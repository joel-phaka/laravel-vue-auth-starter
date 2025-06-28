# Laravel Vue Auth Starter Package

A Composer package that provides a complete Laravel boilerplate with Sanctum, Passport, Socialite, and Vue.js integration.

## Installation

### Method 1: Via Composer (Recommended)

```bash
composer require joel-phaka/laravel-vue-auth-starter
```

Then run the installation command:

```bash
php artisan laravel-vue-auth-starter:install
```

### Method 2: Manual Installation

If you prefer to install manually:

```bash
# Publish all boilerplate files
php artisan vendor:publish --tag=laravel-vue-auth-starter

# Install Passport (creates OAuth tables with proper keys)
php artisan passport:install

# Run migrations (Passport tables will be skipped automatically)
php artisan migrate

# Install npm dependencies
npm install

# Build assets
npm run build
```

## Installation Order & Process

The installation follows a specific order to avoid conflicts:

### 1. **Passport Installation** (`php artisan passport:install`)
- Creates OAuth tables with proper encryption keys
- Generates client secrets and keys
- Sets up the OAuth server

### 2. **Database Migrations** (`php artisan migrate`)
- Creates your custom tables (users, roles, settings, etc.)
- **Automatically skips Passport tables** (already exist)
- Laravel handles this gracefully

### 3. **Asset Building**
- Installs npm dependencies
- Builds Vue.js assets

## Features

- **Laravel Sanctum**: API authentication
- **Laravel Passport**: OAuth2 server implementation
- **Laravel Socialite**: Social media authentication
- **Vue.js 3**: Frontend framework with modern tooling
- **Authentication System**: Complete login/register system
- **Role-based Access Control**: User roles and permissions
- **API Endpoints**: RESTful API with authentication
- **Frontend Components**: Pre-built Vue components
- **Database Migrations**: Complete database structure
- **Seeders**: Sample data for testing

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Social Login (Optional)
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

FACEBOOK_CLIENT_ID=your_facebook_client_id
FACEBOOK_CLIENT_SECRET=your_facebook_client_secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback

# Passport
PASSPORT_PRIVATE_KEY="your_private_key"
PASSPORT_PUBLIC_KEY="your_public_key"
```

### Social Login Setup

1. Configure your social providers in `config/services.php`
2. Add the necessary environment variables
3. Set up OAuth applications in your social provider dashboards

## Usage

### API Authentication

```php
// Login
POST /api/auth/login
{
    "email": "user@example.com",
    "password": "password"
}

// Register
POST /api/auth/register
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
}

// Get user profile
GET /api/user
Authorization: Bearer {token}
```

### Social Login

```php
// Redirect to social provider
GET /auth/{provider}/redirect

// Handle callback
GET /auth/{provider}/callback
```

## Updating the Boilerplate

When you update the boilerplate package, client projects can easily sync the changes:

```bash
# Update the package
composer update joel-phaka/laravel-vue-auth-starter

# Re-publish updated files (use --force to overwrite existing files)
php artisan vendor:publish --tag=laravel-vue-auth-starter --force

# Run any new migrations
php artisan migrate
```

## Customization

### Extending Models

```php
// app/Models/User.php
class User extends \JoelPhaka\LaravelVueAuthStarter\Models\User
{
    // Add your custom methods and relationships
}
```

### Custom Controllers

```php
// app/Http/Controllers/Api/Auth/AuthController.php
class AuthController extends \JoelPhaka\LaravelVueAuthStarter\Http\Controllers\Api\Auth\AuthController
{
    // Override methods or add new ones
}
```

## File Structure

```
stubs/
├── app/                    # Application logic
│   ├── Enums/             # Enum classes
│   ├── Events/            # Event classes
│   ├── Exceptions/        # Custom exceptions
│   ├── Helpers/           # Helper functions
│   ├── Http/              # Controllers, Middleware, Requests
│   ├── Listeners/         # Event listeners
│   ├── Models/            # Eloquent models
│   └── Providers/         # Service providers
├── config/                # Configuration files
├── database/              # Migrations and seeders
├── routes/                # Route definitions
├── resources/             # Frontend resources
└── public/                # Public assets
```

## Troubleshooting

### "Table already exists" errors
If you see errors about Passport tables already existing, this is normal. 
The `passport:install` command creates these tables, and subsequent migrations skip them.

### Manual Passport setup
If you prefer to set up Passport manually:
1. Run `php artisan passport:install` first
2. Then run `php artisan migrate`

### Installation issues
- Test in a clean Laravel installation
- Check for conflicts with existing files
- Verify all required dependencies

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Support

For support, please open an issue on GitHub or contact the maintainers.

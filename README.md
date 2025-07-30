# Laravel Vue Authentication Starter

A production-ready Laravel 12 + Vue 3 authentication starter kit with dual authentication (Sanctum + Passport), social login, role-based access control, and comprehensive security features.

## 🚀 Features

### Authentication & Security
- **Dual Authentication**: Laravel Sanctum (web) + Laravel Passport (API)
- **Social Login**: Google, Facebook integration via Laravel Socialite
- **Role-Based Access Control**: Super Admin, Admin, User hierarchy
- **User Status Management**: Active, Inactive, Suspended, Banned
- **Email Verification**: Required for new accounts
- **Password Reset**: Complete flow with email notifications
- **reCAPTCHA Integration**: v2 checkbox protection
- **Login Analytics**: IP geolocation, device detection, activity logging
- **Rate Limiting**: Built-in protection against brute force attacks

### Frontend
- **Vue 3** with Composition API
- **PrimeVue 4** UI component library
- **Pinia** state management with persistence
- **Vee-Validate + Yup** form validation
- **Tailwind CSS 4** styling
- **Dark/Light Theme** toggle
- **Responsive Design** with mobile-first approach

### Backend
- **Laravel 12** with PHP 8.2+
- **Dynamic Settings System** with caching
- **Comprehensive API** with RESTful endpoints
- **Database Migrations** and seeders
- **Event-Driven Architecture** for extensibility
- **Helper Functions** for common operations

## 📋 Requirements

- PHP 8.2 or higher
- Composer 2.0 or higher
- Node.js 18 or higher
- MySQL 8.0+ or PostgreSQL 13+
- Redis (optional, for caching)

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone https://github.com/joel-phaka/laravel-vue-auth-starter.git
cd laravel-vue-auth-starter
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Environment Variables
Edit `.env` file with your configuration:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_vue_auth
DB_USERNAME=root
DB_PASSWORD=

# Mail (for email verification)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# reCAPTCHA
RECAPTCHA_KEY=your_recaptcha_site_key
RECAPTCHA_SECRET=your_recaptcha_secret_key
RECAPTCHA_VERIFY_URL=https://www.google.com/recaptcha/api/siteverify

# Social Login (optional)
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/signin/google/callback

FACEBOOK_CLIENT_ID=your_facebook_client_id
FACEBOOK_CLIENT_SECRET=your_facebook_client_secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/signin/facebook/callback

# Frontend
VITE_APP_APP_NAME="Laravel Vue Auth"
VITE_APP_RECAPTCHA_KEY=your_recaptcha_site_key
```

### 5. Database Setup
```bash
# Run migrations
php artisan migrate

# Seed database with default data
php artisan db:seed
```

### 6. Passport Setup
```bash
# Install Passport
php artisan passport:install
```

### 7. Build Frontend Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 8. Start Development Server
```bash
# Using Laravel's built-in server
php artisan serve

# Or use the convenience script (starts all services)
composer run dev
```

## 🏃‍♂️ Quick Start

1. **Access the Application**: Visit `http://localhost:8000`
2. **Register**: Create a new account at `/signup`
3. **Verify Email**: Check your email and verify your account
4. **Sign In**: Login at `/signin`
5. **Explore**: Navigate through the authenticated areas

## 📚 API Documentation

### Authentication Endpoints

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password",
    "remember_me": true,
    "recaptcha_token": "token"
}
```

#### Register
```http
POST /api/auth/register
Content-Type: application/json

{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password",
    "accept_terms": true,
    "recaptcha_token": "token"
}
```

#### Get Access Token (OAuth2)
```http
POST /api/auth/token
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

#### Refresh Token
```http
POST /api/auth/token/refresh
Content-Type: application/json

{
    "refresh_token": "token"
}
```

#### Get User Profile
```http
GET /api/auth/user
Authorization: Bearer {access_token}
```

### Password Reset
```http
POST /api/auth/password/email
Content-Type: application/json

{
    "email": "user@example.com"
}
```

```http
POST /api/auth/password/reset
Content-Type: application/json

{
    "token": "reset_token",
    "email": "user@example.com",
    "password": "new_password",
    "password_confirmation": "new_password",
    "recaptcha_token": "token"
}
```

## 🔧 Configuration

### Settings System
The application includes a dynamic settings system:

```php
// Get a setting
$appName = get_setting_value('app_name', 'Default App Name');

// Set a setting
set_setting('maintenance_mode', true, 'system');

// Clear settings cache
clear_settings_cache();
```

### Role-Based Access Control
```php
// Check user role in middleware
Route::middleware(['auth.dynamic', 'role:admin'])->group(function () {
    // Admin only routes
});

// Check role in controllers
if (Auth::user()->role->name === 'super_admin') {
    // Super admin logic
}
```

### Custom Middleware
- `auth.dynamic` - Switches between Sanctum/Passport based on request
- `role:{role_name}` - Role-based authorization
- `verify.recaptcha` - reCAPTCHA validation
- `verify.active.user` - User status verification

## 🎨 Frontend Development

### Available Scripts
```bash
# Development with hot reload
npm run dev

# Build for production
npm run build

# Development with host access
npm run host
```

### State Management
```javascript
// Auth store
import { useAuthStore } from '@/stores/auth.store'
const authStore = useAuthStore()

// Theme store
import { useThemeStore } from '@/stores/theme.store'
const themeStore = useThemeStore()
```

### Form Validation
```javascript
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/yup'
import * as yup from 'yup'

const schema = yup.object({
    email: yup.string().email().required(),
    password: yup.string().min(8).required()
})

const { handleSubmit, errors } = useForm({
    validationSchema: toTypedSchema(schema)
})
```

## 🧪 Testing

```bash
# Run PHP tests
composer test

# Run with coverage
composer test -- --coverage
```

## 🚀 Deployment

### Production Build
```bash
# Install dependencies
composer install --optimize-autoloader --no-dev
npm ci
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set up Passport
php artisan passport:keys
```

### Environment Variables for Production
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_smtp_user
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
```

## 📁 Project Structure

```
laravel-vue-auth-starter/
├── app/
│   ├── Enums/              # PHP enums for constants
│   ├── Events/             # Event classes
│   ├── Http/
│   │   ├── Controllers/    # API and web controllers
│   │   ├── Middleware/     # Custom middleware
│   │   └── Requests/       # Form request validation
│   ├── Listeners/          # Event listeners
│   ├── Models/             # Eloquent models
│   └── Support/            # Helper classes and functions
├── database/
│   ├── migrations/         # Database migrations
│   └── seeders/           # Database seeders
├── resources/
│   └── js/
│       ├── components/     # Vue components
│       ├── composables/    # Vue composables
│       ├── config/         # Configuration files
│       ├── router/         # Vue router setup
│       ├── services/       # API services
│       ├── stores/         # Pinia stores
│       └── views/          # Vue page components
└── routes/
    ├── api.php            # API routes
    └── web.php            # Web routes
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Issues**: [GitHub Issues](https://github.com/joel-phaka/laravel-vue-auth-starter/issues)
- **Documentation**: [Wiki](https://github.com/joel-phaka/laravel-vue-auth-starter/wiki)
- **Email**: joel.phaka@hotmail.com

## 🙏 Acknowledgments

- [Laravel](https://laravel.com/) - The PHP framework
- [Vue.js](https://vuejs.org/) - The progressive JavaScript framework
- [PrimeVue](https://primevue.org/) - UI component library
- [Tailwind CSS](https://tailwindcss.com/) - Utility-first CSS framework

---

**Built with ❤️ by [Joël Phaka](https://github.com/joel-phaka)**

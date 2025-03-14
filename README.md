# Cerberus IAM | API

## Overview

Cerberus is a robust Identity and Access Management (IAM) platform API built with Laravel and Laravel Passport. It provides enterprise-grade user identity management, authentication, and authorization capabilities through a RESTful API interface.

## Features

### Core Functionality

- **User Management**
  - Registration with email verification
  - Profile management
  - Password recovery and reset
  - Account deactivation/reactivation

- **Authentication**
  - OAuth2 implementation via Laravel Passport
  - Bearer token authentication
  - Refresh token rotation
  - Session management
  - API key generation and management

- **Authorization**
  - Role-Based Access Control (RBAC)
  - Permission-based access control
  - Resource-level permissions
  - Custom policy definitions

- **Security**
  - Multi-Factor Authentication (MFA)
  - Brute force protection
  - Rate limiting
  - IP whitelisting
  - Request validation and sanitization

- **Audit & Monitoring**
  - Comprehensive audit logging
  - User activity tracking
  - Failed authentication attempts
  - Security event logging
  - API usage metrics

## Prerequisites

### System Requirements

- PHP 8.1 or higher
- Composer 2.0+
- MySQL 8.0+ or PostgreSQL 13+
- Redis (for caching and queues)
- Node.js 16+ and NPM (for frontend assets)

### Required Extensions

- PHP Extensions:
  - OpenSSL
  - PDO
  - Mbstring
  - Tokenizer
  - XML
  - Ctype
  - JSON
  - BCMath

## Installation

1. Clone the repository:

```bash
git clone https://github.com/your-org/cerberus.git
cd cerberus
```

2. Install PHP dependencies:

```bash
composer install
```

3. Copy the environment file:

```bash
cp .env.example .env
```

4. Generate application key:

```bash
php artisan key:generate
```

5. Configure your database in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cerberus
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run database migrations and seeders:

```bash
php artisan migrate
php artisan db:seed
```

7. Install Laravel Passport:

```bash
php artisan passport:install
```

8. Configure Passport keys:

```bash
php artisan passport:keys
```

## Configuration

### Environment Variables

Key configuration options in your `.env` file:

```env
APP_NAME=Cerberus
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# OAuth Configuration
PASSPORT_TOKEN_EXPIRE_IN=1440
PASSPORT_REFRESH_TOKEN_EXPIRE_IN=43200

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Security Settings

Configure security-related settings in `config/auth.php`:

```php
return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],
    'guards' => [
        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
    ],
    // ...
];
```

## API Documentation

### Authentication Endpoints

#### OAuth2 Password Grant

```http
POST /oauth/token
Content-Type: application/json

{
    "grant_type": "password",
    "client_id": "client-id",
    "client_secret": "client-secret",
    "username": "user@example.com",
    "password": "password",
    "scope": ""
}
```

#### OAuth2 Refresh Token

```http
POST /oauth/token
Content-Type: application/json

{
    "grant_type": "refresh_token",
    "refresh_token": "def502...",
    "client_id": "client-id",
    "client_secret": "client-secret",
    "scope": ""
}
```

#### Register

```http
POST /api/v1/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

### User Management Endpoints

#### Get User Profile

```http
GET /api/v1/users/profile
Authorization: Bearer {access_token}
```

#### Update User Profile

```http
PUT /api/v1/users/profile
Authorization: Bearer {access_token}
Content-Type: application/json

{
    "name": "Updated Name",
    "email": "new.email@example.com"
}
```

For complete API documentation, visit `/docs` after setting up the application.

## Testing

Run the test suite:

```bash
php artisan test
```

Run specific test category:

```bash
php artisan test --group=auth
php artisan test --group=users
php artisan test --group=roles
```

## Security Considerations

- Always use HTTPS in production
- Regularly rotate OAuth client secrets
- Monitor failed authentication attempts
- Enable MFA for administrative accounts
- Keep all dependencies up to date
- Regular security audits and penetration testing
- Implement proper OAuth2 scopes for access control
- Use secure Passport token storage

## Contributing

1. Fork the repository
2. Create your feature branch: `git checkout -b feature/amazing-feature`
3. Commit your changes: `git commit -m 'Add amazing feature'`
4. Push to the branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## Support

For support, email <tjthavarshan@gmail.com> or create an issue in the GitHub repository.

## Acknowledgments

- Laravel Team for the amazing framework
- Laravel Passport contributors
- All other open-source packages used in this project

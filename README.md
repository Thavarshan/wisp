# Crysper - One-Time Secret Sharing Application

Crysper is a secure and straightforward application for sharing encrypted, one-time secrets through a protected link that automatically expires. Once a secret is viewed or reaches its expiration time, it is permanently deleted. For added security, users can set an optional password.

## Table of Contents

- [Table of Contents](#table-of-contents)
- [Features](#features)
- [Technologies Used](#technologies-used)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Migrations](#database-migrations)
- [Usage](#usage)
  - [Creating a Secret](#creating-a-secret)
  - [Accessing a Secret](#accessing-a-secret)
- [Testing](#testing)
  - [Run Feature Tests](#run-feature-tests)
  - [Run Frontend Tests](#run-frontend-tests)
- [Project Structure](#project-structure)
- [Security Considerations](#security-considerations)
- [Contributing](#contributing)
- [License](#license)

---

## Features

- Share encrypted, one-time secrets via secure links.
- Optional password protection for shared secrets.
- Automatic expiration and deletion of secrets after a single view or expiration time.
- Customizable expiration times ranging from minutes to days.
- User-friendly interface for creating and accessing secrets.
- Secure handling of passwords using hashing and encryption.
- Destruction of secrets upon viewing or manual obliteration.

---

## Technologies Used

- **Backend:** Laravel 12, PHP 8.4
- **Frontend:** Vue 3, Inertia.js, TypeScript
- **Database:** PostgreSQL
- **Styling:** Tailwind CSS, shadcn/ui components
- **Security:** Laravel Encryption, Hashing, HTTPS
- **Hosting:** Heroku
- **JavaScript Runtime:** NodeJS v22

---

## Requirements

- PHP 8.4 or higher
- Composer
- Node.js v22 and npm
- PostgreSQL
- OpenSSL for encryption
- Nginx or Apache server

---

## Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/your-username/crysper.git
   cd crysper
   ```

2. **Install PHP dependencies:**

   ```bash
   composer install
   ```

3. **Install Node dependencies:**

   ```bash
   npm install
   ```

4. **Compile front-end assets:**

   ```bash
   npm run build
   ```

---

## Configuration

1. **Copy `.env` file:**

   ```bash
   cp .env.example .env
   ```

2. **Configure database in `.env`:**

   ```
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=crysper
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

3. **Generate application key:**

   ```bash
   php artisan key:generate
   ```

4. **Configure encryption settings in `.env`:**

   ```
   APP_KEY=base64:your_generated_key
   APP_CIPHER=AES-256-CBC
   ```

---

## Database Migrations

Run the following command to create database tables:

```bash
php artisan migrate
```

---

## Usage

### Creating a Secret

1. Visit the homepage and fill out the form:
   - Secret content
   - Optional name
   - Expiration time
   - Optional password

2. Click "Store Secret" to generate a secure link.

### Accessing a Secret

1. Use the provided secure link to access the secret.
2. Enter the password if the secret is password-protected.
3. The secret will be destroyed upon viewing.

---

## Testing

### Run Feature Tests

```bash
php artisan test
```

### Run Frontend Tests

```bash
npm run test
```

---

## Project Structure

```
crysper/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   ├── Models/
├── resources/
│   ├── js/
│   │   ├── components/
│   │   ├── pages/
│   │   ├── utils/
│   ├── views/
├── routes/
│   ├── web.php
├── tests/
│   ├── Feature/
│   ├── Unit/
├── .env.example
├── package.json
├── composer.json
└── README.md
```

---

## Security Considerations

- **Encryption:** Secrets are encrypted using Laravel's AES-256-CBC encryption.
- **Password Protection:** Passwords are hashed using Bcrypt.
- **HTTPS:** Ensure HTTPS is enabled for secure communication.
- **Auto-deletion:** Secrets are automatically deleted after viewing or expiration.

---

## Contributing

1. Fork the repository.
2. Create a new branch:

   ```bash
   git checkout -b feature/your-feature-name
   ```

3. Commit your changes:

   ```bash
   git commit -m "Add your message here"
   ```

4. Push to the branch:

   ```bash
   git push origin feature/your-feature-name
   ```

5. Create a Pull Request.

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

For any issues, please open an issue on GitHub. Thank you for using Crysper!

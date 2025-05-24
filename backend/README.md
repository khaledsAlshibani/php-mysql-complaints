# PHP MySQL Complaints App Backend

A PHP backend application built with a clean architecture for handling user complaints and management.

- JWT-based authentication
- Database migrations
- Static file serving

## Prerequisites

- PHP 8.2 (CLI)
- Composer 2.7.2
- Docker (For database)

## Getting Started

Ensure you are inside the "/backend/" directory.

1. Install dependencies:
   ```bash
   # Clone the repository (if you haven't already)
   # Install PHP dependencies
   composer install
   ```

2. Copy environment configuration:
   ```bash
   cp .env.example .env
   ```

   Or try:

   ```bash
   copy .env.example .env
   ```
   

3. Database setup:
   ```bash
   # Start MySQL and PHPMyAdmin containers
   docker compose up -d

   # Wait a few seconds for MySQL to initialize
   # You can check status with:
   docker compose ps
   ```

4. Application setup:
   ```bash
   # This will:
   # - Run database migrations
   # - Create required directories
   # - Set up initial admin user
   php scripts/setup.php
   ```

5. Start development server:
   ```bash
   # Start PHP development server at http://localhost:8000
   php scripts/serve.php
   ```

6. Database management (optional):
   Access PHPMyAdmin to manage your database:
   - URL: `http://localhost:8090`
   - Username: `root`
   - Password: `root`

## Development Server

The application includes a development server script that handles both API requests and static files:

```bash
php scripts/serve.php
```

This will start a server at `http://localhost:8000` with:
- API endpoints served from `public/index.php`
- Static files (uploads) served from `storage/uploads`
- Automatic routing through `public/router.php`
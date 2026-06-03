# UNaBSYSv3

## Academic Staff Management System - UNaB

UNaBSYSv3 is an academic staff management platform for Universidad Nacional Guillermo Brown (UNaB). It centralizes teacher records, user administration, academic structures, and faculty position assignment and renewal workflows.

The application is built with Laravel 10, Blade, and MariaDB.

## Project Status

- Active institutional project
- Focused on academic administration and role-based workflows
- Suitable as a portfolio example for backend, permissions, and data-model design

## Key Features

- User management:
  - Create, update, and manage multiple user roles.
  - Supports administrative profiles with different permission levels.
- Faculty management:
  - Teacher registration and profile management.
  - Faculty position assignment and renewal workflows.
- Academic structure management:
  - CRUD for subjects, degree programs, and departments.
  - Coordinator assignment by subject, degree program, and department.
- Role-based access control:
  - Fine-grained permissions depending on institutional role.

## Tech Stack

- Backend: Laravel 10, PHP 8.1+
- Frontend: Blade templates
- Database: MariaDB 10.4+
- Web server: Apache or Nginx

## Deployment Notes

This project is designed to run in a traditional PHP hosting environment or a server managed through Apache/Nginx.

Recommended production steps:

1. Configure `.env` for production credentials.
2. Run `composer install --no-dev`.
3. Run `php artisan migrate --force`.
4. Cache configuration and routes.
5. Point the web server document root to the `public/` directory.

## Installation

### 1) Clone the repository

```bash
git clone https://github.com/alyohara/UNaBSYSv3.git
cd UNaBSYSv3
```

### 2) Configure environment

```bash
cp .env.example .env
```

Update `.env` with your local configuration:

```env
APP_NAME="Academic Staff Management"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### 3) Install dependencies

```bash
composer install
```

### 4) Generate application key

```bash
php artisan key:generate
```

### 5) Run migrations

```bash
php artisan migrate
```

### 6) Seed database (optional)

```bash
php artisan db:seed
```

### 7) Start development server

```bash
php artisan serve
```

The app will be available at http://localhost:8000.

## User Roles

- Administrator:
  - Full management of users, teachers, subjects, degree programs, and departments.
- Administrative staff:
  - Level 2: restricted administrative access.
  - Level 1: broader operational permissions.
- Coordinators:
  - Management and coordination responsibilities for assigned subjects, degree programs, and departments.
- Bedel / attendance operator:
  - Teaching workload and attendance-related operations.

## Why This Project Matters

This repository shows experience with institutional software, access control, normalized academic data, and workflow-driven backend development. It complements my work in health-tech and education systems.

## License

This project is licensed under the MIT License.

## Authors

- Diego Agustin Ambrossio - diego.ambrossio@unab.edu.ar
- Angel Leonardo Bianco - angel.bianco@unab.edu.ar

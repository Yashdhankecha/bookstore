# 📚 Bookstore - XAMPP One-Click Setup

PHP + MySQL bookstore project with automatic database setup.

## Quick Start (XAMPP)

1. **Place project in XAMPP**
   ```
   C:\xampp\htdocs\book
   ```

2. **Start XAMPP**
   - Apache → Start
   - MySQL → Start

3. **Run one-click installer**
   ```
   http://localhost/book/setup.php
   ```
   Click **Install Now** → Done in 2-3 seconds.

4. **Open site**
   - Home: `http://localhost/book/`
   - Admin: `http://localhost/book/admin/dashboard.php`

## Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@bookstore.com | password |

## What the Installer Does

- Checks PHP and PDO MySQL
- Creates `bookstore` database if not exists
- Imports `database.sql` (all tables + sample data)
- Verifies admin user
- Auto-detects `APP_URL` (works with any folder/port)
- Creates `install.lock` to prevent re-installation

## Requirements

- PHP 7.4+ (XAMPP with PHP 8.x recommended)
- MySQL / MariaDB
- Apache

Config defaults in `core/config.php`:
```
DB_HOST=127.0.0.1
DB_USER=root
DB_PASS= (empty)
DB_NAME=bookstore
```

If your MySQL password differs, update `core/config.php` or use Advanced Settings in `setup.php`.

## Project Structure

```
book/
├── setup.php          # One-click installer
├── database.sql       # Auto-imported
├── core/config.php    # Auto-detects APP_URL
├── core/classes/Database.php
├── admin/
├── assets/
└── includes/
```

## Troubleshooting

- **MySQL connection failed** → Ensure MySQL is running in XAMPP, port 3306
- **Redirects to setup.php** → Database not ready, run setup.php
- **Reinstall** → Open setup.php → Force Re-Install or delete install.lock

## After Setup

You can delete `setup.php` for security or keep it (shows "Already Installed" on re-open).

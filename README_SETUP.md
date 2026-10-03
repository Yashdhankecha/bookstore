# 📚 Bookstore - XAMPP One-Click Setup Guide

## 🚀 Super Easy Setup (XAMPP)

This project is ready in **one click**. No manual phpMyAdmin import required.

### Step 1: Place Project in XAMPP
```
1. Install XAMPP (if not already installed)
2. Copy the project folder to:
   C:\xampp\htdocs\book
   (Folder name can be 'book' or any name - it is auto-detected)
```

### Step 2: Start XAMPP
```
1. Open XAMPP Control Panel
2. Apache -> Start (should turn green)
3. MySQL  -> Start (should turn green)
   Both must be green!
```

### Step 3: Run Setup File (One Click!)
```
Open in browser:
http://localhost/book/setup.php

If you are using port 8080:
http://localhost:8080/book/setup.php

Then click "Install Now" -> Done in 2 seconds! 🎉
```

### Step 4: Open the Site
```
Home:  http://localhost/book/
Admin: http://localhost/book/admin/dashboard.php
Login: http://localhost/book/login.php
```

---

## 🔑 Default Login Credentials

| Role  | Email                  | Password   |
|-------|------------------------|------------|
| Admin | admin@bookstore.com    | password   |
| Test  | palak@gmail.com        | (hashed)   |

> After admin login, you can create a new user or register.

---

## ✨ What Is Automated?

`setup.php` does all of this automatically:
- ✅ MySQL connection check
- ✅ Auto-creates `bookstore` database
- ✅ Auto-imports `database.sql` (all tables + dummy data)
- ✅ Checks `assets/images` folder
- ✅ Verifies admin user
- ✅ Auto-detects APP_URL (works for localhost/book or localhost:8080/bookstore)
- ✅ Creates `install.lock` to prevent accidental re-installation

---

## 🔧 Manual Settings (If Needed)

Settings in `core/config.php` (XAMPP defaults):
```php
DB_HOST = 127.0.0.1
DB_USER = root
DB_PASS = (empty - XAMPP default)
DB_NAME = bookstore
```

If your MySQL password is different, edit `core/config.php` or change the constants at the top of `setup.php`.

---

## ❓ Troubleshooting

**1. "MySQL connection failed" error**
- Is MySQL started in XAMPP? Check if it is green
- MySQL port should be 3306 (check XAMPP Config -> my.ini)

**2. "database.sql not found"**
- File `database.sql` must be in project root (book/database.sql)

**3. Site redirects to setup.php immediately**
- Database is not ready yet. Open `setup.php` and click Install

**4. Need to reinstall / reset DB**
- Open `setup.php` -> Click "Force Re-Install"
- Or delete the `install.lock` file and run setup again

**5. Port 8080 error**
- Config is now auto-detected, but if you still have issues check the APP_URL shown in `setup.php`

---

## 📁 Project Structure
```
book/
├── setup.php          ← ⭐ One-Click Installer (run this)
├── install.lock       ← Auto-created after install
├── database.sql       ← Auto-imported
├── core/config.php    ← Auto-detects APP_URL
├── core/classes/Database.php ← Auto-redirects to setup if DB missing
├── admin/             ← Admin panel
├── assets/            ← CSS/JS/Images
├── includes/          ← Header/Footer
└── ... (shop, cart, etc.)
```

---

## 🗑️ After Setup

After setup is complete, you can delete `setup.php` for security, or keep it — if opened again it will show "Already Installed".

---

## 💡 Distributing as Zip

Share the zip file. The recipient just needs to:
> "Extract to XAMPP htdocs, open `setup.php` and click Install — that's it!"

No phpMyAdmin, no manual import required.

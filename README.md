# ThriftWear PHP + SQL

ThriftWear is a PHP-rendered sustainable fashion marketplace backed by MySQL. The application lives in `php/`.

## Requirements

- PHP 8.1+ with PDO MySQL enabled
- Docker Desktop, or a local MySQL/MariaDB server

## Run with Docker MySQL
powershell

docker compose up -d
Get-Content php/schema.sql | docker exec -i thriftwear-vscode-full-stack-mysql-1 mysql -uroot -proot_dev_only
Get-Content php/seed.sql | docker exec -i thriftwear-vscode-full-stack-mysql-1 mysql -uroot -proot_dev_only
php -S localhost:8080 -t php php/index.php

Open http://localhost:8080.

The MySQL container uses database `thriftwear`, user `thriftwear`, password `thriftwear_dev`, and host port `3307` (container port `3306`). The schema is available in `php/schema.sql`, and `php/seed.sql` adds seven demo profiles with 16 listings across all four categories.

Demo accounts:
email:admin@gmail.com
password: Admin@123

We have made this website available online. You can open this website by:
https://thriftwear.kesug.com/


## Features

- PHP session registration and login
- Password hashing with `password_hash()` and `password_verify()`
- SQL-backed marketplace listings
- Search and category filtering with prepared statements
- Authenticated listing creation
- Owner-only listing deletion
- Profile listing view
- "Continue with Google" sign-in (OAuth 2.0 authorization code flow)
- Figma-aligned responsive marketplace styling with a green, white, and light-gray palette

## Google sign-in setup

1. In [Google Cloud Console](https://console.cloud.google.com/apis/credentials), create an **OAuth client ID** of type **Web application**.
2. Add `http://localhost:8080/?page=google-callback` as an authorized redirect URI.
3. Copy the client ID and client secret into `php/config.php`:

```php
const GOOGLE_CLIENT_ID = 'your-client-id.apps.googleusercontent.com';
const GOOGLE_CLIENT_SECRET = 'your-client-secret';
```

Until these are set, clicking "Continue with Google" shows a flash message instead of failing. Signing in with Google auto-creates a ThriftWear account (matched by email or Google account ID) with no password set.

## PHP file structure

- `php/index.php` routes requests and loads page data
- `php/actions.php` handles login, registration, listings, logout, and deletion
- `php/pages/` contains one template for each screen
- `php/partials/` contains the shared header and footer
- `php/config.php` contains the database connection and shared helpers

## Local MySQL configuration

Update the constants in `php/config.php` if your local MySQL credentials differ, then import `php/schema.sql` before starting PHP.


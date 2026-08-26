<?php
declare(strict_types=1);

session_start();

const DB_HOST = '127.0.0.1';
const DB_PORT = '3307';
const DB_NAME = 'thriftwear';
const DB_USER = 'thriftwear';
const DB_PASS = 'thriftwear_dev';

const GOOGLE_CLIENT_ID = '742903279913-3669buh680ipurjstnp3dt9josjeu7en.apps.googleusercontent.com';
const GOOGLE_CLIENT_SECRET = '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';
const GOOGLE_REDIRECT_URI = 'http://localhost:8080/?page=google-callback';

function google_oauth_configured(): bool
{
    return GOOGLE_CLIENT_ID !== '' && GOOGLE_CLIENT_SECRET !== '';
}

function db(): PDO
{
    static $pdo;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_auth(): array
{
    $user = current_user();
    if (!$user) {
        header('Location: ?page=login');
        exit;
    }
    return $user;
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

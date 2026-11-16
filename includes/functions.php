<?php
require_once __DIR__ . '/../config/config.php';

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function redirect(string $path): never {
    header('Location: ' . $path);
    exit;
}
function require_login(): void {
    if (empty($_SESSION['user_id'])) redirect('login.php');
}
function flash(string $message, string $type = 'success'): void {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sent = $_POST['csrf'] ?? '';
        if (!hash_equals(csrf_token(), $sent)) {
            http_response_code(419);
            exit('Invalid request token. Please go back and try again.');
        }
    }
}
function current_page(): string {
    return basename($_SERVER['PHP_SELF']);
}

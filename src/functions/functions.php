<?php

function makePage(string $page, array $args): void {
    extract($args);

    require_once COMPONENTS . 'header.php';
    
    require_once VIEWS . $page . '.php';

    require_once COMPONENTS . 'footer.php';
}

function createCsrf(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return sprintf(
        '<input type="hidden" name="csrf_token" value="%s">',
        htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')
    );
}

function csrfVerify(?string $token = null): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $token ??= $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    return !empty($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}
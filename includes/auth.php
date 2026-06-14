<?php
declare(strict_types=1);

function logout_form(string $class = ''): string {
    $classAttribute = $class === '' ? '' : ' class="' . e($class) . '"';
    return '<form method="post" action="logout.php"' . $classAttribute . '>'
        . csrf_input()
        . '<button class="nav-button light" type="submit">' . e(t('logout')) . '</button>'
        . '</form>';
}

function current_user(): ?array {
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    try {
        $stmt = db()->prepare(
            'SELECT id, name, email, phone, address, role, created_at
             FROM users
             WHERE id = ?
             LIMIT 1'
        );
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch() ?: null;
    } catch (Throwable) {
        return null;
    }
}

function require_login(): array {
    $user = current_user();
    if (!$user) {
        redirect('login.php');
    }

    return $user;
}

function require_admin(): array {
    $user = require_login();
    if ($user['role'] !== 'admin') {
        redirect('user-dashboard.php');
    }

    return $user;
}

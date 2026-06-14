<?php
declare(strict_types=1);

function csrf_token(): string {
    return $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
}

function csrf_input(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void {
    $submittedToken = (string)($_POST['csrf_token'] ?? '');
    if ($submittedToken === '' || !hash_equals(csrf_token(), $submittedToken)) {
        http_response_code(403);
        exit('Invalid or expired form token.');
    }
}

<?php
require __DIR__ . '/includes/app.php';

$user = current_user();

ob_start();
require __DIR__ . '/index.html';
$html = ob_get_clean();

if ($user) {
    $dashboardHref = $user['role'] === 'admin' ? 'admin.php' : 'dashboard.php';
    $dashboardText = t('dashboard');
    $authActions = '<!-- AUTH_ACTIONS_START -->'
        . '<span class="nav-auth" data-auth-actions>'
        . '<a class="nav-button primary" href="' . e($dashboardHref) . '"><i data-lucide="layout-dashboard" aria-hidden="true"></i><span>' . e($dashboardText) . '</span></a>'
        . '<a class="nav-button light" href="logout.php"><i data-lucide="log-out" aria-hidden="true"></i><span>' . e(t('logout')) . '</span></a>'
        . '</span>'
        . '<!-- AUTH_ACTIONS_END -->';
    $html = preg_replace('/<!-- AUTH_ACTIONS_START -->.*?<!-- AUTH_ACTIONS_END -->/s', $authActions, $html) ?? $html;
}

echo $html;

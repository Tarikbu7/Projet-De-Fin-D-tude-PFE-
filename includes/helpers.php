<?php
declare(strict_types=1);

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never {
    header('Location: ' . $path);
    exit;
}

function statuses(): array {
    return ['Pending', 'Accepted', 'In progress', 'Completed', 'Cancelled'];
}

function review_statuses(): array {
    return ['Pending', 'Approved', 'Rejected'];
}

function normalize_price(string $price): ?float {
    $price = trim($price);
    if ($price === '') {
        return null;
    }

    if (!is_numeric($price) || (float)$price < 0) {
        throw new InvalidArgumentException('Price must be a non-negative number.');
    }

    return round((float)$price, 2);
}

function render_header(string $title, ?array $user = null): void {
    $dir = is_rtl() ? 'rtl' : 'ltr';
    $lang = lang();
    $safeTitle = e($title);
    $homeLabel = e(t('home'));
    $dashboardLink = $user && $user['role'] === 'admin'
        ? '<a class="nav-button primary" href="admin.php">' . e(t('dashboard')) . '</a>'
        : '';
    $authLink = $user ? logout_form('nav-logout-form') : '<a class="nav-button primary" href="login.php">' . e(t('sign_in')) . '</a>';
    echo <<<HTML
<!DOCTYPE html>
<html lang="$lang" dir="$dir">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{$safeTitle} - Slahpc</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body class="app-body">
  <header class="site-header dashboard-header">
    <a class="brand" href="index.php">
      <span class="brand-mark">CR</span>
      <span><strong>Slahpc</strong><small>{$safeTitle}</small></span>
    </a>
    <nav class="site-nav dashboard-nav">
      <a href="index.php">{$homeLabel}</a>
      $dashboardLink
      $authLink
      <form class="language-form" method="get">
        <select name="lang" onchange="this.form.submit()">
HTML;
    foreach (['en' => 'EN', 'fr' => 'FR', 'ar' => 'AR'] as $code => $label) {
        $selected = $code === lang() ? ' selected' : '';
        echo '<option value="' . e($code) . '"' . $selected . '>' . e($label) . '</option>';
    }
    echo <<<HTML
        </select>
      </form>
    </nav>
  </header>
  <main class="dashboard-shell">
HTML;
}

function render_footer(): void {
    echo '</main><script src="assets/password-toggle.js"></script></body></html>';
}

function flash(?string $message = null): ?string {
    if ($message !== null) {
        $_SESSION['flash'] = $message;
        return null;
    }

    $value = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $value;
}

function table_empty(int $count, int $cols): void {
    if ($count === 0) {
        echo '<tr><td colspan="' . $cols . '">' . e(t('no_rows')) . '</td></tr>';
    }
}

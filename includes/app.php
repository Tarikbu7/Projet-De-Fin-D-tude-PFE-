<?php
declare(strict_types=1);

session_name('slahpc_session');
session_start();

const DB_HOST = '127.0.0.1';
const DB_PORT = '3306';
const DB_NAME = 'slah_pc';
const DB_USER = 'root';
const DB_PASS = '';

$languages = ['en', 'fr', 'ar'];
if (isset($_GET['lang']) && in_array($_GET['lang'], $languages, true)) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'en';

$i18n = [
    'en' => [
        'dashboard' => 'Dashboard', 'admin' => 'Admin', 'user' => 'User', 'logout' => 'Logout',
        'login' => 'Login', 'register' => 'Register', 'email' => 'Email', 'password' => 'Password',
        'name' => 'Name', 'phone' => 'Phone', 'address' => 'Address', 'save' => 'Save',
        'appointments' => 'Appointments', 'products' => 'Products', 'orders' => 'Orders',
        'messages' => 'Messages', 'services' => 'Service prices', 'invoices' => 'Invoices',
        'customers' => 'Customers', 'customer' => 'Customer', 'stats' => 'Stats', 'pc_build' => 'PC build request',
        'status' => 'Status', 'date' => 'Date', 'time' => 'Time', 'details' => 'Details',
        'request_appointment' => 'Request appointment', 'place_order' => 'Place order',
        'send_message' => 'Send message', 'submit' => 'Submit', 'pending' => 'Pending',
        'confirmed' => 'Confirmed', 'in_progress' => 'In progress', 'completed' => 'Completed',
        'cancelled' => 'Cancelled', 'welcome' => 'Welcome', 'admin_dashboard' => 'Admin dashboard',
        'user_dashboard' => 'User dashboard', 'home' => 'Home', 'language' => 'Language',
        'no_rows' => 'No records yet.', 'setup_needed' => 'Database setup is needed.',
        'open_site' => 'Open site', 'quantity' => 'Quantity', 'price' => 'Price', 'total' => 'Total',
        'budget' => 'Budget', 'purpose' => 'Purpose', 'subject' => 'Subject', 'message' => 'Message',
        'admin_note' => 'Admin note', 'create_invoice' => 'Create invoice', 'amount' => 'Amount'
    ],
    'fr' => [
        'dashboard' => 'Tableau de bord', 'admin' => 'Admin', 'user' => 'Utilisateur', 'logout' => 'Déconnexion',
        'login' => 'Connexion', 'register' => 'Inscription', 'email' => 'E-mail', 'password' => 'Mot de passe',
        'name' => 'Nom', 'phone' => 'Téléphone', 'address' => 'Adresse', 'save' => 'Enregistrer',
        'appointments' => 'Rendez-vous', 'products' => 'Produits', 'orders' => 'Commandes',
        'messages' => 'Messages', 'services' => 'Prix des services', 'invoices' => 'Factures',
        'customers' => 'Clients', 'customer' => 'Client', 'stats' => 'Statistiques', 'pc_build' => 'Demande de PC sur mesure',
        'status' => 'Statut', 'date' => 'Date', 'time' => 'Heure', 'details' => 'Détails',
        'request_appointment' => 'Demander un rendez-vous', 'place_order' => 'Passer commande',
        'send_message' => 'Envoyer un message', 'submit' => 'Envoyer', 'pending' => 'En attente',
        'confirmed' => 'Confirmé', 'in_progress' => 'En cours', 'completed' => 'Terminé',
        'cancelled' => 'Annulé', 'welcome' => 'Bienvenue', 'admin_dashboard' => 'Tableau admin',
        'user_dashboard' => 'Tableau utilisateur', 'home' => 'Accueil', 'language' => 'Langue',
        'no_rows' => 'Aucun enregistrement.', 'setup_needed' => 'La base de données doit être installée.',
        'open_site' => 'Ouvrir le site', 'quantity' => 'Quantité', 'price' => 'Prix', 'total' => 'Total',
        'budget' => 'Budget', 'purpose' => 'Utilisation', 'subject' => 'Sujet', 'message' => 'Message',
        'admin_note' => 'Note admin', 'create_invoice' => 'Créer une facture', 'amount' => 'Montant'
    ],
    'ar' => [
        'dashboard' => 'لوحة التحكم', 'admin' => 'المدير', 'user' => 'المستخدم', 'logout' => 'تسجيل الخروج',
        'login' => 'تسجيل الدخول', 'register' => 'إنشاء حساب', 'email' => 'البريد الإلكتروني', 'password' => 'كلمة المرور',
        'name' => 'الاسم', 'phone' => 'الهاتف', 'address' => 'العنوان', 'save' => 'حفظ',
        'appointments' => 'المواعيد', 'products' => 'المنتجات', 'orders' => 'الطلبات',
        'messages' => 'الرسائل', 'services' => 'أسعار الخدمات', 'invoices' => 'الفواتير',
        'customers' => 'العملاء', 'customer' => 'العميل', 'stats' => 'الإحصائيات', 'pc_build' => 'طلب تجميع كمبيوتر',
        'status' => 'الحالة', 'date' => 'التاريخ', 'time' => 'الوقت', 'details' => 'التفاصيل',
        'request_appointment' => 'طلب موعد', 'place_order' => 'إرسال طلب شراء',
        'send_message' => 'إرسال رسالة', 'submit' => 'إرسال', 'pending' => 'قيد الانتظار',
        'confirmed' => 'مؤكد', 'in_progress' => 'قيد العمل', 'completed' => 'مكتمل',
        'cancelled' => 'ملغي', 'welcome' => 'مرحبا', 'admin_dashboard' => 'لوحة تحكم المدير',
        'user_dashboard' => 'لوحة تحكم المستخدم', 'home' => 'الرئيسية', 'language' => 'اللغة',
        'no_rows' => 'لا توجد سجلات بعد.', 'setup_needed' => 'يجب إعداد قاعدة البيانات.',
        'open_site' => 'فتح الموقع', 'quantity' => 'الكمية', 'price' => 'السعر', 'total' => 'المجموع',
        'budget' => 'الميزانية', 'purpose' => 'الاستخدام', 'subject' => 'الموضوع', 'message' => 'الرسالة',
        'admin_note' => 'ملاحظة المدير', 'create_invoice' => 'إنشاء فاتورة', 'amount' => 'المبلغ'
    ],
];

function lang(): string {
    return $_SESSION['lang'] ?? 'en';
}

function is_rtl(): bool {
    return lang() === 'ar';
}

function t(string $key): string {
    global $i18n;
    return $i18n[lang()][$key] ?? $i18n['en'][$key] ?? $key;
}

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function db(bool $withDatabase = true): PDO {
    if (!extension_loaded('pdo_mysql')) {
        throw new RuntimeException('The pdo_mysql extension is not enabled. Start PHP with this project php.ini.');
    }

    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4';
    if ($withDatabase) {
        $dsn .= ';dbname=' . DB_NAME;
    }

    return new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

function redirect(string $path): never {
    header('Location: ' . $path);
    exit;
}

function current_user(): ?array {
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    try {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
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
        redirect('dashboard.php');
    }
    return $user;
}

function statuses(): array {
    return ['Pending', 'Confirmed', 'In progress', 'Completed', 'Cancelled'];
}

function status_label(string $status): string {
    return match ($status) {
        'Pending' => t('pending'),
        'Confirmed' => t('confirmed'),
        'In progress' => t('in_progress'),
        'Completed' => t('completed'),
        'Cancelled' => t('cancelled'),
        default => $status,
    };
}

function render_header(string $title, ?array $user = null): void {
    $dir = is_rtl() ? 'rtl' : 'ltr';
    $lang = lang();
    $adminLink = $user && $user['role'] === 'admin' ? '<a href="admin.php">' . e(t('admin')) . '</a>' : '';
    $dashboardLink = $user ? '<a href="dashboard.php">' . e(t('dashboard')) . '</a>' : '';
    $authLink = $user ? '<a href="logout.php">' . e(t('logout')) . '</a>' : '<a href="login.php">' . e(t('login')) . '</a>';
    echo <<<HTML
<!DOCTYPE html>
<html lang="$lang" dir="$dir">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{$title} - Slahpc</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body class="app-body">
  <header class="site-header dashboard-header">
    <a class="brand" href="index.php">
      <span class="brand-mark">CR</span>
      <span><strong>Slahpc</strong><small>{$title}</small></span>
    </a>
    <nav class="site-nav dashboard-nav">
      <a href="index.php">{$GLOBALS['i18n'][$lang]['home']}</a>
      $dashboardLink
      $adminLink
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
    echo '</main></body></html>';
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

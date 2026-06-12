<?php
declare(strict_types=1);

// Start the user session.
session_name('slahpc_session');
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

// Database settings.
const DB_HOST = '127.0.0.1';
const DB_PORT = '3306';
const DB_NAME = 'slah_pc';
const DB_USER = 'root';
const DB_PASS = '';

// Save the selected language.
$languages = ['en', 'fr', 'ar'];
if (isset($_GET['lang']) && in_array($_GET['lang'], $languages, true)) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'en';

// Text for each language.
$i18n = [
    'en' => [
        'dashboard' => 'Dashboard', 'admin' => 'Admin', 'user' => 'User', 'logout' => 'Logout',
        'login' => 'Login', 'register' => 'Register', 'email' => 'Email', 'password' => 'Password',
        'sign_in' => 'Sign in', 'no_account' => 'Do not have an account?', 'create_account' => 'Create account',
        'name' => 'Name', 'phone' => 'Phone', 'address' => 'Address', 'city' => 'City', 'save' => 'Save',
        'appointments' => 'Appointments', 'products' => 'Products', 'orders' => 'Orders',
        'messages' => 'Messages', 'services' => 'Services', 'invoices' => 'Invoices',
        'customers' => 'Customers', 'customer' => 'Customer', 'stats' => 'Stats', 'pc_build' => 'PC build request',
        'status' => 'Status', 'date' => 'Date', 'time' => 'Time', 'details' => 'Details',
        'request_appointment' => 'Request appointment', 'place_order' => 'Place order',
        'send_message' => 'Send message', 'submit' => 'Submit', 'pending' => 'Pending',
        'accepted' => 'Accepted', 'in_progress' => 'In progress', 'completed' => 'Completed',
        'cancelled' => 'Cancelled', 'welcome' => 'Welcome', 'admin_dashboard' => 'Admin dashboard',
        'user_dashboard' => 'User dashboard', 'home' => 'Home', 'language' => 'Language',
        'no_rows' => 'No records yet.', 'setup_needed' => 'Database setup is needed.',
        'open_site' => 'Open site', 'quantity' => 'Quantity', 'price' => 'Price', 'total' => 'Total',
        'budget' => 'Budget', 'purpose' => 'Purpose', 'subject' => 'Subject', 'message' => 'Message',
        'create_invoice' => 'Create invoice', 'amount' => 'Amount',
        'repair_requests' => 'Repair requests', 'awaiting_quote' => 'Awaiting quote',
        'track_repairs' => 'Track your repair appointment requests and their current status here.',
        'review' => 'Review', 'after_completion' => 'After completion', 'available' => 'Available',
        'leave_review' => 'Leave a review', 'leave_review_desc' => 'Choose a completed service, rate it, then write your comment.',
        'which_service_review' => '1. Which service do you want to review?',
        'choose_completed_service' => 'Choose a completed service',
        'how_was_service' => '2. How was the service?',
        'write_comment' => '3. Write your comment',
        'write_comment_placeholder' => 'Tell us what you liked or what could be better.',
        'send_review' => 'Send my review',
        'no_completed_service' => 'There is no completed service available to review yet.',
        'previous_reviews' => 'Your previous reviews',
        'my_reviews' => 'My reviews',
        'my_reviews_desc' => 'After a repair is completed, you can share your experience here.',
        'review_after_completed' => 'You can write a review after an appointment is marked as completed.',
        'rating' => 'Rating', 'choose_rating' => 'Choose a rating',
        'excellent' => 'Excellent', 'very_good' => 'Very good', 'good' => 'Good', 'fair' => 'Fair', 'poor' => 'Poor',
        'your_review' => 'Your review', 'review_placeholder' => 'Tell us how the repair went',
        'stars' => 'stars',
        'price_after_diagnosis' => 'Price after diagnosis',
        'choose_one' => 'Choose one',
        'service_type_label' => 'Service type',
        'address_label' => 'Address or service area',
        'problem_label' => 'Tell me what is wrong',
        'problem_placeholder' => 'Describe the computer problem',
        'send_request' => 'Send request',
        'hardware_price_note' => 'Hardware repair depends on the problem and replacement parts. You will receive a quote before work begins.',
    ],
    'fr' => [
        'dashboard' => 'Tableau de bord', 'admin' => 'Admin', 'user' => 'Utilisateur', 'logout' => 'Déconnexion',
        'login' => 'Connexion', 'register' => 'Inscription', 'email' => 'E-mail', 'password' => 'Mot de passe',
        'name' => 'Nom', 'phone' => 'Téléphone', 'address' => 'Adresse', 'city' => 'Ville', 'save' => 'Enregistrer',
        'appointments' => 'Rendez-vous', 'products' => 'Produits', 'orders' => 'Commandes',
        'messages' => 'Messages', 'services' => 'Prix des services', 'invoices' => 'Factures',
        'customers' => 'Clients', 'customer' => 'Client', 'stats' => 'Statistiques', 'pc_build' => 'Demande de PC sur mesure',
        'status' => 'Statut', 'date' => 'Date', 'time' => 'Heure', 'details' => 'Détails',
        'request_appointment' => 'Demander un rendez-vous', 'place_order' => 'Passer commande',
        'send_message' => 'Envoyer un message', 'submit' => 'Envoyer', 'pending' => 'En attente',
        'accepted' => 'Accepté', 'in_progress' => 'En cours', 'completed' => 'Terminé',
        'cancelled' => 'Annulé', 'welcome' => 'Bienvenue', 'admin_dashboard' => 'Tableau admin',
        'user_dashboard' => 'Tableau utilisateur', 'home' => 'Accueil', 'language' => 'Langue',
        'no_rows' => 'Aucun enregistrement.', 'setup_needed' => 'La base de données doit être installée.',
        'open_site' => 'Ouvrir le site', 'quantity' => 'Quantité', 'price' => 'Prix', 'total' => 'Total',
        'budget' => 'Budget', 'purpose' => 'Utilisation', 'subject' => 'Sujet', 'message' => 'Message',
        'create_invoice' => 'Créer une facture', 'amount' => 'Montant',
        'repair_requests' => 'Demandes de réparation', 'awaiting_quote' => 'Devis en attente',
        'track_repairs' => 'Suivez ici vos demandes de rendez-vous de réparation et leur statut actuel.',
        'review' => 'Avis', 'after_completion' => 'Après la fin', 'available' => 'Disponible',
        'leave_review' => 'Laisser un avis', 'leave_review_desc' => 'Choisissez un service terminé, notez-le, puis écrivez votre commentaire.',
        'which_service_review' => '1. Quel service souhaitez-vous évaluer ?',
        'choose_completed_service' => 'Choisir un service terminé',
        'how_was_service' => '2. Comment était le service ?',
        'write_comment' => '3. Écrivez votre commentaire',
        'write_comment_placeholder' => 'Dites-nous ce que vous avez aimé ou ce qui pourrait être amélioré.',
        'send_review' => 'Envoyer mon avis',
        'no_completed_service' => 'Aucun service terminé disponible pour un avis pour le moment.',
        'previous_reviews' => 'Vos avis précédents',
        'my_reviews' => 'Mes avis',
        'my_reviews_desc' => 'Après une réparation terminée, vous pouvez partager votre expérience ici.',
        'review_after_completed' => 'Vous pouvez rédiger un avis après qu\'un rendez-vous est marqué comme terminé.',
        'rating' => 'Note', 'choose_rating' => 'Choisir une note',
        'excellent' => 'Excellent', 'very_good' => 'Très bien', 'good' => 'Bien', 'fair' => 'Passable', 'poor' => 'Médiocre',
        'your_review' => 'Votre avis', 'review_placeholder' => 'Dites-nous comment s\'est passée la réparation',
        'stars' => 'étoiles',
        'price_after_diagnosis' => 'Prix après diagnostic',
        'choose_one' => 'Choisir',
        'service_type_label' => 'Type de service',
        'address_label' => 'Adresse ou zone de service',
        'problem_label' => 'Décrivez le problème',
        'problem_placeholder' => 'Décrivez le problème informatique',
        'send_request' => 'Envoyer la demande',
        'hardware_price_note' => 'La réparation matérielle dépend du problème et des pièces de rechange. Vous recevrez un devis avant le début des travaux.',
    ],
    'ar' => [
        'dashboard' => 'لوحة التحكم', 'admin' => 'المدير', 'user' => 'المستخدم', 'logout' => 'تسجيل الخروج',
        'login' => 'تسجيل الدخول', 'register' => 'إنشاء حساب', 'email' => 'البريد الإلكتروني', 'password' => 'كلمة المرور',
        'name' => 'الاسم', 'phone' => 'الهاتف', 'address' => 'العنوان', 'city' => 'المدينة', 'save' => 'حفظ',
        'appointments' => 'المواعيد', 'products' => 'المنتجات', 'orders' => 'الطلبات',
        'messages' => 'الرسائل', 'services' => 'الخدمات', 'invoices' => 'الفواتير',
        'customers' => 'العملاء', 'customer' => 'العميل', 'stats' => 'الإحصائيات', 'pc_build' => 'طلب تجميع كمبيوتر',
        'status' => 'الحالة', 'date' => 'التاريخ', 'time' => 'الوقت', 'details' => 'التفاصيل',
        'request_appointment' => 'طلب موعد', 'place_order' => 'إرسال طلب شراء',
        'send_message' => 'إرسال رسالة', 'submit' => 'إرسال', 'pending' => 'قيد الانتظار',
        'accepted' => 'مقبول', 'in_progress' => 'قيد العمل', 'completed' => 'مكتمل',
        'cancelled' => 'ملغي', 'welcome' => 'مرحبا', 'admin_dashboard' => 'لوحة تحكم المدير',
        'user_dashboard' => 'لوحة تحكم المستخدم', 'home' => 'الرئيسية', 'language' => 'اللغة',
        'no_rows' => 'لا توجد سجلات بعد.', 'setup_needed' => 'يجب إعداد قاعدة البيانات.',
        'open_site' => 'فتح الموقع', 'quantity' => 'الكمية', 'price' => 'السعر', 'total' => 'المجموع',
        'budget' => 'الميزانية', 'purpose' => 'الاستخدام', 'subject' => 'الموضوع', 'message' => 'الرسالة',
        'create_invoice' => 'إنشاء فاتورة', 'amount' => 'المبلغ',
        'repair_requests' => 'طلبات الإصلاح', 'awaiting_quote' => 'في انتظار عرض السعر',
        'track_repairs' => 'تابع هنا طلبات مواعيد الإصلاح وحالتها الراهنة.',
        'review' => 'تقييم', 'after_completion' => 'بعد الانتهاء', 'available' => 'متاح',
        'leave_review' => 'اترك تقييماً', 'leave_review_desc' => 'اختر خدمة مكتملة، قيّمها، ثم اكتب تعليقك.',
        'which_service_review' => '1. أي خدمة تريد تقييمها؟',
        'choose_completed_service' => 'اختر خدمة مكتملة',
        'how_was_service' => '2. كيف كانت الخدمة؟',
        'write_comment' => '3. اكتب تعليقك',
        'write_comment_placeholder' => 'أخبرنا بما أعجبك وما يمكن تحسينه.',
        'send_review' => 'إرسال تقييمي',
        'no_completed_service' => 'لا توجد خدمة مكتملة متاحة للتقييم حالياً.',
        'previous_reviews' => 'تقييماتك السابقة',
        'my_reviews' => 'تقييماتي',
        'my_reviews_desc' => 'بعد اكتمال الإصلاح، يمكنك مشاركة تجربتك هنا.',
        'review_after_completed' => 'يمكنك كتابة تقييم بعد وضع علامة "مكتمل" على الموعد.',
        'rating' => 'التقييم', 'choose_rating' => 'اختر تقييماً',
        'excellent' => 'ممتاز', 'very_good' => 'جيد جداً', 'good' => 'جيد', 'fair' => 'مقبول', 'poor' => 'ضعيف',
        'your_review' => 'تقييمك', 'review_placeholder' => 'أخبرنا كيف سارت عملية الإصلاح',
        'stars' => 'نجوم',
        'price_after_diagnosis' => 'السعر بعد التشخيص',
        'choose_one' => 'اختر',
        'service_type_label' => 'نوع الخدمة',
        'address_label' => 'العنوان أو منطقة الخدمة',
        'problem_label' => 'أخبرني بالمشكلة',
        'problem_placeholder' => 'صف مشكلة الحاسوب',
        'send_request' => 'إرسال الطلب',
        'hardware_price_note' => 'يعتمد إصلاح الأجهزة على طبيعة المشكلة وقطع الغيار. ستتلقى عرض سعر قبل بدء العمل.',
    ],
];

// Language and safe text tools.
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

// Connect to the database.
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

// Check and update database columns.
function database_column_exists(PDO $pdo, string $table, string $column): bool {
    $stmt = $pdo->prepare(
        'SELECT COUNT(*)
         FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = ?
           AND COLUMN_NAME = ?'
    );
    $stmt->execute([$table, $column]);

    return (int)$stmt->fetchColumn() > 0;
}

function ensure_database_column(PDO $pdo, string $table, string $column, string $definition): void {
    foreach ([$table, $column] as $identifier) {
        if (!preg_match('/^[a-z_][a-z0-9_]*$/i', $identifier)) {
            throw new InvalidArgumentException('Invalid database identifier.');
        }
    }

    if (!database_column_exists($pdo, $table, $column)) {
        $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
    }
}

function ensure_reviews_table(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        appointment_id INT NOT NULL,
        rating TINYINT UNSIGNED NOT NULL,
        comment TEXT NOT NULL,
        status ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        reviewed_at TIMESTAMP NULL DEFAULT NULL,
        UNIQUE KEY unique_appointment_review (appointment_id),
        KEY reviews_user_id (user_id),
        CONSTRAINT reviews_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT reviews_appointment_fk FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
}

// Page links and form security.
function redirect(string $path): never {
    header('Location: ' . $path);
    exit;
}

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

// Show the logout form.
function logout_form(string $class = ''): string {
    $classAttribute = $class === '' ? '' : ' class="' . e($class) . '"';
    return '<form method="post" action="logout.php"' . $classAttribute . '>'
        . csrf_input()
        . '<button class="nav-button light" type="submit">' . e(t('logout')) . '</button>'
        . '</form>';
}

// Get the user and check access.
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
        redirect('dashboard.php');
    }
    return $user;
}

// Status lists and price checks.
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

// Translate service names.
function translate_service(string $name): string {
    $map = [
        'en' => [
            'Hardware repair'          => 'Hardware repair',
            'Software repair'          => 'Software repair',
            'Wi-Fi / printer / setup'  => 'Wi-Fi / printer / setup',
            'Backup or data transfer'  => 'Backup or data transfer',
            'Diagnostic visit'         => 'Diagnostic visit',
        ],
        'fr' => [
            'Hardware repair'          => 'Réparation matérielle',
            'Software repair'          => 'Réparation logicielle',
            'Wi-Fi / printer / setup'  => 'Wi-Fi / imprimante / configuration',
            'Backup or data transfer'  => 'Sauvegarde ou transfert de données',
            'Diagnostic visit'         => 'Visite de diagnostic',
        ],
        'ar' => [
            'Hardware repair'          => 'إصلاح الأجهزة',
            'Software repair'          => 'إصلاح البرامج',
            'Wi-Fi / printer / setup'  => 'واي فاي / طابعة / إعداد',
            'Backup or data transfer'  => 'نسخ احتياطي أو نقل البيانات',
            'Diagnostic visit'         => 'زيارة التشخيص',
        ],
    ];
    $lang = lang();
    return $map[$lang][$name] ?? $map['en'][$name] ?? $name;
}

// Translate status names.
function status_label(string $status): string {
    return match ($status) {
        'Pending' => t('pending'),
        'Confirmed', 'Accepted' => t('accepted'),
        'In progress' => t('in_progress'),
        'Completed' => t('completed'),
        'Cancelled' => t('cancelled'),
        default => $status,
    };
}

// Show the page header and menu.
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

// Show the page footer.
function render_footer(): void {
    echo '</main><script src="assets/password-toggle.js"></script></body></html>';
}

// Save or show one message.
function flash(?string $message = null): ?string {
    if ($message !== null) {
        $_SESSION['flash'] = $message;
        return null;
    }
    $value = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $value;
}

// Show a message when a table is empty.
function table_empty(int $count, int $cols): void {
    if ($count === 0) {
        echo '<tr><td colspan="' . $cols . '">' . e(t('no_rows')) . '</td></tr>';
    }
}

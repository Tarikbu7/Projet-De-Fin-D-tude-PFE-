<?php
require __DIR__ . '/includes/app.php';

$user = current_user();
$csrfToken = csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'appointment') {
    if (!$user) {
        redirect('login.php');
    }
    if ($user['role'] === 'admin') {
        redirect('admin.php');
    }

    $serviceType = trim((string)($_POST['service_type'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $problemDetails = trim((string)($_POST['problem_details'] ?? ''));
    verify_csrf();
    $pdo = db();
    $service = $pdo->prepare('SELECT name, base_price FROM services WHERE active = 1 AND name = ? LIMIT 1');
    $service->execute([$serviceType]);
    $selectedService = $service->fetch();

    if (
        !$selectedService
        || $address === ''
        || mb_strlen($address) > 255
        || $problemDetails === ''
    ) {
        flash('Please fill out all appointment fields.');
        redirect('index.php#appointment');
    }

    $stmt = $pdo->prepare('INSERT INTO appointments (user_id, service_type, address, problem_details, price) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([
        $user['id'],
        $serviceType,
        $address,
        $problemDetails,
        $serviceType === 'Hardware repair' ? null : $selectedService['base_price'],
    ]);
    flash('Repair appointment request sent.');
    redirect('index.php#appointment');
}

ob_start();
require __DIR__ . '/index.html';
$html = ob_get_clean();

$appointmentContent = '';
$notice = flash();

if ($notice) {
    $noticeClass = $notice === 'Repair appointment request sent.' ? 'success' : 'error';
    $appointmentContent .= '<p class="notice ' . $noticeClass . '">' . e($notice) . '</p>';
}

if ($user && $user['role'] !== 'admin') {
    $services = db()->query('SELECT name, base_price FROM services WHERE active = 1 ORDER BY name')->fetchAll();
    $serviceOptions = '';
    foreach ($services as $service) {
        $priceLabel = number_format((float)$service['base_price'], 0) . ' MAD';
        if ($service['name'] === 'Hardware repair') {
            $priceLabel = 'Price after diagnosis';
        }
        $serviceOptions .= '<option value="' . e($service['name']) . '">' . e($service['name'] . ' - ' . $priceLabel) . '</option>';
    }

    $appointmentContent .=
        '<form action="index.php#appointment" method="post" class="booking-form home-appointment-form">'
        . '<input type="hidden" name="action" value="appointment">'
        . '<input type="hidden" name="csrf_token" value="' . e($csrfToken) . '">'
        . '<label><span data-i18n="labelService">Service type</span>'
        . '<select name="service_type" required><option value="" data-i18n="optionChoose">Choose one</option>' . $serviceOptions . '</select>'
        . '<small class="service-price-note" data-i18n="hardwarePriceNote">Hardware repair depends on the problem and replacement parts. You will receive a quote before work begins.</small></label>'
        . '<label><span data-i18n="labelAddress">Address or service area</span>'
        . '<input type="text" name="address" value="' . e($user['address'] ?? '') . '" autocomplete="street-address" required></label>'
        . '<label><span data-i18n="labelProblem">Tell me what is wrong</span>'
        . '<textarea name="problem_details" required data-i18n-placeholder="placeholderProblem" placeholder="Describe the computer problem"></textarea></label>'
        . '<button class="button primary full" type="submit"><i data-lucide="send" aria-hidden="true"></i>'
        . '<span data-i18n="sendRequest">Send request</span></button>'
        . '</form>';
} elseif ($user) {
    $appointmentContent .=
        '<div class="booking-form appointment-signin">'
        . '<p>Administrator accounts cannot create customer appointments.</p>'
        . '<a class="button primary full" href="admin.php">Open dashboard</a>'
        . '</div>';
} else {
    $appointmentContent .=
        '<div class="booking-form appointment-signin">'
        . '<i data-lucide="log-in" aria-hidden="true"></i>'
        . '<h3 data-i18n="appointmentSignIn">Sign in to request an appointment</h3>'
        . '<p data-i18n="appointmentSignInCopy">Your account lets you request an appointment and track its status.</p>'
        . '<a class="button primary full" href="login.php"><span data-i18n="loginDashboard">Sign in</span></a>'
        . '</div>';
}

$appointmentBlock = '<!-- HOME_APPOINTMENT_START -->' . $appointmentContent . '<!-- HOME_APPOINTMENT_END -->';
$html = preg_replace('/<!-- HOME_APPOINTMENT_START -->.*?<!-- HOME_APPOINTMENT_END -->/s', $appointmentBlock, $html) ?? $html;

$approvedReviews = db()->query(
    "SELECT r.rating, r.comment, u.name, a.service_type
     FROM reviews r
     JOIN users u ON u.id = r.user_id
     JOIN appointments a ON a.id = r.appointment_id
     WHERE r.status = 'Approved'
     ORDER BY r.reviewed_at DESC, r.created_at DESC
     LIMIT 6"
)->fetchAll();

$reviewCards = '';
foreach ($approvedReviews as $review) {
    $nameParts = preg_split('/\s+/', trim($review['name'])) ?: [];
    $displayName = $nameParts[0] ?? 'Customer';
    if (isset($nameParts[1]) && $nameParts[1] !== '') {
        $displayName .= ' ' . mb_substr($nameParts[1], 0, 1) . '.';
    }
    $stars = str_repeat('<i data-lucide="star" aria-hidden="true"></i>', (int)$review['rating']);
    $reviewCards .= '<article class="review-card">'
        . '<div class="review-stars" aria-label="' . (int)$review['rating'] . ' out of 5 stars">' . $stars . '</div>'
        . '<blockquote>' . e($review['comment']) . '</blockquote>'
        . '<footer><strong>' . e($displayName) . '</strong><span>' . e($review['service_type']) . '</span></footer>'
        . '</article>';
}
if ($reviewCards === '') {
    $reviewCards = '<p class="empty-review-message">Customer reviews will appear here after approval.</p>';
}
$reviewsBlock = '<!-- APPROVED_REVIEWS_START --><div class="review-grid">' . $reviewCards . '</div><!-- APPROVED_REVIEWS_END -->';
$html = preg_replace('/<!-- APPROVED_REVIEWS_START -->.*?<!-- APPROVED_REVIEWS_END -->/s', $reviewsBlock, $html) ?? $html;

if ($user) {
    $accountLink = $user['role'] === 'admin'
        ? '<a class="nav-button primary" href="admin.php"><i data-lucide="layout-dashboard" aria-hidden="true"></i><span>' . e(t('dashboard')) . '</span></a>'
        : '<a class="nav-button primary account-button" href="dashboard.php" aria-label="My account and appointments" title="My account and appointments" data-i18n-aria="accountAppointments"><i data-lucide="circle-user-round" aria-hidden="true"></i></a>';
    $authActions = '<!-- AUTH_ACTIONS_START -->'
        . '<span class="nav-auth" data-auth-actions>'
        . $accountLink
        . logout_form('nav-logout-form')
        . '</span>'
        . '<!-- AUTH_ACTIONS_END -->';
    $html = preg_replace('/<!-- AUTH_ACTIONS_START -->.*?<!-- AUTH_ACTIONS_END -->/s', $authActions, $html) ?? $html;
}

echo $html;

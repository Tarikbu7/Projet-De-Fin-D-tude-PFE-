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
            $priceLabel = t('price_after_diagnosis');
        }
        $translatedName = translate_service($service['name']);
        $serviceOptions .= '<option value="' . e($service['name']) . '">' . e($translatedName . ' - ' . $priceLabel) . '</option>';
    }

    $appointmentContent .=
        '<form action="index.php#appointment" method="post" class="booking-form home-appointment-form">'
        . '<input type="hidden" name="action" value="appointment">'
        . '<input type="hidden" name="csrf_token" value="' . e($csrfToken) . '">'
        . '<label><span>' . e(t('service_type_label')) . '</span>'
        . '<select name="service_type" required><option value="">' . e(t('choose_one')) . '</option>' . $serviceOptions . '</select>'
        . '<small class="service-price-note">' . e(t('hardware_price_note')) . '</small></label>'
        . '<label><span>' . e(t('address_label')) . '</span>'
        . '<input type="text" name="address" value="' . e($user['address'] ?? '') . '" autocomplete="street-address" required></label>'
        . '<label><span>' . e(t('problem_label')) . '</span>'
        . '<textarea name="problem_details" required placeholder="' . e(t('problem_placeholder')) . '"></textarea></label>'
        . '<button class="button primary full" type="submit"><i data-lucide="send" aria-hidden="true"></i>'
        . '<span>' . e(t('send_request')) . '</span></button>'
        . '</form>';
} elseif ($user) {
    $appointmentContent = '';
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

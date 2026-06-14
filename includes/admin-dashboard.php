<?php
declare(strict_types=1);

function admin_stats(PDO $pdo): array {
    return [
        'appointments' => (int)$pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn(),
        'customers' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn(),
        'completed' => (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Completed'")->fetchColumn(),
        'services' => (int)$pdo->query('SELECT COUNT(*) FROM services')->fetchColumn(),
        'reviews' => (int)$pdo->query('SELECT COUNT(*) FROM reviews')->fetchColumn(),
    ];
}

function admin_page_start(string $title, string $activePage, array $user, array $stats): void {
    render_header($title, $user);

    $overviewCount = $stats['appointments'] + $stats['customers'] + $stats['services'] + $stats['reviews'];
    $links = [
        'overview' => ['admin.php', 'Overview', $overviewCount],
        'services' => ['admin-services.php', t('services'), $stats['services']],
        'appointments' => ['admin-appointments.php', t('appointments'), $stats['appointments']],
        'reviews' => ['admin-reviews.php', 'Reviews', $stats['reviews']],
        'customers' => ['admin-customers.php', t('customers'), $stats['customers']],
    ];
    ?>
    <div class="admin-layout">
      <aside class="admin-sidebar" aria-label="Admin dashboard sections">
        <div class="admin-sidebar-title">
          <span class="brand-mark">CR</span>
          <div>
            <strong>Slahpc</strong>
            <small>Repair Desk</small>
          </div>
        </div>
        <nav class="admin-side-nav">
          <?php foreach ($links as $key => [$href, $label, $count]): ?>
            <a href="<?= e($href) ?>"<?= $key === $activePage ? ' class="active" aria-current="page"' : '' ?>>
              <span><?= e((string)$label) ?></span>
              <strong><?= (int)$count ?></strong>
            </a>
          <?php endforeach; ?>
        </nav>
        <div class="admin-sidebar-footer">
          <a href="index.php"><?= e(t('open_site')) ?></a>
          <?= logout_form('sidebar-logout-form') ?>
        </div>
      </aside>

      <div class="admin-content">
        <section class="admin-topbar admin-section">
          <div>
            <small><?= e(date('M d, Y')) ?></small>
            <h1><?= e($title) ?></h1>
          </div>
          <div class="admin-user-chip">
            <span>A</span>
            <strong><?= e($user['name']) ?></strong>
          </div>
        </section>
    <?php

    $message = flash();
    if ($message) {
        $class = str_starts_with($message, 'Error:') ? 'error' : 'success';
        echo '<p class="notice ' . $class . '">' . e($message) . '</p>';
    }
}

function admin_page_end(): void {
    echo '</div></div>';
    render_footer();
}

function admin_handle_service_post(PDO $pdo): void {
    verify_csrf();
    $action = (string)($_POST['action'] ?? '');

    if (in_array($action, ['service_create', 'service_update'], true)) {
        $serviceId = $action === 'service_update'
            ? filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT)
            : null;
        $name = trim((string)($_POST['name'] ?? ''));
        $active = isset($_POST['active']) ? 1 : 0;

        try {
            $basePrice = normalize_price((string)($_POST['base_price'] ?? ''));
        } catch (InvalidArgumentException $exception) {
            flash('Error: ' . $exception->getMessage());
            redirect('admin-services.php');
        }

        if ($name === '' || mb_strlen($name) > 120 || $basePrice === null) {
            flash('Error: Enter a service name and a valid price.');
            redirect('admin-services.php');
        }

        $duplicate = $pdo->prepare(
            'SELECT id FROM services WHERE name = ? AND (? IS NULL OR id <> ?) LIMIT 1'
        );
        $duplicate->execute([$name, $serviceId, $serviceId]);
        if ($duplicate->fetch()) {
            flash('Error: A service with that name already exists.');
            redirect('admin-services.php');
        }

        if ($action === 'service_create') {
            $stmt = $pdo->prepare(
                'INSERT INTO services (name, base_price, active) VALUES (?, ?, ?)'
            );
            $stmt->execute([$name, $basePrice, $active]);
            flash('Service added.');
            redirect('admin-services.php');
        }

        if (!$serviceId) {
            http_response_code(400);
            exit('Invalid service.');
        }

        $stmt = $pdo->prepare('SELECT name FROM services WHERE id = ? LIMIT 1');
        $stmt->execute([$serviceId]);
        $oldName = $stmt->fetchColumn();
        if ($oldName === false) {
            http_response_code(404);
            exit('Service not found.');
        }

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare(
                'UPDATE services SET name = ?, base_price = ?, active = ? WHERE id = ?'
            );
            $stmt->execute([$name, $basePrice, $active, $serviceId]);

            if ($oldName !== $name) {
                $stmt = $pdo->prepare(
                    'UPDATE appointments SET service_type = ? WHERE service_type = ?'
                );
                $stmt->execute([$name, $oldName]);
            }

            $pdo->commit();
        } catch (Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }

        flash('Service updated.');
        redirect('admin-services.php');
    }

    $serviceId = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT);
    if (!$serviceId) {
        http_response_code(400);
        exit('Invalid service.');
    }

    if ($action === 'service_toggle') {
        $stmt = $pdo->prepare('UPDATE services SET active = 1 - active WHERE id = ?');
        $stmt->execute([$serviceId]);
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            exit('Service not found.');
        }

        flash('Service availability updated.');
        redirect('admin-services.php');
    }

    if ($action === 'service_delete') {
        $stmt = $pdo->prepare('SELECT name FROM services WHERE id = ? LIMIT 1');
        $stmt->execute([$serviceId]);
        $serviceName = $stmt->fetchColumn();
        if ($serviceName === false) {
            http_response_code(404);
            exit('Service not found.');
        }

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE service_type = ?');
        $stmt->execute([$serviceName]);
        if ((int)$stmt->fetchColumn() > 0) {
            flash('Error: This service is used by appointments. Disable it instead of deleting it.');
            redirect('admin-services.php');
        }

        $stmt = $pdo->prepare('DELETE FROM services WHERE id = ?');
        $stmt->execute([$serviceId]);
        flash('Service deleted.');
        redirect('admin-services.php');
    }

    http_response_code(400);
    exit('Unsupported service action.');
}

function admin_handle_appointment_post(PDO $pdo): void {
    verify_csrf();

    if (($_POST['action'] ?? '') !== 'status') {
        http_response_code(400);
        exit('Unsupported appointment action.');
    }

    $status = (string)($_POST['status'] ?? '');
    $recordId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!in_array($status, statuses(), true) || !$recordId) {
        http_response_code(400);
        exit('Invalid status update.');
    }

    try {
        $priceValue = normalize_price((string)($_POST['price'] ?? ''));
    } catch (InvalidArgumentException $exception) {
        flash('Error: ' . $exception->getMessage());
        redirect('admin-appointments.php');
    }

    $appointment = $pdo->prepare('SELECT service_type, price FROM appointments WHERE id = ? LIMIT 1');
    $appointment->execute([$recordId]);
    $appointment = $appointment->fetch();
    if (!$appointment) {
        http_response_code(404);
        exit('Appointment not found.');
    }

    $service = $pdo->prepare('SELECT base_price FROM services WHERE name = ? LIMIT 1');
    $service->execute([$appointment['service_type']]);
    $fixedPrice = $service->fetchColumn();
    if ($fixedPrice !== false && (float)$fixedPrice > 0) {
        $priceValue = (float)$fixedPrice;
    }

    $stmt = $pdo->prepare('UPDATE appointments SET status = ?, price = ? WHERE id = ?');
    $stmt->execute([$status, $priceValue, $recordId]);
    flash('Appointment updated.');
    redirect('admin-appointments.php');
}

function admin_handle_review_post(PDO $pdo): void {
    verify_csrf();

    if (($_POST['action'] ?? '') !== 'review_status') {
        http_response_code(400);
        exit('Unsupported review action.');
    }

    $reviewId = filter_input(INPUT_POST, 'review_id', FILTER_VALIDATE_INT);
    $reviewStatus = (string)($_POST['review_status'] ?? '');
    if (!$reviewId || !in_array($reviewStatus, review_statuses(), true)) {
        http_response_code(400);
        exit('Invalid review update.');
    }

    $stmt = $pdo->prepare(
        "UPDATE reviews
         SET status = ?, reviewed_at = CASE WHEN ? = 'Pending' THEN NULL ELSE CURRENT_TIMESTAMP END
         WHERE id = ?"
    );
    $stmt->execute([$reviewStatus, $reviewStatus, $reviewId]);
    flash('Review updated.');
    redirect('admin-reviews.php');
}

function admin_status_form(array $row): void {
    $serviceBasePrice = $row['service_base_price'] ?? null;
    $priceIsEditable = $serviceBasePrice === null || (float)$serviceBasePrice <= 0;
    $priceClass = $priceIsEditable ? 'quote-price-input' : 'fixed-price-input';
    $priceLabel = $priceIsEditable ? 'Price quote' : 'Fixed price';
    $readonly = $priceIsEditable ? '' : ' readonly aria-readonly="true" title="This service has a fixed price"';
    $placeholder = $priceIsEditable ? 'Enter quote' : '';

    echo '<form method="post" class="status-update-form">'
        . csrf_input()
        . '<input type="hidden" name="action" value="status">'
        . '<input type="hidden" name="id" value="' . (int)$row['id'] . '">'
        . '<label><span>' . e(t('status')) . '</span><select name="status">';

    foreach (statuses() as $status) {
        $selected = $status === $row['status'] ? ' selected' : '';
        echo '<option' . $selected . '>' . e($status) . '</option>';
    }

    echo '</select></label>'
        . '<label><span>' . $priceLabel . '</span>'
        . '<input class="' . $priceClass . '" type="number" step="0.01" min="0" name="price" value="'
        . e($row['price'] ?? '') . '" placeholder="' . $placeholder . '"' . $readonly . '></label>'
        . '<button class="button light full" type="submit">' . e(t('save')) . '</button>'
        . '</form>';
}

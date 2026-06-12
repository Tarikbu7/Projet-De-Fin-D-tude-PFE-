<?php
require_once __DIR__ . '/includes/app.php';

// Get the admin and connect to the database.
$user = require_admin();
$pdo = db();

// Create the old repair request table if needed.
$pdo->exec("CREATE TABLE IF NOT EXISTS repair_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(120) NOT NULL,
    family_name VARCHAR(120) NOT NULL,
    phone VARCHAR(60) NOT NULL,
    email VARCHAR(190) NOT NULL,
    problem TEXT NOT NULL,
    price DECIMAL(10,2) NULL,
    status ENUM('Pending','Accepted','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Add missing database parts.
ensure_database_column($pdo, 'appointments', 'price', 'DECIMAL(10,2) NULL AFTER problem_details');
ensure_reviews_table($pdo);

// Save status, price, and review changes.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'status') {
        $table = (string)($_POST['table'] ?? '');
        $status = (string)($_POST['status'] ?? '');
        $recordId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!in_array($table, ['appointments', 'repair_requests'], true)
            || !in_array($status, statuses(), true)
            || !$recordId
        ) {
            http_response_code(400);
            exit('Invalid status update.');
        }

        try {
            $priceValue = normalize_price((string)($_POST['price'] ?? ''));
        } catch (InvalidArgumentException $exception) {
            flash('Error: ' . $exception->getMessage());
            redirect('admin.php');
        }

        if ($table === 'appointments') {
            $appointment = $pdo->prepare('SELECT service_type, price FROM appointments WHERE id = ? LIMIT 1');
            $appointment->execute([$recordId]);
            $appointment = $appointment->fetch();

            if (!$appointment) {
                http_response_code(404);
                exit('Appointment not found.');
            }

            if ($appointment['service_type'] !== 'Hardware repair') {
                $service = $pdo->prepare('SELECT base_price FROM services WHERE active = 1 AND name = ? LIMIT 1');
                $service->execute([$appointment['service_type']]);
                $fixedPrice = $service->fetchColumn();
                $priceValue = $fixedPrice !== false ? (float)$fixedPrice : $appointment['price'];
            }
        }

        $stmt = $pdo->prepare("UPDATE {$table} SET status = ?, price = ? WHERE id = ?");
        $stmt->execute([$status, $priceValue, $recordId]);
    } elseif ($action === 'review_status') {
        $reviewId = (int)($_POST['review_id'] ?? 0);
        $reviewStatus = (string)($_POST['review_status'] ?? '');
        if ($reviewId > 0 && in_array($reviewStatus, review_statuses(), true)) {
            $stmt = $pdo->prepare(
                "UPDATE reviews
                 SET status = ?, reviewed_at = CASE WHEN ? = 'Pending' THEN NULL ELSE CURRENT_TIMESTAMP END
                 WHERE id = ?"
            );
            $stmt->execute([$reviewStatus, $reviewStatus, $reviewId]);
        } else {
            http_response_code(400);
            exit('Invalid review update.');
        }
    } else {
        http_response_code(400);
        exit('Unsupported action.');
    }

    flash('Saved.');
    redirect('admin.php');
}

// Get totals and table data.
$stats = [
    'appointments' => $pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn(),
    'customers' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn(),
    'completed' => (int)$pdo->query("SELECT COUNT(*) FROM repair_requests WHERE status = 'Completed'")->fetchColumn() + (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Completed'")->fetchColumn(),
];
$appointments = $pdo->query('SELECT a.*, u.name, u.email, u.phone FROM appointments a JOIN users u ON u.id = a.user_id ORDER BY a.created_at DESC')->fetchAll();
$customers = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC")->fetchAll();
$repairRequests = $pdo->query('SELECT * FROM repair_requests ORDER BY created_at DESC')->fetchAll();
$reviews = $pdo->query('
    SELECT r.*, u.name, u.email, a.service_type
    FROM reviews r
    JOIN users u ON u.id = r.user_id
    JOIN appointments a ON a.id = r.appointment_id
    ORDER BY r.created_at DESC
')->fetchAll();

// Get the form security code.
$csrfToken = csrf_token();

// Show the status and price form.
function status_form(string $table, array $row): void {
    $priceIsEditable = $table === 'repair_requests' || ($row['service_type'] ?? '') === 'Hardware repair';
    $priceClass = $priceIsEditable ? 'quote-price-input' : 'fixed-price-input';
    $priceLabel = $priceIsEditable ? 'Price quote' : 'Fixed price';
    $readonly = $priceIsEditable ? '' : ' readonly aria-readonly="true" title="This service has a fixed price"';
    $placeholder = $priceIsEditable ? 'Enter quote' : '';
    echo '<form method="post" class="status-update-form">'
        . csrf_input()
        . '<input type="hidden" name="action" value="status">'
        . '<input type="hidden" name="table" value="' . e($table) . '">'
        . '<input type="hidden" name="id" value="' . (int)$row['id'] . '">'
        . '<label><span>' . e(t('status')) . '</span><select name="status">';
    foreach (statuses() as $status) {
        $selected = $status === $row['status'] ? ' selected' : '';
        echo '<option' . $selected . '>' . e($status) . '</option>';
    }
    echo '</select></label><label><span>' . $priceLabel . '</span><input class="' . $priceClass . '" type="number" step="0.01" min="0" name="price" value="' . e($row['price'] ?? '') . '" placeholder="' . $placeholder . '"' . $readonly . '></label><button class="button light full" type="submit">' . e(t('save')) . '</button></form>';
}

// Show the admin page.
render_header(t('admin_dashboard'), $user);
$flash = flash();
?>
<div class="admin-layout">
  <!-- Admin side menu -->
  <aside class="admin-sidebar" aria-label="Admin dashboard sections">
    <div class="admin-sidebar-title">
      <span class="brand-mark">CR</span>
      <div>
        <strong>Slahpc</strong>
        <small>Repair Desk</small>
      </div>
    </div>
    <nav class="admin-side-nav">
      <a href="#overview"><span>Overview</span><strong><?= (int)array_sum(array_map('intval', $stats)) ?></strong></a>
      <a href="#appointments"><span><?= e(t('appointments')) ?></span><strong><?= (int)$stats['appointments'] ?></strong></a>
      <a href="#customers"><span><?= e(t('customers')) ?></span><strong><?= (int)$stats['customers'] ?></strong></a>
    </nav>
    <div class="admin-sidebar-footer">
      <a href="index.php"><?= e(t('open_site')) ?></a>
      <?= logout_form('sidebar-logout-form') ?>
    </div>
  </aside>

  <div class="admin-content">
    <!-- Admin welcome area -->
    <section class="admin-topbar admin-section" id="overview">
      <div>
        <small><?= e(date('M d, Y')) ?></small>
        <h1><?= e(t('welcome')) ?>, Admin</h1>
      </div>
      <div class="admin-user-chip">
        <span>A</span>
        <strong>Admin</strong>
      </div>
    </section>
    <?php if ($flash): ?>
      <p class="notice <?= str_starts_with($flash, 'Error:') ? 'error' : 'success' ?>"><?= e($flash) ?></p>
    <?php endif; ?>

    <!-- Main totals -->
    <section class="admin-summary-grid">
      <article class="admin-summary-card blue"><span><?= e(t('appointments')) ?></span><strong><?= (int)$stats['appointments'] ?></strong><small>Client dashboard requests</small></article>
      <article class="admin-summary-card violet"><span><?= e(t('customers')) ?></span><strong><?= (int)$stats['customers'] ?></strong><small>Registered clients</small></article>
      <article class="admin-summary-card pink"><span>Completed</span><strong><?= (int)$stats['completed'] ?></strong><small>Finished repair jobs</small></article>
    </section>

    <!-- Old repair requests -->
    <section class="dashboard-card admin-section" id="repair-requests">
      <h2><?= e(t('repair_requests')) ?></h2>
      <div class="table-scroll">
        <table><thead><tr><th>#</th><th><?= e(t('customer')) ?></th><th><?= e(t('phone')) ?></th><th><?= e(t('details')) ?></th><th>Price</th><th><?= e(t('status')) ?></th></tr></thead><tbody>
        <?php foreach ($repairRequests as $row): ?><tr><td><?= (int)$row['id'] ?></td><td><?= e($row['first_name'] . ' ' . $row['family_name']) ?><br><small><?= e($row['email']) ?></small><br><small><?= e($row['created_at']) ?></small></td><td><?= e($row['phone']) ?></td><td><?= e($row['problem']) ?></td><td><?php if ($row['price'] !== null && $row['price'] !== ''): ?><strong><?= e($row['price']) ?> MAD</strong><?php endif; ?></td><td><?php status_form('repair_requests', $row); ?></td></tr><?php endforeach; table_empty(count($repairRequests), 6); ?>
        </tbody></table>
      </div>
    </section>

    <!-- Customer repairs -->
    <section class="dashboard-card admin-section admin-appointments-card" id="appointments">
      <h2><?= e(t('appointments')) ?></h2>
      <div class="table-scroll">
        <table class="admin-appointments-table"><thead><tr><th>#</th><th><?= e(t('customer')) ?></th><th><?= e(t('services')) ?></th><th><?= e(t('address')) ?></th><th>Price</th><th><?= e(t('status')) ?></th></tr></thead><tbody>
        <?php foreach ($appointments as $row): ?><tr><td><?= (int)$row['id'] ?></td><td><?= e($row['name']) ?><small><?= e($row['email']) ?></small><small><?= e($row['phone']) ?></small></td><td><?= e($row['service_type']) ?><small class="problem-text"><?= e($row['problem_details']) ?></small></td><td><?= e($row['address']) ?></td><td><?php if ($row['price'] !== null && $row['price'] !== ''): ?><strong><?= e($row['price']) ?> MAD</strong><?php endif; ?></td><td><?php status_form('appointments', $row); ?></td></tr><?php endforeach; table_empty(count($appointments), 6); ?>
        </tbody></table>
      </div>
    </section>

    <!-- Check customer reviews -->
    <section class="dashboard-card admin-section admin-reviews-card" id="reviews">
      <h2>Customer reviews</h2>
      <div class="table-scroll">
        <table class="admin-reviews-table">
          <thead><tr><th>Customer</th><th>Service</th><th>Rating</th><th>Review</th><th>Submitted</th><th>Decision</th></tr></thead>
          <tbody>
          <?php foreach ($reviews as $review): ?>
            <tr>
              <td><?= e($review['name']) ?><small><?= e($review['email']) ?></small></td>
              <td><?= e($review['service_type']) ?></td>
              <td><span class="admin-review-stars" aria-label="<?= (int)$review['rating'] ?> out of 5 stars"><?= str_repeat('★', (int)$review['rating']) ?></span></td>
              <td class="admin-review-comment"><?= e($review['comment']) ?></td>
              <td><?= e(date('M d, Y', strtotime($review['created_at']))) ?></td>
              <td>
                <form method="post" class="review-status-form">
                  <input type="hidden" name="action" value="review_status">
                  <input type="hidden" name="review_id" value="<?= (int)$review['id'] ?>">
                  <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                  <select name="review_status" aria-label="Review status">
                    <?php foreach (['Pending', 'Approved', 'Rejected'] as $reviewStatus): ?>
                      <option<?= $reviewStatus === $review['status'] ? ' selected' : '' ?>><?= e($reviewStatus) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button class="button light full" type="submit">Save</button>
                </form>
              </td>
            </tr>
          <?php endforeach; table_empty(count($reviews), 6); ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Customer list -->
    <section class="dashboard-card admin-section" id="customers">
      <h2><?= e(t('customers')) ?></h2>
      <div class="table-scroll">
        <table><thead><tr><th><?= e(t('name')) ?></th><th><?= e(t('email')) ?></th><th><?= e(t('phone')) ?></th><th><?= e(t('city')) ?></th></tr></thead><tbody>
        <?php foreach ($customers as $row): ?><tr><td><?= e($row['name']) ?></td><td><?= e($row['email']) ?></td><td><?= e($row['phone']) ?></td><td><?= e($row['address']) ?></td></tr><?php endforeach; table_empty(count($customers), 4); ?>
        </tbody></table>
      </div>
    </section>
  </div>
</div>
<?php render_footer(); ?>

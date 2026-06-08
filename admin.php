<?php
require_once __DIR__ . '/includes/app.php';
$user = require_admin();
$pdo = db();

function ensure_column(PDO $pdo, string $table, string $column, string $definition): void {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([$table, $column]);
    if ((int)$stmt->fetchColumn() === 0) {
        $pdo->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    }
}

ensure_column($pdo, 'appointments', 'price', 'DECIMAL(10,2) NULL AFTER problem_details');

function sync_status_values(PDO $pdo, string $table): void {
    $pdo->exec("ALTER TABLE {$table} MODIFY status ENUM('Pending','Confirmed','Accepted','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending'");
    $pdo->exec("UPDATE {$table} SET status = 'Accepted' WHERE status = 'Confirmed'");
    $pdo->exec("ALTER TABLE {$table} MODIFY status ENUM('Pending','Accepted','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending'");
}

sync_status_values($pdo, 'appointments');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'status') {
        $newStatus = (string)($_POST['status'] ?? '');
        if (!in_array($newStatus, statuses(), true)) {
            flash('Invalid appointment status.');
            redirect('admin.php');
        }
        $price = trim($_POST['price'] ?? '');
        $priceValue = $price === '' ? null : (float)$price;
        $appointment = $pdo->prepare('SELECT service_type, price FROM appointments WHERE id = ? LIMIT 1');
        $appointment->execute([(int)$_POST['id']]);
        $appointment = $appointment->fetch();

        if ($appointment) {
            if ($appointment['service_type'] !== 'Hardware repair') {
                $service = $pdo->prepare('SELECT base_price FROM services WHERE active = 1 AND name = ? LIMIT 1');
                $service->execute([$appointment['service_type']]);
                $fixedPrice = $service->fetchColumn();
                $priceValue = $fixedPrice !== false ? (float)$fixedPrice : $appointment['price'];
            }

            $stmt = $pdo->prepare('UPDATE appointments SET status = ?, price = ? WHERE id = ?');
            $stmt->execute([$newStatus, $priceValue, (int)$_POST['id']]);
        }
    } elseif ($action === 'review_status') {
        $reviewStatus = (string)($_POST['review_status'] ?? '');
        if (in_array($reviewStatus, ['Approved', 'Rejected'], true)) {
            $stmt = $pdo->prepare('UPDATE reviews SET status = ?, reviewed_at = CURRENT_TIMESTAMP WHERE id = ?');
            $stmt->execute([$reviewStatus, (int)($_POST['id'] ?? 0)]);
        }
    }
    flash('Saved.');
    redirect('admin.php');
}

$stats = [
    'appointments' => $pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn(),
    'customers' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn(),
    'completed' => $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Completed'")->fetchColumn(),
    'reviews' => $pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'Pending'")->fetchColumn(),
];
$appointments = $pdo->query('SELECT a.*, u.name, u.email, u.phone FROM appointments a JOIN users u ON u.id = a.user_id ORDER BY a.created_at DESC')->fetchAll();
$customers = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC")->fetchAll();
$reviews = $pdo->query(
    'SELECT r.*, u.name, u.email, a.service_type
     FROM reviews r
     JOIN users u ON u.id = r.user_id
     JOIN appointments a ON a.id = r.appointment_id
     ORDER BY FIELD(r.status, "Pending", "Approved", "Rejected"), r.created_at DESC'
)->fetchAll();

function status_form(array $row): void {
    $priceIsEditable = ($row['service_type'] ?? '') === 'Hardware repair';
    $priceClass = $priceIsEditable ? 'quote-price-input' : 'fixed-price-input';
    $priceLabel = $priceIsEditable ? 'Price quote' : 'Fixed price';
    $readonly = $priceIsEditable ? '' : ' readonly aria-readonly="true" title="This service has a fixed price"';
    $placeholder = $priceIsEditable ? 'Enter quote' : '';
    echo '<form method="post" class="status-update-form">' . csrf_input() . '<input type="hidden" name="action" value="status"><input type="hidden" name="id" value="' . (int)$row['id'] . '"><label><span>' . e(t('status')) . '</span><select name="status">';
    foreach (statuses() as $status) {
        $selected = $status === $row['status'] ? ' selected' : '';
        echo '<option' . $selected . '>' . e($status) . '</option>';
    }
    echo '</select></label><label><span>' . $priceLabel . '</span><input class="' . $priceClass . '" type="number" step="0.01" min="0" name="price" value="' . e($row['price'] ?? '') . '" placeholder="' . $placeholder . '"' . $readonly . '></label><button class="button light full" type="submit">' . e(t('save')) . '</button></form>';
}

render_header(t('admin_dashboard'), $user);
$flash = flash();
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
      <a href="#overview"><span>Overview</span><strong><?= (int)array_sum(array_map('intval', $stats)) ?></strong></a>
      <a href="#appointments"><span><?= e(t('appointments')) ?></span><strong><?= (int)$stats['appointments'] ?></strong></a>
      <a href="#reviews"><span>Reviews</span><strong><?= (int)$stats['reviews'] ?></strong></a>
      <a href="#customers"><span><?= e(t('customers')) ?></span><strong><?= (int)$stats['customers'] ?></strong></a>
    </nav>
    <div class="admin-sidebar-footer">
      <a href="index.php"><?= e(t('open_site')) ?></a>
      <?= logout_form('sidebar-logout-form') ?>
    </div>
  </aside>

  <div class="admin-content">
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
    <?php if ($flash): ?><p class="notice success"><?= e($flash) ?></p><?php endif; ?>

    <section class="admin-summary-grid">
      <article class="admin-summary-card blue"><span><?= e(t('appointments')) ?></span><strong><?= (int)$stats['appointments'] ?></strong><small>Client dashboard requests</small></article>
      <article class="admin-summary-card violet"><span><?= e(t('customers')) ?></span><strong><?= (int)$stats['customers'] ?></strong><small>Registered clients</small></article>
      <article class="admin-summary-card pink"><span>Completed</span><strong><?= (int)$stats['completed'] ?></strong><small>Finished repair jobs</small></article>
      <article class="admin-summary-card cyan"><span>Pending reviews</span><strong><?= (int)$stats['reviews'] ?></strong><small>Waiting for moderation</small></article>
    </section>

    <section class="dashboard-card admin-section admin-appointments-card" id="appointments">
      <h2><?= e(t('appointments')) ?></h2>
      <div class="table-scroll">
        <table class="admin-appointments-table"><thead><tr><th>#</th><th><?= e(t('customer')) ?></th><th><?= e(t('services')) ?></th><th><?= e(t('address')) ?></th><th>Price</th><th><?= e(t('status')) ?></th></tr></thead><tbody>
        <?php foreach ($appointments as $row): ?><tr><td><?= (int)$row['id'] ?></td><td><?= e($row['name']) ?><small><?= e($row['email']) ?></small><small><?= e($row['phone']) ?></small></td><td><?= e($row['service_type']) ?><small class="problem-text"><?= e($row['problem_details']) ?></small></td><td><?= e($row['address']) ?></td><td><?php if ($row['price'] !== null && $row['price'] !== ''): ?><strong><?= e($row['price']) ?> MAD</strong><?php endif; ?></td><td><?php status_form($row); ?></td></tr><?php endforeach; table_empty(count($appointments), 6); ?>
        </tbody></table>
      </div>
    </section>

    <section class="dashboard-card admin-section" id="reviews">
      <h2>Customer reviews</h2>
      <div class="table-scroll">
        <table><thead><tr><th>Customer</th><th>Service</th><th>Rating</th><th>Comment</th><th>Status</th><th>Moderation</th></tr></thead><tbody>
        <?php foreach ($reviews as $row): ?>
          <tr>
            <td><?= e($row['name']) ?><br><small><?= e($row['email']) ?></small></td>
            <td><?= e($row['service_type']) ?><br><small>Appointment #<?= (int)$row['appointment_id'] ?></small></td>
            <td><strong><?= str_repeat('★', (int)$row['rating']) ?></strong></td>
            <td><?= e($row['comment']) ?></td>
            <td><span class="review-state <?= strtolower(e($row['status'])) ?>"><?= e($row['status']) ?></span></td>
            <td>
              <form method="post" class="review-moderation-form">
                <?= csrf_input() ?>
                <input type="hidden" name="action" value="review_status">
                <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                <label>
                  <span>Decision</span>
                  <select name="review_status" required>
                    <option value="Approved" <?= $row['status'] === 'Approved' ? 'selected' : '' ?>>Approve</option>
                    <option value="Rejected" <?= $row['status'] === 'Rejected' ? 'selected' : '' ?>>Reject</option>
                  </select>
                </label>
                <button class="button light" type="submit">Save</button>
              </form>
            </td>
          </tr>
        <?php endforeach; table_empty(count($reviews), 6); ?>
        </tbody></table>
      </div>
    </section>

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

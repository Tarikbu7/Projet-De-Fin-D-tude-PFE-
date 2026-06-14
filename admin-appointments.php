<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/admin-dashboard.php';

$user = require_admin();
$pdo = db();
ensure_database_column($pdo, 'appointments', 'price', 'DECIMAL(10,2) NULL AFTER problem_details');
ensure_reviews_table($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_handle_appointment_post($pdo);
}

$stats = admin_stats($pdo);
$appointments = $pdo->query('
    SELECT a.*, u.name, u.email, u.phone, s.base_price AS service_base_price
    FROM appointments a
    JOIN users u ON u.id = a.user_id
    LEFT JOIN services s ON s.name = a.service_type
    ORDER BY a.created_at DESC
')->fetchAll();

admin_page_start('Appointment management', 'appointments', $user, $stats);
?>
<section class="dashboard-card admin-appointments-card">
  <div class="admin-card-heading">
    <div>
      <h2><?= e(t('appointments')) ?></h2>
      <p>Review customer requests, set prices, and update repair progress.</p>
    </div>
  </div>
  <div class="table-scroll">
    <table class="admin-appointments-table">
      <thead><tr><th>#</th><th><?= e(t('customer')) ?></th><th><?= e(t('services')) ?></th><th><?= e(t('address')) ?></th><th>Price</th><th><?= e(t('status')) ?></th></tr></thead>
      <tbody>
      <?php foreach ($appointments as $row): ?>
        <tr>
          <td><?= (int)$row['id'] ?></td>
          <td><?= e($row['name']) ?><small><?= e($row['email']) ?></small><small><?= e($row['phone']) ?></small></td>
          <td><?= e($row['service_type']) ?><small class="problem-text"><?= e($row['problem_details']) ?></small></td>
          <td><?= e($row['address']) ?></td>
          <td><?php if ($row['price'] !== null && $row['price'] !== ''): ?><strong><?= e($row['price']) ?> MAD</strong><?php endif; ?></td>
          <td><?php admin_status_form($row); ?></td>
        </tr>
      <?php endforeach; table_empty(count($appointments), 6); ?>
      </tbody>
    </table>
  </div>
</section>
<?php admin_page_end(); ?>

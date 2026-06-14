<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/admin-dashboard.php';

$user = require_admin();
$pdo = db();
ensure_reviews_table($pdo);

$stats = admin_stats($pdo);
$customers = $pdo->query("
    SELECT u.*,
           (SELECT COUNT(*) FROM appointments a WHERE a.user_id = u.id) AS appointment_count,
           (SELECT COUNT(*) FROM reviews r WHERE r.user_id = u.id) AS review_count
    FROM users u
    WHERE u.role = 'user'
    ORDER BY u.created_at DESC
")->fetchAll();

admin_page_start('Customer management', 'customers', $user, $stats);
?>
<section class="dashboard-card admin-customers-card">
  <div class="admin-card-heading">
    <div>
      <h2><?= e(t('customers')) ?></h2>
      <p>View customer contact details and account activity.</p>
    </div>
  </div>
  <div class="table-scroll">
    <table>
      <thead><tr><th><?= e(t('name')) ?></th><th><?= e(t('email')) ?></th><th><?= e(t('phone')) ?></th><th><?= e(t('city')) ?></th><th>Appointments</th><th>Reviews</th><th>Joined</th></tr></thead>
      <tbody>
      <?php foreach ($customers as $row): ?>
        <tr>
          <td><?= e($row['name']) ?></td>
          <td><?= e($row['email']) ?></td>
          <td><?= e($row['phone']) ?></td>
          <td><?= e($row['address']) ?></td>
          <td><?= (int)$row['appointment_count'] ?></td>
          <td><?= (int)$row['review_count'] ?></td>
          <td><?= e(date('M d, Y', strtotime($row['created_at']))) ?></td>
        </tr>
      <?php endforeach; table_empty(count($customers), 7); ?>
      </tbody>
    </table>
  </div>
</section>
<?php admin_page_end(); ?>

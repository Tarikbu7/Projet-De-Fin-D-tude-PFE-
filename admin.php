<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/admin-dashboard.php';

$user = require_admin();
$pdo = db();
ensure_database_column($pdo, 'appointments', 'price', 'DECIMAL(10,2) NULL AFTER problem_details');
ensure_reviews_table($pdo);

$stats = admin_stats($pdo);
admin_page_start(t('admin_dashboard'), 'overview', $user, $stats);
?>
<section class="admin-summary-grid">
  <a class="admin-summary-card blue" href="admin-appointments.php">
    <span><?= e(t('appointments')) ?></span>
    <strong><?= $stats['appointments'] ?></strong>
    <small>Manage repair requests</small>
  </a>
  <a class="admin-summary-card violet" href="admin-customers.php">
    <span><?= e(t('customers')) ?></span>
    <strong><?= $stats['customers'] ?></strong>
    <small>View registered clients</small>
  </a>
  <a class="admin-summary-card pink" href="admin-appointments.php">
    <span>Completed</span>
    <strong><?= $stats['completed'] ?></strong>
    <small>Finished repair jobs</small>
  </a>
  <a class="admin-summary-card cyan" href="admin-services.php">
    <span><?= e(t('services')) ?></span>
    <strong><?= $stats['services'] ?></strong>
    <small>Manage prices and availability</small>
  </a>
</section>

<section class="dashboard-card admin-overview-card">
  <div class="admin-card-heading">
    <div>
      <h2>Dashboard pages</h2>
      <p>Each administration feature now has its own focused page.</p>
    </div>
  </div>
  <div class="admin-page-grid">
    <a href="admin-services.php"><strong>Services</strong><span>Add, edit, disable, and delete services.</span></a>
    <a href="admin-appointments.php"><strong>Appointments</strong><span>Update repair status and pricing.</span></a>
    <a href="admin-reviews.php"><strong>Reviews</strong><span>Approve or reject customer feedback.</span></a>
    <a href="admin-customers.php"><strong>Customers</strong><span>View registered customer information.</span></a>
  </div>
</section>
<?php admin_page_end(); ?>

<?php
require_once __DIR__ . '/includes/app.php';
$user = require_login();
if ($user['role'] === 'admin') {
    redirect('admin.php');
}

$pdo = db();

function ensure_column(PDO $pdo, string $table, string $column, string $definition): void {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([$table, $column]);
    if ((int)$stmt->fetchColumn() === 0) {
        $pdo->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    }
}

ensure_column($pdo, 'appointments', 'price', 'DECIMAL(10,2) NULL AFTER problem_details');

$appointments = $pdo->prepare('SELECT * FROM appointments WHERE user_id = ? ORDER BY created_at DESC');
$appointments->execute([$user['id']]);
$appointments = $appointments->fetchAll();

render_header(t('user_dashboard'), $user);
?>
<section class="dashboard-hero customer-dashboard-hero">
  <div>
    <h1><?= e(t('welcome')) ?>, <?= e($user['name']) ?></h1>
    <p class="hero-copy">Track your repair appointment requests and their current status here.</p>
  </div>
</section>

<section class="dashboard-card customer-appointments-card">
  <div class="customer-card-heading">
    <h2><?= e(t('appointments')) ?></h2>
    <span class="appointment-count" aria-label="<?= count($appointments) ?> <?= e(t('appointments')) ?>"><?= count($appointments) ?></span>
  </div>
  <div class="customer-table-scroll">
    <table class="customer-appointments-table"><thead><tr><th><?= e(t('services')) ?></th><th><?= e(t('details')) ?></th><th>Price</th><th><?= e(t('status')) ?></th></tr></thead><tbody>
    <?php foreach ($appointments as $row): ?><tr><td><?= e($row['service_type']) ?></td><td><?= e($row['problem_details']) ?></td><td><?php if (($row['price'] ?? null) !== null && $row['price'] !== ''): ?><strong><?= e($row['price']) ?> MAD</strong><?php else: ?><span class="muted-value"><?= e(t('awaiting_quote')) ?></span><?php endif; ?></td><td><span class="status"><?= e(status_label($row['status'])) ?></span></td></tr><?php endforeach; table_empty(count($appointments), 4); ?>
    </tbody></table>
  </div>
</section>
<?php render_footer(); ?>

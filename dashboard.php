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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'appointment') {
        $stmt = $pdo->prepare('INSERT INTO appointments (user_id, service_type, preferred_date, preferred_time, address, problem_details) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $user['id'],
            $_POST['service_type'],
            $_POST['preferred_date'],
            $_POST['preferred_time'],
            $_POST['address'],
            $_POST['problem_details'],
        ]);
        flash('Repair appointment request sent.');
    }
    redirect('dashboard.php');
}

$services = $pdo->query('SELECT * FROM services WHERE active = 1 ORDER BY name')->fetchAll();
$appointments = $pdo->prepare('SELECT * FROM appointments WHERE user_id = ? ORDER BY created_at DESC');
$appointments->execute([$user['id']]);
$appointments = $appointments->fetchAll();

render_header(t('user_dashboard'), $user);
$flash = flash();
?>
<section class="dashboard-hero">
  <div>
    <p class="eyebrow"><?= e(t('user_dashboard')) ?></p>
    <h1><?= e(t('welcome')) ?>, <?= e($user['name']) ?></h1>
    <p class="hero-copy">Use this dashboard only for repair requests. Send your problem and address, then wait for Slahpc to confirm when the repair can be done.</p>
  </div>
</section>
<?php if ($flash): ?><p class="notice success"><?= e($flash) ?></p><?php endif; ?>

<section class="dashboard-card">
  <h2><?= e(t('request_appointment')) ?></h2>
  <form method="post" class="stack-form">
    <input type="hidden" name="action" value="appointment">
    <input type="hidden" name="preferred_date" value="<?= e(date('Y-m-d')) ?>">
    <input type="hidden" name="preferred_time" value="Repairer will confirm">
    <label><?= e(t('services')) ?><select name="service_type" required><?php foreach ($services as $service): ?><option><?= e($service['name']) ?></option><?php endforeach; ?></select></label>
    <label><?= e(t('address')) ?><input type="text" name="address" value="<?= e($user['address']) ?>" required></label>
    <label><?= e(t('details')) ?><textarea name="problem_details" required placeholder="Describe the computer problem"></textarea></label>
    <button class="button primary full" type="submit"><?= e(t('submit')) ?></button>
  </form>
</section>

<section class="dashboard-card">
  <h2><?= e(t('appointments')) ?></h2>
  <table><thead><tr><th>#</th><th><?= e(t('services')) ?></th><th><?= e(t('details')) ?></th><th>Price</th><th><?= e(t('status')) ?></th></tr></thead><tbody>
  <?php foreach ($appointments as $row): ?><tr><td><?= (int)$row['id'] ?></td><td><?= e($row['service_type']) ?></td><td><?= e($row['problem_details']) ?></td><td><?php if (($row['price'] ?? null) !== null && $row['price'] !== ''): ?><strong><?= e($row['price']) ?> MAD</strong><?php endif; ?></td><td><span class="status"><?= e(status_label($row['status'])) ?></span></td></tr><?php endforeach; table_empty(count($appointments), 5); ?>
  </tbody></table>
</section>
<?php render_footer(); ?>

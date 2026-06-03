<?php
require_once __DIR__ . '/includes/app.php';
$user = require_admin();
$pdo = db();
$pdo->exec("CREATE TABLE IF NOT EXISTS repair_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(120) NOT NULL,
    family_name VARCHAR(120) NOT NULL,
    phone VARCHAR(60) NOT NULL,
    email VARCHAR(190) NOT NULL,
    problem TEXT NOT NULL,
    status ENUM('Pending','Confirmed','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'status') {
        $table = $_POST['table'];
        if (in_array($table, ['appointments', 'orders', 'pc_build_requests', 'messages', 'repair_requests', 'invoices'], true)) {
            $stmt = $pdo->prepare("UPDATE {$table} SET status = ? WHERE id = ?");
            $stmt->execute([$_POST['status'], (int)$_POST['id']]);
        }
    }
    if ($action === 'service') {
        $stmt = $pdo->prepare('UPDATE services SET base_price = ?, active = ? WHERE id = ?');
        $stmt->execute([(float)$_POST['base_price'], isset($_POST['active']) ? 1 : 0, (int)$_POST['id']]);
    }
    if ($action === 'product') {
        $stmt = $pdo->prepare('UPDATE products SET price = ?, stock = ?, active = ? WHERE id = ?');
        $stmt->execute([(float)$_POST['price'], (int)$_POST['stock'], isset($_POST['active']) ? 1 : 0, (int)$_POST['id']]);
    }
    if ($action === 'invoice') {
        $stmt = $pdo->prepare('INSERT INTO invoices (user_id, amount, note) VALUES (?, ?, ?)');
        $stmt->execute([(int)$_POST['user_id'], (float)$_POST['amount'], $_POST['note'] ?? '']);
    }
    flash('Saved.');
    redirect('admin.php');
}

$stats = [
    'customers' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn(),
    'appointments' => $pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn(),
    'orders' => $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'messages' => $pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn(),
    'repair_requests' => $pdo->query('SELECT COUNT(*) FROM repair_requests')->fetchColumn(),
];
$customers = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC")->fetchAll();
$appointments = $pdo->query('SELECT a.*, u.name, u.email FROM appointments a JOIN users u ON u.id = a.user_id ORDER BY a.created_at DESC')->fetchAll();
$orders = $pdo->query('SELECT o.*, u.name, u.email FROM orders o JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC')->fetchAll();
$builds = $pdo->query('SELECT b.*, u.name, u.email FROM pc_build_requests b JOIN users u ON u.id = b.user_id ORDER BY b.created_at DESC')->fetchAll();
$messages = $pdo->query('SELECT m.*, u.name, u.email FROM messages m JOIN users u ON u.id = m.user_id ORDER BY m.created_at DESC')->fetchAll();
$repairRequests = $pdo->query('SELECT * FROM repair_requests ORDER BY created_at DESC')->fetchAll();
$services = $pdo->query('SELECT * FROM services ORDER BY id')->fetchAll();
$products = $pdo->query('SELECT * FROM products ORDER BY category, name')->fetchAll();
$invoices = $pdo->query('SELECT i.*, u.name, u.email FROM invoices i JOIN users u ON u.id = i.user_id ORDER BY i.created_at DESC')->fetchAll();

function status_form(string $table, array $row): void {
    echo '<form method="post" class="inline-form"><input type="hidden" name="action" value="status"><input type="hidden" name="table" value="' . e($table) . '"><input type="hidden" name="id" value="' . (int)$row['id'] . '"><select name="status">';
    foreach (statuses() as $status) {
        $selected = $status === $row['status'] ? ' selected' : '';
        echo '<option' . $selected . '>' . e($status) . '</option>';
    }
    echo '</select><button class="button light" type="submit">' . e(t('save')) . '</button></form>';
}

render_header(t('admin_dashboard'), $user);
$flash = flash();
?>
<section class="dashboard-hero">
  <div>
    <p class="eyebrow"><?= e(t('admin_dashboard')) ?></p>
    <h1><?= e(t('welcome')) ?>, Admin</h1>
    <p class="hero-copy">Manage appointments, customers, messages, prices, products, repair status, invoices, and stats.</p>
  </div>
</section>
<?php if ($flash): ?><p class="notice success"><?= e($flash) ?></p><?php endif; ?>

<section class="stats-grid">
  <?php foreach ($stats as $label => $value): ?><article class="stat-card"><span><?= e(t($label)) ?></span><strong><?= (int)$value ?></strong></article><?php endforeach; ?>
</section>

<section class="dashboard-card">
  <h2><?= e(t('appointments')) ?></h2>
  <table><thead><tr><th>#</th><th><?= e(t('customer')) ?></th><th><?= e(t('services')) ?></th><th><?= e(t('date')) ?></th><th><?= e(t('status')) ?></th></tr></thead><tbody>
  <?php foreach ($appointments as $row): ?><tr><td><?= (int)$row['id'] ?></td><td><?= e($row['name']) ?><br><small><?= e($row['email']) ?></small></td><td><?= e($row['service_type']) ?><br><small><?= e($row['problem_details']) ?></small></td><td><?= e($row['preferred_date']) ?> <?= e($row['preferred_time']) ?></td><td><?php status_form('appointments', $row); ?></td></tr><?php endforeach; table_empty(count($appointments), 5); ?>
  </tbody></table>
</section>

<section class="dashboard-card">
  <h2>Repair requests</h2>
  <table><thead><tr><th>#</th><th>Customer</th><th>Contact</th><th>Problem</th><th><?= e(t('status')) ?></th></tr></thead><tbody>
  <?php foreach ($repairRequests as $row): ?><tr><td><?= (int)$row['id'] ?></td><td><?= e($row['first_name'] . ' ' . $row['family_name']) ?><br><small><?= e($row['created_at']) ?></small></td><td><?= e($row['phone']) ?><br><small><?= e($row['email']) ?></small></td><td><?= e($row['problem']) ?></td><td><?php status_form('repair_requests', $row); ?></td></tr><?php endforeach; table_empty(count($repairRequests), 5); ?>
  </tbody></table>
</section>

<section class="dashboard-grid">
  <article class="dashboard-card"><h2><?= e(t('customers')) ?></h2><table><tbody><?php foreach ($customers as $row): ?><tr><td><?= e($row['name']) ?></td><td><?= e($row['email']) ?></td><td><?= e($row['phone']) ?></td></tr><?php endforeach; table_empty(count($customers), 3); ?></tbody></table></article>
  <article class="dashboard-card"><h2><?= e(t('orders')) ?></h2><table><tbody><?php foreach ($orders as $row): ?><tr><td>#<?= (int)$row['id'] ?></td><td><?= e($row['name']) ?></td><td>$<?= e($row['total']) ?></td><td><?php status_form('orders', $row); ?></td></tr><?php endforeach; table_empty(count($orders), 4); ?></tbody></table></article>
</section>

<section class="dashboard-grid">
  <article class="dashboard-card"><h2><?= e(t('pc_build')) ?></h2><table><tbody><?php foreach ($builds as $row): ?><tr><td><?= e($row['name']) ?></td><td><?= e($row['budget']) ?> / <?= e($row['purpose']) ?><br><small><?= e($row['details']) ?></small></td><td><?php status_form('pc_build_requests', $row); ?></td></tr><?php endforeach; table_empty(count($builds), 3); ?></tbody></table></article>
  <article class="dashboard-card"><h2><?= e(t('messages')) ?></h2><table><tbody><?php foreach ($messages as $row): ?><tr><td><?= e($row['name']) ?></td><td><?= e($row['subject']) ?><br><small><?= e($row['body']) ?></small></td><td><?php status_form('messages', $row); ?></td></tr><?php endforeach; table_empty(count($messages), 3); ?></tbody></table></article>
</section>

<section class="dashboard-grid">
  <article class="dashboard-card"><h2><?= e(t('services')) ?></h2><?php foreach ($services as $row): ?><form method="post" class="inline-form"><input type="hidden" name="action" value="service"><input type="hidden" name="id" value="<?= (int)$row['id'] ?>"><span><?= e($row['name']) ?></span><input type="number" step="0.01" name="base_price" value="<?= e($row['base_price']) ?>"><label class="mini-check"><input type="checkbox" name="active" <?= $row['active'] ? 'checked' : '' ?>> active</label><button class="button light" type="submit"><?= e(t('save')) ?></button></form><?php endforeach; ?></article>
  <article class="dashboard-card"><h2><?= e(t('products')) ?></h2><?php foreach ($products as $row): ?><form method="post" class="inline-form"><input type="hidden" name="action" value="product"><input type="hidden" name="id" value="<?= (int)$row['id'] ?>"><span><?= e($row['category']) ?> - <?= e($row['name']) ?></span><input type="number" step="0.01" name="price" value="<?= e($row['price']) ?>"><input type="number" name="stock" value="<?= (int)$row['stock'] ?>"><label class="mini-check"><input type="checkbox" name="active" <?= $row['active'] ? 'checked' : '' ?>> active</label><button class="button light" type="submit"><?= e(t('save')) ?></button></form><?php endforeach; ?></article>
</section>

<section class="dashboard-grid">
  <article class="dashboard-card"><h2><?= e(t('create_invoice')) ?></h2><form method="post" class="stack-form"><input type="hidden" name="action" value="invoice"><label><?= e(t('customers')) ?><select name="user_id"><?php foreach ($customers as $row): ?><option value="<?= (int)$row['id'] ?>"><?= e($row['name']) ?> - <?= e($row['email']) ?></option><?php endforeach; ?></select></label><label><?= e(t('amount')) ?><input type="number" step="0.01" name="amount" required></label><label><?= e(t('details')) ?><textarea name="note"></textarea></label><button class="button primary full" type="submit"><?= e(t('create_invoice')) ?></button></form></article>
  <article class="dashboard-card"><h2><?= e(t('invoices')) ?></h2><table><tbody><?php foreach ($invoices as $row): ?><tr><td>#<?= (int)$row['id'] ?></td><td><?= e($row['name']) ?></td><td>$<?= e($row['amount']) ?></td><td><?php status_form('invoices', $row); ?></td></tr><?php endforeach; table_empty(count($invoices), 4); ?></tbody></table></article>
</section>
<?php render_footer(); ?>

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
    price DECIMAL(10,2) NULL,
    admin_note TEXT NULL,
    status ENUM('Pending','Confirmed','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

function ensure_column(PDO $pdo, string $table, string $column, string $definition): void {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([$table, $column]);
    if ((int)$stmt->fetchColumn() === 0) {
        $pdo->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    }
}

ensure_column($pdo, 'repair_requests', 'price', 'DECIMAL(10,2) NULL AFTER problem');
ensure_column($pdo, 'repair_requests', 'admin_note', 'TEXT NULL AFTER price');
ensure_column($pdo, 'appointments', 'price', 'DECIMAL(10,2) NULL AFTER problem_details');
ensure_column($pdo, 'appointments', 'admin_note', 'TEXT NULL AFTER price');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'status') {
        $table = $_POST['table'];
        if (in_array($table, ['appointments', 'repair_requests'], true)) {
            $price = trim($_POST['price'] ?? '');
            $priceValue = $price === '' ? null : (float)$price;
            $stmt = $pdo->prepare("UPDATE {$table} SET status = ?, price = ?, admin_note = ? WHERE id = ?");
            $stmt->execute([$_POST['status'], $priceValue, trim($_POST['admin_note'] ?? ''), (int)$_POST['id']]);
        }
    }
    flash('Saved.');
    redirect('admin.php');
}

$stats = [
    'repair_requests' => $pdo->query('SELECT COUNT(*) FROM repair_requests')->fetchColumn(),
    'appointments' => $pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn(),
    'customers' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn(),
    'completed' => (int)$pdo->query("SELECT COUNT(*) FROM repair_requests WHERE status = 'Completed'")->fetchColumn() + (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Completed'")->fetchColumn(),
];
$repairRequests = $pdo->query('SELECT * FROM repair_requests ORDER BY created_at DESC')->fetchAll();
$appointments = $pdo->query('SELECT a.*, u.name, u.email, u.phone FROM appointments a JOIN users u ON u.id = a.user_id ORDER BY a.created_at DESC')->fetchAll();
$customers = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC")->fetchAll();

function status_form(string $table, array $row): void {
    echo '<form method="post" class="status-update-form"><input type="hidden" name="action" value="status"><input type="hidden" name="table" value="' . e($table) . '"><input type="hidden" name="id" value="' . (int)$row['id'] . '"><label><span>' . e(t('status')) . '</span><select name="status">';
    foreach (statuses() as $status) {
        $selected = $status === $row['status'] ? ' selected' : '';
        echo '<option' . $selected . '>' . e($status) . '</option>';
    }
    echo '</select></label><label><span>Price</span><input type="number" step="0.01" min="0" name="price" value="' . e($row['price'] ?? '') . '" placeholder="150"></label><label><span>Admin note</span><textarea name="admin_note" placeholder="Client accepted on WhatsApp">' . e($row['admin_note'] ?? '') . '</textarea></label><button class="button light full" type="submit">' . e(t('save')) . '</button></form>';
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
      <a href="#repair-requests"><span><?= e(t('repair_requests')) ?></span><strong><?= (int)$stats['repair_requests'] ?></strong></a>
      <a href="#appointments"><span><?= e(t('appointments')) ?></span><strong><?= (int)$stats['appointments'] ?></strong></a>
      <a href="#customers"><span><?= e(t('customers')) ?></span><strong><?= (int)$stats['customers'] ?></strong></a>
    </nav>
    <div class="admin-sidebar-footer">
      <a href="index.php"><?= e(t('open_site')) ?></a>
      <a href="logout.php"><?= e(t('logout')) ?></a>
    </div>
  </aside>

  <div class="admin-content">
    <section class="admin-topbar admin-section" id="overview">
      <div>
        <small><?= e(date('M d, Y')) ?></small>
        <h1><?= e(t('welcome')) ?>, Admin</h1>
      </div>
      <label class="admin-search">
        <span class="sr-only">Search</span>
        <input type="search" placeholder="Search requests">
      </label>
      <div class="admin-user-chip">
        <span>A</span>
        <strong>Admin</strong>
      </div>
    </section>
    <?php if ($flash): ?><p class="notice success"><?= e($flash) ?></p><?php endif; ?>

    <section class="admin-summary-grid">
      <article class="admin-summary-card cyan"><span><?= e(t('repair_requests')) ?></span><strong><?= (int)$stats['repair_requests'] ?></strong><small>New website requests</small></article>
      <article class="admin-summary-card blue"><span><?= e(t('appointments')) ?></span><strong><?= (int)$stats['appointments'] ?></strong><small>Client dashboard requests</small></article>
      <article class="admin-summary-card violet"><span><?= e(t('customers')) ?></span><strong><?= (int)$stats['customers'] ?></strong><small>Registered clients</small></article>
      <article class="admin-summary-card pink"><span>Completed</span><strong><?= (int)$stats['completed'] ?></strong><small>Finished repair jobs</small></article>
    </section>

    <section class="dashboard-card admin-section" id="repair-requests">
      <h2><?= e(t('repair_requests')) ?></h2>
      <div class="table-scroll">
        <table><thead><tr><th>#</th><th><?= e(t('customer')) ?></th><th><?= e(t('phone')) ?></th><th><?= e(t('details')) ?></th><th>Price / note</th><th><?= e(t('status')) ?></th></tr></thead><tbody>
        <?php foreach ($repairRequests as $row): ?><tr><td><?= (int)$row['id'] ?></td><td><?= e($row['first_name'] . ' ' . $row['family_name']) ?><br><small><?= e($row['email']) ?></small><br><small><?= e($row['created_at']) ?></small></td><td><?= e($row['phone']) ?></td><td><?= e($row['problem']) ?></td><td><?php if ($row['price'] !== null && $row['price'] !== ''): ?><strong><?= e($row['price']) ?> MAD</strong><br><?php endif; ?><?= e($row['admin_note'] ?? '') ?></td><td><?php status_form('repair_requests', $row); ?></td></tr><?php endforeach; table_empty(count($repairRequests), 6); ?>
        </tbody></table>
      </div>
    </section>

    <section class="dashboard-card admin-section" id="appointments">
      <h2><?= e(t('appointments')) ?></h2>
      <div class="table-scroll">
        <table><thead><tr><th>#</th><th><?= e(t('customer')) ?></th><th><?= e(t('services')) ?></th><th><?= e(t('address')) ?></th><th>Price / note</th><th><?= e(t('status')) ?></th></tr></thead><tbody>
        <?php foreach ($appointments as $row): ?><tr><td><?= (int)$row['id'] ?></td><td><?= e($row['name']) ?><br><small><?= e($row['email']) ?></small><br><small><?= e($row['phone']) ?></small></td><td><?= e($row['service_type']) ?><br><small><?= e($row['problem_details']) ?></small></td><td><?= e($row['address']) ?></td><td><?php if ($row['price'] !== null && $row['price'] !== ''): ?><strong><?= e($row['price']) ?> MAD</strong><br><?php endif; ?><?= e($row['admin_note'] ?? '') ?></td><td><?php status_form('appointments', $row); ?></td></tr><?php endforeach; table_empty(count($appointments), 6); ?>
        </tbody></table>
      </div>
    </section>

    <section class="dashboard-card admin-section" id="customers">
      <h2><?= e(t('customers')) ?></h2>
      <div class="table-scroll">
        <table><thead><tr><th><?= e(t('name')) ?></th><th><?= e(t('email')) ?></th><th><?= e(t('phone')) ?></th><th><?= e(t('address')) ?></th></tr></thead><tbody>
        <?php foreach ($customers as $row): ?><tr><td><?= e($row['name']) ?></td><td><?= e($row['email']) ?></td><td><?= e($row['phone']) ?></td><td><?= e($row['address']) ?></td></tr><?php endforeach; table_empty(count($customers), 4); ?>
        </tbody></table>
      </div>
    </section>
  </div>
</div>
<?php render_footer(); ?>

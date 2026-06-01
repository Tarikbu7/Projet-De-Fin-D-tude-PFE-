<?php
require_once __DIR__ . '/includes/app.php';
$user = require_login();
if ($user['role'] === 'admin') {
    redirect('admin.php');
}

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'appointment') {
        $stmt = $pdo->prepare('INSERT INTO appointments (user_id, service_type, preferred_date, preferred_time, address, problem_details) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$user['id'], $_POST['service_type'], $_POST['preferred_date'], $_POST['preferred_time'], $_POST['address'], $_POST['problem_details']]);
        flash('Appointment request sent.');
    }
    if ($action === 'order') {
        $productId = (int)$_POST['product_id'];
        $quantity = max(1, (int)$_POST['quantity']);
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? AND active = 1');
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        if ($product) {
            $total = (float)$product['price'] * $quantity;
            $stmt = $pdo->prepare('INSERT INTO orders (user_id, total, notes) VALUES (?, ?, ?)');
            $stmt->execute([$user['id'], $total, $_POST['notes'] ?? '']);
            $orderId = (int)$pdo->lastInsertId();
            $stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)');
            $stmt->execute([$orderId, $productId, $quantity, $product['price']]);
            flash('Order placed.');
        }
    }
    if ($action === 'pc_build') {
        $stmt = $pdo->prepare('INSERT INTO pc_build_requests (user_id, budget, purpose, details) VALUES (?, ?, ?, ?)');
        $stmt->execute([$user['id'], $_POST['budget'], $_POST['purpose'], $_POST['details']]);
        flash('PC build request sent.');
    }
    if ($action === 'message') {
        $stmt = $pdo->prepare('INSERT INTO messages (user_id, subject, body) VALUES (?, ?, ?)');
        $stmt->execute([$user['id'], $_POST['subject'], $_POST['body']]);
        flash('Message sent.');
    }
    redirect('dashboard.php');
}

$products = $pdo->query('SELECT * FROM products WHERE active = 1 ORDER BY category, name')->fetchAll();
$services = $pdo->query('SELECT * FROM services WHERE active = 1 ORDER BY name')->fetchAll();
$appointments = $pdo->prepare('SELECT * FROM appointments WHERE user_id = ? ORDER BY created_at DESC');
$appointments->execute([$user['id']]);
$appointments = $appointments->fetchAll();
$orders = $pdo->prepare('SELECT o.*, p.name AS product_name, oi.quantity FROM orders o JOIN order_items oi ON oi.order_id = o.id JOIN products p ON p.id = oi.product_id WHERE o.user_id = ? ORDER BY o.created_at DESC');
$orders->execute([$user['id']]);
$orders = $orders->fetchAll();
$builds = $pdo->prepare('SELECT * FROM pc_build_requests WHERE user_id = ? ORDER BY created_at DESC');
$builds->execute([$user['id']]);
$builds = $builds->fetchAll();
$invoices = $pdo->prepare('SELECT * FROM invoices WHERE user_id = ? ORDER BY created_at DESC');
$invoices->execute([$user['id']]);
$invoices = $invoices->fetchAll();

render_header(t('user_dashboard'), $user);
$flash = flash();
?>
<section class="dashboard-hero">
  <div>
    <p class="eyebrow"><?= e(t('user_dashboard')) ?></p>
    <h1><?= e(t('welcome')) ?>, <?= e($user['name']) ?></h1>
    <p class="hero-copy">24/7 requests are open. You can book repair, order parts, ask for a custom PC, and check status here.</p>
  </div>
</section>
<?php if ($flash): ?><p class="notice success"><?= e($flash) ?></p><?php endif; ?>

<section class="dashboard-grid">
  <article class="dashboard-card">
    <h2><?= e(t('request_appointment')) ?></h2>
    <form method="post" class="stack-form">
      <input type="hidden" name="action" value="appointment">
      <label><?= e(t('services')) ?><select name="service_type" required><?php foreach ($services as $service): ?><option><?= e($service['name']) ?></option><?php endforeach; ?></select></label>
      <label><?= e(t('date')) ?><input type="date" name="preferred_date" required></label>
      <label><?= e(t('time')) ?><input type="text" name="preferred_time" value="Any time / 24-7" required></label>
      <label><?= e(t('address')) ?><input type="text" name="address" value="<?= e($user['address']) ?>" required></label>
      <label><?= e(t('details')) ?><textarea name="problem_details" required placeholder="Anything customer want"></textarea></label>
      <button class="button primary full" type="submit"><?= e(t('submit')) ?></button>
    </form>
  </article>

  <article class="dashboard-card">
    <h2><?= e(t('place_order')) ?></h2>
    <form method="post" class="stack-form">
      <input type="hidden" name="action" value="order">
      <label><?= e(t('products')) ?><select name="product_id" required><?php foreach ($products as $product): ?><option value="<?= (int)$product['id'] ?>"><?= e($product['category'] . ' - ' . $product['name'] . ' ($' . $product['price'] . ')') ?></option><?php endforeach; ?></select></label>
      <label><?= e(t('quantity')) ?><input type="number" name="quantity" value="1" min="1" required></label>
      <label><?= e(t('details')) ?><textarea name="notes" placeholder="Tell us exactly what you want to buy."></textarea></label>
      <button class="button primary full" type="submit"><?= e(t('place_order')) ?></button>
    </form>
  </article>

  <article class="dashboard-card">
    <h2><?= e(t('pc_build')) ?></h2>
    <form method="post" class="stack-form">
      <input type="hidden" name="action" value="pc_build">
      <label><?= e(t('budget')) ?><input type="text" name="budget" placeholder="$800, $1200, etc."></label>
      <label><?= e(t('purpose')) ?><input type="text" name="purpose" placeholder="Gaming, work, school, editing..."></label>
      <label><?= e(t('details')) ?><textarea name="details" required placeholder="Anything customer want"></textarea></label>
      <button class="button primary full" type="submit"><?= e(t('submit')) ?></button>
    </form>
  </article>

  <article class="dashboard-card">
    <h2><?= e(t('send_message')) ?></h2>
    <form method="post" class="stack-form">
      <input type="hidden" name="action" value="message">
      <label><?= e(t('subject')) ?><input type="text" name="subject" required></label>
      <label><?= e(t('message')) ?><textarea name="body" required></textarea></label>
      <button class="button primary full" type="submit"><?= e(t('send_message')) ?></button>
    </form>
  </article>
</section>

<section class="dashboard-card">
  <h2><?= e(t('appointments')) ?></h2>
  <table><thead><tr><th>#</th><th><?= e(t('services')) ?></th><th><?= e(t('date')) ?></th><th><?= e(t('time')) ?></th><th><?= e(t('status')) ?></th></tr></thead><tbody>
  <?php foreach ($appointments as $row): ?><tr><td><?= (int)$row['id'] ?></td><td><?= e($row['service_type']) ?></td><td><?= e($row['preferred_date']) ?></td><td><?= e($row['preferred_time']) ?></td><td><span class="status"><?= e(status_label($row['status'])) ?></span></td></tr><?php endforeach; table_empty(count($appointments), 5); ?>
  </tbody></table>
</section>

<section class="dashboard-grid">
  <article class="dashboard-card"><h2><?= e(t('orders')) ?></h2><table><tbody><?php foreach ($orders as $row): ?><tr><td>#<?= (int)$row['id'] ?></td><td><?= e($row['product_name']) ?> x <?= (int)$row['quantity'] ?></td><td>$<?= e($row['total']) ?></td><td><?= e(status_label($row['status'])) ?></td></tr><?php endforeach; table_empty(count($orders), 4); ?></tbody></table></article>
  <article class="dashboard-card"><h2><?= e(t('pc_build')) ?></h2><table><tbody><?php foreach ($builds as $row): ?><tr><td>#<?= (int)$row['id'] ?></td><td><?= e($row['purpose']) ?></td><td><?= e(status_label($row['status'])) ?></td></tr><?php endforeach; table_empty(count($builds), 3); ?></tbody></table></article>
  <article class="dashboard-card"><h2><?= e(t('invoices')) ?></h2><table><tbody><?php foreach ($invoices as $row): ?><tr><td>#<?= (int)$row['id'] ?></td><td>$<?= e($row['amount']) ?></td><td><?= e(status_label($row['status'])) ?></td></tr><?php endforeach; table_empty(count($invoices), 3); ?></tbody></table></article>
</section>
<?php render_footer(); ?>

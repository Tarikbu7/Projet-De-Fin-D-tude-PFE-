<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/admin-dashboard.php';

$user = require_admin();
$pdo = db();
ensure_reviews_table($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_handle_service_post($pdo);
}

$stats = admin_stats($pdo);
$services = $pdo->query(
    'SELECT id, name, base_price, active FROM services ORDER BY active DESC, name'
)->fetchAll();
$csrfToken = csrf_token();

admin_page_start('Service management', 'services', $user, $stats);
?>
<section class="dashboard-card admin-services-card">
  <div class="admin-card-heading">
    <div>
      <h2>Services</h2>
      <p>Add services, change prices, or control what customers can select.</p>
    </div>
  </div>

  <form method="post" class="service-create-form">
    <input type="hidden" name="action" value="service_create">
    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
    <label>
      <span>Service name</span>
      <input type="text" name="name" maxlength="120" required placeholder="Example: Virus removal">
    </label>
    <label>
      <span>Base price (MAD)</span>
      <input type="number" name="base_price" step="0.01" min="0" required placeholder="0.00">
    </label>
    <label class="service-active-field">
      <input type="checkbox" name="active" value="1" checked>
      <span>Available to customers</span>
    </label>
    <button class="button primary" type="submit">Add service</button>
  </form>

  <div class="table-scroll">
    <table class="admin-services-table">
      <thead>
        <tr><th>#</th><th>Name</th><th>Base price</th><th>Available</th><th>Actions</th></tr>
      </thead>
      <tbody>
      <?php foreach ($services as $service): ?>
        <?php $updateFormId = 'service-update-' . (int)$service['id']; ?>
        <tr>
          <td><?= (int)$service['id'] ?></td>
          <td><input form="<?= e($updateFormId) ?>" type="text" name="name" maxlength="120" required value="<?= e($service['name']) ?>" aria-label="Service name"></td>
          <td><input form="<?= e($updateFormId) ?>" type="number" name="base_price" step="0.01" min="0" required value="<?= e($service['base_price']) ?>" aria-label="Base price"></td>
          <td>
            <label class="service-active-field compact">
              <input form="<?= e($updateFormId) ?>" type="checkbox" name="active" value="1"<?= (int)$service['active'] === 1 ? ' checked' : '' ?>>
              <span><?= (int)$service['active'] === 1 ? 'Active' : 'Disabled' ?></span>
            </label>
          </td>
          <td>
            <div class="service-actions">
              <form method="post" id="<?= e($updateFormId) ?>">
                <input type="hidden" name="action" value="service_update">
                <input type="hidden" name="service_id" value="<?= (int)$service['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <button class="button light" type="submit">Save</button>
              </form>
              <form method="post">
                <input type="hidden" name="action" value="service_toggle">
                <input type="hidden" name="service_id" value="<?= (int)$service['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <button class="button light" type="submit"><?= (int)$service['active'] === 1 ? 'Disable' : 'Enable' ?></button>
              </form>
              <form method="post">
                <input type="hidden" name="action" value="service_delete">
                <input type="hidden" name="service_id" value="<?= (int)$service['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <button class="button service-delete-button" type="submit" onclick="return confirm('Delete this unused service?')">Delete</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; table_empty(count($services), 5); ?>
      </tbody>
    </table>
  </div>
</section>
<?php admin_page_end(); ?>

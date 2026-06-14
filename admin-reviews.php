<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/admin-dashboard.php';

$user = require_admin();
$pdo = db();
ensure_reviews_table($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_handle_review_post($pdo);
}

$stats = admin_stats($pdo);
$reviews = $pdo->query('
    SELECT r.*, u.name, u.email, a.service_type
    FROM reviews r
    JOIN users u ON u.id = r.user_id
    JOIN appointments a ON a.id = r.appointment_id
    ORDER BY r.created_at DESC
')->fetchAll();
$csrfToken = csrf_token();

admin_page_start('Review management', 'reviews', $user, $stats);
?>
<section class="dashboard-card admin-reviews-card">
  <div class="admin-card-heading">
    <div>
      <h2>Customer reviews</h2>
      <p>Approve or reject feedback submitted after completed repairs.</p>
    </div>
  </div>
  <div class="table-scroll">
    <table class="admin-reviews-table">
      <thead><tr><th>Customer</th><th>Service</th><th>Rating</th><th>Review</th><th>Submitted</th><th>Decision</th></tr></thead>
      <tbody>
      <?php foreach ($reviews as $review): ?>
        <tr>
          <td><?= e($review['name']) ?><small><?= e($review['email']) ?></small></td>
          <td><?= e($review['service_type']) ?></td>
          <td><span class="admin-review-stars" aria-label="<?= (int)$review['rating'] ?> out of 5 stars"><?= str_repeat('&#9733;', (int)$review['rating']) ?></span></td>
          <td class="admin-review-comment"><?= e($review['comment']) ?></td>
          <td><?= e(date('M d, Y', strtotime($review['created_at']))) ?></td>
          <td>
            <form method="post" class="review-status-form">
              <input type="hidden" name="action" value="review_status">
              <input type="hidden" name="review_id" value="<?= (int)$review['id'] ?>">
              <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
              <select name="review_status" aria-label="Review status">
                <?php foreach (review_statuses() as $reviewStatus): ?>
                  <option<?= $reviewStatus === $review['status'] ? ' selected' : '' ?>><?= e($reviewStatus) ?></option>
                <?php endforeach; ?>
              </select>
              <button class="button light full" type="submit">Save</button>
            </form>
          </td>
        </tr>
      <?php endforeach; table_empty(count($reviews), 6); ?>
      </tbody>
    </table>
  </div>
</section>
<?php admin_page_end(); ?>

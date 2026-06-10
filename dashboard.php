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
$notice = flash();
?>
<section class="dashboard-hero customer-dashboard-hero">
  <div>
    <h1><?= e(t('welcome')) ?>, <?= e($user['name']) ?></h1>
    <p class="hero-copy">Track your repair appointment requests and their current status here.</p>
  </div>
</section>

<?php if ($notice): ?>
  <p class="notice <?= str_starts_with($notice, 'Thank you') ? 'success' : 'error' ?>"><?= e($notice) ?></p>
<?php endif; ?>

<section class="dashboard-card customer-appointments-card">
  <div class="customer-card-heading">
    <h2><?= e(t('appointments')) ?></h2>
    <span class="appointment-count" aria-label="<?= count($appointments) ?> <?= e(t('appointments')) ?>"><?= count($appointments) ?></span>
  </div>
  <div class="customer-table-scroll">
    <table class="customer-appointments-table">
      <thead>
        <tr>
          <th><?= e(t('services')) ?></th>
          <th><?= e(t('details')) ?></th>
          <th>Price</th>
          <th><?= e(t('status')) ?></th>
          <th>Review</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($appointments as $row): ?>
        <tr>
          <td>
            <?= e($row['service_type']) ?>
            <small class="appointment-reference">#<?= (int)$row['id'] ?> · <?= e(date('M d, Y', strtotime($row['created_at']))) ?></small>
          </td>
          <td><?= e($row['problem_details']) ?></td>
          <td>
            <?php if (($row['price'] ?? null) !== null && $row['price'] !== ''): ?>
              <strong><?= e($row['price']) ?> MAD</strong>
            <?php else: ?>
              <span class="muted-value"><?= e(t('awaiting_quote')) ?></span>
            <?php endif; ?>
          </td>
          <td><span class="status"><?= e(status_label($row['status'])) ?></span></td>
          <td>
            <?php if ($row['review_id'] !== null): ?>
              <span class="review-state <?= strtolower(e($row['review_status'])) ?>"><?= e($row['review_status']) ?></span>
            <?php elseif ($row['status'] === 'Completed'): ?>
              <a class="table-review-link" href="#leave-review">Available</a>
            <?php else: ?>
              <span class="muted-value">After completion</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; table_empty(count($appointments), 5); ?>
      </tbody>
    </table>
  </div>
</section>

<section class="dashboard-card customer-reviews-card" id="reviews">
  <div class="customer-card-heading">
    <div>
      <h2>Leave a review</h2>
      <p>Choose a completed service, rate it, then write your comment.</p>
    </div>
  </div>
  <div class="customer-review-list">
    <?php if ($reviewableAppointments): ?>
      <form method="post" class="review-form simple-review-form" id="leave-review">
        <input type="hidden" name="action" value="review">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <label>
          <span>1. Which service do you want to review?</span>
          <select name="appointment_id" required>
            <option value="">Choose a completed service</option>
            <?php foreach ($reviewableAppointments as $row): ?>
              <option value="<?= (int)$row['id'] ?>">
                <?= e($row['service_type']) ?> · <?= e(date('M d, Y', strtotime($row['created_at']))) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>
        <fieldset class="star-rating">
          <legend>2. How was the service?</legend>
          <?php for ($rating = 5; $rating >= 1; $rating--): ?>
            <input type="radio" id="rating-<?= $rating ?>" name="rating" value="<?= $rating ?>" required>
            <label for="rating-<?= $rating ?>" title="<?= $rating ?> stars">★</label>
          <?php endfor; ?>
        </fieldset>
        <label>
          <span>3. Write your comment</span>
          <textarea name="comment" maxlength="1000" rows="4" required placeholder="Tell us what you liked or what could be better."></textarea>
        </label>
        <button class="button primary" type="submit">Send my review</button>
      </form>
    <?php else: ?>
      <p class="empty-review-message">There is no completed service available to review yet.</p>
    <?php endif; ?>

    <?php if ($submittedReviews): ?>
      <div class="submitted-reviews">
        <h3>Your previous reviews</h3>
        <?php foreach ($submittedReviews as $row): ?>
          <article class="submitted-review-row">
            <div>
              <strong><?= e($row['service_type']) ?></strong>
              <span><?= e(date('M d, Y', strtotime($row['created_at']))) ?></span>
            </div>
            <div class="review-rating"><?= str_repeat('&#9733;', (int)$row['review_rating']) ?></div>
            <span class="review-state <?= strtolower(e($row['review_status'])) ?>"><?= e($row['review_status']) ?></span>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="dashboard-card customer-reviews-card" id="reviews">
  <div class="customer-card-heading">
    <div>
      <h2>My reviews</h2>
      <p>After a repair is completed, you can share your experience here.</p>
    </div>
  </div>

  <?php if (!$completedAppointments): ?>
    <p class="reviews-dashboard-empty">You can write a review after an appointment is marked as completed.</p>
  <?php else: ?>
    <div class="customer-review-list">
      <?php foreach ($completedAppointments as $appointment): ?>
        <article class="customer-review-item">
          <div class="customer-review-service">
            <strong><?= e($appointment['service_type']) ?></strong>
            <span><?= e(date('M d, Y', strtotime($appointment['created_at']))) ?></span>
          </div>

          <?php if ($appointment['review_id']): ?>
            <div class="submitted-review">
              <span class="review-moderation-status <?= e(strtolower($appointment['review_status'])) ?>"><?= e($appointment['review_status']) ?></span>
              <strong><?= str_repeat('★', (int)$appointment['review_rating']) ?></strong>
              <p><?= e($appointment['review_comment']) ?></p>
            </div>
          <?php else: ?>
            <form method="post" class="customer-review-form">
              <input type="hidden" name="action" value="review">
              <input type="hidden" name="appointment_id" value="<?= (int)$appointment['id'] ?>">
              <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
              <label>
                <span>Rating</span>
                <select name="rating" required>
                  <option value="">Choose a rating</option>
                  <option value="5">5 - Excellent</option>
                  <option value="4">4 - Very good</option>
                  <option value="3">3 - Good</option>
                  <option value="2">2 - Fair</option>
                  <option value="1">1 - Poor</option>
                </select>
              </label>
              <label>
                <span>Your review</span>
                <textarea name="comment" minlength="10" maxlength="1000" required placeholder="Tell us how the repair went"></textarea>
              </label>
              <button class="button primary" type="submit">Send review</button>
            </form>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php render_footer(); ?>

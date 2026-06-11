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

$stmt = $pdo->prepare('
    SELECT a.*, r.id AS review_id, r.rating AS review_rating, r.comment AS review_comment, r.status AS review_status
    FROM appointments a
    LEFT JOIN reviews r ON r.appointment_id = a.id
    WHERE a.user_id = ?
    ORDER BY a.created_at DESC
');
$stmt->execute([$user['id']]);
$appointments = $stmt->fetchAll();

// Appointments available to review (completed, no review yet)
$reviewableAppointments = array_filter($appointments, fn($a) => $a['status'] === 'Completed' && $a['review_id'] === null);

// All completed appointments (for the reviews section)
$completedAppointments = array_filter($appointments, fn($a) => $a['status'] === 'Completed');

// Already submitted reviews
$submittedReviews = array_filter($appointments, fn($a) => $a['review_id'] !== null);

$csrfToken = csrf_token();

render_header(t('user_dashboard'), $user);
$notice = flash();
?>
<section class="dashboard-hero customer-dashboard-hero">
  <div>
    <h1><?= e(t('welcome')) ?>, <?= e($user['name']) ?></h1>
    <p class="hero-copy"><?= e(t('track_repairs')) ?></p>
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
          <th><?= e(t('price')) ?></th>
          <th><?= e(t('status')) ?></th>
          <th><?= e(t('review')) ?></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($appointments as $row): ?>
        <tr>
          <td>
            <?= e(translate_service($row['service_type'])) ?>
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
              <a class="table-review-link" href="#leave-review"><?= e(t('available')) ?></a>
            <?php else: ?>
              <span class="muted-value"><?= e(t('after_completion')) ?></span>
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
      <h2><?= e(t('leave_review')) ?></h2>
      <p><?= e(t('leave_review_desc')) ?></p>
    </div>
  </div>
  <div class="customer-review-list">
    <?php if ($reviewableAppointments): ?>
      <form method="post" class="review-form simple-review-form" id="leave-review">
        <input type="hidden" name="action" value="review">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <label>
          <span><?= e(t('which_service_review')) ?></span>
          <select name="appointment_id" required>
            <option value=""><?= e(t('choose_completed_service')) ?></option>
            <?php foreach ($reviewableAppointments as $row): ?>
              <option value="<?= (int)$row['id'] ?>">
                <?= e(translate_service($row['service_type'])) ?> · <?= e(date('M d, Y', strtotime($row['created_at']))) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>
        <fieldset class="star-rating">
          <legend><?= e(t('how_was_service')) ?></legend>
          <?php for ($rating = 5; $rating >= 1; $rating--): ?>
            <input type="radio" id="rating-<?= $rating ?>" name="rating" value="<?= $rating ?>" required>
            <label for="rating-<?= $rating ?>" title="<?= $rating ?> <?= e(t('stars')) ?>">★</label>
          <?php endfor; ?>
        </fieldset>
        <label>
          <span><?= e(t('write_comment')) ?></span>
          <textarea name="comment" maxlength="1000" rows="4" required placeholder="<?= e(t('write_comment_placeholder')) ?>"></textarea>
        </label>
        <button class="button primary" type="submit"><?= e(t('send_review')) ?></button>
      </form>
    <?php else: ?>
      <p class="empty-review-message"><?= e(t('no_completed_service')) ?></p>
    <?php endif; ?>

    <?php if ($submittedReviews): ?>
      <div class="submitted-reviews">
        <h3><?= e(t('previous_reviews')) ?></h3>
        <?php foreach ($submittedReviews as $row): ?>
          <article class="submitted-review-row">
            <div>
              <strong><?= e(translate_service($row['service_type'])) ?></strong>
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
      <h2><?= e(t('my_reviews')) ?></h2>
      <p><?= e(t('my_reviews_desc')) ?></p>
    </div>
  </div>

  <?php if (!$completedAppointments): ?>
    <p class="reviews-dashboard-empty"><?= e(t('review_after_completed')) ?></p>
  <?php else: ?>
    <div class="customer-review-list">
      <?php foreach ($completedAppointments as $appointment): ?>
        <article class="customer-review-item">
          <div class="customer-review-service">
            <strong><?= e(translate_service($appointment['service_type'])) ?></strong>
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
                <span><?= e(t('rating')) ?></span>
                <select name="rating" required>
                  <option value=""><?= e(t('choose_rating')) ?></option>
                  <option value="5">5 - <?= e(t('excellent')) ?></option>
                  <option value="4">4 - <?= e(t('very_good')) ?></option>
                  <option value="3">3 - <?= e(t('good')) ?></option>
                  <option value="2">2 - <?= e(t('fair')) ?></option>
                  <option value="1">1 - <?= e(t('poor')) ?></option>
                </select>
              </label>
              <label>
                <span><?= e(t('your_review')) ?></span>
                <textarea name="comment" minlength="10" maxlength="1000" required placeholder="<?= e(t('review_placeholder')) ?>"></textarea>
              </label>
              <button class="button primary" type="submit"><?= e(t('send_review')) ?></button>
            </form>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php render_footer(); ?>

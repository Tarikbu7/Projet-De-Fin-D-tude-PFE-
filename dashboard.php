<?php
require_once __DIR__ . '/includes/app.php';

// Get the logged-in customer.
$user = require_login();
if ($user['role'] === 'admin') {
    redirect('admin.php');
}

$pdo = db();

// Add missing database parts.
ensure_database_column($pdo, 'appointments', 'price', 'DECIMAL(10,2) NULL AFTER problem_details');
ensure_reviews_table($pdo);

// Check and save a customer review.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if (($_POST['action'] ?? '') !== 'review') {
        http_response_code(400);
        exit('Unsupported action.');
    }

    $appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $comment = trim((string)($_POST['comment'] ?? ''));

    if (
        !$appointmentId
        || !$rating
        || $rating < 1
        || $rating > 5
        || mb_strlen($comment) < 10
        || mb_strlen($comment) > 1000
    ) {
        flash('Please choose a completed service, select a rating, and write at least 10 characters.');
        redirect('dashboard.php#leave-review');
    }

    $appointment = $pdo->prepare(
        'SELECT id
         FROM appointments
         WHERE id = ? AND user_id = ? AND status = "Completed"
         LIMIT 1'
    );
    $appointment->execute([$appointmentId, $user['id']]);

    if (!$appointment->fetch()) {
        flash('This appointment is not available for review.');
        redirect('dashboard.php#leave-review');
    }

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO reviews (user_id, appointment_id, rating, comment)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$user['id'], $appointmentId, $rating, $comment]);
        flash('Thank you. Your review was submitted for approval.');
    } catch (PDOException $exception) {
        $duplicateEntry = (string)$exception->getCode() === '23000';
        flash($duplicateEntry
            ? 'A review has already been submitted for this appointment.'
            : 'Your review could not be saved. Please try again.');
    }

    redirect('dashboard.php#reviews');
}

// Get repairs and their reviews.
$stmt = $pdo->prepare('
    SELECT a.*, r.id AS review_id, r.rating AS review_rating, r.comment AS review_comment, r.status AS review_status
    FROM appointments a
    LEFT JOIN reviews r ON r.appointment_id = a.id
    WHERE a.user_id = ?
    ORDER BY a.created_at DESC
');
$stmt->execute([$user['id']]);
$appointments = $stmt->fetchAll();

// Group repairs for the review area.
$reviewableAppointments = array_filter(
    $appointments,
    static fn(array $appointment): bool =>
        $appointment['status'] === 'Completed' && $appointment['review_id'] === null
);
$submittedReviews = array_filter(
    $appointments,
    static fn(array $appointment): bool => $appointment['review_id'] !== null
);

// Get the form code and page message.
$csrfToken = csrf_token();

render_header(t('user_dashboard'), $user);
$notice = flash();
?>
<!-- Customer welcome area -->
<section class="dashboard-hero customer-dashboard-hero">
  <div>
    <h1><?= e(t('welcome')) ?>, <?= e($user['name']) ?></h1>
    <p class="hero-copy"><?= e(t('track_repairs')) ?></p>
  </div>
</section>

<?php if ($notice): ?>
  <p class="notice <?= str_starts_with($notice, 'Thank you') ? 'success' : 'error' ?>"><?= e($notice) ?></p>
<?php endif; ?>

<!-- Customer repair list -->
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

<!-- New and old reviews -->
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
<?php render_footer(); ?>

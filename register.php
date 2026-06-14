<?php
require_once __DIR__ . '/includes/config.php';

// Send logged-in users to the home page.
if (current_user()) {
    redirect('index.php');
}

// Set the starting form values and errors.
$error = null;
$name = '';
$email = '';
$phone = '';
$city = '';
$fieldErrors = [];

// Check the form and create the account.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($name === '' || mb_strlen($name) > 120) {
        $fieldErrors['name'] = 'Please enter your name.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $fieldErrors['email'] = 'Please enter a valid email address.';
    }
    if (mb_strlen($phone) > 30) {
        $fieldErrors['phone'] = 'Phone number is too long.';
    }
    if (mb_strlen($city) > 255) {
        $fieldErrors['city'] = 'City is too long.';
    }
    if (mb_strlen($password) < 8) {
        $fieldErrors['password'] = 'Use at least 8 characters.';
    }

    if ($fieldErrors === []) {
        try {
            $stmt = db()->prepare('INSERT INTO users (name, email, phone, address, password_hash, role) VALUES (?, ?, ?, ?, ?, "user")');
            $stmt->execute([$name, $email, $phone, $city, password_hash($password, PASSWORD_DEFAULT)]);
            flash('Your account was created. You can sign in now.');
            redirect('login.php');
        } catch (Throwable $exception) {
            $error = 'We could not create the account. That email may already be registered.';
        }
    } else {
        $error = 'Please correct the highlighted fields.';
    }
}

// Show the register form.
render_header(t('register'));
?>
<!-- Register form -->
<section class="auth-panel auth-panel-wide">
  <div class="auth-heading">
    <h1>Create your account</h1>
    <p>You will use it to request appointments and follow their progress.</p>
  </div>
  <?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
  <p class="auth-form-message" role="alert" hidden></p>
  <form method="post" class="stack-form auth-register-form">
    <?= csrf_input() ?>
    <label>
      <span><?= e(t('name')) ?></span>
      <input type="text" name="name" autocomplete="name" maxlength="120" required value="<?= e($name) ?>" aria-describedby="name-error"<?= isset($fieldErrors['name']) ? ' class="is-invalid" aria-invalid="true"' : '' ?>>
      <small class="field-error" id="name-error"><?= e($fieldErrors['name'] ?? '') ?></small>
    </label>
    <label>
      <span><?= e(t('email')) ?></span>
      <input type="email" name="email" autocomplete="email" maxlength="180" required value="<?= e($email) ?>" aria-describedby="email-error"<?= isset($fieldErrors['email']) ? ' class="is-invalid" aria-invalid="true"' : '' ?>>
      <small class="field-error" id="email-error"><?= e($fieldErrors['email'] ?? '') ?></small>
    </label>
    <label>
      <span><?= e(t('phone')) ?> <small>(optional)</small></span>
      <input type="tel" name="phone" autocomplete="tel" maxlength="30" value="<?= e($phone) ?>" aria-describedby="phone-error"<?= isset($fieldErrors['phone']) ? ' class="is-invalid" aria-invalid="true"' : '' ?>>
      <small class="field-error" id="phone-error"><?= e($fieldErrors['phone'] ?? '') ?></small>
    </label>
    <label>
      <span><?= e(t('city')) ?> <small>(optional)</small></span>
      <input type="text" name="city" autocomplete="address-level2" maxlength="255" value="<?= e($city) ?>" aria-describedby="city-error"<?= isset($fieldErrors['city']) ? ' class="is-invalid" aria-invalid="true"' : '' ?>>
      <small class="field-error" id="city-error"><?= e($fieldErrors['city'] ?? '') ?></small>
    </label>
    <label class="auth-password-field">
    <span><?= e(t('password')) ?></span>
    <span class="password-input">
        <input type="password" name="password" autocomplete="new-password" required minlength="8" data-password-input aria-describedby="password-help password-error"<?= isset($fieldErrors['password']) ? ' class="is-invalid" aria-invalid="true"' : '' ?>>
        <button type="button" class="password-toggle" aria-label="Show password" aria-pressed="false" data-password-toggle>
          Show
        </button>
      </span>
      <small id="password-help">Use at least 8 characters.</small>
      <small class="field-error" id="password-error"><?= e($fieldErrors['password'] ?? '') ?></small>
    </label>
    <button class="button primary full auth-submit" type="submit"><?= e(t('create_account')) ?></button>
  </form>
  <p class="muted-line">Already have an account? <a href="login.php"><?= e(t('sign_in')) ?></a></p>
</section>
<script src="assets/register-validation.js"></script>
<?php render_footer(); ?>

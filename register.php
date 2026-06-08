<?php
require_once __DIR__ . '/includes/app.php';

if (current_user()) {
    redirect('index.php');
}

$error = null;
$name = '';
$email = '';
$phone = '';
$city = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    try {
        $errors = [];
        if ($name === '') {
            $errors[] = 'Name is required.';
        }
        if ($email === '') {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Enter a valid email address.';
        }
        if ($phone === '') {
            $errors[] = 'Phone number is required.';
        }
        if ($city === '') {
            $errors[] = 'City is required.';
        }
        if ($password === '') {
            $errors[] = 'Password is required.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must contain at least 6 characters.';
        }

        if ($errors) {
            $error = implode(' ', $errors);
        } else {
            $stmt = db()->prepare('INSERT INTO users (name, email, phone, address, password_hash, role) VALUES (?, ?, ?, ?, ?, "user")');
            $stmt->execute([$name, $email, $phone, $city, password_hash($password, PASSWORD_DEFAULT)]);
            flash('Your account was created. You can sign in now.');
            redirect('login.php');
        }
    } catch (Throwable $exception) {
        $error = 'We could not create the account. That email may already be registered.';
    }
}

render_header(t('register'));
?>
<section class="auth-panel auth-panel-wide">
  <div class="auth-heading">
    <h1>Create your account</h1>
    <p>You will use it to request appointments and follow their progress.</p>
  </div>
  <?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
  <form method="post" class="stack-form auth-register-form" novalidate>
    <?= csrf_input() ?>
    <label>
      <span><?= e(t('name')) ?></span>
      <input type="text" name="name" autocomplete="name" required value="<?= e($name) ?>">
    </label>
    <label>
      <span><?= e(t('email')) ?></span>
      <input type="email" name="email" autocomplete="email" required value="<?= e($email) ?>">
    </label>
    <label>
      <span><?= e(t('phone')) ?></span>
      <input type="tel" name="phone" autocomplete="tel" required value="<?= e($phone) ?>">
    </label>
    <label>
      <span><?= e(t('city')) ?></span>
      <input type="text" name="city" autocomplete="address-level2" required value="<?= e($city) ?>">
    </label>
    <label class="auth-password-field">
      <span><?= e(t('password')) ?></span>
      <input type="password" name="password" autocomplete="new-password" required minlength="6">
      <small>Use at least 6 characters.</small>
    </label>
    <button class="button primary full auth-submit" type="submit"><?= e(t('create_account')) ?></button>
  </form>
  <p class="muted-line">Already have an account? <a href="login.php"><?= e(t('sign_in')) ?></a></p>
</section>
<?php render_footer(); ?>

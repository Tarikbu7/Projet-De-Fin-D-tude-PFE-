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
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    try {
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
            $error = 'Enter your name, a valid email, and a password of at least 6 characters.';
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
  <form method="post" class="stack-form auth-register-form">
    <label>
      <span><?= e(t('name')) ?></span>
      <input type="text" name="name" autocomplete="name" required value="<?= e($name) ?>">
    </label>
    <label>
      <span><?= e(t('email')) ?></span>
      <input type="email" name="email" autocomplete="email" required value="<?= e($email) ?>">
    </label>
    <label>
      <span><?= e(t('phone')) ?> <small>(optional)</small></span>
      <input type="tel" name="phone" autocomplete="tel" value="<?= e($phone) ?>">
    </label>
    <label>
      <span><?= e(t('city')) ?> <small>(optional)</small></span>
      <input type="text" name="city" autocomplete="address-level2" value="<?= e($city) ?>">
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

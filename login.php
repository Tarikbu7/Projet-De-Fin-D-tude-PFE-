<?php
require_once __DIR__ . '/includes/app.php';

if (current_user()) {
    redirect('index.php');
}

$error = null;
$email = '';
$message = flash();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    try {
        $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify((string)($_POST['password'] ?? ''), $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            unset($_SESSION['csrf_token']);
            redirect('index.php');
        }
        $error = 'Invalid email or password.';
    } catch (Throwable $exception) {
        $error = t('setup_needed') . ' ' . $exception->getMessage();
    }
}

render_header(t('login'));
?>
<section class="auth-panel">
  <div class="auth-heading">
    <h1>Sign in to your account</h1>
    <p>Enter your email and password to continue.</p>
  </div>
  <?php if ($message): ?><p class="auth-notice success"><?= e($message) ?></p><?php endif; ?>
  <?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
  <form method="post" class="stack-form">
    <?= csrf_input() ?>
    <label>
      <span><?= e(t('email')) ?></span>
      <input type="email" name="email" autocomplete="email" required value="<?= e($email) ?>">
    </label>
    <label>
      <span><?= e(t('password')) ?></span>
      <span class="password-input">
        <input type="password" name="password" autocomplete="current-password" required data-password-input>
        <button type="button" class="password-toggle" aria-label="Show password" aria-pressed="false" data-password-toggle>
          Show
        </button>
      </span>
      <a class="forgot-password-link" href="forgot-password.php">Forgot password?</a>
    </label>
    <button class="button primary full" type="submit"><?= e(t('sign_in')) ?></button>
  </form>
  <p class="muted-line">New here? <a href="register.php"><?= e(t('create_account')) ?></a></p>
</section>
<?php render_footer(); ?>

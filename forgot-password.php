<?php
require_once __DIR__ . '/includes/config.php';

// Send logged-in users to the home page.
if (current_user()) {
    redirect('index.php');
}

// Set the starting form values.
$email = '';
$error = null;
$submitted = false;

// Check the email and accept the request.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $submitted = true;
    }
}

// Show the form or success message.
render_header('Forgot password');
?>
<!-- Password reset form -->
<section class="auth-panel">
  <div class="auth-heading">
    <h1>Reset your password</h1>
    <p>Enter the email address connected to your account.</p>
  </div>

  <?php if ($submitted): ?>
    <p class="auth-notice success">
      Your request was received. Please contact Slahpc support to complete the password reset.
    </p>
    <a class="button primary full" href="login.php">Back to sign in</a>
  <?php else: ?>
    <?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
    <form method="post" class="stack-form">
      <?= csrf_input() ?>
      <label>
        <span><?= e(t('email')) ?></span>
        <input type="email" name="email" autocomplete="email" required value="<?= e($email) ?>">
      </label>
      <button class="button primary full" type="submit">Request password reset</button>
    </form>
    <p class="muted-line"><a href="login.php">Back to sign in</a></p>
  <?php endif; ?>
</section>
<?php render_footer(); ?>

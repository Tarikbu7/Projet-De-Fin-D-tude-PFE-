<?php
require_once __DIR__ . '/includes/app.php';

if (current_user()) {
    redirect(current_user()['role'] === 'admin' ? 'admin.php' : 'dashboard.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([trim($_POST['email'] ?? '')]);
        $user = $stmt->fetch();
        if ($user && password_verify((string)($_POST['password'] ?? ''), $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            redirect($user['role'] === 'admin' ? 'admin.php' : 'dashboard.php');
        }
        $error = 'Invalid email or password.';
    } catch (Throwable $exception) {
        $error = t('setup_needed') . ' ' . $exception->getMessage();
    }
}

render_header(t('login'));
?>
<section class="auth-panel dashboard-card">
  <h1><?= e(t('login')) ?></h1>
  <?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
  <form method="post" class="stack-form">
    <label><?= e(t('email')) ?><input type="email" name="email" required value="admin@slahpc.com"></label>
    <label><?= e(t('password')) ?><input type="password" name="password" required value="admin123"></label>
    <button class="button primary full" type="submit"><?= e(t('sign_in')) ?></button>
  </form>
  <p class="muted-line"><?= e(t('no_account')) ?> <a href="register.php"><?= e(t('create_account')) ?></a></p>
</section>
<?php render_footer(); ?>

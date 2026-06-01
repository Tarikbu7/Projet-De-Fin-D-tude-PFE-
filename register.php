<?php
require_once __DIR__ . '/includes/app.php';

if (current_user()) {
    redirect('dashboard.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        if ($name === '' || $email === '' || strlen($password) < 6) {
            $error = 'Please enter a name, email, and password with at least 6 characters.';
        } else {
            $stmt = db()->prepare('INSERT INTO users (name, email, phone, address, password_hash, role) VALUES (?, ?, ?, ?, ?, "user")');
            $stmt->execute([$name, $email, trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? ''), password_hash($password, PASSWORD_DEFAULT)]);
            flash('Account created. Please login.');
            redirect('login.php');
        }
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

render_header(t('register'));
?>
<section class="auth-panel dashboard-card">
  <h1><?= e(t('register')) ?></h1>
  <?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
  <form method="post" class="stack-form">
    <label><?= e(t('name')) ?><input type="text" name="name" required></label>
    <label><?= e(t('email')) ?><input type="email" name="email" required></label>
    <label><?= e(t('phone')) ?><input type="tel" name="phone"></label>
    <label><?= e(t('address')) ?><input type="text" name="address"></label>
    <label><?= e(t('password')) ?><input type="password" name="password" required minlength="6"></label>
    <button class="button primary full" type="submit"><?= e(t('register')) ?></button>
  </form>
  <p class="muted-line"><a href="login.php"><?= e(t('login')) ?></a></p>
</section>
<?php render_footer(); ?>

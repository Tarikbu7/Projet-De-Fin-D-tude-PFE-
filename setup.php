<?php
require_once __DIR__ . '/includes/app.php';

// Only run setup on this computer.
$remoteAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if (!in_array($remoteAddress, ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Setup can only be run from this computer.');
}

// Set the starting setup values.
$messages = [];
$errors = [];
$adminExists = false;
$databaseReady = false;

// Create or update the database.
try {
    $pdo = db(false);
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $pdo->exec('USE `' . DB_NAME . '`');

    // User accounts.
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(120) NOT NULL,
        email VARCHAR(180) NOT NULL UNIQUE,
        phone VARCHAR(30) DEFAULT NULL,
        address VARCHAR(255) DEFAULT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('user','admin') NOT NULL DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    // Services and prices.
    $pdo->exec("CREATE TABLE IF NOT EXISTS services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL UNIQUE,
        base_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        active TINYINT(1) NOT NULL DEFAULT 1,
        description TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    // Customer repairs.
    $pdo->exec("CREATE TABLE IF NOT EXISTS appointments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        service_type VARCHAR(100) NOT NULL,
        address VARCHAR(255) DEFAULT NULL,
        problem_details TEXT DEFAULT NULL,
        price DECIMAL(10,2) DEFAULT NULL,
        status ENUM('Pending','Accepted','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT appointments_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    // Old repair requests.
    $pdo->exec("CREATE TABLE IF NOT EXISTS repair_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(120) NOT NULL,
        family_name VARCHAR(120) NOT NULL,
        phone VARCHAR(60) NOT NULL,
        email VARCHAR(190) NOT NULL,
        problem TEXT NOT NULL,
        price DECIMAL(10,2) DEFAULT NULL,
        status ENUM('Pending','Accepted','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    // Products.
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        description TEXT DEFAULT NULL,
        price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        stock INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    // Orders.
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        status ENUM('Pending','Accepted','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT orders_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT orders_product_fk FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    // Messages.
    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        subject VARCHAR(200) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT messages_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    // Bills.
    $pdo->exec("CREATE TABLE IF NOT EXISTS invoices (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        appointment_id INT DEFAULT NULL,
        amount DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT invoices_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    // Custom PC requests.
    $pdo->exec("CREATE TABLE IF NOT EXISTS pc_builds (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        budget DECIMAL(10,2) NOT NULL,
        purpose VARCHAR(200) NOT NULL,
        details TEXT DEFAULT NULL,
        status ENUM('Pending','Accepted','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT pc_builds_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    // Customer reviews.
    ensure_reviews_table($pdo);

    // Update an old database.
    ensure_database_column($pdo, 'appointments', 'address', 'VARCHAR(255) NULL AFTER service_type');
    ensure_database_column($pdo, 'appointments', 'price', 'DECIMAL(10,2) NULL AFTER problem_details');
    ensure_database_column($pdo, 'services', 'base_price', 'DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER name');
    ensure_database_column($pdo, 'services', 'active', 'TINYINT(1) NOT NULL DEFAULT 1 AFTER base_price');

    $pdo->exec("ALTER TABLE appointments MODIFY status ENUM('Pending','Confirmed','Accepted','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending'");
    $pdo->exec("UPDATE appointments SET status = 'Accepted' WHERE status = 'Confirmed'");
    $pdo->exec("ALTER TABLE appointments MODIFY status ENUM('Pending','Accepted','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending'");

    if (database_column_exists($pdo, 'services', 'price')) {
        $pdo->exec('UPDATE services SET base_price = price WHERE price IS NOT NULL AND base_price = 0');
    }

    // Add missing services.
    $findService = $pdo->prepare('SELECT id FROM services WHERE name = ? LIMIT 1');
    $insertService = $pdo->prepare(
        'INSERT INTO services (name, base_price, active, description) VALUES (?, ?, 1, ?)'
    );
    $defaultServices = [
        ['Hardware repair', 0, 'Price confirmed after diagnosis.'],
        ['Software repair', 150, 'Windows, software, and performance support.'],
        ['Wi-Fi / printer / setup', 120, 'Network, printer, and device setup.'],
        ['Backup or data transfer', 200, 'Backup and file transfer services.'],
        ['Diagnostic visit', 100, 'Computer inspection and diagnosis.'],
    ];
    foreach ($defaultServices as $defaultService) {
        $findService->execute([$defaultService[0]]);
        if (!$findService->fetchColumn()) {
            $insertService->execute($defaultService);
        }
    }

    $databaseReady = true;
    $adminExists = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn() > 0;

    // Create the first admin.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();

        if (($_POST['action'] ?? '') !== 'create_admin' || $adminExists) {
            http_response_code(400);
            exit('Administrator creation is not available.');
        }

        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($password) < 12) {
            $errors[] = 'Enter a name, a valid email, and a password of at least 12 characters.';
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO users (name, email, password_hash, role)
                 VALUES (?, ?, ?, 'admin')"
            );
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $adminExists = true;
            $messages[] = 'Administrator account created.';
        }
    }

    $messages[] = 'Database and tables are ready.';
} catch (Throwable $exception) {
    $errors[] = $exception->getMessage();
}

// Show setup results and the admin form.
render_header('Setup');
?>
<!-- Setup results and admin form -->
<section class="auth-panel">
  <div class="auth-heading">
    <h1>Database setup</h1>
    <p>Create or update the Slahpc database for this version of the application.</p>
  </div>

  <?php foreach ($messages as $message): ?>
    <p class="notice success"><?= e($message) ?></p>
  <?php endforeach; ?>

  <?php foreach ($errors as $error): ?>
    <p class="notice error"><?= e($error) ?></p>
  <?php endforeach; ?>

  <?php if ($databaseReady && !$adminExists): ?>
    <form method="post" class="stack-form">
      <?= csrf_input() ?>
      <input type="hidden" name="action" value="create_admin">
      <label>
        <span>Administrator name</span>
        <input type="text" name="name" autocomplete="name" required>
      </label>
      <label>
        <span>Administrator email</span>
        <input type="email" name="email" autocomplete="email" required>
      </label>
      <label>
        <span>Administrator password</span>
        <span class="password-input">
          <input type="password" name="password" autocomplete="new-password" minlength="12" required data-password-input>
          <button type="button" class="password-toggle" aria-label="Show password" aria-pressed="false" data-password-toggle>
            Show
          </button>
        </span>
        <small>Use at least 12 characters.</small>
      </label>
      <button class="button primary full" type="submit">Create administrator</button>
    </form>
  <?php elseif ($databaseReady): ?>
    <a class="button primary full" href="login.php">Go to login</a>
  <?php endif; ?>
</section>
<?php render_footer(); ?>

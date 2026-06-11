<?php
require_once __DIR__ . '/includes/app.php';

$messages = [];
$errors   = [];

try {
    // Connect without selecting a database first to create it if needed
    $pdo = db(false);
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $pdo->exec('USE `' . DB_NAME . '`');
    $messages[] = 'Database "' . DB_NAME . '" is ready.';

    // Users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id              INT AUTO_INCREMENT PRIMARY KEY,
        name            VARCHAR(120)  NOT NULL,
        email           VARCHAR(180)  NOT NULL UNIQUE,
        phone           VARCHAR(30)   DEFAULT NULL,
        address         VARCHAR(255)  DEFAULT NULL,
        password_hash   VARCHAR(255)  NOT NULL,
        role            ENUM('user','admin') NOT NULL DEFAULT 'user',
        created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    $messages[] = 'Table "users" OK.';

    // Appointments table
    $pdo->exec("CREATE TABLE IF NOT EXISTS appointments (
        id              INT AUTO_INCREMENT PRIMARY KEY,
        user_id         INT           NOT NULL,
        service_type    VARCHAR(100)  NOT NULL,
        problem_details TEXT          DEFAULT NULL,
        price           DECIMAL(10,2) DEFAULT NULL,
        status          ENUM('Pending','Accepted','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
        created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT appointments_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    $messages[] = 'Table "appointments" OK.';

    // Products table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        name        VARCHAR(200)  NOT NULL,
        description TEXT          DEFAULT NULL,
        price       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        stock       INT           NOT NULL DEFAULT 0,
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    $messages[] = 'Table "products" OK.';

    // Orders table
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        user_id     INT           NOT NULL,
        product_id  INT           NOT NULL,
        quantity    INT           NOT NULL DEFAULT 1,
        total       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        status      ENUM('Pending','Accepted','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT orders_user_fk    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
        CONSTRAINT orders_product_fk FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    $messages[] = 'Table "orders" OK.';

    // Messages table
    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        user_id     INT           NOT NULL,
        subject     VARCHAR(200)  NOT NULL,
        message     TEXT          NOT NULL,
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT messages_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    $messages[] = 'Table "messages" OK.';

    // Services table
    $pdo->exec("CREATE TABLE IF NOT EXISTS services (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        name        VARCHAR(200)  NOT NULL,
        price       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        description TEXT          DEFAULT NULL,
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    $messages[] = 'Table "services" OK.';

    // Invoices table
    $pdo->exec("CREATE TABLE IF NOT EXISTS invoices (
        id              INT AUTO_INCREMENT PRIMARY KEY,
        user_id         INT           NOT NULL,
        appointment_id  INT           DEFAULT NULL,
        amount          DECIMAL(10,2) NOT NULL,
        created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT invoices_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    $messages[] = 'Table "invoices" OK.';

    // PC build requests
    $pdo->exec("CREATE TABLE IF NOT EXISTS pc_builds (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        user_id     INT           NOT NULL,
        budget      DECIMAL(10,2) NOT NULL,
        purpose     VARCHAR(200)  NOT NULL,
        details     TEXT          DEFAULT NULL,
        status      ENUM('Pending','Accepted','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT pc_builds_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    $messages[] = 'Table "pc_builds" OK.';

    // Reviews table
    ensure_reviews_table($pdo);
    $messages[] = 'Table "reviews" OK.';

    // Create default admin if none exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    if ((int)$stmt->fetchColumn() === 0) {
        $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'admin')")
            ->execute(['Admin', 'admin@slahpc.com', password_hash('admin123', PASSWORD_DEFAULT)]);
        $messages[] = 'Default admin created — email: admin@slahpc.com / password: admin123 (change it after first login!)';
    }

    $messages[] = '✅ Setup complete.';

} catch (Throwable $e) {
    $errors[] = $e->getMessage();
}

render_header('Setup');
?>
<section class="auth-panel">
  <h1>Database Setup</h1>
  <?php foreach ($messages as $msg): ?>
    <p class="notice success"><?= e($msg) ?></p>
  <?php endforeach; ?>
  <?php foreach ($errors as $err): ?>
    <p class="notice error"><?= e($err) ?></p>
  <?php endforeach; ?>
  <?php if (empty($errors)): ?>
    <a class="button primary" href="login.php">Go to login</a>
  <?php endif; ?>
</section>
<?php render_footer(); ?>

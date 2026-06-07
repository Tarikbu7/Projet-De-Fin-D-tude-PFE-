<?php
require_once __DIR__ . '/includes/app.php';

$error = null;
$done = false;

try {
    $pdo = db(false);
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $pdo->exec('USE `' . DB_NAME . '`');

    $schema = [
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            email VARCHAR(190) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('admin','user') NOT NULL DEFAULT 'user',
            phone VARCHAR(60) NULL,
            address VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS services (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            base_price DECIMAL(10,2) NOT NULL DEFAULT 0,
            active TINYINT(1) NOT NULL DEFAULT 1
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS appointments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            service_type VARCHAR(120) NOT NULL,
            preferred_date DATE NOT NULL,
            preferred_time VARCHAR(80) NOT NULL,
            address VARCHAR(255) NOT NULL,
            problem_details TEXT NOT NULL,
            price DECIMAL(10,2) NULL,
            status ENUM('Pending','Accepted','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS repair_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(120) NOT NULL,
            family_name VARCHAR(120) NOT NULL,
            phone VARCHAR(60) NOT NULL,
            email VARCHAR(190) NOT NULL,
            problem TEXT NOT NULL,
            price DECIMAL(10,2) NULL,
            status ENUM('Pending','Accepted','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
    ];

    foreach ($schema as $sql) {
        $pdo->exec($sql);
    }

    $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES ('Admin', 'admin@slahpc.com', ?, 'admin') ON DUPLICATE KEY UPDATE role = 'admin'");
    $stmt->execute([$adminHash]);

    $services = [
        [2, 'Software repair', 100], [3, 'Hardware repair', 0],
        [4, 'Wi-Fi / printer / setup', 60], [5, 'Backup or data transfer', 700],
    ];
    $stmt = $pdo->prepare('INSERT INTO services (id, name, base_price) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), base_price = VALUES(base_price)');
    foreach ($services as $service) {
        $stmt->execute($service);
    }

    $done = true;
} catch (Throwable $exception) {
    $error = $exception->getMessage();
}

render_header('Setup');
?>
<section class="dashboard-card">
  <h1>Slahpc setup</h1>
  <?php if ($done): ?>
    <p class="notice success">Database <strong>slah_pc</strong> is ready. Admin login: <strong>admin@slahpc.com</strong> / <strong>admin123</strong></p>
    <div class="dashboard-actions">
      <a class="button primary" href="login.php"><?= e(t('login')) ?></a>
      <a class="button light" href="index.php"><?= e(t('open_site')) ?></a>
    </div>
  <?php else: ?>
    <p class="notice error"><?= e($error) ?></p>
    <p>Start the server with: <code>php -c php.ini -S 127.0.0.1:8022</code></p>
  <?php endif; ?>
</section>
<?php render_footer(); ?>

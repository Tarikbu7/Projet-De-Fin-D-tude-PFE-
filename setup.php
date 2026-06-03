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
        "CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category VARCHAR(80) NOT NULL,
            name VARCHAR(140) NOT NULL,
            description TEXT NULL,
            price DECIMAL(10,2) NOT NULL DEFAULT 0,
            stock INT NOT NULL DEFAULT 0,
            active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS appointments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            service_type VARCHAR(120) NOT NULL,
            preferred_date DATE NOT NULL,
            preferred_time VARCHAR(80) NOT NULL,
            address VARCHAR(255) NOT NULL,
            problem_details TEXT NOT NULL,
            status ENUM('Pending','Confirmed','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
            admin_note TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            status ENUM('Pending','Confirmed','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
            total DECIMAL(10,2) NOT NULL DEFAULT 0,
            notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            unit_price DECIMAL(10,2) NOT NULL DEFAULT 0,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS pc_build_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            budget VARCHAR(80) NULL,
            purpose VARCHAR(160) NULL,
            details TEXT NOT NULL,
            status ENUM('Pending','Confirmed','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            subject VARCHAR(160) NOT NULL,
            body TEXT NOT NULL,
            status ENUM('Pending','Confirmed','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
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
            status ENUM('Pending','Confirmed','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS invoices (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            appointment_id INT NULL,
            order_id INT NULL,
            amount DECIMAL(10,2) NOT NULL DEFAULT 0,
            status ENUM('Pending','Confirmed','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
            note TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB"
    ];

    foreach ($schema as $sql) {
        $pdo->exec($sql);
    }

    $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES ('Admin', 'admin@slahpc.com', ?, 'admin') ON DUPLICATE KEY UPDATE role = 'admin'");
    $stmt->execute([$adminHash]);

    $services = [
        ['Diagnostic visit', 55], ['Software repair', 75], ['Hardware repair', 95],
        ['Wi-Fi / printer / setup', 80], ['Backup or data transfer', 90],
    ];
    $stmt = $pdo->prepare('INSERT IGNORE INTO services (id, name, base_price) VALUES (?, ?, ?)');
    foreach ($services as $index => $service) {
        $stmt->execute([$index + 1, $service[0], $service[1]]);
    }

    $products = [
        ['CPU', 'Intel Core i5 / Ryzen 5 class CPU', 'Balanced processor for gaming, school, and office PCs.', 160, 8],
        ['RAM', '16GB DDR4 / DDR5 RAM kit', 'Reliable memory upgrade for faster multitasking.', 55, 15],
        ['MOBO', 'ATX / mATX motherboard', 'Motherboard options matched to your CPU and build.', 120, 6],
        ['SSD', '1TB NVMe SSD', 'Fast storage upgrade for Windows, games, and work files.', 75, 12],
        ['GPU', 'Gaming graphics card', 'GPU options based on budget and performance target.', 280, 4],
        ['Power Supply', '650W certified PSU', 'Stable power supply for new builds and upgrades.', 85, 7],
    ];
    $stmt = $pdo->prepare('INSERT IGNORE INTO products (id, category, name, description, price, stock) VALUES (?, ?, ?, ?, ?, ?)');
    foreach ($products as $index => $product) {
        $stmt->execute([$index + 1, ...$product]);
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

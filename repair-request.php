<?php
require_once __DIR__ . '/includes/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php#contact');
}

$firstName = trim($_POST['first_name'] ?? '');
$familyName = trim($_POST['family_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$problem = trim($_POST['problem'] ?? '');

if ($firstName === '' || $familyName === '' || $phone === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $problem === '') {
    redirect('index.php?repair=error#contact');
}

try {
    $pdo = db();
    $pdo->exec("CREATE TABLE IF NOT EXISTS repair_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(120) NOT NULL,
        family_name VARCHAR(120) NOT NULL,
        phone VARCHAR(60) NOT NULL,
        email VARCHAR(190) NOT NULL,
        problem TEXT NOT NULL,
        status ENUM('Pending','Confirmed','In progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    $stmt = $pdo->prepare('INSERT INTO repair_requests (first_name, family_name, phone, email, problem) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$firstName, $familyName, $phone, $email, $problem]);
} catch (Throwable) {
    redirect('index.php?repair=error#contact');
}

redirect('index.php?repair=sent#contact');

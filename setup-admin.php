<?php
require_once __DIR__ . '/includes/config.php';

/**
 * Configuration for the admin account.
 * Change these values as needed.
 */
$admin_email = 'admin@admin.com';
$admin_password = 'admin_password_123';
$admin_name = 'Administrator';

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Admin Setup</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; max-width: 600px; margin: 0 auto; }
        .success { color: #28a745; background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .error { color: #721c24; background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .warning { color: #856404; background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; border-radius: 4px; }
        code { background: #eee; padding: 2px 4px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Admin Account Setup</h1>";

try {
    $pdo = db();
    
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$admin_email]);
    $user = $stmt->fetch();

    $password_hash = password_hash($admin_password, PASSWORD_DEFAULT);

    if ($user) {
        // Update existing user to admin and reset password
        $stmt = $pdo->prepare("UPDATE users SET name = ?, password_hash = ?, role = 'admin' WHERE id = ?");
        $stmt->execute([$admin_name, $password_hash, $user['id']]);
        echo "<div class='success'>Admin account <strong>updated</strong> successfully!</div>";
    } else {
        // Create new admin account
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, address, password_hash, role) VALUES (?, ?, '', '', ?, 'admin')");
        $stmt->execute([$admin_name, $admin_email, $password_hash]);
        echo "<div class='success'>Admin account <strong>created</strong> successfully!</div>";
    }
    
    echo "<ul>
        <li><strong>Name:</strong> " . htmlspecialchars($admin_name) . "</li>
        <li><strong>Email:</strong> <code>" . htmlspecialchars($admin_email) . "</code></li>
        <li><strong>Password:</strong> <code>" . htmlspecialchars($admin_password) . "</code></li>
    </ul>";
    
    echo "<div class='warning'>
        <strong>SECURITY WARNING:</strong> Please delete this file (<code>setup-admin.php</code>) from your server immediately after use to prevent unauthorized access.
    </div>";

} catch (PDOException $e) {
    echo "<div class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</body>
</html>";

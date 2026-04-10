<?php
require 'db.php';

try {
    $sql = "CREATE DATABASE IF NOT EXISTS fdl_db";
    $conn->exec($sql);
    $conn->exec("USE fdl_db");

    $sql = "CREATE TABLE IF NOT EXISTS admin_users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);

    // Auto-seed an admin account: admin / admin123
    $hashed_password = password_hash("admin123", PASSWORD_DEFAULT);
    $stmt = $conn->prepare("SELECT COUNT(*) FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    if($stmt->fetchColumn() == 0) {
        $insert_stmt = $conn->prepare("INSERT INTO admin_users (username, password) VALUES ('admin', :pass)");
        $insert_stmt->execute(['pass' => $hashed_password]);
        echo "Admin user created (admin / admin123)\n";
    }

    $sql = "CREATE TABLE IF NOT EXISTS games (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        game_type ENUM('Datewise', 'Timewise') DEFAULT 'Datewise',
        interval_mins INT DEFAULT 30,
        logo_path VARCHAR(255),
        last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);

    $sql = "CREATE TABLE IF NOT EXISTS results (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        game_id INT(6) UNSIGNED,
        lock_date DATE NOT NULL,
        lock_time TIME NOT NULL,
        lock_value VARCHAR(50) NOT NULL,
        last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
    )";
    $conn->exec($sql);

    echo "Done.\n";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

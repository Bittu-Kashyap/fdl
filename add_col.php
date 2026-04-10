<?php
require 'db.php';
try {
    $conn->exec("ALTER TABLE games ADD COLUMN time_slot VARCHAR(100) DEFAULT NULL AFTER game_type");
    echo "Column added successfully.\n";
}
catch (PDOException $e) {
    if ($e->getCode() == '42S21') {
        echo "Column already exists.\n";
    }
    else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>

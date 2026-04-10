<?php
require 'db.php';
$stmt = $conn->query("SELECT NOW()");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt2 = $conn->query("SELECT CURRENT_TIMESTAMP");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
echo "PHP Time: " . date('Y-m-d H:i:s') . "\n";
?>

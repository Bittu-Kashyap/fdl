<?php
require 'db.php';
$stmt = $conn->query("SELECT * FROM games");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt = $conn->query("SELECT * FROM results");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>

<?php
// config/db.php
require_once __DIR__ . '/../classes/Database.php';

$dsn = "mysql:host=localhost;dbname=training_db;charset=utf8mb4";
$user = "root";
$pass = "";

// TODO: obtain the single shared PDO connection
// Hint: $db = Database::getInstance($dsn, $user, $pass);
try {
    $db = Database::getInstance($dsn, $user, $pass);
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}

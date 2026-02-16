<?php
$host = 'localhost';
$db   = 'u868991478_scrum_poker';
$user = 'u868991478_scrum_poker';
$pass = 'xxxx';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    exit('Błąd połączenia z bazą: ' . $e->getMessage());
}

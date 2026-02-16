<?php
require 'db.php';
session_start();
$name = $_SESSION['player_name'] ?? null;
$vote = $_POST['vote'] ?? null;

if ($name && $vote !== null) {
    $stmt = $pdo->prepare("UPDATE players SET vote = ? WHERE name = ?");
    $stmt->execute([$vote, $name]);
}

header("Location: index.php");
exit;

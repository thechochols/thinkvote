<?php
require 'db.php';
$pdo->query("UPDATE players SET vote = NULL");
$pdo->query("UPDATE settings SET reveal = FALSE WHERE id = 1");
header("Location: index.php");
exit;

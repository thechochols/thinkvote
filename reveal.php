<?php
require 'db.php';
$pdo->query("UPDATE settings SET reveal = TRUE WHERE id = 1");
header("Location: index.php");
exit;

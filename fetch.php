<?php
require 'db.php';

$players = $pdo->query("SELECT name, vote FROM players")->fetchAll(PDO::FETCH_ASSOC);
$reveal = $pdo->query("SELECT reveal FROM settings WHERE id = 1")->fetchColumn();

// Określ kolejność sortowania głosów:
$order = ['☕','?','0','½','1','2','3','5','8','13','20','40','100'];

// Jeśli karty mają być odkryte — sortuj:
if ($reveal) {
    usort($players, function ($a, $b) use ($order) {
        // Gracze bez głosu (null) idą na koniec
        if ($a['vote'] === null && $b['vote'] === null) return 0;
        if ($a['vote'] === null) return 1;
        if ($b['vote'] === null) return -1;

        // Sortowanie wg zdefiniowanej kolejności
        $aIndex = array_search($a['vote'], $order);
        $bIndex = array_search($b['vote'], $order);

        return $aIndex <=> $bIndex;
    });
}

echo json_encode([
    'players' => $players,
    'reveal' => (bool)$reveal
]);

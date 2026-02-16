<?php
require 'db.php';

$players = $pdo->query("SELECT name, vote FROM players")->fetchAll(PDO::FETCH_ASSOC);
$reveal = $pdo->query("SELECT reveal FROM settings WHERE id = 1")->fetchColumn();

// Określ kolejność sortowania głosów:
$order = ['☕','?','0','½','1','2','3','5','8','13','20','40','100'];

// Mapa wartości do liczb (do obliczeń)
$numericMap = [
    '0' => 0, '½' => 0.5, '1' => 1, '2' => 2, '3' => 3,
    '5' => 5, '8' => 8, '13' => 13, '20' => 20, '40' => 40, '100' => 100
];

// Jeśli karty mają być odkryte — sortuj i oblicz sugestię:
$suggestion = null;

if ($reveal) {
    usort($players, function ($a, $b) use ($order) {
        if ($a['vote'] === null && $b['vote'] === null) return 0;
        if ($a['vote'] === null) return 1;
        if ($b['vote'] === null) return -1;

        $aIndex = array_search($a['vote'], $order);
        $bIndex = array_search($b['vote'], $order);

        return $aIndex <=> $bIndex;
    });

    // Oblicz sugerowaną liczbę story pointów
    $numericVotes = [];
    foreach ($players as $p) {
        if ($p['vote'] !== null && isset($numericMap[$p['vote']])) {
            $numericVotes[] = $numericMap[$p['vote']];
        }
    }

    $totalVoters = count($numericVotes);

    if ($totalVoters >= 3) {
        // Odrzuć min i max
        sort($numericVotes);
        $trimmed = array_slice($numericVotes, 1, -1);
        $avg = array_sum($trimmed) / count($trimmed);

        // Zaokrąglij do najbliższej wartości z sekwencji Fibonacciego
        $fibValues = [0, 0.5, 1, 2, 3, 5, 8, 13, 20, 40, 100];
        $closest = $fibValues[0];
        $closestDiff = abs($avg - $closest);
        foreach ($fibValues as $fv) {
            $diff = abs($avg - $fv);
            if ($diff < $closestDiff) {
                $closestDiff = $diff;
                $closest = $fv;
            }
        }

        // Zamień z powrotem na etykietę
        $reverseMap = array_flip($numericMap);
        $closestLabel = $reverseMap[$closest] ?? (string)$closest;

        $suggestion = [
            'value' => $closestLabel,
            'average' => round($avg, 1),
            'totalVoters' => $totalVoters,
            'trimmedCount' => count($trimmed),
            'min' => $numericVotes[0],
            'max' => end($numericVotes),
        ];
    } elseif ($totalVoters === 2) {
        // Przy 2 głosach — średnia bez odrzucania
        $avg = array_sum($numericVotes) / $totalVoters;
        $fibValues = [0, 0.5, 1, 2, 3, 5, 8, 13, 20, 40, 100];
        $closest = $fibValues[0];
        $closestDiff = abs($avg - $closest);
        foreach ($fibValues as $fv) {
            $diff = abs($avg - $fv);
            if ($diff < $closestDiff) {
                $closestDiff = $diff;
                $closest = $fv;
            }
        }
        $reverseMap = array_flip($numericMap);
        $closestLabel = $reverseMap[$closest] ?? (string)$closest;

        $suggestion = [
            'value' => $closestLabel,
            'average' => round($avg, 1),
            'totalVoters' => $totalVoters,
            'trimmedCount' => $totalVoters,
            'min' => null,
            'max' => null,
        ];
    } elseif ($totalVoters === 1) {
        $reverseMap = array_flip($numericMap);
        $suggestion = [
            'value' => $reverseMap[$numericVotes[0]] ?? (string)$numericVotes[0],
            'average' => $numericVotes[0],
            'totalVoters' => 1,
            'trimmedCount' => 1,
            'min' => null,
            'max' => null,
        ];
    }
}

echo json_encode([
    'players' => $players,
    'reveal' => (bool)$reveal,
    'suggestion' => $suggestion
]);

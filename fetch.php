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

    // Zbierz głosy liczbowe (oryginalne etykiety)
    $validVotes = [];
    foreach ($players as $p) {
        if ($p['vote'] !== null && isset($numericMap[$p['vote']])) {
            $validVotes[] = $p['vote'];
        }
    }

    $totalVoters = count($validVotes);

    if ($totalVoters >= 3) {
        // Sortuj wg kolejności w sekwencji
        usort($validVotes, function ($a, $b) use ($order) {
            return array_search($a, $order) <=> array_search($b, $order);
        });

        // Odrzuć min i max
        $trimmed = array_slice($validVotes, 1, -1);

        // Znajdź modę (najczęstszą wartość) w trimmed
        $counts = array_count_values($trimmed);
        arsort($counts);
        $maxCount = max($counts);
        $modes = array_keys(array_filter($counts, fn($c) => $c === $maxCount));

        if (count($modes) === 1) {
            // Jedna wyraźna moda
            $suggestedLabel = $modes[0];
            $method = 'mode';
        } else {
            // Remis — bierz medianę z trimmed (górną przy parzystej liczbie)
            $count = count($trimmed);
            if ($count % 2 === 0) {
                // Parzysta liczba — bierz wyższy ze środkowych elementów
                $medianIndex = $count / 2;
            } else {
                // Nieparzysta — środkowy element
                $medianIndex = intdiv($count, 2);
            }
            $suggestedLabel = $trimmed[$medianIndex];
            $method = 'median';
        }

        // Oblicz średnią do wyświetlenia
        $numericTrimmed = array_map(fn($v) => $numericMap[$v], $trimmed);
        $avg = array_sum($numericTrimmed) / count($numericTrimmed);

        $suggestion = [
            'value' => $suggestedLabel,
            'method' => $method,
            'average' => round($avg, 1),
            'totalVoters' => $totalVoters,
            'trimmedCount' => count($trimmed),
            'trimmedVotes' => $trimmed,
            'min' => $numericMap[$validVotes[0]],
            'max' => $numericMap[end($validVotes)],
        ];
    } elseif ($totalVoters === 2) {
        // 2 głosy — jeśli takie same, to wynik; jeśli różne, wyższa z dwóch
        usort($validVotes, function ($a, $b) use ($order) {
            return array_search($a, $order) <=> array_search($b, $order);
        });

        if ($validVotes[0] === $validVotes[1]) {
            $suggestedLabel = $validVotes[0];
        } else {
            // Przy 2 różnych głosach — bezpieczniej wybrać wyższy
            $suggestedLabel = $validVotes[1];
        }

        $numericValues = array_map(fn($v) => $numericMap[$v], $validVotes);
        $avg = array_sum($numericValues) / count($numericValues);

        $suggestion = [
            'value' => $suggestedLabel,
            'method' => 'pair',
            'average' => round($avg, 1),
            'totalVoters' => $totalVoters,
            'trimmedCount' => $totalVoters,
            'trimmedVotes' => $validVotes,
            'min' => null,
            'max' => null,
        ];
    } elseif ($totalVoters === 1) {
        $suggestion = [
            'value' => $validVotes[0],
            'method' => 'single',
            'average' => $numericMap[$validVotes[0]],
            'totalVoters' => 1,
            'trimmedCount' => 1,
            'trimmedVotes' => $validVotes,
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

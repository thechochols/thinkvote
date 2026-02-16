<?php
require 'db.php';
session_start();

// Obsługa wyboru imienia
if (isset($_POST['name'])) {
    $_SESSION['player_name'] = $_POST['name'];
}

$name = $_SESSION['player_name'] ?? null;

// Jeśli imię nie wybrane – pokaż ekran wyboru
if (!$name) {
    $players = $pdo->query("SELECT name FROM players ORDER BY id")->fetchAll();
    ?>
    <!DOCTYPE html>
    <html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Wybór gracza</title>
        <link rel="apple-touch-icon" sizes="180x180" href="scrum/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/scrum/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/scrum/favicon-16x16.png">
        <link rel="manifest" href="/scrum/site.webmanifest">
        <style>
            body {
                font-family: sans-serif;
                background: #f4f4f4;
                text-align: center;
                padding: 2rem;
            }
            .player-grid {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
                margin-top: 2rem;
            }
            .player-card {
                background: white;
                border: 2px solid #007BFF;
                color: #007BFF;
                padding: 1rem 2rem;
                border-radius: 12px;
                font-size: 1.2rem;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .player-card:hover {
                background: #007BFF;
                color: white;
            }
            h2 {
                color: #333;
            }
        </style>
    </head>
    <body>
        <h2>🧑‍💻 Wybierz swoje imię</h2>
        <form method="post">
            <div class="player-grid">
                <?php foreach ($players as $p): ?>
                    <button class="player-card" type="submit" name="name" value="<?= htmlspecialchars($p['name']) ?>">
                        <?= htmlspecialchars($p['name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </form>
    </body>
    </html>
    <?php exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>ThinkVote</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            background: #f7f7f7;
            padding: 2rem;
        }

        h1, h2, h3 {
            margin-bottom: 1rem;
        }

        .vote-btns {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .vote-btns form {
            display: inline-block;
        }

        .card-button {
            position: relative;
            width: 80px;
            height: 128px;
            background: white;
            color: #007bff;
            border: 2px solid #007bff;
            border-radius: 12px;
            font-size: 2rem;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: background 0.2s ease, color 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .card-button:hover {
            background: #007bff;
            color: white;
        }

        .card-button .corner {
            position: absolute;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .card-button .top-left {
            top: 6px;
            left: 8px;
        }

        .card-button .bottom-right {
            bottom: 6px;
            right: 8px;
            transform: rotate(180deg);
        }

        .card-button .center {
            font-size: 1.8rem;
        }

        .card {
            display: inline-block;
            background: white;
            padding: 1rem;
            margin: 0.5rem;
            border-radius: 10px;
            min-width: 80px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .own {
            border: 2px solid #007BFF;
        }

        .actions {
            margin-bottom: 2rem;
        }

        .actions button {
            margin: 0.5rem;
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
            background: white;
            color: #28a745;
            border: 2px solid #28a745;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .actions button:hover {
            background: #28a745;
            color: white;
        }

        .actions .reset {
            color: #dc3545;
            border-color: #dc3545;
        }

        .actions .reset:hover {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <h1>🂠 ThinkVote</h1></br>
    <h2>Pomyśl i zagłosuj, <?= htmlspecialchars($name) ?></h2>

    <div class="vote-btns">
        <?php foreach (['☕','?','0','½','1','2','3','5','8','13','20','40','100'] as $val): ?>
            <form method="POST" action="vote.php">
                <input type="hidden" name="vote" value="<?= $val ?>">
                <button class="card-button">
                    <span class="corner top-left"><?= $val ?></span>
                    <span class="center"><?= $val ?></span>
                    <span class="corner bottom-right"><?= $val ?></span>
                </button>
            </form>
        <?php endforeach; ?>
    </div>

    <div class="actions">
        <form method="POST" action="reveal.php" style="display:inline;">
            <button class="action">👁️ Odkryj karty</button>
        </form>
        <form method="POST" action="reset.php" style="display:inline;">
            <button class="action reset">🔄 Resetuj</button>
        </form>
    </div>

    <h3>Głosy:</h3>
    <div id="players"></div>

    <script>
        const playerName = <?= json_encode($name) ?>;

        function fetchPlayers() {
            fetch('fetch.php')
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('players');
                    container.innerHTML = '';
                    data.players.forEach(player => {
                        const div = document.createElement('div');
                        div.className = 'card';
                        if (player.name === playerName) div.classList.add('own');

                        const name = document.createElement('strong');
                        name.textContent = player.name;
                        div.appendChild(name);
                        div.appendChild(document.createElement('br'));

                        const vote = document.createElement('div');
                        vote.style.fontSize = '2.2rem';
                        vote.style.paddingTop = '1rem';
                        vote.innerHTML = data.reveal
                            ? (player.vote ?? '-')
                            : (player.vote ? '🂠' : '⏳');
                        div.appendChild(vote);
                        container.appendChild(div);
                    });
                });
        }

        fetchPlayers();
        setInterval(fetchPlayers, 1000);
    </script>
</body>
</html>

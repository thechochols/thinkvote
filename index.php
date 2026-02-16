<?php
require 'db.php';
session_start();

// Obsługa wyboru imienia
if (isset($_POST['name'])) {
    $_SESSION['player_name'] = $_POST['name'];
}

$name = $_SESSION['player_name'] ?? null;

// Pobierz aktualny głos gracza
$currentVote = null;
if ($name) {
    $stmt = $pdo->prepare("SELECT vote FROM players WHERE name = ?");
    $stmt->execute([$name]);
    $currentVote = $stmt->fetchColumn();
}

// Jeśli imię nie wybrane – pokaż ekran wyboru
if (!$name) {
    $players = $pdo->query("SELECT name FROM players ORDER BY id")->fetchAll();
    ?>
    <!DOCTYPE html>
    <html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Wybór gracza — ThinkVote</title>
        <link rel="apple-touch-icon" sizes="180x180" href="scrum/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/scrum/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/scrum/favicon-16x16.png">
        <link rel="manifest" href="/scrum/site.webmanifest">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
                background: #fafafa;
                min-height: 100vh;
                text-align: center;
                padding: 3rem 1.5rem;
                color: #18181b;
            }

            h2 {
                font-size: 2rem;
                font-weight: 600;
                color: #18181b;
                margin-bottom: 0.5rem;
            }

            .subtitle {
                color: #71717a;
                font-size: 0.95rem;
                margin-bottom: 2.5rem;
            }

            .player-grid {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
                max-width: 700px;
                margin: 0 auto;
            }

            .player-card {
                background: white;
                border: 2px solid #e4e4e7;
                color: #3f3f46;
                padding: 1rem 2.2rem;
                border-radius: 14px;
                font-size: 1.15rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                animation: fadeInUp 0.5s ease backwards;
            }

            .player-card:hover {
                background: #e11d48;
                border-color: #e11d48;
                color: white;
                transform: translateY(-4px);
                box-shadow: 0 8px 25px rgba(225,29,72,0.2);
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    </head>
    <body>
        <h2>🧑‍💻 Wybierz swoje imię</h2>
        <p class="subtitle">Kliknij na swoje imię, aby dołączyć do sesji</p>
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ThinkVote</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            text-align: center;
            background: #fafafa;
            min-height: 100vh;
            padding: 2rem 1rem;
            color: #18181b;
        }

        h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
            color: #18181b;
            letter-spacing: 0.5px;
        }

        h2 {
            font-size: 1.15rem;
            font-weight: 400;
            color: #71717a;
            margin-bottom: 2rem;
        }

        h3 {
            font-size: 0.85rem;
            font-weight: 600;
            color: #a1a1aa;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        /* === KARTY GŁOSOWANIA === */
        .vote-btns {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.6rem;
            margin-bottom: 2rem;
            padding: 0.5rem 1rem;
        }

        .vote-btns form {
            flex-shrink: 0;
        }

        .card-button {
            position: relative;
            width: 80px;
            height: 120px;
            background: white;
            color: #e11d48;
            border: 2px solid #e4e4e7;
            border-radius: 12px;
            font-size: 2rem;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .card-button:hover {
            border-color: #e11d48;
            color: #e11d48;
            transform: translateY(-6px);
            box-shadow: 0 10px 25px rgba(225,29,72,0.15);
        }

        .card-button.selected {
            background: #e11d48;
            border-color: #e11d48;
            color: white;
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(225,29,72,0.3);
        }

        .card-button.selected .corner {
            color: rgba(255,255,255,0.6);
        }

        .card-button:active {
            transform: translateY(-1px) scale(0.98);
        }

        .card-button .corner {
            position: absolute;
            font-size: 0.65rem;
            font-weight: 700;
            color: #a1a1aa;
            transition: color 0.25s ease;
        }

        .card-button:hover .corner {
            color: #e11d48;
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
            font-size: 1.7rem;
            font-weight: 500;
        }

        /* === PRZYCISKI AKCJI === */
        .actions {
            margin-bottom: 2rem;
            display: flex;
            justify-content: center;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .actions form {
            display: inline-block;
        }

        .actions button {
            padding: 0.7rem 1.8rem;
            font-size: 0.95rem;
            font-weight: 500;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .actions .action {
            background: #f0fdf4;
            border: 2px solid #bbf7d0;
            color: #059669;
        }

        .actions .action:hover {
            background: #059669;
            border-color: #059669;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(5,150,105,0.25);
        }

        .actions .reset {
            background: #fff1f2;
            border: 2px solid #fecdd3;
            color: #dc2626;
        }

        .actions .reset:hover {
            background: #dc2626;
            border-color: #dc2626;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220,38,38,0.25);
        }

        /* === KARTY GRACZY === */
        #players {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.8rem;
            max-width: 900px;
            margin: 1rem auto 0;
        }

        .card {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            background: white;
            padding: 1rem 1.3rem;
            border-radius: 14px;
            min-width: 90px;
            border: 2px solid #e4e4e7;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .card.new {
            animation: fadeInUp 0.4s ease backwards;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.07);
        }

        .own {
            border-color: #e11d48;
            box-shadow: 0 0 0 3px rgba(225,29,72,0.08), 0 1px 3px rgba(0,0,0,0.05);
        }

        .player-name {
            color: #71717a;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .player-vote {
            font-size: 2rem;
            padding-top: 0.6rem;
            color: #18181b;
            transition: all 0.4s ease;
        }

        .player-vote.revealed {
            animation: flipIn 0.5s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes flipIn {
            0% {
                transform: rotateY(90deg);
                opacity: 0;
            }
            100% {
                transform: rotateY(0);
                opacity: 1;
            }
        }

        .divider {
            width: 50px;
            height: 2px;
            background: #e4e4e7;
            margin: 1.2rem auto;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <h1>🂠 ThinkVote</h1>
    <h2>Pomyśl i zagłosuj, <?= htmlspecialchars($name) ?></h2>

    <div class="vote-btns">
        <?php foreach (['☕','?','0','½','1','2','3','5','8','13','20','40','100'] as $val): ?>
            <form method="POST" action="vote.php">
                <input type="hidden" name="vote" value="<?= $val ?>">
                <button class="card-button<?= ($currentVote === $val) ? ' selected' : '' ?>">
                    <span class="corner top-left"><?= $val ?></span>
                    <span class="center"><?= $val ?></span>
                    <span class="corner bottom-right"><?= $val ?></span>
                </button>
            </form>
        <?php endforeach; ?>
    </div>

    <div class="actions">
        <form method="POST" action="reveal.php">
            <button class="action">👁️ Odkryj karty</button>
        </form>
        <form method="POST" action="reset.php">
            <button class="action reset">🔄 Resetuj</button>
        </form>
    </div>

    <h3>Głosy</h3>
    <div class="divider"></div>
    <div id="players"></div>

    <script>
        const playerName = <?= json_encode($name) ?>;

        let prevDataJSON = '';
        let prevReveal = false;
        let isFirstLoad = true;

        function getVoteDisplay(player, reveal) {
            if (reveal) return player.vote ?? '–';
            return player.vote ? '🂠' : '⏳';
        }

        function buildCard(player, index, reveal, justRevealed, animate) {
            const div = document.createElement('div');
            div.className = 'card';
            div.dataset.name = player.name;
            if (animate) {
                div.classList.add('new');
                div.style.animationDelay = (index * 0.06) + 's';
            }
            if (player.name === playerName) div.classList.add('own');

            const nameEl = document.createElement('strong');
            nameEl.className = 'player-name';
            nameEl.textContent = player.name;
            div.appendChild(nameEl);

            const voteEl = document.createElement('div');
            voteEl.className = 'player-vote';
            if (justRevealed) voteEl.classList.add('revealed');
            voteEl.innerHTML = getVoteDisplay(player, reveal);
            div.appendChild(voteEl);

            return div;
        }

        function updateSelectedCard(data) {
            const myData = data.players.find(p => p.name === playerName);
            const myVote = myData ? myData.vote : null;
            document.querySelectorAll('.card-button').forEach(btn => {
                const form = btn.closest('form');
                const input = form.querySelector('input[name="vote"]');
                if (input && input.value === myVote) {
                    btn.classList.add('selected');
                } else {
                    btn.classList.remove('selected');
                }
            });
        }

        function updatePlayers(data) {
            const newJSON = JSON.stringify(data);

            updateSelectedCard(data);

            if (newJSON === prevDataJSON) return;

            const container = document.getElementById('players');
            const justRevealed = data.reveal && !prevReveal;
            const justHidden = !data.reveal && prevReveal;

            if (isFirstLoad) {
                container.innerHTML = '';
                data.players.forEach((player, index) => {
                    container.appendChild(buildCard(player, index, data.reveal, false, true));
                });
                isFirstLoad = false;
            } else {
                const existingCards = container.querySelectorAll('.card');
                const existingMap = {};
                existingCards.forEach(card => {
                    existingMap[card.dataset.name] = card;
                });

                const newNames = data.players.map(p => p.name);
                const existingNames = Object.keys(existingMap);

                const playersChanged = newNames.length !== existingNames.length ||
                    newNames.some((name, i) => name !== existingNames[i]);

                if (playersChanged) {
                    container.innerHTML = '';
                    data.players.forEach((player, index) => {
                        const isNew = !existingMap[player.name];
                        container.appendChild(buildCard(player, index, data.reveal, justRevealed, isNew));
                    });
                } else {
                    data.players.forEach(player => {
                        const card = existingMap[player.name];
                        if (!card) return;

                        const voteEl = card.querySelector('.player-vote');
                        const newVoteHTML = getVoteDisplay(player, data.reveal);

                        if (voteEl.innerHTML !== newVoteHTML) {
                            voteEl.innerHTML = newVoteHTML;

                            if (justRevealed) {
                                voteEl.classList.remove('revealed');
                                void voteEl.offsetWidth;
                                voteEl.classList.add('revealed');
                            }
                        }

                        if (justHidden) {
                            voteEl.classList.remove('revealed');
                        }
                    });
                }
            }

            prevDataJSON = newJSON;
            prevReveal = data.reveal;
        }

        function fetchPlayers() {
            fetch('fetch.php')
                .then(res => res.json())
                .then(updatePlayers)
                .catch(() => {});
        }

        fetchPlayers();
        setInterval(fetchPlayers, 2000);
    </script>
</body>
</html>

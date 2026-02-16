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
                background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
                min-height: 100vh;
                text-align: center;
                padding: 3rem 1.5rem;
                color: white;
            }

            h2 {
                font-size: 2rem;
                font-weight: 300;
                color: white;
                text-shadow: 0 2px 10px rgba(0,0,0,0.3);
                margin-bottom: 0.5rem;
            }

            .subtitle {
                color: rgba(255,255,255,0.5);
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
                background: rgba(255,255,255,0.08);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid rgba(255,255,255,0.15);
                color: white;
                padding: 1rem 2.2rem;
                border-radius: 14px;
                font-size: 1.15rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                animation: fadeInUp 0.5s ease backwards;
            }

            .player-card:hover {
                background: rgba(99,102,241,0.35);
                border-color: rgba(99,102,241,0.6);
                transform: translateY(-4px);
                box-shadow: 0 8px 30px rgba(99,102,241,0.3);
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
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
            padding: 2rem 1rem;
            color: white;
        }

        h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
            text-shadow: 0 0 30px rgba(99,102,241,0.4), 0 2px 10px rgba(0,0,0,0.3);
            letter-spacing: 1px;
        }

        h2 {
            font-size: 1.2rem;
            font-weight: 300;
            color: rgba(255,255,255,0.6);
            margin-bottom: 2.5rem;
        }

        h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: rgba(255,255,255,0.8);
            margin-bottom: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            display: inline-block;
        }

        h3::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 2px;
            background: rgba(99,102,241,0.6);
            border-radius: 2px;
        }

        .vote-btns {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.8rem;
            margin-bottom: 2.5rem;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        .vote-btns form {
            display: inline-block;
        }

        .card-button {
            position: relative;
            width: 76px;
            height: 120px;
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: rgba(255,255,255,0.85);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 14px;
            font-size: 2rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .card-button:hover {
            background: rgba(99,102,241,0.45);
            border-color: rgba(99,102,241,0.7);
            color: white;
            transform: translateY(-8px) scale(1.06);
            box-shadow: 0 14px 35px rgba(99,102,241,0.35);
        }

        .card-button:active {
            transform: translateY(-2px) scale(1.02);
        }

        .card-button .corner {
            position: absolute;
            font-size: 0.7rem;
            font-weight: 700;
            color: rgba(255,255,255,0.5);
        }

        .card-button:hover .corner {
            color: rgba(255,255,255,0.9);
        }

        .card-button .top-left {
            top: 7px;
            left: 9px;
        }

        .card-button .bottom-right {
            bottom: 7px;
            right: 9px;
            transform: rotate(180deg);
        }

        .card-button .center {
            font-size: 1.7rem;
            font-weight: 600;
        }

        .actions {
            margin-bottom: 2.5rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .actions form {
            display: inline-block;
        }

        .actions button {
            padding: 0.8rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .actions .action {
            background: rgba(16,185,129,0.15);
            border: 1px solid rgba(16,185,129,0.35);
            color: #34d399;
            box-shadow: 0 4px 15px rgba(16,185,129,0.1);
        }

        .actions .action:hover {
            background: rgba(16,185,129,0.35);
            border-color: rgba(16,185,129,0.6);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(16,185,129,0.25);
        }

        .actions .reset {
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.35);
            color: #f87171;
            box-shadow: 0 4px 15px rgba(239,68,68,0.1);
        }

        .actions .reset:hover {
            background: rgba(239,68,68,0.35);
            border-color: rgba(239,68,68,0.6);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(239,68,68,0.25);
        }

        #players {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
            max-width: 900px;
            margin: 1rem auto 0;
        }

        .card {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 1.2rem 1.5rem;
            border-radius: 16px;
            min-width: 100px;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .card.new {
            animation: fadeInUp 0.4s ease backwards;
        }

        .card:hover {
            background: rgba(255,255,255,0.1);
            transform: translateY(-3px);
        }

        .own {
            border: 2px solid rgba(99,102,241,0.5);
            box-shadow: 0 0 25px rgba(99,102,241,0.15), 0 4px 15px rgba(0,0,0,0.2);
        }

        .player-name {
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .player-vote {
            font-size: 2.2rem;
            padding-top: 0.8rem;
            color: white;
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
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(99,102,241,0.5), transparent);
            margin: 1.5rem auto;
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
                <button class="card-button">
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

        function updatePlayers(data) {
            const newJSON = JSON.stringify(data);

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
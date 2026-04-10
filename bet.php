<?php
session_start();

// 🔥 CACHE BLOCK (IMPORTANT)
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once 'db.php';

// Agar user login nahi hai, login page par bhejo
if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

date_default_timezone_set('Asia/Kolkata');
$current_time = date('d M Y, h:i A');

// Fetch Current Bets (only current game bets)
$stmt_current = $conn->prepare("
    SELECT * FROM bets 
    WHERE user_id = ? AND game_time >= NOW()
    ORDER BY game_time ASC
");
$stmt_current->execute([$user_id]);
$current_bets_auto = $stmt_current->fetchAll();

// Success Message
if(isset($_SESSION['bet_message'])){
    echo '<div class="alert-success-msg" id="successMsg">'.$_SESSION['bet_message'].'</div>';
    unset($_SESSION['bet_message']);
}

// MULTI BET MESSAGE (ONLY CURRENT TIME SHOW)
if(isset($_SESSION['bet_list'])){
    foreach($_SESSION['bet_list'] as $bet){
        if(strtotime($bet['time']) >= time()){
            $time = date("h:i A", strtotime($bet['time']));
            echo '<div class="alert-success-msg" style="background:#0ea5e9;">
                    <b>Bet Confirmed ✅</b><br>
                    Game: '.$bet['game'].'<br>
                    Time: '.$time.'<br>
                    Numbers: '.$bet['numbers'].'<br>
                    Amount: ₹'.$bet['amount'].'
                  </div>';
        }
    }
}

// Fetch Games
$stmt = $conn->query("SELECT id, name, interval_mins FROM games ORDER BY name ASC");
$games = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Place Bet</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* === ORIGINAL CSS KE SAARE RULES === */
body {
    background: radial-gradient(circle at top left, #0f172a, #020617);
    font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
    color: #fff;
    margin: 0;
    padding: 0;
}
.bet-card {
    max-width: 700px;
    margin: 40px auto;
    background: linear-gradient(145deg,#0b1220,#111827);
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.45);
    border: 1px solid rgba(255,255,255,0.06);
}
.title {
    font-weight: 700;
    font-size: 28px;
    text-align: center;
    margin-bottom: 5px;
    background: linear-gradient(to right,#38bdf8,#818cf8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.subtitle {
    text-align: center;
    color: #94a3b8;
    margin-bottom: 25px;
    font-size: 14px;
}
.time-box {
    background:#0f172a;
    border:1px solid rgba(255,255,255,0.08);
    padding:12px;
    border-radius:10px;
    text-align:center;
    margin-bottom:25px;
    font-size:14px;
}
.time-box b { color:#38bdf8; font-weight:600; }
.form-label { font-size:13px; color:#94a3b8; margin-bottom:6px; }
.form-control, .form-select {
    background:#0b1220;
    border:1px solid rgba(255,255,255,0.08);
    color:#fff;
    padding:12px;
    border-radius:10px;
}
.form-control:focus, .form-select:focus {
    border-color:#38bdf8;
    box-shadow:0 0 0 0.2rem rgba(56,189,248,0.15);
    background:#0b1220;
    color:#fff;
}
.btn-bet {
    width:100%;
    padding:12px;
    border-radius:10px;
    font-weight:600;
    border:none;
    background:linear-gradient(to right,#22c55e,#16a34a);
    color:#fff;
    transition:0.2s;
}
.btn-bet:hover {
    transform:translateY(-1px);
    box-shadow:0 10px 20px rgba(34,197,94,0.25);
}
.alert-success-msg {
    max-width:700px;
    margin:20px auto 10px auto;
    background:#16a34a;
    color:#fff;
    padding:12px;
    border-radius:10px;
    text-align:center;
    font-weight:500;
    box-shadow:0 10px 25px rgba(0,0,0,0.25);
}
.alert-error {
    background:#dc2626;
    padding:12px;
    border-radius:10px;
    text-align:center;
    margin-bottom:15px;
    display:none;
}
.number-grid {
    display:grid;
    grid-template-columns: repeat(10, 1fr);
    gap:6px;
    max-height:250px;
    overflow-y:auto;
    margin-bottom:15px;
}
.number-grid input[type="checkbox"]{display:none;}
.number-label {
    background:#0b1220;
    border:1px solid rgba(255,255,255,0.08);
    padding:6px 5px;
    border-radius:5px;
    text-align:center;
    cursor:pointer;
    font-size:12px;
    user-select:none;
}
.number-label:hover{background:#1e293b;}
.number-grid input[type="checkbox"]:checked + .number-label{
    background:#22c55e;
    color:#fff;
    border-color:#16a34a;
}
.logout-link {
    display:block;
    max-width:700px;
    margin:20px auto;
    text-align:right;
    font-size:14px;
}
.logout-link a {
    color:#f87171;
    text-decoration:none;
    font-weight:500;
}
.logout-link a:hover { text-decoration:underline; }
.current-bets-container {
    max-width:700px;
    margin:30px auto;
    background:#0b1220;
    border:1px solid rgba(255,255,255,0.06);
    border-radius:12px;
    padding:15px;
}
.current-bets-container h4 { color:#38bdf8; margin-bottom:15px; }
.current-bet-row {
    display:flex;
    justify-content:space-between;
    background:#020617;
    padding:8px 10px;
    margin-bottom:6px;
    border-radius:6px;
    font-size:13px;
}
.current-bet-row span:last-child { color:#94a3b8; }
</style>
</head>

<body>

<div class="logout-link">
    <a href="user/logout.php">Logout</a>
</div>

<div class="bet-card">
    <div class="title">🎯 Place Your Bet</div>
    <div class="subtitle">Fast • Secure • Real-time Game Entry</div>

    <div class="time-box">🕒 Current Time: <b><?= $current_time ?></b></div>

    <div id="errorBox" class="alert-error">❌ Game Not OK — Betting Closed</div>

    <form method="POST" action="place_bet.php" id="betForm">
        <div class="mb-3">
            <label class="form-label">Select Game</label>
            <select name="game_id" id="gameSelect" class="form-select" required>
                <option value="">-- Choose Game --</option>
                <?php foreach($games as $game): ?>
                    <option value="<?= $game['id'] ?>" data-interval="<?= $game['interval_mins'] ?>">
                        <?= htmlspecialchars($game['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Select Game Time</label>
            <input type="time" name="game_time" id="gameTime" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Select Number(s)</label>
            <div class="number-grid">
                <?php for($i=0; $i<=99; $i++):
                    $num = str_pad($i,2,'0',STR_PAD_LEFT); ?>
                    <input type="checkbox" id="num<?= $num ?>" name="numbers[]" value="<?= $num ?>">
                    <label class="number-label" for="num<?= $num ?>"><?= $num ?></label>
                <?php endfor; ?>
            </div>
            <small style="color:#94a3b8;">Click to select multiple numbers</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Select Token</label>
            <select name="token" id="tokenSelect" class="form-select" required>
                <?php 
                $tokens = [10,20,30,40,50,60,70,80,100,150,200,250,300,350,400,450,500,600,700,800,900,1000,1500,2000,2500,3000,3500,4000,4500,5000];
                foreach($tokens as $t): ?>
                    <option value="<?= $t ?>"><?= $t ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" id="amountInput" class="form-control" readonly required>
        </div>

        <button type="submit" class="btn-bet">🚀 Bet Lagao</button>
    </form>
</div>

<script>
const tokenSelect = document.getElementById('tokenSelect');
const amountInput = document.getElementById('amountInput');
amountInput.value = tokenSelect.value;

tokenSelect.addEventListener('change', function(){
    amountInput.value = this.value;
});

// Success message auto hide after 2 seconds
setTimeout(function(){
    let msg = document.getElementById('successMsg');
    if(msg){
        msg.style.transition = "opacity 0.5s ease";
        msg.style.opacity = "0";
        setTimeout(() => msg.remove(), 500);
    }
}, 2000);
</script>

<script>
const gameSelect = document.getElementById('gameSelect');
const gameTimeInput = document.getElementById('gameTime');
const errorBox = document.getElementById('errorBox');
const betForm = document.getElementById('betForm');

const RESULT_HOUR = 18; 
const RESULT_MIN = 0;

function checkBetAllowed() {
    let selectedOption = gameSelect.options[gameSelect.selectedIndex];
    let interval = parseInt(selectedOption.getAttribute('data-interval'));
    let gameTime = gameTimeInput.value;

    if (!interval || !gameTime) return;

    let now = new Date();

    let [hours, minutes] = gameTime.split(':');
    let gameDateTime = new Date();
    gameDateTime.setHours(hours, minutes, 0, 0);

    let diffMinutes = (gameDateTime - now) / (1000 * 60);

    let cutoff = 0;
    if (interval == 15) cutoff = 3;
    else if (interval == 30) cutoff = 5;
    else if (interval == 60) cutoff = 10;

    let gameClosed = diffMinutes <= cutoff;

    let resultDateTime = new Date();
    resultDateTime.setDate(resultDateTime.getDate() + 1);
    resultDateTime.setHours(RESULT_HOUR, RESULT_MIN, 0, 0);

    let resultCutoffTime = new Date(resultDateTime.getTime() - (60 * 60 * 1000));
    let resultClosed = now >= resultCutoffTime;

    if (gameClosed || resultClosed) {
        errorBox.style.display = "block";
        betForm.querySelector("button").disabled = true;
    } else {
        errorBox.style.display = "none";
        betForm.querySelector("button").disabled = false;
    }
}

gameSelect.addEventListener('change', checkBetAllowed);
gameTimeInput.addEventListener('change', checkBetAllowed);
setInterval(checkBetAllowed, 5000);
</script>

<script>
// 🔥 CLEAN BACK BLOCK
history.pushState(null, null, location.href);

window.addEventListener('popstate', function () {
    window.location.replace('user/login.php');
});

window.addEventListener('load', function () {
    history.pushState(null, null, location.href);
});
</script>
</body>
</html>
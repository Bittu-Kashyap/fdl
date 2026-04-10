<?php
require_once 'db.php';

/* ------------------ FILTER VALUES ------------------ */
$selected_game = isset($_GET['game_id']) ? (int)$_GET['game_id'] : 0;
$selected_date = isset($_GET['date']) ? $_GET['date'] : '';

/* ------------------ GAME LIST ------------------ */
$games = $conn->query("SELECT id,name FROM games ORDER BY name ASC")->fetchAll();

/* ------------------ PAGE TITLE ------------------ */
$game_name = "Result History";
if($selected_game){
    $stmt = $conn->prepare("SELECT name FROM games WHERE id=?");
    $stmt->execute([$selected_game]);
    $g = $stmt->fetch();
    if($g){
        $game_name = $g['name']." Result History";
    }
}

/* ------------------ RESULT QUERY ------------------ */
$where = [];
$params = [];

if($selected_game){
    $where[] = "game_id=?";
    $params[] = $selected_game;
}
if($selected_date){
    $where[] = "lock_date=?";
    $params[] = $selected_date;
}

$where_sql = count($where) ? ("WHERE ".implode(" AND ",$where)) : "";

$stmt = $conn->prepare("
SELECT lock_date, lock_time, lock_value
FROM results
$where_sql
ORDER BY lock_date DESC, lock_time DESC
LIMIT 100
");
$stmt->execute($params);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?=$game_name?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
:root{
    --bg:#0b1220;
    --bg-soft:#0f172a;
    --card:rgba(255,255,255,0.05);
    --border:rgba(255,255,255,0.08);
    --accent:#3b82f6;
    --accent2:#8b5cf6;
    --success:#22c55e;
    --text:#e2e8f0;
    --muted:#94a3b8;
}

*{box-sizing:border-box}

body{
    margin:0;
    background:
        radial-gradient(circle at 10% 10%, #1e293b 0%, transparent 40%),
        radial-gradient(circle at 90% 20%, #1e1b4b 0%, transparent 40%),
        var(--bg);
    color:var(--text);
    font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
}

/* Layout */
.container{max-width:1200px}

/* Header */
.page-header{
    background: linear-gradient(135deg,var(--accent),var(--accent2));
    padding:28px 32px;
    border-radius:22px;
    margin-bottom:28px;
    box-shadow:
        0 20px 50px rgba(0,0,0,0.45),
        inset 0 1px 0 rgba(255,255,255,0.15);
}
.page-title{
    font-size:28px;
    font-weight:800;
    letter-spacing:.5px;
    margin:0;
}
.btn-back{
    background:#fff;
    border:none;
    border-radius:12px;
    padding:10px 18px;
    font-weight:600;
}

/* Glass Card */
.glass{
    background:var(--card);
    backdrop-filter: blur(18px);
    border:1px solid var(--border);
    border-radius:22px;
    box-shadow:0 20px 40px rgba(0,0,0,0.35);
}

/* Filter Card */
.filter-card{ padding:26px; }

.form-label{
    font-size:13px;
    color:var(--muted);
    margin-bottom:6px;
}

.form-control,.form-select{
    background:var(--bg-soft);
    border:1px solid var(--border);
    color:#fff;
    border-radius:14px;
    padding:12px 14px;
}

.form-control:focus,.form-select:focus{
    border-color:var(--accent);
    box-shadow:0 0 0 .2rem rgba(59,130,246,.15);
}

.btn-search{
    border:none;
    border-radius:14px;
    padding:12px;
    font-weight:700;
    background:linear-gradient(135deg,var(--accent),var(--accent2));
    color:#fff;
    letter-spacing:.5px;
}

/* Table */
.table-wrap{ overflow:hidden; border-radius:22px; }

.table{
    margin:0;
    color:#fff;
}

.table thead{
    background:rgba(255,255,255,0.04);
    backdrop-filter: blur(8px);
}

.table th{
    border:none;
    padding:18px 22px;
    font-size:13px;
    letter-spacing:.6px;
    text-transform:uppercase;
    color:var(--muted);
}

.table td{
    border-top:1px solid rgba(255,255,255,.04);
    padding:20px 22px;
    font-size:15px;
}

.table tbody tr{
    transition:.25s;
}
.table tbody tr:hover{
    background:rgba(255,255,255,.03);
}

.result-value{
    font-size:24px;
    font-weight:900;
    background:linear-gradient(90deg,#fbbf24,#f59e0b);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

/* Empty State */
.empty{
    padding:70px 0;
    text-align:center;
    color:var(--muted);
    font-size:15px;
}

/* Responsive */
@media(max-width:768px){
    .page-header{ text-align:center }
}
</style>
</head>

<body>

<div class="container py-4">

    <!-- Header -->
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h3 class="page-title"><?=$game_name?></h3>
        <a href="index.php" class="btn btn-back">← Back</a>
    </div>

    <!-- Filters -->
    <div class="glass filter-card mb-4">
        <form method="get" class="row g-4 align-items-end">

            <div class="col-md-4">
                <label class="form-label">Select Game</label>
                <select name="game_id" class="form-select">
                    <option value="">All Games</option>
                    <?php foreach($games as $game): ?>
                        <option value="<?=$game['id']?>"
                            <?=$selected_game==$game['id']?'selected':''?>>
                            <?=$game['name']?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Select Date</label>
                <input type="date" name="date" value="<?=$selected_date?>" class="form-control">
            </div>

            <div class="col-md-4 d-grid">
                <button class="btn-search">🔎 SEARCH RECORDS</button>
            </div>

        </form>
    </div>

    <!-- Results -->
    <div class="glass table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:35%">Date</th>
                    <th style="width:35%">Time</th>
                    <th style="width:30%">Result</th>
                </tr>
            </thead>
            <tbody>

            <?php if(count($results)>0): ?>
                <?php foreach($results as $r): ?>
                <tr>
                    <td><?=htmlspecialchars($r['lock_date'])?></td>
                    <td><?=htmlspecialchars($r['lock_time'])?></td>
                    <td><span class="result-value"><?=htmlspecialchars($r['lock_value'])?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="empty">No records found</td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>

</body>
</html>
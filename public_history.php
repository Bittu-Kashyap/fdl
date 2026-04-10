<?php
require_once 'db.php';
require_once 'generate_random.php';

$games_stmt = $conn->query("SELECT id, name FROM games ORDER BY name ASC");
$all_games = $games_stmt->fetchAll();

$filter_game = isset($_GET['filter_game']) && $_GET['filter_game'] !== '' ? (int)$_GET['filter_game'] : null;
$filter_date = isset($_GET['filter_date']) && $_GET['filter_date'] !== '' ? $_GET['filter_date'] : null;

$where = [];
$params = [];
if ($filter_game) {
    $where[] = "results.game_id = :game";
    $params[':game'] = $filter_game;
}
if ($filter_date) {
    $where[] = "results.lock_date = :date";
    $params[':date'] = $filter_date;
}

$where_clause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : '';

$limit = 20; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$count_stmt = $conn->prepare("SELECT COUNT(*) FROM results $where_clause");
foreach ($params as $k => $v) $count_stmt->bindValue($k, $v);
$count_stmt->execute();
$total_pages = ceil($count_stmt->fetchColumn() / $limit);

$stmt = $conn->prepare("SELECT results.*, games.name as game_name FROM results LEFT JOIN games ON results.game_id = games.id $where_clause ORDER BY results.lock_date DESC, results.lock_time DESC LIMIT :limit OFFSET :offset");
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Public History - FBD Live Results</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --card-border: #334155;
            --primary: #38bdf8;
            --accent: #f43f5e;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: radial-gradient(circle at 50% 0%, #1e293b 0%, transparent 70%);
        }

        .navbar {
            background-color: rgba(15, 23, 42, 0.8) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            background: linear-gradient(to right, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-section {
            padding: 3rem 1rem 2rem;
            text-align: center;
        }

        .hero-title {
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            letter-spacing: -1px;
        }

        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            color: var(--text-main);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }

        .form-control, .form-select {
            background-color: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--card-border);
            color: var(--text-main);
            border-radius: 8px;
            padding: 0.6rem 1rem;
        }

        .form-control:focus, .form-select:focus {
            background-color: rgba(15, 23, 42, 0.8);
            color: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(56, 189, 248, 0.25);
        }

        .table {
            color: var(--text-main);
            margin-bottom: 0;
        }

        .table th {
            background-color: rgba(0,0,0,0.2) !important;
            border-color: var(--card-border) !important;
            color: var(--text-muted) !important;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .table td {
            border-color: var(--card-border) !important;
            padding: 1rem;
            vertical-align: middle; color:#fff;
            background-color: transparent !important;
        }
        
        .pagination .page-link {
            background-color: var(--card-bg);
            border-color: var(--card-border);
            color: var(--text-muted);
            margin: 0 4px;
            border-radius: 8px;
        }
        
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #38bdf8, #0284c7);
            border-color: transparent;
            color: #fff;
        }

        footer {
            margin-top: auto;
            border-top: 1px solid var(--card-border);
            padding: 2rem 0;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.875rem;
        }
		.text-muted {color:#38bdf8 !important;}
    </style>
</head>
<body>

<nav class="navbar navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fa-solid fa-bolt text-info me-2"></i>FBD Live
        </a>
        <a href="index.php" class="btn btn-outline-info btn-sm rounded-pill px-3">
            <i class="fa-solid fa-house me-1"></i> Back Home
        </a>
    </div>
</nav>

<section class="hero-section container">
    <h1 class="hero-title"><i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>Result History</h1>
    <p class="text-muted fs-5">Browse the comprehensive archive of all previous lock values.</p>
</section>

<div class="container mb-5">
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted small">Select Game</label>
                    <select name="filter_game" class="form-select">
                        <option value="">-- All Games --</option>
                        <?php foreach ($all_games as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= $filter_game == $g['id'] ? 'selected' : '' ?>><?= htmlspecialchars($g['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small">Select Date</label>
                    <input type="date" name="filter_date" class="form-control" value="<?= htmlspecialchars($filter_date ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #38bdf8, #0284c7); border: none;">
                        <i class="fa-solid fa-search me-1"></i> Search Records
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0 text-light">
                <thead>
                    <tr>
                        <th class="ps-4">Game</th>
                        <th>Lock Date</th>
                        <th>Lock Time</th>
                        <th class="text-end pe-4">Lock Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($results) > 0): ?>
                        <?php foreach ($results as $row): ?>
                        <tr>
                            <td class="ps-4 fw-semibold text-warning"><?= htmlspecialchars($row['game_name'] ?? 'Unknown/Deleted') ?></td>
                            <td><?= date('l, d M Y', strtotime($row['lock_date'])) ?></td>
                            <td><?= date('h:i A', strtotime($row['lock_time'])) ?></td>
                            <td class="text-end pe-4 fw-bold text-success fs-5"><?= htmlspecialchars($row['lock_value']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center py-5 text-muted"><i class="fa-solid fa-folder-open fs-1 d-block mb-3 opacity-25"></i>No historical records match your search.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($total_pages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
           <?php if ($total_pages > 1): ?>
<nav aria-label="Page navigation">
    <ul class="pagination mt-4 justify-content-center">

        <?php
        $range = 2; // 2 page before and after current page

        // PREV Button
        if ($page > 1):
            $q = $_GET;
            $q['page'] = $page - 1;
            $prev_link = '?' . http_build_query($q);
        ?>
            <li class="page-item">
                <a class="page-link" href="<?= $prev_link ?>">Prev</a>
            </li>
        <?php endif; ?>

        <?php
        $start = max(1, $page - $range);
        $end   = min($total_pages, $page + $range);

        for ($i = $start; $i <= $end; $i++):
            $q = $_GET;
            $q['page'] = $i;
            $link = '?' . http_build_query($q);
        ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php
        // NEXT Button
        if ($page < $total_pages):
            $q = $_GET;
            $q['page'] = $page + 1;
            $next_link = '?' . http_build_query($q);
        ?>
            <li class="page-item">
                <a class="page-link" href="<?= $next_link ?>">Next</a>
            </li>
        <?php endif; ?>

    </ul>
</nav>
<?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<footer>
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y') ?> FBD Live Results.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once 'db.php';
require_once 'generate_random.php';

$query = "
SELECT g.id, g.name, g.game_type, g.interval_mins, g.logo_path, 
       r.lock_date, r.lock_time, r.lock_value
FROM games g
LEFT JOIN results r ON r.id = (
    SELECT id FROM results r2 
    WHERE r2.game_id = g.id 
      AND CONCAT(r2.lock_date, ' ', r2.lock_time) <= NOW()
    ORDER BY r2.lock_date DESC, r2.lock_time DESC 
    LIMIT 1
)
ORDER BY g.id ASC
";
$stmt = $conn->query($query);
$games_data = $stmt->fetchAll();

/* Latest result time */
$query_all = "
SELECT g.id,
       (SELECT CONCAT(r3.lock_date, ' ', r3.lock_time) 
        FROM results r3 
        WHERE r3.game_id = g.id 
        ORDER BY r3.lock_date DESC, r3.lock_time DESC LIMIT 1) as max_datetime
FROM games g
";
$stmt_all = $conn->query($query_all);
$max_times = [];
foreach ($stmt_all->fetchAll() as $rt)
{
    if ($rt['max_datetime'])
    {
        $max_times[$rt['id']] = $rt['max_datetime'];
    }
}

/* Next future result */
$query_next = "
SELECT g.id,
       (SELECT CONCAT(r4.lock_date, ' ', r4.lock_time)
        FROM results r4
        WHERE r4.game_id = g.id
        AND CONCAT(r4.lock_date, ' ', r4.lock_time) > NOW()
        ORDER BY r4.lock_date ASC, r4.lock_time ASC
        LIMIT 1) as next_datetime
FROM games g
";

$stmt_next = $conn->query($query_next);

$next_times = [];

foreach ($stmt_next->fetchAll() as $nt)
{
    if ($nt['next_datetime'])
    {
        $next_times[$nt['id']] = $nt['next_datetime'];
    }
}
?> <?php
/* ---------------- AUTO REFRESH TIME ---------------- */
$refresh_timestamp = null;

foreach ($next_times as $gid => $dtstr) {
    $ts = strtotime($dtstr);
    if ($ts && $ts > time()) {
        if ($refresh_timestamp === null || $ts < $refresh_timestamp) {
            $refresh_timestamp = $ts;
        }
    }
}

$refresh_delay_ms = null;
if ($refresh_timestamp !== null) {
    $diff = $refresh_timestamp - time();
    if ($diff < 0) $diff = 0;
    $refresh_delay_ms = $diff * 1000;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>RDL Satta King Result Today <?php echo date('d M Y'); ?> | Live Fast Satta Result </title>
    <meta name="description" content="Check RDL Satta King Result Today 
				<?php echo date('d M Y'); ?>. Fastest live satta result of Gali, Desawar, Faridabad & Ghaziabad. Daily updated accurate results.">
    <meta name="keywords" content="satta king result today, rdl satta king, gali result, desawar result, faridabad result, ghaziabad result, live satta result, fast satta king result">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="author" content="FDL Satta King">
    <!-- ✅ OPEN GRAPH -->
    <meta property="og:title" content="RDL Satta King Result Today 
									<?php echo date('d M Y'); ?>">
    <meta property="og:description" content="Fast live satta king results of all games. Check now.">
    <meta property="og:url" content="https://rdlsattakingresults.com/">
    <meta property="og:type" content="website">
    <meta property="og:image" content="https://rdlsattakingresults.com/logo.png">
    <!-- ✅ TWITTER -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="RDL Satta King Result Today">
    <meta name="twitter:description" content="Check latest satta king results fast and live.">
    <meta name="twitter:image" content="https://rdlsattakingresults.com/logo.png">
    <!-- ✅ PERFORMANCE -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- ✅ SCHEMA (ADVANCED) -->
    <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "RDL Satta King Result",
        "url": "https://rdlsattakingresults.com/",
        "potentialAction": {
          "@type": "SearchAction",
          "target": "https://rdlsattakingresults.com/?q={search_term_string}",
          "query-input": "required name=search_term_string"
        }
      }
    </script>
    <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "FDL Satta King",
        "url": "https://rdlsattakingresults.com/",
        "logo": "https://rdlsattakingresults.com/logo.png"
      }
    </script>
    <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [{
          "@type": "ListItem",
          "position": 1,
          "name": "Home",
          "item": "https://rdlsattakingresults.com/"
        }]
      }
    </script>
    <link rel="canonical" href="https://rdlsattakingresults.com/">
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
        overflow-x: hidden;
        overflow-y: auto;
        background-image: radial-gradient(circle at 50% 0%, #1e293b 0%, transparent 70%);
      }

      .hero-title2 {
        font-weight: 600;
        margin-bottom: 1rem;
        letter-spacing: -1px;
        animation: blink 0.3s infinite;
        font-size: 2.5rem;
      }

      @keyframes blink {
        0% {
          color: #fff;
        }

        25% {
          color: #ffc52a;
        }

        50% {
          color: #8efc2c;
        }

        75% {
          color: #61fdff;
        }

        100% {
          color: #fff
        }
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
        padding: 20px 15px;
        text-align: center;
      }

      .hero-title {
        font-weight: 600;
        letter-spacing: -1px;
        animation: blink 0.3s infinite;
        font-size: 2.5rem;
      }

      @keyframes blink {
        0% {
          color: #fff;
        }

        25% {
          color: #ffc52a;
        }

        50% {
          color: #8efc2c;
        }

        75% {
          color: #61fdff;
        }

        100% {
          color: #fff
        }
      }

      .game-card {
        background-color: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 16px;
        padding: 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
      }

      .game-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #38bdf8, #818cf8);
        opacity: 0;
        transition: opacity 0.3s ease;
      }

      .game-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.4);
      }

      .game-card:hover::before {
        opacity: 1;
      }

      .game-logo {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 1rem;
        border: 2px solid var(--card-border);
        background-color: #0f172a;
        padding: 2px;
      }

      .game-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        text-align: center;
      }

      .result-circle {
        width: 75px;
        height: 75px;
        border-radius: 50%;
        background: rgba(56, 189, 248, 0.1);
        border: 2px dashed rgba(56, 189, 248, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        font-weight: 800;
        color: yellow;
        margin-bottom: 1.5rem;
        text-shadow: 0 0 20px rgba(56, 189, 248, 0.3);
        animation: pulse 3s infinite;
      }

      .no-result {
        font-size: 1.5rem;
        color: var(--text-muted);
        text-shadow: none;
      }

      .time-badge {
        background-color: rgba(15, 23, 42, 0.6);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        color: var(--text-muted);
        border: 1px solid var(--card-border);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
      }

      .next-time {
        margin-top: 0.75rem;
        font-size: 12px;
        font-weight: 600;
        color: #818cf8;
        background: rgba(129, 140, 248, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        width: 100%;
        text-align: center;
        border: 1px solid rgba(129, 140, 248, 0.2);
      }

      @keyframes pulse {
        0% {
          box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.2);
        }

        70% {
          box-shadow: 0 0 0 15px rgba(56, 189, 248, 0);
        }

        100% {
          box-shadow: 0 0 0 0 rgba(56, 189, 248, 0);
        }
      }

      .marquee-wrap {
        width: 100%;
        overflow: hidden;
        background: #111827;
        border-top: 1px solid #334155;
        border-bottom: 1px solid #334155;
        padding: 8px 0;
      }

      .marquee-text {
        display: inline-block;
        white-space: nowrap;
        color: yellow;
        font-weight: 600;
        font-size: 18px;
        padding-left: 100%;
        animation: marqueeMove 15s linear infinite;
      }

      @keyframes marqueeMove {
        0% {
          transform: translateX(0);
        }

        100% {
          transform: translateX(-100%);
        }
      }

      footer {
        margin-top: auto;
        border-top: 1px solid var(--card-border);
        padding: 2rem 0;
        text-align: center;
        color: var(--text-muted);
        font-size: 0.875rem;
      }

      /* Coins, Rupees, Confetti, Spark Particles, Jackpot Explosions */
      .coin,
      .rupee,
      .confetti,
      .spark,
      .explosion {
        position: fixed;
        top: -50px;
        width: 30px;
        height: 30px;
        background-size: contain;
        background-repeat: no-repeat;
        z-index: 1;
        pointer-events: none;
        animation-name: fall;
        animation-timing-function: linear;
        animation-iteration-count: infinite;
      }

      .coin,
      .rupee {
        filter: drop-shadow(0 0 10px #ffd700) drop-shadow(0 0 20px #ffcc00);
        animation: shimmer-glow 1.5s infinite alternate;
      }

      .spark {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: radial-gradient(circle, #fff700, #ffcc00);
        animation-name: sparkFall;
        animation-duration: 2s;
        animation-iteration-count: infinite;
      }

      .explosion {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: radial-gradient(circle, #ffdd00, #ff8800);
        animation-name: explode;
        animation-duration: 1.5s;
        animation-iteration-count: 1;
      }

      @keyframes fall {
        0% {
          transform: translateY(-50px) rotate(0deg);
        }

        100% {
          transform: translateY(110vh) rotate(360deg);
        }
      }

      @keyframes shimmer-glow {
        0% {
          transform: scale(1);
        }

        50% {
          transform: scale(1.3);
        }

        100% {
          transform: scale(1);
        }
      }

      @keyframes sparkFall {
        0% {
          transform: translateY(0) scale(0.5);
          opacity: 1;
        }

        100% {
          transform: translateY(120vh) scale(1);
          opacity: 0;
        }
      }

      @keyframes explode {
        0% {
          transform: scale(0);
          opacity: 1;
        }

        50% {
          transform: scale(1.5);
          opacity: 0.7;
        }

        100% {
          transform: scale(0);
          opacity: 0;
        }
      }

      .header-card img {
        animation: glow 2s infinite alternate;
      }

      @keyframes glow {
        0% {
          filter: drop-shadow(0 0 5px gold);
        }

        50% {
          filter: drop-shadow(0 0 15px gold);
        }

        100% {
          filter: drop-shadow(0 0 5px gold);
        }
      }

      /* ================= 1366x768 PERFECT FIX ================= */
      @media (max-width: 1366px) and (min-height: 700px) {
        .hero-section {
          padding: 40px 15px !important;
        }

        .hero-title {
          font-size: 2rem !important;
        }

        .hero-title2 {
          font-size: 2rem !important;
        }

        .game-card {
          padding: 1.2rem !important;
        }

        .result-circle {
          width: 100px !important;
          height: 100px !important;
          font-size: 2.2rem !important;
        }

        .container.mb-5.pb-5 {
          margin-bottom: 30px !important;
          padding-bottom: 30px !important;
        }
      }

      /* ================= HEIGHT BASED FIX (IMPORTANT) ================= */
      @media (max-height: 768px) {
        body {
          display: block !important;
        }

        .hero-section {
          padding-top: 30px !important;
          padding-bottom: 20px !important;
        }

        .result-circle {
          margin-bottom: 1rem !important;
        }

        .game-title {
          margin-bottom: 1rem !important;
        }
      }

      /* ================= CARD ALIGNMENT FIX ================= */
      .row.g-4 {
        align-items: stretch;
      }

      /* ================= EXTRA SMALL HEIGHT FIX ================= */
      @media (max-height: 650px) {
        .hero-title {
          font-size: 1.8rem !important;
        }

        .result-circle {
          width: 90px !important;
          height: 90px !important;
          font-size: 2rem !important;
        }
      }

      /* ================= FULL RESPONSIVE FIX ================= */
      /* Desktop container control */
      .container {
        max-width: 1200px;
      }

      /* ================= GRID FIX ================= */
      @media (max-width: 1200px) {
        .col-lg-3 {
          flex: 0 0 33.33%;
          max-width: 33.33%;
        }
      }

      @media (max-width: 992px) {
        .col-md-4 {
          flex: 0 0 50%;
          max-width: 50%;
        }
      }

      /* ===== MOBILE 2 CARDS PER ROW ===== */
      @media (max-width: 576px) {
        .col-6 {
          flex: 0 0 50% !important;
          max-width: 50% !important;
        }

        .game-card {
          padding: 0.8rem !important;
        }

        .result-circle {
          width: 80px !important;
          height: 80px !important;
          font-size: 1.8rem !important;
        }

        .game-title {
          font-size: 0.9rem !important;
        }

        .marquee-text {
          font-size: 14px;
        }

        .row.g-4 {
          --bs-gutter-x: 10px;
          --bs-gutter-y: 10px;
        }
      }

      /* ================= HERO FIX ================= */
      .hero-section {
        padding: 40px 15px;
      }

      .hero-title,
      .hero-title2 {
        font-size: 36px;
      }

      @media (max-width: 992px) {

        .hero-title,
        .hero-title2 {
          font-size: 2rem;
        }
      }

      /* ================= HEIGHT FIX ================= */
      @media (max-height: 768px) {
        .hero-section {
          padding: 25px 10px !important;
        }

        .result-circle {
          margin-bottom: 1rem;
        }
      }

      /* ================= ALIGNMENT FIX ================= */
      .row.g-4 {
        align-items: stretch;
      }

      /* ================= OVERFLOW FIX ================= */
      body {
        overflow-x: hidden;
      }
    </style>
  </head>
  <body>
    <nav class="navbar navbar-dark sticky-top">
      <div class="container">
        <a class="navbar-brand" href="index.php">
          <i class="fa-solid fa-chess text-info me-2"></i> FDL Satta King Results </a>
        <a href="public_history.php" class="btn btn-outline-info btn-sm rounded-pill px-3">
          <i class="fa-solid fa-clock-rotate-left me-1"></i> Result History </a>
      </div>
    </nav>
    <section class="hero-section container">
      <h1 class="hero-title">RDL Satta King Result Today <?php echo date('d M Y'); ?> | Live Fast Update </h1>
      <p class="fs-5 max-w-2xl mx-auto" style="color: #fff;">Latest results of all games</p>
      <h3>
        <div class="marquee-wrap">
          <div class="marquee-text"> Welcome to FDL Satta King Results. visit our website - https://www.fdlsattakingresults.com </div>
        </div>
      </h3>
    </section>
    <!-- ✅ SEO CONTENT (HIDDEN - GOOGLE KE LIYE) -->
    <!--<section class="container mt-4">
      <h2>RDL Satta King Result Today - Live Updates</h2>
      <p> RDL Satta King Result ek popular platform hai jahan aap <strong>satta king result today</strong> easily check kar sakte hain. Yahan Gali, Desawar, Faridabad aur Ghaziabad jaise sabhi major games ke latest aur accurate results milte hain. </p>
      <p> Website par aapko <strong>live satta result</strong>, next result timing aur previous history sab kuch ek hi jagah milta hai. Yeh site fast loading aur fully mobile responsive hai. </p>
      <p> Agar aap daily <strong>fast satta king result</strong> dekhna chahte hain to yeh platform best hai. </p>
    </section>-->
    <div class="container mb-5 pb-5">
      <div class="row g-4 justify-content-center"> <?php foreach ($games_data as $game): ?> <?php
    $next_time_str = "Waiting for first result";
    $show_next = false;

    /* First priority → future result from DB */
    if (isset($next_times[$game['id']]))
    {

        $dt = new DateTime($next_times[$game['id']]);

        if ($game['game_type'] == 'Timewise')
        {
            $next_time_str = $dt->format('h:i A');
        }
        else
        {
            $next_time_str = $dt->format('d M h:i A');
        }

        $show_next = true;
    }

    /* fallback logic */
    else
    {

        $abs_latest = isset($max_times[$game['id']]) ? $max_times[$game['id']] : null;

        if ($abs_latest)
        {

            $dt = new DateTime($abs_latest);

            /* Timewise game */
            if ($game['game_type'] == 'Timewise')
            {

                $interval = (int)$game['interval_mins'];

                while ($dt <= new DateTime())
                {
                    $dt->modify("+$interval minutes");
                }

                $next_time_str = $dt->format('h:i A');
            }

            /* Daywise game */
            else
            {

                while ($dt <= new DateTime())
                {
                    $dt->modify("+1 day");
                }

                $next_time_str = $dt->format('d M h:i A');
            }

            $show_next = true;
        }
    }
?> <div class="col-6 col-md-4 col-lg-3">
          <a href="game-history.php?game_id=
																											<?=$game['id']?>" class="text-decoration-none">
            <div class="game-card"> <?php if ($game['logo_path']): ?>
              <!-- <img src="uploads/
																												<?=htmlspecialchars($game['logo_path']) ?>" 
     alt="<?=htmlspecialchars($game['name']) ?> result"
     class="game-logo"
     loading="lazy"> --> <?php
    else: ?> <div class="game-logo d-flex align-items-center justify-content-center text-muted fs-3">
                <i class="fa-solid fa-gamepad"></i>
              </div> <?php
    endif; ?> <div class="game-title"> <?=htmlspecialchars($game['name']) ?> </div>
              <div class="result-circle d-flex flex-column"> <?php if ($game['lock_value'] !== null): ?> <span> <?=htmlspecialchars($game['lock_value']) ?> </span> <?php
    else: ?> <span class="no-result">--</span> <?php
    endif; ?> </div> <?php if ($show_next): ?> <div class="next-time">
                <i class="fa-solid fa-forward-step me-1"></i> Next Result: <?=$next_time_str ?>
              </div> <?php
    endif; ?>
            </div>
          </a>
        </div> <?php
endforeach; ?> <?php if (count($games_data) === 0): ?> <div class="col-12 text-center py-5">
          <div class="fs-1 text-muted mb-3">
            <i class="fa-solid fa-ghost"></i>
          </div>
          <h4 class="text-muted">No games are active right now.</h4>
        </div> <?php
endif; ?> </div>
    </div>
    <footer>
      <div class="container">
        <p class="mb-0">&copy; <?=date('Y') ?> FDL Satta King Results. </p>
      </div>
    </footer>
    <!-- Jackpot Animation Script -->
    <script>
      const elements = ["data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCI+PHBhdGggZD0iTTMwIDBDMTMuNDkgMCAwIDEzLjQ5IDAgMzBzMTMuNDkgMzAgMzAgMzAgMzAtMTMuNDkgMzAtMzBTNDYuNTEgMCAzMCAweiIgZmlsbD0iI2ZmYzAwMCIvPjwvc3ZnPg==", "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCI+PHBhdGggZD0iTTIwIDMwIGgxMCIgc3Ryb2tlPSIjZmZjMDAwIiBzdHJva2Utd2lkdGg9IjQiLz48L3N2Zz4=", "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCI+PHJlY3Qgd2lkdGg9IjIwIiBoZWlnaHQ9IjIwIiBmaWxsPSIjZmY3MDAwIi8+PC9zdmc+"];

      function createElement() {
        const el = document.createElement("div");
        const type = Math.random();
        if (type < 0.35) el.classList.add("coin");
        else if (type < 0.65) el.classList.add("rupee");
        else if (type < 0.9) el.classList.add("confetti");
        else el.classList.add("spark"); // small particles
        const size = type < 0.9 ? Math.random() * 25 + 20 : Math.random() * 10 + 5;
        el.style.width = size + "px";
        el.style.height = size + "px";
        el.style.left = Math.random() * window.innerWidth + "px";
        if (el.classList.contains("coin") || el.classList.contains("rupee") || el.classList.contains("confetti")) {
          el.style.backgroundImage = `url(${elements[Math.floor(Math.random()*elements.length)]})`;
          el.style.animationDuration = (5 + Math.random() * 5) + "s";
        }
        if (el.classList.contains("spark")) {
          el.style.animationDuration = (2 + Math.random() * 2) + "s";
        }
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 15000);
      }
      // Jackpot Explosions randomly
      function createExplosion() {
        const el = document.createElement("div");
        el.classList.add("explosion");
        el.style.left = Math.random() * window.innerWidth + "px";
        el.style.top = Math.random() * window.innerHeight / 2 + "px";
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 1500);
      }
      setInterval(createElement, 500);
      setInterval(createExplosion, 3000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> <?php if($refresh_delay_ms !== null): ?> <script>
      if (window.innerWidth > 768) {
        animation
      }
    </script>
    <script>
      setTimeout(function() {
        location.reload();
      }, < ? = $refresh_delay_ms ? > );
    </script>
    <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [{
          "@type": "Question",
          "name": "Satta King Result kaha dekhe?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Aap yahan live satta king result daily check kar sakte hain."
          }
        }, {
          "@type": "Question",
          "name": "Aaj ka Satta Result kya hai?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Aaj ka latest result upar list me diya gaya hai."
          }
        }]
      }
    </script> <?php endif; ?>
  </body>
</html>
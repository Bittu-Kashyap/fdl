<?php
require_once 'db.php';

// Fetch all Timewise games to check if they need auto-generated results
$stmt = $conn->query("SELECT * FROM games WHERE game_type = 'Timewise'");
$games = $stmt->fetchAll();

$now = new DateTime();

foreach ($games as $game) {
    $interval = (int)$game['interval_mins'];
    if ($interval <= 0)
        continue;

    // Get the latest result
    $res_stmt = $conn->prepare("SELECT lock_date, lock_time FROM results WHERE game_id = ? ORDER BY lock_date DESC, lock_time DESC LIMIT 1");
    $res_stmt->execute([$game['id']]);
    $latest = $res_stmt->fetch();

    if ($latest) {
        $latest_dt = new DateTime($latest['lock_date'] . ' ' . $latest['lock_time']);

        while (true) {
            $next_dt = clone $latest_dt;
            $next_dt->modify("+$interval minutes");

            // If the next calculated interval is in the past, it means we missed it!
            if ($next_dt <= $now) {
                // Generate a random result, e.g., a 2-digit number lock value
                $rand_val = rand(10, 99);

                $ins_stmt = $conn->prepare("INSERT INTO results (game_id, lock_date, lock_time, lock_value) VALUES (?, ?, ?, ?)");
                $ins_stmt->execute([
                    $game['id'],
                    $next_dt->format('Y-m-d'),
                    $next_dt->format('H:i:s'),
                    (string)$rand_val
                ]);

                $latest_dt = $next_dt; // Loop forward
            }
            else {
                break; // Future time, stop generating
            }
        }
    }
}
?>

<?php
session_start();
require_once 'db.php';
date_default_timezone_set('Asia/Kolkata');

// ✅ Login check
if(!isset($_SESSION['user_id'])){
    die("Login karo pehle");
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    // ✅ Correct user id
    $user_id = $_SESSION['user_id'];

    $game_id = $_POST['game_id'];
    $game_time = $_POST['game_time'];
    $token = (int)$_POST['token'];
    $numbers = $_POST['numbers'] ?? [];

    if(empty($numbers)){
        $_SESSION['bet_message'] = "❌ Select at least one number!";
        header("Location: bet.php");
        exit;
    }

    try {

        // Game Name Fetch
        $stmtGame = $conn->prepare("SELECT name FROM games WHERE id = ?");
        $stmtGame->execute([$game_id]);
        $game = $stmtGame->fetch();

        // Insert Bet
        $stmt = $conn->prepare("INSERT INTO bets (user_id, game_id, game_time, number, token, amount, created_at) VALUES (:uid,:gid,:gtime,:number,:token,:amount,NOW())");

        foreach($numbers as $num){
            $stmt->execute([
                ':uid' => $user_id,
                ':gid' => $game_id,
                ':gtime' => $game_time,
                ':number' => $num,
                ':token' => $token,
                ':amount' => $token,
            ]);
        }

        // Session display (same as your logic)
        if(!isset($_SESSION['bet_list'])){
            $_SESSION['bet_list'] = [];
        }

        $_SESSION['bet_list'][] = [
            'numbers' => implode(',', $numbers),
            'amount' => $token,
            'game' => $game['name'] ?? '',
            'time' => $game_time
        ];

        $_SESSION['bet_message'] = "✅ Bet placed successfully!";

        header("Location: bet.php");
        exit;

    } catch(Exception $e){
        $_SESSION['bet_message'] = "❌ Bet failed: ".$e->getMessage();
        header("Location: bet.php");
        exit;
    }
}
?>
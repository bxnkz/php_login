<?php
// เริ่มต้นเซสชัน
session_start();

// ฟังก์ชันสำหรับการสร้างสำรับไพ่ใหม่และสับไพ่
function create_deck()
{
    $deck_id = file_get_contents('https://deckofcardsapi.com/api/deck/new/shuffle/?deck_count=1');
    $deck_id = json_decode($deck_id)->deck_id;
    return $deck_id;
}

// ฟังก์ชันสำหรับการแจกไพ่
function draw_cards($deck_id, $count)
{
    $cards = file_get_contents("https://deckofcardsapi.com/api/deck/$deck_id/draw/?count=$count");
    $cards = json_decode($cards)->cards;
    return $cards;
}

// ฟังก์ชันสำหรับการคำนวณแต้มไพ่
function calculate_points($hand)
{
    $points = 0;
    foreach ($hand as $card) {
        $value = $card->value;
        if (is_numeric($value)) {
            $points += $value;
        } elseif (in_array($value, ['JACK', 'QUEEN', 'KING'])) {
            $points += 10;
        } elseif ($value == 'ACE') {
            $points += 1;
        }
    }
    return $points % 10;
}

// เริ่มเกมใหม่เมื่อกดปุ่ม "Deal New Hand"
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deal'])) {
    $deck_id = create_deck();
    $player_hand = draw_cards($deck_id, 2);
    $banker_hand = draw_cards($deck_id, 2);

    $player_points = calculate_points($player_hand);
    $banker_points = calculate_points($banker_hand);

    $_SESSION['deck_id'] = $deck_id;
    $_SESSION['player_hand'] = $player_hand;
    $_SESSION['banker_hand'] = $banker_hand;
    $_SESSION['player_points'] = $player_points;
    $_SESSION['banker_points'] = $banker_points;

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Baccarat Game</title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
        }

        .hand {
            display: inline-block;
            margin: 20px;
        }

        .card {
            display: inline-block;
            margin: 5px;
            padding: 10px;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
        }

        img {
            width: 80px;
            /* ปรับขนาดรูปภาพตามต้องการ */
        }
    </style>
</head>

<body>
    <h1>Baccarat Game</h1>
    <?php if (isset($_SESSION['player_hand'])) : ?>
        <div class="hand">
            <h2>Player's Hand (<?php echo $_SESSION['player_points']; ?> points)</h2>
            <?php foreach ($_SESSION['player_hand'] as $card) : ?>
                <div class="card">
                    <img src="<?php echo $card->image; ?>" alt="<?php echo $card->value . ' of ' . $card->suit; ?>">
                </div>
            <?php endforeach; ?>
        </div>
        <div class="hand">
            <h2>Banker's Hand (<?php echo $_SESSION['banker_points']; ?> points)</h2>
            <?php foreach ($_SESSION['banker_hand'] as $card) : ?>
                <div class="card">
                    <img src="<?php echo $card->image; ?>" alt="<?php echo $card->value . ' of ' . $card->suit; ?>">
                </div>
            <?php endforeach; ?>
        </div>
        <h2>
            <?php
            if ($_SESSION['player_points'] > $_SESSION['banker_points']) {
                echo "Player Wins!";
            } elseif ($_SESSION['player_points'] < $_SESSION['banker_points']) {
                echo "Banker Wins!";
            } else {
                echo "It's a Tie!";
            }
            ?>
        </h2>
        <form method="post">
            <button type="submit" name="deal">Deal New Hand</button>
        </form>
    <?php endif; ?>
</body>

</html>
<?php

session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $flip_result = (rand(0,1) == 0) ? 'Heads':'Tails';
    $_SESSION['result'] = $flip_result;

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coin Flip Game</title>
    <style>
        body{
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            background-color: gray;
        }
        .result{
            margin-top: 20px;
            font-size: 24px;
        }
        button{
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h1>Coin Flip Game</h1>
    <form method="post">
        <button type="submit">Flip Coin</button>
    </form>
    <div class="result">
        <?php
        if(isset($_SESSION['result'])){
            echo $_SESSION['result'];
            unset($_SESSION['result']);
        }
        ?>
    </div>
</body>
</html>
<?php

$servername = "localhost";
$username = "root";
$pwd = "";
$dbname = "esami";
$port = 3307;

$connection = new mysqli($servername, $username, $pwd, $dbname, $port);

if (!isset($connection)) {
    die(1);
}

$initialState = generateSudoku();
$gameId = random_int(1, 250);

$stmt = $connection->prepare("INSERT INTO sudoku (id, statoiniziale) VALUES (?, ?)");
$stmt->bind_param("ss", $gameId, $initialState);
$stmt->execute();
$stmt->close();

setcookie("game_id", $gameId, time() + 3600);
echo json_encode(["id" => $gameId, "state" => $initialState]);
exit;

function generateSudoku() {
    $state = str_repeat('0', 81);
    $state = str_split($state);
    
    for ($i = 0; $i < 8; $i++) {
        do {
            $pos = rand(0, 80);
            $num = rand(1, 9);
        } while ($state[$pos] !== '0' || !isValidPlacement($state, $pos, $num));
        
        $state[$pos] = (string)$num;
    }
    
    return implode('', $state);
}

function isValidPlacement(&$state, $pos, $num) {
    $row = intdiv($pos, 9);
    $col = $pos % 9;
    $boxRow = intdiv($row, 3) * 3;
    $boxCol = intdiv($col, 3) * 3;
    
    for ($i = 0; $i < 9; $i++) {
        if ($state[$row * 9 + $i] == $num || $state[$i * 9 + $col] == $num) {
            return false;
        }
    }
    
    for ($r = $boxRow; $r < $boxRow + 3; $r++) {
        for ($c = $boxCol; $c < $boxCol + 3; $c++) {
            if ($state[$r * 9 + $c] == $num) {
                return false;
            }
        }
    }
    
    return true;
}
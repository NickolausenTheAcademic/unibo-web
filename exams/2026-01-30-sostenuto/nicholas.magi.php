<?php

$server = "localhost";
$user = "root";
$pw = "";
$db = "iot_db";
$port = 3306;

$conn = new mysqli($server, $user, $pw, $db, $port);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $paramsRequired = [ "sensor_id", "value", "timestamp" ];
    foreach ($paramsRequired as $param) {
        if (!isset($_POST[$param]) || empty($_POST[$param])) {
            echo json_encode([
                "error" => true,
                "message" => "invalid " .  $param . " received!"
            ]);
            die(1);
        }
    }

    if (!is_numeric($_POST["value"])) {
        echo json_encode([
            "error" => true,
            "message" => "invalid value received!"
        ]);
        die(1);
    }

    $stmt = $conn->prepare("SELECT * FROM sensors WHERE sensor_id = ?;");
    $stmt->bind_param("i", $_POST["sensor_id"]);
    $stmt->execute();
    $res = $stmt->get_result();
    if (count($res->fetch_all(MYSQLI_ASSOC)) <= 0) {
        echo json_encode([
            "error" => true,
            "message" => "sensor not found!"
        ]);
        die(1);
    }

    $stmt = $conn->prepare("INSERT INTO `measurements` (`sensor_id`, `value`, `timestamp`) VALUES (?,?,?)");
    $stmt->bind_param("ids", $_POST["sensor_id"], $_POST["value"], $_POST["timestamp"]);
    $ok = $stmt->execute();

    if (!$ok) {
        echo json_encode([
            "error" => true,
            "message" => "could not insert measurements!"
        ]);
        die(1);
    }

    echo json_encode([
        "sensor_id" => $_POST["sensor_id"],
        "value" => $_POST["value"],
        "timestamp" => $_POST["timestamp"],
    ]);
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    /*
     * Correzione a posteriori: manca isset($_GET["sensor_id"])! 
     */
    $stmt = $conn->prepare("SELECT * FROM `sensors` WHERE sensor_id = ?;");
    $stmt->bind_param("i", $_GET["sensor_id"]);
    $stmt->execute();
    $res = $stmt->get_result();
    if (count($res->fetch_all(MYSQLI_ASSOC)) <= 0) {
        echo json_encode([
            "error" => true,
            "message" => "sensor not found!"
        ]);
        die(1);
    }

    $stmt = $conn->prepare("SELECT `value` FROM `measurements` WHERE sensor_id = ?;");
    $stmt->bind_param("i", $_GET["sensor_id"]);
    $stmt->execute();
    $res = $stmt->get_result();
    $measurements = $res->fetch_all(MYSQLI_ASSOC);
    $max = max(array_column($measurements, "value"));
    $min = min(array_column($measurements, "value"));
    $avg = 0;
    /*
    * Correzione a posteriori: $m è un array associativo!
    * per sommare correttamente i valori, usa $m["value"]! 
    */
    foreach ($measurements as $m) {
        $avg += $m;
    }
    $avg /= count($measurements);
    echo json_encode([
        "sensor_id" => $_GET["sensor_id"],
        "min" => $min,
        "max" => $max,
        "average" => $avg,
    ]);
    die(1);
}
/*
 * Correzione a posteriori: la consegna specifica 
 * "La pagina PHP deve SEMPRE restituire un JSON valido."
 * 
 * Forse era opportuno mettere un'istruzione finale del tipo:
 *  echo json_encode([
 *      "error" => true,
 *      "message" => "unknown request type: " . $_SERVER["REQUEST_METHOD"],
 *  ]);
 */
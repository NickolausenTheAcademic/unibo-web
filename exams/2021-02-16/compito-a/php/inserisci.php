<?php

$paramsRequired = [ "chiave", "valore", "metodo" ];
foreach ($paramsRequired as $param) {
    if (!isset($_POST[$param])) {
        echo json_encode([
            "success" => false,
            "message" => $param . "is not set!",
        ]);
        die(1);
    }
}

if (!in_array($_POST["metodo"], [ "cookie", "db" ])) {
    echo json_encode([
        "success" => false,
        "message" => "unknown method " . $_POST["metodo"],
    ]);
    die(1);
}

$key = $_POST["chiave"];
$value = $_POST["valore"];

switch ($_POST["metodo"]) {
    case 'cookie':
        $alreadySet = isset($_COOKIE[$key]);
        setcookie($key, $value);
        echo json_encode([
            "success" => true,
            "message" => ($alreadySet ? "updated" : "added") . " cookie for " . $key,
        ]);
        break;
    case 'db':
        $server = "localhost";
        $user = "root";
        $pw = "";
        $db = "esami";
        $port = 3307;

        $conn = new mysqli($server, $user, $pw, $db, $port);
        $stmt = $conn->prepare("SELECT * FROM `numeri` WHERE chiave = ?;");
        $stmt->bind_param("s", $key);
        $stmt->execute();
        $res = $stmt->get_result();
        $alreadySet = count($res->fetch_all(MYSQLI_ASSOC)) > 0;
        if ($alreadySet) {
            $stmt = $conn->prepare("UPDATE `numeri` SET valore = ? WHERE chiave = ?");
            $stmt->bind_param("ss", $key, $value);
            $stmt->execute();
            $success = $stmt->affected_rows > 0;
            $message = $success ? "successfully updated value for key " . $key : "something went wrong :/";
            echo json_encode([
                "success" => $success,
                "message" => $message
            ]);
            die($success ? 0 : 1);
        }
        $stmt = $conn->prepare("INSERT INTO `numeri` (chiave, valore) VALUES (?, ?)");
        $stmt->bind_param("ss", $key, $value);
        $success = $stmt->execute();
        $message = $success ? "successfully inserted pair (" . $key . ", " . $value .")" : "something went wrong :/";
        echo json_encode([
            "success" => $success,
            "message" => $message
        ]);
        die($success ? 0 : 1);
        break;
}
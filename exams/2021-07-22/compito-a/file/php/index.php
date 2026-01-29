<?php

$possibleValues = [ "extract", "new", "check" ];
if (!isset($_POST["action"])) {
    echo json_encode([
        "success" => false,
        "message" => "action not set",
    ]);
    die(1);
}

if (!in_array($_POST["action"], $possibleValues)) {
    echo json_encode([
        "success" => false,
        "message" => "unkown action value",
    ]);
    die(1);
}

$server = "localhost";
$username = "root";
$pw = "";
$dbname = "lotto";
$port = "3306";

$conn = new mysqli($server, $username, $pw, $dbname, $port);

switch ($_POST["action"]) {
    case 'extract':
        $randNumber = rand(1, 90);
        $stmt = $conn->prepare("SELECT * FROM `estrazione`");
        $res = $stmt->execute()->get_result();
        if (count($res->fetch_all(MYSQLI_ASSOC)) > 5) {
            echo json_encode([
                "success" => false,
                "message" => "more than 5 numbers are present",
            ]);
            die(1);
        }
        $stmt = $conn->prepare("SELECT * FROM `estrazione` WHERE numero = ?;");
        $stmt->bind_param("i", $randNumber);
        $res = $stmt->execute()->get_result();
        if (count($res->fetch_all(MYSQLI_ASSOC)) > 0) {
            echo json_encode([
                "success" => false,
                "message" => "number already present!",
            ]);
            die(1);
        }
        $stmt = $conn->prepare("INSERT INTO `estrazione` (`numero`) VALUES (?);");
        $stmt->bind_param("i", $randNumber);
        $res = $stmt->execute();
        echo json_encode([
            "success" => $res->affected_rows > 0,
            "message" => $res->affected_rows > 0 ? 
                "correctly inserted!" : "something went wrong...",
        ]);
        break;
    
    case 'new':
        $stmt = $conn->prepare("TRUNCATE TABLE `estrazione`");
        $res = $stmt->execute();
        echo json_encode([
            "success" => $res->affected_rows > 0,
            "message" => $res->affected_rows > 0 ? 
                "a new lotto has begun" : "something went wrong...",
        ]);
        break;

    case 'check':
        if (!isset($_POST['sequence'])) {
            echo json_encode([
                "success" => false,
                "message" => "sequence not set",
            ]);
            die(1);
        }
        $numbers = explode('-', $_POST['sequence']);
        foreach ($numbers as $num) {
            $stmt = $conn->prepare("SELECT * FROM `estrazione` WHERE numero = ?;");
            $stmt->bind_param("i", intval($num));
            $res = $stmt->execute()->get_result();
            
            if (count($res->fetch_all(MYSQLI_ASSOC)) <= 0) {
                echo json_encode([
                    "success" => true,
                    "message" => "no lotto :/",
                ]);
                exit(0);
            }
        }
        echo json_encode([
            "success" => true,
            "message" => "lotto!!",
        ]);
        break;
    default:
        # code...
        break;
}
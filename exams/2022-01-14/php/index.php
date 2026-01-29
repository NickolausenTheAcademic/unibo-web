<?php

$paramsToCheck = [ "nome", "cognome", "CF", "dataNascita", "sesso" ];
foreach ($paramsToCheck as $param) {
    if (!isset($_POST[$param])) {
        die(1);
    }
}

$servername = "localhost";
$username = "root";
$pw = "";
$dbname = "esami";
$port = 3307;

$conn = new mysqli($servername, $username, $pw, $dbname, $port);

assert(is_string($_POST["nome"]));
assert(is_string($_POST["nome"]));
assert(is_string($_POST["CF"]) && strlen($_POST["CF"]) === 16);
list($y, $m, $d) = explode("-", $_POST["dataNascita"]);
assert(is_numeric($m) && in_array(intval($m), range(1, 12)));
assert(is_numeric($d) && in_array(intval($d), range(1, 31)));
assert(in_array($_POST["sesso"], [ "M", "F", "A" ]));
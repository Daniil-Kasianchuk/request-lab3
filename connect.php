<?php

$dsn = "mysql:host=localhost;dbname=iteh2lb1var4";
$user = "root";
$pass = "";

try {
    $dbh = new PDO($dsn, $user, $pass);
} catch (PDOException $ex) {
    echo $ex->GetMessage();
}
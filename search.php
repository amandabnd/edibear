<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'traveylo_edibear');
define('DB_CHARSET', 'utf8');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

$pdo = new PDO(
    "mysql:host=".DB_HOST.";charset="DB_CHARSET.";dbname="DB_NAME, DB_USER, DB_PASSWORD,[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);


$stmt = $pdo->prepare("SELECT * FROM 'homework_details' WHERE 'title' LIKE ? OR 'description' LIKE ?" );
$stmt -> execute([
    "%".$_POST['search']. "%" , "%".$_POST['search']."%"
]);
$results = $stmt -> fetchAll();


?>
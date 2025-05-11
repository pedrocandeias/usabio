<?php
// Database config
$host = 'db:3306';
$dbname = 'db';
$usernameDB = 'db';
$passwordDB = 'db';
$charset  = 'utf8mb4';

/* $host  = 'localhost';
$dbname ='pedrocan_testflowdb';
$usernameDB = 'pedrocan_dbadmin';
$passwordDB = 'pcdbadminin1.';
$charset  = 'utf8mb4'; */

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $usernameDB, $passwordDB, $options);
} catch (\PDOException $e) {
    echo "Error connecting to database: " . $e->getMessage();
    exit;
}
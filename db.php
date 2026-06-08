<?php
// db.php — Database connection
$host = "ftpupload.net";
$dbname = "edunova.free.nf";
$username = "if0_42101555";
$password = "gcV9ungorL";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

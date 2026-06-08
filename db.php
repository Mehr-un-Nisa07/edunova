<?php
// db.php — Database connection
$host = "mysql.railway.internal";
$dbname = "railway";
$username = "root";
$password = "fKcsRUAldpWPIKzXrlxajoDMFyvZJmba";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

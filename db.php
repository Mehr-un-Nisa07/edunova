<?php
// db.php — Database connection
$host = getenv('mysql.railway.internal');
$dbname = getenv('railway');
$username = getenv('root');
$password = getenv('fKcsRUAldpWPIKzXrlxajoDMFyvZJmba');
$port = getenv('3306');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>



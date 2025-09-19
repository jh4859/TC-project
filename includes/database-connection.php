<?php 
$type   = 'mysql';
$server = 'localhost';
$db = 'aaaz4420';
$port = '3306';
$charset = 'utf8mb4';

$username = 'aaaz4420';
$password = 'parktj6051@';

$options = [
    PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES      => false
];
$dsn = "$type:host=$server;dbname=$db;port=$port;charset=$charset";

try{
    $pdo = new PDO($dsn, $username, $password, $options);
} catch(PDOException $e) {
    throw new PDOException($e->getMessage(), $e->getCode());
}
?>

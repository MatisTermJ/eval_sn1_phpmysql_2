<?php
session_start()
$pdo = new PDO('mysql:host=localhost;dbname=plus_ou_moins;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
?>
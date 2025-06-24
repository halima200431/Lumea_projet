<?php
$host = 'localhost';
$db   = 'qr_db';
$user = 'test';
$pass = '123';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=8889;dbname=$db;charset=$charset";
try {
    $pdo = new PDO($dsn, $user, $pass);
    echo "Connexion réussie !";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
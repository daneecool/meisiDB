<?php
$host = 'db';
$dbname = 'meishi_db';
$user = 'meishi_user';
$password = 'secret';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('DB接続エラー: ' . $e->getMessage());
}


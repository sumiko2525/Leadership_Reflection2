<?php
$dsn = 'mysql:host=mysql***9.db.sakura.ne.jp;dbname=l***p_****_u*;charset=utf8';
$user = '*p';
$password = '*******5';

try {
    $pdo = new PDO($dsn, $user, $password);
    // エラーモードを例外に設定
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit('DB接続エラー：' . $e->getMessage());
}
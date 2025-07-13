<?php
// ログインチェック関数
function loginCheck() {
    if (!isset($_SESSION['chk_ssid']) || $_SESSION['chk_ssid'] !== session_id()) {
        exit('LOGIN ERROR');
    }
    session_regenerate_id(true);
    $_SESSION['chk_ssid'] = session_id();
}

// 管理者権限チェック関数
function kanriCheck() {
    if (!isset($_SESSION['kanri_flg']) || $_SESSION['kanri_flg'] != 1) {
        exit('LOGIN ERROR');
    }
}

// DB接続関数
function db_conn() {
    try {
        $dbname = 'leadership_trust_user';
        $host = 'mysql3109.db.sakura.ne.jp';
        $user = 'leadership';
        $pass = 'Haruka2525';
        $dsn = "mysql:dbname=$dbname;charset=utf8;host=$host";

        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;

    } catch (PDOException $e) {
        exit('DBConnectError: ' . $e->getMessage());
    }
}

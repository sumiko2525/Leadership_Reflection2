<?php
// logout.php
// セッション開始
session_start();

// セッション変数を全て解除
$_SESSION = array();

// セッションIDのクッキーがあれば削除
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// セッションを破棄
session_destroy();

// ログインページへリダイレクト
header("Location: login.php");
exit();
?>

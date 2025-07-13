<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// セッションを開始（ログイン状態を記録するために必要）
session_start();


// POSTで受け取ったログインIDとパスワードを変数に代入
$lid = $_POST['lid']; // フォームから送られてきたログインID
$lpw = $_POST['lpw']; // フォームから送られてきたパスワード

// 共通関数を使えるように読み込む（DB接続など）
require_once('funcs.php');

// DBに接続（funcs.phpに定義したdb_conn()を使う）
$pdo = db_conn();

// SQL文を準備：入力されたID（lid）に一致し、かつ生存フラグ（life_flg=0）のユーザーを探す
$sql = 'SELECT * FROM lr_user WHERE lid = :lid AND life_flg = 0';

// SQL実行の準備（プリペアドステートメント）
$stmt = $pdo->prepare($sql);

// :lid にフォームからのIDをバインド（文字列として）
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);

// SQLを実行
$status = $stmt->execute();

// 実行に失敗した場合の処理
if ($status == false) {
    // SQLに問題があった場合はエラーメッセージを表示して終了
    echo 'SQLエラー';
    exit;
}

// 実行成功時、該当するユーザー情報を1件取得
$val = $stmt->fetch();

// 該当するユーザーが存在し、パスワードが一致するか確認
if ($val && password_verify($lpw, $val['lpw'])) {
    // 一致した場合：ログイン成功

    // セッションIDを再発行してセッションに保存（セキュリティ対策）
    session_regenerate_id(true);

    // ログイン情報をセッションに保存
    $_SESSION['chk_ssid'] = session_id();
    $_SESSION['user_id'] = $val['id'];         // DB上のユーザーID
    $_SESSION['user_name'] = $val['name'];     // 表示用ユーザー名
    $_SESSION['kanri_flg'] = $val['kanri_flg']; // 管理者権限（任意）

    // ログイン成功したのでトップページへリダイレクト
    header('Location: index.php');
    exit();
} else {
    // ログイン失敗時（IDが存在しない or パスワードが違う）
    echo 'ログインに失敗しました';
}

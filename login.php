<?php
// セッション開始（すでにログイン中のユーザーが login.php を再度開いた場合にリダイレクト）
session_start();

if (isset($_SESSION['chk_ssid']) && $_SESSION['chk_ssid'] === session_id()) {
    header("Location: view.php"); // すでにログイン済みなら一覧ページへ
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f9f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h1 {
            text-align: center;
            color: #00796b;
        }
        label {
            display: block;
            margin-top: 1rem;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            margin-top: 1.5rem;
            width: 100%;
            padding: 10px;
            background-color: #009688;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #00796b;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
        }
        .back-link a {
            color: #009688;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>ログイン</h1>

        <!-- エラーがある場合、メッセージを表示 -->
        <?php if (isset($_GET['error'])): ?>
            <p style="color:red; text-align:center;">ユーザーIDまたはパスワードが違います。</p>
        <?php endif; ?>

        <!-- ログインフォーム -->
        <form action="login.act.php" method="POST">
            <label for="lid">ユーザーID：</label>
            <input type="text" name="lid" id="lid" required>

            <label for="lpw">パスワード：</label>
            <input type="password" name="lpw" id="lpw" required>

            <input type="submit" value="ログイン">
        </form>
    </div>
</body>
</html>

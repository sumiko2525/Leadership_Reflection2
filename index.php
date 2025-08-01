<?php
session_start();
require_once('funcs.php');
loginCheck(); // ログインしていない場合は強制終了
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Leadership Reflection Note</title>
    <style>
        body {
            font-family: "Hiragino Kaku Gothic ProN", sans-serif;
            background-color: #f0f9f8;
            color: #333;
            padding: 2rem;
            text-align: center;
        }

        h1 {
            color: #00796b;
        }

        .form-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: inline-block;
            margin-bottom: 2rem;
        }

        input, textarea {
            width: 90%;
            margin: 5px 0;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"], .btn-link {
            background-color: #009688;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            display: inline-block;
            text-decoration: none;
        }

        input[type="submit"]:hover, .btn-link:hover {
            background-color: #00796b;
        }
    </style>
</head>
<body>

    <h1>Leadership Reflection Note</h1>

    <div class="form-container">
        <form method="POST" action="insert.php">
            <p><input type="date" name="log_date" required></p>
            <p><input type="text" name="title" placeholder="タイトル" required></p>
            <p><textarea name="reflection" placeholder="ふりかえり内容" required></textarea></p>
            <p>🔥活力レベル（0〜10）:<input type="number" name="energy_level" min="0" max="10" required></p>
            <p>🌱信頼レベル（0〜10）:<input type="number" name="trust_level" min="0" max="10" required></p>
            <p><input type="text" name="emotion" placeholder="気持ち（任意）"></p>
            <p><textarea name="learning" placeholder="学び（任意）"></textarea></p>
            <p><textarea name="next_action" placeholder="次の行動（任意）"></textarea></p>
            <p><input type="submit" value="記録する"></p>
        </form>

        <!-- ✅ 過去の記録を見るボタンを分岐 -->
        <?php if ($_SESSION['kanri_flg'] == 1): ?>
            <a class="btn-link" href="view.php">📄 過去の記録を見る</a>
        <?php else: ?>
            <a class="btn-link" href="mypage.php">📄 過去の記録を見る</a>
        <?php endif; ?>
    </div>

    <div>
        <a class="btn-link" href="logout.php">ログアウト</a>
    </div>

</body>
</html>

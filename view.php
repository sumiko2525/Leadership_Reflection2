<?php
// セッションを開始（ログイン中かどうかをチェックするために必要）
session_start();

// 共通関数ファイル（DB接続やログインチェックなど）を読み込む
require_once('funcs.php');

// ログインしていない場合は強制終了させる関数（LOGIN ERROR）
loginCheck(); // ←ログイン済みでなければ先に進めません

// DB接続関数（PDOオブジェクトを返す）を使ってデータベースへ接続
$pdo = db_conn();

// SQL文の準備（管理者かどうかで分岐）
if ($_SESSION['kanri_flg'] == 1) {
    // 管理者は全データを表示
    $sql = 'SELECT * FROM leadership_note WHERE deleted = 0 ORDER BY log_date DESC';
    $stmt = $pdo->prepare($sql);
} else {
    // 一般ユーザーは自分の記録のみ表示
    $sql = 'SELECT * FROM leadership_note WHERE deleted = 0 AND user_id = :user_id ORDER BY log_date DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
}

// SQL実行
$stmt->execute();

// 取得したすべてのレコードを連想配列として格納
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Leadership Reflection Note 一覧</title>
    <style>
        body {
            font-family: "Hiragino Kaku Gothic ProN", sans-serif;
            background-color: #f0f9f8;
            color: #333;
            padding: 2rem;
        }

        h1 {
            text-align: center;
            color: #00796b;
        }

        a {
            text-decoration: none;
            color: #009688;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 150, 136, 0.2);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #00796b;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #e0f2f1;
        }

        tr:hover {
            background-color: #b2dfdb;
        }

        .btn {
            display: inline-block;
            padding: 8px 14px;
            font-size: 0.9rem;
            color: white;
            background-color: #009688;
            border: none;
            border-radius: 4px;
            text-align: center;
        }

        .btn:hover {
            background-color: #00796b;
        }

        .delete-btn {
            background-color: #e57373;
        }

        .delete-btn:hover {
            background-color: #d32f2f;
        }

        .top-link, .logout-link {
            display: block;
            margin-top: 1rem;
            text-align: center;
        }

        form {
            margin-bottom: 1rem;
        }

        .checkbox {
            text-align: center;
        }
    </style>
</head>
<body>

    <h1>Leadership Reflection Note 一覧</h1>

    <!-- 一覧表示＋チェックボックス付き削除フォーム -->
    <form method="POST" action="delete.php">
        <table>
            <tr>
                <th>選択</th>
                <th>日付</th>
                <th>タイトル</th>
                <th>ふりかえり</th>
                <th>🔥活力</th>
                <th>🌱信頼</th>
                <th>学び</th>
                <th>次の行動</th>
                <th>気持ち</th>
                <th>編集</th>
            </tr>

            <!-- 1レコードずつ表示 -->
            <?php foreach ($notes as $note): ?>
                <tr>
                    <td class="checkbox">
                        <!-- 削除対象のチェックボックス（idで指定） -->
                        <input type="checkbox" name="delete_ids[]" value="<?= htmlspecialchars($note['id']) ?>">
                    </td>
                    <td><?= htmlspecialchars($note['log_date']) ?></td>
                    <td><?= htmlspecialchars($note['title']) ?></td>
                    <td><?= nl2br(htmlspecialchars($note['reflection'])) ?></td>
                    <td><?= htmlspecialchars($note['energy_level']) ?></td>
                    <td><?= htmlspecialchars($note['trust_level']) ?></td>
                    <td><?= nl2br(htmlspecialchars($note['learning'])) ?></td>
                    <td><?= nl2br(htmlspecialchars($note['next_action'])) ?></td>
                    <td><?= htmlspecialchars($note['emotion']) ?></td>
                    <td>
                        <!-- 編集ページへ遷移 -->
                        <a class="btn" href="edit.php?id=<?= $note['id'] ?>">編集</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- チェックしたものを削除するボタン -->
        <button class="btn delete-btn" type="submit">選択したものを削除</button>
    </form>

    <!-- 新規登録画面へ -->
    <div class="top-link">
        <a class="btn" href="index.php">＋新しく記録する</a>
    </div>

    <!-- ログアウトへのリンク -->
    <div class="logout-link">
        <a class="btn" href="logout.php">ログアウト</a>
    </div>

</body>
</html>

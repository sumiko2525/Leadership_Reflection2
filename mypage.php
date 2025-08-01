<?php
session_start();           // セッションスタート
require_once('funcs.php'); // 関数ファイル読み込み
loginCheck();              // ログインチェック
require_once('db_connect.php');

$user_id = $_SESSION['user_id']; // ログイン中のユーザーID

// 自分の記録のみ取得（論理削除されていない）
$sql = 'SELECT * FROM leadership_note WHERE user_id = :user_id AND deleted = 0 ORDER BY log_date DESC';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>マイページ - Leadership Reflection Note</title>
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

        .top-link, .logout-link, .export-link {
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
    <h1><?= htmlspecialchars($_SESSION['user_name']) ?> さんの記録一覧</h1>

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
            <?php foreach ($notes as $note): ?>
                <tr>
                    <td class="checkbox">
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
                    <td><a class="btn" href="edit.php?id=<?= $note['id'] ?>">編集</a></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <button class="btn delete-btn" type="submit">選択したものを削除</button>
    </form>

    <div class="top-link">
        <a class="btn" href="index.php">＋新しく記録する</a>
    </div>
    <div class="export-link">
        <a class="btn" href="export_csv.php">📄 CSV出力</a>
    </div>
    <div class="logout-link">
        <a class="btn" href="logout.php">ログアウト</a>
    </div>

<?php
// 日付ごとの平均活力・信頼を計算
$dailyData = [];
foreach ($notes as $note) {
    $date = $note['log_date'];
    if (!isset($dailyData[$date])) {
        $dailyData[$date] = ['energy_sum' => 0, 'trust_sum' => 0, 'count' => 0];
    }
    $dailyData[$date]['energy_sum'] += (int)$note['energy_level'];
    $dailyData[$date]['trust_sum'] += (int)$note['trust_level'];
    $dailyData[$date]['count']++;
}

// Chart.js 用にデータを整形
$labels = [];
$energyData = [];
$trustData = [];
foreach ($dailyData as $date => $data) {
    $labels[] = $date;
    $energyData[] = round($data['energy_sum'] / $data['count'], 1);
    $trustData[] = round($data['trust_sum'] / $data['count'], 1);
}
?>

<!-- グラフ描画エリア -->
<h2 style="text-align:center; margin-top:40px;">📈 活力と信頼の推移</h2>
<canvas id="myChart" width="400" height="200"></canvas>

<!-- Chart.jsのCDNを読み込み -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('myChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [
            {
                label: '活力の平均',
                data: <?= json_encode($energyData) ?>,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                fill: false,
                tension: 0.3
            },
            {
                label: '信頼の平均',
                data: <?= json_encode($trustData) ?>,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                fill: false,
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, max: 10 }
        }
    }
});
</script>

</body>
</html>

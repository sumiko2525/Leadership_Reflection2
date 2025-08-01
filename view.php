<?php
session_start();
require_once('funcs.php');
loginCheck();
$pdo = db_conn();

// -------------------------
// 🔍 検索キーワードの取得
// -------------------------
$keyword = $_GET['keyword'] ?? '';

// -------------------------
// 🔄 並び替え用の取得
// -------------------------
$order = $_GET['order'] ?? 'log_date_desc';
switch ($order) {
    case 'log_date_asc': $order_sql = 'log_date ASC'; break;
    case 'energy_desc': $order_sql = 'energy_level DESC'; break;
    case 'energy_asc': $order_sql = 'energy_level ASC'; break;
    case 'trust_desc': $order_sql = 'trust_level DESC'; break;
    case 'trust_asc': $order_sql = 'trust_level ASC'; break;
    default: $order_sql = 'log_date DESC';
}

// -------------------------
// SQLのベース
// -------------------------
if ($_SESSION['kanri_flg'] == 1) {
    $sql = "SELECT * FROM leadership_note WHERE deleted = 0";
} else {
    $sql = "SELECT * FROM leadership_note WHERE deleted = 0 AND user_id = :user_id";
}

// 🔍 検索条件を追加
if ($keyword !== '') {
    $sql .= " AND (title LIKE :kw OR reflection LIKE :kw OR learning LIKE :kw OR next_action LIKE :kw OR emotion LIKE :kw)";
}

$sql .= " ORDER BY $order_sql";

$stmt = $pdo->prepare($sql);

// バインド
if ($_SESSION['kanri_flg'] != 1) {
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
}
if ($keyword !== '') {
    $stmt->bindValue(':kw', "%$keyword%", PDO::PARAM_STR);
}

$stmt->execute();
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 📊 平均と合計の計算
$total_energy = $total_trust = 0;
$count = count($notes);
foreach ($notes as $n) {
    $total_energy += $n['energy_level'];
    $total_trust += $n['trust_level'];
}
$avg_energy = $count ? round($total_energy / $count, 1) : 0;
$avg_trust = $count ? round($total_trust / $count, 1) : 0;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>Leadership Reflection Note 一覧</title>
<style>
    body { font-family: sans-serif; background: #f0f9f8; padding: 2rem; }
    h1 { text-align: center; color: #00796b; }
    .summary { text-align: center; margin-bottom: 1rem; }
    .sort-buttons { text-align: center; margin-bottom: 1rem; }
    .sort-buttons a { margin: 0 5px; padding: 5px 10px; background: #009688; color: #fff; border-radius: 4px; text-decoration: none; }
    .search-box { text-align: center; margin-bottom: 1rem; }
    table { width: 100%; border-collapse: collapse; background: #fff; }
    th, td { padding: 10px; border: 1px solid #ccc; }
    th { background: #00796b; color: white; }
    tr:nth-child(even) { background: #e0f2f1; }
</style>
</head>
<body>

<h1>Leadership Reflection Note 一覧</h1>

<!-- CSVダウンロードボタン -->
<div style="text-align:center; margin-bottom: 15px;">
    <a class="btn" href="export_csv.php">📄 CSVダウンロード</a>
</div>

<!-- 📊 平均表示 -->
<div class="summary">
    📊 平均活力: <?= $avg_energy ?> ｜ 平均信頼: <?= $avg_trust ?> （記録数: <?= $count ?>件）
</div>

<!-- 🔍 検索フォーム -->
<div class="search-box">
    <form method="GET">
        <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="キーワードを入力">
        <input type="hidden" name="order" value="<?= htmlspecialchars($order) ?>">
        <button type="submit">検索</button>
        <a href="view.php">クリア</a>
    </form>
</div>

<!-- 🔄 並び替えボタン -->
<div class="sort-buttons">
    並び替え：
    <a href="?order=log_date_desc&keyword=<?= urlencode($keyword) ?>">日付(新しい順)</a>
    <a href="?order=log_date_asc&keyword=<?= urlencode($keyword) ?>">日付(古い順)</a>
    <a href="?order=energy_desc&keyword=<?= urlencode($keyword) ?>">活力(高い順)</a>
    <a href="?order=energy_asc&keyword=<?= urlencode($keyword) ?>">活力(低い順)</a>
    <a href="?order=trust_desc&keyword=<?= urlencode($keyword) ?>">信頼(高い順)</a>
    <a href="?order=trust_asc&keyword=<?= urlencode($keyword) ?>">信頼(低い順)</a>
</div>

<form method="POST" action="delete.php">
<table>
    <tr>
        <th>選択</th><th>日付</th><th>タイトル</th><th>ふりかえり</th><th>🔥活力</th><th>🌱信頼</th><th>学び</th><th>次の行動</th><th>気持ち</th><th>編集</th>
    </tr>
    <?php foreach ($notes as $note): ?>
    <tr>
        <td><input type="checkbox" name="delete_ids[]" value="<?= $note['id'] ?>"></td>
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

<button type="submit">選択したものを削除</button>
</form>

<div class="top-link"><a href="index.php">＋新しく記録する</a></div>
<div class="logout-link"><a href="logout.php">ログアウト</a></div>

</body>
</html>

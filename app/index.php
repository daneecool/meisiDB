<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

require 'db.php';

// フィルタ入力取得
$received_from = $_GET['received_from'] ?? '';
$received_to = $_GET['received_to'] ?? '';
$company = $_GET['company'] ?? '';
$name = $_GET['name'] ?? '';
$is_public_only = isset($_GET['is_public_only']) ? true : false;

// ソート処理
$sort_column = $_GET['sort'] ?? 'id';
$sort_order = $_GET['order'] ?? 'asc';
$valid_sort_columns = ['id', 'received_date', 'company', 'name', 'tel', 'email', 'owner_name'];
$valid_sort_orders = ['asc', 'desc'];

if (!in_array($sort_column, $valid_sort_columns)) {
    $sort_column = 'id';
}
if (!in_array($sort_order, $valid_sort_orders)) {
    $sort_order = 'asc';
}

// ユーザー情報
$user_id = $_SESSION['username'];
$user_name = htmlspecialchars($_SESSION['display_name']);

// ページング
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

// SQL構築用
$sql_base = "FROM meishi m
             LEFT JOIN users u ON m.user_id = u.username
             WHERE 1=1";
$sql_where = "";
$params = [];

if ($is_public_only) {
    $sql_where .= " AND m.is_public = TRUE";
} else {
    // Show own cards and public cards
    $sql_where .= " AND (m.user_id = :user_id OR m.is_public = TRUE)";
    $params[':user_id'] = $user_id;
}
if ($received_from !== '') {
    $sql_where .= " AND m.received_date >= :received_from";
    $params[':received_from'] = $received_from;
}
if ($received_to !== '') {
    $sql_where .= " AND m.received_date <= :received_to";
    $params[':received_to'] = $received_to;
}
if ($company !== '') {
    $sql_where .= " AND m.company ILIKE :company";
    $params[':company'] = '%' . $company . '%';
}
if ($name !== '') {
    $sql_where .= " AND m.name ILIKE :name";
    $params[':name'] = '%' . $name . '%';
}

// 総件数取得
$count_sql = "SELECT COUNT(*) $sql_base $sql_where";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();
$total_pages = max(1, ceil($total_records / $per_page));

// 名刺取得
$list_sql = "SELECT m.*, u.display_name AS owner_name $sql_base $sql_where ORDER BY $sort_column $sort_order LIMIT :limit OFFSET :offset";
$params[':limit'] = $per_page;
$params[':offset'] = $offset;

$stmt = $pdo->prepare($list_sql);
foreach ($params as $key => &$val) {
    $stmt->bindValue($key, $val, in_array($key, [':limit', ':offset']) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->execute();
$meishi_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>名刺一覧</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-900">
<div class="text-right mb-4">
  <span class="mr-4 text-sm">ようこそ、<?php echo $user_name ?> さん</span>
  <a href="logout.php" class="bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700">ログアウト</a>
</div>

<div class="max-w-7xl mx-auto p-6">
  <img src="title.png" alt="タイトル画像" class="h-12 mb-6">

  <form method="get" class="bg-white p-4 rounded shadow mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="flex space-x-2 col-span-2">
      <input type="date" name="received_from" value="<?= htmlspecialchars($received_from) ?>" class="border rounded p-2 w-full">
      <span class="self-center">～</span>
      <input type="date" name="received_to" value="<?= htmlspecialchars($received_to) ?>" class="border rounded p-2 w-full">
    </div>
    <input type="text" name="company" placeholder="会社名" value="<?= htmlspecialchars($company) ?>" class="border rounded p-2 w-full">
    <input type="text" name="name" placeholder="名前" value="<?= htmlspecialchars($name) ?>" class="border rounded p-2 w-full">
    <label class="col-span-4 flex items-center space-x-2">
      <input type="checkbox" name="is_public_only" <?= $is_public_only ? 'checked' : '' ?>>
      <span>公開名刺も表示する</span>
    </label>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-400 w-full md:col-span-4">検索</button>
  </form>

  <div class="mb-4">
    <a href="add.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">＋ 名刺登録</a>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full bg-white border border-gray-200 shadow rounded">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2 border">
            No
          </th>
          <th class="p-2 border">
            <a href="?sort=received_date&order=<?= $sort_column === 'received_date' && $sort_order === 'asc' ? 'desc' : 'asc' ?>">
              受領日
              <?php if ($sort_column === 'received_date'): ?>
                <?php if ($sort_order === 'desc'): ?>
                  <span class="text-black font-bold">▼</span>
                <?php else: ?>
                  <span class="text-black font-bold">▲</span>
                <?php endif; ?>
              <?php endif; ?>
            </a>
          </th>
          <th class="p-2 border">
            <a href="?sort=company&order=<?= $sort_column === 'company' && $sort_order === 'asc' ? 'desc' : 'asc' ?>">
              会社名
              <?php if ($sort_column === 'company'): ?>
                <?php if ($sort_order === 'desc'): ?>
                  <span class="text-black font-bold">▼</span>
                <?php else: ?>
                  <span class="text-black font-bold">▲</span>
                <?php endif; ?>
              <?php endif; ?>
            </a>
          </th>
          <th class="p-2 border">
            <a href="?sort=name&order=<?= $sort_column === 'name' && $sort_order === 'asc' ? 'desc' : 'asc' ?>">
              名前
              <?php if ($sort_column === 'name'): ?>
                <?php if ($sort_order === 'desc'): ?>
                  <span class="text-black font-bold">▼</span>
                <?php else: ?>
                  <span class="text-black font-bold">▲</span>
                <?php endif; ?>
              <?php endif; ?>
            </a>
          </th>
          <th class="p-2 border">
            <a href="?sort=tel&order=<?= $sort_column === 'tel' && $sort_order === 'asc' ? 'desc' : 'asc' ?>">
              電話番号
              <?php if ($sort_column === 'tel'): ?>
                <?php if ($sort_order === 'desc'): ?>
                  <span class="text-black font-bold">▼</span>
                <?php else: ?>
                  <span class="text-black font-bold">▲</span>
                <?php endif; ?>
              <?php endif; ?>
            </a>
          </th>
          <th class="p-2 border">
            <a href="?sort=email&order=<?= $sort_column === 'email' && $sort_order === 'asc' ? 'desc' : 'asc' ?>">
              メール
              <?php if ($sort_column === 'email'): ?>
                <?php if ($sort_order === 'desc'): ?>
                  <span class="text-black font-bold">▼</span>
                <?php else: ?>
                  <span class="text-black font-bold">▲</span>
                <?php endif; ?>
              <?php endif; ?>
            </a>
          </th>
          <th class="p-2 border">
            <a href="?sort=owner_name&order=<?= $sort_column === 'owner_name' && $sort_order === 'asc' ? 'desc' : 'asc' ?>">
              受領者
              <?php if ($sort_column === 'owner_name'): ?>
                <?php if ($sort_order === 'desc'): ?>
                  <span class="text-black font-bold">▼</span>
                <?php else: ?>
                  <span class="text-black font-bold">▲</span>
                <?php endif; ?>
              <?php endif; ?>
            </a>
          </th>
          <th class="p-2 border" colspan="2">画像</th>
          <th class="p-2 border">操作</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($meishi_list as $i => $m): ?>
          <tr>
            <td class="p-2 border text-center"><?= ($offset + $i + 1) ?></td>
            <td class="p-2 border text-center"><?= htmlspecialchars($m['received_date']) ?></td>
            <td class="p-2 border"><?= htmlspecialchars($m['company']) ?></td>
            <td class="p-2 border"><?= htmlspecialchars($m['name']) ?></td>
            <td class="p-2 border"><?= htmlspecialchars($m['tel']) ?></td>
            <td class="p-2 border">
              <?php if (!empty($m['email'])): ?>
                <a href="mailto:<?= htmlspecialchars($m['email']) ?>" class="text-blue-600 underline hover:text-blue-800">
                  <?= htmlspecialchars($m['email']) ?>
                </a>
              <?php endif; ?>
            </td>
            <td class="border px-2 py-1"><?= htmlspecialchars($m['owner_name'] ?? $m['user_id']) ?></td>
            <td class="p-2 border text-center" colspan="2">
              <div x-data="{ show: false, side: 'front' }">
                <?php if (!empty($m['image_front']) || !empty($m['image_back'])): ?>
                  <?php if (!empty($m['image_front'])): ?>
                    <img src="<?= htmlspecialchars($m['image_front']) ?>" width="100" class="inline cursor-pointer rounded shadow" @click="side='front'; show=true">
                  <?php endif; ?>
                  <?php if (!empty($m['image_back'])): ?>
                    <img src="<?= htmlspecialchars($m['image_back']) ?>" width="100" class="inline cursor-pointer rounded shadow ml-2" @click="side='back'; show=true">
                  <?php endif; ?>
                <?php else: ?>
                  <span class="text-gray-400">No image</span>
                <?php endif; ?>
                <div x-show="show"
                     x-transition:enter="transition-opacity duration-200"
                     x-transition:leave="transition-opacity duration-150"
                     class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50"
                     style="display: none"
                     @click.away="show = false">
                  <div class="bg-white p-4 rounded shadow-lg relative max-w-full w-[90%] md:w-[500px]">
                    <button class="absolute top-2 right-2 text-gray-500 hover:text-black" @click="show = false">✕</button>

                    <?php if (!empty($m['image_front'])): ?>
                      <div x-show="side === 'front'" class="text-center h-[400px] flex items-center justify-center">
                        <img src="<?= htmlspecialchars($m['image_front']) ?>" class="max-h-full max-w-full object-contain rounded shadow">
                      </div>
                    <?php endif; ?>

                    <?php if (!empty($m['image_back'])): ?>
                      <div x-show="side === 'back'" class="text-center h-[400px] flex items-center justify-center">
                        <img src="<?= htmlspecialchars($m['image_back']) ?>" class="max-h-full max-w-full object-contain rounded shadow">
                      </div>
                    <?php endif; ?>

                    <?php if (!empty($m['image_front']) && !empty($m['image_back'])): ?>
                      <div class="mb-4 text-center">
                        <button @click="side = (side === 'front' ? 'back' : 'front')" class="px-4 py-1 rounded bg-blue-600 text-white">
                          表/裏 切替
                        </button>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </td>
            <td class="p-2 border whitespace-nowrap text-center">
              <a href="edit.php?id=<?= htmlspecialchars($m['id']) ?>" class="inline-block bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">編集</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- ページネーション -->
  <?php if ($total_pages > 1): ?>
    <div class="mt-6 text-center space-x-1">
      <?php for ($p = 1; $p <= $total_pages; $p++): ?>
        <?php
          $query = $_GET;
          $query['page'] = $p;
          $query_str = http_build_query($query);
        ?>
        <a href="?<?= htmlspecialchars($query_str) ?>"
           class="inline-block px-3 py-1 border rounded <?= $p == $page ? 'bg-blue-600 text-white' : 'bg-white text-blue-600 hover:bg-blue-100' ?>">
           <?= $p ?>
        </a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>

</div>
</body>
</html>
<?php
require 'db.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM meishi WHERE id = :id");
$stmt->execute([':id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die('データが見つかりません');
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <title>名刺編集</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
  <div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">名刺編集</h1>
    <!-- 更新フォーム -->
    <form id="updateForm" action="update.php" method="post" enctype="multipart/form-data" class="bg-white p-6 rounded shadow space-y-4">
      <input type="hidden" name="id" value="<?= $data['id'] ?>">
      
      <div>
        <label class="block font-semibold mb-1">受領日</label>
        <input type="date" name="received_date" value="<?= $data['received_date'] ?>" class="w-full border rounded p-2" />
      </div>

      <div>
        <label class="block font-semibold mb-1">会社名</label>
        <input type="text" name="company" value="<?= htmlspecialchars($data['company']) ?>" class="w-full border rounded p-2" />
      </div>

      <div>
        <label class="block font-semibold mb-1">名前</label>
        <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" class="w-full border rounded p-2" />
      </div>

      <div>
        <label class="block font-semibold mb-1">電話番号</label>
        <input type="text" name="tel" value="<?= htmlspecialchars($data['tel']) ?>" class="w-full border rounded p-2" />
      </div>

      <div>
        <label class="block font-semibold mb-1">メールアドレス</label>
        <input type="text" name="email" value="<?= htmlspecialchars($data['email']) ?>" class="w-full border rounded p-2" />
      </div>

      <div>
        <label class="block font-semibold mb-1">備考</label>
        <textarea name="notes" class="w-full border rounded p-2" rows="4"><?= htmlspecialchars($data['notes']) ?></textarea>
      </div>

      <div>
        <label class="block font-semibold mb-1">画像（表）</label>
        <input type="file" name="image_front" class="block w-full text-sm" />
        <?php if ($data['image_front']): ?>
          <img src="<?= $data['image_front'] ?>" class="mt-2 w-32 rounded shadow" />
        <?php endif; ?>
      </div>

      <div>
        <label class="block font-semibold mb-1">画像（裏）</label>
        <input type="file" name="image_back" class="block w-full text-sm" />
        <?php if ($data['image_back']): ?>
          <img src="<?= $data['image_back'] ?>" class="mt-2 w-32 rounded shadow" />
        <?php endif; ?>
      </div>

      <div>
        <label class="block font-semibold mb-1">公開する</label>
        <input type="checkbox" name="is_public" class="mr-2" <?= $data['is_public'] ? 'checked' : '' ?>>
      </div>

    </form>

    <!-- 削除フォーム -->
    <form id="deleteForm" action="delete.php" method="post" onsubmit="return confirm('本当に削除しますか？')">
      <input type="hidden" name="id" value="<?= $data['id'] ?>" />
    </form>

    <!-- フッターボタン -->
    <div class="flex justify-end gap-4 mt-6">
      <button onclick="history.back()" class="inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">戻る</button>
      <button type="submit" form="deleteForm" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
        削除
      </button>
      <button type="submit" form="updateForm" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        更新
      </button>
    </div>
  </div>
</body>
</html>


<?php
require 'db.php';

$id = $_POST['id'] ?? 0;

// 画像ファイルも削除
$stmt = $pdo->prepare("SELECT image_front, image_back FROM meishi WHERE id = :id");
$stmt->execute([':id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
    $files = [];

    if (!empty($data['image_front'])) {
        $files[] = $data['image_front'];
    }
    if (!empty($data['image_back'])) {
        $files[] = $data['image_back'];
    }

    // 重複を除去してから削除
    $files = array_unique($files);

    foreach ($files as $file) {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM meishi WHERE id = :id");
    $stmt->execute([':id' => $id]);
}


header('Location: index.php');
exit;



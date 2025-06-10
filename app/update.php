<?php
require 'db.php';
session_start();

function uploadImage($fileKey, $oldPath) {
    $uploadDir = 'uploads/';
    if (!empty($_FILES[$fileKey]['name'])) {
        $fileName = time() . '_' . basename($_FILES[$fileKey]['name']);
        $filePath = $uploadDir . $fileName;
        move_uploaded_file($_FILES[$fileKey]['tmp_name'], $filePath);
        // 古いファイル削除（同じパスでなければ）
        if ($oldPath && $oldPath !== $filePath && file_exists($oldPath)) {
            unlink($oldPath);
        }
        return $filePath;
    }
    return $oldPath;
}

$user_id = $_SESSION['username'] ?? null;
if (!$user_id) {
    echo '<div style="text-align:center; margin-top:50px;">
            <p>ログインが必要です</p>
            <a href="login.php" style="display:inline-block; margin-top:20px; padding:10px 20px; background:#2563eb; color:#fff; border-radius:5px; text-decoration:none;">ログインページへ</a>
          </div>';
    exit;
}

// 所有確認
$stmt = $pdo->prepare("SELECT image_front, image_back FROM meishi WHERE id = :id AND user_id = :user_id");
$stmt->execute([
    ':id' => $_POST['id'],
    ':user_id' => $user_id
]);
$old = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$old) {
    die('対象の名刺が存在しない、または権限がありません');
}

// 更新処理
$sql = "UPDATE meishi SET
    received_date = :received_date,
    company = :company,
    name = :name,
    tel = :tel,
    email = :email,
    notes = :notes,
    image_front = :image_front,
    image_back = :image_back,
    is_public = :is_public
    WHERE id = :id AND user_id = :user_id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':received_date' => $_POST['received_date'],
    ':company' => $_POST['company'],
    ':name' => $_POST['name'],
    ':tel' => $_POST['tel'],
    ':email' => $_POST['email'],
    ':notes' => $_POST['notes'],
    ':image_front' => uploadImage('image_front', $old['image_front']),
    ':image_back' => uploadImage('image_back', $old['image_back']),
    ':is_public' => isset($_POST['is_public']) ? 1 : 0,
    ':id' => $_POST['id'],
    ':user_id' => $user_id
]);

header('Location: index.php');
exit;


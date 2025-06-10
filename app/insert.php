<?php
require 'db.php';
session_start(); // セッションからログインユーザ取得

function uploadImage($fileKey) {
    $uploadDir = 'uploads/';
    if (!empty($_FILES[$fileKey]['name'])) {
        $fileName = time() . '_' . basename($_FILES[$fileKey]['name']);
        $filePath = $uploadDir . $fileName;
        move_uploaded_file($_FILES[$fileKey]['tmp_name'], $filePath);
        return $filePath;
    }
    return null;
}

// セッションからユーザID（LDAP認証したときに保存した名前）を取得
$user_id = $_SESSION['username'] ?? null;

if (!$user_id) {
    echo '<div style="text-align:center; margin-top:50px;">
            <p>ログインが必要です</p>
            <a href="login.php" style="display:inline-block; margin-top:20px; padding:10px 20px; background:#2563eb; color:#fff; border-radius:5px; text-decoration:none;">ログインページへ</a>
          </div>';
    exit;
}


$sql = "INSERT INTO meishi (received_date, company, name, tel, email, notes, image_front, image_back, user_id, is_public)
        VALUES (:received_date, :company, :name, :tel, :email, :notes, :image_front, :image_back, :user_id, :is_public)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':received_date' => $_POST['received_date'],
    ':company' => $_POST['company'],
    ':name' => $_POST['name'],
    ':tel' => $_POST['tel'],
    ':email' => $_POST['email'],
    ':notes' => $_POST['notes'],
    ':image_front' => uploadImage('image_front'),
    ':image_back' => uploadImage('image_back'),
    ':user_id' => $_SESSION['username'],
    ':is_public' => isset($_POST['is_public']) ? 1 : 0,  // ← チェックボックスから取得
]);

header('Location: index.php');
exit;


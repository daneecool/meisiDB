<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>名刺登録</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">名刺登録フォーム</h1>

    <form action="insert.php" method="post" enctype="multipart/form-data" class="bg-white p-6 rounded shadow space-y-4">

        <div>
            <label class="block font-semibold mb-1">受領日</label>
            <input type="date" name="received_date" id="received_date" class="w-full border rounded p-2" required>
        </div>



        <div>
            <label class="block font-semibold mb-1">会社名</label>
            <input type="text" name="company" class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block font-semibold mb-1">名前</label>
            <input type="text" name="name" class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block font-semibold mb-1">電話番号</label>
            <input type="text" name="tel" class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block font-semibold mb-1">メールアドレス</label>
            <input type="text" name="email" class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block font-semibold mb-1">備考</label>
            <textarea name="notes" class="w-full border rounded p-2" rows="4"></textarea>
        </div>

        <div>
            <label class="block font-semibold mb-1">名刺画像（表）</label>
            <input type="file" name="image_front" class="block w-full text-sm">
        </div>
        <div>
            <label class="block font-semibold mb-1">名刺画像（裏）</label>
            <input type="file" name="image_back" class="block w-full text-sm">
        </div>
        <div>
            <label class="block font-semibold mb-1">公開する</label>
            <input type="checkbox" name="is_public" class="mr-2" checked>
        </div>

        <div class="text-right">
            <button onclick="history.back()" class="inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">戻る</button>
            <button type="submit" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">登録</button>
        </div>
    </form>
</div>

</body>
</html>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0'); // 月は0から始まるので+1
    const dd = String(today.getDate()).padStart(2, '0');
    const formattedDate = `${yyyy}-${mm}-${dd}`;
    document.getElementById("received_date").value = formattedDate;
  });
</script>

<?php
session_start();

// 로그인 여부 확인
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

include 'includes/database-connection.php';

// GET 파라미터
$item_id = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

if (!$item_id || !$category_id) {
    header("Location: admincategory_items.php?category_id=$category_id");
    exit;
}

// 아이템 정보 가져오기
$stmt = $pdo->prepare("SELECT * FROM treshitems WHERE item_id=? AND is_deleted=0");
$stmt->execute([$item_id]);
$item = $stmt->fetch();
if (!$item) {
    header("Location: admincategory_items.php?category_id=$category_id");
    exit;
}

// POST 처리: 아이템 수정
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['item_name'];
    $type = $_POST['classification'];
    $disposal_method = $_POST['disposal_method'] ?? null;
    $tip = $_POST['tip'] ?? null;
    $image_url = $item['image_url'];

    // 이미지 업로드
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === 0) {
        $ext = pathinfo($_FILES['item_image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('item_', true) . "." . $ext;
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/images/";
        move_uploaded_file($_FILES['item_image']['tmp_name'], $target_dir . $filename);
        $image_url = "/images/" . $filename;
    }

    $stmt = $pdo->prepare("UPDATE treshitems SET name=?, type=?, disposal_method=?, tip=?, image_url=? WHERE item_id=?");
    $stmt->execute([$name, $type, $disposal_method, $tip, $image_url, $item_id]);

    header("Location: admincategory_items.php?category_id=$category_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>아이템 수정</title>
    <link rel="stylesheet" href="../style/admin.css">
    <link rel="stylesheet" href="../style/admincategory_create.css">
</head>
<body>
<header>
    <div class="logo">
        <a href="admin.php"><img src="/images/admin_logo.png" alt="로고"></a>
    </div>
    <nav class="tab">
        <a href="admin_tip.php">분리배출 꿀팁</a>
        <a href="admin_news.php">환경뉴스</a>
        <a href="admin_category.php">카테고리</a>
        <a href="logout.php">로그아웃</a>
    </nav>
</header>
<hr/>
<main>
    <h2>아이템 수정</h2>
    <div class="section">
        <form action="" method="POST" enctype="multipart/form-data">
            <label>아이템 이름</label>
            <input type="text" name="item_name" value="<?= htmlspecialchars($item['name']) ?>" required>

            <label>아이템 이미지</label>
            <input type="file" name="item_image" accept="image/*">
            <?php if($item['image_url']): ?>
                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="아이템 이미지" style="height:60px;margin-top:5px;">
            <?php endif; ?>

            <label>분류</label>
            <select name="classification" required>
                <option value="음식물" <?= $item['type']=='음식물' ? 'selected' : '' ?>>음식물쓰레기</option>
                <option value="일반" <?= $item['type']=='일반' ? 'selected' : '' ?>>일반쓰레기</option>
                <option value="재활용" <?= $item['type']=='재활용' ? 'selected' : '' ?>>재활용쓰레기</option>
            </select>

            <label>버리는 방법</label>
            <textarea name="disposal_method"><?= htmlspecialchars($item['disposal_method']) ?></textarea>

            <label>알면 더 도움되는 팁</label>
            <textarea name="tip"><?= htmlspecialchars($item['tip']) ?></textarea>

            <button type="submit">수정</button>
        </form>
    </div>
</main>
<footer>
    <p>© 2025 TC. All rights reserved.</p>
</footer>
</body>
</html>

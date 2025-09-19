<?php
session_start();

// 로그인 여부 확인
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

include 'includes/database-connection.php';

// GET 파라미터로 카테고리 선택
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

if (!$category_id) {
    header("Location: admin_category.php");
    exit;
}

// 카테고리 정보 가져오기
$stmt = $pdo->prepare("SELECT * FROM categories WHERE category_id=? AND is_deleted=0");
$stmt->execute([$category_id]);
$category = $stmt->fetch();
if (!$category) {
    header("Location: admin_category.php");
    exit;
}

// 해당 카테고리 아이템 목록
$stmt = $pdo->prepare("SELECT * FROM treshitems WHERE category_id=? AND is_deleted=0 ORDER BY item_id DESC");
$stmt->execute([$category_id]);
$item_list = $stmt->fetchAll();

// POST 처리: 카테고리 수정
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
    $name = $_POST['category_name'];
    $image_url = $category['image_url'];

    // 이미지 업로드
    if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === 0) {
        $ext = pathinfo($_FILES['category_image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('cat_', true) . "." . $ext;
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/images/";
        move_uploaded_file($_FILES['category_image']['tmp_name'], $target_dir . $filename);
        $image_url = "/images/" . $filename;
    }

    $stmt = $pdo->prepare("UPDATE categories SET name=?, image_url=? WHERE category_id=?");
    $stmt->execute([$name, $image_url, $category_id]);

    header("Location: admincategory_items.php?category_id=$category_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>TC 관리자 - <?= htmlspecialchars($category['name']) ?> 아이템 관리</title>
    <link rel="stylesheet" href="../style/admin.css">
    <link rel="stylesheet" href="../style/admincategory_create.css">
    <link rel="stylesheet" href="../style/admincategory_items.css">
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
    <h2>카테고리 수정</h2>
    <div class="section">
        <form action="" method="POST" enctype="multipart/form-data">
            <label>카테고리 이름</label>
            <input type="text" name="category_name" value="<?= htmlspecialchars($category['name']) ?>" required>
            
            <label>카테고리 이미지</label>
            <input type="file" name="category_image" accept="image/*">
            <?php if($category['image_url']): ?>
                <img src="<?= htmlspecialchars($category['image_url']) ?>" alt="이미지" style="height:80px;margin-top:5px;">
            <?php endif; ?>
            
            <button type="submit">수정</button>
        </form>
    </div>

    <h2>아이템 목록</h2>
    <div class="item-list-section">
        <table class="item-list-table">
            <thead>
                <tr>
                    <th>이름</th>
                    <th>분류</th>
                    <th>이미지</th>
                    <th>비고</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($item_list as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= htmlspecialchars($item['type']) ?></td>
                        <td>
                            <?php if($item['image_url']): ?>
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="아이템">
                            <?php else: ?>-
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="admincategory_item_edit.php?item_id=<?= $item['item_id'] ?>&category_id=<?= $category_id ?>"><button>수정</button></a>
                            <a href="admincategory_delete_item.php?item_id=<?= $item['item_id'] ?>&category_id=<?= $category_id ?>"
                            onclick="return confirm('정말 삭제하시겠습니까?');"><button>삭제</button></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($item_list)): ?>
                    <tr><td colspan="4">등록된 아이템이 없습니다.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<footer>
    <p>© 2025 TC. All rights reserved.</p>
</footer>
</body>
</html>

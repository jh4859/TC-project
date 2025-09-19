<?php
session_start();

// 로그인 여부 확인
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

include 'includes/database-connection.php';

// 카테고리 불러오기
$stmt = $pdo->prepare("SELECT category_id, name, image_url FROM categories WHERE is_deleted = 0 ORDER BY category_id ASC");
$stmt->execute();
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TC 관리자페이지 - 카테고리</title>
    <link rel="stylesheet" href="../style/admin.css">
    <link rel="stylesheet" href="../style/admin_category.css">
</head>
<body>
<header>
    <!-- 로고 -->
    <div class="logo">
        <a href="admin.php"><img src="../images/admin_logo.png" alt="로고"></a>
    </div>

    <!-- 탭 메뉴 -->
    <nav class="tab">
        <a href="admin_tip.php">분리배출 꿀팁</a>
        <a href="admin_news.php">환경뉴스</a>
        <a href="admin_category.php">카테고리</a>
        <a href="logout.php">로그아웃</a>
    </nav>
</header>

<hr />
<main>
    <div class="category_table">
        <h2 class="table-title">카테고리</h2>
        <table>
            <thead>
                <tr>
                    <th>번호</th>
                    <th>카테고리명</th>
                    <th>이미지</th>
                    <th>비고</th>
                </tr>
            </thead>
            <tbody>
                <?php if($categories): ?>
                    <?php foreach($categories as $index => $cat): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($cat['name']) ?></td>
                            <td>
                                <?php if($cat['image_url']): ?>
                                    <img src="<?= htmlspecialchars($cat['image_url']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>">
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="admincategory_items.php?category_id=<?= $cat['category_id'] ?>"><button class="edit">수정</button></a>
                                <a href="admincategory_delete.php?category_id=<?= $cat['category_id'] ?>" 
                                onclick="return confirm('정말 이 카테고리와 안에 있는 모든 아이템을 삭제하시겠습니까?');">
                                <button>삭제</button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center;">등록된 카테고리가 없습니다.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="btn_box">
            <a href="admincategory_create.php"><button class="register">등록</button></a>
        </div>
    </div>
</main>

<footer>
    <p>© 2025 TC. All rights reserved.</p>
</footer>
</body>
</html>

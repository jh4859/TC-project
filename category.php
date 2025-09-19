<?php
include 'includes/database-connection.php';

// 카테고리 목록 조회
$stmt = $pdo->query("SELECT category_id, name, image_url FROM categories ORDER BY category_id ASC");
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TC - 카테고리</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/category.css">
</head>
<body>
    <header>
        <div class="top">
            <div class="logo">
                <a href="index.php"><img src="../images/logo.png" alt="logo"></a>
            </div>
            <div class="greeting">
                <h3>오늘도 분리수고하셨습니다</h3>
            </div>
        </div>

        <!-- 중단 영역 (검색창) -->
        <div class="middle">
            <form class="search" action="search.php" method="get">
                <input type="text" name="q" placeholder="바나나껍질, 프링글스통" />
                <button type="submit">🔍</button>
            </form>
            <p class="search-desc">어떻게 버리지?</p>
        </div>

        <div class="bottom">
            <div class="tab"><a href="tip.php">분리배출 꿀팁</a></div>
            <div class="tab"><a href="news.php">환경뉴스</a></div>
            <div class="tab"><a href="category.php">카테고리</a></div>
        </div>
    </header>

    <main>
        <hr/>
        <div class="category-tab">
            <p class="title-tab">카테고리별 쓰레기</p>
            <div class="cards-container">
                <?php foreach($categories as $cat): ?>
                    <a href="categoryitem.php?category_id=<?= $cat['category_id'] ?>">
                        <div class="category-item-tab">
                            <img src="<?= htmlspecialchars($cat['image_url']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>">
                            <p><?= htmlspecialchars($cat['name']) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>© 2025 TC. All rights reserved.</p>
    </footer>
</body>
</html>

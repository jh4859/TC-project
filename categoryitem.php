<?php
include 'includes/database-connection.php';

// GET 파라미터로 전달된 category_id 확인
$category_id = $_GET['category_id'] ?? 0;
$category_id = (int)$category_id; // 안전하게 정수 변환

if($category_id === 0){
    die('유효하지 않은 카테고리입니다.');
}

// 카테고리 정보 가져오기
$stmt = $pdo->prepare("SELECT name FROM categories WHERE category_id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch();

if(!$category){
    die('존재하지 않는 카테고리입니다.');
}

// 해당 카테고리의 쓰레기 아이템 조회
$stmt = $pdo->prepare("SELECT item_id, name, image_url FROM treshitems WHERE category_id = ? ORDER BY item_id ASC");
$stmt->execute([$category_id]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TC - <?= htmlspecialchars($category['name']) ?></title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/categoryitem.css">
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
        <div class="category">
            <h2><?= htmlspecialchars($category['name']) ?></h2>
            <div class="result">
                <?php if($items): ?>
                    <?php foreach($items as $item): ?>
                        <a href="itemview.php?item_id=<?= $item['item_id'] ?>">
                            <div class="category_item">
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                <p><?= htmlspecialchars($item['name']) ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>등록된 아이템이 없습니다.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>© 2025 TC. All rights reserved.</p>
    </footer>
</body>
</html>

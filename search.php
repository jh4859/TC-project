<?php
include 'includes/database-connection.php';

// 검색어 받기 (GET 파라미터)
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

// 검색 실행
$results = [];
if ($keyword !== '') {
    $stmt = $pdo->prepare("
        SELECT item_id, name, image_url 
        FROM treshitems 
        WHERE name LIKE :keyword AND is_deleted = 0
        ORDER BY name ASC
    ");
    $stmt->execute([':keyword' => "%$keyword%"]);
    $results = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TC - 검색결과</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/search.css">
</head>
<body>
<header>
    <!-- 상단 영역 -->
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
            <input type="text" name="q" placeholder="바나나껍질, 프링글스통" value="<?= htmlspecialchars($keyword) ?>" />
            <button type="submit">🔍</button>
        </form>
        <p class="search-desc">어떻게 버리지?</p>
    </div>

    <!-- 하단 탭 -->
    <div class="bottom">
        <div class="tab"><a href="tip.php">분리배출 꿀팁</a></div>
        <div class="tab"><a href="news.php">환경뉴스</a></div>
        <div class="tab"><a href="category.php">카테고리</a></div>
    </div>
</header>

<main>
    <hr/>
    <div class="content">
        <?php if ($keyword): ?>
            <p>"<?= htmlspecialchars($keyword) ?>"에 대한 검색결과</p>
            <div class="result">
                <?php if ($results): ?>
                    <?php foreach ($results as $row): ?>
                        <a href="itemview.php?item_id=<?= $row['item_id'] ?>" class="item">
                            <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                            <p><?= htmlspecialchars($row['name']) ?></p>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align:center;">검색 결과가 없습니다.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p style="text-align:center;">검색어를 입력해주세요.</p>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>© 2025 TC. All rights reserved.</p>
</footer>
</body>
</html>

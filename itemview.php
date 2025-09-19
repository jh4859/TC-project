<?php
include 'includes/database-connection.php';

// GET 파라미터로 아이템 ID 받기
$item_id = $_GET['item_id'] ?? 0;
$item_id = (int)$item_id;

if ($item_id === 0) {
    die("유효하지 않은 아이템입니다.");
}

// 아이템 정보 불러오기
$stmt = $pdo->prepare("SELECT name, type, image_url, category_id, disposal_method, tip FROM treshitems WHERE item_id = ?");
$stmt->execute([$item_id]);
$item = $stmt->fetch();

if (!$item) {
    die("존재하지 않는 아이템입니다.");
}

// 카테고리 이름 가져오기
$stmt = $pdo->prepare("SELECT name FROM categories WHERE category_id = ?");
$stmt->execute([$item['category_id']]);
$category = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TC - <?= htmlspecialchars($item['name']) ?></title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/itemview.css">
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
                <input type="text" name="q" placeholder="바나나껍질, 프링글스통" />
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
        <div class="view_content">
            <h2><?= htmlspecialchars($item['name']) ?></h2>
            <div class="result">
                <div class="img">
                    <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                </div>
                <div class="type">
                    <p>카테고리: <?= htmlspecialchars($category['name']) ?></p>
                    <p>분류종류: <?= htmlspecialchars($item['type']) ?>쓰레기</p>
                </div>
                <div class="waste-disposal">
                    <h3>버리는 방법</h3>
                    <p><?= nl2br(htmlspecialchars($item['disposal_method'])) ?></p>
                </div>
                <div class="tip">
                    <h3>알면 더 도움되는 팁</h3>
                    <ul>
                        <?php 
                        // DB에서 tip을 여러 줄로 저장했다고 가정 -> 줄바꿈 단위로 분리
                        $tips = explode("\n", $item['tip']);
                        foreach ($tips as $tip): 
                            if (trim($tip) !== ''): ?>
                                <li><?= htmlspecialchars(trim($tip)) ?></li>
                            <?php endif;
                        endforeach; 
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>© 2025 TC. All rights reserved.</p>
    </footer>
</body>
</html>

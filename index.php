<?php 
include 'includes/database-connection.php';

// 실생활 헷갈리는 쓰레기 아이템
$items = [
    ['name' => '바나나껍질', 'image' => '../images/banana.png', 'link' => 'itemview.php?item_id=91'],
    ['name' => '견과류 껍질', 'image' => '../images/nut.png', 'link' => 'itemview.php?item_id=134'],
    ['name' => '코팅된 종이', 'image' => '../images/coated_paper.png', 'link' => 'itemview.php?item_id=131'],
    ['name' => '작은 플라스틱', 'image' => '../images/small_plastic.png', 'link' => 'itemview.php?item_id=138'],
    ['name' => '샴프통', 'image' => '../images/shampoo.jpg', 'link' => 'itemview.php?item_id=61']
];

// 카테고리 목록 조회
$stmt = $pdo->query("SELECT category_id, name, image_url FROM categories ORDER BY category_id ASC");
$categories = $stmt->fetchAll();

// 전체 게시물 수 가져오는 함수
function getTotalPosts($pdo, $board_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Post WHERE board_id = ? AND is_deleted = 0");
    $stmt->execute([$board_id]);
    return $stmt->fetchColumn();
}

// 게시판별 최근 글 가져오기
function getRecentPosts($pdo, $board_id, $limit = 5){
    $stmt = $pdo->prepare("SELECT post_id, title, created_at 
                           FROM Post 
                           WHERE board_id = ? AND is_deleted = 0 
                           ORDER BY created_at DESC 
                           LIMIT ?");
    $stmt->execute([$board_id, $limit]);
    return $stmt->fetchAll();
}

// 분리배출 꿀팁
$totalTipPosts = getTotalPosts($pdo, 1);
$tipPosts = getRecentPosts($pdo, 1);

// 환경뉴스
$totalNewsPosts = getTotalPosts($pdo, 2);
$newsPosts = getRecentPosts($pdo, 2);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TC</title>
    <link rel="stylesheet" href="../style/style.css">
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
            <p class="title">카테고리별 쓰레기</p>
            <?php foreach($categories as $cat): ?>
                <a href="categoryitem.php?category_id=<?= $cat['category_id'] ?>">
                    <div class="category-item">
                        <img src="<?= htmlspecialchars($cat['image_url']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>">
                        <p><?= htmlspecialchars($cat['name']) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <hr/>
        <div class="trashitem">
            <p class="title">실생활에서 헷갈리는 쓰레기</p>
            <div class="items">
                <?php foreach($items as $item): ?>
                    <a href="<?= $item['link'] ?>" class="item">
                        <img src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>" height="80">
                        <p><?= $item['name'] ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <hr/>
        <div class="board">
            <!-- 분리배출 꿀팁 -->
            <div class="tip">
                <div class="header">
                    <span>분리배출 꿀팁</span>
                    <a href="tip.php">더보기</a>
                </div>
                <div class="content">
                    <table>
                        <thead>
                            <tr>
                                <th>번호</th>
                                <th>제목</th>
                                <th>작성일</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($tipPosts as $index => $post): ?>
                                <tr>
                                    <td><?= $totalTipPosts - $index ?></td>
                                    <td><a href="tableview.php?post_id=<?= $post['post_id'] ?>"><?= htmlspecialchars($post['title']) ?></a></td>
                                    <td><?= substr($post['created_at'], 0, 10) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 환경뉴스 -->
            <div class="news">
                <div class="header">
                    <span>환경뉴스</span>
                    <a href="news.php">더보기</a>
                </div>
                <div class="content">
                    <table>
                        <thead>
                            <tr>
                                <th>번호</th>
                                <th>제목</th>
                                <th>작성일</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($newsPosts as $index => $post): ?>
                                <tr>
                                    <td><?= $totalNewsPosts - $index ?></td>
                                    <td><a href="tableview.php?post_id=<?= $post['post_id'] ?>"><?= htmlspecialchars($post['title']) ?></a></td>
                                    <td><?= substr($post['created_at'], 0, 10) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>© 2025 TC. All rights reserved.</p>
    </footer>
</body>
</html>

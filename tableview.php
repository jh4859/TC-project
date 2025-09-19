<?php
include 'includes/database-connection.php';

// GET으로 post_id받기
$post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

if ($post_id > 0) {
    $stmt = $pdo->prepare("SELECT board_id, title, content, author, created_at 
                            FROM Post 
                            WHERE post_id = ? AND is_deleted = 0");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
}

// board_id를 DB에서 가져온 값으로 설정
$board_id = $post['board_id'] ?? 0;

// post가 없으면 안내 메시지
if (!$post) {
    $post = [
        'title' => '존재하지 않는 게시글입니다.',
        'content' => '',
        'author' => '-',
        'created_at' => '-'
    ];
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TC - <?= htmlspecialchars($post['title']) ?></title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/tableview.css">
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
    <hr />

    <main>
        <h2 class="post_title"><?= htmlspecialchars($post['title']) ?></h2>

        <div class="table_title">
            <div class="type">
                분류: <?= ($board_id == 1) ? '질문' : (($board_id == 2) ? '뉴스' : '기타') ?>
            </div>
            <div class="author">작성자: <?= htmlspecialchars($post['author']) ?></div>
            <div class="date">작성일: <?= $post['created_at'] !== '-' ? date('Y.m.d', strtotime($post['created_at'])) : '-' ?></div>
        </div>

        <div class="table_content">
            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        </div>

        <div class="list">
            <?php if ($board_id == 1): ?>
                <a href="tip.php"><button>목록보기</button></a>
            <?php elseif ($board_id == 2): ?>
                <a href="news.php"><button>목록보기</button></a>
            <?php else: ?>
                <a href="index.php"><button>목록보기</button></a>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>© 2025 TC. All rights reserved.</p>
    </footer>
</body>
</html>

<?php
session_start();

// 로그인 여부 확인
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

include 'includes/database-connection.php';

// GET으로 post_id 받기
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
    <link rel="stylesheet" href="../style/admin.css">
    <link rel="stylesheet" href="../style/tableview.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="admin.php"><img src="../images/admin_logo.png" alt="로고"></a>
        </div>
        <nav class="tab">
            <a href="admin_tip.php">분리배출 꿀팁</a>
            <a href="admin_news.php">환경뉴스</a>
            <a href="admin_category.php">카테고리</a>
            <a href="logout.php">로그아웃</a>
        </nav>
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
                <a href="admin_tip.php"><button>목록보기</button></a>
            <?php elseif ($board_id == 2): ?>
                <a href="admin_news.php"><button>목록보기</button></a>
            <?php else: ?>
                <a href="admin.php"><button>목록보기</button></a>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>© 2025 TC. All rights reserved.</p>
    </footer>
</body>
</html>

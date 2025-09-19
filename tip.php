<?php
include 'includes/database-connection.php';

// 페이지 번호
$perPage = 10; // 한 페이지에 보여줄 글 개수
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// 전체 글 수 확인
$stmt = $pdo->prepare("SELECT COUNT(*) FROM Post WHERE board_id=1 AND is_deleted=0");
$stmt->execute();
$total_posts = $stmt->fetchColumn();
$total_pages = ceil($total_posts / $perPage);

// 게시글 불러오기 (분리배출 꿀팁 board_id = 1, 최신순)
$start = ($page - 1) * $perPage;
$stmt = $pdo->prepare("SELECT post_id, title, author, created_at 
                       FROM Post 
                       WHERE board_id = 1 AND is_deleted = 0 
                       ORDER BY created_at DESC
                       LIMIT :start, :perPage");
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();
$posts_display = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TC - 분리배출 꿀팁</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/tip.css">
    <link rel="stylesheet" href="../style/admin_pagenation.css">
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

<hr />
<main>
    <h2 class="table-title">분리배출 꿀팁</h2>
    <table class="tip_table">
        <thead>
        <tr>
            <th class="num-col">번호</th>
            <th class="title-col">제목</th>
            <th class="writer-col">작성자</th>
            <th class="date-col">작성일</th>
        </tr>
        </thead>
        <tbody>
        <?php if($posts_display): ?>
            <?php foreach($posts_display as $index => $post): ?>
                <tr>
                    <!-- 최신 글이 1번부터 나오도록 -->
                    <td><?= $total_posts - (($page - 1) * $perPage + $index) ?></td>
                    <td>
                        <a href="tableview.php?post_id=<?= $post['post_id'] ?>">
                            <?= htmlspecialchars(mb_strimwidth($post['title'], 0, 60, '...')) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($post['author']) ?></td>
                    <td><?= date('Y.m.d', strtotime($post['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align:center;">등록된 꿀팁이 없습니다.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- 페이지네이션 -->
    <div class="pagination">
        <?php if($page > 1): ?>
            <a href="?page=<?= $page-1 ?>">&laquo; 이전</a>
        <?php endif; ?>

        <?php for($i=1; $i<=$total_pages; $i++): ?>
            <a href="?page=<?= $i ?>" <?= $i==$page ? 'class="active"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>

        <?php if($page < $total_pages): ?>
            <a href="?page=<?= $page+1 ?>">다음 &raquo;</a>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>© 2025 TC. All rights reserved.</p>
</footer>
</body>
</html>

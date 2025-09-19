<?php
session_start();

// 로그인 여부 확인
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

include 'includes/database-connection.php';

// 페이지네이션 설정
$perPage = 10; // 한 페이지에 보여줄 글 개수
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// 전체 게시글 수 확인
$stmt = $pdo->prepare("SELECT COUNT(*) FROM Post WHERE board_id=1 AND is_deleted=0");
$stmt->execute();
$total_posts = $stmt->fetchColumn();
$total_pages = ceil($total_posts / $perPage);

// 현재 페이지 게시글 불러오기 (최신글이 먼저 나오도록 DESC 정렬)
$start = ($page - 1) * $perPage;
$stmt = $pdo->prepare("SELECT post_id, title, author, created_at 
                       FROM Post 
                       WHERE board_id = 1 AND is_deleted = 0 
                       ORDER BY created_at DESC
                       LIMIT :start, :perPage");
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TC 관리자페이지 - 분리배출 꿀팁</title>
    <link rel="stylesheet" href="../style/admin.css">
    <link rel="stylesheet" href="../style/admin_tip.css">
    <link rel="stylesheet" href="../style/admin_pagenation.css">
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
        <div class="admin_tip_table">
            <h2 class="table-title">분리배출 꿀팁</h2>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th class="id-col">번호</th>
                        <th class="title-col">제목</th>
                        <th class="writer-col">작성자</th>
                        <th class="date-col">작성일</th>
                        <th class="action-col">비고</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($posts): ?>
                        <?php foreach($posts as $index => $post): ?>
                            <tr>
                                <!-- 최신 글이 1번 → 오래된 글은 페이지 넘어가면서 뒤로 밀림 -->
                                <td><?= $total_posts - (($page - 1) * $perPage + $index) ?></td>
                                <td>
                                    <a href="admin_tableview.php?post_id=<?= $post['post_id'] ?>">
                                        <?= htmlspecialchars(mb_strimwidth($post['title'], 0, 50, '...')) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($post['author']) ?></td>
                                <td><?= date('Y.m.d', strtotime($post['created_at'])) ?></td>
                                <td>
                                    <a href="admintable_create.php?post_id=<?= $post['post_id'] ?>"><button class="btn-edit">수정</button></a>
                                    <a href="admintable_delete.php?post_id=<?= $post['post_id'] ?>" onclick="return confirm('정말 삭제하시겠습니까?');"><button class="btn-delete">삭제</button></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center;">등록된 게시글이 없습니다.</td>
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

            <div class="btn_box">
                <a href="admintable_create.php"><button class="register">등록</button></a>
            </div>
        </div>
    </main>
    <footer>
        <p>© 2025 TC. All rights reserved.</p>
    </footer>
</body>
</html>

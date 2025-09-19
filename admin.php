<?php
session_start();

// 로그인 여부 확인
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

include 'includes/database-connection.php';

/* ===========================
   분리배출 꿀팁 (board_id = 1)
   =========================== */
// 전체 게시물 수
$totalTipStmt = $pdo->prepare("SELECT COUNT(*) FROM Post WHERE board_id = 1 AND is_deleted = 0");
$totalTipStmt->execute();
$totalTipPosts = $totalTipStmt->fetchColumn();

// 최근 10개 게시물 가져오기
$tipStmt = $pdo->prepare("SELECT post_id, title, author, created_at 
                          FROM Post 
                          WHERE board_id = 1 AND is_deleted = 0 
                          ORDER BY created_at DESC 
                          LIMIT 10");
$tipStmt->execute();
$tipPosts = $tipStmt->fetchAll();

/* ===========================
   환경뉴스 (board_id = 2)
   =========================== */
// 전체 게시물 수
$totalNewsStmt = $pdo->prepare("SELECT COUNT(*) FROM Post WHERE board_id = 2 AND is_deleted = 0");
$totalNewsStmt->execute();
$totalNewsPosts = $totalNewsStmt->fetchColumn();

// 최근 10개 게시물 가져오기
$newsStmt = $pdo->prepare("SELECT post_id, title, author, created_at 
                           FROM Post 
                           WHERE board_id = 2 AND is_deleted = 0 
                           ORDER BY created_at DESC 
                           LIMIT 10");
$newsStmt->execute();
$newsPosts = $newsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TC 관리자페이지</title>
  <link rel="stylesheet" href="../style/admin.css">
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
    <div class="tables">
      <!-- 분리배출 꿀팁 -->
      <div class="tip">
        <h2 class="table-title">분리배출 꿀팁</h2>
        <table class="custom-table">
          <thead>
            <tr>
              <th class="num-col">번호</th>
              <th class="title-col">제목</th>
              <th class="writer-col">작성자</th>
              <th class="date-col">날짜</th>
            </tr>
          </thead>
          <tbody>
            <?php if($tipPosts): ?>
              <?php foreach($tipPosts as $index => $post): ?>
                <tr>
                  <!-- 전체 게시물 기준 순번 -->
                  <td><?= $totalTipPosts - $index ?></td>
                  <td><a href="admin_tableview.php?post_id=<?= $post['post_id'] ?>"><?= htmlspecialchars($post['title']) ?></a></td>
                  <td><?= htmlspecialchars($post['author']) ?></td>
                  <td><?= date('Y.m.d', strtotime($post['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" style="text-align:center;">등록된 게시글이 없습니다.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- 환경뉴스 -->
      <div class="news">
        <h2 class="table-title">환경뉴스</h2>
        <table class="custom-table">
          <thead>
            <tr>
              <th class="num-col">번호</th>
              <th class="title-col">제목</th>
              <th class="writer-col">작성자</th>
              <th class="date-col">날짜</th>
            </tr>
          </thead>
          <tbody>
            <?php if($newsPosts): ?>
              <?php foreach($newsPosts as $index => $post): ?>
                <tr>
                  <!-- 전체 게시물 기준 순번 -->
                  <td><?= $totalNewsPosts - $index ?></td>
                  <td><a href="admin_tableview.php?post_id=<?= $post['post_id'] ?>"><?= htmlspecialchars($post['title']) ?></a></td>
                  <td><?= htmlspecialchars($post['author']) ?></td>
                  <td><?= date('Y.m.d', strtotime($post['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" style="text-align:center;">등록된 게시글이 없습니다.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <footer>
      <p>© 2025 TC. All rights reserved.</p>
  </footer>
</body>
</html>

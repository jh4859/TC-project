<?php
session_start();

// 로그인 여부 확인
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

include 'includes/database-connection.php';

// 수정 모드일 경우 기존 게시글 불러오기
$post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

$title = '';
$content = '';
$author = '';
$boardType = '';
$actionMessage = ''; // 등록/수정 알림용

if ($post_id > 0) {
    $stmt = $pdo->prepare("SELECT board_id, title, content, author FROM Post WHERE post_id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if ($post) {
        $title = $post['title'];
        $content = $post['content'];
        $author = $post['author'];
        $boardType = ($post['board_id'] == 1) ? 'tip' : 'news';
    }
}

// POST 전송 처리 (신규 등록 + 수정 통합)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $boardType = $_POST['boardType'] ?? '';
    $title     = $_POST['title'] ?? '';
    $content   = $_POST['content'] ?? '';
    $author    = ($boardType === 'tip') ? '관리자' : ($_POST['author'] ?? '');

    $board_id = ($boardType === 'tip') ? 1 : (($boardType === 'news') ? 2 : 0);

    if ($post_id > 0) {
        // 수정 모드
        $stmt = $pdo->prepare("UPDATE Post SET board_id=?, title=?, content=?, author=? WHERE post_id=?");
        $stmt->execute([$board_id, $title, $content, $author, $post_id]);
        $actionMessage = '게시글이 성공적으로 수정되었습니다.';
    } else {
        // 신규 등록 모드
        if($board_id && $title && $content && $author){
            $stmt = $pdo->prepare("INSERT INTO Post (board_id, title, content, author, is_deleted) VALUES (?, ?, ?, ?, 0)");
            $stmt->execute([$board_id, $title, $content, $author]);
            $actionMessage = '게시글이 성공적으로 등록되었습니다.';
        } else {
            $actionMessage = '모든 항목을 정확히 입력해주세요.';
        }
    }

    // 알림 후 해당 게시판 목록으로 이동
    $redirectUrl = 'admin.php';
    if ($boardType === 'tip') $redirectUrl = 'admin_tip.php';
    elseif ($boardType === 'news') $redirectUrl = 'admin_news.php';

    echo "<script>
        alert('".addslashes($actionMessage)."');
        window.location.href = '$redirectUrl';
    </script>";
    exit;
}

// 취소 시 돌아갈 URL 결정
$cancelUrl = ($boardType === 'tip') ? 'admin_tip.php' : (($boardType === 'news') ? 'admin_news.php' : 'admin.php');
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8" />
  <title>TC 관리자페이지</title>
  <link rel="stylesheet" href="../style/admin.css">
  <link rel="stylesheet" href="../style/admintable_create.css">
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
    <div class="table_create">
      <h2><?= $post_id > 0 ? '게시글 수정' : '게시글 등록' ?></h2>

      <form method="post">
        <table>
          <tr>
            <th>게시판</th>
            <td>
              <select name="boardType" required>
                <option value="">게시판을 선택하세요</option>
                <option value="tip" <?= $boardType === 'tip' ? 'selected' : '' ?>>분리배출 꿀팁</option>
                <option value="news" <?= $boardType === 'news' ? 'selected' : '' ?>>환경뉴스</option>
              </select>
            </td>
          </tr>
          <tr>
            <th>제목</th>
            <td><input type="text" name="title" placeholder="제목을 입력하세요" required value="<?= htmlspecialchars($title) ?>"></td>
          </tr>
          <tr>
            <th>내용</th>
            <td><textarea name="content" placeholder="내용을 입력하세요" required><?= htmlspecialchars($content) ?></textarea></td>
          </tr>
          <tr>
            <th>작성자</th>
            <td>
              <input type="text" id="author" name="author" value="<?= htmlspecialchars($author ?: '관리자') ?>" <?= $boardType === 'tip' ? 'readonly' : '' ?>>
            </td>
          </tr>
        </table>
        <div class="btn_box">
          <button type="submit" class="register"><?= $post_id > 0 ? '수정' : '등록' ?></button>
          <button type="button" class="cancel" id="cancelBtn">취소</button>
        </div>
      </form>
    </div>
  </main>

  <script>
    const boardSelect = document.querySelector('select[name="boardType"]');
    const authorInput = document.getElementById('author');
    const cancelBtn = document.getElementById('cancelBtn');

    // 초기 설정
    if(boardSelect.value === 'tip') {
        authorInput.value = '관리자';
        authorInput.readOnly = true;
    } else if(boardSelect.value === 'news') {
        authorInput.readOnly = false;
    }

    // 선택 변경 시 처리
    boardSelect.addEventListener('change', function() {
        if(this.value === 'tip') {
            authorInput.value = '관리자';
            authorInput.readOnly = true;
        } else if(this.value === 'news') {
            authorInput.value = '';
            authorInput.readOnly = false;
        } else {
            authorInput.value = '';
            authorInput.readOnly = true;
        }
    });

    // 취소 버튼 클릭 시 선택된 boardType에 따라 이동
    cancelBtn.addEventListener('click', function() {
        let boardType = boardSelect.value;
        let url = 'admin.php'; // 기본
        if (boardType === 'tip') url = 'admin_tip.php';
        else if (boardType === 'news') url = 'admin_news.php';
        location.href = url;
    });
  </script>

  <footer>
      <p>© 2025 TC. All rights reserved.</p>
  </footer>
</body>
</html>

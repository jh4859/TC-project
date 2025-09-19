<?php
session_start();

// 로그인 여부 확인
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

include 'includes/database-connection.php';

// 카테고리 목록 불러오기
$stmt = $pdo->prepare("SELECT category_id, name FROM categories WHERE is_deleted = 0 ORDER BY category_id ASC");
$stmt->execute();
$categories = $stmt->fetchAll();

// POST 처리
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    // ===== 카테고리 추가 =====
    if(isset($_POST['category_name']) && !isset($_POST['item_name'])){
        $name = $_POST['category_name'];

        // 이미지 처리 (고유 이름 생성)
        $image_url = null;
        if(isset($_FILES['category_image']) && $_FILES['category_image']['error'] === 0){
            $ext = pathinfo($_FILES['category_image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('cat_', true) . "." . $ext;
            $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/images/";  // 서버 루트 기준
            $target_file = $target_dir . $filename;
            move_uploaded_file($_FILES['category_image']['tmp_name'], $target_file);
            $image_url = "/images/" . $filename; // 절대 경로로 저장
        }

        // DB insert
        $stmt = $pdo->prepare("INSERT INTO categories (name, image_url) VALUES (?, ?)");
        $stmt->execute([$name, $image_url]);

        // 저장 후 admin_category.php로 이동
        header("Location: admin_category.php");
        exit;
    }

    // ===== 아이템 추가 =====
    if(isset($_POST['item_name'])){
        $category_id = $_POST['category_id'];
        $name = $_POST['item_name'];
        $type = $_POST['classification'];
        $disposal_method = $_POST['disposal_method'] ?? null;
        $tip = $_POST['tip'] ?? null;

        // 이미지 처리 (고유 이름 생성)
        $image_url = null;
        if(isset($_FILES['item_image']) && $_FILES['item_image']['error'] === 0){
            $ext = pathinfo($_FILES['item_image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('item_', true) . "." . $ext;
            $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/images/";
            $target_file = $target_dir . $filename;
            move_uploaded_file($_FILES['item_image']['tmp_name'], $target_file);
            $image_url = "/images/" . $filename; // 절대 경로
        }

        // DB insert
        $stmt = $pdo->prepare("INSERT INTO treshitems (category_id, name, image_url, type, disposal_method, tip) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$category_id, $name, $image_url, $type, $disposal_method, $tip]);

        // 저장 후 admin_category.php로 이동
        header("Location: admin_category.php");
        exit;
    }
}

// 아이템 목록 불러오기 (관리용)
$stmt = $pdo->prepare("SELECT t.item_id, t.name, t.image_url, t.type, c.name AS category_name
                       FROM treshitems t
                       JOIN categories c ON t.category_id = c.category_id
                       WHERE t.is_deleted = 0
                       ORDER BY t.item_id DESC");
$stmt->execute();
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8" />
  <title>TC 관리자 페이지</title>
  <link rel="stylesheet" href="../style/admin.css">
  <link rel="stylesheet" href="../style/admincategory_create.css">
</head>
<body>
  <header>
      <div class="logo">
        <a href="admin.php"><img src="/images/admin_logo.png" alt="로고"></a>
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
    <h2>카테고리 관리</h2>
    <div class="section">
      <form action="admincategory_create.php" method="POST" enctype="multipart/form-data">
        <label>새 카테고리 이름</label>
        <input type="text" name="category_name" placeholder="예: 음식물 쓰레기" required />
        <label>카테고리 이미지</label>
        <input type="file" name="category_image" accept="image/*" />
        <button type="submit">카테고리 추가</button>
      </form>
    </div>

    <h2>아이템 등록</h2>
    <div class="section">
      <form action="admincategory_create.php" method="POST" enctype="multipart/form-data">
        <label>카테고리 선택</label>
        <select name="category_id" required>
          <option value="">-- 카테고리 선택 --</option>
          <?php foreach($categories as $cat): ?>
            <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>

        <label>아이템 이름</label>
        <input type="text" name="item_name" placeholder="예: 사과껍질" required />

        <label>아이템 이미지</label>
        <input type="file" name="item_image" accept="image/*" />

        <label>분류</label>
        <select name="classification" required>
          <option value="음식물">음식물쓰레기</option>
          <option value="일반">일반쓰레기</option>
          <option value="재활용">재활용쓰레기</option>
        </select>

        <label>버리는 방법</label>
        <textarea name="disposal_method" placeholder="예: 물기를 제거한 뒤 음식물 쓰레기 봉투에 넣어 버립니다."></textarea>

        <label>알면 더 도움되는 팁</label>
        <textarea name="tip" placeholder="예: 종량제 봉투 안에 신문지를 깔면 냄새를 줄일 수 있어요."></textarea>

        <button type="submit">아이템 추가</button>
      </form>
    </div>

  </main>
  <footer>
    <p>© 2025 TC. All rights reserved.</p>
  </footer>
</body>
</html>

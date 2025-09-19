<?php
session_start();

// 로그인 여부 확인
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

include 'includes/database-connection.php';

// GET 파라미터 확인
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

if (!$category_id) {
    header("Location: admin_category.php");
    exit;
}

try {
    // 카테고리 삭제 (ON DELETE CASCADE로 아이템도 자동 삭제)
    $stmt = $pdo->prepare("DELETE FROM categories WHERE category_id = ?");
    $stmt->execute([$category_id]);

    if ($stmt->rowCount() === 0) {
        // 삭제된 카테고리가 없음
        die("삭제할 카테고리가 없습니다.");
    }

} catch (PDOException $e) {
    die("삭제 중 오류 발생: " . $e->getMessage());
}

// 삭제 후 카테고리 목록 페이지로 이동
header("Location: admin_category.php");
exit;
?>

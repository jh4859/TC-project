<?php
session_start();

// 로그인 여부 확인
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

include 'includes/database-connection.php';

// GET 파라미터 확인
$item_id = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

if (!$item_id || !$category_id) {
    header("Location: admincategory_items.php?category_id=$category_id");
    exit;
}

// 하드 삭제: DB에서 아이템 완전히 제거
$stmt = $pdo->prepare("DELETE FROM treshitems WHERE item_id = ?");
$stmt->execute([$item_id]);

// 삭제 후 다시 아이템 목록 페이지로 이동
header("Location: admincategory_items.php?category_id=$category_id");
exit;
?>

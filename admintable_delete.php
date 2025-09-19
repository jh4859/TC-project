<?php
session_start();

// 로그인 여부 확인
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

include 'includes/database-connection.php';

$post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

// 삭제 전 board_id 확인
$board_id = 0;
if ($post_id > 0) {
    $stmt = $pdo->prepare("SELECT board_id FROM Post WHERE post_id=?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    $board_id = $post['board_id'] ?? 0;

    // 실제 삭제
    $stmt = $pdo->prepare("DELETE FROM Post WHERE post_id=?");
    $stmt->execute([$post_id]);
}

// board_id에 따라 리다이렉트
if ($board_id == 1) {
    header("Location: admin_tip.php");
} elseif ($board_id == 2) {
    header("Location: admin_news.php");
} else {
    header("Location: admin.php"); // 기본 페이지
}
exit;
?>

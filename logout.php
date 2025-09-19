<?php
session_start();
session_unset();   // 세션 변수 전체 해제
session_destroy(); // 세션 자체 삭제
header("Location: admin_login.php"); // 로그인 페이지로 이동
exit;

?>
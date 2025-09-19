<?php
session_start();
include 'includes/database-connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = '아이디와 비밀번호를 입력하세요.';
    } else {
        // DB 테이블 이름은 실제 존재하는 이름으로 변경하세요
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin) {
            $storedPassword = $admin['password'];
            // password_verify 또는 평문 비교 허용
            if (password_verify($password, $storedPassword) || $password === $storedPassword) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $admin['username'];
                header('Location: admin.php');
                exit;
            } else {
                $error = '아이디 또는 비밀번호가 잘못되었습니다.';
            }
        } else {
            $error = '아이디 또는 비밀번호가 잘못되었습니다.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>관리자 로그인</title>
    <link rel="stylesheet" href="../style/admin_login.css">
</head>
<body>
    <div class="login-box">
        <h2>관리자 로그인</h2>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="post" action="admin_login.php">
            <input type="text" name="username" placeholder="아이디" required>
            <input type="password" name="password" placeholder="비밀번호" required>
            <input type="submit" value="로그인">
        </form>
    </div>
</body>
</html>

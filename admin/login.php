<?php
require_once __DIR__ . '/../core/bootstrap.php';
if (isset($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }
$error_message = '';
$login_message = $_GET['message'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: dashboard.php');
        exit;
    } else { $error_message = '用户名或密码错误！'; }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台登录</title>
    <link rel="stylesheet" href="assets/admin-style.css">
</head>
<body>
    <div class="form-page-wrapper">
        <div class="marquee-card">
            <div class="card-content form-container">
                <h1>🔐 站点目录管理后台</h1>
                <?php if ($error_message): ?><div class="message error"><?php echo htmlspecialchars($error_message); ?></div><?php endif; ?>
                <?php if ($login_message === 'password_changed'): ?><div class="message success">密码已更新，请使用新密码登录。</div><?php endif; ?>
                <form method="POST" action="login.php">
                    <div class="form-group"><label for="username">👤用户名</label><input type="text" id="username" name="username" required></div>
                    <div class="form-group"><label for="password">🔑密码</label><input type="password" id="password" name="password" required></div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 16px;">🌐登 录</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
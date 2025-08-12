<?php
require_once __DIR__ . '/../core/bootstrap.php';
// 权限检查
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// 从会话中获取当前用户名
$current_username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>账户设置</title>
    <link rel="stylesheet" href="assets/admin-style.css">
</head>
<body>
    <div class="form-container">
        <div class="form-wrapper">
            <h1>⚙️ 账户设置</h1>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="<?php echo $_SESSION['message_type']; ?>"><?php echo $_SESSION['message']; ?></div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            <?php endif; ?>

            <form action="update_account.php" method="POST">
                <div class="form-group">
                    <label for="username">登录用户名</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($current_username); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">新密码</label>
                    <input type="password" id="password" name="password">
                    <p class="form-note">如不修改密码，请留空。</p>
                </div>
                <div class="form-group">
                    <label for="password_confirm">确认新密码</label>
                    <input type="password" id="password_confirm" name="password_confirm">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">保存更改</button>
                    <a href="dashboard.php" class="btn btn-secondary" style="float: right;">返回主面板</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
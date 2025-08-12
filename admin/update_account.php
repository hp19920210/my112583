<?php
require_once __DIR__ . '/../core/bootstrap.php';

// 权限检查
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    die('禁止访问');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('方法不被允许');
}

$user_id = $_SESSION['user_id'];
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// --- 数据验证 ---
if (empty($username)) {
    $_SESSION['message'] = "错误: 用户名不能为空！";
    $_SESSION['message_type'] = "error-message";
    header('Location: account.php');
    exit;
}
if (!empty($password) && $password !== $password_confirm) {
    $_SESSION['message'] = "错误: 两次输入的新密码不匹配！";
    $_SESSION['message_type'] = "error-message";
    header('Location: account.php');
    exit;
}

$pdo = get_pdo();

try {
    // --- 更新逻辑 ---
    if (!empty($password)) {
        // 如果密码不为空，则更新用户名和密码
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET username = :username, password_hash = :password_hash WHERE id = :id");
        $stmt->execute(['username' => $username, 'password_hash' => $password_hash, 'id' => $user_id]);
        
        // 密码已更改，为了安全，立即销毁当前会话，强制用户用新凭据重新登录
        session_unset();
        session_destroy();
        header('Location: login.php?message=password_changed'); // 跳转到登录页并附带提示
        exit;
    } else {
        // 如果密码为空，只更新用户名
        $stmt = $pdo->prepare("UPDATE users SET username = :username WHERE id = :id");
        $stmt->execute(['username' => $username, 'id' => $user_id]);
        
        // 更新会话中的用户名
        $_SESSION['username'] = $username;
        $_SESSION['message'] = "用户名已成功更新！";
        $_SESSION['message_type'] = "success-message";
    }

} catch (PDOException $e) {
    $_SESSION['message'] = "数据库操作失败: " . $e->getMessage();
    $_SESSION['message_type'] = "error-message";
}

header('Location: account.php');
exit;
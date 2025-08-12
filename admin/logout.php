<?php
// 必须先开启会话才能销毁它
session_start();

// 1. 清空所有会话变量
$_SESSION = [];

// 2. 如果使用基于cookie的会话，则删除会话cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. 最终销毁会话
session_destroy();

// 4. 重定向到登录页面
header('Location: login.php');
exit;
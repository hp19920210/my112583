<?php
require_once __DIR__ . '/../core/bootstrap.php';

// 权限检查
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    die('禁止访问');
}

// 检查ID是否存在
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: dashboard.php');
    exit;
}

// 执行删除操作
try {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("DELETE FROM sites WHERE id = :id");
    $stmt->execute(['id' => $id]);
} catch (PDOException $e) {
    // 在生产环境中，这里应该记录错误日志而不是直接显示
    die("数据库删除操作失败: " . $e->getMessage());
}

// 操作完成，跳转回主面板
header('Location: dashboard.php');
exit;
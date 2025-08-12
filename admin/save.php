<?php
require_once __DIR__ . '/../core/bootstrap.php';

// 权限检查
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    die('禁止访问');
}

// 只接受POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('方法不被允许');
}

// --- 数据准备 ---
$id = $_POST['id'] ?: null;

// 使用一个数组来收集所有待处理的数据
$data = [
    'name' => $_POST['name'] ?? '',
    'url' => $_POST['url'] ?? '',
    'description' => $_POST['description'] ?? '',
    'category' => $_POST['category'] ?? '默认分类',
    'tags' => $_POST['tags'] ?? '',
    'status' => (int)($_POST['status'] ?? 1),
    'admin_url' => $_POST['admin_url'] ?? '',
    'admin_username' => $_POST['admin_username'] ?? '',
    'notes' => $_POST['notes'] ?? '',
    'created_date' => $_POST['created_date'] ?? date('Y-m-d'),
    // 每次保存都自动更新“最后更新时间”
    'updated_date' => date('Y-m-d'),
];

// --- 密码处理 ---
// 只有当用户输入了新密码时，才进行加密和更新
if (!empty($_POST['admin_password'])) {
    $encrypted_pass = encrypt_password($_POST['admin_password']);
    if ($encrypted_pass === false) {
        die('密码加密失败！');
    }
    $data['admin_password'] = $encrypted_pass;
}

$pdo = get_pdo();

// --- 数据库操作 ---
if ($id) {
    // 更新模式 (UPDATE)
    $sql_parts = [];
    foreach ($data as $key => $value) {
        $sql_parts[] = "{$key} = :{$key}";
    }
    $sql = "UPDATE sites SET " . implode(', ', $sql_parts) . " WHERE id = :id";
    $data['id'] = $id;

    $stmt = $pdo->prepare($sql);

} else {
    // 新增模式 (INSERT)
    $columns = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));
    $sql = "INSERT INTO sites ({$columns}) VALUES ({$placeholders})";
    
    $stmt = $pdo->prepare($sql);
}

// 执行SQL
try {
    $stmt->execute($data);
} catch (PDOException $e) {
    die("数据库操作失败: " . $e->getMessage());
}

// --- 操作完成，跳转回主面板 ---
header('Location: dashboard.php');
exit;
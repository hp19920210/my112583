<?php
require_once __DIR__ . '/../core/bootstrap.php';

// 权限检查
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// --- 表单处理逻辑 ---
$page_title = '➕ 添加新站点';
$site = [
    'id' => null, 'name' => '', 'url' => '', 'description' => '', 'category' => '默认分类',
    'tags' => '', 'status' => 1, 'admin_url' => '', 'admin_username' => '',
    'admin_password' => '', 'notes' => '', 'created_date' => date('Y-m-d'), 'updated_date' => date('Y-m-d')
];
$form_action = 'save.php';

// 判断是编辑模式还是新增模式
$site_id = $_GET['id'] ?? null;
if ($site_id) {
    $page_title = '✏️ 编辑站点';
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT * FROM sites WHERE id = :id");
    $stmt->execute(['id' => $site_id]);
    $data = $stmt->fetch();

    if ($data) {
        $site = $data;
        // 如果密码已加密，则解密以在表单中显示
        if (!empty($site['admin_password'])) {
            $decrypted_pass = decrypt_password($site['admin_password']);
            $site['admin_password'] = $decrypted_pass !== false ? $decrypted_pass : '密码解密失败！';
        }
    } else {
        // 如果找不到对应的ID，跳转回主面板
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="assets/admin-style.css">
</head>
<body>
    <div class="editor-container">
        <header class="dashboard-header">
            <h1><?php echo $page_title; ?></h1>
            <a href="dashboard.php" class="btn btn-secondary">返回主面板</a>
        </header>

        <main>
            <form action="<?php echo $form_action; ?>" method="POST" class="site-form">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$site['id']); ?>">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">🌐 站点名称 *</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($site['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="url">🔗 站点URL *</label>
                        <input type="url" id="url" name="url" value="<?php echo htmlspecialchars($site['url']); ?>" required>
                    </div>
                    <div class="form-group full-width">
                        <label for="description">📝 功能描述</label>
                        <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($site['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="category">📂 站点分类</label>
                        <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($site['category']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="status">✅ 状态</label>
                        <select id="status" name="status">
                            <option value="1" <?php echo ($site['status'] == 1) ? 'selected' : ''; ?>>🟢 正常</option>
                            <option value="0" <?php echo ($site['status'] == 0) ? 'selected' : ''; ?>>🔴 下线</option>
                            <option value="2" <?php echo ($site['status'] == 2) ? 'selected' : ''; ?>>🟡 维护</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="admin_url">⚙️ 后台地址</label>
                        <input type="url" id="admin_url" name="admin_url" value="<?php echo htmlspecialchars($site['admin_url']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="admin_username">👤 后台用户名</label>
                        <input type="text" id="admin_username" name="admin_username" value="<?php echo htmlspecialchars($site['admin_username']); ?>">
                    </div>
                     <div class="form-group">
                        <label for="admin_password">🔑 后台密码 (留空则不修改)</label>
                        <input type="text" id="admin_password" name="admin_password" value="">
                        <?php if($site_id): ?>
                           <small>当前密码: <?php echo htmlspecialchars($site['admin_password']); ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="tags">🏷️ 标签 (用英文逗号,分隔)</label>
                        <input type="text" id="tags" name="tags" value="<?php echo htmlspecialchars($site['tags']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="created_date">🗓️ 搭建时间</label>
                        <input type="date" id="created_date" name="created_date" value="<?php echo htmlspecialchars($site['created_date']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="updated_date">🔄 最后更新时间</label>
                        <input type="date" id="updated_date" name="updated_date" value="<?php echo htmlspecialchars($site['updated_date']); ?>">
                    </div>
                    <div class="form-group full-width">
                        <label for="notes">📌 注意事项</label>
                        <textarea id="notes" name="notes" rows="3"><?php echo htmlspecialchars($site['notes']); ?></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 保存信息</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
<?php
// 引入配置文件来获取数据库路径
if (!file_exists(__DIR__ . '/config.php')) {
    die('错误: 配置文件 config.php 不存在！请先根据 core/config.sample.php 在根目录创建。');
}
require_once __DIR__ . '/config.php';

// --- 安全检查 ---
if (file_exists(DB_PATH)) {
    die('警告: 数据库文件已存在！为防止数据丢失，请手动删除 data/my_sites.sqlite 文件后重试。');
}

try {
    // --- 连接数据库 (如果不存在则创建) ---
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ 数据库文件创建成功: " . DB_PATH . "<br>";

    // --- 创建 SITES 表 ---
    $sitesTableSql = "
    CREATE TABLE sites (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        url TEXT NOT NULL,
        description TEXT,
        category TEXT DEFAULT '默认分类',
        tags TEXT,
        status INTEGER DEFAULT 1,
        admin_url TEXT,
        admin_username TEXT,
        admin_password TEXT,
        notes TEXT,
        created_date TEXT,
        updated_date TEXT
    );";
    $pdo->exec($sitesTableSql);
    echo "✅ 数据表 'sites' 创建成功。<br>";

    // --- 创建 USERS 表 ---
    $usersTableSql = "
    CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL
    );";
    $pdo->exec($usersTableSql);
    echo "✅ 数据表 'users' 创建成功。<br>";

    // --- 创建默认管理员 ---
    $adminUser = 'admin';
    $adminPass = 'password'; // 强烈建议立即修改
    $passwordHash = password_hash($adminPass, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)");
    $stmt->execute(['username' => $adminUser, 'password_hash' => $passwordHash]);
    echo "✅ 默认管理员创建成功。<br>";
    echo "------------------------------------<br>";
    echo "<b>用户名:</b> " . htmlspecialchars($adminUser) . "<br>";
    echo "<b>密&nbsp;&nbsp;&nbsp;码:</b> " . htmlspecialchars($adminPass) . "<br>";
    echo "------------------------------------<br>";

    echo "🎉 <h2>安装完成！</h2>";
    echo "<p style='color:red; font-weight:bold;'>为了安全，请立即从服务器删除此 install.php 文件！</p>";

} catch (PDOException $e) {
    die("❌ 数据库操作失败: " . $e->getMessage());
}
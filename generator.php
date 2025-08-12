<?php
// 初始化变量
$generated_key = '';
$password_to_hash = '';
$generated_hash = '';

// --- 逻辑处理 ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 检查是否是生成随机密钥
    if (isset($_POST['generate_key'])) {
        // 使用密码学安全的方法生成一个32字节的随机串，并转换为64个字符的十六进制表示
        $generated_key = bin2hex(random_bytes(32));
    }

    // 检查是否是生成密码哈希
    if (isset($_POST['generate_hash'])) {
        $password_to_hash = $_POST['password_to_hash'];
        if (!empty($password_to_hash)) {
            // 使用 PHP 目前最推荐的 password_hash() 函数
            $generated_hash = password_hash($password_to_hash, PASSWORD_DEFAULT);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>安全密钥与哈希生成器</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; max-width: 800px; margin: 20px auto; padding: 0 15px; background-color: #f8f9fa; }
        .container { background-color: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        h1, h2 { border-bottom: 2px solid #eee; padding-bottom: 10px; color: #0056b3; }
        .section { margin-bottom: 30px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input[type="text"] { width: calc(100% - 20px); padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-top: 10px; }
        button:hover { background-color: #0056b3; }
        .result { background-color: #e9ecef; padding: 15px; border-radius: 4px; word-wrap: break-word; font-family: "Courier New", Courier, monospace; margin-top: 15px; border: 1px solid #ced4da; }
        .warning { color: #c00; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 安全密钥与哈希生成器</h1>
        
        <div class="section">
            <h2>1. ENCRYPTION_KEY 生成器</h2>
            <p>用于 `config.php` 文件中的加密密钥。点击按钮生成一个密码学安全的64位随机密钥。</p>
            <form method="POST" action="">
                <button type="submit" name="generate_key">🚀 生成随机密钥</button>
            </form>
            <?php if ($generated_key): ?>
                <p><strong>生成结果:</strong></p>
                <div class="result"><?php echo htmlspecialchars($generated_key); ?></div>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>2. 密码哈希生成器</h2>
            <p>用于将你的管理员密码转换为安全的哈希值。输入你想要的密码，然后点击生成。</p>
            <form method="POST" action="">
                <label for="password_to_hash">输入你的密码:</label>
                <input type="text" id="password_to_hash" name="password_to_hash" value="<?php echo htmlspecialchars($password_to_hash); ?>" required>
                <button type="submit" name="generate_hash">🛡️ 生成哈希值</button>
            </form>
            <?php if ($generated_hash): ?>
                <p><strong>生成结果 (此哈希值将被存入数据库):</strong></p>
                <div class="result"><?php echo htmlspecialchars($generated_hash); ?></div>
            <?php endif; ?>
        </div>

        <p class="warning">⚠️ 使用完毕后，请务必从服务器上删除此 `generator.php` 文件以确保安全。</p>
    </div>
</body>
</html>
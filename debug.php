<?php
header('Content-Type: text/plain; charset=utf-8');

echo "--- 站点目录权限诊断脚本 ---\n\n";

// 诊断一：检查配置文件是否存在
$config_path = __DIR__ . '/config.php';
echo "1. 检查配置文件...\n";
if (!file_exists($config_path)) {
    echo "   ❌ 失败: 配置文件 '{$config_path}' 不存在！\n\n";
    exit;
}
require_once $config_path;
echo "   ✅ 成功: 配置文件加载成功。\n\n";


// 诊断二：检查数据库路径和目录
echo "2. 检查数据库路径和目录...\n";
if (!defined('DB_PATH')) {
    echo "   ❌ 失败: 配置文件中未定义常量 'DB_PATH'！\n\n";
    exit;
}

$db_path = DB_PATH;
$data_dir = dirname($db_path);

echo "   - 配置文件中的数据库路径 (DB_PATH): {$db_path}\n";
echo "   - 解析出的数据目录路径: {$data_dir}\n\n";


// 诊断三：检查数据目录是否存在
echo "3. 检查 '{$data_dir}' 目录是否存在...\n";
if (is_dir($data_dir)) {
    echo "   ✅ 成功: 目录存在。\n\n";
} else {
    echo "   ❌ 失败: 目录不存在！请检查 'data' 目录是否已创建在网站根目录下。\n\n";
    exit;
}


// 诊断四：检查数据目录是否可写 (这是最关键的一步)
echo "4. 检查 '{$data_dir}' 目录是否可写...\n";
if (is_writable($data_dir)) {
    echo "   ✅ 成功: PHP脚本有权限在此目录中写入文件。\n\n";
} else {
    echo "   ❌ 失败: PHP脚本没有权限在此目录中写入文件。\n";
    echo "      这通常是服务器文件权限或所有权问题。请再次确认 DirectAdmin 中此目录的权限已设为 775 或 777。\n\n";
    // 尝试获取服务器用户信息，帮助诊断
    if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
        $processUser = posix_getpwuid(posix_geteuid());
        echo "   - 执行PHP的服务器用户: " . $processUser['name'] . "\n\n";
    }
}


// 诊断五：检查磁盘空间配额
echo "5. 检查磁盘剩余空间...\n";
$free_space = @disk_free_space($data_dir);
if ($free_space !== false) {
    echo "   ✅ 成功: 当前目录所在磁盘剩余空间: " . round($free_space / 1024 / 1024, 2) . " MB\n\n";
} else {
    echo "   ⚠️ 警告: 无法获取磁盘剩余空间信息。\n\n";
}


echo "--- 诊断结束 ---";
echo "--- 核心函数库诊断脚本 ---\n\n";

// --- 第一部分：检查 crypto.php 文件本身 ---
echo "1. 检查文件 '/core/crypto.php'...\n";
$crypto_path = __DIR__ . '/core/crypto.php';

if (!file_exists($crypto_path)) {
    echo "   ❌ 致命错误: 文件不存在！请立即创建此文件。\n\n";
    exit;
}
echo "   ✅ 文件存在。\n";

if (!is_readable($crypto_path)) {
    echo "   ❌ 致命错误: 文件不可读！请检查其文件权限。\n\n";
    exit;
}
echo "   ✅ 文件可读。\n";

// 读取文件内容进行检查
$crypto_content = file_get_contents($crypto_path);
if (empty(trim($crypto_content))) {
    echo "   ❌ 致命错误: 文件是空的！请将正确的代码粘贴进去。\n\n";
    exit;
}
echo "   ✅ 文件非空。\n";

// 检查函数定义是否在文件内容中
if (strpos($crypto_content, 'function encrypt_password') !== false) {
    echo "   ✅ 成功: 在文件内容中找到了 'function encrypt_password' 的定义。\n\n";
} else {
    echo "   ❌ 致命错误: 在文件内容中 未能找到 'function encrypt_password' 的定义！文件内容是错误的或过时的。\n\n";
    exit;
}


// --- 第二部分：尝试加载环境并检查函数 ---
echo "2. 模拟真实环境加载并检查函数...\n";
try {
    // 我们只加载 bootstrap，它应该会帮我们加载所有东西
    require_once __DIR__ . '/core/bootstrap.php';
    echo "   ✅ 'bootstrap.php' 加载成功。\n";

    // 这是最终的检查
    if (function_exists('encrypt_password')) {
        echo "   ✅✅✅ 终极成功: 'encrypt_password' 函数在当前环境中已定义！\n\n";
        echo "--- 诊断结论 --- \n";
        echo "诊断脚本表明一切正常。如果 'save.php' 仍然报错，问题可能非常诡异，例如服务器缓存（OPCache）。\n";
        echo "请尝试在 'save.php' 文件的最顶部添加一行代码 `opcache_reset();` 来强制刷新缓存再试一次。";

    } else {
        echo "   ❌❌❌ 终极失败: 'encrypt_password' 函数在加载环境后依然未定义！\n\n";
        echo "--- 诊断结论 --- \n";
        echo "这说明 'bootstrap.php' 的加载流程有问题，或者 'crypto.php' 文件有语法错误导致其没有被完全解析。\n";
        echo "请仔细核对 'bootstrap.php' 和 'crypto.php' 的每一行代码。";
    }

} catch (Exception $e) {
    echo "   ❌ 在加载 bootstrap.php 时发生异常: " . $e->getMessage() . "\n";
}

echo "\n--- 诊断结束 ---";

?>
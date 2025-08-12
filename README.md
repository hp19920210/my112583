# 📑 我的站点目录 (My Site Directory)

最初创建这个项目，是为了解决一个很简单的需求：用一种优雅、有条理且完全由我掌控的方式，来管理和展示搭建的所有网站。希望有一个私密的后台，可以安全地记录每个站点的后台地址、用户名、密码等敏感信息；同时，也有一个漂亮的公开主页，能将这些作品作为我的数字名片展示给访客。

经过努力，这个小小的想法最终变成了一个功能完整、设计精良的全功能Web应用。这份文档，就是它的使用说明书。

## ✨ 功能亮点

* **① 全功能后台**: 拥有安全的用户认证和完整的站点信息增、删、改、查（CRUD）功能。
* **② 响应式前端**: 精心设计的前端页面，采用动态边框卡片布局，在桌面和移动设备上都有绝佳的浏览体验。
* **③ 安全凭据管理**: 后台密码使用强哈希加密；站点记录的密码则使用双向加密，并提供了便捷的“一键复制”功能，兼顾安全与便利。
* **④ 主题自适应**: 自动适应操作系统的浅色/深色模式，提供舒适的视觉体验。
* **⑤ PWA支持**: 支持“添加到主屏幕”，可像原生App一样被安装到桌面或手机，并具备基础的离线访问能力。
* **⑥ 动态页脚**: 页脚集成了一个实时跳动的北京时间霓虹灯时钟，为页面增添了一丝生动的“彩蛋”气息。

## 🚀 快速部署指南

想要从零开始将这个项目部署到你自己的服务器上？非常简单，只需四步。

### **第1步：上传文件**

将我们最终确定的项目目录（包含 `index.php`, `admin/`, `assets/`, `core/`, `data/` 等）完整上传到你的网站根目录（例如 `public_html`）。

### **第2步：创建并编辑配置文件**

这是最关键的一步，我们需要告诉项目如何连接数据库以及如何加密数据。

1. 在项目 *根目录* 下，创建一个名为 `config.php` 的文件。
2. 将以下内容复制到 `config.php` 中：

    ```php
    <?php
    // 数据库文件路径 (通常无需修改)
    define('DB_PATH', __DIR__ . '/data/my_sites.sqlite');

    // ‼️ 重要: 用于加密站点密码的密钥。
    // 请务必替换成一个你自己生成的、长且随机的字符串。
    define('ENCRYPTION_KEY', '在这里填入你生成的64位随机密钥');
    
3. **生成并替换密钥**: 访问一个安全的密码生成网站（如 `1Password` 的在线生成器或者generator.php文件），创建一个至少64位的随机字符串，用它替换上面代码中的 `'在这里填入你生成的64位随机密钥'`。

### **第3步：初始化数据库**

数据库的创建是通过一个一次性的安装脚本来完成的。

1. 在项目 **根目录** 下，创建一个名为 `install.php` 的文件。
2. 将以下代码完整地复制进去：

    ```php
    <?php
    if (!file_exists(__DIR__ . '/config.php')) {
        die('错误: 配置文件 config.php 不存在！');
    }
    require_once __DIR__ . '/config.php';
    if (file_exists(DB_PATH)) {
        die('警告: 数据库文件已存在！为防止数据丢失，请手动删除 data/my_sites.sqlite 文件后重试。');
    }
    try {
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 创建 SITES 表
        $pdo->exec("CREATE TABLE sites (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, url TEXT NOT NULL, description TEXT, category TEXT DEFAULT '默认分类', tags TEXT, status INTEGER DEFAULT 1, admin_url TEXT, admin_username TEXT, admin_password TEXT, notes TEXT, created_date TEXT, updated_date TEXT);");
        
        // 创建 USERS 表
        $pdo->exec("CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT NOT NULL UNIQUE, password_hash TEXT NOT NULL);");
        
        // 创建默认管理员
        $adminUser = 'admin';
        $adminPass = 'password';
        $passwordHash = password_hash($adminPass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)");
        $stmt->execute(['username' => $adminUser, 'password_hash' => $passwordHash]);

        echo "<h1>🎉 安装成功！</h1>";
        echo "<p>数据库和数据表已成功创建。</p>";
        echo "<p>默认后台登录凭据：</p>";
        echo "<ul><li><strong>用户名:</strong> admin</li><li><strong>密码:</strong> password</li></ul>";
        echo "<p style='color:red; font-weight:bold;'>为了安全，请立即从服务器删除此 install.php 文件！</p>";

    } catch (PDOException $e) {
        die("❌ 数据库操作失败: " . $e->getMessage());
    }

3.**运行脚本**: 在浏览器中访问 `https://你的域名/install.php`。
4. **‼️ 删除脚本**: 看到“安装成功”的提示后，**请立即从服务器上删除 `install.php` 文件！** 这是保障安全的必要步骤。

### **第4步：首次登录与使用**

1. 访问 `https://你的域名/admin/` 进入后台登录页。
2. 使用默认凭据登录：
    * 用户名: `admin`
    * 密码: `password`
3. 登录后，点击右上角的 **“⚙️ 账户设置”**，立刻修改你的默认用户名和密码。

恭喜你，现在整个系统已经完全属于你了！

## 🔧 后台使用指南

* **主面板 (`dashboard.php`)**: 这里以卡片形式展示你所有的站点。你可以直观地看到每个站点的关键信息，并通过卡片底部的按钮进行编辑或删除。对于用户名和密码，点击旁边的“📋”图标即可一键复制。
* **添加/编辑站点 (`editor.php`)**: 点击“➕ 添加新站点”或“✏️ 编辑”按钮后进入此页面。这里包含了站点的所有字段，请根据提示填写。
* **账户设置 (`account.php`)**: 在这里修改你的后台登录凭据。注意，修改密码后系统会为了安全强制你重新登录。

## ⭐ PWA (桌面安装)

在支持的浏览器（如电脑或手机上的Chrome、Edge、Safari）上访问你的网站主页时，地址栏或菜单中会出现“安装”或“添加到主屏幕”的选项。点击后，你的网站就会像一个独立的App一样出现在你的设备上，拥有自己的图标和独立的窗口。

---

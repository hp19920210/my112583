<?php

require_once __DIR__ . '/core/bootstrap.php';
$items_per_page = 15;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;
$pdo = get_pdo();
$total_stmt = $pdo->query("SELECT COUNT(*) FROM sites WHERE status = 1");
$total_items = $total_stmt->fetchColumn();
$total_pages = ceil($total_items / $items_per_page);
$stmt = $pdo->prepare("SELECT * FROM sites WHERE status = 1 ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$sites = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的站点目录</title>

    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="manifest.json">

    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <div class="content-wrapper">
        <h1 class="main-title">📑 我的站点目录</h1>
        <div class="sites-list-container">
            <?php if (empty($sites)): ?>
                <div class="empty-state">🖥️ 暂无在线站点，请前往后台添加。</div>
            <?php else: ?>
                <?php foreach ($sites as $site): ?>
                    <div class="site-card">
                        <div class="site-card-content">
                            <header class="card-header">
                                <h2 class="site-name">
                                    <a href="<?php echo htmlspecialchars($site['url']); ?>" target="_blank" rel="noopener noreferrer">
                                        🖥️ <?php echo htmlspecialchars($site['name']); ?>
                                    </a>
                                </h2>
                            </header>
                            <div class="info-grid">
                                <div class="info-item"><span class="label">🌐 站点链接</span><span class="value"><a href="<?php echo htmlspecialchars($site['url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($site['url']); ?></a></span></div>
                                <div class="info-item"><span class="label">📝 站点描述</span><span class="value"><?php echo htmlspecialchars($site['description']); ?></span></div>
                                <div class="info-item"><span class="label">⚙️ 后台管理</span><span class="value"><a href="<?php echo htmlspecialchars($site['admin_url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($site['admin_url'] ?: '未提供'); ?></a></span></div>
                                <div class="info-item"><span class="label">📌 注意事项</span><span class="value"><?php echo htmlspecialchars($site['notes'] ?: '无'); ?></span></div>
                                <div class="info-item"><span class="label">🗓️ 创建时间</span><span class="value"><?php echo htmlspecialchars($site['created_date']); ?></span></div>
                                <div class="info-item"><span class="label">🔄 最后更新</span><span class="value"><?php echo htmlspecialchars($site['updated_date']); ?></span></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php if ($total_pages > 1): ?>
            <nav class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?><a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $current_page) ? 'current' : ''; ?>"><?php echo $i; ?></a><?php endfor; ?>
            </nav>
        <?php endif; ?>
    </div>
    <footer class="footer-time"><span id="beijing-time"></span></footer>
    <script>
        function updateBeijingTime() {
            const timeElement = document.getElementById('beijing-time');
            if (!timeElement) return;
            const formatter = new Intl.DateTimeFormat('zh-CN', {
                timeZone: 'Asia/Shanghai',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });
            timeElement.textContent = `BEIJING TIME: ${formatter.format(new Date())}`;
        }
        updateBeijingTime();
        setInterval(updateBeijingTime, 1000);
        // Service Worker 注册逻辑
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('Service Worker registered successfully:', registration);
                    })
                    .catch(error => {
                        console.log('Service Worker registration failed:', error);
                    });
            });
        }
    </script>
</body>

</html>
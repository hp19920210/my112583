<?php
require_once __DIR__ . '/../core/bootstrap.php';

// 权限检查
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 从数据库获取所有站点数据
$pdo = get_pdo();
$stmt = $pdo->query("SELECT * FROM sites ORDER BY id DESC");
$sites = $stmt->fetchAll();

// 状态徽章函数
function get_status_badge($status)
{
    switch ($status) {
        case 0:
            return '<span class="badge status-offline">🔴 下线</span>';
        case 2:
            return '<span class="badge status-maintenance">🟡 维护</span>';
        default:
            return '<span class="badge status-online">🟢 正常</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台主面板</title>
    <link rel="stylesheet" href="assets/admin-style.css">
</head>

<body>
    <div class="main-container">
        <header class="dashboard-header">
            <h1>🖥️ 站点目录管理面板</h1>
            <div class="user-info">
                <span>你好, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</span>
                <a href="account.php" class="btn btn-secondary">⚙️ 账户设置</a>
                <a href="logout.php" class="btn btn-logout">安全退出</a>
            </div>
        </header>

        <main>
            <div class="toolbar">
                <a href="editor.php" class="btn btn-primary">➕ 添加新站点</a>
            </div>

            <div class="site-cards-container">
                <?php if (empty($sites)): ?>
                    <div class="empty-state">还没有任何站点，请点击上方按钮添加一个吧！</div>
                <?php else: ?>
                    <?php foreach ($sites as $site): ?>
                        <div class="marquee-card">
                            <div class="card-content">
                                <header class="card-main-header">
                                    <h3 class="card-site-name">🖥️ <?php echo htmlspecialchars($site['name']); ?></h3>
                                    <?php echo get_status_badge($site['status']); ?>
                                </header>

                                <div class="card-section">
                                    <p class="description-text"><strong>📝 站点描述:</strong> <?php echo htmlspecialchars($site['description'] ?: '无'); ?></p>
                                </div>

                                <div class="card-info-grid">
                                    <div class="info-item">
                                        <strong>🔗 公开链接:</strong>
                                        <a href="<?php echo htmlspecialchars($site['url']); ?>" target="_blank"><?php echo htmlspecialchars($site['url']); ?></a>
                                    </div>
                                    <div class="info-item">
                                        <strong>⚙️ 后台地址:</strong>
                                        <a href="<?php echo htmlspecialchars($site['admin_url']); ?>" target="_blank"><?php echo htmlspecialchars($site['admin_url'] ?: '无'); ?></a>
                                    </div>
                                    <div class="info-item">
                                        <strong>👤 用户名:</strong>
                                        <div class="copy-wrapper">
                                            <span><?php echo htmlspecialchars($site['admin_username'] ?: '无'); ?></span>
                                            <?php if (!empty($site['admin_username'])): ?>
                                                <span class="copy-icon" title="复制" data-copy-value="<?php echo htmlspecialchars($site['admin_username']); ?>">📋</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <strong>🔑 密码:</strong>
                                        <div class="copy-wrapper">
                                            <span>********</span>
                                            <?php
                                            $decrypted_password = !empty($site['admin_password']) ? decrypt_password($site['admin_password']) : '';
                                            ?>
                                            <?php if ($decrypted_password): ?>
                                                <span class="copy-icon" title="复制" data-copy-value="<?php echo htmlspecialchars($decrypted_password); ?>">📋</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <strong>🗓️ 创建时间:</strong>
                                        <span><?php echo htmlspecialchars($site['created_date']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <strong>🔄 最后更新:</strong>
                                        <span title="<?php echo htmlspecialchars($site['updated_date']); ?>"><?php echo format_relative_time($site['updated_date']); ?></span>
                                    </div>
                                </div>

                                <?php if (!empty($site['notes'])): ?>
                                    <div class="card-section notes-section">
                                        <p><strong>📌 注意事项:</strong> <?php echo nl2br(htmlspecialchars($site['notes'])); ?></p>
                                    </div>
                                <?php endif; ?>

                                <footer class="card-footer">
                                    <div class="actions">
                                        <a href="editor.php?id=<?php echo $site['id']; ?>" class="btn btn-edit">✏️ 编辑</a>
                                        <a href="delete.php?id=<?php echo $site['id']; ?>" class="btn btn-delete js-delete-link" data-site-name="<?php echo htmlspecialchars($site['name']); ?>">🗑️ 删除</a>
                                    </div>
                                </footer>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <div id="delete-modal" class="modal-overlay" style="display: none;">
        <div class="modal-box">
            <h2 class="modal-title">🗑️ 确认删除</h2>
            <p class="modal-content">你真的要删除站点 <strong id="modal-site-name"></strong> 吗？<br>这个操作将无法撤销。</p>
            <div class="modal-actions">
                <button id="modal-cancel-btn" class="btn btn-secondary">取消</button>
                <a id="modal-confirm-btn" href="#" class="btn btn-logout">确认删除</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('delete-modal');
            if (modal) {
                const modalSiteName = document.getElementById('modal-site-name');
                const modalConfirmBtn = document.getElementById('modal-confirm-btn');
                const modalCancelBtn = document.getElementById('modal-cancel-btn');
                const deleteLinks = document.querySelectorAll('.js-delete-link');
                deleteLinks.forEach(link => {
                    link.addEventListener('click', function(event) {
                        event.preventDefault();
                        const siteName = this.getAttribute('data-site-name');
                        const deleteUrl = this.getAttribute('href');
                        modalSiteName.textContent = siteName;
                        modalConfirmBtn.setAttribute('href', deleteUrl);
                        modal.style.display = 'flex';
                    });
                });
                modalCancelBtn.addEventListener('click', () => {
                    modal.style.display = 'none';
                });
                modal.addEventListener('click', function(event) {
                    if (event.target === this) {
                        modal.style.display = 'none';
                    }
                });
            }


            const copyIcons = document.querySelectorAll('.copy-icon');
            copyIcons.forEach(icon => {
                icon.addEventListener('click', function(e) {
                    e.preventDefault();
                    const valueToCopy = this.getAttribute('data-copy-value');
                    navigator.clipboard.writeText(valueToCopy).then(() => {
                        const originalIcon = this.textContent;
                        this.textContent = '✅';
                        setTimeout(() => {
                            this.textContent = originalIcon;
                        }, 1500);
                    }).catch(err => {
                        console.error('复制失败: ', err);
                    });
                });
            });
        });
    </script>
</body>

</html>
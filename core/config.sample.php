<?php
/**
 * 配置文件模板
 *
 * 请复制此文件为 config.php, 然后填入真实配置。
 * config.php 已被添加到 .gitignore, 不会进入版本控制。
 */

// 数据库文件路径
define('DB_PATH', __DIR__ . '/../data/my_sites.sqlite');

// ‼️ 重要: 用于加密站点密码的密钥。必须是一个长且随机的字符串。
define('ENCRYPTION_KEY', '!!!GENERATE-A-RANDOM-KEY-AND-PUT-IT-HERE!!!');
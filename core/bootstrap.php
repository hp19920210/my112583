<?php
// 开启会话
session_start();

// 设置时区，以确保时间函数正常工作
date_default_timezone_set('Asia/Shanghai');

// 开启错误报告（开发阶段）
// 在生产环境中，你可能希望将其关闭或记录到文件
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 加载私人配置文件
// 我们将 config.php 放在根目录，比放在 core 目录更安全一层
if (!file_exists(__DIR__ . '/../config.php')) {
    die('错误: 配置文件 config.php 不存在！请先根据 config.sample.php 创建。');
}
require_once __DIR__ . '/../config.php';

// 加载核心函数库 (未来我们将在这里添加更多文件)
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/crypto.php';
require_once __DIR__ . '/functions.php';
<?php
/**
 * 数据库连接模块
 * 提供一个可复用的函数来获取PDO数据库连接对象。
 */

function get_pdo(): PDO
{
    // 这个静态变量确保在同一次请求中，数据库只被连接一次。
    static $pdo = null;

    if ($pdo === null) {
        try {
            // 从 bootstrap.php 加载的 config.php 中获取 DB_PATH
            $pdo = new PDO('sqlite:' . DB_PATH);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // 设置默认的获取模式为关联数组
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // 如果连接失败，停止执行并显示错误
            die("❌ 数据库连接失败: " . $e->getMessage());
        }
    }

    return $pdo;
}
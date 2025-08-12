<?php
/**
 * 格式化日期为相对时间 (例如: "3天前")
 * * @param string $date_string 标准的日期时间字符串 (例如: '2025-08-10')
 * @return string 格式化后的相对时间字符串
 */
function format_relative_time(string $date_string): string
{
    if (empty($date_string)) {
        return '未设置';
    }

    $timestamp = strtotime($date_string);
    // 如果时间戳解析失败，直接返回原始字符串
    if ($timestamp === false) {
        return htmlspecialchars($date_string);
    }
    
    $diff_seconds = time() - $timestamp;

    // 如果是未来的时间，直接显示日期
    if ($diff_seconds < 0) {
        return htmlspecialchars($date_string);
    }

    // 计算时间间隔
    if ($diff_seconds < 60) {
        return "刚刚";
    }
    
    $diff_minutes = round($diff_seconds / 60);
    if ($diff_minutes < 60) {
        return $diff_minutes . " 分钟前";
    }

    $diff_hours = round($diff_seconds / 3600);
    if ($diff_hours < 24) {
        return $diff_hours . " 小时前";
    }

    $diff_days = round($diff_seconds / 86400);
    if ($diff_days < 30) {
        return $diff_days . " 天前";
    }
    
    $diff_months = round($diff_seconds / 2592000);
    if ($diff_months < 12) {
        return $diff_months . " 个月前";
    }

    $diff_years = round($diff_seconds / 31536000);
    return $diff_years . " 年前";
}
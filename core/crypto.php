<?php
/**
 * 加密/解密模块
 */

// 定义加密算法和选项
define('ENCRYPTION_METHOD', 'AES-256-CBC');

/**
 * 加密密码
 * @param string $plaintext 原始密码
 * @return string|false 返回 "iv:ciphertext" 格式的加密字符串，失败则返回false
 */
function encrypt_password(string $plaintext): string|false
{
    // ENCRYPTION_KEY 来自 config.php
    $key = ENCRYPTION_KEY;
    
    // 生成一个密码学安全的、该算法所需的长度的初始化向量 (IV)
    $iv_length = openssl_cipher_iv_length(ENCRYPTION_METHOD);
    $iv = openssl_random_pseudo_bytes($iv_length);

    // 加密
    $ciphertext = openssl_encrypt($plaintext, ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);

    if ($ciphertext === false) {
        return false;
    }

    // 将 IV 和密文一起存储，用冒号分隔。IV对于解密至关重要。
    // 使用 base64 编码以确保可以安全地存入数据库。
    return base64_encode($iv . $ciphertext);
}

/**
 * 解密密码
 * @param string $encrypted_string "iv:ciphertext" 格式的加密字符串
 * @return string|false 返回原始密码，失败则返回false
 */
function decrypt_password(string $encrypted_string): string|false
{
    $key = ENCRYPTION_KEY;
    
    // Base64 解码
    $decoded_string = base64_decode($encrypted_string, true);
    if ($decoded_string === false) {
        return false;
    }

    // 分离 IV 和密文
    $iv_length = openssl_cipher_iv_length(ENCRYPTION_METHOD);
    $iv = substr($decoded_string, 0, $iv_length);
    $ciphertext = substr($decoded_string, $iv_length);
    
    // 解密
    $plaintext = openssl_decrypt($ciphertext, ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);

    return $plaintext;
}
<?php
/**
 * Security Helper Functions
 * Protection against XSS, CSRF, SQL Injection, Brute Force
 */

// ========== CSRF Protection ==========
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}

function checkCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            die('Invalid CSRF token');
        }
    }
}

// ========== Rate Limiting ==========
function checkRateLimit($action = 'login', $maxAttempts = 5, $windowSeconds = 300) {
    $key = 'rate_' . $action . '_' . $_SERVER['REMOTE_ADDR'];
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'first_attempt' => time()];
    }
    
    $data = &$_SESSION[$key];
    
    // Reset if window expired
    if (time() - $data['first_attempt'] > $windowSeconds) {
        $data = ['count' => 0, 'first_attempt' => time()];
    }
    
    $data['count']++;
    
    if ($data['count'] > $maxAttempts) {
        $remaining = $windowSeconds - (time() - $data['first_attempt']);
        return ['blocked' => true, 'remaining' => $remaining];
    }
    
    return ['blocked' => false, 'attempts' => $data['count']];
}

function resetRateLimit($action = 'login') {
    $key = 'rate_' . $action . '_' . $_SERVER['REMOTE_ADDR'];
    unset($_SESSION[$key]);
}

// ========== Input Sanitization ==========
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function sanitizeFileName($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    return substr($filename, 0, 100);
}

// ========== Security Headers ==========
function setSecurityHeaders() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https: blob:; frame-src https://www.google.com;");
    
    // Prevent caching for admin pages
    if (strpos($_SERVER['REQUEST_URI'], '/admin') !== false) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }
}

// ========== Password Security ==========
function hashPassword($password) {
    // Use BCRYPT for PHP 7.4 compatibility
    return password_hash($password, PASSWORD_BCRYPT, [
        'cost' => 12
    ]);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// ========== Session Security ==========
function secureSession() {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Regenerate session ID periodically
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } elseif (time() - $_SESSION['created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// ========== IP Validation ==========
function getClientIP() {
    $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = explode(',', $_SERVER[$header])[0];
            if (filter_var(trim($ip), FILTER_VALIDATE_IP)) {
                return trim($ip);
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// ========== File Upload Security ==========
function validateUpload($file, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']) {
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Upload error';
        return $errors;
    }
    
    // Check file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        $errors[] = 'File too large (max ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB)';
    }
    
    // Check MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, $allowedTypes)) {
        $errors[] = 'Invalid file type';
    }
    
    // Check for PHP code in file
    $content = file_get_contents($file['tmp_name']);
    if (preg_match('/<\?php|<\?=/i', $content)) {
        $errors[] = 'Invalid file content';
    }
    
    return $errors;
}

// ========== Logging ==========
function logSecurityEvent($event, $details = []) {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/security_' . date('Y-m') . '.log';
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => getClientIP(),
        'event' => $event,
        'uri' => $_SERVER['REQUEST_URI'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'details' => $details
    ];
    
    file_put_contents($logFile, json_encode($logEntry) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

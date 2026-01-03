<?php
/**
 * Application Configuration
 * LearnPro Academy - Landing Page CMS
 */

session_start();

// Site Settings
define('SITE_URL', 'http://localhost/landingap');
define('ADMIN_PATH', '/admin');

// Security
define('SECRET_KEY', 'LP_2026_' . md5('landingap_secret'));

// Captcha Settings
define('CAPTCHA_TYPE', 'custom'); // 'custom' or 'google'
define('GOOGLE_RECAPTCHA_SITE_KEY', ''); // Fill when using Google
define('GOOGLE_RECAPTCHA_SECRET_KEY', ''); // Fill when using Google

// Map Settings
define('MAP_PROVIDER', 'openstreetmap'); // 'openstreetmap' or 'google'
define('GOOGLE_MAPS_API_KEY', ''); // Fill when using Google Maps

// Upload Settings
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Include Database
require_once __DIR__ . '/database.php';

// Helper Functions
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: " . SITE_URL . $url);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/admin/login.php');
    }
}

// Get Settings from DB
function getSetting($key, $default = '') {
    static $settings = null;
    if ($settings === null) {
        $pdo = getDB();
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $settings[$key] ?? $default;
}

function getAllSettings() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM settings ORDER BY setting_group, id");
    return $stmt->fetchAll();
}

function updateSetting($key, $value) {
    $pdo = getDB();
    $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
    return $stmt->execute([$value, $key]);
}

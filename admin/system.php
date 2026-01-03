<?php
/**
 * System Settings Page
 * Manage Captcha, Map API, and other system configurations
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/security.php';
requireLogin();
setSecurityHeaders();

$page_title = 'ตั้งค่าระบบ';
$current_page = 'system';
$message = '';
$error = '';

// Get current config
$configPath = __DIR__ . '/../config/config.php';
$configContent = file_get_contents($configPath);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF();
    
    $captchaType = $_POST['captcha_type'] ?? 'custom';
    $googleRecaptchaSite = sanitizeInput($_POST['google_recaptcha_site'] ?? '');
    $googleRecaptchaSecret = sanitizeInput($_POST['google_recaptcha_secret'] ?? '');
    $mapProvider = $_POST['map_provider'] ?? 'openstreetmap';
    $googleMapsKey = sanitizeInput($_POST['google_maps_key'] ?? '');
    
    // Update config file
    $configContent = preg_replace(
        "/define\('CAPTCHA_TYPE', '.*?'\);/",
        "define('CAPTCHA_TYPE', '$captchaType');",
        $configContent
    );
    $configContent = preg_replace(
        "/define\('GOOGLE_RECAPTCHA_SITE_KEY', '.*?'\);/",
        "define('GOOGLE_RECAPTCHA_SITE_KEY', '$googleRecaptchaSite');",
        $configContent
    );
    $configContent = preg_replace(
        "/define\('GOOGLE_RECAPTCHA_SECRET_KEY', '.*?'\);/",
        "define('GOOGLE_RECAPTCHA_SECRET_KEY', '$googleRecaptchaSecret');",
        $configContent
    );
    $configContent = preg_replace(
        "/define\('MAP_PROVIDER', '.*?'\);/",
        "define('MAP_PROVIDER', '$mapProvider');",
        $configContent
    );
    $configContent = preg_replace(
        "/define\('GOOGLE_MAPS_API_KEY', '.*?'\);/",
        "define('GOOGLE_MAPS_API_KEY', '$googleMapsKey');",
        $configContent
    );
    
    if (file_put_contents($configPath, $configContent)) {
        $message = 'บันทึกการตั้งค่าระบบเรียบร้อย!';
        logSecurityEvent('system_config_updated', ['admin' => $_SESSION['admin_username']]);
    } else {
        $error = 'ไม่สามารถบันทึกไฟล์ config ได้';
    }
}

// Parse current values
preg_match("/define\('CAPTCHA_TYPE', '(.*?)'\);/", $configContent, $m);
$currentCaptcha = $m[1] ?? 'custom';
preg_match("/define\('GOOGLE_RECAPTCHA_SITE_KEY', '(.*?)'\);/", $configContent, $m);
$googleRecaptchaSite = $m[1] ?? '';
preg_match("/define\('GOOGLE_RECAPTCHA_SECRET_KEY', '(.*?)'\);/", $configContent, $m);
$googleRecaptchaSecret = $m[1] ?? '';
preg_match("/define\('MAP_PROVIDER', '(.*?)'\);/", $configContent, $m);
$currentMap = $m[1] ?? 'openstreetmap';
preg_match("/define\('GOOGLE_MAPS_API_KEY', '(.*?)'\);/", $configContent, $m);
$googleMapsKey = $m[1] ?? '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <h1>🔧 ตั้งค่าระบบ</h1>
            <p class="page-subtitle">จัดการ Captcha และ Map API</p>
            
            <?php if ($message): ?>
            <div class="alert alert-success"><?= e($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" class="settings-form">
                <?= csrfField() ?>
                
                <!-- Captcha Settings -->
                <div style="background: rgba(99,102,241,0.1); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem;">🔐 ระบบ Captcha</h3>
                    
                    <div class="form-group">
                        <label>ประเภท Captcha</label>
                        <select name="captcha_type" id="captchaType" onchange="toggleCaptchaFields()">
                            <option value="custom" <?= $currentCaptcha === 'custom' ? 'selected' : '' ?>>Custom Captcha (ระบบเอง)</option>
                            <option value="google" <?= $currentCaptcha === 'google' ? 'selected' : '' ?>>Google reCAPTCHA v2</option>
                        </select>
                    </div>
                    
                    <div id="googleCaptchaFields" style="display: <?= $currentCaptcha === 'google' ? 'block' : 'none' ?>;">
                        <div class="form-group">
                            <label>Google reCAPTCHA Site Key</label>
                            <input type="text" name="google_recaptcha_site" value="<?= e($googleRecaptchaSite) ?>" placeholder="6LexxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxAA">
                        </div>
                        <div class="form-group">
                            <label>Google reCAPTCHA Secret Key</label>
                            <input type="text" name="google_recaptcha_secret" value="<?= e($googleRecaptchaSecret) ?>" placeholder="6LexxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxAA">
                        </div>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">
                            📌 สมัครได้ที่: <a href="https://www.google.com/recaptcha/admin" target="_blank" style="color: var(--primary);">Google reCAPTCHA Admin</a>
                        </p>
                    </div>
                </div>
                
                <!-- Map Settings -->
                <div style="background: rgba(14,165,233,0.1); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem;">🗺️ ระบบแผนที่</h3>
                    
                    <div class="form-group">
                        <label>ผู้ให้บริการแผนที่</label>
                        <select name="map_provider" id="mapProvider" onchange="toggleMapFields()">
                            <option value="openstreetmap" <?= $currentMap === 'openstreetmap' ? 'selected' : '' ?>>OpenStreetMap (ฟรี)</option>
                            <option value="google" <?= $currentMap === 'google' ? 'selected' : '' ?>>Google Maps</option>
                        </select>
                    </div>
                    
                    <div id="googleMapFields" style="display: <?= $currentMap === 'google' ? 'block' : 'none' ?>;">
                        <div class="form-group">
                            <label>Google Maps API Key</label>
                            <input type="text" name="google_maps_key" value="<?= e($googleMapsKey) ?>" placeholder="AIzaxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                        </div>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">
                            📌 สร้าง API Key ได้ที่: <a href="https://console.cloud.google.com/apis/credentials" target="_blank" style="color: var(--primary);">Google Cloud Console</a>
                        </p>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">💾 บันทึกการตั้งค่า</button>
            </form>
        </div>
    </main>
    
    <script>
    function toggleCaptchaFields() {
        const type = document.getElementById('captchaType').value;
        document.getElementById('googleCaptchaFields').style.display = type === 'google' ? 'block' : 'none';
    }
    function toggleMapFields() {
        const provider = document.getElementById('mapProvider').value;
        document.getElementById('googleMapFields').style.display = provider === 'google' ? 'block' : 'none';
    }
    </script>
</body>
</html>

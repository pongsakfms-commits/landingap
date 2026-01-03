<?php
/**
 * Settings Management
 */
require_once __DIR__ . '/../config/config.php';
requireLogin();

$group = $_GET['group'] ?? 'general';
$page_title = 'ตั้งค่า';
$current_page = 'settings-' . $group;
$message = '';
$error = '';

// Group labels
$groups = [
    'general' => '⚙️ ทั่วไป',
    'colors' => '🎨 สี',
    'fonts' => '🔤 ฟอนต์',
    'hero' => '🦸 Hero Section',
    'curriculum' => '📚 หลักสูตร',
    'pricing' => '💰 ราคา',
    'contact' => '📍 ติดต่อ & แผนที่',
    'footer' => '📝 Footer'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDB();
    
    foreach ($_POST['settings'] ?? [] as $key => $value) {
        updateSetting($key, $value);
    }
    
    // Handle file uploads
    if (!empty($_FILES['images']['name'])) {
        foreach ($_FILES['images']['name'] as $key => $filename) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if (in_array($ext, ALLOWED_EXTENSIONS)) {
                    $newname = $key . '_' . time() . '.' . $ext;
                    $target = UPLOAD_PATH . $newname;
                    
                    if (!is_dir(UPLOAD_PATH)) {
                        mkdir(UPLOAD_PATH, 0777, true);
                    }
                    
                    if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target)) {
                        updateSetting($key, SITE_URL . '/uploads/' . $newname);
                    }
                }
            }
        }
    }
    
    $message = 'บันทึกการตั้งค่าเรียบร้อยแล้ว!';
}

// Get settings for this group
$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM settings WHERE setting_group = ? ORDER BY id");
$stmt->execute([$group]);
$settings = $stmt->fetchAll();
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
            <h1><?= $groups[$group] ?? 'ตั้งค่า' ?></h1>
            <p class="page-subtitle">จัดการการตั้งค่าเว็บไซต์</p>
            
            <!-- Tabs -->
            <div class="tabs">
                <?php foreach ($groups as $g => $label): ?>
                <a href="?group=<?= $g ?>" class="tab <?= $group === $g ? 'active' : '' ?>">
                    <?= $label ?>
                </a>
                <?php endforeach; ?>
            </div>
            
            <?php if ($message): ?>
            <div class="alert alert-success"><?= e($message) ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="settings-form">
                <?php foreach ($settings as $setting): ?>
                <div class="form-group">
                    <label><?= e($setting['setting_label']) ?></label>
                    
                    <?php if ($setting['setting_type'] === 'text'): ?>
                    <input type="text" name="settings[<?= e($setting['setting_key']) ?>]" value="<?= e($setting['setting_value']) ?>">
                    
                    <?php elseif ($setting['setting_type'] === 'textarea'): ?>
                    <textarea name="settings[<?= e($setting['setting_key']) ?>]"><?= e($setting['setting_value']) ?></textarea>
                    
                    <?php elseif ($setting['setting_type'] === 'number'): ?>
                    <input type="number" name="settings[<?= e($setting['setting_key']) ?>]" value="<?= e($setting['setting_value']) ?>">
                    
                    <?php elseif ($setting['setting_type'] === 'color'): ?>
                    <div class="color-input-wrapper">
                        <input type="color" value="<?= e($setting['setting_value']) ?>" onchange="this.nextElementSibling.value=this.value">
                        <input type="text" name="settings[<?= e($setting['setting_key']) ?>]" value="<?= e($setting['setting_value']) ?>" onchange="this.previousElementSibling.value=this.value">
                    </div>
                    
                    <?php elseif ($setting['setting_type'] === 'image'): ?>
                    <?php if ($setting['setting_value']): ?>
                    <img src="<?= e($setting['setting_value']) ?>" class="image-preview"><br>
                    <?php endif; ?>
                    <input type="file" name="images[<?= e($setting['setting_key']) ?>]" accept="image/*">
                    <input type="hidden" name="settings[<?= e($setting['setting_key']) ?>]" value="<?= e($setting['setting_value']) ?>">
                    <small style="color: var(--text-secondary);">หรือใส่ URL:</small>
                    <input type="url" name="settings[<?= e($setting['setting_key']) ?>]" value="<?= e($setting['setting_value']) ?>" style="margin-top: 0.5rem;">
                    
                    <?php elseif ($setting['setting_type'] === 'select'): ?>
                    <select name="settings[<?= e($setting['setting_key']) ?>]">
                        <?php foreach (explode(',', $setting['setting_options']) as $opt): ?>
                        <option value="<?= e(trim($opt)) ?>" <?= $setting['setting_value'] === trim($opt) ? 'selected' : '' ?>>
                            <?= e(trim($opt)) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                
                <button type="submit" class="btn-submit">💾 บันทึกการตั้งค่า</button>
            </form>
        </div>
    </main>
</body>
</html>

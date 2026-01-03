<?php
/**
 * SEO Settings Management
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/security.php';
requireLogin();
setSecurityHeaders();

$page_title = 'ตั้งค่า SEO';
$current_page = 'seo';
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF();
    
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
    
    $message = 'บันทึกการตั้งค่า SEO เรียบร้อยแล้ว!';
}

// Get SEO settings
$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM settings WHERE setting_group = 'seo' ORDER BY id");
$stmt->execute();
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
            <h1>🔍 ตั้งค่า SEO</h1>
            <p class="page-subtitle">จัดการ Meta Tags, Schema.org, และการตั้งค่า SEO</p>
            
            <?php if ($message): ?>
            <div class="alert alert-success"><?= e($message) ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="settings-form">
                <?= csrfField() ?>
                
                <!-- Basic SEO -->
                <div style="background: rgba(99,102,241,0.1); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem;">📝 Meta Tags พื้นฐาน</h3>
                    
                    <?php foreach ($settings as $setting): ?>
                    <?php if (strpos($setting['setting_key'], 'seo_') === 0 && strpos($setting['setting_key'], 'schema_') === false): ?>
                    <div class="form-group">
                        <label><?= e($setting['setting_label']) ?></label>
                        
                        <?php if ($setting['setting_type'] === 'text'): ?>
                        <input type="text" name="settings[<?= e($setting['setting_key']) ?>]" value="<?= e($setting['setting_value']) ?>">
                        <?php if ($setting['setting_key'] === 'seo_title'): ?>
                        <small style="color: var(--text-secondary);">ความยาว: <span id="titleCount"><?= strlen($setting['setting_value']) ?></span>/60 ตัวอักษร</small>
                        <?php elseif ($setting['setting_key'] === 'seo_description'): ?>
                        <small style="color: var(--text-secondary);">ความยาว: <span id="descCount"><?= strlen($setting['setting_value']) ?></span>/160 ตัวอักษร</small>
                        <?php endif; ?>
                        
                        <?php elseif ($setting['setting_type'] === 'textarea'): ?>
                        <textarea name="settings[<?= e($setting['setting_key']) ?>]"><?= e($setting['setting_value']) ?></textarea>
                        
                        <?php elseif ($setting['setting_type'] === 'image'): ?>
                        <?php if ($setting['setting_value']): ?>
                        <img src="<?= e($setting['setting_value']) ?>" class="image-preview"><br>
                        <?php endif; ?>
                        <input type="file" name="images[<?= e($setting['setting_key']) ?>]" accept="image/*">
                        <input type="hidden" name="settings[<?= e($setting['setting_key']) ?>]" value="<?= e($setting['setting_value']) ?>">
                        
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
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <!-- Schema.org -->
                <div style="background: rgba(14,165,233,0.1); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem;">🏷️ Schema.org (Structured Data)</h3>
                    <p style="color: var(--text-secondary); margin-bottom: 1rem; font-size: 0.875rem;">
                        ข้อมูลเชิงโครงสร้างช่วยให้ Google และ AI Search เข้าใจเนื้อหาเว็บไซต์ได้ดีขึ้น
                    </p>
                    
                    <?php foreach ($settings as $setting): ?>
                    <?php if (strpos($setting['setting_key'], 'schema_') === 0): ?>
                    <div class="form-group">
                        <label><?= e($setting['setting_label']) ?></label>
                        
                        <?php if ($setting['setting_type'] === 'text'): ?>
                        <input type="text" name="settings[<?= e($setting['setting_key']) ?>]" value="<?= e($setting['setting_value']) ?>">
                        
                        <?php elseif ($setting['setting_type'] === 'number'): ?>
                        <input type="number" name="settings[<?= e($setting['setting_key']) ?>]" value="<?= e($setting['setting_value']) ?>">
                        
                        <?php elseif ($setting['setting_type'] === 'image'): ?>
                        <?php if ($setting['setting_value']): ?>
                        <img src="<?= e($setting['setting_value']) ?>" class="image-preview"><br>
                        <?php endif; ?>
                        <input type="file" name="images[<?= e($setting['setting_key']) ?>]" accept="image/*">
                        <input type="hidden" name="settings[<?= e($setting['setting_key']) ?>]" value="<?= e($setting['setting_value']) ?>">
                        
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
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <!-- SEO Preview -->
                <div style="background: var(--bg-card); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem;">👁️ SEO Preview (Google Search)</h3>
                    <div style="background: #fff; padding: 1rem; border-radius: 8px; color: #333;">
                        <div style="color: #1a0dab; font-size: 1.25rem; margin-bottom: 0.25rem;" id="previewTitle">
                            <?= e(getSetting('seo_title', getSetting('site_name'))) ?>
                        </div>
                        <div style="color: #006621; font-size: 0.875rem; margin-bottom: 0.25rem;">
                            <?= e(SITE_URL) ?>
                        </div>
                        <div style="color: #545454; font-size: 0.875rem;" id="previewDesc">
                            <?= e(getSetting('seo_description', getSetting('site_description'))) ?>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">💾 บันทึกการตั้งค่า SEO</button>
            </form>
        </div>
    </main>
    
    <script>
    // Character counters
    document.querySelector('input[name="settings[seo_title]"]')?.addEventListener('input', function() {
        document.getElementById('titleCount').textContent = this.value.length;
        document.getElementById('previewTitle').textContent = this.value;
    });
    document.querySelector('textarea[name="settings[seo_description]"]')?.addEventListener('input', function() {
        document.getElementById('descCount').textContent = this.value.length;
        document.getElementById('previewDesc').textContent = this.value;
    });
    </script>
</body>
</html>

<?php
/**
 * Theme Management
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/security.php';
requireLogin();
setSecurityHeaders();

$page_title = 'จัดการธีม';
$current_page = 'themes';
$message = '';
$pdo = getDB();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF();
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'apply_preset') {
        // Get preset colors
        $stmt = $pdo->prepare("SELECT * FROM theme_presets WHERE name = ?");
        $stmt->execute([$_POST['preset']]);
        $preset = $stmt->fetch();
        
        if ($preset) {
            updateSetting('color_primary', $preset['primary_color']);
            updateSetting('color_secondary', $preset['secondary_color']);
            updateSetting('color_accent', $preset['accent_color']);
            updateSetting('color_bg_dark', $preset['bg_dark']);
            updateSetting('color_bg_card', $preset['bg_card']);
            updateSetting('color_text', $preset['text_color']);
            updateSetting('theme_preset', $preset['name']);
            $message = 'ใช้ธีม "' . $preset['label'] . '" เรียบร้อยแล้ว!';
        }
    } elseif ($action === 'save_custom') {
        updateSetting('color_primary', $_POST['color_primary']);
        updateSetting('color_secondary', $_POST['color_secondary']);
        updateSetting('color_accent', $_POST['color_accent']);
        updateSetting('color_bg_dark', $_POST['color_bg_dark']);
        updateSetting('color_bg_card', $_POST['color_bg_card']);
        updateSetting('color_text', $_POST['color_text']);
        updateSetting('theme_preset', 'custom');
        $message = 'บันทึกธีมกำหนดเองเรียบร้อย!';
    }
}

// Get presets
$presets = $pdo->query("SELECT * FROM theme_presets ORDER BY id")->fetchAll();
$currentPreset = getSetting('theme_preset', 'dark-purple');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
    <style>
        .theme-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .theme-card {
            background: var(--bg-card);
            border-radius: 12px;
            overflow: hidden;
            border: 3px solid transparent;
            transition: all 0.3s;
            cursor: pointer;
        }
        .theme-card:hover { border-color: rgba(99,102,241,0.5); }
        .theme-card.active { border-color: var(--primary); }
        .theme-preview {
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 600;
        }
        .theme-info { padding: 1rem; }
        .theme-info h4 { margin-bottom: 0.5rem; }
        .theme-colors { display: flex; gap: 0.5rem; }
        .color-dot { width: 24px; height: 24px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.2); }
        .custom-colors { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <h1>🎨 จัดการธีม</h1>
            <p class="page-subtitle">เลือกธีมสำเร็จรูปหรือกำหนดสีเอง</p>
            
            <?php if ($message): ?>
            <div class="alert alert-success"><?= e($message) ?></div>
            <?php endif; ?>
            
            <!-- Theme Presets -->
            <h3 style="margin-bottom: 1rem;">🎭 ธีมสำเร็จรูป</h3>
            <div class="theme-grid">
                <?php foreach ($presets as $preset): ?>
                <form method="POST" style="margin: 0;">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="apply_preset">
                    <input type="hidden" name="preset" value="<?= e($preset['name']) ?>">
                    <button type="submit" class="theme-card <?= $currentPreset === $preset['name'] ? 'active' : '' ?>" style="width: 100%; text-align: left; border: none; font-family: inherit;">
                        <div class="theme-preview" style="background: <?= e($preset['bg_dark']) ?>; color: <?= e($preset['text_color']) ?>;">
                            <span style="color: <?= e($preset['primary_color']) ?>;">Learn</span>Pro
                        </div>
                        <div class="theme-info">
                            <h4><?= e($preset['label']) ?></h4>
                            <div class="theme-colors">
                                <span class="color-dot" style="background: <?= e($preset['primary_color']) ?>;"></span>
                                <span class="color-dot" style="background: <?= e($preset['secondary_color']) ?>;"></span>
                                <span class="color-dot" style="background: <?= e($preset['accent_color']) ?>;"></span>
                                <span class="color-dot" style="background: <?= e($preset['bg_dark']) ?>;"></span>
                            </div>
                        </div>
                    </button>
                </form>
                <?php endforeach; ?>
            </div>
            
            <!-- Custom Colors -->
            <form method="POST" class="settings-form">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="save_custom">
                
                <h3 style="margin-bottom: 1rem;">🖌️ กำหนดสีเอง</h3>
                
                <div class="custom-colors">
                    <div class="form-group">
                        <label>สีหลัก (Primary)</label>
                        <div class="color-input-wrapper">
                            <input type="color" value="<?= e(getSetting('color_primary', '#6366f1')) ?>" onchange="this.nextElementSibling.value=this.value">
                            <input type="text" name="color_primary" value="<?= e(getSetting('color_primary', '#6366f1')) ?>" onchange="this.previousElementSibling.value=this.value">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>สีรอง (Secondary)</label>
                        <div class="color-input-wrapper">
                            <input type="color" value="<?= e(getSetting('color_secondary', '#0ea5e9')) ?>" onchange="this.nextElementSibling.value=this.value">
                            <input type="text" name="color_secondary" value="<?= e(getSetting('color_secondary', '#0ea5e9')) ?>" onchange="this.previousElementSibling.value=this.value">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>สีเน้น (Accent)</label>
                        <div class="color-input-wrapper">
                            <input type="color" value="<?= e(getSetting('color_accent', '#f59e0b')) ?>" onchange="this.nextElementSibling.value=this.value">
                            <input type="text" name="color_accent" value="<?= e(getSetting('color_accent', '#f59e0b')) ?>" onchange="this.previousElementSibling.value=this.value">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>พื้นหลัง</label>
                        <div class="color-input-wrapper">
                            <input type="color" value="<?= e(getSetting('color_bg_dark', '#0f172a')) ?>" onchange="this.nextElementSibling.value=this.value">
                            <input type="text" name="color_bg_dark" value="<?= e(getSetting('color_bg_dark', '#0f172a')) ?>" onchange="this.previousElementSibling.value=this.value">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>สีการ์ด</label>
                        <div class="color-input-wrapper">
                            <input type="color" value="<?= e(getSetting('color_bg_card', '#1e293b')) ?>" onchange="this.nextElementSibling.value=this.value">
                            <input type="text" name="color_bg_card" value="<?= e(getSetting('color_bg_card', '#1e293b')) ?>" onchange="this.previousElementSibling.value=this.value">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>สีข้อความ</label>
                        <div class="color-input-wrapper">
                            <input type="color" value="<?= e(getSetting('color_text', '#f8fafc')) ?>" onchange="this.nextElementSibling.value=this.value">
                            <input type="text" name="color_text" value="<?= e(getSetting('color_text', '#f8fafc')) ?>" onchange="this.previousElementSibling.value=this.value">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit" style="margin-top: 1rem;">💾 บันทึกสีกำหนดเอง</button>
            </form>
        </div>
    </main>
</body>
</html>

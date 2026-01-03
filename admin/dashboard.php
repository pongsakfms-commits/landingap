<?php
/**
 * Admin Dashboard
 */
require_once __DIR__ . '/../config/config.php';
requireLogin();

$page_title = 'Dashboard';
$current_page = 'dashboard';

// Get stats
$pdo = getDB();
$stats = [
    'courses' => $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn(),
    'packages' => $pdo->query("SELECT COUNT(*) FROM pricing_packages")->fetchColumn(),
    'settings' => $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn()
];
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
            <h1>📊 Dashboard</h1>
            <p class="page-subtitle">ยินดีต้อนรับสู่ระบบจัดการ LearnPro Academy</p>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📚</div>
                    <div class="stat-info">
                        <span class="stat-number"><?= $stats['courses'] ?></span>
                        <span class="stat-label">คอร์สเรียน</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-info">
                        <span class="stat-number"><?= $stats['packages'] ?></span>
                        <span class="stat-label">แพ็กเกจ</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⚙️</div>
                    <div class="stat-info">
                        <span class="stat-number"><?= $stats['settings'] ?></span>
                        <span class="stat-label">ตั้งค่า</span>
                    </div>
                </div>
            </div>
            
            <div class="quick-actions">
                <h2>🚀 การดำเนินการด่วน</h2>
                <div class="action-grid">
                    <a href="settings.php?group=hero" class="action-card">
                        <span class="action-icon">🎨</span>
                        <span>แก้ไข Hero Section</span>
                    </a>
                    <a href="settings.php?group=colors" class="action-card">
                        <span class="action-icon">🌈</span>
                        <span>ปรับสีเว็บไซต์</span>
                    </a>
                    <a href="courses.php" class="action-card">
                        <span class="action-icon">📖</span>
                        <span>จัดการคอร์ส</span>
                    </a>
                    <a href="pricing.php" class="action-card">
                        <span class="action-icon">💳</span>
                        <span>จัดการราคา</span>
                    </a>
                    <a href="settings.php?group=contact" class="action-card">
                        <span class="action-icon">📍</span>
                        <span>แก้ไขแผนที่</span>
                    </a>
                    <a href="<?= SITE_URL ?>" target="_blank" class="action-card">
                        <span class="action-icon">👁️</span>
                        <span>ดูเว็บไซต์</span>
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

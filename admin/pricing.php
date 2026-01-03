<?php
/**
 * Pricing Packages Management
 */
require_once __DIR__ . '/../config/config.php';
requireLogin();

$page_title = 'จัดการแพ็กเกจ';
$current_page = 'pricing';
$message = '';
$pdo = getDB();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_package') {
        $stmt = $pdo->prepare("INSERT INTO pricing_packages (name, price, period, is_featured, badge, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['price'],
            $_POST['period'],
            isset($_POST['is_featured']) ? 1 : 0,
            $_POST['badge'] ?: null,
            (int)$_POST['sort_order']
        ]);
        $message = 'เพิ่มแพ็กเกจเรียบร้อย!';
    } elseif ($action === 'edit_package') {
        $stmt = $pdo->prepare("UPDATE pricing_packages SET name=?, price=?, period=?, is_featured=?, badge=?, sort_order=?, is_active=? WHERE id=?");
        $stmt->execute([
            $_POST['name'],
            $_POST['price'],
            $_POST['period'],
            isset($_POST['is_featured']) ? 1 : 0,
            $_POST['badge'] ?: null,
            (int)$_POST['sort_order'],
            isset($_POST['is_active']) ? 1 : 0,
            $_POST['id']
        ]);
        $message = 'อัพเดทแพ็กเกจเรียบร้อย!';
    } elseif ($action === 'delete_package') {
        $stmt = $pdo->prepare("DELETE FROM pricing_packages WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $message = 'ลบแพ็กเกจเรียบร้อย!';
    } elseif ($action === 'update_features') {
        // Clear existing mappings
        $pdo->exec("DELETE FROM package_features");
        
        // Insert new mappings
        $stmt = $pdo->prepare("INSERT INTO package_features (package_id, feature_id, value, is_included) VALUES (?, ?, ?, ?)");
        foreach ($_POST['features'] ?? [] as $pkg_id => $features) {
            foreach ($features as $feat_id => $data) {
                $stmt->execute([
                    $pkg_id,
                    $feat_id,
                    $data['value'] ?? '✓',
                    isset($data['included']) ? 1 : 0
                ]);
            }
        }
        $message = 'อัพเดทฟีเจอร์เรียบร้อย!';
    }
}

// Get data
$packages = $pdo->query("SELECT * FROM pricing_packages ORDER BY sort_order")->fetchAll();
$features = $pdo->query("SELECT * FROM pricing_features ORDER BY sort_order")->fetchAll();
$package_features = $pdo->query("SELECT * FROM package_features")->fetchAll();

// Build feature map
$feature_map = [];
foreach ($package_features as $pf) {
    $feature_map[$pf['package_id']][$pf['feature_id']] = $pf;
}

// Edit mode
$edit_pkg = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM pricing_packages WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_pkg = $stmt->fetch();
}
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
            <h1>💳 จัดการแพ็กเกจราคา</h1>
            <p class="page-subtitle">จัดการแพ็กเกจและฟีเจอร์</p>
            
            <?php if ($message): ?>
            <div class="alert alert-success"><?= e($message) ?></div>
            <?php endif; ?>
            
            <!-- Add/Edit Package Form -->
            <form method="POST" class="settings-form" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem;"><?= $edit_pkg ? '✏️ แก้ไขแพ็กเกจ' : '➕ เพิ่มแพ็กเกจใหม่' ?></h3>
                
                <input type="hidden" name="action" value="<?= $edit_pkg ? 'edit_package' : 'add_package' ?>">
                <?php if ($edit_pkg): ?>
                <input type="hidden" name="id" value="<?= $edit_pkg['id'] ?>">
                <?php endif; ?>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label>ชื่อแพ็กเกจ</label>
                        <input type="text" name="name" value="<?= e($edit_pkg['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>ราคา (บาท)</label>
                        <input type="number" name="price" value="<?= e($edit_pkg['price'] ?? '') ?>" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>ต่อ</label>
                        <input type="text" name="period" value="<?= e($edit_pkg['period'] ?? '/เดือน') ?>">
                    </div>
                    <div class="form-group">
                        <label>Badge</label>
                        <input type="text" name="badge" value="<?= e($edit_pkg['badge'] ?? '') ?>" placeholder="เช่น แนะนำ">
                    </div>
                    <div class="form-group">
                        <label>ลำดับ</label>
                        <input type="number" name="sort_order" value="<?= e($edit_pkg['sort_order'] ?? 0) ?>">
                    </div>
                </div>
                
                <div class="form-group" style="display: flex; gap: 2rem;">
                    <label><input type="checkbox" name="is_featured" <?= ($edit_pkg['is_featured'] ?? false) ? 'checked' : '' ?>> แนะนำ (Highlight)</label>
                    <?php if ($edit_pkg): ?>
                    <label><input type="checkbox" name="is_active" <?= $edit_pkg['is_active'] ? 'checked' : '' ?>> เปิดใช้งาน</label>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn-submit"><?= $edit_pkg ? '💾 บันทึก' : '➕ เพิ่มแพ็กเกจ' ?></button>
                <?php if ($edit_pkg): ?>
                <a href="pricing.php" style="margin-left: 1rem; color: var(--text-secondary);">ยกเลิก</a>
                <?php endif; ?>
            </form>
            
            <!-- Packages Table -->
            <table class="data-table" style="margin-bottom: 2rem;">
                <thead>
                    <tr>
                        <th>ชื่อ</th>
                        <th>ราคา</th>
                        <th>Badge</th>
                        <th>แนะนำ</th>
                        <th>สถานะ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($packages as $pkg): ?>
                    <tr>
                        <td><strong><?= e($pkg['name']) ?></strong></td>
                        <td>฿<?= number_format($pkg['price']) ?><?= e($pkg['period']) ?></td>
                        <td><?= $pkg['badge'] ?: '-' ?></td>
                        <td><?= $pkg['is_featured'] ? '⭐' : '-' ?></td>
                        <td><?= $pkg['is_active'] ? '✅' : '❌' ?></td>
                        <td>
                            <a href="?edit=<?= $pkg['id'] ?>" class="btn-sm btn-edit">แก้ไข</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('ยืนยัน?')">
                                <input type="hidden" name="action" value="delete_package">
                                <input type="hidden" name="id" value="<?= $pkg['id'] ?>">
                                <button type="submit" class="btn-sm btn-delete">ลบ</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Feature Matrix -->
            <form method="POST" class="settings-form">
                <h3 style="margin-bottom: 1rem;">📋 ตารางฟีเจอร์</h3>
                <input type="hidden" name="action" value="update_features">
                
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ฟีเจอร์</th>
                                <?php foreach ($packages as $pkg): ?>
                                <th style="text-align: center;"><?= e($pkg['name']) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($features as $feat): ?>
                            <tr>
                                <td><?= e($feat['feature_name']) ?></td>
                                <?php foreach ($packages as $pkg): ?>
                                <?php $pf = $feature_map[$pkg['id']][$feat['id']] ?? []; ?>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="features[<?= $pkg['id'] ?>][<?= $feat['id'] ?>][included]" <?= ($pf['is_included'] ?? false) ? 'checked' : '' ?>>
                                    <input type="text" name="features[<?= $pkg['id'] ?>][<?= $feat['id'] ?>][value]" value="<?= e($pf['value'] ?? '✓') ?>" style="width: 80px; padding: 0.25rem; margin-left: 0.5rem;">
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <button type="submit" class="btn-submit" style="margin-top: 1rem;">💾 บันทึกฟีเจอร์</button>
            </form>
        </div>
    </main>
</body>
</html>

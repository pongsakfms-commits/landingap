<?php
/**
 * Courses Management
 */
require_once __DIR__ . '/../config/config.php';
requireLogin();

$page_title = 'จัดการคอร์ส';
$current_page = 'courses';
$message = '';
$error = '';
$pdo = getDB();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO courses (category, icon, title, duration, badge, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['category'],
            $_POST['icon'],
            $_POST['title'],
            $_POST['duration'],
            $_POST['badge'] ?: null,
            (int)$_POST['sort_order']
        ]);
        $message = 'เพิ่มคอร์สเรียบร้อยแล้ว!';
    } elseif ($action === 'edit') {
        $stmt = $pdo->prepare("UPDATE courses SET category=?, icon=?, title=?, duration=?, badge=?, sort_order=?, is_active=? WHERE id=?");
        $stmt->execute([
            $_POST['category'],
            $_POST['icon'],
            $_POST['title'],
            $_POST['duration'],
            $_POST['badge'] ?: null,
            (int)$_POST['sort_order'],
            isset($_POST['is_active']) ? 1 : 0,
            $_POST['id']
        ]);
        $message = 'อัพเดทคอร์สเรียบร้อยแล้ว!';
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $message = 'ลบคอร์สเรียบร้อยแล้ว!';
    }
}

// Get all courses
$courses = $pdo->query("SELECT * FROM courses ORDER BY category, sort_order")->fetchAll();
$categories = $pdo->query("SELECT DISTINCT category FROM courses")->fetchAll(PDO::FETCH_COLUMN);

// Edit mode
$edit_course = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_course = $stmt->fetch();
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
            <h1>📖 จัดการคอร์สเรียน</h1>
            <p class="page-subtitle">เพิ่ม แก้ไข ลบคอร์สเรียนในหลักสูตร</p>
            
            <?php if ($message): ?>
            <div class="alert alert-success"><?= e($message) ?></div>
            <?php endif; ?>
            
            <!-- Add/Edit Form -->
            <form method="POST" class="settings-form" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem;"><?= $edit_course ? '✏️ แก้ไขคอร์ส' : '➕ เพิ่มคอร์สใหม่' ?></h3>
                
                <input type="hidden" name="action" value="<?= $edit_course ? 'edit' : 'add' ?>">
                <?php if ($edit_course): ?>
                <input type="hidden" name="id" value="<?= $edit_course['id'] ?>">
                <?php endif; ?>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label>หมวดหมู่</label>
                        <input type="text" name="category" value="<?= e($edit_course['category'] ?? '') ?>" required list="categories">
                        <datalist id="categories">
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= e($cat) ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="form-group">
                        <label>ไอคอน (Emoji)</label>
                        <input type="text" name="icon" value="<?= e($edit_course['icon'] ?? '📚') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>ชื่อคอร์ส</label>
                        <input type="text" name="title" value="<?= e($edit_course['title'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>ระยะเวลา</label>
                        <input type="text" name="duration" value="<?= e($edit_course['duration'] ?? '') ?>" placeholder="เช่น 12 ชั่วโมง">
                    </div>
                    <div class="form-group">
                        <label>Badge</label>
                        <input type="text" name="badge" value="<?= e($edit_course['badge'] ?? '') ?>" placeholder="เช่น ยอดนิยม, ใหม่">
                    </div>
                    <div class="form-group">
                        <label>ลำดับ</label>
                        <input type="number" name="sort_order" value="<?= e($edit_course['sort_order'] ?? 0) ?>">
                    </div>
                </div>
                
                <?php if ($edit_course): ?>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" <?= $edit_course['is_active'] ? 'checked' : '' ?>>
                        เปิดใช้งาน
                    </label>
                </div>
                <?php endif; ?>
                
                <button type="submit" class="btn-submit"><?= $edit_course ? '💾 บันทึก' : '➕ เพิ่มคอร์ส' ?></button>
                <?php if ($edit_course): ?>
                <a href="courses.php" style="margin-left: 1rem; color: var(--text-secondary);">ยกเลิก</a>
                <?php endif; ?>
            </form>
            
            <!-- Courses Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ไอคอน</th>
                        <th>หมวดหมู่</th>
                        <th>ชื่อคอร์ส</th>
                        <th>ระยะเวลา</th>
                        <th>Badge</th>
                        <th>สถานะ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?= e($course['icon']) ?></td>
                        <td><?= e($course['category']) ?></td>
                        <td><?= e($course['title']) ?></td>
                        <td><?= e($course['duration']) ?></td>
                        <td><?= $course['badge'] ? '<span style="background: var(--warning); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">' . e($course['badge']) . '</span>' : '-' ?></td>
                        <td><?= $course['is_active'] ? '✅' : '❌' ?></td>
                        <td>
                            <a href="?edit=<?= $course['id'] ?>" class="btn-sm btn-edit">แก้ไข</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('ยืนยันการลบ?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $course['id'] ?>">
                                <button type="submit" class="btn-sm btn-delete">ลบ</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>

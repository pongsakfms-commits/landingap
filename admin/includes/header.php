<!-- Header -->
<header class="top-header">
    <button class="toggle-sidebar" onclick="document.body.classList.toggle('sidebar-collapsed')">☰</button>
    <h2 class="header-title"><?= $page_title ?? 'Admin' ?></h2>
    <div class="header-actions">
        <span class="admin-name">👤 <?= e($_SESSION['admin_username'] ?? 'Admin') ?></span>
        <a href="logout.php" class="logout-btn">ออกจากระบบ</a>
    </div>
</header>

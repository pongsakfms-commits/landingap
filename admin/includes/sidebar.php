<!-- Sidebar Navigation -->
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="dashboard.php" class="sidebar-logo">Learn<span>Pro</span></a>
        <span class="sidebar-badge">Admin</span>
    </div>
    
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?= ($current_page ?? '') === 'dashboard' ? 'active' : '' ?>">
            <span class="nav-icon">📊</span>
            <span>Dashboard</span>
        </a>
        
        <div class="nav-section">การตั้งค่าเว็บไซต์</div>
        
        <a href="settings.php?group=general" class="nav-item <?= ($current_page ?? '') === 'settings-general' ? 'active' : '' ?>">
            <span class="nav-icon">⚙️</span>
            <span>ทั่วไป</span>
        </a>
        <a href="settings.php?group=colors" class="nav-item <?= ($current_page ?? '') === 'settings-colors' ? 'active' : '' ?>">
            <span class="nav-icon">🎨</span>
            <span>สี</span>
        </a>
        <a href="settings.php?group=fonts" class="nav-item <?= ($current_page ?? '') === 'settings-fonts' ? 'active' : '' ?>">
            <span class="nav-icon">🔤</span>
            <span>ฟอนต์</span>
        </a>
        <a href="settings.php?group=hero" class="nav-item <?= ($current_page ?? '') === 'settings-hero' ? 'active' : '' ?>">
            <span class="nav-icon">🦸</span>
            <span>Hero Section</span>
        </a>
        
        <div class="nav-section">เนื้อหา</div>
        
        <a href="settings.php?group=curriculum" class="nav-item <?= ($current_page ?? '') === 'settings-curriculum' ? 'active' : '' ?>">
            <span class="nav-icon">📚</span>
            <span>หลักสูตร</span>
        </a>
        <a href="courses.php" class="nav-item <?= ($current_page ?? '') === 'courses' ? 'active' : '' ?>">
            <span class="nav-icon">📖</span>
            <span>จัดการคอร์ส</span>
        </a>
        <a href="settings.php?group=pricing" class="nav-item <?= ($current_page ?? '') === 'settings-pricing' ? 'active' : '' ?>">
            <span class="nav-icon">💰</span>
            <span>ราคา</span>
        </a>
        <a href="pricing.php" class="nav-item <?= ($current_page ?? '') === 'pricing' ? 'active' : '' ?>">
            <span class="nav-icon">💳</span>
            <span>จัดการแพ็กเกจ</span>
        </a>
        
        <div class="nav-section">ติดต่อ</div>
        
        <a href="settings.php?group=contact" class="nav-item <?= ($current_page ?? '') === 'settings-contact' ? 'active' : '' ?>">
            <span class="nav-icon">📍</span>
            <span>ข้อมูลติดต่อ & แผนที่</span>
        </a>
        <a href="settings.php?group=footer" class="nav-item <?= ($current_page ?? '') === 'settings-footer' ? 'active' : '' ?>">
            <span class="nav-icon">📝</span>
            <span>Footer & Social</span>
        </a>
        
        <div class="nav-section">ระบบ</div>
        
        <a href="themes.php" class="nav-item <?= ($current_page ?? '') === 'themes' ? 'active' : '' ?>">
            <span class="nav-icon">🎨</span>
            <span>จัดการธีม</span>
        </a>
        <a href="seo.php" class="nav-item <?= ($current_page ?? '') === 'seo' ? 'active' : '' ?>">
            <span class="nav-icon">🔍</span>
            <span>ตั้งค่า SEO</span>
        </a>
        <a href="system.php" class="nav-item <?= ($current_page ?? '') === 'system' ? 'active' : '' ?>">
            <span class="nav-icon">🔧</span>
            <span>ตั้งค่าระบบ</span>
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <a href="<?= SITE_URL ?>" target="_blank" class="view-site">
            <span>👁️</span> ดูเว็บไซต์
        </a>
    </div>
</aside>

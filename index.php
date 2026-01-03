<?php
/**
 * LearnPro Academy - Main Landing Page
 * Dynamic content loaded from database
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/security.php';
setSecurityHeaders();

// Get all settings
$pdo = getDB();

// Get courses grouped by category
$courses = $pdo->query("SELECT * FROM courses WHERE is_active = 1 ORDER BY category, sort_order")->fetchAll();
$coursesByCategory = [];
foreach ($courses as $course) {
    $coursesByCategory[$course['category']][] = $course;
}

// Get pricing packages with features
$packages = $pdo->query("SELECT * FROM pricing_packages WHERE is_active = 1 ORDER BY sort_order")->fetchAll();
$features = $pdo->query("SELECT * FROM pricing_features ORDER BY sort_order")->fetchAll();
$packageFeatures = $pdo->query("SELECT * FROM package_features")->fetchAll();
$featureMap = [];
foreach ($packageFeatures as $pf) {
    $featureMap[$pf['package_id']][$pf['feature_id']] = $pf;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e(getSetting('site_description')) ?>">
    <title><?= e(getSetting('site_name')) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=<?= urlencode(getSetting('font_family', 'Noto Sans Thai')) ?>:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php if (MAP_PROVIDER === 'openstreetmap'): ?>
    <!-- Leaflet Map - CDN with local fallback -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          onerror="this.href='assets/leaflet/leaflet.css'">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>window.L || document.write('<script src="assets/leaflet/leaflet.js"><\/script>')</script>
    <?php elseif (MAP_PROVIDER === 'google' && GOOGLE_MAPS_API_KEY): ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY ?>"></script>
    <?php endif; ?>
    <style>
        :root {
            --primary: <?= e(getSetting('color_primary', '#6366f1')) ?>;
            --secondary: <?= e(getSetting('color_secondary', '#0ea5e9')) ?>;
            --accent: <?= e(getSetting('color_accent', '#f59e0b')) ?>;
            --bg-dark: <?= e(getSetting('color_bg_dark', '#0f172a')) ?>;
            --bg-card: <?= e(getSetting('color_bg_card', '#1e293b')) ?>;
            --text-primary: <?= e(getSetting('color_text', '#f8fafc')) ?>;
            --text-secondary: #94a3b8;
            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            --shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            --radius: 16px;
            --font-family: '<?= e(getSetting('font_family', 'Noto Sans Thai')) ?>', sans-serif;
            --font-size-base: <?= e(getSetting('font_size_base', '16')) ?>px;
        }
    </style>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="#" class="logo"><?= e(getSetting('site_name', 'LearnPro')) ?></a>
            <button class="hamburger" id="hamburger" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
            <ul class="nav-menu" id="navMenu">
                <li><a href="#hero">หน้าแรก</a></li>
                <li><a href="#curriculum">หลักสูตร</a></li>
                <li><a href="#pricing">ราคา</a></li>
                <li><a href="#contact">ติดต่อ</a></li>
                <li><a href="#" class="btn-nav"><?= e(getSetting('hero_btn_primary', 'สมัครเรียน')) ?></a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1><?= e(getSetting('hero_title_1', 'เปลี่ยนอนาคตของคุณ')) ?><br>
                    <span class="gradient-text"><?= e(getSetting('hero_title_2', 'ด้วยทักษะใหม่')) ?></span>
                </h1>
                <p><?= e(getSetting('hero_subtitle')) ?></p>
                <div class="hero-buttons">
                    <a href="#pricing" class="btn-primary"><?= e(getSetting('hero_btn_primary', 'สมัครเรียนเลย')) ?></a>
                    <a href="#curriculum" class="btn-secondary"><?= e(getSetting('hero_btn_secondary', 'ดูหลักสูตร')) ?></a>
                </div>
                <div class="hero-stats">
                    <div class="stat">
                        <strong><?= e(getSetting('hero_stat_1_number', '10,000+')) ?></strong>
                        <span><?= e(getSetting('hero_stat_1_label', 'นักเรียน')) ?></span>
                    </div>
                    <div class="stat">
                        <strong><?= e(getSetting('hero_stat_2_number', '50+')) ?></strong>
                        <span><?= e(getSetting('hero_stat_2_label', 'คอร์ส')) ?></span>
                    </div>
                    <div class="stat">
                        <strong><?= e(getSetting('hero_stat_3_number', '98%')) ?></strong>
                        <span><?= e(getSetting('hero_stat_3_label', 'พึงพอใจ')) ?></span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <img src="<?= e(getSetting('hero_image')) ?>" alt="ผู้สอน">
                <div class="floating-card card-1">
                    <span class="icon">🎓</span>
                    <span>ใบรับรอง</span>
                </div>
                <div class="floating-card card-2">
                    <span class="icon">⭐</span>
                    <span>4.9 Rating</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Curriculum Section -->
    <section class="curriculum" id="curriculum">
        <div class="section-container">
            <h2 class="section-title"><?= e(getSetting('curriculum_title', 'หลักสูตรของเรา')) ?></h2>
            <p class="section-subtitle"><?= e(getSetting('curriculum_subtitle')) ?></p>
            
            <!-- Mobile: Accordion -->
            <div class="curriculum-accordion">
                <?php foreach ($coursesByCategory as $category => $categoryCourses): ?>
                <?php $firstCourse = $categoryCourses[0]; ?>
                <div class="accordion-item">
                    <button class="accordion-header">
                        <span><?= e($firstCourse['icon']) ?> <?= e($category) ?></span>
                        <span class="accordion-icon">+</span>
                    </button>
                    <div class="accordion-content">
                        <ul>
                            <?php foreach ($categoryCourses as $course): ?>
                            <li><?= e($course['title']) ?> - <?= e($course['duration']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Desktop: Grid -->
            <div class="curriculum-grid">
                <?php foreach ($coursesByCategory as $category => $categoryCourses): ?>
                <?php $firstCourse = $categoryCourses[0]; ?>
                <div class="course-card">
                    <div class="course-icon"><?= e($firstCourse['icon']) ?></div>
                    <h3><?= e($category) ?></h3>
                    <ul>
                        <?php foreach ($categoryCourses as $course): ?>
                        <li><?= e($course['title']) ?> - <?= e($course['duration']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ($firstCourse['badge']): ?>
                    <span class="course-badge"><?= e($firstCourse['badge']) ?></span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing" id="pricing">
        <div class="section-container">
            <h2 class="section-title"><?= e(getSetting('pricing_title', 'แพ็กเกจราคา')) ?></h2>
            <p class="section-subtitle"><?= e(getSetting('pricing_subtitle')) ?></p>

            <!-- Mobile: Cards -->
            <div class="pricing-cards">
                <?php foreach ($packages as $pkg): ?>
                <div class="price-card <?= $pkg['is_featured'] ? 'featured' : '' ?>">
                    <?php if ($pkg['badge']): ?>
                    <div class="badge"><?= e($pkg['badge']) ?></div>
                    <?php endif; ?>
                    <h3><?= e($pkg['name']) ?></h3>
                    <div class="price">฿<?= number_format($pkg['price']) ?><span><?= e($pkg['period']) ?></span></div>
                    <ul>
                        <?php foreach ($features as $feat): ?>
                        <?php $pf = $featureMap[$pkg['id']][$feat['id']] ?? null; ?>
                        <li><?= $pf && $pf['is_included'] ? '✓' : '✗' ?> <?= e($feat['feature_name']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button class="btn-card <?= $pkg['is_featured'] ? 'primary' : '' ?>">เลือกแพ็กเกจ</button>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Desktop: Table -->
            <div class="pricing-table-wrapper">
                <table class="pricing-table">
                    <thead>
                        <tr>
                            <th>Features</th>
                            <?php foreach ($packages as $pkg): ?>
                            <th class="<?= $pkg['is_featured'] ? 'highlight' : '' ?>">
                                <?= e($pkg['name']) ?><br>
                                <small>฿<?= number_format($pkg['price']) ?><?= e($pkg['period']) ?></small>
                            </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($features as $feat): ?>
                        <tr>
                            <td><?= e($feat['feature_name']) ?></td>
                            <?php foreach ($packages as $pkg): ?>
                            <?php $pf = $featureMap[$pkg['id']][$feat['id']] ?? null; ?>
                            <td class="<?= $pkg['is_featured'] ? 'highlight' : '' ?>">
                                <?= $pf ? e($pf['value']) : '✗' ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td></td>
                            <?php foreach ($packages as $pkg): ?>
                            <td class="<?= $pkg['is_featured'] ? 'highlight' : '' ?>">
                                <button class="btn-table <?= $pkg['is_featured'] ? 'primary' : '' ?>">เลือก</button>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Contact Section with Map -->
    <section class="contact" id="contact">
        <div class="section-container">
            <h2 class="section-title"><?= e(getSetting('contact_title', 'ติดต่อเรา')) ?></h2>
            
            <div class="contact-grid">
                <div class="contact-info">
                    <div class="contact-item">
                        <span class="contact-icon">📍</span>
                        <div>
                            <strong>ที่อยู่</strong>
                            <p><?= nl2br(e(getSetting('contact_address'))) ?></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <span class="contact-icon">📞</span>
                        <div>
                            <strong>โทรศัพท์</strong>
                            <p><?= e(getSetting('contact_phone')) ?></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <span class="contact-icon">✉️</span>
                        <div>
                            <strong>อีเมล</strong>
                            <p><?= e(getSetting('contact_email')) ?></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <span class="contact-icon">💬</span>
                        <div>
                            <strong>LINE</strong>
                            <p><?= e(getSetting('contact_line')) ?></p>
                        </div>
                    </div>
                </div>
                <div class="map-container">
                    <div id="map" style="height: 400px; border-radius: var(--radius);"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <p><?= e(getSetting('footer_text')) ?></p>
                <div class="social-links">
                    <?php if ($fb = getSetting('social_facebook')): ?>
                    <a href="<?= e($fb) ?>" target="_blank" rel="noopener">Facebook</a>
                    <?php endif; ?>
                    <?php if ($ig = getSetting('social_instagram')): ?>
                    <a href="<?= e($ig) ?>" target="_blank" rel="noopener">Instagram</a>
                    <?php endif; ?>
                    <?php if ($yt = getSetting('social_youtube')): ?>
                    <a href="<?= e($yt) ?>" target="_blank" rel="noopener">YouTube</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
    // Initialize Map when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        const mapLat = <?= (float)getSetting('map_lat', 13.7563) ?>;
        const mapLng = <?= (float)getSetting('map_lng', 100.5018) ?>;
        const mapZoom = <?= (int)getSetting('map_zoom', 15) ?>;
        const siteName = '<?= e(getSetting('site_name')) ?>';
        
        <?php if (MAP_PROVIDER === 'openstreetmap'): ?>
        // OpenStreetMap (Leaflet) - Free
        if (typeof L !== 'undefined') {
            try {
                const map = L.map('map').setView([mapLat, mapLng], mapZoom);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(map);
                
                // Custom marker icon
                const marker = L.marker([mapLat, mapLng]).addTo(map);
                marker.bindPopup('<strong>' + siteName + '</strong><br>คลิกเพื่อดูเส้นทาง').openPopup();
                
                // Invalidate size after a short delay (fixes rendering issues)
                setTimeout(function() { map.invalidateSize(); }, 100);
            } catch (e) {
                console.error('Map initialization error:', e);
                document.getElementById('map').innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#94a3b8;">📍 แผนที่กำลังโหลด...</div>';
            }
        } else {
            // Leaflet not loaded - show placeholder
            document.getElementById('map').innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#94a3b8;flex-direction:column;gap:1rem;"><span style="font-size:3rem;">📍</span><span>พิกัด: ' + mapLat + ', ' + mapLng + '</span><a href="https://www.openstreetmap.org/?mlat=' + mapLat + '&mlon=' + mapLng + '#map=' + mapZoom + '/' + mapLat + '/' + mapLng + '" target="_blank" style="color:#6366f1;">ดูบน OpenStreetMap →</a></div>';
        }
        <?php elseif (MAP_PROVIDER === 'google' && GOOGLE_MAPS_API_KEY): ?>
        // Google Maps
        if (typeof google !== 'undefined' && google.maps) {
            const map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: mapLat, lng: mapLng },
                zoom: mapZoom,
                styles: [
                    { elementType: "geometry", stylers: [{ color: "#1e293b" }] },
                    { elementType: "labels.text.stroke", stylers: [{ color: "#1e293b" }] },
                    { elementType: "labels.text.fill", stylers: [{ color: "#94a3b8" }] }
                ]
            });
            new google.maps.Marker({
                position: { lat: mapLat, lng: mapLng },
                map: map,
                title: siteName
            });
        }
        <?php else: ?>
        // No map provider configured
        document.getElementById('map').innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#94a3b8;flex-direction:column;gap:1rem;"><span style="font-size:3rem;">📍</span><span>กรุณาตั้งค่า Map Provider ใน Admin Panel</span></div>';
        <?php endif; ?>
    });
    </script>
</body>
</html>

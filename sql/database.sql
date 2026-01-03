-- ========================================
-- LearnPro Academy - Database Schema
-- ========================================

CREATE DATABASE IF NOT EXISTS `landingap_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `landingap_db`;

-- ========================================
-- Admin Users Table
-- ========================================
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
) ENGINE=InnoDB;

-- Default admin: admin / admin123
INSERT INTO `admins` (`username`, `password`, `email`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com');

-- ========================================
-- Settings Table
-- ========================================
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT,
    `setting_type` ENUM('text', 'textarea', 'color', 'image', 'select', 'number') DEFAULT 'text',
    `setting_group` VARCHAR(50) DEFAULT 'general',
    `setting_label` VARCHAR(100),
    `setting_options` TEXT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ========================================
-- General Settings
-- ========================================
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
('site_name', 'LearnPro Academy', 'text', 'general', 'ชื่อเว็บไซต์'),
('site_description', 'คอร์สเรียนออนไลน์คุณภาพสูง เรียนได้ทุกที่ทุกเวลา', 'textarea', 'general', 'คำอธิบายเว็บไซต์'),
('site_logo', '', 'image', 'general', 'โลโก้'),
('favicon', '', 'image', 'general', 'Favicon');

-- ========================================
-- Color Settings
-- ========================================
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
('color_primary', '#6366f1', 'color', 'colors', 'สีหลัก (Primary)'),
('color_secondary', '#0ea5e9', 'color', 'colors', 'สีรอง (Secondary)'),
('color_accent', '#f59e0b', 'color', 'colors', 'สีเน้น (Accent)'),
('color_bg_dark', '#0f172a', 'color', 'colors', 'สีพื้นหลัง'),
('color_bg_card', '#1e293b', 'color', 'colors', 'สีการ์ด'),
('color_text', '#f8fafc', 'color', 'colors', 'สีข้อความ');

-- ========================================
-- Font Settings
-- ========================================
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`, `setting_options`) VALUES
('font_family', 'Noto Sans Thai', 'select', 'fonts', 'ฟอนต์หลัก', 'Noto Sans Thai,Sarabun,Prompt,Kanit,IBM Plex Sans Thai');

INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
('font_size_base', '16', 'number', 'fonts', 'ขนาดฟอนต์พื้นฐาน (px)');

-- ========================================
-- Hero Section
-- ========================================
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
('hero_title_1', 'เปลี่ยนอนาคตของคุณ', 'text', 'hero', 'หัวข้อหลัก บรรทัด 1'),
('hero_title_2', 'ด้วยทักษะใหม่', 'text', 'hero', 'หัวข้อหลัก บรรทัด 2'),
('hero_subtitle', 'เรียนรู้จากผู้เชี่ยวชาญระดับประเทศ พร้อมใบรับรองที่ได้รับการยอมรับ', 'textarea', 'hero', 'คำอธิบาย'),
('hero_image', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=600&h=700&fit=crop', 'image', 'hero', 'รูปผู้สอน'),
('hero_btn_primary', 'สมัครเรียนเลย', 'text', 'hero', 'ปุ่มหลัก'),
('hero_btn_secondary', 'ดูหลักสูตร', 'text', 'hero', 'ปุ่มรอง'),
('hero_stat_1_number', '10,000+', 'text', 'hero', 'สถิติ 1 - ตัวเลข'),
('hero_stat_1_label', 'นักเรียน', 'text', 'hero', 'สถิติ 1 - ป้าย'),
('hero_stat_2_number', '50+', 'text', 'hero', 'สถิติ 2 - ตัวเลข'),
('hero_stat_2_label', 'คอร์ส', 'text', 'hero', 'สถิติ 2 - ป้าย'),
('hero_stat_3_number', '98%', 'text', 'hero', 'สถิติ 3 - ตัวเลข'),
('hero_stat_3_label', 'พึงพอใจ', 'text', 'hero', 'สถิติ 3 - ป้าย');

-- ========================================
-- Curriculum Section
-- ========================================
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
('curriculum_title', 'หลักสูตรของเรา', 'text', 'curriculum', 'หัวข้อ'),
('curriculum_subtitle', 'เลือกเรียนได้ตามความสนใจ', 'text', 'curriculum', 'คำอธิบาย');

-- ========================================
-- Pricing Section
-- ========================================
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
('pricing_title', 'แพ็กเกจราคา', 'text', 'pricing', 'หัวข้อ'),
('pricing_subtitle', 'เลือกแพ็กเกจที่เหมาะกับคุณ', 'text', 'pricing', 'คำอธิบาย');

-- ========================================
-- Contact / Map Section
-- ========================================
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
('contact_title', 'ติดต่อเรา', 'text', 'contact', 'หัวข้อ'),
('contact_address', '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110', 'textarea', 'contact', 'ที่อยู่'),
('contact_phone', '02-123-4567', 'text', 'contact', 'เบอร์โทร'),
('contact_email', 'contact@learnpro.academy', 'text', 'contact', 'อีเมล'),
('contact_line', '@learnproacademy', 'text', 'contact', 'LINE ID'),
('map_lat', '13.7563', 'text', 'contact', 'ละติจูด'),
('map_lng', '100.5018', 'text', 'contact', 'ลองจิจูด'),
('map_zoom', '15', 'number', 'contact', 'ระดับซูม');

-- ========================================
-- Footer Settings
-- ========================================
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
('footer_text', '© 2026 LearnPro Academy. All rights reserved.', 'text', 'footer', 'ข้อความ Footer'),
('social_facebook', 'https://facebook.com/learnpro', 'text', 'footer', 'Facebook URL'),
('social_instagram', 'https://instagram.com/learnpro', 'text', 'footer', 'Instagram URL'),
('social_youtube', 'https://youtube.com/learnpro', 'text', 'footer', 'YouTube URL');

-- ========================================
-- Courses Table
-- ========================================
CREATE TABLE IF NOT EXISTS `courses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category` VARCHAR(100) NOT NULL,
    `icon` VARCHAR(10) DEFAULT '📚',
    `title` VARCHAR(200) NOT NULL,
    `duration` VARCHAR(50),
    `badge` VARCHAR(50) NULL,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `courses` (`category`, `icon`, `title`, `duration`, `badge`, `sort_order`) VALUES
('พัฒนาแอปมือถือ', '📱', 'Flutter Fundamentals', '12 ชั่วโมง', 'ยอดนิยม', 1),
('พัฒนาแอปมือถือ', '📱', 'React Native', '15 ชั่วโมง', NULL, 2),
('พัฒนาแอปมือถือ', '📱', 'iOS Development', '20 ชั่วโมง', NULL, 3),
('Web Development', '💻', 'HTML/CSS/JavaScript', '10 ชั่วโมง', 'ใหม่', 4),
('Web Development', '💻', 'React.js', '18 ชั่วโมง', NULL, 5),
('Web Development', '💻', 'Node.js Backend', '16 ชั่วโมง', NULL, 6),
('UX/UI Design', '🎨', 'Figma Mastery', '8 ชั่วโมง', NULL, 7),
('UX/UI Design', '🎨', 'Design System', '10 ชั่วโมง', NULL, 8),
('UX/UI Design', '🎨', 'Prototyping', '6 ชั่วโมง', NULL, 9);

-- ========================================
-- Pricing Packages Table
-- ========================================
CREATE TABLE IF NOT EXISTS `pricing_packages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `period` VARCHAR(50) DEFAULT '/เดือน',
    `is_featured` TINYINT(1) DEFAULT 0,
    `badge` VARCHAR(50) NULL,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO `pricing_packages` (`name`, `price`, `period`, `is_featured`, `badge`, `sort_order`) VALUES
('Basic', 990.00, '/เดือน', 0, NULL, 1),
('Pro', 1990.00, '/เดือน', 1, 'แนะนำ', 2),
('Enterprise', 4990.00, '/เดือน', 0, NULL, 3);

-- ========================================
-- Pricing Features Table
-- ========================================
CREATE TABLE IF NOT EXISTS `pricing_features` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `feature_name` VARCHAR(200) NOT NULL,
    `sort_order` INT DEFAULT 0
) ENGINE=InnoDB;

INSERT INTO `pricing_features` (`feature_name`, `sort_order`) VALUES
('เข้าถึงคอร์ส', 1),
('ดูวิดีโอไม่จำกัด', 2),
('ใบรับรอง', 3),
('1-on-1 Mentoring', 4);

-- ========================================
-- Package Feature Mapping
-- ========================================
CREATE TABLE IF NOT EXISTS `package_features` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `package_id` INT NOT NULL,
    `feature_id` INT NOT NULL,
    `value` VARCHAR(100) DEFAULT '✓',
    `is_included` TINYINT(1) DEFAULT 1,
    FOREIGN KEY (`package_id`) REFERENCES `pricing_packages`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`feature_id`) REFERENCES `pricing_features`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO `package_features` (`package_id`, `feature_id`, `value`, `is_included`) VALUES
(1, 1, '10 คอร์ส', 1), (1, 2, '✓', 1), (1, 3, '✗', 0), (1, 4, '✗', 0),
(2, 1, 'ทุกคอร์ส', 1), (2, 2, '✓', 1), (2, 3, '✓', 1), (2, 4, '✗', 0),
(3, 1, 'ทุกคอร์ส', 1), (3, 2, '✓', 1), (3, 3, '✓', 1), (3, 4, '✓', 1);

-- ========================================
-- SEO & Theme Settings - Additional SQL
-- ========================================

-- Theme Presets
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`, `setting_options`) VALUES
('theme_preset', 'dark-purple', 'select', 'colors', 'ธีมสำเร็จรูป', 'dark-purple,dark-blue,dark-green,light-modern,light-warm');

-- SEO Settings (Basic)
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
('seo_title', 'LearnPro Academy | คอร์สเรียนออนไลน์คุณภาพสูง', 'text', 'seo', 'SEO Title (50-60 ตัวอักษร)'),
('seo_description', 'เรียนออนไลน์กับผู้เชี่ยวชาญ พัฒนาทักษะ Web, Mobile, Design พร้อมใบรับรอง เรียนได้ทุกที่ทุกเวลา', 'textarea', 'seo', 'Meta Description (150-160 ตัวอักษร)'),
('seo_keywords', 'คอร์สออนไลน์, เรียนออนไลน์, พัฒนาเว็บ, React, Flutter, UX UI, ใบรับรอง', 'textarea', 'seo', 'Keywords'),
('seo_og_image', '', 'image', 'seo', 'OG Image (1200x630)'),
('seo_canonical', '', 'text', 'seo', 'Canonical URL'),
('seo_author', 'LearnPro Academy', 'text', 'seo', 'Author'),
('seo_language', 'th', 'text', 'seo', 'Language Code');

-- SEO Settings (with options)
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`, `setting_options`) VALUES
('seo_robots', 'index, follow', 'select', 'seo', 'Robots', 'index follow,noindex nofollow,index nofollow,noindex follow'),
('schema_type', 'EducationalOrganization', 'select', 'seo', 'Schema Type', 'EducationalOrganization,Organization,LocalBusiness,Course');

-- Schema.org Settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
('schema_name', 'LearnPro Academy', 'text', 'seo', 'Organization Name'),
('schema_logo', '', 'image', 'seo', 'Organization Logo'),
('schema_phone', '+66-2-123-4567', 'text', 'seo', 'Phone (Schema)'),
('schema_price_range', '฿฿', 'text', 'seo', 'Price Range'),
('schema_rating', '4.9', 'text', 'seo', 'Rating'),
('schema_review_count', '1250', 'number', 'seo', 'Review Count');

-- Hero Section SEO
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
('hero_h1_text', 'เปลี่ยนอนาคตของคุณด้วยทักษะใหม่', 'text', 'hero', 'H1 Tag Text'),
('hero_image_alt', 'ผู้เชี่ยวชาญสอนคอร์สออนไลน์', 'text', 'hero', 'Hero Image Alt Text'),
('hero_image_title', 'เรียนกับมืออาชีพ', 'text', 'hero', 'Hero Image Title');

-- Curriculum Section SEO
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
('curriculum_h2_text', 'หลักสูตรออนไลน์คุณภาพสูง', 'text', 'curriculum', 'H2 Tag Text');

-- Pricing Section SEO
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
('pricing_h2_text', 'แพ็กเกจราคาคอร์สเรียน', 'text', 'pricing', 'H2 Tag Text');

-- Contact Section SEO
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
('contact_h2_text', 'ติดต่อ LearnPro Academy', 'text', 'contact', 'H2 Tag Text');

-- Social Media
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
('social_twitter', '', 'text', 'footer', 'Twitter/X URL'),
('social_linkedin', '', 'text', 'footer', 'LinkedIn URL'),
('social_tiktok', '', 'text', 'footer', 'TikTok URL');

-- Theme Presets Table
CREATE TABLE IF NOT EXISTS `theme_presets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `label` VARCHAR(100),
    `primary_color` VARCHAR(20),
    `secondary_color` VARCHAR(20),
    `accent_color` VARCHAR(20),
    `bg_dark` VARCHAR(20),
    `bg_card` VARCHAR(20),
    `text_color` VARCHAR(20),
    `is_dark` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO `theme_presets` (`name`, `label`, `primary_color`, `secondary_color`, `accent_color`, `bg_dark`, `bg_card`, `text_color`, `is_dark`) VALUES
('dark-purple', 'Dark Purple (Default)', '#6366f1', '#0ea5e9', '#f59e0b', '#0f172a', '#1e293b', '#f8fafc', 1),
('dark-blue', 'Dark Blue Ocean', '#3b82f6', '#06b6d4', '#fbbf24', '#0c1929', '#1a2e44', '#f1f5f9', 1),
('dark-green', 'Dark Forest', '#10b981', '#14b8a6', '#f59e0b', '#0a1f1a', '#163b2f', '#ecfdf5', 1),
('light-modern', 'Light Modern', '#6366f1', '#0ea5e9', '#f59e0b', '#f8fafc', '#ffffff', '#1e293b', 0),
('light-warm', 'Light Warm', '#f97316', '#fb923c', '#eab308', '#fffbeb', '#ffffff', '#292524', 0);

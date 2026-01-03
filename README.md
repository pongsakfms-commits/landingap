# 🎓 LearnPro Academy - Landing Page CMS

[![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

ระบบ Landing Page CMS แบบครบวงจร พร้อม Admin Panel สำหรับจัดการเนื้อหา, ธีม, SEO และอื่นๆ ออกแบบมาให้ใช้งานง่าย ปลอดภัย และย้าย Domain ได้สะดวก

![LearnPro Academy](https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=800&h=400&fit=crop)

## ✨ Features

### 🎨 Frontend
- **Responsive Design** - Mobile-first, รองรับทุกขนาดหน้าจอ
- **Dark Theme** - ดีไซน์โมเดิร์น สีเข้ม สวยงาม
- **Animations** - Smooth scroll, hover effects, floating cards
- **OpenStreetMap** - แผนที่ฟรี ไม่ต้องใช้ API Key
- **Google Maps** - รองรับ Google Maps API (optional)

### 🔐 Admin Panel
- **Secure Login** - Password hashing (BCRYPT)
- **Custom CAPTCHA** - ป้องกัน bot
- **Dashboard** - ภาพรวมข้อมูลเว็บไซต์
- **Settings Management** - จัดการตั้งค่าทุกส่วน

### 📝 Content Management
- **General Settings** - ชื่อเว็บ, โลโก้, คำอธิบาย
- **Hero Section** - หัวข้อ, ปุ่ม, รูปภาพ, สถิติ
- **Courses** - เพิ่ม/แก้ไข/ลบ คอร์สเรียน
- **Pricing** - แพ็กเกจราคา พร้อม Features
- **Contact** - ข้อมูลติดต่อ, พิกัดแผนที่
- **Footer** - ข้อความ Copyright, Social Links

### 🎨 Theme Customization
- **5 Theme Presets** - Dark Purple, Dark Blue, Dark Green, Light Modern, Light Warm
- **Custom Colors** - ปรับสีได้เอง (Primary, Secondary, Accent, Background)
- **Font Selection** - เลือกฟอนต์ไทยได้หลายแบบ

### 🔍 SEO & Schema.org
- **Meta Tags** - Title, Description, Keywords, Author, Robots
- **Open Graph** - Facebook sharing optimization
- **Twitter Cards** - Twitter sharing optimization
- **Schema.org JSON-LD** - Structured data สำหรับ Search Engines & AI
- **Canonical URL** - ป้องกัน duplicate content
- **Favicon** - รองรับ favicon

### 🛡️ Security
- **CSRF Protection** - ป้องกัน Cross-Site Request Forgery
- **Rate Limiting** - ป้องกัน brute force
- **Input Sanitization** - ป้องกัน XSS
- **Secure Sessions** - Session security headers
- **Password Hashing** - BCRYPT algorithm
- **File Upload Validation** - ตรวจสอบไฟล์อัพโหลด

## 📋 Requirements

- PHP 7.4+ (รองรับ PHP 8.x)
- MySQL 5.7+ / MariaDB 10.3+
- Apache / Nginx (mod_rewrite enabled)
- GD Library (สำหรับ CAPTCHA)

## 🚀 Installation

### 1. Clone Repository
```bash
git clone <your-repository-url>
cd landingap
```

### 2. สร้าง Database
```bash
# สร้าง Database
mysql -u root -p -e "CREATE DATABASE landingap_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"

# Import Schema
mysql -u root -p --default-character-set=utf8mb4 landingap_db < sql/database.sql
mysql -u root -p --default-character-set=utf8mb4 landingap_db < sql/seo_themes.sql
```

### 3. ตั้งค่า Config
แก้ไขไฟล์ `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'landingap_db');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

แก้ไขไฟล์ `config/config.php`:
```php
define('SITE_URL', 'https://yourdomain.com');
```

### 4. ตั้งค่า Permissions (Linux/Mac)
```bash
chmod 755 uploads/
chmod 755 logs/
```

### 5. เข้าใช้งาน
- **Frontend:** `https://yourdomain.com/index.php`
- **Admin Panel:** `https://yourdomain.com/admin/login.php`
- **Default Login:** `admin` / `password`

⚠️ **อย่าลืมเปลี่ยนรหัสผ่านหลังติดตั้ง!**

## 📁 Project Structure

```
landingap/
├── admin/                  # Admin Panel
│   ├── assets/            # CSS สำหรับ Admin
│   ├── includes/          # Sidebar, Header
│   ├── login.php          # หน้า Login
│   ├── dashboard.php      # Dashboard
│   ├── settings.php       # ตั้งค่าทั่วไป (8 กลุ่ม)
│   ├── courses.php        # จัดการคอร์ส
│   ├── pricing.php        # จัดการแพ็กเกจ
│   ├── themes.php         # จัดการธีม
│   ├── seo.php            # ตั้งค่า SEO
│   └── captcha.php        # สร้าง CAPTCHA
├── assets/
│   └── leaflet/           # Leaflet Map (local fallback)
├── config/
│   ├── config.php         # ตั้งค่าหลัก
│   ├── database.php       # ตั้งค่า Database
│   └── security.php       # Security functions
├── docs/
│   └── MANUAL.md          # คู่มือการใช้งาน
├── sql/
│   ├── database.sql       # Database schema
│   └── seo_themes.sql     # SEO & Theme data
├── uploads/               # ไฟล์อัพโหลด
├── logs/                  # Security logs
├── index.php              # หน้าแรก (Dynamic)
├── index.html             # หน้าแรก (Static Demo)
├── styles.css             # CSS หลัก
├── script.js              # JavaScript หลัก
└── README.md              # ไฟล์นี้
```

## 🔧 Configuration

### Map Provider
ใน `config/config.php`:
```php
// OpenStreetMap (ฟรี - แนะนำ)
define('MAP_PROVIDER', 'openstreetmap');

// หรือ Google Maps (ต้องใส่ API Key)
define('MAP_PROVIDER', 'google');
define('GOOGLE_MAPS_API_KEY', 'your-api-key');
```

### CAPTCHA Provider
```php
// Custom CAPTCHA (Default)
define('CAPTCHA_PROVIDER', 'custom');

// หรือ Google reCAPTCHA v2
define('CAPTCHA_PROVIDER', 'recaptcha');
define('RECAPTCHA_SITE_KEY', 'your-site-key');
define('RECAPTCHA_SECRET_KEY', 'your-secret-key');
```

## 🌐 Domain Migration

ย้าย Domain ง่ายๆ เพียง:

1. อัพโหลดไฟล์ทั้งหมดไปยัง Server ใหม่
2. สร้าง Database และ Import SQL files
3. แก้ไข `config/database.php` และ `config/config.php`
4. เปลี่ยน `SITE_URL` เป็น Domain ใหม่

## 📱 Screenshots

### Frontend
- Hero Section พร้อม Gradient Text
- Curriculum แบบ Accordion (Mobile) / Grid (Desktop)
- Pricing Table พร้อม Feature Comparison
- Contact Section พร้อม OpenStreetMap

### Admin Panel
- Dashboard พร้อมสถิติ
- Settings Manager (8 กลุ่ม)
- Course & Pricing Management
- Theme Customizer
- SEO Settings

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License.

## 👨‍💻 Author

Created with ❤️ for LearnPro Academy

---

⭐ **Star this repo** if you found it useful!

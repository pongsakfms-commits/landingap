<?php
/**
 * Admin Login Page
 */
require_once __DIR__ . '/../config/config.php';

$error = '';

// Already logged in
if (isLoggedIn()) {
    redirect('/admin/dashboard.php');
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha = strtoupper(trim($_POST['captcha'] ?? ''));
    
    // Validate captcha
    if (CAPTCHA_TYPE === 'custom') {
        if ($captcha !== ($_SESSION['captcha_code'] ?? '')) {
            $error = 'รหัส Captcha ไม่ถูกต้อง';
        }
    } elseif (CAPTCHA_TYPE === 'google') {
        // Google reCAPTCHA validation
        $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
        $verify = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . GOOGLE_RECAPTCHA_SECRET_KEY . '&response=' . $recaptcha_response);
        $result = json_decode($verify);
        if (!$result->success) {
            $error = 'กรุณายืนยัน reCAPTCHA';
        }
    }
    
    if (empty($error)) {
        // Check credentials
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            // Update last login
            $stmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$admin['id']]);
            
            redirect('/admin/dashboard.php');
        } else {
            $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
        }
    }
    
    // Clear captcha
    unset($_SESSION['captcha_code']);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - LearnPro Academy</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600&display=swap" rel="stylesheet">
    <?php if (CAPTCHA_TYPE === 'google' && GOOGLE_RECAPTCHA_SITE_KEY): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php endif; ?>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Noto Sans Thai', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-card {
            background: #1e293b;
            border-radius: 16px;
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .logo {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 0.5rem;
        }
        .logo span { color: #6366f1; }
        .subtitle {
            text-align: center;
            color: #94a3b8;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        label {
            display: block;
            color: #94a3b8;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 0.875rem 1rem;
            background: #0f172a;
            border: 2px solid #334155;
            border-radius: 8px;
            color: #f8fafc;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #6366f1;
        }
        .captcha-wrapper {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .captcha-wrapper input {
            flex: 1;
        }
        .captcha-img {
            border-radius: 8px;
            cursor: pointer;
        }
        .error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            color: #ef4444;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #6366f1 0%, #0ea5e9 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.875rem;
        }
        .back-link:hover { color: #6366f1; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">Learn<span>Pro</span></div>
        <p class="subtitle">Admin Panel</p>
        
        <?php if ($error): ?>
        <div class="error"><?= e($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>ชื่อผู้ใช้</label>
                <input type="text" name="username" required autocomplete="username">
            </div>
            
            <div class="form-group">
                <label>รหัสผ่าน</label>
                <input type="password" name="password" required autocomplete="current-password">
            </div>
            
            <?php if (CAPTCHA_TYPE === 'custom'): ?>
            <div class="form-group">
                <label>รหัส Captcha (คลิกรูปเพื่อเปลี่ยน)</label>
                <div class="captcha-wrapper">
                    <input type="text" name="captcha" required placeholder="กรอกรหัสที่เห็น" autocomplete="off">
                    <img src="captcha.php" class="captcha-img" alt="Captcha" onclick="this.src='captcha.php?'+Date.now()">
                </div>
            </div>
            <?php elseif (CAPTCHA_TYPE === 'google' && GOOGLE_RECAPTCHA_SITE_KEY): ?>
            <div class="form-group">
                <div class="g-recaptcha" data-sitekey="<?= GOOGLE_RECAPTCHA_SITE_KEY ?>" data-theme="dark"></div>
            </div>
            <?php endif; ?>
            
            <button type="submit">เข้าสู่ระบบ</button>
        </form>
        
        <a href="<?= SITE_URL ?>" class="back-link">← กลับหน้าเว็บไซต์</a>
    </div>
</body>
</html>

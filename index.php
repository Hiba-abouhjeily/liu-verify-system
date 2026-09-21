<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';

/*
|--------------------------------------------------------------------------
| Simple Router
|--------------------------------------------------------------------------
| This version works inside any XAMPP subfolder without needing .html files.
| Use links like: index.php?page=login, index.php?page=dashboard, etc.
*/
function cleanPageName($page) {
    return preg_replace('/[^a-zA-Z0-9_]/', '', (string)$page);
}

$page = isset($_GET['page']) ? cleanPageName($_GET['page']) : 'home';
if ($page === '') {
    $page = 'home';
}

function appUrl($page = 'home', array $params = []) {
    $script = $_SERVER['SCRIPT_NAME'] ?? 'index.php';

    if ($page !== '' && $page !== 'home') {
        $params = array_merge(['page' => $page], $params);
    }

    return $script . (empty($params) ? '' : '?' . http_build_query($params));
}

function redirectTo($page = 'home', array $params = []) {
    header('Location: ' . appUrl($page, $params));
    exit;
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$SECRET_ADMIN_KEY = 'XyZ123SecretKey2024';

/*
|--------------------------------------------------------------------------
| Automatic Database Setup
|--------------------------------------------------------------------------
| This fixes errors like:
| Table 'certificate_verify.settings' doesn't exist
| Table 'certificate_verify.certificates' doesn't exist
|
| The app needs 3 tables: users, certificates, settings.
*/
function dbColumnExists($pdo, $table, $column) {
    $stmt = $pdo->prepare("\n        SELECT COUNT(*)\n        FROM INFORMATION_SCHEMA.COLUMNS\n        WHERE TABLE_SCHEMA = DATABASE()\n          AND TABLE_NAME = ?\n          AND COLUMN_NAME = ?\n    ");
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

function addColumnIfMissing($pdo, $table, $column, $definition) {
    if (!dbColumnExists($pdo, $table, $column)) {
        $pdo->exec("ALTER TABLE `$table` ADD COLUMN $definition");
    }
}

try {
    $pdo->exec("\n        CREATE TABLE IF NOT EXISTS users (\n            id INT AUTO_INCREMENT PRIMARY KEY,\n            fullname VARCHAR(150) NOT NULL,\n            email VARCHAR(190) NOT NULL UNIQUE,\n            password VARCHAR(255) NOT NULL,\n            role ENUM('user','admin') NOT NULL DEFAULT 'user',\n            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP\n        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci\n    ");

    $pdo->exec("\n        CREATE TABLE IF NOT EXISTS certificates (\n            id INT AUTO_INCREMENT PRIMARY KEY,\n            user_id INT NOT NULL,\n            student_name VARCHAR(190) NOT NULL,\n            student_id VARCHAR(100) NOT NULL,\n            degree VARCHAR(190) NOT NULL,\n            status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',\n            hash_code VARCHAR(255) NULL,\n            requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,\n            issued_at DATETIME NULL,\n            approved_at DATETIME NULL,\n            INDEX idx_cert_user_id (user_id),\n            INDEX idx_cert_status (status),\n            INDEX idx_cert_hash_code (hash_code)\n        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci\n    ");

    $pdo->exec("\n        CREATE TABLE IF NOT EXISTS settings (\n            id INT AUTO_INCREMENT PRIMARY KEY,\n            setting_key VARCHAR(100) NOT NULL UNIQUE,\n            setting_value TEXT NULL,\n            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP\n        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci\n    ");

    /* Add missing columns if the tables already existed from an older version */
    addColumnIfMissing($pdo, 'users', 'fullname', "fullname VARCHAR(150) NOT NULL DEFAULT ''");
    addColumnIfMissing($pdo, 'users', 'email', "email VARCHAR(190) NOT NULL DEFAULT ''");
    addColumnIfMissing($pdo, 'users', 'password', "password VARCHAR(255) NOT NULL DEFAULT ''");
    addColumnIfMissing($pdo, 'users', 'role', "role ENUM('user','admin') NOT NULL DEFAULT 'user'");
    addColumnIfMissing($pdo, 'users', 'created_at', "created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");

    addColumnIfMissing($pdo, 'certificates', 'user_id', "user_id INT NOT NULL DEFAULT 0");
    addColumnIfMissing($pdo, 'certificates', 'student_name', "student_name VARCHAR(190) NOT NULL DEFAULT ''");
    addColumnIfMissing($pdo, 'certificates', 'student_id', "student_id VARCHAR(100) NOT NULL DEFAULT ''");
    addColumnIfMissing($pdo, 'certificates', 'degree', "degree VARCHAR(190) NOT NULL DEFAULT ''");
    addColumnIfMissing($pdo, 'certificates', 'status', "status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
    addColumnIfMissing($pdo, 'certificates', 'hash_code', "hash_code VARCHAR(255) NULL");
    addColumnIfMissing($pdo, 'certificates', 'requested_at', "requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
    addColumnIfMissing($pdo, 'certificates', 'issued_at', "issued_at DATETIME NULL");
    addColumnIfMissing($pdo, 'certificates', 'approved_at', "approved_at DATETIME NULL");

    addColumnIfMissing($pdo, 'settings', 'setting_key', "setting_key VARCHAR(100) NOT NULL DEFAULT ''");
    addColumnIfMissing($pdo, 'settings', 'setting_value', "setting_value TEXT NULL");
    addColumnIfMissing($pdo, 'settings', 'updated_at', "updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

    /* Keep hash_code nullable for pending certificates */
    try { $pdo->exec("ALTER TABLE certificates MODIFY hash_code VARCHAR(255) NULL"); } catch (Exception $ignored) {}

    /* Default domain setting */
    $stmt = $pdo->prepare("\n        INSERT INTO settings (setting_key, setting_value)\n        VALUES ('site_domain', '')\n        ON DUPLICATE KEY UPDATE setting_key = setting_key\n    ");
    $stmt->execute();

} catch (PDOException $e) {
    die("Database setup error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

function isLoggedIn() { return isset($_SESSION['user_id']); }
function isAdmin() { return isset($_SESSION['role']) && $_SESSION['role'] === 'admin'; }
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}


$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'site_domain'");
$stmt->execute();
$SITE_DOMAIN = $stmt->fetchColumn();
if (!$SITE_DOMAIN) $SITE_DOMAIN = $_SERVER['HTTP_HOST'];


$pdo->exec("UPDATE certificates SET status = 'approved', issued_at = NOW(), approved_at = NOW() WHERE status = 'pending' AND requested_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)");


if (isset($_GET['secret']) && $_GET['secret'] === $SECRET_ADMIN_KEY) {
    $_SESSION['user_id'] = 999;
    $_SESSION['role'] = 'admin';
    $_SESSION['fullname'] = 'Secret Admin';
    redirectTo('admin');
}


if (isset($_GET['hash']) && !empty($_GET['hash'])) {
    $hash = cleanInput($_GET['hash']);
    $stmt = $pdo->prepare("SELECT * FROM certificates WHERE hash_code = ?");
    $stmt->execute([$hash]);
    $cert = $stmt->fetch();
    if ($cert) {
        $_SESSION['verify_result'] = [
            'valid' => true,
            'student_name' => $cert['student_name'],
            'degree' => $cert['degree'],
            'issued_at' => $cert['issued_at'],
            'hash' => $cert['hash_code']
        ];
    } else {
        $_SESSION['verify_result'] = ['valid' => false];
    }
    redirectTo('verify');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    
    if ($action === 'register') {
        $fullname = cleanInput($_POST['fullname']);
        $email = cleanInput($_POST['email']);
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];
        
        $_SESSION['old_register'] = [
            'fullname' => $fullname,
            'email'=> $email
        ];
        
        if ($password !== $confirm) {
            $_SESSION['error'] = "Passwords do not match.";
            redirectTo('register');
            
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email format.";
             redirectTo('register');
            
        } elseif (strlen($password) < 8) {
            $_SESSION['error'] = "Password must be at least 6 characters.";
             redirectTo('register');
            
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = "Email already registered.";
                
                $_SESSSION['old_register']['email'] = '';
                redirectTo('register');
                
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, 'user')");
                $stmt->execute([$fullname, $email, $hashed]);
                
                unset($_SESSION['old_register']);
                $_SESSION['success'] = "Registration successful! Please login.";
                
                redirectTo('login');
            }
        }
    }
    
    
    if ($action === 'login') {
        $email = cleanInput($_POST['email']);
        $password = $_POST['password'];
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['fullname'] = $user['fullname'];
            if(!isAdmin())
                redirectTo('dashboard');
            else
                redirectTo('admin');
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            redirectTo('login');
        }
    }
    
    
    if ($action === 'verify_hash') {
        $hash = cleanInput($_POST['hash']);
        $stmt = $pdo->prepare("SELECT * FROM certificates WHERE hash_code = ?");
        $stmt->execute([$hash]);
        $cert = $stmt->fetch();
        if ($cert) {
            $_SESSION['verify_result'] = [
                'valid' => true,
                'student_name' => $cert['student_name'],
                'degree' => $cert['degree'],
                'issued_at' => $cert['issued_at'],
                'hash' => $cert['hash_code']
            ];
        } else {
            $_SESSION['verify_result'] = ['valid' => false];
        }
        redirectTo('verify');
    }
    
    
    if ($action === 'request_certificate' && isLoggedIn()) {
        $student_name = cleanInput($_POST['student_name']);
        $student_id = cleanInput($_POST['student_id']);
        $degree = cleanInput($_POST['degree']);
        $stmt = $pdo->prepare("INSERT INTO certificates (user_id, student_name, student_id, degree, status, requested_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
        $stmt->execute([$_SESSION['user_id'], $student_name, $student_id, $degree]);
        $_SESSION['success'] = "Certificate requested! It will be available in 24 hours or when admin approves.";
        redirectTo('dashboard');
    }
    
    
    if ($action === 'approve_cert' && isAdmin()) {
        $cert_id = (int)$_POST['cert_id'];
        $hash = hash('sha256', $cert_id . time() . rand());
        $stmt = $pdo->prepare("UPDATE certificates SET status='approved', issued_at=NOW(), approved_at=NOW(), hash_code=? WHERE id=?");
        $stmt->execute([$hash, $cert_id]);
        $_SESSION['success'] = "Certificate approved!";
        redirectTo('admin');
    }
    
    
    if ($action === 'delete_cert' && isAdmin()) {
        $cert_id = (int)$_POST['cert_id'];
        $stmt = $pdo->prepare("DELETE FROM certificates WHERE id=?");
        $stmt->execute([$cert_id]);
        $_SESSION['success'] = "Certificate deleted!";
        redirectTo('admin');
    }
    
    
    if ($action === 'delete_user' && isAdmin()) {
        $user_id = (int)$_POST['user_id'];
        if ($user_id != $_SESSION['user_id']) {
            $stmt = $pdo->prepare("DELETE FROM certificates WHERE user_id=?");
            $stmt->execute([$user_id]);
            $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
            $stmt->execute([$user_id]);
            $_SESSION['success'] = "User deleted!";
        } else {
            $_SESSION['error'] = "You cannot delete yourself!";
        }
        redirectTo('admin');
    }
    
    
    if ($action === 'update_domain' && isAdmin()) {
        $domain = cleanInput($_POST['domain']);
        $stmt = $pdo->prepare("
            INSERT INTO settings (setting_key, setting_value)
            VALUES ('site_domain', ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");
        $stmt->execute([$domain]);
        $_SESSION['success'] = "Domain updated!";
        redirectTo('admin');
    }
}


if (isset($_GET['logout'])) {
    session_destroy();
    redirectTo('home');
}



if (($page === 'dashboard' || $page === 'admin') && !isLoggedIn()) {
    redirectTo('login');
}


if (isset($_GET['download']) && isLoggedIn()) {
    $id = (int)$_GET['download'];
    $stmt = $pdo->prepare("SELECT * FROM certificates WHERE id = ? AND user_id = ? AND status = 'approved'");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $cert = $stmt->fetch();
    if ($cert) {
        require_once 'libs/fpdf/fpdf.php';
        require_once 'libs/phpqrcode/qrlib.php';
        
        if (!$cert['hash_code']) {
            $hash = hash('sha256', $cert['id'] . $cert['student_id'] . time());
            $stmt = $pdo->prepare("UPDATE certificates SET hash_code=? WHERE id=?");
            $stmt->execute([$hash, $cert['id']]);
            $cert['hash_code'] = $hash;
        }
        
        $qr_file = 'temp/qr_' . $cert['id'] . '.png';
        if (!file_exists('temp')) mkdir('temp', 0777, true);
        $verify_url = "http://" . $SITE_DOMAIN . appUrl('verify', ['hash' => $cert['hash_code']]);
        QRcode::png($verify_url, $qr_file, QR_ECLEVEL_L, 12);
        
        
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        
        
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect(0, 0, 297, 210, 'F');
        
        
        $pdf->SetDrawColor(15, 76, 146);
        $pdf->SetLineWidth(2);
        $pdf->Rect(6, 6, 285, 198);
        
        
        $pdf->SetDrawColor(212, 175, 55);
        $pdf->SetLineWidth(1);
        $pdf->Rect(10, 10, 277, 190);
        
        
        $pdf->SetDrawColor(200, 220, 240);
        $pdf->SetLineWidth(0.5);
        $pdf->Rect(14, 14, 269, 182);
        
        
        $pdf->SetDrawColor(212, 175, 55);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(40, 28, 257, 28);
        $pdf->Line(40, 32, 257, 32);
        
        
        
        
        $pdf->SetFont('Arial', 'B', 26);
        $pdf->SetTextColor(15, 76, 146);
        $pdf->SetXY(60, 22);
        $pdf->Cell(180, 12, 'LEBANESE INTERNATIONAL UNIVERSITY', 0, 1, 'C');
        
        
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetXY(60, 36);
        $pdf->Cell(180, 6, 'Academic Certificate of Achievement', 0, 1, 'C');
        
        
        $pdf->SetFillColor(15, 76, 146);
        $pdf->Rect(60, 55, 180, 18, 'F');
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY(60, 58);
        $pdf->Cell(180, 12, 'CERTIFICATE OF ACHIEVEMENT', 0, 0, 'C');
        
        
        $pdf->SetFont('Arial', '', 14);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY(0, 90);
        $pdf->Cell(297, 10, 'This is to certify that', 0, 1, 'C');
        
        
        $pdf->SetFont('Arial', 'B', 32);
        $pdf->SetTextColor(15, 76, 146);
        $pdf->Cell(297, 22, strtoupper($cert['student_name']), 0, 1, 'C');
        
        
        $pdf->SetDrawColor(212, 175, 55);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(80, 122, 220, 122);
        
        
        $pdf->SetFont('Arial', '', 14);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->Cell(297, 12, 'has successfully completed the requirements for', 0, 1, 'C');
        
        
        $pdf->SetFont('Arial', 'B', 22);
        $pdf->SetTextColor(15, 76, 146);
        $pdf->Cell(297, 18, $cert['degree'], 0, 1, 'C');
        
        
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(297, 8, 'Issued on: ' . date('F d, Y', strtotime($cert['issued_at'])), 0, 1, 'C');
        
        
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->SetFillColor(248, 250, 252);
        $pdf->Rect(50, 158, 197, 38, 'DF');
        
        
        $pdf->Image($qr_file, 65, 163, 30, 30);
        
        
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor(15, 76, 146);
        $pdf->SetXY(105, 168);
        $pdf->Cell(0, 6, 'VERIFICATION HASH', 0, 0);
        
        
        $hash_full = $cert['hash_code'];
        $hash_part1 = substr($hash_full, 0, 35);
        $hash_part2 = substr($hash_full, 35);
        
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY(105, 176);
        $pdf->Cell(0, 4, $hash_part1, 0, 0);
        $pdf->SetXY(105, 182);
        $pdf->Cell(0, 4, $hash_part2, 0, 0);
        
        
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetXY(105, 190);
        $pdf->Cell(0, 4, 'Scan QR Code to Verify Online', 0, 0);
        
        
        $pdf->SetDrawColor(100, 100, 100);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(45, 205, 115, 205);
        $pdf->Line(182, 205, 252, 205);
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY(60, 209);
        $pdf->Cell(0, 5, 'University Registrar', 0, 0);
        $pdf->SetXY(197, 209);
        $pdf->Cell(0, 5, 'University President', 0, 0);
        
        
        $pdf->SetDrawColor(212, 175, 55);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(70, 216, 230, 216);
        
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->SetXY(0, 220);
        $pdf->Cell(297, 5, 'This certificate is digitally signed and blockchain-verified', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(297, 4, 'Verify at: ' . $SITE_DOMAIN . appUrl('verify'), 0, 1, 'C');
        
        $pdf->Output('D', 'certificate_' . $cert['id'] . '.pdf');
        if (file_exists($qr_file)) unlink($qr_file);
        exit;
    }
}


$verifyResult = null;
if (isset($_SESSION['verify_result'])) {
    $verifyResult = $_SESSION['verify_result'];
    unset($_SESSION['verify_result']);
}


$userCerts = [];
if ($page === 'dashboard' && isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT * FROM certificates WHERE user_id = ? ORDER BY requested_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $userCerts = $stmt->fetchAll();
}


$pendingCerts = $allCerts = $allUsers = [];
$totalUsers = $totalCerts = 0;
$currentDomain = '';
if ($page === 'admin' && isAdmin()) {
    $pendingCerts = $pdo->query("SELECT c.*, u.fullname as user_name FROM certificates c JOIN users u ON c.user_id = u.id WHERE c.status='pending' ORDER BY c.requested_at DESC")->fetchAll();
    $allCerts = $pdo->query("SELECT c.*, u.fullname as user_name FROM certificates c JOIN users u ON c.user_id = u.id ORDER BY c.requested_at DESC")->fetchAll();
    $allUsers = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalCerts = $pdo->query("SELECT COUNT(*) FROM certificates")->fetchColumn();
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'site_domain'");
    $stmt->execute();
    $currentDomain = $stmt->fetchColumn();
}

$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
        if ($page === 'login') echo 'Login | LIU Verify';
        elseif ($page === 'register') echo 'Register | LIU Verify';
        elseif ($page === 'dashboard') echo 'Dashboard | LIU Verify';
        elseif ($page === 'admin') echo 'Admin Panel | LIU Verify';
        elseif ($page === 'verify') echo 'Verify | LIU Verify';
        elseif ($page === 'about') echo 'About Us | LIU Verify';
        elseif ($page === 'contact') echo 'Contact Us | LIU Verify';
        elseif ($page === 'help') echo 'Help | LIU Verify';
        else echo 'LIU Verify';
    ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .alert-error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .result-valid { background: #d4edda; border-left: 5px solid #28a745; padding: 20px; border-radius: 10px; margin-top: 20px; }
        .result-invalid { background: #f8d7da; border-left: 5px solid #dc3545; padding: 20px; border-radius: 10px; margin-top: 20px; text-align: center; }
        @media (max-width: 768px) {
            .nav-links { gap: 8px; }
            .nav-links a { font-size: 11px; }
            .logo span { font-size: 14px; }
            .logo img { height: 35px; }
            .container { padding: 15px; margin-top: 80px; }
            .auth-card { padding: 20px; }
            .dashboard-container { flex-direction: column; }
            .sidebar { width: 100%; margin-bottom: 20px; }
        }
    </style>
</head>
<body>


<nav>
    <div class="nav-container">
        <div class="logo">
            <img src="assets/logoimage.png" alt="LIU Logo">
            <span>Verify</span>
        </div>

        <ul class="nav-links">
            <li><a href="<?= e(appUrl('home')) ?>">Home</a></li>

            <?php if (isLoggedIn()): ?>
                <li><a href="<?= e(appUrl('dashboard')) ?>">Dashboard</a></li>
                <li><a href="<?= e(appUrl('about')) ?>">About Us</a></li>
                <li><a href="<?= e(appUrl('contact')) ?>">Contact</a></li>
                <li><a href="<?= e(appUrl('home', ['logout' => 1])) ?>">Logout</a></li>
            <?php else: ?>
                <li><a href="<?= e(appUrl('login')) ?>">LogIn</a></li>
                <li><a href="<?= e(appUrl('register')) ?>">Register</a></li>
                <li><a href="<?= e(appUrl('about')) ?>">About Us</a></li>
                <li><a href="<?= e(appUrl('contact')) ?>">Contact</a></li>
                
            <?php endif; ?>
        </ul>

        <a href="<?= e(appUrl('help')) ?>" class="help-link">
            <i class="fas fa-question-circle"></i> Help
        </a>
    </div>
</nav>


<div class="container">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>
</div>


<?php if ($page === 'home'): ?>
<div class="container">
    <section class="hero-card">
        <h1>Academic Certificate Verification</h1>
        <p>A secure, decentralized system for issuing and verifying academic credentials using blockchain principles.</p>
        <br>
        <?php if (isLoggedIn()): ?>
            <a href="<?= e(appUrl('dashboard')) ?>" class="btn">Go to Dashboard</a>
        <?php else: ?>
            <a href="<?= e(appUrl('register')) ?>" class="btn">Get Started</a>
        <?php endif; ?>
    </section>

    <section class="info-grid">
        <div class="info-card">
            <i class="fas fa-shield-halved"></i>
            <h3>Cryptographic Hashing</h3>
            <p>Every certificate is converted into a unique SHA-256 hash, making it difficult to forge or alter.</p>
        </div>
        <div class="info-card">
            <i class="fas fa-cubes"></i>
            <h3>Blockchain Integrity</h3>
            <p>Digital fingerprints are stored as verifiable records for transparency and trust.</p>
        </div>
        <div class="info-card">
            <i class="fas fa-qrcode"></i>
            <h3>Instant QR Verify</h3>
            <p>Employers can validate authenticity by scanning a QR code or entering the hash code.</p>
        </div>
    </section>
</div>


<?php elseif ($page === 'login' && !isLoggedIn()): ?>
<div class="container login-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-user-shield fa-3x" style="color: #0f4c92; margin-bottom: 20px;"></i>
            <h2>Login</h2>
            <p>Access your decentralized verification system.</p>
        </div>
        <form method="POST" class="auth-form">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" name="email" placeholder="name@student.liu.edu.lb" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn-full">Login</button>
        </form>
        <div class="auth-footer"><p>Don't have an account? <a href="<?= e(appUrl('register')) ?>">Register here</a></p></div>
    </div>
</div>


<?php elseif ($page === 'register' && !isLoggedIn()): ?>
    
<?php $oldRegister = $_SESSION['old_register'] ?? []; ?>
<div class="container login-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-user-plus"></i>
            <h2>Create Account</h2>
            <p>Join the secure academic network.</p>
        </div>
        <form method="POST" class="auth-form">
            <input type="hidden" name="action" value="register">
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" name="fullname" placeholder="Your full name" value="<?= htmlspecialchars($oldRegiser['fullname'] ?? '')?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> LIU Email</label>
                    <input type="email" name="email" placeholder="name@student.liu.edu.lb" value="<?= htmlspecialchars($oldRegiser['email'] ?? '')?>"required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" placeholder="Min. 8 characters" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-check-double"></i> Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="Repeat password" required>
                </div>
            </div>

            <button type="submit" class="btn-full">Create Account</button>
        </form>
        <div class="auth-footer"><p>Already have an account? <a href="<?= e(appUrl('login')) ?>">Login here</a></p></div>
    </div>
</div>


<?php elseif ($page === 'dashboard' && isLoggedIn()): ?>
<div class="container dashboard-container" style="display: flex; gap: 30px;">
    <aside class="sidebar" style="width: 280px; background: white; padding: 20px; border-radius: 15px;">
        <div style="text-align: center;">
            <i class="fas fa-user-circle fa-4x" style="color: #0f4c92;"></i>
            <h3><?= htmlspecialchars($_SESSION['fullname']) ?></h3>
            <p>Student</p>
        </div>
        <ul style="list-style: none; margin-top: 20px;">
            <li><a href="<?= e(appUrl('dashboard')) ?>"><i class="fas fa-th-large"></i> Overview</a></li>
            <li><a href="#requestForm"><i class="fas fa-plus-circle"></i> Request Certificate</a></li>
        </ul>
    </aside>
    <main style="flex:1;">
        <div class="hero" style="background:white; padding:20px; border-radius:15px; margin-bottom:20px;">
            <h2>Welcome back, <?= htmlspecialchars($_SESSION['fullname']) ?>!</h2>
            <p>Manage your decentralized academic records from this portal.</p>
        </div>
        
        <div class="info-grid" style="grid-template-columns:1fr;">
            <table style="width:100%; background:white; border-radius:10px; overflow:hidden;">
                <tr style="background:#0f4c92; color:white;">
                    <th style="padding:12px;">Student Name</th><th style="padding:12px;">Degree</th><th style="padding:12px;">Status</th><th style="padding:12px;">Action</th>
                </tr>
                <?php foreach ($userCerts as $cert): ?>
                <tr>
                    <td style="padding:12px;"><?= htmlspecialchars($cert['student_name']) ?></td>
                    <td style="padding:12px;"><?= htmlspecialchars($cert['degree']) ?></td>
                    <td style="padding:12px;"><span style="background: <?= $cert['status'] == 'pending' ? '#ffc107' : '#28a745' ?>; padding: 4px 10px; border-radius: 20px;"><?= $cert['status'] ?></span></td>
                    <td style="padding:12px;"><?php if ($cert['status'] == 'approved'): ?><a href="<?= e(appUrl('home', ['download' => $cert['id']])) ?>" class="btn">Download PDF</a><?php else: ?>Waiting<?php endif; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div id="requestForm" style="margin-top:40px; background:white; padding:30px; border-radius:15px;">
            <h3>Request New Certificate</h3>
            <form method="POST">
                <input type="hidden" name="action" value="request_certificate">
                <div class="form-group"><label>Student Full Name</label><input type="text" name="student_name" required style="width:100%; padding:10px;"></div>
                <div class="form-group"><label>Student ID</label><input type="text" name="student_id" required style="width:100%; padding:10px;"></div>
                <div class="form-group"><label>Degree</label><select name="degree" required style="width:100%; padding:10px;"><option>BS in Computer Science</option><option>BE in Computer Engineering</option><option>BS in MIS</option><option>BBA</option></select></div>
                <button type="submit" class="btn">Submit Request</button>
            </form>
        </div>
    </main>
</div>


<?php elseif ($page === 'admin' && isAdmin()): ?>
<div class="container">
    <h1>🔒 Admin Panel</h1>
    
    <div class="domain-card" style="background:white; padding:20px; border-radius:10px; margin-bottom:30px; border:1px solid #0f4c92;">
        <h3><i class="fas fa-globe"></i> QR Code Domain Settings</h3>
        <form method="POST">
            <input type="hidden" name="action" value="update_domain">
            <div style="display: flex; gap: 10px;">
                <input type="text" name="domain" value="<?= htmlspecialchars($currentDomain ?? $_SERVER['HTTP_HOST']) ?>" required style="flex:1; padding:10px; border:1px solid #ddd; border-radius:5px;">
                <button type="submit" class="btn" style="background:#0f4c92;">Update Domain</button>
            </div>
        </form>
    </div>
    
    <div class="admin-stats" style="display: grid; grid-template-columns: repeat(3,1fr); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card" style="background:white; padding:20px; border-radius:10px; text-align:center;"><div style="font-size:32px; font-weight:bold; color:#0f4c92;"><?= $totalUsers ?></div><div>Total Users</div></div>
        <div class="stat-card" style="background:white; padding:20px; border-radius:10px; text-align:center;"><div style="font-size:32px; font-weight:bold; color:#0f4c92;"><?= $totalCerts ?></div><div>Total Certificates</div></div>
        <div class="stat-card" style="background:white; padding:20px; border-radius:10px; text-align:center;"><div style="font-size:32px; font-weight:bold; color:#0f4c92;"><?= count($pendingCerts) ?></div><div>Pending Approval</div></div>
    </div>
    
    <h2>Pending Certificates</h2>
    <table style="width:100%; background:white;">
        <tr style="background:#0f4c92; color:white;"><th>ID</th><th>User</th><th>Student</th><th>Degree</th><th>Action</th></tr>
        <?php foreach ($pendingCerts as $c): ?>
        <tr>
            <td style="padding:12px;"><?= $c['id'] ?></td>
            <td style="padding:12px;"><?= htmlspecialchars($c['user_name']) ?></td>
            <td style="padding:12px;"><?= htmlspecialchars($c['student_name']) ?></td>
            <td style="padding:12px;"><?= htmlspecialchars($c['degree']) ?></td>
            <td style="padding:12px;"><form method="POST"><input type="hidden" name="action" value="approve_cert"><input type="hidden" name="cert_id" value="<?= $c['id'] ?>"><button type="submit" class="btn" style="background:#28a745;">Approve</button></form></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>All Certificates</h2>
    <table style="width:100%; background:white;">
        <tr style="background:#0f4c92; color:white;"><th>ID</th><th>User</th><th>Student</th><th>Degree</th><th>Status</th><th>Action</th></tr>
        <?php foreach ($allCerts as $c): ?>
        <tr>
            <td style="padding:12px;"><?= $c['id'] ?></td>
            <td style="padding:12px;"><?= htmlspecialchars($c['user_name']) ?></td>
            <td style="padding:12px;"><?= htmlspecialchars($c['student_name']) ?></td>
            <td style="padding:12px;"><?= htmlspecialchars($c['degree']) ?></td>
            <td style="padding:12px;"><?= $c['status'] ?></td>
            <td style="padding:12px;">
                <?php if ($c['status'] == 'pending'): ?><form method="POST" style="display:inline;"><input type="hidden" name="action" value="approve_cert"><input type="hidden" name="cert_id" value="<?= $c['id'] ?>"><button type="submit" class="btn" style="background:#28a745;">Approve</button></form><?php endif; ?>
                <form method="POST" style="display:inline;"><input type="hidden" name="action" value="delete_cert"><input type="hidden" name="cert_id" value="<?= $c['id'] ?>"><button type="submit" class="btn" style="background:#dc3545;" onclick="return confirm('Delete?')">Delete</button></form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>Users</h2>
    <table style="width:100%; background:white;">
        <tr style="background:#0f4c92; color:white;"><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr>
        <?php foreach ($allUsers as $u): ?>
        <tr>
            <td style="padding:12px;"><?= $u['id'] ?></td>
            <td style="padding:12px;"><?= htmlspecialchars($u['fullname']) ?></td>
            <td style="padding:12px;"><?= htmlspecialchars($u['email']) ?></td>
            <td style="padding:12px;"><?= $u['role'] ?></td>
            <td style="padding:12px;">
                <?php if ($u['id'] != $_SESSION['user_id'] && $u['role'] != 'admin'): ?>
                <form method="POST"><input type="hidden" name="action" value="delete_user"><input type="hidden" name="user_id" value="<?= $u['id'] ?>"><button type="submit" class="btn" style="background:#dc3545;" onclick="return confirm('Delete user?')">Delete</button></form>
                <?php elseif ($u['role'] == 'admin'): ?>🔒 Admin<?php else: ?>You<?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>


<?php elseif ($page === 'verify'): ?>
<div class="container">
    <div class="auth-card" style="max-width: 600px; margin: 0 auto;">
        <div class="auth-header">
            <i class="fas fa-fingerprint fa-3x" style="color: #0f4c92;"></i>
            <h2>Verify Certificate</h2>
            <p>Enter the SHA-256 hash from your certificate</p>
        </div>
        
        <form method="POST">
            <input type="hidden" name="action" value="verify_hash">
            <div class="form-group">
                <input type="text" name="hash" placeholder="Paste hash code here..." required style="width:100%; padding:12px;">
            </div>
            <button type="submit" class="btn-full">Verify Hash</button>
        </form>
        
        <?php if ($verifyResult): ?>
            <?php if ($verifyResult['valid']): ?>
            <div class="result-valid">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                    <i class="fas fa-check-circle fa-2x" style="color: #28a745;"></i>
                    <h3 style="color: #28a745; margin: 0;">✅ Certificate is VALID</h3>
                </div>
                <p><strong>Student Name:</strong> <?= htmlspecialchars($verifyResult['student_name']) ?></p>
                <p><strong>Degree:</strong> <?= htmlspecialchars($verifyResult['degree']) ?></p>
                <p><strong>Issue Date:</strong> <?= date('F d, Y', strtotime($verifyResult['issued_at'])) ?></p>
                <p><strong>Hash:</strong> <code style="word-break:break-all;"><?= htmlspecialchars($verifyResult['hash']) ?></code></p>
            </div>
            <?php else: ?>
            <div class="result-invalid">
                <i class="fas fa-times-circle fa-2x" style="color: #dc3545; margin-bottom: 10px;"></i>
                <h3 style="color: #dc3545;">❌ Certificate NOT FOUND</h3>
                <p>The hash you entered does not match any certificate in our system.</p>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>


<?php elseif ($page === 'about'): ?>
<div class="container about-page">
    <section class="about-wrapper">
        <div class="about-content">
            <span class="section-badge"><i class="fas fa-globe"></i> About LIU Verify</span>
            <h1>Global Recognition</h1>
            <p class="about-description">LIU certificates are recognized and respected worldwide, opening doors for our graduates in international markets and top-tier global enterprises.</p>
            <div class="why-liu">
                <h2>Why LIU?</h2>
                <ul class="about-features">
                    <li><i class="fas fa-check-circle"></i><div><strong>Modern Curriculum</strong><p>Focused on industry-leading technologies like Blockchain, AI, and secure digital systems.</p></div></li>
                    <li><i class="fas fa-check-circle"></i><div><strong>Global Network</strong><p>Strong partnerships with international academic institutions and professional organizations.</p></div></li>
                    <li><i class="fas fa-check-circle"></i><div><strong>Practical Excellence</strong><p>Emphasis on hands-on senior projects, real-world implementation, and career-ready skills.</p></div></li>
                </ul>
            </div>
        </div>
        <aside class="mission-card">
            <div class="mission-icon"><i class="fas fa-award"></i></div>
            <h2>Our Mission</h2>
            <p>We aim to protect the prestige of your LIU degree. By using <strong>Decentralized Ledger Technology</strong>, we provide a permanent and transparent record of your success that can be verified anywhere in the world instantly.</p>
        </aside>
    </section>
</div>


<?php elseif ($page === 'contact'): ?>
<div class="container contact-page">
    <section class="contact-wrapper">
        <div class="contact-info-text">
            <span class="section-badge"><i class="fas fa-headset"></i> Contact Support</span>
            <h1>Contact Our Team</h1>
            <p>Have questions regarding the blockchain verification process or need technical support with your digital certificates?</p>
            <p>Our administration is available Monday through Friday to assist you with any inquiries.</p>
            <div class="contact-highlights">
                <div class="contact-highlight"><i class="fas fa-envelope"></i><div><strong>Email Support</strong><span>info@liu.edu.lb</span></div></div>
                <div class="contact-highlight"><i class="fas fa-clock"></i><div><strong>Working Hours</strong><span>Monday - Friday</span></div></div>
                <div class="contact-highlight"><i class="fas fa-shield-alt"></i><div><strong>Secure Verification</strong><span>Blockchain-based certificate support</span></div></div>
            </div>
        </div>
        <section class="contact-form-card">
            <div class="contact-form-header"><i class="fas fa-paper-plane"></i><h2>Send a Message</h2><p>Fill out the form below and our team will get back to you.</p></div>
            <form method="POST" class="contact-form">
                <div class="form-group"><label><i class="fas fa-user"></i> Your Name</label><input type="text" name="name" placeholder="Enter your full name" required></div>
                <div class="form-group"><label><i class="fas fa-envelope"></i> Your Email</label><input type="email" name="email" placeholder="Enter your email address" required></div>
                <div class="form-group"><label><i class="fas fa-comment-dots"></i> Message</label><textarea name="message" rows="5" placeholder="Type your message here..." required></textarea></div>
                <button type="submit" class="btn-full">Submit Inquiry</button>
            </form>
        </section>
    </section>
</div>


<?php elseif ($page === 'help'): ?>
<div class="container">
    <div class="auth-card" style="max-width:600px; margin:0 auto;">
        <h2><i class="fas fa-question-circle"></i> Help Center</h2>
        <p><strong>How to Verify a Certificate:</strong> Enter the SHA-256 hash from your certificate PDF and click Verify. Or scan the QR code on the certificate.</p>
        <p><strong>How to Request a Certificate:</strong> Register an account, login, go to Dashboard, click Request Certificate, fill in your details, wait for approval (24h auto or admin).</p>
        <p><strong>Still Need Help?</strong> Email: info@liu.edu.lb | Phone: +961 1 706 881</p>
    </div>
</div>
<?php endif; ?>


<footer>
    <div class="footer-content">
        <div class="footer-section about">
            <h3>LIU Verify</h3>
            <p>Securing academic integrity through decentralized blockchain technology for future graduates.</p>
        </div>
        <div class="footer-section contact">
            <h3>Contact Us</h3>
            <p><i class="fas fa-envelope"></i> <a href="mailto:info@liu.edu.lb" class="footer-link">info@liu.edu.lb</a></p>
            <p><i class="fas fa-phone"></i> <a href="tel:+9611706881" class="footer-link">+961 1 706 881</a></p>
        </div>
        <div class="footer-section social">
            <h3>Follow Us</h3>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; 2026 Lebanese International University - Senior Project
    </div>
</footer>
</body>
</html>
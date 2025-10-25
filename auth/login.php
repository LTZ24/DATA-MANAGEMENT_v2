<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/config.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    redirect(BASE_URL . '/index.php');
}

$client = getGoogleClient();
$authUrl = $client->createAuthUrl();

// Check for session timeout message
$sessionTimeout = isset($_GET['session_timeout']) ? true : false;
$logoutReason = isset($_GET['reason']) ? $_GET['reason'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/smk62.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body.login-page {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 1000px;
        }
        
        .login-box {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 500px;
        }
        
        .login-left {
            background: linear-gradient(135deg, #50e3c2 0%, #4dd0e1 100%);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
        }
        
        .login-left img {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin-bottom: 1.5rem;
            background: white;
            border-radius: 50%;
            padding: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .login-left h1 {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        
        .login-left p {
            font-size: 1rem;
            opacity: 0.95;
            margin-bottom: 2rem;
        }
        
        .login-left .features {
            text-align: left;
            width: 100%;
        }
        
        .login-left .features ul {
            list-style: none;
        }
        
        .login-left .features li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
        }
        
        .login-left .features li i {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .login-right {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h2 {
            font-size: 1.75rem;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: #718096;
            font-size: 0.95rem;
        }
        
        .alert-timeout {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: start;
            gap: 0.75rem;
            font-size: 0.875rem;
            animation: slideDown 0.3s ease;
        }
        
        .alert-timeout i {
            font-size: 1.25rem;
            margin-top: 2px;
        }
        
        .google-btn {
            background: white;
            color: #444;
            border: 2px solid #ddd;
            padding: 14px 24px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .google-btn:hover {
            background: #f8f9fa;
            border-color: #50e3c2;
            box-shadow: 0 4px 12px rgba(80, 227, 194, 0.2);
            transform: translateY(-2px);
        }
        
        .google-icon {
            width: 20px;
            height: 20px;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }
        
        .login-footer p {
            color: #718096;
            font-size: 0.875rem;
            margin: 0;
        }
        
        .login-footer a {
            transition: color 0.2s;
        }
        
        .login-footer a:hover {
            color: #4dd0e1;
            text-decoration: underline;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .login-box {
                grid-template-columns: 1fr;
                min-height: auto;
            }
            
            .login-left {
                padding: 2rem 1.5rem;
            }
            
            .login-left img {
                width: 80px;
                height: 80px;
                padding: 10px;
            }
            
            .login-left h1 {
                font-size: 1.25rem;
            }
            
            .login-left p {
                font-size: 0.875rem;
                margin-bottom: 1rem;
            }
            
            .login-left .features {
                display: none;
            }
            
            .login-right {
                padding: 2rem 1.5rem;
            }
            
            .login-header h2 {
                font-size: 1.375rem;
            }
            
            .login-header p {
                font-size: 0.875rem;
            }
            
            .google-btn {
                padding: 12px 20px;
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <!-- Left Panel -->
            <div class="login-left">
                <img src="<?php echo BASE_URL; ?>/assets/images/smk62.png" alt="Logo SMKN 62">
                <h1>Database Guru</h1>
                <p>SMKN 62 Jakarta</p>
                
                <div class="features">
                    <ul>
                        <li>
                            <i class="fas fa-shield-alt"></i>
                            <span>Login aman dengan Google OAuth</span>
                        </li>
                        <li>
                            <i class="fas fa-cloud"></i>
                            <span>Data tersimpan di Google Cloud</span>
                        </li>
                        <li>
                            <i class="fas fa-users"></i>
                            <span>Manajemen data guru terintegrasi</span>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Akses 24/7 dari mana saja</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Right Panel -->
            <div class="login-right">
                <div class="login-header">
                    <h2>Selamat Datang</h2>
                    <p>Login untuk mengakses sistem</p>
                </div>
                
                <?php if ($sessionTimeout || $logoutReason === 'timeout'): ?>
                    <div class="alert-timeout">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Sesi Berakhir</strong><br>
                            <small>Sesi telah berakhir karena tidak ada aktivitas selama 30 menit.</small>
                        </div>
                    </div>
                <?php endif; ?>
                
                <a href="<?php echo htmlspecialchars($authUrl); ?>" class="google-btn">
                    <svg class="google-icon" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Masuk dengan Google
                </a>
                
                <div class="login-footer">
                    <p style="margin: 0; line-height: 1.8;">
                        <a href="<?php echo BASE_URL; ?>/privacy.php" style="color: #50e3c2; text-decoration: none; margin: 0 8px;">Privacy Policy</a>
                        <span style="color: #cbd5e0;">•</span>
                        <a href="<?php echo BASE_URL; ?>/terms.php" style="color: #50e3c2; text-decoration: none; margin: 0 8px;">Terms of Service</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>

<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/config.php';

requireLogin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - <?php echo APP_NAME; ?></title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/smk62.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .profile-header {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .profile-picture {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            object-fit: cover;
            border: 4px solid var(--primary-color);
        }
        
        .profile-picture-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
        }
        
        .profile-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .profile-email {
            color: var(--text-color);
            font-size: 1rem;
        }
        
        .profile-info {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .profile-info h2 {
            font-size: 1.5rem;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .info-row {
            display: flex;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            flex: 0 0 200px;
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-value {
            flex: 1;
            color: var(--text-color);
        }
        
        .info-note {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            padding: 1.5rem;
            border-radius: 12px;
            margin-top: 2rem;
        }
        
        .info-note h3 {
            color: #1565c0;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.125rem;
        }
        
        .info-note ul {
            color: #1565c0;
            line-height: 2;
            padding-left: 1.5rem;
        }
        
        .info-note a {
            color: #1565c0;
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .info-row {
                flex-direction: column;
            }
            
            .info-label {
                flex: 1;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include __DIR__ . '/../includes/header.php'; ?>
        
        <div class="content-wrapper">
            <div class="profile-container">
                
                <div class="profile-header">
                    <?php if (isset($_SESSION['user_picture']) && !empty($_SESSION['user_picture'])): ?>
                        <img src="<?php echo htmlspecialchars($_SESSION['user_picture']); ?>" 
                             alt="Profile Picture" 
                             class="profile-picture">
                    <?php else: ?>
                        <div class="profile-picture-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="profile-name">
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                    </div>
                    <div class="profile-email">
                        <?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>
                    </div>
                </div>
                
                <div class="profile-info">
                    <h2 style="margin-bottom: 1.5rem;">
                        <i class="fas fa-info-circle"></i> Informasi Akun
                    </h2>
                    
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-user"></i> Nama Lengkap
                        </div>
                        <div class="info-value">
                            <?php echo htmlspecialchars($_SESSION['user_name'] ?? '-'); ?>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-envelope"></i> Email
                        </div>
                        <div class="info-value">
                            <?php echo htmlspecialchars($_SESSION['user_email'] ?? '-'); ?>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-id-badge"></i> Google ID
                        </div>
                        <div class="info-value">
                            <?php echo htmlspecialchars($_SESSION['user_id'] ?? '-'); ?>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-sign-in-alt"></i> Login Terakhir
                        </div>
                        <div class="info-value">
                            <?php 
                            if (isset($_SESSION['created'])) {
                                echo date('d-m-Y H:i:s', $_SESSION['created']);
                            } else {
                                echo '-';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-shield-alt"></i> Provider
                        </div>
                        <div class="info-value">
                            <i class="fab fa-google" style="color: #4285f4;"></i> Google OAuth 2.0
                        </div>
                    </div>
                </div>
                
                <div class="info-note">
                    <h3>
                        <i class="fas fa-info-circle"></i> Catatan
                    </h3>
                    <ul>
                        <li>Data profil Anda dikelola oleh Google</li>
                        <li>Untuk mengubah informasi profil, silakan kunjungi <a href="https://myaccount.google.com/" target="_blank">Google Account</a></li>
                        <li>Foto profil akan otomatis tersinkronisasi dengan akun Google Anda</li>
                    </ul>
                </div>
            </div>
            
            <?php include __DIR__ . '/../includes/footer.php'; ?>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/i18n.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>

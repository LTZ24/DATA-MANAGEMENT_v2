<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/config.php';

requireLogin();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'clear_cache':
                $essential = [
                    'access_token',
                    'user_id',
                    'user_email',
                    'user_name',
                    'user_picture',
                    'created',
                    'last_activity'
                ];
                
                foreach ($_SESSION as $key => $value) {
                    if (!in_array($key, $essential)) {
                        unset($_SESSION[$key]);
                    }
                }
                
                $success = 'Cache berhasil dibersihkan!';
                break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - <?php echo APP_NAME; ?></title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/smk62.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .settings-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .settings-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .settings-section h2 {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
            font-size: 1.5rem;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .setting-item {
            padding: 1.5rem 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .setting-item:last-child {
            border-bottom: none;
        }
        
        .setting-info {
            flex: 1;
            min-width: 250px;
        }
        
        .setting-info h3 {
            font-size: 1.125rem;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
            font-weight: 600;
        }
        
        .setting-info p {
            color: var(--text-color);
            font-size: 0.875rem;
            line-height: 1.5;
        }
        
        .setting-action {
            flex-shrink: 0;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .badge-success {
            background: #10b981;
            color: white;
        }
        
        /* Dark Mode Toggle */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.3s;
            border-radius: 30px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }
        
        .toggle-switch input:checked + .toggle-slider {
            background-color: var(--primary-color);
        }
        
        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(30px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .settings-section {
                padding: 1.5rem;
            }
            
            .setting-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .setting-action {
                width: 100%;
            }
            
            .setting-action button,
            .setting-action form {
                width: 100%;
            }
            
            .setting-action button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include __DIR__ . '/../includes/header.php'; ?>
        
        <div class="content-wrapper">
            <?php include __DIR__ . '/../includes/page-navigation.php'; ?>
            <div class="settings-container">
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Data & Cache -->
                <div class="settings-section">
                    <h2><i class="fas fa-database"></i> Data & Cache</h2>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Bersihkan Cache</h3>
                            <p>Hapus data cache untuk meningkatkan performa aplikasi</p>
                        </div>
                        <div class="setting-action">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="clear_cache">
                                <button type="submit" class="btn btn-primary" onclick="return confirm('Yakin ingin membersihkan cache?')">
                                    <i class="fas fa-trash-alt"></i> Bersihkan
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Sinkronisasi Data</h3>
                            <p>Real-time dengan Google Sheets</p>
                        </div>
                        <div class="setting-action">
                            <span class="badge badge-success">
                                <i class="fas fa-check"></i> Aktif
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Tentang Aplikasi -->
                <div class="settings-section">
                    <h2><i class="fas fa-info-circle"></i> Tentang Aplikasi</h2>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Nama Aplikasi</h3>
                            <p><?php echo APP_NAME; ?></p>
                        </div>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Versi</h3>
                            <p><?php echo APP_VERSION; ?></p>
                        </div>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Database</h3>
                            <p>Google Sheets API (Real-time Sync)</p>
                        </div>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Storage</h3>
                            <p>Google Drive API</p>
                        </div>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Autentikasi</h3>
                            <p>Login menggunakan Google OAuth 2.0</p>
                        </div>
                        <div class="setting-action">
                            <span class="badge badge-success">
                                <i class="fab fa-google"></i> Terkoneksi
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php include __DIR__ . '/../includes/footer.php'; ?>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>

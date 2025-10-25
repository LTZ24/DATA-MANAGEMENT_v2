<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/config.php';

requireLogin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Sheets - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sheets-info {
            background: var(--white);
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        
        .sheets-preview {
            background: var(--white);
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: var(--shadow);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .info-item {
            background: var(--light-color);
            padding: 1rem;
            border-radius: 0.375rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .info-label {
            font-size: 0.875rem;
            color: var(--secondary-color);
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark-color);
            word-break: break-all;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/header.php'; ?>
    
    <div class="container">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
        
        <main class="main-content">
            <h1>Google Sheets Database</h1>
            <p>Informasi dan akses ke Google Sheets yang digunakan untuk menyimpan data</p>
            
            <div class="sheets-info">
                <h2><i class="fas fa-table"></i> Informasi Sheets</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Spreadsheet ID</div>
                        <div class="info-value" style="font-size: 0.875rem;">
                            <?php echo GOOGLE_SHEETS_ID; ?>
                        </div>
                    </div>
                    <div class="info-item" style="border-left-color: var(--success-color);">
                        <div class="info-label">Status</div>
                        <div class="info-value" style="color: var(--success-color);">
                            <i class="fas fa-check-circle"></i> Aktif
                        </div>
                    </div>
                    <div class="info-item" style="border-left-color: var(--info-color);">
                        <div class="info-label">Sinkronisasi</div>
                        <div class="info-value" style="color: var(--info-color);">
                            <i class="fas fa-sync"></i> Real-time
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 2rem;">
                    <a href="https://docs.google.com/spreadsheets/d/<?php echo GOOGLE_SHEETS_ID; ?>" 
                       target="_blank" 
                       class="btn btn-primary">
                        <i class="fas fa-external-link-alt"></i> Buka Google Sheets
                    </a>
                </div>
            </div>
            
            <div class="sheets-preview">
                <h2><i class="fas fa-list"></i> Sheet yang Tersedia</h2>
                <div style="display: grid; gap: 1rem; margin-top: 1rem;">
                    <div style="background: var(--light-color); padding: 1.5rem; border-radius: 0.375rem;">
                        <h3 style="margin-bottom: 0.5rem;">
                            <i class="fas fa-link"></i> Links
                        </h3>
                        <p style="color: var(--secondary-color); margin-bottom: 1rem;">
                            Menyimpan data links/tautan penting
                        </p>
                        <div style="font-size: 0.875rem; color: var(--secondary-color);">
                            Kolom: Title, URL, Created At, Updated At
                        </div>
                    </div>
                    
                    <div style="background: var(--light-color); padding: 1.5rem; border-radius: 0.375rem;">
                        <h3 style="margin-bottom: 0.5rem;">
                            <i class="fas fa-file-alt"></i> Forms
                        </h3>
                        <p style="color: var(--secondary-color); margin-bottom: 1rem;">
                            Menyimpan data Google Forms
                        </p>
                        <div style="font-size: 0.875rem; color: var(--secondary-color);">
                            Kolom: Title, URL, Created At, Updated At
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 1.5rem; border-radius: 0.5rem; margin-top: 2rem;">
                <h3 style="color: #856404; margin-bottom: 1rem;">
                    <i class="fas fa-exclamation-triangle"></i> Penting!
                </h3>
                <ul style="color: #856404; line-height: 2;">
                    <li>Jangan mengubah struktur sheet (header kolom) secara manual</li>
                    <li>Gunakan aplikasi ini untuk mengelola data</li>
                    <li>Backup data secara berkala</li>
                    <li>Pastikan permission sheet sudah diatur dengan benar</li>
                </ul>
            </div>
            
            <?php include __DIR__ . '/../../includes/footer.php'; ?>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>

<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/config.php';

requireLogin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Database Guru SMKN 62 Jakarta</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#50e3c2">
    <meta name="description" content="Sistem Manajemen Database Guru SMK Negeri 62 Jakarta">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="DB Guru 62">
    
    <!-- Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/icons/favicon-16x16.png">
    <link rel="apple-touch-icon" href="assets/images/icons/apple-touch-icon.png">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="manifest.json">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content-wrapper">
            <div class="dashboard">
                <h1>Dashboard</h1>
                <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['username'] ?? 'User'); ?>!</p>
                
                <?php
                $cacheTime = 300;
                
                if (!isset($_SESSION['dashboard_cache']) || 
                    !isset($_SESSION['dashboard_cache_time']) || 
                    (time() - $_SESSION['dashboard_cache_time']) > $cacheTime) {
                    
                    $links = getLinksFromSheets();
                    $forms = getFormsFromSheets();
                    
                    try {
                        $client = getGoogleClient();
                        $driveService = new Google_Service_Drive($client);
                        
                        $about = $driveService->about->get(['fields' => 'storageQuota']);
                        $quota = $about->getStorageQuota();
                        $storagePercent = round(($quota->getUsage() / $quota->getLimit()) * 100, 2);
                        $storageUsed = formatFileSize($quota->getUsage());
                        $storageTotal = formatFileSize($quota->getLimit());
                        $storageRemaining = formatFileSize($quota->getLimit() - $quota->getUsage());
                        
                        $filesResult = $driveService->files->listFiles([
                            'q' => "mimeType != 'application/vnd.google-apps.folder' and trashed = false",
                            'fields' => 'files(id)',
                            'pageSize' => 100
                        ]);
                        $totalFiles = count($filesResult->getFiles());
                        
                        $recentFiles = $driveService->files->listFiles([
                            'q' => "mimeType != 'application/vnd.google-apps.folder' and trashed = false",
                            'orderBy' => 'createdTime desc',
                            'fields' => 'files(id, name, createdTime, mimeType, size)',
                            'pageSize' => 5
                        ]);
                        $uploads = $recentFiles->getFiles();
                        
                    } catch (Exception $e) {
                        $storagePercent = 0;
                        $storageUsed = '0 KB';
                        $storageTotal = '0 KB';
                        $storageRemaining = '0 KB';
                        $totalFiles = 0;
                        $uploads = [];  
                    }
                    
                    $_SESSION['dashboard_cache'] = [
                        'links' => $links,
                        'forms' => $forms,
                        'storagePercent' => $storagePercent,
                        'storageUsed' => $storageUsed,
                        'storageTotal' => $storageTotal,
                        'storageRemaining' => $storageRemaining,
                        'totalFiles' => $totalFiles,
                        'uploads' => $uploads
                    ];
                    $_SESSION['dashboard_cache_time'] = time();
                    
                } else {
                    $cache = $_SESSION['dashboard_cache'];
                    $links = $cache['links'];
                    $forms = $cache['forms'];
                    $storagePercent = $cache['storagePercent'];
                    $storageUsed = $cache['storageUsed'];
                    $storageTotal = $cache['storageTotal'];
                    $storageRemaining = $cache['storageRemaining'];
                    $totalFiles = $cache['totalFiles'];
                    $uploads = $cache['uploads'];
                }
                ?>
                
                <div class="stats-grid">
                    <!-- Card 1: Storage Google Drive -->
                    <div class="stat-card storage-card">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                            <i class="fas fa-hdd" style="font-size: 2.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h3 style="margin: 0; font-size: 1rem;">Storage Google Drive</h3>
                                <p style="margin: 0; font-size: 0.875rem; color: var(--secondary-color);">
                                    <?php echo $storageRemaining; ?> tersisa terpakai dari <?php echo $storageTotal; ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div style="position: relative; background: #e5e7eb; height: 10px; border-radius: 1rem; overflow: hidden; box-shadow: inset 0 2px 4px rgba(0,0,0,0.06); margin-bottom: 1rem;">
                            <div style="
                                background: linear-gradient(90deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);
                                height: 100%;
                                width: <?php echo $storagePercent; ?>%;
                                transition: width 0.6s ease;
                                border-radius: 1rem;
                                box-shadow: 0 0 8px rgba(59, 130, 246, 0.4);
                            "></div>
                        </div>
                        
                        <!-- Storage Details dengan Icon -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <div>
                                <div style="font-size: 0.75rem; color: var(--secondary-color); margin-bottom: 0.25rem;">
                                    <i class="fas fa-database" style="color: #3b82f6; font-size: 0.75rem; margin-right: 0.25rem;"></i>
                                    Terpakai
                                </div>
                                <div style="font-size: 0.95rem; font-weight: 600; color: var(--dark-color);">
                                    <?php echo $storageUsed; ?> (<?php echo $storagePercent; ?>%)
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 0.75rem; color: var(--secondary-color); margin-bottom: 0.25rem;">
                                    <i class="fas fa-cloud" style="color: #10b981; font-size: 0.75rem; margin-right: 0.25rem;"></i>
                                    Tersedia
                                </div>
                                <div style="font-size: 0.95rem; font-weight: 600; color: #10b981;">
                                    <?php echo $storageRemaining; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card 2: Recent Uploads -->
                    <div class="stat-card upload-card">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                            <i class="fas fa-clock-rotate-left" style="font-size: 2.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h3 style="margin: 0; font-size: 1rem;">Upload Terbaru</h3>
                                <p style="margin: 0; font-size: 0.875rem; color: var(--secondary-color);">
                                    Total <?php echo number_format($totalFiles); ?> file
                                </p>
                            </div>
                        </div>
                        
                        <!-- Upload History Table -->
                        <div style="max-height: 140px; overflow-y: auto;">
                            <?php if (empty($uploads)): ?>
                                <div style="text-align: center; padding: 1.5rem; color: var(--secondary-color); font-size: 0.875rem;">
                                    <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem; display: block; opacity: 0.5;"></i>
                                    Belum ada upload
                                </div>
                            <?php else: ?>
                                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    <?php foreach ($uploads as $file): 
                                        $date = new DateTime($file->getCreatedTime());
                                        $timeAgo = $date->format('d/m/y H:i');
                                    ?>
                                        <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: var(--light-color); border-radius: 0.5rem; font-size: 0.8rem;">
                                            <i class="fas fa-file" style="color: #3b82f6; font-size: 0.9rem;"></i>
                                            <div style="flex: 1; min-width: 0;">
                                                <div style="font-weight: 600; color: var(--dark-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    <?php echo htmlspecialchars(strlen($file->getName()) > 20 ? substr($file->getName(), 0, 20) . '...' : $file->getName()); ?>
                                                </div>
                                                <div style="color: var(--secondary-color); font-size: 0.75rem;">
                                                    <?php echo $timeAgo; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="quick-actions">
                    <h2>Aksi Cepat</h2>
                    <div class="action-buttons">
                        <a href="<?php echo BASE_URL; ?>/pages/links/index.php" class="btn btn-primary">
                            <i class="fas fa-link"></i> Kelola Links
                        </a>
                        <a href="<?php echo BASE_URL; ?>/pages/forms/index.php" class="btn btn-secondary">
                            <i class="fas fa-file-alt"></i> Kelola Forms
                        </a>
                        <a href="<?php echo BASE_URL; ?>/pages/files/index.php" class="btn btn-success">
                            <i class="fas fa-folder-open"></i> File Manager
                        </a>
                        <button onclick="refreshPage()" class="btn btn-info" type="button">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
            
            <?php include 'includes/footer.php'; ?>
        </div> <!-- End content-wrapper -->
    </div> <!-- End main-content -->
    
    <script src="assets/js/main.js"></script>
    <script src="assets/js/pwa.js"></script>
    <script>
        function refreshPage() {
            const icon = event.target.classList.contains('fa-sync-alt') 
                ? event.target 
                : event.target.querySelector('i');
            
            if (icon) {
                icon.classList.add('fa-spin');
            }
            
            setTimeout(() => {
                window.location.reload();
            }, 800);
        }
    </script>
</body>
</html>

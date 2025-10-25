<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/config.php';

requireLogin();

$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

// Get links from Google Sheets
$links = getLinksFromSheets();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Links - <?php echo APP_NAME; ?></title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/smk62.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/ajax.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .links-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        
        .links-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .links-header h2 {
            font-size: 1.5rem;
            color: var(--dark-color);
            margin: 0;
        }
        
        .links-grid {
            display: grid;
            gap: 1rem;
        }
        
        .link-item {
            background: var(--light-color);
            padding: 1.5rem;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            gap: 1rem;
        }
        
        .link-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .link-info {
            flex: 1;
            min-width: 0;
        }
        
        .link-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .link-url {
            color: var(--primary-color);
            font-size: 0.875rem;
            word-break: break-all;
            text-decoration: none;
        }
        
        .link-url:hover {
            text-decoration: underline;
        }
        
        .link-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-color);
        }
        
        .empty-state i {
            font-size: 3rem;
            display: block;
            margin-bottom: 1rem;
            color: var(--primary-color);
            opacity: 0.5;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .links-container {
                padding: 1.5rem;
            }
            
            .link-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .link-actions {
                width: 100%;
            }
            
            .link-actions a {
                flex: 1;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include __DIR__ . '/../../includes/header.php'; ?>
        
        <div class="content-wrapper">
            
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
            
            <div class="links-container">
                <div class="links-header">
                    <h2>Daftar Links (<?php echo count($links); ?>)</h2>
                    <a href="add.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Link
                    </a>
                </div>
                
                <?php if (empty($links)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada link. Klik tombol "Tambah Link" untuk menambahkan.</p>
                    </div>
                <?php else: ?>
                    <div class="links-grid">
                        <?php foreach ($links as $link): ?>
                            <div class="link-item">
                                <div class="link-info">
                                    <div class="link-title">
                                        <i class="fas fa-link"></i>
                                        <?php echo htmlspecialchars($link['title']); ?>
                                    </div>
                                    <a href="<?php echo htmlspecialchars($link['url']); ?>" 
                                       target="_blank" 
                                       class="link-url">
                                        <?php echo htmlspecialchars($link['url']); ?>
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <div style="font-size: 0.75rem; color: var(--secondary-color); margin-top: 0.5rem;">
                                        <i class="fas fa-clock"></i>
                                        Dibuat: <?php echo formatDateTime($link['created_at']); ?>
                                    </div>
                                </div>
                                <div class="link-actions">
                                    <a href="edit.php?id=<?php echo $link['id']; ?>" 
                                       class="btn btn-primary btn-sm" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" 
                                          action="delete.php?id=<?php echo $link['id']; ?>" 
                                          style="display: inline;"
                                          data-ajax="true"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus link ini?');">
                                        <input type="hidden" name="confirm" value="1">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php include __DIR__ . '/../../includes/footer.php'; ?>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/i18n.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/ajax.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>

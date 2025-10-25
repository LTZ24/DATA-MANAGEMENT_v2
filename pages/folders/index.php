<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/config.php';

requireLogin();

$categories = getDriveCategories();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Folder Google Drive - <?php echo APP_NAME; ?></title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/smk62.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .folders-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .folder-card {
            background: var(--white);
            border-radius: 0.5rem;
            padding: 2rem;
            box-shadow: var(--shadow);
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .folder-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        
        .folder-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .folder-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }
        
        .folder-id {
            font-size: 0.75rem;
            color: var(--secondary-color);
            word-break: break-all;
            background: var(--light-color);
            padding: 0.5rem;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }
        
        .folder-link {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: var(--primary-color);
            color: var(--white);
            border-radius: 0.375rem;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .folder-link:hover {
            background: #1d4ed8;
            color: var(--white);
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/header.php'; ?>
    
    <div class="container">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
        
        <main class="main-content">
            <h1>Folder Google Drive</h1>
            <p>Akses cepat ke folder-folder penting di Google Drive SMKN 62 Jakarta</p>
            
            <div class="folders-grid">
                <?php foreach ($categories as $key => $category): ?>
                    <div class="folder-card" style="border-top: 4px solid <?php echo $category['color']; ?>">
                        <div class="folder-icon" style="color: <?php echo $category['color']; ?>">
                            <i class="fas <?php echo $category['icon']; ?>"></i>
                        </div>
                        <div class="folder-name"><?php echo $category['name']; ?></div>
                        <a href="https://drive.google.com/drive/folders/<?php echo $category['folder_id']; ?>" 
                           target="_blank" 
                           class="folder-link">
                            <i class="fas fa-folder-open"></i> Buka Folder
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="background: var(--white); padding: 2rem; border-radius: 0.5rem; box-shadow: var(--shadow); margin-top: 2rem;">
                <h2><i class="fas fa-info-circle"></i> Informasi</h2>
                <ul style="line-height: 2; color: var(--dark-color);">
                    <li><i class="fas fa-check-circle" style="color: var(--success-color);"></i> Klik "Buka Folder" untuk mengakses folder Google Drive</li>
                    <li><i class="fas fa-check-circle" style="color: var(--success-color);"></i> Pastikan Anda memiliki akses ke folder tersebut</li>
                    <li><i class="fas fa-check-circle" style="color: var(--success-color);"></i> Folder akan terbuka di tab baru</li>
                    <li><i class="fas fa-check-circle" style="color: var(--success-color);"></i> Login dengan akun Google yang memiliki akses</li>
                </ul>
            </div>
            
            <?php include __DIR__ . '/../../includes/footer.php'; ?>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>

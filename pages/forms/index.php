<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/config.php';

requireLogin();

$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

// Get forms from Google Sheets
$forms = getFormsFromSheets();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Forms - <?php echo APP_NAME; ?></title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/smk62.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .forms-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        
        .forms-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .forms-header h2 {
            font-size: 1.5rem;
            color: var(--dark-color);
            margin: 0;
        }
        
        .forms-grid {
            display: grid;
            gap: 1rem;
        }
        
        .form-item {
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
        
        .form-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .form-info {
            flex: 1;
            min-width: 0;
        }
        
        .form-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-url {
            color: var(--primary-color);
            font-size: 0.875rem;
            word-break: break-all;
            text-decoration: none;
        }
        
        .form-url:hover {
            text-decoration: underline;
        }
        
        .form-actions {
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
            .forms-container {
                padding: 1.5rem;
            }
            
            .form-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .form-actions {
                width: 100%;
            }
            
            .form-actions a,
            .form-actions button {
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
            
            <div class="forms-container">
                <div class="forms-header">
                    <h2>Daftar Forms (<?php echo count($forms); ?>)</h2>
                    <a href="add.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Form
                    </a>
                </div>
                
                <?php if (empty($forms)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada form. Klik tombol "Tambah Form" untuk menambahkan.</p>
                    </div>
                <?php else: ?>
                    <div class="forms-grid">
                        <?php foreach ($forms as $form): ?>
                            <div class="form-item">
                                <div class="form-info">
                                    <div class="form-title">
                                        <i class="fas fa-file-alt"></i>
                                        <?php echo htmlspecialchars($form['title']); ?>
                                    </div>
                                    <a href="<?php echo htmlspecialchars($form['url']); ?>" 
                                       target="_blank" 
                                       class="form-url">
                                        <?php echo htmlspecialchars($form['url']); ?>
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <div style="font-size: 0.75rem; color: var(--secondary-color); margin-top: 0.5rem;">
                                        <i class="fas fa-clock"></i>
                                        Dibuat: <?php echo formatDateTime($form['created_at']); ?>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <a href="edit.php?id=<?php echo $form['id']; ?>" 
                                       class="btn btn-primary btn-sm" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="delete.php?id=<?php echo $form['id']; ?>" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus form ini?');">
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
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>

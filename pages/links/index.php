<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/config.php';

requireLogin();

$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

$cacheTime = 300;

$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';

$categories = getLinkCategories();

$cacheKey = 'links_cache_' . ($selectedCategory ?: 'all');

if (isset($_SESSION[$cacheKey]) && 
    isset($_SESSION[$cacheKey . '_time']) && 
    (time() - $_SESSION[$cacheKey . '_time']) < $cacheTime) {
    $links = $_SESSION[$cacheKey];
} else {
    if ($selectedCategory && isset($categories[$selectedCategory])) {
        $links = getLinksFromSheets($selectedCategory);
    } else {
        $links = [];
        foreach ($categories as $key => $category) {
            $categoryLinks = getLinksFromSheets($key);
            foreach ($categoryLinks as $link) {
                $link['category'] = $key;
                $link['category_name'] = $category['name'];
                $link['category_color'] = $category['color'];
                $links[] = $link;
            }
        }
    }
    $_SESSION[$cacheKey] = $links;
    $_SESSION[$cacheKey . '_time'] = time();
}
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
        
        /* Category Filter */
        .category-filter {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--border-color);
        }
        
        .category-filter-dropdown {
            display: none;
            width: 100%;
            max-width: 100%;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--border-color);
            box-sizing: border-box;
        }
        
        .category-filter-dropdown select {
            width: 100%;
            max-width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 1rem;
            font-size: 0.95rem;
            font-weight: 500;
            background: white;
            color: var(--dark-color);
            cursor: pointer;
            transition: all 0.3s;
            box-sizing: border-box;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M10.293 3.293L6 7.586 1.707 3.293A1 1 0 00.293 4.707l5 5a1 1 0 001.414 0l5-5a1 1 0 10-1.414-1.414z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
        }
        
        .category-filter-dropdown select:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .category-btn {
            padding: 0.625rem 1.25rem;
            border: 2px solid var(--border-color);
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--dark-color);
        }
        
        .category-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .category-btn.active {
            color: white;
            border-color: transparent;
        }
        
        .category-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
            margin-bottom: 0.5rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .links-container {
                padding: 1rem;
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
            
            /* Hide button filters, show dropdown on mobile */
            .category-filter {
                display: none;
            }
            
            .category-filter-dropdown {
                display: block;
                padding: 0 0.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include __DIR__ . '/../../includes/header.php'; ?>
        
        <div class="content-wrapper">
            <?php include __DIR__ . '/../../includes/page-navigation.php'; ?>
            
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
                
                <!-- Mobile Category Dropdown -->
                <div class="category-filter-dropdown">
                    <select onchange="window.location.href=this.value">
                        <option value="index.php" <?php echo empty($selectedCategory) ? 'selected' : ''; ?>>Semua Kategori</option>
                        <?php foreach ($categories as $key => $category): ?>
                            <option value="index.php?category=<?php echo $key; ?>" 
                                    <?php echo $selectedCategory === $key ? 'selected' : ''; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Desktop Category Buttons -->
                <div class="category-filter">
                    <a href="index.php" 
                       class="category-btn <?php echo empty($selectedCategory) ? 'active' : ''; ?>"
                       style="<?php echo empty($selectedCategory) ? 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);' : ''; ?>">
                        <i class="fas fa-th"></i>
                        Semua Kategori
                    </a>
                    <?php foreach ($categories as $key => $category): ?>
                        <a href="index.php?category=<?php echo $key; ?>" 
                           class="category-btn <?php echo $selectedCategory === $key ? 'active' : ''; ?>"
                           style="<?php echo $selectedCategory === $key ? 'background: ' . $category['color'] . ';' : ''; ?>">
                            <i class="fas <?php echo $category['icon']; ?>"></i>
                            <?php echo $category['name']; ?>
                        </a>
                    <?php endforeach; ?>
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
                                    <?php if (isset($link['category_name'])): ?>
                                        <div class="category-badge" style="background: <?php echo $link['category_color']; ?>">
                                            <i class="fas <?php echo $categories[$link['category']]['icon']; ?>"></i>
                                            <?php echo $link['category_name']; ?>
                                        </div>
                                    <?php endif; ?>
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
                                    <a href="edit.php?id=<?php echo $link['id']; ?>&category=<?php echo $link['category']; ?>" 
                                       class="btn btn-primary btn-sm" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" 
                                          action="delete.php?id=<?php echo $link['id']; ?>&category=<?php echo $link['category']; ?>" 
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
    
    <script src="<?php echo BASE_URL; ?>/assets/js/ajax.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>

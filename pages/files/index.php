<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/config.php';

requireLogin();

// Get all files from Google Drive
function getFilesFromDrive() {
    try {
        $client = getGoogleClient();
        $driveService = new Google_Service_Drive($client);
        
        $categories = [
            'kesiswaan' => ['name' => 'Kesiswaan', 'folder_id' => '1ek7EDGg525Nr6sT30yNhyLvphgXGN-3z', 'icon' => 'users', 'color' => '#3b82f6'],
            'kurikulum' => ['name' => 'Kurikulum', 'folder_id' => '1JlqjO6AxW2ML-FuP14f22wMmLSbASSzA', 'icon' => 'book', 'color' => '#10b981'],
            'sapras' => ['name' => 'Sapras & Humas', 'folder_id' => '13F-Cg44IpKOn-iWPpPfRq5A57Adf9VM6', 'icon' => 'building', 'color' => '#f59e0b'],
            'tata_usaha' => ['name' => 'Tata Usaha', 'folder_id' => '1P_z7_txZbvQX4yLJez4Zzh0gViBDHd30', 'icon' => 'briefcase', 'color' => '#8b5cf6']
        ];
        
        $allFiles = [];
        
        foreach ($categories as $key => $category) {
            $folderId = $category['folder_id'];
            
            $parameters = [
                'q' => "'{$folderId}' in parents and trashed=false",
                'fields' => 'files(id, name, mimeType, size, createdTime, modifiedTime, webViewLink, iconLink, thumbnailLink)',
                'orderBy' => 'modifiedTime desc',
                'pageSize' => 1000
            ];
            
            $results = $driveService->files->listFiles($parameters);
            $files = $results->getFiles();
            
            foreach ($files as $file) {
                $allFiles[] = [
                    'id' => $file->getId(),
                    'name' => $file->getName(),
                    'mimeType' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'createdTime' => $file->getCreatedTime(),
                    'modifiedTime' => $file->getModifiedTime(),
                    'webViewLink' => $file->getWebViewLink(),
                    'iconLink' => $file->getIconLink(),
                    'thumbnailLink' => $file->getThumbnailLink(),
                    'category' => $key,
                    'categoryName' => $category['name'],
                    'categoryIcon' => $category['icon'],
                    'categoryColor' => $category['color']
                ];
            }
        }
        
        return $allFiles;
    } catch (Exception $e) {
        return [];
    }
}

// Get drive storage info
function getDriveStorageInfo() {
    try {
        $client = getGoogleClient();
        $driveService = new Google_Service_Drive($client);
        
        $about = $driveService->about->get(['fields' => 'storageQuota']);
        $quota = $about->getStorageQuota();
        
        return [
            'limit' => $quota->getLimit(),
            'usage' => $quota->getUsage(),
            'usageInDrive' => $quota->getUsageInDrive(),
            'percent' => round(($quota->getUsage() / $quota->getLimit()) * 100, 2)
        ];
    } catch (Exception $e) {
        return null;
    }
}

$files = getFilesFromDrive();
$storageInfo = getDriveStorageInfo();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager - <?php echo APP_NAME; ?></title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/smk62.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/ajax.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .files-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            border: 1px solid #f3f4f6;
        }
        
        .files-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .filter-controls {
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            border: 1px solid #e5e7eb;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .filter-group label {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--dark-color);
        }
        
        .filter-group select,
        .filter-group input {
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        
        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .category-filters {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }
        
        .category-filter-dropdown {
            display: none;
            width: 100%;
            max-width: 100%;
            margin-bottom: 1.5rem;
            box-sizing: border-box;
        }
        
        .category-filter-dropdown select {
            width: 100%;
            max-width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
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
            border-color: #3b82f6;
        }
        
        .category-filter-btn {
            padding: 0.75rem 1.5rem;
            border: 2px solid #e5e7eb;
            background: white;
            border-radius: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--dark-color);
        }
        
        .category-filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: #3b82f6;
            background: #eff6ff;
        }
        
        .category-filter-btn.active {
            border-color: #3b82f6;
            background: #3b82f6;
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .files-table {
            width: 100%;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        thead {
            background: var(--light-color);
        }
        
        thead th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--dark-color);
            font-size: 0.9rem;
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
        }
        
        thead th:hover {
            background: var(--border-color);
        }
        
        thead th i {
            margin-left: 0.5rem;
            opacity: 0.5;
        }
        
        tbody tr {
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s;
        }
        
        tbody tr:hover {
            background: var(--light-color);
        }
        
        tbody td {
            padding: 1rem;
            font-size: 0.9rem;
        }
        
        .file-name-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .file-icon {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
        }
        
        .file-name {
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .category-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            color: var(--white);
        }
        
        .file-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
        }
        
        .storage-info {
            background: white;
            color: var(--dark-color);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .storage-info h3 {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--dark-color);
            font-size: 1.125rem;
        }
        
        .storage-info h3 i {
            color: var(--primary-color);
        }
        
        .storage-bar {
            background: #f3f4f6;
            height: 12px;
            border-radius: 1rem;
            overflow: hidden;
            margin-bottom: 0.75rem;
            border: 1px solid #e5e7eb;
        }
        
        .storage-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #2563eb);
            border-radius: 1rem;
            transition: width 0.5s ease;
            box-shadow: 0 0 8px rgba(59, 130, 246, 0.3);
        }
        
        .storage-stats {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: var(--text-color);
            font-weight: 500;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--secondary-color);
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .category-filters {
                display: none; /* Hide button filters on mobile */
            }
            
            .category-filter-dropdown {
                display: block; /* Show dropdown on mobile */
                padding: 0 0.5rem;
            }
            
            .filter-controls {
                grid-template-columns: 1fr;
                padding: 0 0.5rem;
            }
            
            .files-header {
                flex-direction: column;
                align-items: stretch;
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
            <div class="files-header">
                <a href="upload.php" class="btn btn-primary">
                    <i class="fas fa-cloud-upload-alt"></i> Upload File
                </a>
            </div>
            
            <?php if ($storageInfo): ?>
            <div class="storage-info">
                <h3>
                    <i class="fas fa-hdd"></i>
                    Penggunaan Storage Google Drive
                </h3>
                <div class="storage-bar">
                    <div class="storage-bar-fill" style="width: <?php echo $storageInfo['percent']; ?>%"></div>
                </div>
                <div class="storage-stats">
                    <span><?php echo formatFileSize($storageInfo['usage']); ?> terpakai</span>
                    <span><?php echo formatFileSize($storageInfo['limit']); ?> total (<?php echo $storageInfo['percent']; ?>%)</span>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="files-container">
                <!-- Mobile Category Dropdown -->
                <div class="category-filter-dropdown">
                    <select onchange="filterByCategory(this.value)">
                        <option value="all">Semua Kategori</option>
                        <option value="kesiswaan">Kesiswaan</option>
                        <option value="kurikulum">Kurikulum</option>
                        <option value="sapras">Sapras & Humas</option>
                        <option value="tata_usaha">Tata Usaha</option>
                    </select>
                </div>
                
                <!-- Desktop Category Buttons -->
                <div class="category-filters">
                    <button class="category-filter-btn active" onclick="filterByCategory('all')">
                        <i class="fas fa-th"></i> Semua Kategori
                    </button>
                    <button class="category-filter-btn" onclick="filterByCategory('kesiswaan')" data-color="#3b82f6">
                        <i class="fas fa-users"></i> Kesiswaan
                    </button>
                    <button class="category-filter-btn" onclick="filterByCategory('kurikulum')" data-color="#10b981">
                        <i class="fas fa-book"></i> Kurikulum
                    </button>
                    <button class="category-filter-btn" onclick="filterByCategory('sapras')" data-color="#f59e0b">
                        <i class="fas fa-building"></i> Sapras & Humas
                    </button>
                    <button class="category-filter-btn" onclick="filterByCategory('tata_usaha')" data-color="#8b5cf6">
                        <i class="fas fa-briefcase"></i> Tata Usaha
                    </button>
                </div>
                
                <div class="filter-controls">
                    <div class="filter-group">
                        <label><i class="fas fa-search"></i> Cari File</label>
                        <input type="text" id="searchInput" placeholder="Cari nama file..." onkeyup="filterFiles()">
                    </div>
                    <div class="filter-group">
                        <label><i class="fas fa-sort"></i> Urutkan</label>
                        <select id="sortSelect" onchange="sortFiles()">
                            <option value="modified_desc">Terakhir Diubah (Terbaru)</option>
                            <option value="modified_asc">Terakhir Diubah (Terlama)</option>
                            <option value="name_asc">Nama (A-Z)</option>
                            <option value="name_desc">Nama (Z-A)</option>
                            <option value="size_desc">Ukuran (Terbesar)</option>
                            <option value="size_asc">Ukuran (Terkecil)</option>
                            <option value="created_desc">Tanggal Upload (Terbaru)</option>
                            <option value="created_asc">Tanggal Upload (Terlama)</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label><i class="fas fa-calendar"></i> Filter Tanggal</label>
                        <input type="date" id="dateFilter" onchange="filterFiles()">
                    </div>
                    <div class="filter-group" style="display: flex; align-items: flex-end;">
                        <button class="btn btn-secondary" onclick="resetFilters()" style="width: 100%;">
                            <i class="fas fa-redo"></i> Reset Filter
                        </button>
                    </div>
                </div>
                
                <div class="files-table">
                    <?php if (empty($files)): ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>Belum Ada File</h3>
                            <p>Upload file pertama Anda dengan klik tombol "Upload File"</p>
                        </div>
                    <?php else: ?>
                        <table id="filesTable">
                            <thead>
                                <tr>
                                    <th onclick="sortTable('name')">
                                        Nama File <i class="fas fa-sort"></i>
                                    </th>
                                    <th onclick="sortTable('category')">
                                        Kategori <i class="fas fa-sort"></i>
                                    </th>
                                    <th onclick="sortTable('size')">
                                        Ukuran <i class="fas fa-sort"></i>
                                    </th>
                                    <th onclick="sortTable('modified')">
                                        Terakhir Diubah <i class="fas fa-sort"></i>
                                    </th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="filesTableBody">
                                <?php foreach ($files as $file): ?>
                                    <tr class="file-row" 
                                        data-category="<?php echo $file['category']; ?>"
                                        data-name="<?php echo strtolower($file['name']); ?>"
                                        data-size="<?php echo $file['size']; ?>"
                                        data-modified="<?php echo strtotime($file['modifiedTime']); ?>"
                                        data-created="<?php echo strtotime($file['createdTime']); ?>"
                                        data-date="<?php echo date('Y-m-d', strtotime($file['modifiedTime'])); ?>">
                                        <td>
                                            <div class="file-name-cell">
                                                <img src="<?php echo $file['iconLink']; ?>" alt="" class="file-icon">
                                                <span class="file-name"><?php echo htmlspecialchars($file['name']); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="category-badge" style="background: <?php echo $file['categoryColor']; ?>">
                                                <i class="fas fa-<?php echo $file['categoryIcon']; ?>"></i>
                                                <?php echo $file['categoryName']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatFileSize($file['size']); ?></td>
                                        <td><?php echo formatDateTime($file['modifiedTime']); ?></td>
                                        <td>
                                            <div class="file-actions">
                                                <a href="<?php echo $file['webViewLink']; ?>" 
                                                   target="_blank" 
                                                   class="btn btn-primary btn-icon" 
                                                   title="Lihat">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo $file['webViewLink']; ?>" 
                                                   target="_blank" 
                                                   class="btn btn-success btn-icon" 
                                                   title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <div id="noResults" class="empty-state" style="display: none;">
                            <i class="fas fa-search"></i>
                            <h3>Tidak Ada Hasil</h3>
                            <p>Tidak ada file yang sesuai dengan filter Anda</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php include __DIR__ . '/../../includes/footer.php'; ?>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/ajax.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    <script>
        let currentCategory = 'all';
        
        function filterByCategory(category) {
            currentCategory = category;
            
            // Update active button
            document.querySelectorAll('.category-filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('.category-filter-btn').classList.add('active');
            
            filterFiles();
        }
        
        function filterFiles() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const dateFilter = document.getElementById('dateFilter').value;
            const rows = document.querySelectorAll('.file-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const category = row.dataset.category;
                const name = row.dataset.name;
                const date = row.dataset.date;
                
                let show = true;
                
                // Category filter
                if (currentCategory !== 'all' && category !== currentCategory) {
                    show = false;
                }
                
                // Search filter
                if (searchTerm && !name.includes(searchTerm)) {
                    show = false;
                }
                
                // Date filter
                if (dateFilter && date !== dateFilter) {
                    show = false;
                }
                
                row.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });
            
            // Show/hide no results message
            document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
            document.getElementById('filesTable').style.display = visibleCount === 0 ? 'none' : 'table';
        }
        
        function sortFiles() {
            const select = document.getElementById('sortSelect');
            const [field, direction] = select.value.split('_');
            const tbody = document.getElementById('filesTableBody');
            const rows = Array.from(tbody.querySelectorAll('.file-row'));
            
            rows.sort((a, b) => {
                let aVal, bVal;
                
                if (field === 'name') {
                    aVal = a.dataset.name;
                    bVal = b.dataset.name;
                    return direction === 'asc' ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
                } else if (field === 'size') {
                    aVal = parseInt(a.dataset.size);
                    bVal = parseInt(b.dataset.size);
                } else if (field === 'modified') {
                    aVal = parseInt(a.dataset.modified);
                    bVal = parseInt(b.dataset.modified);
                } else if (field === 'created') {
                    aVal = parseInt(a.dataset.created);
                    bVal = parseInt(b.dataset.created);
                }
                
                return direction === 'asc' ? aVal - bVal : bVal - aVal;
            });
            
            rows.forEach(row => tbody.appendChild(row));
        }
        
        function sortTable(column) {
            const select = document.getElementById('sortSelect');
            const currentSort = select.value.split('_');
            
            if (currentSort[0] === column) {
                // Toggle direction
                select.value = column + '_' + (currentSort[1] === 'asc' ? 'desc' : 'asc');
            } else {
                // Default to desc for new column
                select.value = column + '_desc';
            }
            
            sortFiles();
        }
        
        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('dateFilter').value = '';
            document.getElementById('sortSelect').value = 'modified_desc';
            currentCategory = 'all';
            
            document.querySelectorAll('.category-filter-btn').forEach((btn, index) => {
                btn.classList.toggle('active', index === 0);
            });
            
            filterFiles();
            sortFiles();
        }
    </script>
</body>
</html>

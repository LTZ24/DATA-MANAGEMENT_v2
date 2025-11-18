<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/ajax_helpers.php';

requireLogin();

$error = '';
$success = '';

// Define folder categories
$categories = [
    'kesiswaan' => [
        'name' => 'Kesiswaan',
        'folder_id' => '1ek7EDGg525Nr6sT30yNhyLvphgXGN-3z',
        'icon' => 'users',
        'color' => '#3b82f6'
    ],
    'kurikulum' => [
        'name' => 'Kurikulum',
        'folder_id' => '1JlqjO6AxW2ML-FuP14f22wMmLSbASSzA',
        'icon' => 'book',
        'color' => '#10b981'
    ],
    'sapras' => [
        'name' => 'Sapras & Humas',
        'folder_id' => '13F-Cg44IpKOn-iWPpPfRq5A57Adf9VM6',
        'icon' => 'building',
        'color' => '#f59e0b'
    ],
    'tata_usaha' => [
        'name' => 'Tata Usaha',
        'folder_id' => '1P_z7_txZbvQX4yLJez4Zzh0gViBDHd30',
        'icon' => 'briefcase',
        'color' => '#8b5cf6'
    ]
];

$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $category = sanitize($_POST['category']);
    
    if (!isset($categories[$category])) {
        $error = 'Kategori tidak valid!';
    } elseif (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'File harus diupload!';
    } else {
        try {
            $file = $_FILES['file'];
            $folderId = $categories[$category]['folder_id'];
            
            // Upload to Google Drive
            $client = getGoogleClient();
            $driveService = new Google_Service_Drive($client);
            
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $file['name'],
                'parents' => [$folderId]
            ]);
            
            $content = file_get_contents($file['tmp_name']);
            $mimeType = $file['type'];
            
            $uploadedFile = $driveService->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id, name, mimeType, size, createdTime'
            ]);
            
            if ($uploadedFile->id) {
                $success = 'File berhasil diupload ke ' . $categories[$category]['name'] . '!';
                
                if (isAjaxRequest()) {
                    ajaxSuccess($success, ['file_id' => $uploadedFile->id]);
                }
                
                header("refresh:2;url=" . BASE_URL . "/pages/files/index.php?category=" . $category . "&success=" . urlencode($success));
            } else {
                $error = 'Gagal mengupload file ke Google Drive!';
            }
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
            
            if (isAjaxRequest()) {
                ajaxError($error);
            }
        }
    }
    
    if ($error && isAjaxRequest()) {
        ajaxError($error);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File - <?php echo APP_NAME; ?></title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/smk62.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/ajax.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .form-container {
            background: var(--white);
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 3rem;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--light-color);
        }
        
        .form-header i {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            display: block;
        }
        
        .form-header h2 {
            font-size: 1.75rem;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .form-header p {
            color: var(--secondary-color);
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 2rem;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }
        
        .form-group label i {
            color: var(--primary-color);
            margin-right: 0.5rem;
            width: 20px;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 2px solid var(--border-color);
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s;
            background: var(--white);
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            transform: translateY(-2px);
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .file-upload-area {
            border: 3px dashed var(--border-color);
            border-radius: 0.75rem;
            padding: 3rem 2rem;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            background: var(--light-color);
        }
        
        .file-upload-area:hover {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
        }
        
        .file-upload-area.dragover {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.1);
            transform: scale(1.02);
        }
        
        .file-upload-area i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .file-upload-area p {
            color: var(--secondary-color);
            margin: 0.5rem 0;
        }
        
        .file-upload-area input[type="file"] {
            display: none;
        }
        
        .file-info {
            display: none;
            background: var(--light-color);
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            margin-top: 1rem;
            align-items: center;
            gap: 1rem;
        }
        
        .file-info.show {
            display: flex;
        }
        
        .file-info i {
            font-size: 2rem;
            color: var(--primary-color);
        }
        
        .file-info .file-details {
            flex: 1;
        }
        
        .file-info .file-name {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }
        
        .file-info .file-size {
            font-size: 0.875rem;
            color: var(--secondary-color);
        }
        
        .category-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .category-card {
            padding: 1.5rem;
            border: 2px solid var(--border-color);
            border-radius: 0.75rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: var(--white);
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .category-card.selected {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
        }
        
        .category-card i {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            display: block;
        }
        
        .category-card .category-name {
            font-weight: 600;
            color: var(--dark-color);
            font-size: 0.95rem;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 2px solid var(--light-color);
        }
        
        .form-actions button,
        .form-actions a {
            flex: 1;
            padding: 1rem 2rem;
            font-size: 1.05rem;
            font-weight: 600;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }
        
        .form-actions button:hover,
        .form-actions .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
        }
        
        .form-actions .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-header .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .category-selector {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
                margin-bottom: 1.5rem;
            }
            
            .category-card {
                padding: 1rem;
            }
            
            .category-card i {
                font-size: 1.75rem;
                margin-bottom: 0.5rem;
            }
            
            .category-card .category-name {
                font-size: 0.85rem;
            }
        }
        
        @media (max-width: 480px) {
            .category-selector {
                gap: 0.5rem;
            }
            
            .category-card {
                padding: 0.75rem;
            }
            
            .category-card i {
                font-size: 1.5rem;
                margin-bottom: 0.4rem;
            }
            
            .category-card .category-name {
                font-size: 0.8rem;
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
                <div class="alert alert-success" data-persistent>
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="form-container">
                <div class="form-header">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <h2>Upload File Baru</h2>
                    <p>Pilih kategori dan upload file ke Google Drive</p>
                </div>
                
                <form method="POST" action="" enctype="multipart/form-data" id="uploadForm">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-folder"></i> Pilih Kategori
                        </label>
                        <div class="category-selector">
                            <?php foreach ($categories as $key => $cat): ?>
                                <div class="category-card <?php echo $selectedCategory === $key ? 'selected' : ''; ?>" 
                                     onclick="selectCategory('<?php echo $key; ?>', this)"
                                     data-category="<?php echo $key; ?>">
                                    <i class="fas fa-<?php echo $cat['icon']; ?>" 
                                       style="color: <?php echo $cat['color']; ?>"></i>
                                    <div class="category-name"><?php echo $cat['name']; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="category" id="categoryInput" 
                               value="<?php echo htmlspecialchars($selectedCategory); ?>" required>
                        <small><i class="fas fa-info-circle"></i> Pilih salah satu kategori sesuai jenis file</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="file">
                            <i class="fas fa-file"></i> Pilih File
                        </label>
                        <div class="file-upload-area" id="fileUploadArea">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p style="font-weight: 600; font-size: 1.1rem; color: var(--dark-color);">
                                Klik atau drag & drop file di sini
                            </p>
                            <p>Mendukung semua jenis file (PDF, Word, Excel, Gambar, dll)</p>
                            <p style="font-size: 0.875rem;">Maksimal 100MB per file</p>
                            <input type="file" name="file" id="fileInput" required>
                        </div>
                        <div class="file-info" id="fileInfo">
                            <i class="fas fa-file-alt"></i>
                            <div class="file-details">
                                <div class="file-name" id="fileName"></div>
                                <div class="file-size" id="fileSize"></div>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm" onclick="clearFile()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <small><i class="fas fa-info-circle"></i> File akan langsung terupload ke Google Drive setelah submit</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            <i class="fas fa-cloud-upload-alt"></i> Upload File
                        </button>
                    </div>
                </form>
            </div>
            
            <?php include __DIR__ . '/../../includes/footer.php'; ?>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/ajax.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    <script>
        const fileInput = document.getElementById('fileInput');
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const submitBtn = document.getElementById('submitBtn');
        const categoryInput = document.getElementById('categoryInput');
        
        // Click to upload
        fileUploadArea.addEventListener('click', () => {
            fileInput.click();
        });
        
        // File selected
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                displayFileInfo(e.target.files[0]);
            }
        });
        
        // Drag and drop
        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('dragover');
        });
        
        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('dragover');
        });
        
        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
            
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                displayFileInfo(e.dataTransfer.files[0]);
            }
        });
        
        function displayFileInfo(file) {
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.classList.add('show');
            checkFormValid();
        }
        
        function clearFile() {
            fileInput.value = '';
            fileInfo.classList.remove('show');
            checkFormValid();
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
        
        function selectCategory(category, element) {
            document.querySelectorAll('.category-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            element.classList.add('selected');
            categoryInput.value = category;
            checkFormValid();
        }
        
        function checkFormValid() {
            const hasFile = fileInput.files.length > 0;
            const hasCategory = categoryInput.value !== '';
            submitBtn.disabled = !(hasFile && hasCategory);
        }
    </script>
</body>
</html>

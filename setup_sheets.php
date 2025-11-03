<?php
/**
 * Google Sheets Setup Helper
 * 
 * Script ini membantu membuat struktur Google Sheets otomatis
 * untuk sistem kategori Links dan Forms
 * 
 * CARA PAKAI:
 * 1. Pastikan sudah login ke aplikasi
 * 2. Akses: http://localhost/Data-Base-Guru-v2/setup_sheets.php
 * 3. Klik "Create Sheets Structure"
 * 4. Copy Sheets IDs yang dihasilkan
 * 5. Paste ke includes/config.php
 * 6. HAPUS file ini setelah selesai (untuk keamanan)
 */

require_once __DIR__ . '/includes/config.php';
requireLogin();

// Check if user is admin (opsional, sesuaikan dengan sistem Anda)
// if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
//     die('Access denied. Admin only.');
// }

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Sheets Setup Helper</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 0.95em;
        }
        
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .warning i {
            color: #ffc107;
            margin-right: 8px;
        }
        
        .info-box {
            background: #d1ecf1;
            border-left: 4px solid #0dcaf0;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .steps {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .steps h3 {
            color: #495057;
            margin-bottom: 15px;
        }
        
        .steps ol {
            margin-left: 20px;
        }
        
        .steps li {
            margin-bottom: 10px;
            color: #6c757d;
        }
        
        .category-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .category-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #dee2e6;
            text-align: center;
        }
        
        .category-card i {
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .category-card.kesiswaan { border-color: #50e3c2; color: #50e3c2; }
        .category-card.kurikulum { border-color: #4a90e2; color: #4a90e2; }
        .category-card.sapras { border-color: #f39c12; color: #f39c12; }
        .category-card.tata_usaha { border-color: #e74c3c; color: #e74c3c; }
        
        .btn {
            background: #50e3c2;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .btn:hover {
            background: #45d0b0;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(80, 227, 194, 0.3);
        }
        
        .btn-danger {
            background: #e74c3c;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .result-box {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            display: none;
        }
        
        .result-box.show {
            display: block;
        }
        
        .result-box h3 {
            color: #155724;
            margin-bottom: 15px;
        }
        
        .sheets-id {
            background: white;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 10px;
            font-family: 'Courier New', monospace;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .sheets-id strong {
            color: #495057;
        }
        
        .sheets-id code {
            color: #e83e8c;
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 4px;
        }
        
        .copy-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85em;
        }
        
        .copy-btn:hover {
            background: #0056b3;
        }
        
        .error-box {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            display: none;
            color: #721c24;
        }
        
        .error-box.show {
            display: block;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #6c757d;
            text-decoration: none;
            margin-top: 20px;
            font-size: 0.95em;
        }
        
        .back-link:hover {
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <i class="fas fa-table"></i>
            Google Sheets Setup Helper
        </h1>
        <p class="subtitle">Buat struktur Google Sheets otomatis untuk sistem kategori</p>
        
        <div class="warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Peringatan:</strong> Script ini akan membuat 4 spreadsheet baru di Google Drive Anda. 
            Pastikan Anda sudah siap dan hapus file ini setelah selesai!
        </div>
        
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <strong>Yang akan dibuat:</strong> 4 Google Sheets (1 untuk setiap kategori), 
            masing-masing berisi 2 sheets (Links & Forms) dengan headers yang sudah disetup.
        </div>
        
        <h3>Kategori yang akan disetup:</h3>
        <div class="category-list">
            <div class="category-card kesiswaan">
                <i class="fas fa-users"></i>
                <h4>Kesiswaan</h4>
            </div>
            <div class="category-card kurikulum">
                <i class="fas fa-book"></i>
                <h4>Kurikulum</h4>
            </div>
            <div class="category-card sapras">
                <i class="fas fa-building"></i>
                <h4>Sapras & Humas</h4>
            </div>
            <div class="category-card tata_usaha">
                <i class="fas fa-file-invoice"></i>
                <h4>Tata Usaha</h4>
            </div>
        </div>
        
        <div class="steps">
            <h3>Langkah-langkah:</h3>
            <ol>
                <li>Klik tombol "Create Sheets Structure" di bawah</li>
                <li>Tunggu proses selesai (bisa 30-60 detik)</li>
                <li>Copy Sheets IDs yang dihasilkan</li>
                <li>Edit file <code>includes/config.php</code></li>
                <li>Paste Sheets IDs ke konstanta yang sesuai</li>
                <li><strong>HAPUS file setup_sheets.php ini!</strong></li>
            </ol>
        </div>
        
        <button class="btn" onclick="createSheets()">
            <i class="fas fa-magic"></i>
            Create Sheets Structure
        </button>
        
        <div id="loading" style="display:none; margin-top:20px;">
            <i class="fas fa-spinner fa-spin"></i> Creating sheets... Please wait...
        </div>
        
        <div id="result" class="result-box"></div>
        <div id="error" class="error-box"></div>
        
        <br><br>
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
    </div>
    
    <script>
        function createSheets() {
            const loading = document.getElementById('loading');
            const result = document.getElementById('result');
            const error = document.getElementById('error');
            
            loading.style.display = 'block';
            result.classList.remove('show');
            error.classList.remove('show');
            
            fetch('setup_sheets_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                
                if (data.success) {
                    let html = '<h3><i class="fas fa-check-circle"></i> Success! Sheets Created</h3>';
                    html += '<p style="margin-bottom:15px;">Copy IDs ini ke <code>includes/config.php</code>:</p>';
                    
                    for (const [category, id] of Object.entries(data.sheets_ids)) {
                        const constName = `SHEETS_ID_${category.toUpperCase()}`;
                        html += `
                            <div class="sheets-id">
                                <div>
                                    <strong>${constName}</strong><br>
                                    <code>${id}</code>
                                </div>
                                <button class="copy-btn" onclick="copyToClipboard('${id}', this)">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                            </div>
                        `;
                    }
                    
                    html += '<br><p><strong>Paste ke config.php seperti ini:</strong></p>';
                    html += '<pre style="background:#f8f9fa; padding:15px; border-radius:4px; overflow-x:auto;">';
                    for (const [category, id] of Object.entries(data.sheets_ids)) {
                        const constName = `SHEETS_ID_${category.toUpperCase()}`;
                        html += `define('${constName}', '${id}');\n`;
                    }
                    html += '</pre>';
                    
                    result.innerHTML = html;
                    result.classList.add('show');
                } else {
                    error.innerHTML = '<h3><i class="fas fa-exclamation-circle"></i> Error</h3>' +
                                    '<p>' + data.message + '</p>';
                    error.classList.add('show');
                }
            })
            .catch(err => {
                loading.style.display = 'none';
                error.innerHTML = '<h3><i class="fas fa-exclamation-circle"></i> Error</h3>' +
                                '<p>' + err.message + '</p>';
                error.classList.add('show');
            });
        }
        
        function copyToClipboard(text, button) {
            navigator.clipboard.writeText(text).then(() => {
                const originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i> Copied!';
                button.style.background = '#28a745';
                
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.style.background = '#007bff';
                }, 2000);
            });
        }
    </script>
</body>
</html>

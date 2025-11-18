<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Data Management System</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#50e3c2">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/icons/favicon-32x32.png">
    <link rel="apple-touch-icon" href="assets/images/icons/apple-touch-icon.png">
    <link rel="manifest" href="manifest.json">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #50e3c2 0%, #4dd0e1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .error-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        
        .error-code {
            font-size: 80px;
            font-weight: 700;
            color: #50e3c2;
            margin-bottom: 20px;
            line-height: 1;
        }
        
        .error-title {
            font-size: 24px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
        }
        
        .error-message {
            font-size: 16px;
            color: #718096;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .btn-home {
            display: inline-block;
            background: linear-gradient(135deg, #50e3c2 0%, #4dd0e1 100%);
            color: white;
            padding: 14px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(80, 227, 194, 0.4);
        }
        
        .error-icon {
            font-size: 100px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .error-container {
                padding: 40px 30px;
            }
            
            .error-code {
                font-size: 60px;
            }
            
            .error-title {
                font-size: 20px;
            }
            
            .error-message {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <?php
        $code = isset($_GET['code']) ? (int)$_GET['code'] : 404;
        
        $errors = [
            400 => [
                'icon' => '⚠️',
                'title' => 'Bad Request',
                'message' => 'Permintaan tidak valid. Silakan periksa input Anda.'
            ],
            401 => [
                'icon' => '🔒',
                'title' => 'Unauthorized',
                'message' => 'Anda tidak memiliki akses. Silakan login terlebih dahulu.'
            ],
            403 => [
                'icon' => '🚫',
                'title' => 'Forbidden',
                'message' => 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.'
            ],
            404 => [
                'icon' => '🔍',
                'title' => 'Not Found',
                'message' => 'Halaman yang Anda cari tidak ditemukan.'
            ],
            500 => [
                'icon' => '💥',
                'title' => 'Internal Server Error',
                'message' => 'Terjadi kesalahan pada server. Tim kami sedang memperbaikinya.'
            ]
        ];
        
        $error = $errors[$code] ?? $errors[404];
        ?>
        
        <div class="error-icon"><?php echo $error['icon']; ?></div>
        <div class="error-code"><?php echo $code; ?></div>
        <h1 class="error-title"><?php echo $error['title']; ?></h1>
        <p class="error-message"><?php echo $error['message']; ?></p>
        <a href="/Data-Base-Guru-v2/index.php" class="btn-home">Kembali ke Dashboard</a>
    </div>
</body>
</html>

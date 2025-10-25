<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/config.php';

requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : -1;

if ($id < 0) {
    redirect(BASE_URL . '/pages/forms/index.php');
}

$forms = getFormsFromSheets();
$form = null;

foreach ($forms as $f) {
    if ($f['id'] == $id) {
        $form = $f;
        break;
    }
}

if (!$form) {
    redirect(BASE_URL . '/pages/forms/index.php?error=Form tidak ditemukan');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $url = sanitize($_POST['url']);
    
    if (empty($title) || empty($url)) {
        $error = 'Judul dan URL harus diisi!';
    } else {
        try {
            if (updateFormInSheets($id, $title, $url)) {
                $success = 'Form berhasil diupdate!';
                header("refresh:2;url=index.php?success=Form berhasil diupdate");
            } else {
                $error = 'Gagal mengupdate form di Google Sheets!';
            }
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Form - <?php echo APP_NAME; ?></title>
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
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            transform: translateY(-2px);
        }
        
        .form-group small {
            display: block;
            margin-top: 0.5rem;
            color: var(--secondary-color);
            font-size: 0.875rem;
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
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/header.php'; ?>
    
    <div class="container">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h1>Edit Form</h1>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
            
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
                    <i class="fas fa-edit"></i>
                    <h2>Edit Form</h2>
                    <p>Update informasi form Google yang sudah tersimpan</p>
                </div>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="title">
                            <i class="fas fa-heading"></i> Judul Form
                        </label>
                        <input type="text" id="title" name="title" required 
                               value="<?php echo htmlspecialchars($form['title']); ?>"
                               placeholder="Masukkan judul form">
                        <small>Nama/judul dari Google Forms yang akan ditampilkan</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="url">
                            <i class="fas fa-link"></i> URL Google Forms
                        </label>
                        <input type="url" id="url" name="url" required 
                               value="<?php echo htmlspecialchars($form['url']); ?>"
                               placeholder="https://docs.google.com/forms/d/...">
                        <small>URL lengkap dari Google Forms Anda</small>
                    </div>
                    
                    <div class="form-actions">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Form
                        </button>
                    </div>
                </form>
            </div>
            
            <?php include __DIR__ . '/../../includes/footer.php'; ?>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/i18n.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/ajax.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>

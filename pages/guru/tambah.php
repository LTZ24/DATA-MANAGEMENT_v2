<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/config.php';

requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $nip = sanitize($_POST['nip']);
    $nama = sanitize($_POST['nama']);
    $tempat_lahir = sanitize($_POST['tempat_lahir']);
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $agama = sanitize($_POST['agama']);
    $alamat = sanitize($_POST['alamat']);
    $telepon = sanitize($_POST['telepon']);
    $email = sanitize($_POST['email']);
    $pendidikan_terakhir = sanitize($_POST['pendidikan_terakhir']);
    $jurusan = sanitize($_POST['jurusan']);
    $tahun_lulus = $_POST['tahun_lulus'] ?? '';
    $status = $_POST['status'];
    $status_aktif = $_POST['status_aktif'] ?? 'aktif';
    $tmt_kerja = $_POST['tmt_kerja'] ?? '';
    
    // Validation
    if (empty($nip) || empty($nama) || empty($jenis_kelamin) || empty($status)) {
        $error = 'Data wajib (*) harus diisi!';
    } else {
        try {
            // Check if NIP already exists
            $allGuru = getGuruFromSheets();
            $nipExists = false;
            foreach ($allGuru as $g) {
                if ($g['nip'] === $nip) {
                    $nipExists = true;
                    break;
                }
            }
            
            if ($nipExists) {
                $error = 'NIP sudah terdaftar!';
            } else {
                // Prepare data
                $data = [
                    'nip' => $nip,
                    'nama' => $nama,
                    'tempat_lahir' => $tempat_lahir,
                    'tanggal_lahir' => $tanggal_lahir,
                    'jenis_kelamin' => $jenis_kelamin,
                    'agama' => $agama,
                    'alamat' => $alamat,
                    'telepon' => $telepon,
                    'email' => $email,
                    'pendidikan_terakhir' => $pendidikan_terakhir,
                    'jurusan' => $jurusan,
                    'tahun_lulus' => $tahun_lulus,
                    'status' => $status,
                    'status_aktif' => $status_aktif,
                    'tmt_kerja' => $tmt_kerja
                ];
                
                // Add to Google Sheets
                if (addGuruToSheets($data)) {
                    $success = 'Data guru berhasil ditambahkan ke Google Sheets!';
                    // Redirect after 2 seconds
                    header("refresh:2;url=daftar.php?success=Data guru berhasil ditambahkan");
                } else {
                    $error = 'Gagal menambahkan data ke Google Sheets!';
                }
            }
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
            error_log('Error adding guru: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Guru - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .form-container {
            background: var(--white);
            border-radius: 0.5rem;
            box-shadow: var(--shadow);
            padding: 2rem;
            max-width: 900px;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: flex-end;
        }
        
        .required::after {
            content: " *";
            color: var(--danger-color);
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/header.php'; ?>
    
    <div class="container">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h1>Tambah Data Guru</h1>
                <a href="daftar.php" class="btn btn-secondary">
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
                <form method="POST" action="">
                    <!-- Data Pribadi -->
                    <div class="form-section">
                        <h3>Data Pribadi</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nip" class="required">NIP</label>
                                <input type="text" id="nip" name="nip" required 
                                       value="<?php echo htmlspecialchars($_POST['nip'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="nama" class="required">Nama Lengkap</label>
                                <input type="text" id="nama" name="nama" required 
                                       value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tempat_lahir">Tempat Lahir</label>
                                <input type="text" id="tempat_lahir" name="tempat_lahir" 
                                       value="<?php echo htmlspecialchars($_POST['tempat_lahir'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" id="tanggal_lahir" name="tanggal_lahir" 
                                       value="<?php echo htmlspecialchars($_POST['tanggal_lahir'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="jenis_kelamin" class="required">Jenis Kelamin</label>
                                <select id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="L" <?php echo ($_POST['jenis_kelamin'] ?? '') === 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="P" <?php echo ($_POST['jenis_kelamin'] ?? '') === 'P' ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="agama">Agama</label>
                                <select id="agama" name="agama">
                                    <option value="">-- Pilih --</option>
                                    <option value="Islam" <?php echo ($_POST['agama'] ?? '') === 'Islam' ? 'selected' : ''; ?>>Islam</option>
                                    <option value="Kristen" <?php echo ($_POST['agama'] ?? '') === 'Kristen' ? 'selected' : ''; ?>>Kristen</option>
                                    <option value="Katolik" <?php echo ($_POST['agama'] ?? '') === 'Katolik' ? 'selected' : ''; ?>>Katolik</option>
                                    <option value="Hindu" <?php echo ($_POST['agama'] ?? '') === 'Hindu' ? 'selected' : ''; ?>>Hindu</option>
                                    <option value="Buddha" <?php echo ($_POST['agama'] ?? '') === 'Buddha' ? 'selected' : ''; ?>>Buddha</option>
                                    <option value="Konghucu" <?php echo ($_POST['agama'] ?? '') === 'Konghucu' ? 'selected' : ''; ?>>Konghucu</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea id="alamat" name="alamat" rows="3"><?php echo htmlspecialchars($_POST['alamat'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="telepon">Telepon</label>
                                <input type="tel" id="telepon" name="telepon" 
                                       value="<?php echo htmlspecialchars($_POST['telepon'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data Pendidikan -->
                    <div class="form-section">
                        <h3>Data Pendidikan</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="pendidikan_terakhir">Pendidikan Terakhir</label>
                                <select id="pendidikan_terakhir" name="pendidikan_terakhir">
                                    <option value="">-- Pilih --</option>
                                    <option value="S3" <?php echo ($_POST['pendidikan_terakhir'] ?? '') === 'S3' ? 'selected' : ''; ?>>S3</option>
                                    <option value="S2" <?php echo ($_POST['pendidikan_terakhir'] ?? '') === 'S2' ? 'selected' : ''; ?>>S2</option>
                                    <option value="S1" <?php echo ($_POST['pendidikan_terakhir'] ?? '') === 'S1' ? 'selected' : ''; ?>>S1</option>
                                    <option value="D4" <?php echo ($_POST['pendidikan_terakhir'] ?? '') === 'D4' ? 'selected' : ''; ?>>D4</option>
                                    <option value="D3" <?php echo ($_POST['pendidikan_terakhir'] ?? '') === 'D3' ? 'selected' : ''; ?>>D3</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="jurusan">Jurusan</label>
                                <input type="text" id="jurusan" name="jurusan" 
                                       value="<?php echo htmlspecialchars($_POST['jurusan'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tahun_lulus">Tahun Lulus</label>
                                <input type="number" id="tahun_lulus" name="tahun_lulus" 
                                       min="1950" max="<?php echo date('Y'); ?>"
                                       value="<?php echo htmlspecialchars($_POST['tahun_lulus'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data Kepegawaian -->
                    <div class="form-section">
                        <h3>Data Kepegawaian</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="status" class="required">Status Kepegawaian</label>
                                <select id="status" name="status" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="PNS" <?php echo ($_POST['status'] ?? '') === 'PNS' ? 'selected' : ''; ?>>PNS</option>
                                    <option value="PPPK" <?php echo ($_POST['status'] ?? '') === 'PPPK' ? 'selected' : ''; ?>>PPPK</option>
                                    <option value="GTT" <?php echo ($_POST['status'] ?? '') === 'GTT' ? 'selected' : ''; ?>>GTT</option>
                                    <option value="GTY" <?php echo ($_POST['status'] ?? '') === 'GTY' ? 'selected' : ''; ?>>GTY</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="status_aktif">Status Aktif</label>
                                <select id="status_aktif" name="status_aktif">
                                    <option value="aktif" <?php echo ($_POST['status_aktif'] ?? 'aktif') === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="tidak aktif" <?php echo ($_POST['status_aktif'] ?? '') === 'tidak aktif' ? 'selected' : ''; ?>>Tidak Aktif</option>
                                    <option value="pensiun" <?php echo ($_POST['status_aktif'] ?? '') === 'pensiun' ? 'selected' : ''; ?>>Pensiun</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tmt_kerja">TMT Kerja</label>
                                <input type="date" id="tmt_kerja" name="tmt_kerja" 
                                       value="<?php echo htmlspecialchars($_POST['tmt_kerja'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="daftar.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
            
            <?php include __DIR__ . '/../../includes/footer.php'; ?>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>

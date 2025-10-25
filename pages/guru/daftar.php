<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/config.php';

requireLogin();

$error = '';
$success = isset($_GET['success']) ? $_GET['success'] : '';

// Get guru list from Google Sheets
$allGuruList = getGuruFromSheets();

// Search and filter
$search = sanitize($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';

$guruList = $allGuruList;

// Apply search filter
if (!empty($search)) {
    $guruList = array_filter($guruList, function($guru) use ($search) {
        $searchLower = strtolower($search);
        return (
            stripos($guru['nama'], $search) !== false ||
            stripos($guru['nip'], $search) !== false ||
            stripos($guru['email'], $search) !== false
        );
    });
}

// Apply status filter
if (!empty($status)) {
    $guruList = array_filter($guruList, function($guru) use ($status) {
        return ($guru['status_aktif'] ?? 'aktif') === $status;
    });
}

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 20;
$totalRecords = count($guruList);
$totalPages = ceil($totalRecords / $limit);
$offset = ($page - 1) * $limit;

// Slice array for pagination
$guruList = array_slice($guruList, $offset, $limit);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Guru - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .table-container {
            background: var(--white);
            border-radius: 0.5rem;
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-top: 2rem;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .search-filter {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .search-box {
            display: flex;
            gap: 0.5rem;
        }
        
        .search-box input {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            min-width: 300px;
        }
        
        .filter-select {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .data-table th {
            background-color: var(--light-color);
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .data-table tr:hover {
            background-color: var(--light-color);
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }
        
        .pagination a,
        .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            text-decoration: none;
            color: var(--dark-color);
        }
        
        .pagination a:hover {
            background-color: var(--light-color);
        }
        
        .pagination .active {
            background-color: var(--primary-color);
            color: var(--white);
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/header.php'; ?>
    
    <div class="container">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
        
        <main class="main-content">
            <h1>Daftar Guru</h1>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
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
            
            <div class="table-container">
                <div class="table-header">
                    <div>
                        <h2>Total: <?php echo $totalRecords; ?> Guru</h2>
                    </div>
                    <div class="search-filter">
                        <form method="GET" class="search-box">
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Cari nama, NIP, atau email..." 
                                value="<?php echo htmlspecialchars($search); ?>"
                            >
                            <select name="status" class="filter-select" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="aktif" <?php echo $status === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                                <option value="tidak aktif" <?php echo $status === 'tidak aktif' ? 'selected' : ''; ?>>Tidak Aktif</option>
                                <option value="pensiun" <?php echo $status === 'pensiun' ? 'selected' : ''; ?>>Pensiun</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </form>
                        <a href="tambah.php" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Tambah Guru
                        </a>
                    </div>
                </div>
                
                <?php if (empty($guruList)): ?>
                    <p style="text-align: center; padding: 2rem; color: var(--secondary-color);">
                        <i class="fas fa-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                        Tidak ada data guru yang ditemukan.
                    </p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Jenis Kelamin</th>
                                <th>Status Kepegawaian</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($guruList as $index => $guru): ?>
                                <tr>
                                    <td><?php echo $offset + $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($guru['nip']); ?></td>
                                    <td><?php echo htmlspecialchars($guru['nama']); ?></td>
                                    <td><?php echo $guru['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                                    <td><?php echo htmlspecialchars($guru['status']); ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = 'badge-success';
                                        if ($guru['status_aktif'] === 'tidak aktif') {
                                            $badgeClass = 'badge-danger';
                                        } elseif ($guru['status_aktif'] === 'pensiun') {
                                            $badgeClass = 'badge-warning';
                                        }
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>">
                                            <?php echo ucfirst($guru['status_aktif']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="detail.php?id=<?php echo $guru['id']; ?>" class="btn btn-info btn-sm" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $guru['id']; ?>" class="btn btn-primary btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="hapus.php?id=<?php echo $guru['id']; ?>" style="display: inline;" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data guru:\n<?php echo addslashes($guru['nama']); ?> (<?php echo $guru['nip']; ?>)?');">
                                                <input type="hidden" name="confirm" value="1">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>">
                                    <i class="fas fa-chevron-left"></i> Sebelumnya
                                </a>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i == $page): ?>
                                    <span class="active"><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>">
                                    Selanjutnya <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <?php include __DIR__ . '/../../includes/footer.php'; ?>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>

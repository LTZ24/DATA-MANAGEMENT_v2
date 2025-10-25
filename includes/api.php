<?php
session_start();
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? '';

try {
    $db = getDBConnection();
    
    switch ($action) {
        case 'getDashboardStats':
            $stats = getDashboardStats($db);
            echo json_encode(['success' => true, 'stats' => $stats]);
            break;
            
        case 'getGuruList':
            $guru = getGuruList($db);
            echo json_encode(['success' => true, 'data' => $guru]);
            break;
            
        case 'searchGuru':
            $keyword = $_GET['keyword'] ?? '';
            $guru = searchGuru($db, $keyword);
            echo json_encode(['success' => true, 'data' => $guru]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getDashboardStats($db) {
    // Get total guru
    $stmt = $db->query("SELECT COUNT(*) as total FROM guru");
    $total_guru = $stmt->fetch()['total'] ?? 0;
    
    // Get active guru
    $stmt = $db->query("SELECT COUNT(*) as total FROM guru WHERE status = 'aktif'");
    $guru_aktif = $stmt->fetch()['total'] ?? 0;
    
    // Get data in cloud (placeholder)
    $data_cloud = $total_guru;
    
    // Get last sync time
    $stmt = $db->query("SELECT MAX(updated_at) as last_sync FROM guru");
    $last_sync_raw = $stmt->fetch()['last_sync'] ?? null;
    $last_sync = $last_sync_raw ? formatDateTime($last_sync_raw) : 'Belum pernah';
    
    return [
        'total_guru' => $total_guru,
        'guru_aktif' => $guru_aktif,
        'data_cloud' => $data_cloud,
        'last_sync' => $last_sync
    ];
}

function getGuruList($db) {
    $stmt = $db->query("SELECT * FROM guru ORDER BY nama ASC");
    return $stmt->fetchAll();
}

function searchGuru($db, $keyword) {
    $stmt = $db->prepare("
        SELECT * FROM guru 
        WHERE nama LIKE ? 
        OR nip LIKE ? 
        OR email LIKE ?
        ORDER BY nama ASC
    ");
    $searchTerm = "%$keyword%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    return $stmt->fetchAll();
}

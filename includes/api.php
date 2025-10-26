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
    // Get total guru - Using prepared statement for consistency
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM guru");
    $stmt->execute();
    $total_guru = $stmt->fetch()['total'] ?? 0;
    
    // Get active guru - Prepared statement with parameter
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM guru WHERE status = ?");
    $stmt->execute(['aktif']);
    $guru_aktif = $stmt->fetch()['total'] ?? 0;
    
    // Get data in cloud (placeholder)
    $data_cloud = $total_guru;
    
    // Get last sync time - Prepared statement
    $stmt = $db->prepare("SELECT MAX(updated_at) as last_sync FROM guru");
    $stmt->execute();
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
    // Use prepared statement even without parameters for consistency
    $stmt = $db->prepare("SELECT * FROM guru ORDER BY nama ASC");
    $stmt->execute();
    return $stmt->fetchAll();
}

function searchGuru($db, $keyword) {
    // Sanitize input - remove potential SQL injection characters
    $keyword = trim($keyword);
    
    // Use prepared statement with parameterized query
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

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
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getDashboardStats($db) {
    // Dashboard stats without guru data
    return [
        'total_files' => 0,
        'total_links' => 0,
        'total_forms' => 0,
        'last_sync' => formatDateTime(date('Y-m-d H:i:s'))
    ];
}

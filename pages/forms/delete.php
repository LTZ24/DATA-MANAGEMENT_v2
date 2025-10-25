<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/config.php';

requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : -1;

if ($id < 0) {
    redirect(BASE_URL . '/pages/forms/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    try {
        if (deleteFormFromSheets($id)) {
            redirect(BASE_URL . '/pages/forms/index.php?success=Form berhasil dihapus');
        } else {
            redirect(BASE_URL . '/pages/forms/index.php?error=Gagal menghapus form');
        }
    } catch (Exception $e) {
        redirect(BASE_URL . '/pages/forms/index.php?error=' . urlencode($e->getMessage()));
    }
} else {
    redirect(BASE_URL . '/pages/forms/index.php');
}

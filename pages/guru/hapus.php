<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/config.php';

requireLogin();

// Get row ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id < 2) { // Row 1 is header
    redirect(BASE_URL . '/pages/guru/daftar.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    try {
        // Delete from Google Sheets
        if (deleteGuruFromSheets($id)) {
            redirect(BASE_URL . '/pages/guru/daftar.php?success=Data guru berhasil dihapus');
        } else {
            redirect(BASE_URL . '/pages/guru/daftar.php?error=Gagal menghapus data dari Google Sheets');
        }
    } catch (Exception $e) {
        error_log('Error deleting guru: ' . $e->getMessage());
        redirect(BASE_URL . '/pages/guru/daftar.php?error=Terjadi kesalahan: ' . $e->getMessage());
    }
} else {
    redirect(BASE_URL . '/pages/guru/daftar.php');
}

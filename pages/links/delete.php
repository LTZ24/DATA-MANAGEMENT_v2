<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/ajax_helpers.php';

requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : -1;

if ($id < 0) {
    if (isAjaxRequest()) {
        ajaxError('ID tidak valid');
    }
    redirect(BASE_URL . '/pages/links/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    try {
        if (deleteLinkFromSheets($id)) {
            if (isAjaxRequest()) {
                ajaxSuccess('Link berhasil dihapus', [], BASE_URL . '/pages/links/index.php');
            }
            redirect(BASE_URL . '/pages/links/index.php?success=Link berhasil dihapus');
        } else {
            if (isAjaxRequest()) {
                ajaxError('Gagal menghapus link');
            }
            redirect(BASE_URL . '/pages/links/index.php?error=Gagal menghapus link');
        }
    } catch (Exception $e) {
        if (isAjaxRequest()) {
            ajaxError($e->getMessage());
        }
        redirect(BASE_URL . '/pages/links/index.php?error=' . urlencode($e->getMessage()));
    }
} else {
    if (isAjaxRequest()) {
        ajaxError('Method tidak valid');
    }
    redirect(BASE_URL . '/pages/links/index.php');
}

<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/ajax_helpers.php';

requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : -1;
$category = isset($_GET['category']) ? $_GET['category'] : '';

if ($id < 0 || empty($category)) {
    if (isAjaxRequest()) {
        ajaxError('ID atau kategori tidak valid');
    }
    redirect(BASE_URL . '/pages/links/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    try {
        if (deleteLinkFromSheets($id, $category)) {
            if (isAjaxRequest()) {
                ajaxSuccess('Link berhasil dihapus', [], BASE_URL . '/pages/links/index.php?category=' . $category);
            }
            redirect(BASE_URL . '/pages/links/index.php?success=Link berhasil dihapus&category=' . $category);
        } else {
            if (isAjaxRequest()) {
                ajaxError('Gagal menghapus link');
            }
            redirect(BASE_URL . '/pages/links/index.php?error=Gagal menghapus link&category=' . $category);
        }
    } catch (Exception $e) {
        if (isAjaxRequest()) {
            ajaxError($e->getMessage());
        }
        redirect(BASE_URL . '/pages/links/index.php?error=' . urlencode($e->getMessage()) . '&category=' . $category);
    }
} else {
    if (isAjaxRequest()) {
        ajaxError('Method tidak valid');
    }
    redirect(BASE_URL . '/pages/links/index.php');
}

<?php
/**
 * Helper functions for AJAX requests
 */

function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function ajaxResponse($success, $message, $data = [], $redirect = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'redirect' => $redirect
    ]);
    exit;
}

function ajaxSuccess($message, $data = [], $redirect = null) {
    ajaxResponse(true, $message, $data, $redirect);
}

function ajaxError($message, $data = []) {
    ajaxResponse(false, $message, $data);
}

function renderPage($content, $isAjax = null) {
    if ($isAjax === null) {
        $isAjax = isAjaxRequest();
    }
    
    if ($isAjax) {
        echo $content;
    } else {
        return $content;
    }
}

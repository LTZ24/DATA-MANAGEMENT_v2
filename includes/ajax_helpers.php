<?php
/**
 * Helper functions for AJAX requests
 */

/**
 * Check if the current request is an AJAX request
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Send JSON response for AJAX requests
 */
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

/**
 * Send success JSON response
 */
function ajaxSuccess($message, $data = [], $redirect = null) {
    ajaxResponse(true, $message, $data, $redirect);
}

/**
 * Send error JSON response
 */
function ajaxError($message, $data = []) {
    ajaxResponse(false, $message, $data);
}

/**
 * Render page for AJAX or regular request
 */
function renderPage($content, $isAjax = null) {
    if ($isAjax === null) {
        $isAjax = isAjaxRequest();
    }
    
    if ($isAjax) {
        // For AJAX, return only the main content
        echo $content;
    } else {
        // For regular request, include full page structure
        return $content;
    }
}

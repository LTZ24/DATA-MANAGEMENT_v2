<?php
/**
 * Cache Helper Functions
 */

function clearCache($type = 'all') {
    $patterns = [];
    
    switch ($type) {
        case 'links':
            $patterns = ['links_cache_'];
            break;
        case 'forms':
            $patterns = ['forms_cache_'];
            break;
        case 'files':
            $patterns = ['files_cache', 'storage_cache'];
            break;
        case 'dashboard':
            $patterns = ['dashboard_cache'];
            break;
        case 'all':
        default:
            $patterns = ['links_cache_', 'forms_cache_', 'files_cache', 'storage_cache', 'dashboard_cache'];
            break;
    }
    
    foreach ($_SESSION as $key => $value) {
        foreach ($patterns as $pattern) {
            if (strpos($key, $pattern) === 0) {
                unset($_SESSION[$key]);
            }
        }
    }
}

function clearDataCache() {
    clearCache('links');
    clearCache('forms');
    clearCache('files');
    clearCache('dashboard');
}

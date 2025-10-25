<?php
/**
 * i18n - Internationalization Helper
 * Simple language switcher for Indonesian and English
 */

// Get current language from localStorage (via cookie) or default to Indonesian
function getCurrentLanguage() {
    return isset($_COOKIE['language']) ? $_COOKIE['language'] : 'id';
}

// Load language file
function loadLanguage($lang = null) {
    if ($lang === null) {
        $lang = getCurrentLanguage();
    }
    
    // Validate language
    if (!in_array($lang, ['id', 'en'])) {
        $lang = 'id';
    }
    
    $langFile = __DIR__ . '/lang/' . $lang . '.php';
    
    if (file_exists($langFile)) {
        return require $langFile;
    }
    
    // Fallback to Indonesian
    return require __DIR__ . '/lang/id.php';
}

// Translate function
function __($key, $default = null) {
    global $translations;
    
    if (!isset($translations)) {
        $translations = loadLanguage();
    }
    
    return isset($translations[$key]) ? $translations[$key] : ($default ?? $key);
}

// Alias for translate function
function t($key, $default = null) {
    return __($key, $default);
}

// Set language (call this to change language)
function setLanguage($lang) {
    if (in_array($lang, ['id', 'en'])) {
        setcookie('language', $lang, time() + (86400 * 365), '/'); // 1 year
        $_COOKIE['language'] = $lang;
        return true;
    }
    return false;
}

// Initialize translations
$translations = loadLanguage();

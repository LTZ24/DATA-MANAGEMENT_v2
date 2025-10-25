/**
 * i18n JavaScript Helper
 * Sync language between localStorage and cookies
 */

(function() {
    'use strict';
    
    // Sync language on page load
    function syncLanguage() {
        const lang = localStorage.getItem('language') || 'id';
        
        // Set cookie
        document.cookie = `language=${lang}; path=/; max-age=31536000`; // 1 year
    }
    
    // Initialize
    syncLanguage();
    
    // Listen for storage changes (from other tabs)
    window.addEventListener('storage', function(e) {
        if (e.key === 'language') {
            syncLanguage();
            window.location.reload();
        }
    });
})();

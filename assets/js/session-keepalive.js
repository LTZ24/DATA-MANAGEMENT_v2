/**
 * Session Keep-Alive Script
 * Mencegah session timeout ketika user sedang aktif menggunakan aplikasi
 * 
 * Cara Kerja:
 * 1. Setiap 5 menit (300 detik), kirim ping ke server
 * 2. Server akan update $_SESSION['last_activity']
 * 3. Session timeout = 30 menit (1800 detik)
 * 4. Jadi user bisa idle maksimal 30 menit tanpa aktivitas
 * 
 * Auto Activity Detection:
 * - Mouse movement
 * - Keyboard input
 * - Scroll
 * - Click
 */

(function() {
    'use strict';
    
    // Configuration
    const CONFIG = {
        PING_INTERVAL: 5 * 60 * 1000,        // 5 menit (dalam milliseconds)
        SESSION_TIMEOUT: 30 * 60 * 1000,     // 30 menit
        WARNING_TIME: 28 * 60 * 1000,        // 28 menit (warning 2 menit sebelum timeout)
        IDLE_CHECK_INTERVAL: 60 * 1000,      // 1 menit
        ACTIVITY_EVENTS: ['mousedown', 'keydown', 'scroll', 'touchstart', 'click']
    };
    
    let lastActivityTime = Date.now();
    let sessionPingTimer = null;
    let idleCheckTimer = null;
    let warningShown = false;
    
    /**
     * Update last activity timestamp
     */
    function updateActivity() {
        lastActivityTime = Date.now();
        warningShown = false;
        hideWarning();
    }
    
    /**
     * Send ping to server to keep session alive
     */
    function pingServer() {
        fetch(window.location.href, {
            method: 'HEAD',
            headers: {
                'X-Session-Update': 'true'
            },
            cache: 'no-cache'
        }).then(response => {
            if (response.ok) {
                console.log('[Session] Keep-alive ping successful');
            } else {
                console.warn('[Session] Keep-alive ping failed:', response.status);
            }
        }).catch(error => {
            console.error('[Session] Keep-alive error:', error);
        });
    }
    
    /**
     * Check if user is idle
     */
    function checkIdleTime() {
        const idleTime = Date.now() - lastActivityTime;
        
        // Show warning 2 minutes before timeout
        if (idleTime >= CONFIG.WARNING_TIME && !warningShown) {
            showWarning();
            warningShown = true;
        }
        
        // Redirect to login if timeout reached
        if (idleTime >= CONFIG.SESSION_TIMEOUT) {
            console.log('[Session] Timeout reached, redirecting to login...');
            window.location.href = getBaseUrl() + '/auth/login.php?session_timeout=1';
        }
    }
    
    /**
     * Show session timeout warning
     */
    function showWarning() {
        // Remove existing warning if any
        hideWarning();
        
        const warning = document.createElement('div');
        warning.id = 'session-timeout-warning';
        warning.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
            color: white;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(255,107,107,0.3);
            z-index: 10000;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            animation: slideInRight 0.3s ease-out;
        `;
        
        warning.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 1.25rem;"></i>
                <div>
                    <strong style="display: block; margin-bottom: 4px;">Peringatan Session</strong>
                    <small style="opacity: 0.95;">Sesi akan berakhir dalam 2 menit. Lakukan aktivitas untuk melanjutkan.</small>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" style="
                    background: rgba(255,255,255,0.2);
                    border: none;
                    color: white;
                    width: 24px;
                    height: 24px;
                    border-radius: 50%;
                    cursor: pointer;
                    margin-left: 8px;
                ">×</button>
            </div>
        `;
        
        document.body.appendChild(warning);
        
        // Auto hide after 10 seconds
        setTimeout(() => {
            hideWarning();
        }, 10000);
    }
    
    /**
     * Hide session timeout warning
     */
    function hideWarning() {
        const warning = document.getElementById('session-timeout-warning');
        if (warning) {
            warning.remove();
        }
    }
    
    /**
     * Get base URL from current location
     */
    function getBaseUrl() {
        const path = window.location.pathname;
        const parts = path.split('/');
        // Assuming structure: /Data-Base-Guru-v2/...
        return parts.slice(0, 2).join('/') || '';
    }
    
    /**
     * Initialize session keep-alive system
     */
    function init() {
        console.log('[Session] Keep-alive system initialized');
        console.log('[Session] Ping interval:', CONFIG.PING_INTERVAL / 1000, 'seconds');
        console.log('[Session] Session timeout:', CONFIG.SESSION_TIMEOUT / 1000 / 60, 'minutes');
        
        // Register activity listeners
        CONFIG.ACTIVITY_EVENTS.forEach(event => {
            document.addEventListener(event, updateActivity, { passive: true });
        });
        
        // Start ping timer
        sessionPingTimer = setInterval(pingServer, CONFIG.PING_INTERVAL);
        
        // Start idle check timer
        idleCheckTimer = setInterval(checkIdleTime, CONFIG.IDLE_CHECK_INTERVAL);
        
        // Initial ping
        pingServer();
        
        // Update activity on page load
        updateActivity();
    }
    
    /**
     * Cleanup on page unload
     */
    function cleanup() {
        if (sessionPingTimer) {
            clearInterval(sessionPingTimer);
        }
        if (idleCheckTimer) {
            clearInterval(idleCheckTimer);
        }
        
        CONFIG.ACTIVITY_EVENTS.forEach(event => {
            document.removeEventListener(event, updateActivity);
        });
        
        console.log('[Session] Keep-alive system cleaned up');
    }
    
    // Add CSS animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', cleanup);
    
    // Expose to global scope for debugging
    window.SessionKeepAlive = {
        getLastActivity: () => lastActivityTime,
        getIdleTime: () => Date.now() - lastActivityTime,
        pingNow: pingServer,
        showWarning: showWarning,
        hideWarning: hideWarning
    };
    
})();

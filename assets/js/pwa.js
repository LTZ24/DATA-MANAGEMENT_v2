tcp:80 tcp:80/**
 * PWA Installation & Update Handler
 */

class PWAManager {
    constructor() {
        this.deferredPrompt = null;
        this.isStandalone = false;
        this.swRegistration = null;
        
        this.init();
    }
    
    init() {
        this.checkStandaloneMode();
        this.registerServiceWorker();
        this.setupInstallPrompt();
        this.setupUpdateHandler();
        this.addConnectionListeners();
        this.createInstallButton();
    }
    
    checkStandaloneMode() {
        this.isStandalone = window.matchMedia('(display-mode: standalone)').matches || 
                           window.navigator.standalone || 
                           document.referrer.includes('android-app://');
        
        if (this.isStandalone) {
            console.log('[PWA] Running in standalone mode');
            document.body.classList.add('pwa-standalone');
        }
    }
    
    async registerServiceWorker() {
        if (!('serviceWorker' in navigator)) {
            console.warn('[PWA] Service workers not supported');
            return;
        }
        
        try {
            this.swRegistration = await navigator.serviceWorker.register('/Data-Base-Guru-v2/sw.js', {
                scope: '/Data-Base-Guru-v2/'
            });
            
            console.log('[PWA] Service worker registered:', this.swRegistration.scope);
            
            this.swRegistration.addEventListener('updatefound', () => {
                console.log('[PWA] New service worker available');
                this.handleUpdate();
            });
            
        } catch (error) {
            console.error('[PWA] Service worker registration failed:', error);
        }
    }
    
    setupInstallPrompt() {
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            console.log('[PWA] Install prompt available');
            this.showInstallButton();
        });
        
        window.addEventListener('appinstalled', () => {
            console.log('[PWA] App installed successfully');
            this.hideInstallButton();
            this.showNotification('Aplikasi berhasil diinstall!', 'success');
            this.deferredPrompt = null;
        });
    }
    
    setupUpdateHandler() {
        if (!navigator.serviceWorker) return;
        
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            console.log('[PWA] Controller changed, reloading...');
            window.location.reload();
        });
    }
    
    async handleUpdate() {
        const newWorker = this.swRegistration.installing;
        
        newWorker.addEventListener('statechange', () => {
            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                this.showUpdateNotification();
            }
        });
    }
    
    showUpdateNotification() {
        const updateBanner = document.createElement('div');
        updateBanner.className = 'pwa-update-banner';
        updateBanner.innerHTML = `
            <div class="pwa-update-content">
                <i class="fas fa-sync-alt"></i>
                <span>Update tersedia!</span>
            </div>
            <button class="pwa-update-btn" onclick="window.pwaManager.applyUpdate()">
                Update Sekarang
            </button>
        `;
        document.body.appendChild(updateBanner);
        
        setTimeout(() => {
            updateBanner.classList.add('show');
        }, 100);
    }
    
    applyUpdate() {
        if (!this.swRegistration || !this.swRegistration.waiting) return;
        
        this.swRegistration.waiting.postMessage({ type: 'SKIP_WAITING' });
    }
    
    createInstallButton() {
        if (this.isStandalone) return;
        
        const installBtn = document.createElement('button');
        installBtn.id = 'pwa-install-btn';
        installBtn.className = 'pwa-install-button';
        installBtn.style.display = 'none';
        installBtn.innerHTML = `
            <i class="fas fa-download"></i>
            <span>Install App</span>
        `;
        
        installBtn.addEventListener('click', () => {
            this.promptInstall();
        });
        
        document.body.appendChild(installBtn);
    }
    
    showInstallButton() {
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.style.display = 'flex';
            setTimeout(() => {
                installBtn.classList.add('show');
            }, 100);
        }
    }
    
    hideInstallButton() {
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.classList.remove('show');
            setTimeout(() => {
                installBtn.style.display = 'none';
            }, 300);
        }
    }
    
    async promptInstall() {
        if (!this.deferredPrompt) {
            console.warn('[PWA] Install prompt not available');
            return;
        }
        
        this.deferredPrompt.prompt();
        
        const { outcome } = await this.deferredPrompt.userChoice;
        console.log(`[PWA] Install prompt outcome: ${outcome}`);
        
        if (outcome === 'accepted') {
            console.log('[PWA] User accepted install prompt');
        } else {
            console.log('[PWA] User dismissed install prompt');
        }
        
        this.deferredPrompt = null;
        this.hideInstallButton();
    }
    
    addConnectionListeners() {
        window.addEventListener('online', () => {
            console.log('[PWA] Connection restored');
            this.showNotification('Koneksi kembali', 'success');
            this.syncData();
        });
        
        window.addEventListener('offline', () => {
            console.log('[PWA] Connection lost');
            this.showNotification('Tidak ada koneksi internet', 'warning');
        });
    }
    
    async syncData() {
        if (!this.swRegistration || !this.swRegistration.sync) return;
        
        try {
            await this.swRegistration.sync.register('sync-data');
            console.log('[PWA] Background sync registered');
        } catch (error) {
            console.error('[PWA] Background sync failed:', error);
        }
    }
    
    showNotification(message, type = 'info') {
        const existingNotif = document.querySelector('.pwa-notification');
        if (existingNotif) {
            existingNotif.remove();
        }
        
        const notification = document.createElement('div');
        notification.className = `pwa-notification pwa-notification-${type}`;
        
        const icons = {
            success: 'fa-check-circle',
            warning: 'fa-exclamation-triangle',
            error: 'fa-times-circle',
            info: 'fa-info-circle'
        };
        
        notification.innerHTML = `
            <i class="fas ${icons[type] || icons.info}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    async checkForUpdates() {
        if (!this.swRegistration) return;
        
        try {
            await this.swRegistration.update();
            console.log('[PWA] Checked for updates');
        } catch (error) {
            console.error('[PWA] Update check failed:', error);
        }
    }
    
    async clearCache() {
        if (!('caches' in window)) return;
        
        try {
            const cacheNames = await caches.keys();
            await Promise.all(
                cacheNames.map(cacheName => caches.delete(cacheName))
            );
            console.log('[PWA] Cache cleared');
            this.showNotification('Cache berhasil dibersihkan', 'success');
        } catch (error) {
            console.error('[PWA] Failed to clear cache:', error);
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.pwaManager = new PWAManager();
    });
} else {
    window.pwaManager = new PWAManager();
}

const pwaStyles = document.createElement('style');
pwaStyles.textContent = `
    .pwa-install-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: linear-gradient(135deg, #50e3c2, #4dd0e1);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(80, 227, 194, 0.4);
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 9999;
        transition: all 0.3s ease;
        transform: translateY(100px);
        opacity: 0;
    }
    
    .pwa-install-button.show {
        transform: translateY(0);
        opacity: 1;
    }
    
    .pwa-install-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(80, 227, 194, 0.6);
    }
    
    .pwa-update-banner {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 10000;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transform: translateY(-100%);
        transition: transform 0.3s ease;
    }
    
    .pwa-update-banner.show {
        transform: translateY(0);
    }
    
    .pwa-update-content {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
    }
    
    .pwa-update-btn {
        background: white;
        color: #667eea;
        border: none;
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .pwa-update-btn:hover {
        transform: scale(1.05);
    }
    
    .pwa-notification {
        position: fixed;
        bottom: 80px;
        right: 20px;
        background: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 9998;
        transform: translateX(400px);
        opacity: 0;
        transition: all 0.3s ease;
        max-width: 300px;
    }
    
    .pwa-notification.show {
        transform: translateX(0);
        opacity: 1;
    }
    
    .pwa-notification-success { border-left: 4px solid #50e3c2; color: #0d4d47; }
    .pwa-notification-warning { border-left: 4px solid #f39c12; color: #8b5a00; }
    .pwa-notification-error { border-left: 4px solid #e74c3c; color: #7f2315; }
    .pwa-notification-info { border-left: 4px solid #3498db; color: #1a5490; }
    
    .pwa-notification i {
        font-size: 1.2rem;
    }
    
    .pwa-notification-success i { color: #50e3c2; }
    .pwa-notification-warning i { color: #f39c12; }
    .pwa-notification-error i { color: #e74c3c; }
    .pwa-notification-info i { color: #3498db; }
    
    @media (max-width: 480px) {
        .pwa-install-button {
            bottom: 10px;
            right: 10px;
            padding: 10px 20px;
            font-size: 0.85rem;
        }
        
        .pwa-notification {
            bottom: 70px;
            right: 10px;
            left: 10px;
            max-width: none;
        }
        
        .pwa-update-banner {
            padding: 12px 15px;
            flex-direction: column;
            gap: 10px;
        }
    }
`;
document.head.appendChild(pwaStyles);

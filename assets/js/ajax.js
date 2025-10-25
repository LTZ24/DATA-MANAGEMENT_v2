// AJAX Loading System
class AjaxLoader {
    constructor() {
        this.loadingOverlay = this.createLoadingOverlay();
        this.initPageLinks();
        this.initForms();
        this.cache = new Map();
    }

    createLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'ajax-loading-overlay';
        overlay.innerHTML = `
            <div class="ajax-spinner">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading...</p>
            </div>
        `;
        overlay.style.display = 'none';
        document.body.appendChild(overlay);
        return overlay;
    }

    showLoading() {
        this.loadingOverlay.style.display = 'flex';
    }

    hideLoading() {
        this.loadingOverlay.style.display = 'none';
    }

    initPageLinks() {
        // Only handle links explicitly marked with data-ajax="true"
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[data-ajax="true"]');
            if (link) {
                e.preventDefault();
                const url = link.getAttribute('href');
                this.loadPage(url);
            }
        });
    }

    initForms() {
        document.addEventListener('submit', (e) => {
            const form = e.target.closest('form[data-ajax="true"]');
            if (form) {
                e.preventDefault();
                this.submitForm(form);
            }
        });
    }

    async loadPage(url, pushState = true) {
        try {
            // Check cache first - use cache immediately without loading
            if (this.cache.has(url)) {
                const cachedContent = this.cache.get(url);
                this.updateContent(cachedContent);
                if (pushState) {
                    history.pushState({ url }, '', url);
                }
                return;
            }

            this.showLoading();

            // Set timeout for fetch (10 seconds max)
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000);

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const html = await response.text();
            
            // Cache the response (limit cache size to 10 pages)
            if (this.cache.size >= 10) {
                const firstKey = this.cache.keys().next().value;
                this.cache.delete(firstKey);
            }
            this.cache.set(url, html);

            this.updateContent(html);

            if (pushState) {
                history.pushState({ url }, '', url);
            }

        } catch (error) {
            console.error('Error loading page:', error);
            if (error.name === 'AbortError') {
                this.showError('Request timeout. Silakan refresh halaman.');
            } else {
                this.showError('Gagal memuat halaman. Silakan coba lagi.');
            }
        } finally {
            this.hideLoading();
        }
    }

    async submitForm(form) {
        try {
            this.showLoading();

            const formData = new FormData(form);
            const method = form.getAttribute('method') || 'POST';
            const action = form.getAttribute('action') || window.location.href;

            const response = await fetch(action, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const result = await response.json();

            if (result.success) {
                this.showSuccess(result.message || 'Berhasil!');
                
                // Reload current page content after successful submit
                if (result.redirect) {
                    setTimeout(() => {
                        this.loadPage(result.redirect);
                    }, 1000);
                } else {
                    setTimeout(() => {
                        this.loadPage(window.location.pathname, false);
                    }, 1000);
                }
                
                // Clear cache for this page
                this.cache.delete(window.location.pathname);
            } else {
                this.showError(result.message || 'Terjadi kesalahan');
            }

        } catch (error) {
            console.error('Error submitting form:', error);
            this.showError('Gagal mengirim data. Silakan coba lagi.');
        } finally {
            this.hideLoading();
        }
    }

    updateContent(html) {
        // Extract main content from response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        const newContent = doc.querySelector('.main-content');
        const currentContent = document.querySelector('.main-content');
        
        if (newContent && currentContent) {
            // Use faster innerHTML replacement
            currentContent.innerHTML = newContent.innerHTML;
            
            // Scroll to top instantly (no smooth animation for speed)
            window.scrollTo(0, 0);
            
            // Re-initialize event listeners only for AJAX-enabled elements
            this.initPageLinks();
            this.initForms();
        } else {
            // Fallback: reload the entire page
            window.location.reload();
        }
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `ajax-notification ajax-notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    clearCache() {
        this.cache.clear();
        this.showSuccess('Cache berhasil dibersihkan!');
    }
}

// Initialize AJAX loader when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.ajaxLoader = new AjaxLoader();

    // Handle browser back/forward buttons
    window.addEventListener('popstate', (e) => {
        if (e.state && e.state.url) {
            window.ajaxLoader.loadPage(e.state.url, false);
        }
    });
});

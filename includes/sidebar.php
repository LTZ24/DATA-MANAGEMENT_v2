<div class="sidebar" id="sidebar">
    <!-- Restore sidebar state immediately to prevent flash -->
    <script>
        (function() {
            if (window.innerWidth > 1024) {
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    document.getElementById('sidebar').classList.add('collapsed');
                }
            }
            // Enable transitions after page fully loaded
            setTimeout(function() {
                document.getElementById('sidebar').classList.add('transition-enabled');
                document.body.classList.add('page-loaded');
            }, 100);
        })();
    </script>

    <div class="sidebar-header">
        <div class="logo">
            <img src="<?php echo BASE_URL; ?>/assets/images/smk62.png" 
                 alt="SMKN 62 Jakarta" 
                 style="width: 40px; height: 40px; object-fit: contain; border-radius: 8px;">
            <span class="logo-text">SMKN 62 Jakarta</span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar">
            <?php if (isset($_SESSION['user_picture']) && !empty($_SESSION['user_picture'])): ?>
                <img src="<?php echo htmlspecialchars($_SESSION['user_picture']); ?>" 
                     alt="Profile" 
                     style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
            <?php else: ?>
                <i class="fas fa-user-circle"></i>
            <?php endif; ?>
        </div>
        <div class="user-info">
            <h4><?php echo htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['username'] ?? 'User'); ?></h4>
            <p><?php echo __('admin'); ?></p>
        </div>
    </div>

    <nav class="sidebar-menu">
        <ul>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' && strpos($_SERVER['PHP_SELF'], '/pages/') === false) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>/index.php" data-tooltip="<?php echo __('nav_dashboard'); ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span><?php echo __('nav_dashboard'); ?></span>
                </a>
            </li>

            <li class="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/links/') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>/pages/links/index.php" data-tooltip="<?php echo __('nav_links'); ?>">
                    <i class="fas fa-link"></i>
                    <span><?php echo __('nav_links'); ?></span>
                </a>
            </li>

            <li class="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/forms/') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>/pages/forms/index.php" data-tooltip="<?php echo __('nav_forms'); ?>">
                    <i class="fas fa-file-alt"></i>
                    <span><?php echo __('nav_forms'); ?></span>
                </a>
            </li>

            <li class="menu-separator">
                <span><?php echo __('nav_file_management'); ?></span>
            </li>

            <li class="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/files/') !== false && strpos($_SERVER['PHP_SELF'], 'upload.php') === false) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>/pages/files/index.php" data-tooltip="<?php echo __('nav_file_manager'); ?>">
                    <i class="fas fa-folder-open"></i>
                    <span><?php echo __('nav_file_manager'); ?></span>
                </a>
            </li>

            <li class="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/files/upload.php') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>/pages/files/upload.php" data-tooltip="<?php echo __('nav_upload'); ?>">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span><?php echo __('nav_upload'); ?></span>
                </a>
            </li>

            <li class="menu-separator">
                <span><?php echo __('nav_account'); ?></span>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>/auth/logout.php" data-tooltip="<?php echo __('logout'); ?>">
                    <i class="fas fa-sign-out-alt"></i>
                    <span><?php echo __('logout'); ?></span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const menuToggle = document.getElementById('menuToggle');
        const mainContent = document.querySelector('.main-content');

        if (!sidebar || !sidebarToggle || !sidebarOverlay || !mainContent) {
            return;
        }

        // Toggle untuk mobile dan desktop
        if (menuToggle) {
            menuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                if (window.innerWidth <= 1024) {
                    // Mobile: Tampilkan sebagai overlay
                    sidebar.classList.add('active');
                    sidebarOverlay.classList.add('active');
                } else {
                    // Desktop: Toggle collapse
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                }
            });
        }

        // Tutup sidebar di mobile
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });

        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });

        // Update main-content class
        if (window.innerWidth > 1024) {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed && sidebar.classList.contains('collapsed')) {
                mainContent.classList.add('sidebar-collapsed');
            }
        }

        // Handle resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 1024) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('sidebar-collapsed');
                }
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('sidebar-collapsed');
            }
        });
    });
</script>

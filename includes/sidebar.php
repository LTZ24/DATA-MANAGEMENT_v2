<div class="sidebar" id="sidebar">
    <script>
        (function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (window.innerWidth > 1024) {
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    if (sidebar) {
                        sidebar.classList.add('collapsed');
                    }
                    if (mainContent) {
                        mainContent.classList.add('sidebar-collapsed');
                    }
                }
            }
            
            document.body.classList.add('page-loaded');
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

    <nav class="sidebar-menu">
        <ul>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' && strpos($_SERVER['PHP_SELF'], '/pages/') === false) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>/index.php" data-tooltip="Dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/links/') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>/pages/links/index.php" data-tooltip="Kelola Links">
                    <i class="fas fa-link"></i>
                    <span>Kelola Links</span>
                </a>
            </li>

            <li class="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/forms/') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>/pages/forms/index.php" data-tooltip="Kelola Forms">
                    <i class="fas fa-file-alt"></i>
                    <span>Kelola Forms</span>
                </a>
            </li>

            <li class="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/files/') !== false && strpos($_SERVER['PHP_SELF'], 'upload.php') === false) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>/pages/files/index.php" data-tooltip="File Manager">
                    <i class="fas fa-folder-open"></i>
                    <span>File Manager</span>
                </a>
            </li>

            <li class="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/files/upload.php') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>/pages/files/upload.php" data-tooltip="Upload File">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>Upload File</span>
                </a>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>/auth/logout.php" data-tooltip="Keluar">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
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

        if (window.innerWidth > 1024) {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed && sidebar.classList.contains('collapsed')) {
                mainContent.classList.add('sidebar-collapsed');
            }
        }

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

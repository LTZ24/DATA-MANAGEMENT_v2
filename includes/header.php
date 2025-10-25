<header class="header">
    <div class="header-left">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="page-title">
            <?php
            $current_page = basename($_SERVER['PHP_SELF']);
            $page_titles = [
                'index.php' => 'Dashboard',
                'links' => 'Kelola Links',
                'forms' => 'Kelola Forms',
                'files' => 'File Manager',
                'upload.php' => 'Upload File'
            ];
            
            // Detect page title
            $title = __('dashboard_title');
            if (strpos($_SERVER['PHP_SELF'], '/pages/links/') !== false) {
                if (strpos($_SERVER['PHP_SELF'], 'add.php') !== false) {
                    $title = __('add_link');
                } elseif (strpos($_SERVER['PHP_SELF'], 'edit.php') !== false) {
                    $title = __('edit_link');
                } else {
                    $title = __('links_title');
                }
            } elseif (strpos($_SERVER['PHP_SELF'], '/pages/forms/') !== false) {
                if (strpos($_SERVER['PHP_SELF'], 'add.php') !== false) {
                    $title = __('add_form');
                } elseif (strpos($_SERVER['PHP_SELF'], 'edit.php') !== false) {
                    $title = __('edit_form');
                } else {
                    $title = __('forms_title');
                }
            } elseif (strpos($_SERVER['PHP_SELF'], '/pages/files/upload.php') !== false) {
                $title = __('upload_file_title');
            } elseif (strpos($_SERVER['PHP_SELF'], '/pages/files/') !== false) {
                $title = __('file_manager_title');
            } elseif (strpos($_SERVER['PHP_SELF'], '/pages/settings.php') !== false) {
                $title = __('settings_title');
            } elseif (strpos($_SERVER['PHP_SELF'], '/pages/profile.php') !== false) {
                $title = __('profile_title');
            }
            ?>
            <h2><?php echo $title; ?></h2>
        </div>
    </div>

    <div class="header-right">
        <div class="datetime-display" id="datetimeDisplay">
            <i class="fas fa-calendar-alt"></i>
            <span id="currentDateTime"></span>
        </div>

        <div class="header-actions">
            <div class="user-menu" id="userMenuToggle">
                <div class="user-avatar-small">
                    <?php if (isset($_SESSION['user_picture']) && !empty($_SESSION['user_picture'])): ?>
                        <img src="<?php echo htmlspecialchars($_SESSION['user_picture']); ?>" 
                             alt="Profile" 
                             style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                    <?php else: ?>
                        <i class="fas fa-user-circle"></i>
                    <?php endif; ?>
                </div>
                <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['username'] ?? 'User'); ?></span>
                <i class="fas fa-chevron-down dropdown-icon"></i>
            </div>

            <!-- User Dropdown Menu -->
            <div class="user-dropdown" id="userDropdown">
                <div class="dropdown-header">
                    <div class="dropdown-avatar">
                        <?php if (isset($_SESSION['user_picture']) && !empty($_SESSION['user_picture'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['user_picture']); ?>" 
                                 alt="Profile" 
                                 style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                            <i class="fas fa-user-circle"></i>
                        <?php endif; ?>
                    </div>
                    <div class="dropdown-user-info">
                        <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['username'] ?? 'User'); ?></strong>
                        <span class="user-email"><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'user@example.com'); ?></span>
                    </div>
                </div>

                <div class="dropdown-divider"></div>

                <div class="dropdown-menu-items">
                    <a href="<?php echo BASE_URL; ?>/pages/profile.php" class="dropdown-item">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo __('profile'); ?></span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/pages/settings.php" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span><?php echo __('settings'); ?></span>
                    </a>
                    
                    <div class="dropdown-divider"></div>
                    
                    <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="dropdown-item logout-item">
                        <i class="fas fa-sign-out-alt"></i>
                        <span><?php echo __('logout'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    // Date Time Update with i18n support
    function updateDateTime() {
        const now = new Date();
        const lang = localStorage.getItem('language') || 'id';
        
        // Indonesian
        const daysID = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const monthsID = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                         'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        // English
        const daysEN = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const monthsEN = ['January', 'February', 'March', 'April', 'May', 'June',
                         'July', 'August', 'September', 'October', 'November', 'December'];
        
        const days = lang === 'en' ? daysEN : daysID;
        const months = lang === 'en' ? monthsEN : monthsID;

        const dayName = days[now.getDay()];
        const day = now.getDate();
        const month = months[now.getMonth()];
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        const dateTimeString = `${dayName}, ${day} ${month} ${year} - ${hours}:${minutes}:${seconds} WIB`;
        const dateTimeElement = document.getElementById('currentDateTime');
        if (dateTimeElement) {
            dateTimeElement.textContent = dateTimeString;
        }
    }

    updateDateTime();
    setInterval(updateDateTime, 1000);

    // User Dropdown Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const userMenuToggle = document.getElementById('userMenuToggle');
        const userDropdown = document.getElementById('userDropdown');

        if (userMenuToggle && userDropdown) {
            userMenuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('active');
                userMenuToggle.classList.toggle('active');
            });

            userDropdown.addEventListener('click', function(e) {
                if (e.target.closest('a')) {
                    userDropdown.classList.remove('active');
                    userMenuToggle.classList.remove('active');
                }
            });

            document.addEventListener('click', function(e) {
                if (!userMenuToggle.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.remove('active');
                    userMenuToggle.classList.remove('active');
                }
            });
        }
    });
</script>

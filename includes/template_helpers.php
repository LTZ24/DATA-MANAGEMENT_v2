<?php
/**
 * Template helper functions to reduce code duplication
 */

function renderHead($title = '', $additionalCSS = []) {
    $pageTitle = !empty($title) ? $title . ' - ' . APP_NAME : APP_NAME;
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($pageTitle); ?></title>
        
        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#50e3c2">
        <meta name="description" content="Sistem Manajemen Database Guru SMK Negeri 62 Jakarta">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="DB Guru 62">
        
        <!-- Icons -->
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo BASE_URL; ?>/assets/images/icons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo BASE_URL; ?>/assets/images/icons/favicon-16x16.png">
        <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>/assets/images/icons/apple-touch-icon.png">
        
        <!-- PWA Manifest -->
        <link rel="manifest" href="<?php echo BASE_URL; ?>/manifest.json">
        
        <!-- Stylesheets -->
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/ajax.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    </head>
    <body>
    <?php
}

function renderFooter($additionalJS = []) {
    ?>
    <script src="<?php echo BASE_URL; ?>/assets/js/ajax.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/pwa.js"></script>
    <?php foreach ($additionalJS as $js): ?>
        <script src="<?php echo $js; ?>"></script>
    <?php endforeach; ?>
    </body>
    </html>
    <?php
}

function renderAlert($type, $message) {
    if (empty($message)) return;
    
    $icon = $type === 'success' ? 'check-circle' : 'exclamation-circle';
    ?>
    <div class="alert alert-<?php echo $type; ?>">
        <i class="fas fa-<?php echo $icon; ?>"></i>
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php
}

function startPageLayout() {
    include __DIR__ . '/header.php';
    ?>
    <div class="container">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="main-content">
    <?php
}

function endPageLayout() {
    ?>
        </main>
    </div>
    <?php
    include __DIR__ . '/footer.php';
}

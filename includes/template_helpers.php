<?php
/**
 * Template helper functions to reduce code duplication
 */

/**
 * Render the page head section
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
        <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/smk62.png">
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

/**
 * Render the page footer scripts
 */
function renderFooter($additionalJS = []) {
    ?>
    <script src="<?php echo BASE_URL; ?>/assets/js/ajax.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    <?php foreach ($additionalJS as $js): ?>
        <script src="<?php echo $js; ?>"></script>
    <?php endforeach; ?>
    </body>
    </html>
    <?php
}

/**
 * Render alert messages
 */
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

/**
 * Start page layout with header and sidebar
 */
function startPageLayout() {
    include __DIR__ . '/header.php';
    ?>
    <div class="container">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="main-content">
    <?php
}

/**
 * End page layout
 */
function endPageLayout() {
    ?>
        </main>
    </div>
    <?php
    include __DIR__ . '/footer.php';
}

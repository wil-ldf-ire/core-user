<?php
require_once __DIR__ . '/../init.php';

if (!$currentUser['id']) {
    ob_start();
    header('Location: /user/login');
    die();
}

require_once __DIR__ . '/../includes/_header.php';

if (($types['webapp']['user_theme'] ?? false)) {
    if (file_exists(THEME_PATH . '/pages/user/index.php')) {
        include_once THEME_PATH . '/pages/user/index.php';
    } elseif (file_exists(THEME_PATH . '/user-index.php')) {
        include_once THEME_PATH . '/user-index.php';
    } else {
        include_once __DIR__ . '/index.php';
    }

} else {
    include_once __DIR__ . '/index.php';
}

require_once __DIR__ . '/includes/_footer.php';

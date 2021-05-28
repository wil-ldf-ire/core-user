<?php
include_once __DIR__ . '/init.php';
include_once __DIR__ . '/includes/_header.php';

if (!$currentUser['id']) {
    header('Location: /user/login');
    die();
}

if (
    ($types['webapp']['user_theme'] ?? false) &&
    file_exists(THEME_PATH . '/pages/user/' . $slug . '.php')
):
    include_once THEME_PATH . '/pages/user/' . $slug . '.php';
elseif (
    ($types['webapp']['user_theme'] ?? false) &&
    file_exists(THEME_PATH . '/user-' . $slug . '.php')
):
    include_once THEME_PATH . '/user-' . $slug . '.php';
else:
    include_once THEME_PATH . '/404.php';
endif;

include_once __DIR__ . '/includes/_footer.php';

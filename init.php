<?php
include_once __DIR__ . '/header.php';

if (!$_SESSION['user']['id']) {
	header('Location: /user/login');
	die();
}

$dash = new Wildfire\Core\Dash;
$types = $dash->getTypes();

if ($types['webapp']['user_theme'] ?? false) {
	if (file_exists(THEME_PATH . '/pages/user/' . $slug . '.php')) {
		include_once THEME_PATH . '/pages/user/' . $slug . '.php';
	} elseif (file_exists(THEME_PATH . '/user-' . $slug . '.php')) {
		include_once THEME_PATH . '/user-' . $slug . '.php';
	} else {
		die('file not found');
	}
} else {
	include_once THEME_PATH . '/404.php';
}

include_once __DIR__ . '/footer.php';

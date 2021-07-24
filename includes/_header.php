<?php
if (
	($types['webapp']['user_theme'] ?? false) &&
	file_exists(THEME_PATH . '/pages/user/_header.php')
):
	include_once THEME_PATH . '/pages/user/_header.php';
elseif (
	($types['webapp']['user_theme'] ?? false) &&
	file_exists(THEME_PATH . '/pages/user/header.php')
):
	include_once THEME_PATH . '/pages/user/header.php';
elseif (
	($types['webapp']['user_theme'] ?? false) &&
	file_exists(THEME_PATH . '/user-header.php')
):
	include_once THEME_PATH . '/user-header.php';
else:
?>

<!doctype html>
<html lang="<?=$types['webapp']['lang']?>">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title><?=(isset($headmeta_title) ? $headmeta_title . ' &raquo; ' : '') . 'Dashboard'?></title>
	<meta
		name="description"
		content="Access authorisation<?=isset($headmeta_title) ? ' for ' . $headmeta_title : ''?>"
	>
	<link rel="stylesheet" href="https://use.typekit.net/xkh7dxd.css">
	<link rel="stylesheet" href="/vendor/wildfire/auth/css/bootstrap.min.css">
	<link rel="stylesheet" href="/vendor/wildfire/auth/css/wildfire.css">
	<link rel="stylesheet" href="/vendor/wildfire/auth/plugins/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="/vendor/wildfire/auth/css/custom.css">
  	<link rel="stylesheet" href="/vendor/wildfire/auth/css/user.css">
</head>

<body class="text-center">
	<hr class="hr fixed-top" style="margin:0 !important;">

<?php endif;?>

<?php
namespace Wildfire;

$sql = new Core\MySQL();
$dash = new Core\Dash();
$admin = new Core\Admin();
$auth = new Auth\Auth();

$type = 'user';
$types = $dash->getTypes();
$menus = $dash->getMenus();
$currentUser = $auth->getCurrentUser();
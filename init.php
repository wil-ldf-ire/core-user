<?php
namespace Wildfire\Core;

$sql = new MySQL();
$dash = new Dash();
$admin = new Admin();
$theme = new Theme();

$type = 'user';
$types = $dash->getTypes();
$menus = $dash->getMenus();
$session_user = $dash->getSessionUser();
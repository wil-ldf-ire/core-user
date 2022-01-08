<?php
if (
	($types['webapp']['user_theme'] ?? false) &&
	file_exists(THEME_PATH . '/pages/user/_footer.php')
):
	include_once THEME_PATH . '/pages/user/_footer.php';
elseif (
	($types['webapp']['user_theme'] ?? false) &&
	file_exists(THEME_PATH . '/pages/user/footer.php')
):
	include_once THEME_PATH . '/pages/user/footer.php';
elseif (
	($types['webapp']['user_theme'] ?? false) &&
	file_exists(THEME_PATH . '/user-footer.php')
):
	include_once THEME_PATH . '/user-footer.php';
else:
?>
	<script src="/vendor/wildfire/auth/theme/assets/plugins/jquery.min.js"></script>
	<script src="/vendor/wildfire/auth/theme/assets/plugins/popper/popper.min.js"></script>
	<script src="/vendor/wildfire/auth/theme/assets/plugins/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
<?php endif;?>

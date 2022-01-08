<?php
require_once __DIR__.'/../init.php';

if (
    isset($_GET['action']) &&
    $_GET['action'] == 'exit'
) {
    session_destroy();
    ob_start();
    header("Location: /user/login");
}

$form_email = $_POST['email'] ?? false;
$form_mobile = $_POST['mobile'] ?? false;
$form_password = $_POST['password'] ?? false;
$redirect_url = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : '';

if (($form_email || $form_mobile) && $form_password) {
    $user_id = $auth->getUserId($_POST);

    if ($user_id) {
        $user = $dash->get_content($user_id);
        $auth->doAfterLogin($user, $redirect_url);
    }
} elseif ($currentUser['id'] ?? false) {
    $user = $dash->get_content($currentUser['id']);
    $auth->doAfterLogin($user, $redirect_url);
}


$use_custom_theme = $types['webapp']['user_theme'] ?? false;

if ($use_custom_theme && file_exists(THEME_PATH.'/pages/user/login.php')):
    require_once THEME_PATH.'/pages/user/login.php';
    elseif ($use_custom_theme && file_exists(THEME_PATH.'/user-login.php')):
        require_once THEME_PATH.'/user-login.php';
    else: // if custom user theme doesn't exitst
        require_once __DIR__.'/../includes/_header.php';
?>

    <form class="form-user" method="post" action="/user/login">
        <h2>
            <?= $menus['main']['logo']['name'] ?? '' ?>
        </h2>

        <h4 class="my-3 font-weight-normal">
            <span class="fas fa-lock"></span>&nbsp;Sign in
        </h4>

        <?php if ($_POST): ?>
            <div class="alert alert-danger"
                >Wrong credentials. Please try again.
            </div>
        <?php endif; ?>

        <label for="inputEmail" class="sr-only">Email address</label>
        <input
            type="email"
            name="email"
            id="inputEmail"
            class="form-control my-1"
            value="<?= $form_email ?>"
            placeholder="Email address"
            required
            autofocus
        >

        <label for="inputPassword" class="sr-only">Password</label>
        <input
            type="password"
            name="password"
            id="inputPassword"
            class="form-control my-1"
            placeholder="Password"
            required
        >

        <div class="checkbox my-1 small">
            <label>
                <input type="checkbox" class="my-0" value="remember-me"> Remember me
            </label>
        </div>

        <button type="submit" class="btn btn-sm btn-primary btn-block my-1"
            >Sign in
        </button>

        <a
            class="btn btn-sm btn-outline-primary btn-block my-1"
            href="/user/register"
            >Register
        </a>

        <p class="text-muted small my-2">
            <a href="/user/forgot-password">
                <span class="fas fa-key"></span> Forgot password?
            </a>
        </p>

        <p class="text-muted small my-5">
            <a href="/">
                <span class="fas fa-angle-double-left"
                    >&nbsp;<?= $menus['main']['logo']['name'] ?? '' ?>
                </span>
            </a>
        </p>

        <p class="text-muted small my-5">
            &copy;&nbsp;
            <?= date('Y') == '2020 ' ? date('Y') : '2020 - ' . date('Y') ?>&nbsp;
            <?= $menus['main']['logo']['name'] ?? 'Wildfire' ?>
        </p>
    </form>

<?php
    require_once __DIR__.'/../includes/_footer.php';
endif; // custom theme override
?>

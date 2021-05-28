<?php
namespace Wildfire\Auth;
use Wildfire\Core\Dash as Dash;

class Auth {

    protected static $types;

    public function __construct() {
        $dash = new Dash();
        self::$types = $dash->get_types(ABSOLUTE_PATH . '/config/types.json');
    }

    public function doAfterLogin($user, $redirect_url = '') {
        global $_SESSION;

        $roleslug = $user['role_slug'];
        $types = self::$types;

        $user['role'] = $types['user']['roles'][$roleslug]['role'];

        //for admin and crew (staff)
        if ($types['user']['roles'][$roleslug]['role'] == 'admin' || $types['user']['roles'][$roleslug]['role'] == 'crew') {
            $_SESSION['user'] = $user;
            $_SESSION['user']['wildfire_dashboard_access'] = 1;
            ob_start();
            header('Location: ' . (trim($redirect_url) ? trim($redirect_url) : '/admin'));
        }

        //for members
        elseif ($types['user']['roles'][$roleslug]['role'] == 'member') {
            $_SESSION['user'] = $user;
            $_SESSION['user']['wildfire_dashboard_access'] = 0;
            ob_start();
            header('Location: ' . (trim($redirect_url) ? trim($redirect_url) : '/user'));
        }

        //for visitors and anybody else
        else {
            ob_start();
            header('Location: ' . (trim($redirect_url) ? trim($redirect_url) : '/'));
        }
    }

    public function getCurrentUser() {
        global $_SESSION;
        return ($_SESSION['user'] ?? null);
    }
}

?>
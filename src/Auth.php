<?php
namespace Wildfire\Auth;
use Dotenv\Dotenv as Dotenv;
use Firebase\JWT\JWT as JWT;
use Wildfire\Core\Dash as Dash;

$dotenv = Dotenv::createImmutable(__DIR__, '../../../../.env');
$dotenv->load();

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
            $user['wildfire_dashboard_access'] = 1;
            $access_token = $this->setCurrentUser($user);
            $_SESSION['access_token'] = $access_token;

            ob_start();
            header('Location: ' . (trim($redirect_url) ? trim($redirect_url) : '/admin'));
        }

        //for members
        elseif ($types['user']['roles'][$roleslug]['role'] == 'member') {
            $user['wildfire_dashboard_access'] = 0;
            $access_token = $this->setCurrentUser($user);
            $_SESSION['access_token'] = $access_token;

            ob_start();
            header('Location: ' . (trim($redirect_url) ? trim($redirect_url) : '/user'));
        }

        //for visitors and anybody else
        else {
            ob_start();
            header('Location: ' . (trim($redirect_url) ? trim($redirect_url) : '/'));
        }
    }

    public function getCurrentUser($access_token = '') {
        global $_SESSION, $_ENV;

        if (!$access_token) {
            $access_token = $_SESSION['access_token'];
        }

        if ($access_token) {
            return (array) JWT::decode($access_token, $_ENV['TRIBE_API_SECRET_KEY'], array('HS256'));
        } else {
            return false;
        }

    }

    public function setCurrentUser($user, $timeout = 0) {
        global $_SESSION, $_ENV;

        $payload = array(
            "iss" => $_ENV['BASE_URL'], //“iss” (Issuer) Claim
            "aud" => $_ENV['BASE_URL'],
            "iat" => time(), //"iat" (Issued At) Claim
            "nbf" => time(), //"nbf" (Not Before) Claim
        );

        if ($timeout) {
            $payload["exp"] = $timeout;
        }
        // "exp" (Expiration Time) Claim

        $payload = array_merge($payload, $user);

        $jwt_token = JWT::encode($payload, $_ENV['TRIBE_API_SECRET_KEY']);

        $_SESSION['access_token'] = $jwt_token;

        return array(
            "access_token" => $jwt_token,
            "token_type" => "Bearer",
            "user_id" => $user['user_id'],
        );
    }
}

?>
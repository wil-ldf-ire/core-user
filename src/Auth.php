<?php
namespace Wildfire\Auth;
use Firebase\JWT\JWT as JWT;
use Wildfire\Core\Dash as Dash;
use Wildfire\Core\MySQL as MySQL;

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
            $this->setCurrentUser($user);

            ob_start();
            header('Location: ' . (trim($redirect_url) ? trim($redirect_url) : '/admin'));
        }

        //for members
        elseif ($types['user']['roles'][$roleslug]['role'] == 'member') {
            $user['wildfire_dashboard_access'] = 0;
            $this->setCurrentUser($user);

            ob_start();
            header('Location: ' . (trim($redirect_url) ? trim($redirect_url) : '/user'));
        }

        //for visitors and anybody else
        else {
            ob_start();
            header('Location: ' . (trim($redirect_url) ? trim($redirect_url) : '/'));
        }
    }

    public function getApiAccess($api_key, $api_secret) {
        $sql = new MySQL();
        $types = self::$types;

        $user_id = (string) json_decode($sql->executeSQL("
            SELECT `content`->'$.user_id' `user_id` FROM `data`
            WHERE
            `content`->'$.api_key'='" . $api_key . "' &&
            `content`->'$.api_secret'='" . $api_secret . "' &&
            `content`->'$.type'='api_key_secret'")[0]['user_id'], true);

        $user = $this->getUser($user_id);
        $roleslug = $user['role_slug'];
        $user['role'] = $types['user']['roles'][$roleslug]['role'];

        //for admin and dev (staff)
        if ($types['user']['roles'][$roleslug]['role'] == 'admin' || $types['user']['roles'][$roleslug]['role'] == 'dev') {
            return $this->setCurrentUser($user, 900);
        } else {
            return false;
        }

    }

    public function getUser($val) {
        $sql = new MySQL();
        $or = array();

        if (is_int($val)) {
            $q = $sql->executeSQL("SELECT * FROM `data` WHERE `id`='$val' && `content`->'$.type'='user'");
        } else {
            $q = $sql->executeSQL("SELECT * FROM `data` WHERE `content`->'$.user_id'='$val' && `content`->'$.type'='user'");
        }

        if ($q[0]['id']) {
            $or = json_decode($q[0]['content'], true);
            $or['id'] = $q[0]['id'];
            $or['updated_on'] = $q[0]['updated_on'];
            $or['created_on'] = $q[0]['created_on'];
            return $or;
        } else {
            return 0;
        }

    }

    public function getCurrentUser($access_token = '') {
        global $_SESSION, $_ENV;

        if (!$access_token) {
            $access_token = $_SESSION['access_token'];
        }

        if ($access_token) {

            try {
                $decoded = JWT::decode($access_token, ($_ENV['TRIBE_API_SECRET_KEY'] ?? $_ENV['DB_PASS']), array('HS256'));
            } catch (Exception $e) {
                if ($e->getMessage() == "Expired token") {
                    return 'expired';

                } else {
                    return false;
                }
            }

            return (array) $decoded;

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
            $payload["exp"] = time() + $timeout;
        }
        // "exp" (Expiration Time) Claim

        $payload = array_merge($payload, $user);

        $jwt_token = JWT::encode($payload, ($_ENV['TRIBE_API_SECRET_KEY'] ?? $_ENV['DB_PASS']));

        $_SESSION['access_token'] = $jwt_token;

        return array(
            "access_token" => $jwt_token,
            "token_type" => "Bearer",
            "user_id" => $user['user_id'],
        );
    }

    public function getUserId($post) {
        $sql = new MySQL();

        if (($post['email'] ?? false)) {
            $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE `content`->'$.email'='" . $post['email'] . "' && `content`->'$.password'='" . md5($post['password']) . "' && `content`->'$.type'='user'");
        } elseif (($post['mobile'] ?? false)) {
            $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE `content`->'$.mobile'='" . $post['mobile'] . "' && `content`->'$.password'='" . md5($post['password']) . "' && `content`->'$.type'='user'");
        }

        return ($q[0]['id'] ?? false);
    }
}

?>
<?php

if (@file_exists("org_netbeans_saas/RestConnection.php")) {
    include_once "org_netbeans_saas/RestConnection.php";
} else {
    include_once "../org_netbeans_saas/RestConnection.php";
}

include_once "FlickrPhotoServiceAuthenticatorProfile.php";

define("_ATTR_PREFIX", "FlickrPhotoServiceAuthenticator");
define("_SAAS", "org_netbeans_saas_");
define("_RETURN_URL", "flickr_return_url");
define("_AUTH_TOKEN", "flickr_auth_token");
define("_FROB", "flickr_frob");
define("_EXPIRE", "3600");

class FlickrPhotoServiceAuthenticator {

    private static $inited = false;
    private static $LOGIN_URL;

    public static function init() {
        if (self::$inited)
            return;
        self::$LOGIN_URL = "org_netbeans_saas_flickr/" . str_replace("Authenticator", "Login", _ATTR_PREFIX) . ".php";
        self::$inited = true;
    }

    public static function getApiKey() {
        $apiKey = FlickrPhotoServiceAuthenticatorProfile::getApiKey();
        if ($apiKey == null || $apiKey == "") {
            throw new Exception("Please specify your api key in the Profile file.");
        }
        return $apiKey;
    }

    public static function getSecret() {
        $secret = FlickrPhotoServiceAuthenticatorProfile::getSecret();
        if ($secret == null || $secret == "") {
            throw new Exception("Please specify your secret key in the Profile file.");
        }
        return $secret;
    }

    public static function getAuthToken() {
        $authToken = self::getSession(_AUTH_TOKEN);

        if ($authToken == null || $authToken == "") {
            throw new Exception("Failed to get a valid authentication token.");
        }
        return $authToken;
    }

    public static function login() {
        self::init();
        $authToken = self::getSession(_AUTH_TOKEN);

        // If there is already a auth token, we are already logged in.
        // Simply return.
        if ($authToken != null) {
            return;
        }

        $frob = self::getSession(_FROB);

        // If there is an auth token instead of a session key, we need to
        // obtain the session key using the auth token.  If there is no
        // auth token, we redirect to the login page.
        if ($frob != null) {
            self::deleteSession(_FROB);
            $apiKey = self::getApiKey();
            $method = "flickr.auth.getToken";
            $sig = array();
            $sig["method"] = $method;
            $sig["frob"] = $frob;
            $sig["api_key"] = $apiKey;
            $apiSig = self::sign($sig);

            $params = array();
            $params["method"] = $method;
            $params["api_key"] = $apiKey;
            $params["api_sig"] = $apiSig;
            $params["frob"] = $frob;
            $conn = new RestConnection(
                    "http://api.flickr.com/services/rest/", null, $params);
            $res = $conn->get();
            $result = $res->getResponseBody();
            $result = str_replace("<", "&lt;", $result);
            $result = str_replace(">", "&gt;", $result);

            try {
                $ts = strpos($result, "&lt;token&gt;") + 13;
                $te = strpos($result, "&lt;", $ts);
                $len = $te - $ts;
                $authToken = substr($result, $ts, $len);
                self::saveSession(_AUTH_TOKEN, $authToken);
            } catch (Exception $ex) {
                throw new Exception("Failed to get authentication token: " . $result);
            }

            $returnUrl = self::getSession(_RETURN_URL);
            if ($returnUrl != null) {
                self::deleteSession(_RETURN_URL);
                self::doRedirect($returnUrl . "?auth_token=" . $authToken);
                exit(0);
            }
        } else {
            self::sessionInit();
            self::saveSession(_RETURN_URL, $_SERVER['REQUEST_URI']);
            self::doRedirect(self::$LOGIN_URL . "?rUrl=" . $_SERVER['REQUEST_URI']);
            exit(0);
        }
    }

    private static function sessionInit() {
        session_start();
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        session_destroy();
    }

    public static function getSession($name) {
        if ($_SESSION[$name] != null)
            return $_SESSION[$name];
        else
            return $_COOKIE[_SAAS . $name];
    }

    public static function saveSession($name, $value) {
        setcookie(_SAAS . $name, $value, time() + _EXPIRE, "/");
        $_SESSION[$name] = $value;
    }

    public static function deleteSession($name) {
        setcookie(_SAAS . $name, "", time() - _EXPIRE, "/");
        $_SESSION[$name] = null;
    }

    public static function logout() {
        
    }

    public static function sign($params) {
        $sign = '';
        $values = array();
        ksort($params);
        foreach ($params as $k => $v) {
            $sign .= $k . $v;
        }
        return md5(self::getSecret() . $sign);
    }

    public static function doRedirect($url) {
        printf("<html>");
        printf("<head>");
        printf("<title></title>");
        printf("<meta http-equiv=\"refresh\" content=\"3; URL=" . $url . "\">");
        printf("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">");
        printf("</head>");
        printf("<body>");
        printf("<p align=\"center\">Redirecting...</p>");
        printf("<p align=\"center\">");
        printf("<a href= \"" . $url . "\">" . $url . "</a></p>");
        printf("<p align=\"center\">If you are not redirected automatically within a few seconds then please click on the link above.</p>");
        printf("</body>");
        printf("</html>");
    }

}

?>

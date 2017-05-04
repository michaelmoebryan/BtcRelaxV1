<?php

namespace BtcRelax;

use BtcRelax\BitID;
use BtcRelax\Config;
use BtcRelax\DAO;
use BtcRelax\SessionExpiredException;
use BtcRelax\Utils;
use Exception;


final class SecureSession {

    const BITID_KEY = '_bitid';
    const CUST_ID = '_custid';
    const STATUS_UNAUTH = "UNAUTHENTICATED";
    const STATUS_GUEST = "GUEST";
    const STATUS_USER = "USER";
    const STATUS_ROOT = "ROOT";
    const STATUS_BANNED = "BANNED";

    private $bitid = null;
    private $start_time = null;
    private $timeout = 3600;
    private $customer = null;
    private $nonce = null;
    protected $useProxy = false;

    /**
     * List of trusted proxy IP addresses
     *
     * @var array
     */
    protected $trustedProxies = array();

    /**
     * HTTP header to introspect for proxies
     *
     * @var string
     */
    protected $proxyHeader = 'HTTP_X_FORWARDED_FOR';

    public function __constructor() {
    }
    
    public function init()
    {
        //\session_set_cookie_params(self::$timeout);
        if (!$this->is_session_started()) {
            $this->startSession();
        } else {
            $isValid = $this->Check();
            if (!$isValid) {
                $this->killSession();
                $this->startSession();
            }
        }
     
    }
    

    private function startSession() {
        session_start();
        if (isset($_SESSION['last_active']) == false) {
            $_SESSION['start_time'] = time();
        }
        $_SESSION['last_active'] = time();
   }
   


    public function setNonce($nonce) {
        $this->setValue('nonce', $nonce);

        
    }

    public function setBitid($bitid) {
        $this->setValue("bitid", $bitid);
    }

    public function setCustomer($customer) {
        $this->setValue("customer", $customer);
    }

    public function getNonce() {
        $this->nonce = $this->getValue("nonce");
        $copy = $this->nonce;
        return $copy;
    }

    public function getCustomer() {
        $this->customer = $this->getValue("customer");
        $copy = $this->customer;
        return $copy;
    }

    public function getBitid() {
        $this->bitid = $this->getValue("bitid");
        $copy = $this->bitid;
        return $copy;
    }

    public function Authenticate(\BtcRelax\User $user = null) {
        $result = false;
        if (empty($user))
            {
                $bitid = new BitID();
                $nonce = $bitid->generateNonce();
                $this->setNonce($nonce);
                $config = Config::getConfig();
                $server_url = $config['SERVER_URL'];
                $bitid_uri = $bitid->buildURI($server_url . 'callback.php', $nonce);
                $this->setValue('bitid_uri', $bitid_uri);
                $qr_uri = $bitid->qrCode($bitid_uri);
                $this->setValue('qr_uri', $qr_uri);
                $ajax_uri = $server_url . 'ajax.php';
                $this->setValue('ajax_uri', $ajax_uri);
                $user_uri = Utils::createLink('user');
                $this->setValue('user_uri', $user_uri);
                $dao = new DAO();
                $remoteIp = $_SERVER['REMOTE_ADDR'];
                $this->setValue('remote_ip',$remoteIp); 
                $dao->insert($nonce,$remoteIp);
                $result = array('bitid_uri' => $bitid_uri, 'qr_uri' => $qr_uri, 'ajax_uri' => $ajax_uri , 'user_uri' => $user_uri ); 
            }
            else
            {
                $customer = $user->getCustomer();
                $this->setCustomer($customer);
                $result = true;
            }
        return $result;
    }

    public function hasBitid() {
        return $this->getValue('bitid');
    }

    public function hasCustomer() {
        return $this->getValue('customer');
    }

    public function hasNonce() {
        return $this->getValue('nonce');
    }

    public function Check() {
        $result = false;        
        if ((isset($_SESSION['last_active']) && (time() < ($_SESSION['last_active'] + $this->timeout )))) {
            $result = true;
        }
        return $result;
    }

    public function is_session_started() {
        if (php_sapi_name() !== 'cli') {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            } else {
                return session_id() === '' ? FALSE : TRUE;
            }
        }
        return FALSE;
    }

    public function setValue($session, $value) {
        if (is_string($value))
        {
            SecureSession::logMessage(\sprintf("Session id:%s saved value:%s for key:%s",session_id(),$value,$session ),Log::DEBUG);      
        }
        else {
            SecureSession::logMessage(\sprintf("Session id:%s saved value of type:%s for key:%s",session_id(),gettype($value),$session ),Log::DEBUG);    
        }
        if ($this->is_session_started()) {
            $_SESSION[$session] = $value;
            $_SESSION['last_active'] = time();
        }
    }

    
    public function getValue($session) {
        if ($this->is_session_started()) {
            if (isset($_SESSION[$session])) {
                if ($this->Check()) {
                    $_SESSION['last_active'] = time();
                    return $_SESSION[$session];
                } else {
                    //error_log();                                    
                    $this->killSession();
                    return new SessionExpiredException(session_id());
                }
            }
        } else {
            return null;
        }
    }
    
    public function clearValue($session)
    {
        unset($_SESSION[$session]);
    }

    public function killSession() {
        $_SESSION = array();
        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (isset($_COOKIE[session_name()])):
            setcookie(session_name(), '', time() - 7000000, '/');
        endif;
        \session_destroy();
        return;
    }

    function GetNewPair() {
        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 384,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        // Create the private and public key
        $res = openssl_pkey_new($config);

        // Extract the private key from $res to $privKey
        openssl_pkey_export($res, $privKey);

        // Extract the public key from $res to $pubKey
        $pubKey = openssl_pkey_get_details($res);
        $pubKey = $pubKey["key"];

        $result = ['PrivateKey' => $privKey, 'PublicKey' => $pubKey];
        return $result;
    }
    
    public static function logMessage($msg,$logLevel)
	{
            try
            {
                Log::general($msg,$logLevel);
            }
            catch (Exception $ex)
            {
                throw new Exception('Critical permissions, denied!!!');
            }
        }
 
    public static function allStatuses() {
        return [
            self::STATUS_UNAUTH,
            self::STATUS_GUEST,
            self::STATUS_USER,
            self::STATUS_ROOT,
            self::STATUS_BANNED
        ];
    }


    public function getIpAddress()
    {
        $ip = $this->getIpAddressFromProxy();
        if (FALSE == $ip) {
            // direct IP address
            if (isset($_SERVER['REMOTE_ADDR'])) {
               $ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
            }
        }
        return $ip;
    }

    protected function getIpAddressFromProxy()
    {
        if (!$this->useProxy
            || (isset($_SERVER['REMOTE_ADDR']) && !in_array($_SERVER['REMOTE_ADDR'], $this->trustedProxies))
        ) {
            return false;
        }

        $header = $this->proxyHeader;
        if (!isset($_SERVER[$header]) || empty($_SERVER[$header])) {
            return false;
        }

        // Extract IPs
        $ips = explode(',', $_SERVER[$header]);
        // trim, so we can compare against trusted proxies properly
        $ips = array_map('trim', $ips);
        // remove trusted proxy IPs
        $ips = array_diff($ips, $this->trustedProxies);

        // Any left?
        if (empty($ips)) {
            return false;
        }

        // Since we've removed any known, trusted proxy servers, the right-most
        // address represents the first IP we do not know about -- i.e., we do
        // not know if it is a proxy server, or a client. As such, we treat it
        // as the originating IP.
        // @see http://en.wikipedia.org/wiki/X-Forwarded-For
        $ip = array_pop($ips);
        return $ip;
    }
    
}

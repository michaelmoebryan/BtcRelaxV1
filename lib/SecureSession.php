<?php

namespace BtcRelax;

    use BtcRelax\SessionExpiredException;    
    use \BtcRelax\Config;
    use \BtcRelax\DAO;
    use \BtcRelax\Utils;
    use \BtcRelax\BitID;
    
class SecureSession {
	const BITID_KEY = '_bitid';
	const CUST_ID = '_custid';
    
        const STATUS_UNAUTH = "UNAUTHENTICATED";
        const STATUS_GUEST = "GUEST";
        const STATUS_USER = "USER";
        const STATUS_ROOT = "ROOT";
        const STATUS_BANNED = "BANNED";

	private $bitid = null;
        private $start_time = null;
	static $timeout = 3600;   
	private $customer = null;
	private $nonce = null;
   

    public function _constructor() {             
            \session_set_cookie_params(self::$timeout);
            if (!is_session_started())
            {
                  $this->startSession();
            }
            else
            {
                $isValid = $this->Check();
                if (!$isValid)
                {
                    $this->killSession();
                    $this->startSession();
                    
                }
            }
        }

        private function startSession()
        {
            session_start();
            if (isset($_SESSION['last_active']) == false)
                {
                    $_SESSION['start_time'] = time();
                }
            $_SESSION['last_active'] = time();
        }

	  public  function setBitid($bitid) {
		$this->setVal("bitid", $bitid);
	  } 
	  
	  public function setCustomer($customer)
	  {
                $this->setVal("customer", $customer);    	  
	  }
	  
	  public  function getCustomer()
	  {
                $this->customer = $this->getValue("customer");
                $copy = $this->customer;
                return $copy;           
	  }
	   
	  
	  public function getBitid() 
          {
		$this->bitid = $this->getValue("bitid");		
                $copy = $this->bitid;
		return $copy;
	  }
          
	  public  function Authenticate()
          {
                $bitid = new BitID();
                $nonce = $bitid->generateNonce();
                $this->setVal('nonce', $nonce);
                $config = Config::getConfig('BitId');      
                
                $bitid_uri = $bitid->buildURI( $config['SERVER_URL'] . 'callback.php', $nonce);
                $this->setVal('bitid_uri', $bitid_uri);
                
                $qr_uri = $bitid->qrCode($bitid_uri);
                $this->setVal('qr_uri', $qr_uri);
                
                $ajax_uri = $config['SERVER_URL'] . 'ajax.php';
                $this->setVal('ajax_uri', $ajax_uri);
                
                $user_uri = Utils::createLink('user'); 
                $this->setVal('user_uri', $user_uri);
                
                $dao = new DAO();
                $dao->insert($nonce, $_SERVER['REMOTE_ADDR']);               
                return ;                
          }
          
  
	  public function hasBitid() {
		return $this->getValue('bitid');
	  }
			   
	  public function hasCustomer() {
		return $this->getValue('customer');
	  }
          
          public function hasNonce() 
          {
              return $this->getValue('nonce'); 
          }

          public function Check()
	  {
		$result = false;
                $sTimeout = self::$timeout;
		if ( (isset($_SESSION['last_active']) && (time() < ($_SESSION['last_active']+ $sTimeout ))))
			{
				$result = true;
			}          
		return $result;
	  }
  
          public static function is_session_started()
            {
                    if ( php_sapi_name() !== 'cli' ) {
                            if ( version_compare(phpversion(), '5.4.0', '>=') ) {
                                    return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
                            } else {
                                    return session_id() === '' ? FALSE : TRUE;
                            }
                    }
                    return FALSE;
            }
	
          public function setVal($session, $value) {
		if(self::is_session_started()) 
		{
			$_SESSION[$session] = $value;
			$_SESSION['last_active'] =  time();
		}
    }
 
          public function getValue($session) {	  
		if (self::is_session_started())
		{
			if (isset($_SESSION[$session]))
			{
				if ($this->Check())
				{
					$_SESSION['last_active'] =  time();
					return $_SESSION[$session]; 
				}
                                else
                                {
                                    //error_log();                                    
                                    $thid->killSession();
                                    return new SessionExpiredException(session_id());
                                }
                                   
			}            
		}
		else
		{
			return null;
		}
    }
 
          public function killSession()
          {
              	$_SESSION = array();
                // If it's desired to kill the session, also delete the session cookie.
                // Note: This will destroy the session, and not just the session data!
                if(isset($_COOKIE[session_name()])):
                    setcookie(session_name(), '', time()-7000000, '/');
		endif;  
                \session_destroy();
                return;
          }
 
          function GetNewPair ()
		{
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

			$result = array ('PrivateKey'=>$privKey, 'PublicKey'=>$pubKey);
			return $result;
		}
	  

        public static function allStatuses() {
            return [
                self::STATUS_UNAUTH,
                self::STATUS_GUEST,
                self::STATUS_USER,
                self::STATUS_ROOT,
                self::STATUS_BANNED
                ];}
            
      
        public function getActualStatus()
        {
            $result = self::STATUS_UNAUTH;
            if ($this->hasCustomer())
            {
                $cust = $this->getCustomer();
                /// TODO: Check are its root user? 
            }
            else
            {
                if ($this->hasBitid())
                {
                    $result = self::STATUS_GUEST;
                }
            }
            return $result;              
        }
}

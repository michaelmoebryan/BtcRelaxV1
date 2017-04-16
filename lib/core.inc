<?php
namespace BtcRelax;

use BtcRelax\Model\Order;
use BtcRelax\NotFoundException;
use BtcRelax\SecureSession;
use BtcRelax\SessionExpiredException;
use const E_STRICT;
require_once __DIR__.'/logger.php';

    final class Core {

	const DEFAULT_PAGE = 'main';
	const PAGE_DIR = '/lib/page/';
	const LAYOUT_DIR = '/lib/layout/';

	private static $CLASSES = [
		'BtcRelax\DAO' => '/dao/DAO.php',
		'BtcRelax\Config' => '/config/config.php',
		'BtcRelax\Flash' => '/flash/flash.php',
		'BtcRelax\NotFoundException' => '/exception/NotFoundException.php',
            	'BtcRelax\SessionExpiredException' => '/exception/SessionExceptions.php',
		'BtcRelax\BaseDao' => '/dao/BaseDao.php',
		'BtcRelax\BookmarkDao' => '/dao/BookmarkDao.php',
		'BtcRelax\Dao\BookmarkSearchCriteria' => '/dao/BookmarkSearchCriteria.php',
		'BtcRelax\OrderDao' => '/dao/OrderDao.php',
                'BtcRelax\Dao\OrderSearchCriteria' => '/dao/OrderSearchCriteria.php',
                'BtcRelax\CustomerDao' => '/dao/CustomerDao.php',
		'BtcRelax\Mapping\BookmarkMapper' => '/mapping/BookmarkMapper.php',
		'BtcRelax\Mapping\CustomerMapper' => '/mapping/CustomerMapper.php',
                'BtcRelax\Mapping\OrderMapper' => '/mapping/OrderMapper.php',
		'BtcRelax\Model\Bookmark' => '/model/bookmark.php',
		'BtcRelax\Model\Customer' => '/model/customer.php',
                'BtcRelax\Model\Order' => '/model/order.php',
		'BtcRelax\Validation\BookmarkValidator' => '/validation/BookmarkValidator.php',
		'BtcRelax\Validation\CustomerValidator' => '/validation/CustomerValidator.php',
                'BtcRelax\Validation\OrderValidator' => '/validation/OrderValidator.php',
		'BtcRelax\Validation\ValidationError' => '/validation/ValidationError.php',
		'BtcRelax\Utils' => '/utils/utils.php',
		'BtcRelax\BitID' => '/BitID.php',
		'BtcRelax\LayoutHeader' => '/header.inc',
		'BtcRelax\Dhtmlx' => '/dhtmlx.inc',
		'BtcRelax\SecureSession' => '/SecureSession.php',
                'BtcRelax\AM' => '/AM.php',
                'BtcRelax\OM' => '/OM.php',
                'BtcRelax\RE' => '/RE.php',
                'BtcRelax\User' => '/usr.php',
                'BtcRelax\RestResponse' => '/RestResponse.php',
                'BtcRelax\RestConnection' => '/RestConnection.php',
                'Geary' => '/external/Geary.php',
                'QRcode' => '/classes/QRcode.php'
           ];
    
        protected $current_session;
        protected $root_user_id;
        protected $isAllowFreeRegistration;
        protected $host_name;
        protected $config;
        public $am_instance;
        public $om_instance;
        public $re_instance;
        public $state;
        public $version;
        
        
	public function __construct() {    
            $this->version = '1.1.1.5';
            global $cookie_lifetime;
            if(isset($cookie_lifetime) && $cookie_lifetime != "") {
                ini_set("session.cookie_lifetime", $cookie_lifetime);
            }
        }

	public function init() {
		// error reporting - all errors for development (ensure you have display_errors = On in your php.ini file)
		Log::general(sprintf('Start to initialize core version:%s',$this->version ), Log::DEBUG);
                error_reporting(E_ALL | E_STRICT);
		mb_internal_encoding('UTF-8');       
                set_exception_handler([$this, 'handleException']);
		spl_autoload_register([$this, 'loadClass']);
                try
                {
                    $this->config = Config::getConfig();     
                    $this->root_user_id = $this->config['HUB_ROOT']; 
                    $this->isAllowFreeRegistration = $this->config['IS_FREE_REGISTER'];

                    $this->re_instance = new RE();
                    $this->am_instance = new AM($this->root_user_id,$this->isAllowFreeRegistration);                
                    $this->om_instance = new OM();

                    $cur_session = new SecureSession();
                    $cur_session->init();
                    $this->current_session = $cur_session;
                }
                catch (Exception $exc)
                {
                    Log::general(sprintf("Core initialization error: %s", $exc->getMessage()));
                    throw $exc;
                }
        }
        


        
	public function getAM()
        {
            return $this->am_instance;
        }
        
        public function getOM()
        {
            return $this->om_instance;
        }
        
        public function getRE()
        {
            return $this->re_instance;
        }
        
	public function setBitId($vBitId)
	{
            //if ($this->current_session->hasCustomer() === false && $vBitId !== null) {
            if ($vBitId !== null){
            $dao = new DAO();
            $idCustomer = $dao->customerByBitId($vBitId);
            if (isset($idCustomer) && ($idCustomer !== false)) {
                $custDao = new CustomerDao();
                $newCustomer = $custDao->findById($idCustomer);
                $this->current_session->setCustomer($newCustomer);
                //Flash::addFlash("Добро пожаловать. Вы успешно вошли в систему!");
                return true;
            } else {
                    $this->current_session->setBitid($vBitId);
                    return true;
                    }
        } else 
            {
                return false;
            }
        }
	
	public function getBitId()
	{
            return $this->current_session->getBitid();
	}    

        public function getSelectedBookmark()
        {
            $result = null;
            $selectedBookmarkId = $this->current_session->getValue('selectedBookmarkId');
            if ($selectedBookmarkId != null)
            {
                $bookmarkDao = new BookmarkDao();
                $result  = $bookmarkDao->findById($selectedBookmarkId);
            
            }
            return $result;
        }
        
        public function setCurrentOrder(Order $order = null)
        {
            $this->current_session->setValue('currentOrder',$order);
            // Clear selected bookmark if we already have order
            $this->current_session->clearValue('selectedBookmarkId');             
        }
        
        public function getCurrentOrder()
        {
            $result = $this->current_session->getValue('currentOrder');
            return $result;
        }
               
	public function getCustomer()
	{
            return $this->current_session->getCustomer();
	}
        
        public function getUser()
	{
            $result = null;
            try
            {
                $cust = $this->current_session->getCustomer();
                if (isset($cust))
                {
                    $cUser = new User();
                    $cUser->init($cust->getIdCustomer());
                    $result = $cUser;                
                }   
            }
            catch (Exception $ex)
            {
                Log::general('Error while getting user:' . $ex->getMessage(),Log::ERROR);
            }
            return $result;
        }

        public function killSession()
        {
            $this->current_session->killSession();
        }
        
	public function isAuthenticated()
	{
		$result = ($this->getSessionState() !==  SecureSession::STATUS_UNAUTH);   
		return $result;	
	}
	
       
        public function setAuthenticate()
        {
            $result =  $this->current_session->Authenticate();
            return $result;
        }
	
        public function getSessionState()
        {
                $result = SecureSession::STATUS_UNAUTH;
                if ($this->current_session->hasCustomer()) {
                    $customerId = $this->getCustomer()->getIdCustomer();
                    if ($customerId === $this->root_user_id) {
                        $result = SecureSession::STATUS_ROOT;
                    } else {
                        $result = SecureSession::STATUS_USER;
                    }
                } else {
                    if ($this->current_session->hasBitid()) {
                        $result = SecureSession::STATUS_GUEST;
                    }
                }
                return $result;
        }
        
        public function getNonce()
        {
            $result = $this->current_session->getNonce();
            return $result;
        }
        
	public function getDefaultPage()
	{
	    $actualState = $this->getSessionState();
            switch ($actualState) {
            case SecureSession::STATUS_USER:
              $result_page = 'user';            
                break;
            case SecureSession::STATUS_GUEST:
                $result_page = 'guest';
                break;
            case SecureSession::STATUS_ROOT:
                $result_page = 'user';       
                break;
            default:
                $result_page = 'main';
               break;
            }       
            return $result_page;
	}
	 
	public function run() {
	$nonce = Utils::tryGetParam('nonce');
        $getBookmarkVar = Utils::tryGetParam('getBookmark');		
        $extraParams = array();
        if (($nonce !== false) && (!empty($nonce)))
                    {
                        $dao = new DAO();                   
                        $addr = $dao->address($nonce, @$_SERVER['REMOTE_ADDR']);
                        if (isset($addr))
                        {
                            $this->setBitid($addr);
                        }
                    }
        if (isset($getBookmarkVar) && (is_numeric($getBookmarkVar)))
        {                    
                $this->current_session->setValue('selectedBookmarkId',$getBookmarkVar);
        }
        if (isset($_REQUEST))
        {
            $extraParams = $_REQUEST;
        }
	$this->runPage($this->getPage(),$extraParams);
	}


	public function handleException($ex) {
		$extra = ['message' => $ex->getMessage()];
		switch ($ex) {
                    case $ex instanceof NotFoundException:
			header('HTTP/1.0 404 Not Found');
			$this->runPage('404', $extra);
                        break;
                    case $ex instanceof SessionExpiredException:
                        header('Session was expired');
                        $this->runPage('main',$extra);
                    default:
                        header('HTTP/1.1 500 Internal Server Error');
			$this->runPage('500', $extra);
                        break;
                }
 
	}

	public function loadClass($name) {
            Log::general(sprintf("Loading class name:%s", $name),Log::DEBUG); 
		if (!$this->TryToAutoload($name))
			{
				if (!class_exists($name))
				{
					if (!array_key_exists($name, self::$CLASSES)) {
								$this->logError("Critical!!! Class: " . $name . " not found! ");
								//die('Class "' . $name . '" not found.');                
							}
					require_once __DIR__ . self::$CLASSES[$name];                                
				}
			}
	}

	private function getPage() {
		//$page = self::DEFAULT_PAGE;
		$page = $this->GetDefaultPage();            
		if (array_key_exists('page', $_GET)) {
			$page = $_GET['page'];
		}
		return $this->checkPage($page);
	}

	private function checkPage($page) {
		if (!preg_match('/^[a-z0-9-]+$/i', $page)) {
			// TODO log attempt, redirect attacker, ...
			throw new NotFoundException('Unsafe page "' . $page . '" requested');
		}
		if (!$this->hasScript($page)
				&& !$this->hasTemplate($page)) {
			// TODO log attempt, redirect attacker, ...
			throw new NotFoundException('Page "' . $page . '" not found');
		}
		return $page;
	}

	private function runPage($page, array $extra = []) {
		$run = false;
		if ($this->hasScript($page)) {
			$run = true;
			require $this->getScript($page);
		}
		if ($this->hasTemplate($page)) {
			$run = true;
			// data for main template
			$template = $this->getTemplate($page);
//			$flashes = null;
//			if (Flash::hasFlashes()) {
//				$flashes = Flash::getFlashes();
//			}

			// main template (layout)
			require $_SERVER['DOCUMENT_ROOT'] . self::LAYOUT_DIR . 'index.phtml';
		}
		if (!$run) {
			die('Page "' . $page . '" has neither script nor template!');
		}
	}

	private function logError($error)
	{
            try
            {
                Log::general($error);
                //$this->current_session->logMessage($error);
            }
            catch (Exception $exc)
            {
                $date = date('d/m/Y h:i:s a', time());
                error_log($date . "Core unable to use logger, because:" . $exc->getMessage() . ". While catched error:" . $error.PHP_EOL, 3, __DIR__ . "/core-errors.log");
            }
        }
	
	private function getScript($page) {
		return $_SERVER['DOCUMENT_ROOT'] . self::PAGE_DIR . $page . '.php';
	}

	private function getTemplate($page) {
		return $_SERVER['DOCUMENT_ROOT'] . self::PAGE_DIR . $page . '.phtml';
	}

	private function hasScript($page) {
		return file_exists($this->getScript($page));
	}

	private function hasTemplate($page) {
		return file_exists($this->getTemplate($page));
	}
	
	function TryToAutoload($f) 
	{
		$result= false;	
		$base = dirname(__FILE__)."/";
			$interfaceFile = $base . "classes/interface/" . $f . "Interface.php";

			if (file_exists($interfaceFile)) {
				require_once $interfaceFile;
				$result = true;
			}

			$classFile = $base . "classes/" . $f . ".php";
			if (file_exists($classFile)) {
				require_once $classFile;
				$result = true;
			}

			$utilFile = $base . "classes/util/" . $f . ".php";
			if (file_exists($utilFile)) {
				require_once $utilFile;
			$result = true;
			}
		return $result;    
	}

}

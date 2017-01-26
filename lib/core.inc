<?php
namespace BtcRelax;

use BtcRelax\Flash;
use BtcRelax\NotFoundException;
use BtcRelax\SessionExpiredException;
use BtcRelax\SecureSession;


	
	
    final class Core {

	const DEFAULT_PAGE = 'shop';
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
		'BtcRelax\CustomerDao' => '/dao/CustomerDao.php',
		'BtcRelax\Mapping\BookmarkMapper' => '/mapping/BookmarkMapper.php',
		'BtcRelax\Mapping\CustomerMapper' => '/mapping/CustomerMapper.php',
		'BtcRelax\Model\Bookmark' => '/model/bookmark.php',
		'BtcRelax\Model\Customer' => '/model/customer.php',
		'BtcRelax\Validation\BookmarkValidator' => '/validation/BookmarkValidator.php',
		'BtcRelax\Validation\CustomerValidator' => '/validation/CustomerValidator.php',
		'BtcRelax\Validation\ValidationError' => '/validation/ValidationError.php',
		'BtcRelax\Utils' => '/utils/utils.php',
		'BtcRelax\BitID' => '/BitID.php',
		'BtcRelax\LayoutHeader' => '/header.inc',
		'BtcRelax\Dhtmlx' => '/dhtmlx.inc',
		'BtcRelax\SecureSession' => '/SecureSession.php',
	];
    
        private $current_session = null;
        public $state;
        
	public function __construct() {    

        }
	/**
	 * System config.
	 */
	public function init() {
		// error reporting - all errors for development (ensure you have display_errors = On in your php.ini file)
		error_reporting(E_ALL | E_STRICT);
		mb_internal_encoding('UTF-8');
		set_exception_handler([$this, 'handleException']);
		spl_autoload_register([$this, 'loadClass']);	               
                $this->current_session = new \BtcRelax\SecureSession();
                
        }
	
	public function setBitId($vBitId)
	{
            if ($this->current_session->hasCustomer() === false && $vBitId !== null) {
            $dao = new DAO();
            $idCustomer = $dao->customerByBitId($vBitId);
            if (isset($idCustomer) && ($idCustomer !== false)) {
                $custDao = new CustomerDao();
                $newCustomer = $custDao->findById($idCustomer);
                $this->current_session->setCustomer($newCustomer);
                Flash::addFlash("Добро пожаловать. Вы успешно вошли в систему!");
                return true;
            } else {
                $this->current_session->setBitid($vBitId);
                return true;
            };
        } else {
            return false;
            }
        }
	
	public function getBitId()
	{
		$this->current_session->getBitid();
	}    

	public function getCustomer()
	{
            $this->current_session->getCustomer();
	}
 
	public function isAuthenticated()
	{
		$result = ($this->current_session->getActualStatus() !==  SecureSession::STATUS_UNAUTH);   
		return $result;	
	}
	
	public function isGuest()
	{              
		$result = ($this->current_session->getActualStatus() == SecureSession::STATUS_GUEST) ;   
		return $result;				
	}   
	
	public function getDefaultPage()
	{
	    $actState = $this->current_session->getActualStatus();
            switch ($actState) {
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
		//$nonce = Utils::tryGetUrlParam('nonce');
        $nonce = $this->current_session->getValue('nonce');		
        if (($nonce !== false) && (!empty($nonce)))
		{
                    $dao = new DAO();                   
                    $addr = $dao->address($nonce, @$_SERVER['REMOTE_ADDR']);
                    if (isset($addr))
                    {
			$this->setBitid($addr);
                    }
		}
		$this->runPage($this->getPage());
	}

	/**
	 * Exception handler.
	 */
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

	/**
	 * Class loader.
	 */
	public function loadClass($name) {
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
			$flashes = null;
			if (Flash::hasFlashes()) {
				$flashes = Flash::getFlashes();
			}

			// main template (layout)
			require $_SERVER['DOCUMENT_ROOT'] . self::LAYOUT_DIR . 'index.phtml';
		}
		if (!$run) {
			die('Page "' . $page . '" has neither script nor template!');
		}
	}

	private function logError($error)
	{
		$date = date('d/m/Y h:i:s a', time());
		error_log($date . ":" . $error.PHP_EOL, 3, __DIR__ . "/logic-errors.log");
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



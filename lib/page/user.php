<?phpnamespace BtcRelax;use BtcRelax\Config;use BtcRelax\Dao\BookmarkSearchCriteria;use BtcRelax\SecureSession;use BtcRelax\Utils;use BtcRelax\Validation\BookmarkValidator;               global $core;                        $status = $core->getSessionState();    $cUser = $core->getUser();        switch ($status)    {        case SecureSession::STATUS_ROOT:            $header = new LayoutHeader($cUser);            break;        case SecureSession::STATUS_USER:            break;        case SecureSession::STATUS_GUEST:            Utils::redirect('guest');            break;        default:            Utils::redirect('main');            break;         }    $currentOrder = $core->getCurrentOrder();    /* @var $core type */    $om = $core->getOM();    $activeOrder = $om->getOrdersByUser($cUser,true);        if (FALSE !== $activeOrder)    {        LOG::general("Active users order not found.",LOG::INFO);        $core->setCurrentOrder($activeOrder);        $currentOrder = $activeOrder;    }    else	{		LOG::general("Active user order found.",LOG::INFO);	}    if (array_key_exists("getBookmark", $extra))            {		$selectedBookmarkId = $extra["getBookmark"];    		LOG::general(sprintf("User try to get bookmarkId:%s", $selectedBookmarkId));		if ($selectedBookmarkId != null)            {                $dao = new BookmarkDao();                $selectedBookmark = $dao->findById($selectedBookmarkId);                $currentOrder = $om->createNewOrder($cUser, $selectedBookmark );                if ($currentOrder != null) {                    $core->setCurrentOrder($currentOrder);                }            };    }    if ($currentOrder !== null)    {            $currentState = $currentOrder->getState();            switch ($currentState){                case Model\Order::STATUS_PAID:                    if (array_key_exists("setPointCatched", $extra))                    {                        $bookmarkId = $extra['setPointCatched'];                        $om->setPointCatched($currentOrder, $bookmarkId);                    }                    $filledOrder =  $om->fillBookmarksByOrder($currentOrder);                    if ($filledOrder != null)                    {                        $currentOrder = $filledOrder;                        $core->setCurrentOrder($filledOrder);                    }                    break;                case Model\Order::STATUS_WAIT_FOR_PAY:                    $checkResult = $om->checkPaymentByOrder($currentOrder);                    break;                case Model\Order::STATUS_CREATED:                        if (array_key_exists("isConfirmed", $extra))                        {                            $isOrderConfirmed = filter_var($extra["isConfirmed"], FILTER_VALIDATE_BOOLEAN);                            if ($isOrderConfirmed === false)                            {                                $core->setCurrentOrder();                            }                            else                            {                                $registerResult = $om->tryConfirmOrder($currentOrder);                                if (FALSE === $registerResult)                                    {                                        $currentOrder = $registerResult;                                     }                                else                                {                                    $core->setCurrentOrder($registerResult);                                }                            }                              }                    break;                default:                        break;            };    };       function actionGetActiveBookmarks() {                        $dao = new BookmarkDao();                        $status = BookmarkValidator::validateStatus('Published');                        $search = (new BookmarkSearchCriteria())->setStatus($status);                        $bookmarksList = $dao->find($search);                        return $bookmarksList;    }        function renderGetOwnedOrder(\BtcRelax\Model\Order $pOrder) {        $result = "";        if ($pOrder->getOwnedPointCount() > 0 )        {            $bList = $pOrder->getBuyedPoints();            foreach ($bList as $curPoint)            {               $result    = $curPoint->getPrivateForm();            }        }        return $result;    }        function renderGetActiveBookmarks() {        $result = "";        $bList = actionGetActiveBookmarks();        if (empty($bList))        {            $result = "<button class=\"button button3\" ><h3>No active bookmarks now!</h3></button>";        }        else        {		$pLang = 'ru';                foreach ($bList as $curPoint) 		{                    $result  .= $curPoint->GetPublicForm($pLang);								 		}        }        return $result;    }    if ( !function_exists('sys_get_temp_dir')) {         function sys_get_temp_dir() {             if (!empty($_ENV['TMP'])) { return realpath($_ENV['TMP']); }             if (!empty($_ENV['TMPDIR'])) { return realpath( $_ENV['TMPDIR']); }             if (!empty($_ENV['TEMP'])) { return realpath( $_ENV['TEMP']); }             $tempfile=tempnam(__FILE__,'');             if (file_exists($tempfile)) {             unlink($tempfile);         return realpath(dirname($tempfile));         }     return null;   } } 
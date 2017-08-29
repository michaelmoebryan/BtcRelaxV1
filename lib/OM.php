<?php
namespace BtcRelax;
use \BtcRelax\RE;
use \BtcRelax\OrderDao;
use BtcRelax\Core;

require 'OMInterface.php';

class OM implements IOM {

    public function createNewOrder(\BtcRelax\User $user, \BtcRelax\Model\Bookmark $bookmark) {
        $vRE = new RE();
        $vBTCPrice = $vRE->getBTCPrice($bookmark->getCustomPrice(),$bookmark->getPriceCurrency());
        $newOrder = new \BtcRelax\Model\Order($vBTCPrice, $user->getCustomerId(),$bookmark);
        return $newOrder;
    }

    public function tryConfirmOrder(Model\Order $order) {
        $result = false;
        $isError = false;
        try {
            $bookmark = $order->getBookmark();
            $targetAddress = $bookmark->getTargetAddress();
            $newInvoiceAddress = $this->getPaymentAddress($order);
            $order->setInvoiceAddress($newInvoiceAddress);          
        } catch (Exception $exc) {
            $isError = true;
            $order->setLastError("Ошибка при генерации адреса оплаты. Заказ, не может быть зарегистрирован.");
            $result= $order;
            Log::general($exc->getTraceAsString(), Log::WARN);
        }
        if (!$isError)
        {
            $orderDao = new OrderDao();
            $registerResult = $orderDao->registerOrder($order);
            if (FALSE !== $registerResult)
            {
                $result = $registerResult;     
            }            
        }
        return $result;
    }        
//        $gateway_id = '9ce69c112c43f8004d09d90c6aea3eabf98b03c3815240479a365dd0b97a40c6';
//        $gateway_secret = '4E2VT6UDKwKQbfotz8nMypSGGThC47nxrgjT4nxmdEx4T4EiWe1kmYZF4JQTD9xC';
//        $price = $order->getBTCPrice();
//        $keychain_id = 0;

    /// Return null if no changes happen, and object as a result of changes
    public function checkPaymentByOrder(Model\Order $order)
    {
        $result = false;
        $isNeedToUpdate = false;
        $priceStart = $order->getBalanceDate();
        if (isset($priceStart))
        {
            $priceStart->modify("+30 second");
            $now = new \DateTime('Now');
            if ($now >= $priceStart)
            {
                $isNeedToUpdate = true;
            }
        }
        else
        {
            $isNeedToUpdate = true;
        }
        if ($isNeedToUpdate)
        {
            $oldBalance = $order->getInvoiceBalance();
            $newBalance = $order->CheckPaymentAddress();
            if (!is_numeric($newBalance))
            {
                Log::general("Getting balance wrong result type!".$newBalance , Log::WARN);
            }
            if ($oldBalance != $newBalance)
            {
                $order->setInvoiceBalance($newBalance);
                $dao = new OrderDao();
                $updatedOrder = $dao->updateHotBalance($order);
                if (isset($updatedOrder))
                {
                    $result = $updatedOrder;
                }
            }
            else {
                $order->setBalanceDate(new \DateTime('now'));
            }
        }
        return $result;
    }
    
    public function getOrderById($orderId) {
                $dao = new \BtcRelax\OrderDao();
    		$result=$dao->findById($orderId);
		return $result;			
    }
    
    public function fillBookmarksByOrder(\BtcRelax\Model\Order $order)
    {
        $result = null;
        $dao = new BookmarkDao();
        $bookSearchCriteria = new Dao\BookmarkSearchCriteria();
        $bookSearchCriteria->setOrderId($order->getIdOrder());
        $bookMarkList = $dao->find($bookSearchCriteria);
        if (count($bookMarkList)>0)
        {
            $result = $order->setBuyedPoints($bookMarkList);
        }
        return $result;
    }
    
    private function getPaymentAddress(\BtcRelax\Model\Order $order)
    {
//        $result = null;
//        require_once __DIR__ . "/classes/BlockIo.php";
//        $apiKey = "eb79-6015-7777-f428";
//	$version = 2; // API version
//	$pin = "06102010";
//	$block_io = new \BlockIo($apiKey, $pin, $version);
//        $newAddr = $block_io->create_forwarding_address(array('to_address' => $targetAddress));
//        if ($newAddr->status == "success" )
//            {
//                $result = $newAddr->data->forwarding_address; 
//            }
//        return $result;
//          $result = null;
//          require_once __DIR__."/classes/Geary.php";
//          $gateway_id = '479129bfd293810337fb277b599089104baa5320c5289f44d6b46ebcb413148b';
//          $gateway_secret = 'RsPexuivx6Eao6W275MtmRjzp9EypbJUj6GS6mpnhLfjroDU4Pn72va8FUtoDnne';
//          
//          $price = $order->getBTCPrice();
//          
//          $geary = new \Geary($gateway_id,$gateway_secret);
//          $keychain_id = $geary->get_last_keychain_id();
//          $new_key = $keychain_id->last_keychain_id + 1;
//          $newOrder = $geary->create_order($price, $new_key);
//          $result = $newOrder->address;
            $result = false;
            global $core;
            $am = $core->getAM();
            $re = $core->getRE();
            $bookmark = $order->getBookmark();
            $droperId = $bookmark->getIdDroper();
            $user = $am->getUserById($droperId);
            //require_once 'HD.php';
            $xpub = $user->getXPub();
            $path = sprintf('0/%d',$user->getInvoicesCount());
            $hd = new \HD();
            $hd->set_xpub($xpub);
            $address = $hd->address_from_xpub($path);
            Log::general(sprintf("Generated new address:%s", $address), Log::INFO);
	    $balance = $re->getBallance($address);  
            if (FALSE !== $balance)
            {
                $dao = new CustomerDao();
                $dao->AddInvoiceAddressToXPub($xpub, $address, $balance );
                $result = $address;
            };
            return $result;
    }

    public function getOrdersByUser(\BtcRelax\User $user, $onlyActive=true) {
        $result = false;
        /* @var $onlyActive boolean */
        $orderSearchCriteria = new Dao\OrderSearchCriteria($user->getCustomerId(),$onlyActive);
        $dao = new \BtcRelax\OrderDao();
        $founded = $dao->find($orderSearchCriteria);
        $activeOrder = reset($founded);
        if (FALSE !== $activeOrder)
        {
            $result = $activeOrder;
        }
        return $result;
    }

    private function TryToGetBookmarkId($pBookMarkId) {
        $resultMessage = "Error";
        if (GetDbConnector($dbCon)) {
            $callQuery = "CALL `AttachPointToOrder`(?, ?, @pResultId)";
            $call = $dbCon->prepare($callQuery);
            $call->bind_param('ii', $this->pOrderId, $pBookMarkId);
            $call->execute();

            $select = $dbCon->query("SELECT  @pResultId");
            $result = $select->fetch_assoc();
            $pResultId = $result['@pResultId'];
            if ($pResultId == 100) {
                $resultMessage = null;
            } else {
                $resultMessage = $pResultId;
            }
        }
        return $resultMessage;
    }

    public function setPointCatched(Model\Order $order, $bookmarkId) {
        $dao = new \BtcRelax\BookmarkDao();
        $orderId = $order->getIdOrder();
        Log::general(sprintf("Setting point:%d catched by order id:%d", $bookmarkId,$orderId), Log::INFO);
        $dbResult = $dao->setBookmarkSaled($orderId, $bookmarkId);
        if ($dbResult !== false)
        {
            
        }
    }

}

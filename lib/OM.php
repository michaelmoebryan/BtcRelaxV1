<?php
namespace BtcRelax;
use \BtcRelax\RE;
use BtcRelax\OrderDao;

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
            $newInvoiceAddress = $this->getPaymentAddress($targetAddress);
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


    
    public function getOrderById($orderId) {        
    }
    
    private function getPaymentAddress($targetAddress)
    {
        $result = null;
        require_once __DIR__ . "/classes/BlockIo.php";
        $apiKey = "eb79-6015-7777-f428";
	$version = 2; // API version
	$pin = "06102010";
	$block_io = new \BlockIo($apiKey, $pin, $version);
        $newAddr = $block_io->create_forwarding_address(array('to_address' => $targetAddress));
        if ($newAddr->status == "success" )
            {
                $result = $newAddr->data->forwarding_address; 
            }
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

 }

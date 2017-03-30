<?php
namespace BtcRelax;
use \BtcRelax\RE;
require 'omI.php';

class OM implements IOM {

    public function createNewOrder(\BtcRelax\User $user, \BtcRelax\Model\Bookmark $bookmark) {
        $vRE = new RE();
        $vBTCPrice = $vRE->getBTCPrice($bookmark->getCustomPrice(),$bookmark->getPriceCurrency());
        $newOrder = new \BtcRelax\Model\Order($vBTCPrice, $user->getCustomerId(),$bookmark->getIdBookmark());
        return $newOrder;
    }

    public function tryConfirmOrder(Model\Order $order) {
        
    }

    
    public function getOrderById($orderId) {
        
    }

    public function getOrdersByUser(\BtcRelax\User $user, $onlyActive=true) {
        $orderSearchCriteria = new Dao\OrderSearchCriteria($user->getCustomerId(),$onlyActive);
        $dao = new \BtcRelax\OrderDao();
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

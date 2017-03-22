<?php

namespace BtcRelax;

use Exception;
use PDO;
use BtcRelax\Model\Bookmark;
use BtcRelax\OrderDao;
use BtcRelax\Model\Order;
use BtcRelax\Config;
use BtcRelax\User;

require 'omI.php';

class OM implements IOM {
    //put your code here
    
    
    public function CreateNewOrder(\BtcRelax\User $user, \BtcRelax\Model\Bookmark $bookmark) {
       // TODO: By default think that UAH but need to extend for any currency  
        $vUAHPrice = $bookmark->getCustomPrice();
        $vBTCPrice = GetBTCPriceByUAH($vUAHPrice);
        $result = new Model\Order($vBTCPrice, $user->getCustomerId());
        return $result;        
    }

    public function getOrderById($orderId) {
        
    }

    public function getOrdersByUser(\BtcRelax\User $user, $pOnlyActive = true) {
        $result = null;
        $orderSearchCriteria = new \BtcRelax\Dao\OrderSearchCriteria(null, $user->getCustomerId(),$pOnlyActive );
        $dao = new \BtcRelax\OrderDao();
        $activeOrder = $dao->find($orderSearchCriteria);
        if ($activeOrder != null) 
        {
            $result = $activeOrder;
        }
        return $result;
    }
    
   
//    private function TryToGetBookmarkId($pBookMarkId)
//    {
//			$resultMessage = "Error";
//			  if (GetDbConnector($dbCon))
//			  {
//				  $callQuery = "CALL `AttachPointToOrder`(?, ?, @pResultId)";
//				  $call = $dbCon->prepare($callQuery);
//				  $call->bind_param('ii', $this->pOrderId, $pBookMarkId);
//				  $call->execute();
//
//				  $select = $dbCon->query("SELECT  @pResultId");
//				  $result = $select->fetch_assoc();
//				  $pResultId    = $result['@pResultId'];
//				  if ( $pResultId == 100)
//				  {
//					$resultMessage = null;
//				  }
//				  else
//				  {
//					$resultMessage =   $pResultId;                    
//				  }
//			  }
//			  return $resultMessage;       
//    }
//    
    function GetBTCPriceByUAH($pUAHPrice)
    {
        $vRate = GetRateBy_btcbank();
        if (array_key_exists('avg', $vRate))
            {
		if (is_numeric($vRate['avg']))
                {
                    $vExchangeRate = $vRate['avg'];
                    $vOneUAHPrice = round( 1 / $vExchangeRate, 8, PHP_ROUND_HALF_UP);
                    $resultPrice = round($pUAHPrice * $vOneUAHPrice , 4, PHP_ROUND_HALF_UP);
                    if (is_numeric($resultPrice))
                    {
			if ($resultPrice > 0)
                            return $resultPrice;
                    }
		}
	}
    }
    
    function GetRateBy_btcbank()
    {
	$url = "http://btcbank.com.ua/rates.xml";
	$xmlfile = file_get_contents($url);         
        $rates =  simplexml_load_string($xmlfile);
	$inPrice ; 
			$outPrice;
			foreach ($rates->item as $item)
			{
				if ($item->from == 'BTC' && $item->to == 'P24UAH')
				{   
					$outPrice = $item->out;  
				};
				
				if ($item->from == 'P24UAH' && $item->to == 'BTC')
				{   
					$inPrice = $item->in; 
				};
							 
			}; 
			if (isset($inPrice) && isset($outPrice))
			{
				$avgPrice = round ( ($inPrice + $outPrice) / 2, 2, PHP_ROUND_HALF_UP); 
				return (array('out' => $outPrice, 'in' => $inPrice , 'avg' => $avgPrice ));                 
			};   

    }


}

<?php

namespace BtcRelax;

use BtcRelax\Dao\OrderSearchCriteria;
use BtcRelax\Model\Bookmark;
use BtcRelax\Model\Order;
use BtcRelax\User;
use function GetDbConnector;

require 'omI.php';

class OM implements IOM {
    //put your code here
    
    
    public function CreateNewOrder(User $user, Bookmark $bookmark) {
       // TODO: By default think that UAH but need to extend for any currency  
        $vUAHPrice = $bookmark->getCustomPrice();
        $vBTCPrice = $this->GetBTCPriceByUAH($vUAHPrice);
        $newOrder = new Order($vBTCPrice,$user->getCustomerId());
        return $newOrder;
    }

    public function getOrderById($orderId) {
        
    }

    public function getOrdersByUser(User $user,$onlyActive=true) {
        $orderSearchCriteria = new OrderSearchCriteria(null,$user->getCustomerId(), $onlyActive);
        $dao = new OrderDao();
    }
   
    private function TryToGetBookmarkId($pBookMarkId)
    {
			$resultMessage = "Error";
			  if (GetDbConnector($dbCon))
			  {
				  $callQuery = "CALL `AttachPointToOrder`(?, ?, @pResultId)";
				  $call = $dbCon->prepare($callQuery);
				  $call->bind_param('ii', $this->pOrderId, $pBookMarkId);
				  $call->execute();

				  $select = $dbCon->query("SELECT  @pResultId");
				  $result = $select->fetch_assoc();
				  $pResultId    = $result['@pResultId'];
				  if ( $pResultId == 100)
				  {
					$resultMessage = null;
				  }
				  else
				  {
					$resultMessage =   $pResultId;                    
				  }
			  }
			  return $resultMessage;       
    }
    
    protected  function GetBTCPriceByUAH($pUAHPrice)
    {
        //$vRate = $this->GetRate_BtcBank();
        $vRate = $this->GetRate_Kuna();
        if (array_key_exists('avg', $vRate))
            {
		if (is_numeric($vRate['avg']))
                {
                    $vExchangeRate = $vRate['avg'];
                    $vOneUAHPrice = round( 1 / $vExchangeRate, 8, PHP_ROUND_HALF_UP);
                    $resultPrice = round($pUAHPrice * $vOneUAHPrice , 4, PHP_ROUND_HALF_UP);
                    if (is_numeric($resultPrice))
                    {
                        return $resultPrice;   
                    }
		}
            }
    }
    
    protected function GetRate_Kuna()
    {
        $url = "https://kuna.io/api/v2/tickers/btcuah";
        $xml = simplexml_load_file($url);
        return $xml;
    }
    
    protected function GetRate_BtcBank()
    {
	$url = "http://btcbank.com.ua/rates.xml";
        $xml = simplexml_load_file($url);
        $json_string = json_encode($xml);
        $result_array = json_decode($json_string, TRUE);

	$inPrice = null;
        $outPrice = null;
        foreach ($rates->item as $item)
		{
			if ($item->from == 'BTC' && $item->to == 'P24UAH')
				{   
					$outPrice = $item->out;  
				}
            if ($item->from == 'P24UAH' && $item->to == 'BTC')
				{   
					$inPrice = $item->in; 
				}
                }
        if (isset($inPrice) && isset($outPrice))
			{
				$avgPrice = round ( ($inPrice + $outPrice) / 2, 2, PHP_ROUND_HALF_UP); 
				return (['out' => $outPrice, 'in' => $inPrice , 'avg' => $avgPrice]);                 
			}
    }
}

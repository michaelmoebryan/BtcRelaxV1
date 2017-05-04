<?php
namespace BtcRelax;

use BtcRelax\Utils;

require 'REInterface.php';
        
class RE implements IRE {

    public static $CURRENCIES = [
        IRE::BTC => 'BTC',
        IRE::UAH => 'UAH',
        IRE::USD => 'USD'
    ];
    


    public function __construct() {
        
    }
       
    public function getBTCPrice($price,$currency = 'UAH') {
        $result = null;
        if (!is_numeric($price))
        {
            throw new \LogicException('Price has incorrect value!');
        }
        switch ($currency) {
            case IRE::UAH:
                $vExchangeResult = $this->getExchangeRate(); 
                $result = 1/$vExchangeResult*$price;
                break;
            default:
                throw new \LogicException('Price set with incompatible currency!');
        }
        if (!is_numeric($result))
        {
            throw new \LogicException('Price undefined!');
        }
        return $result;
    }
  
    private function getExchangeRate()
    {
        $req = Utils::httpGet("https://kuna.io/api/v2/tickers/btcuah");
        $json = json_decode($req); 
        if (json_last_error() === JSON_ERROR_NONE) { 
            $last = $json->ticker->last;
            if (is_numeric($last))
            {
                return $json->ticker->last;
            }
        } 
        throw  new \BtcRelax\NotFoundException("Cannot get exchange rate!");
    }

    public function getBallance($address) {
        $result  = false;
        $requestURI = sprintf('http://blockchain.info/address/%s?format=json',$address);
        $response = file_get_contents($requestURI);
        if (FALSE == $response)
        {
            Log::general(sprintf('Error while getting ballance by URL:%s', $requestURI ), Log::WARN);
            $result = false;            
        }
        else
        {
            $object = json_decode($response);
            $inBtc = $object->total_received / 100000000;
            Log::general(sprintf('Received ballance: %s for:%s', $inBtc, $address ), Log::INFO);
            $result = $inBtc;
        }
        return $result;        
    }

}

<?php
namespace BtcRelax;
require 'reI.php';
use BtcRelax\Utils;

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
            throw new Exception('Price has incorrect value!');
        }
        switch ($currency) {
            case IRE::UAH:
                $vExchangeResult = $this->getExchangeRate(); 
                $result = 1/$vExchangeResult*$price;
                break;
            default:
                throw new \BtcRelax\NotFoundException('Price set with incompatible currency!');
        }
        if (!is_numeric($result))
        {
            throw new \BtcRelax\NotFoundException('Price undefined!');
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

}

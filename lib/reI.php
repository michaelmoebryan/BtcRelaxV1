<?php
namespace BtcRelax;
interface IRE {
    
    const BTC = "BTC";
    const UAH = "UAH";
    const USD = "USD";
    
   
    public function getBTCPrice($price, $currency);
    
}

<?php

namespace BtcRelax\Model;

class Order {
    
    const STATUS_CREATED = "Created";
    const STATUS_CONFIRMED = "Confirmed";
  
    
    private $pIdOrder;
    private $pCreateDate  ;
    private $pEndDate;
    private $pState = self::STATUS_CREATED;
    private $pBTCPrice;
    private $pInvoiceAddress;

    public function __construct() {
        
        
    }
    
    function getIdOrder()
    {
        return $this->pIdOrder;
    }
    
    
                             
    function getCreateDate ()
    {
	return $this->pCreateDate;
    }
			 
    function setCreateDate($pValue)
    {
	$this->CreateDate = $pValue;
    }
    
    function getEndDate ()
    {
	return $this->pEndDate;
    }
			 
    function setEndDate($pValue)
    {
	$this->pEndDate = $pValue;
    }
    
    function getBTCPrice() {
        return $this->pBTCPrice;
    }

    function getInvoiceAddress() {
        return $this->pInvoiceAddress;
    }

    function getCustomer() {
        return $this->pCustomer;
    }

    function setBTCPrice($pBTCPrice) {
        $this->pBTCPrice = $pBTCPrice;
    }

    function setInvoiceAddress($pInvoiceAddress) {
        $this->pInvoiceAddress = $pInvoiceAddress;
    }

    function setCustomer($pCustomer) {
        $this->pCustomer = $pCustomer;
    }
    
}

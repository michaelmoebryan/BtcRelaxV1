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
    private $pSaller;
    private $pCreator;
    
    public function __construct($pBTCPrice, $pCreator) {
        $this->pBTCPrice = $pBTCPrice;
        $this->pCreator = $pCreator;
    }

        function getSaller() {
        return $this->pSaller;
    }

    public function setSaller($pSaller) {
        $this->pSaller = $pSaller;
    }

        
    function getState() {
        return $this->pState;
    }

    public function setState($pState) {
        $this->pState = $pState;
    }

        function getIdOrder()
    {
        return $this->pIdOrder;
    }
    
    public function setIdOrder($pIdOrder) {
        $this->pIdOrder = $pIdOrder;
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

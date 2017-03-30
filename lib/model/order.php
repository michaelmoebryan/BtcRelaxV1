<?php

namespace BtcRelax\Model;

class Order {
    
    const STATUS_CREATED = "Created";
    const STATUS_CONFIRMED = "Confirmed";
  
    
    private $pIdOrder;
    private $pCreateDate;
    private $pEndDate;
    private $pState = self::STATUS_CREATED;
    private $pBTCPrice;
    private $pInvoiceAddress;
    private $pSaller;
    private $pCreator;
    private $pBookmarkId;
    private $pLastError;
    
    public function __construct($pBTCPrice = null, $pCreator = null, $pBookmarkId = null) {
        if ($pBTCPrice !== null)
        {
            $this->pBTCPrice = round($pBTCPrice,8);
        }
        if ($pCreator !== null)
        {
            $this->pCreator = $pCreator;
        }
        if ($pBookmarkId !== null)
        {
            $this->pBookmarkId = $pBookmarkId;
        }
    }

    public static function allStatuses() {
        return [
            self::STATUS_CONFIRMED,
            self::STATUS_CREATED
        ];
    }
    
    public function getLastError() {
        return $this->pLastError;
    }

    public function setLastError($pLastError) {
        $this->pLastError = $pLastError;
    }

    function getSaller() {
        return $this->pSaller;
    }

    public function getBookmarkId() {
        return $this->pBookmarkId;
    }

    public function setBookmarkId($pBookmarkId) {
        $this->pBookmarkId = $pBookmarkId;
    }

    public function setSaller($pSaller) {
        $this->pSaller = $pSaller;
    }

        
    public function getState() {
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
	$this->pCreateDate = $pValue;
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
        $this->pBTCPrice = round($pBTCPrice, 8);
    }

    function setInvoiceAddress($pInvoiceAddress) {
        $this->pInvoiceAddress = $pInvoiceAddress;
    }

    function setCustomer($pCustomer) {
        $this->pCustomer = $pCustomer;
    }
    
}

<?php
namespace BtcRelax\Model;

class Order {
    
    const STATUS_CREATED = "Created";
    const STATUS_CONFIRMED = "Confirmed";
    const STATUS_PAID = "Paid";
    const STATUS_WAIT_FOR_PAY = "WaitForPayment"; 
    const STATUS_CANCELED = "Canceled"; 
    const STATUS_FINISHED = "Finished";
    
    const DELIVERY_HOTPOINT = "HotHiddenPoint";
    const DELIVERY_ORDEREDPOINT =  "OrderedHotPoint";
    const DELIVERY_POSTOFFICE = "PostOffice";
    
    private $pIdOrder;
    private $pCreateDate;
    private $pEndDate;
    private $pState = self::STATUS_CREATED;
    private $pSaller;
    private $pBTCPrice;
    private $pPricingDate;
    private $pInvoiceAddress;
    private $pCreator;
    private $pDeliveryMethod = self::DELIVERY_HOTPOINT;
    private $pInvoiceBalance;
    private $pBalanceDate;
    
    /// Variables with session scope
    private $pBookmark;
    private $pLastError;
    
    public function __construct($pBTCPrice = null, $pCreator = null, $pBookmark = null) {
        if ($pBTCPrice !== null)
        {
            $this->pBTCPrice = round($pBTCPrice,8);
        }
        if ($pCreator !== null)
        {
            $this->pCreator = $pCreator;
        }
        if ($pBookmark !== null)
        {
            $this->pBookmark = $pBookmark;
        }
    }
    
    public function getInvoiceBalance() {
        return $this->pInvoiceBalance === null ? 0: $this->pInvoiceBalance;
    }

    public function getBalanceDate() {
        return $this->pBalanceDate;
    }

    public function setInvoiceBalance($pInvoiceBalance) {
        $this->pInvoiceBalance = $pInvoiceBalance;
    }

    public function setBalanceDate($pBalanceDate) {
        $this->pBalanceDate = $pBalanceDate;
    }

    
        
    public function getCreator() {
            return $this->pCreator;
        }

    public function setCreator($pCreator) {
            $this->pCreator = $pCreator;
        }

    public function getPricingDate() {
        return $this->pPricingDate;
    }
       
    public function getDeliveryMethod() {
        return $this->pDeliveryMethod;
    }

    public function setPricingDate($pPricingDate) {
        $this->pPricingDate = $pPricingDate;
    }

    public function setDeliveryMethod($pDeliveryMethod) {
        $this->pDeliveryMethod = $pDeliveryMethod;
    }

    public static function allStatuses() {
        return [
            self::STATUS_CONFIRMED,
            self::STATUS_CREATED,
            self::STATUS_CANCELED,
            self::STATUS_FINISHED,
            self::STATUS_PAID,
            self::STATUS_WAIT_FOR_PAY
        ];
    }
    
    public function getLastError() {
        return $this->pLastError;
    }

    public function setLastError($pLastError) {
        $this->pLastError = $pLastError;
    }

    public function getSaller() {
        return $this->pSaller;
    }

    public function getBookmark() {
        return $this->pBookmark;
    }

    public function setBookmark($pBookmark) {
        $this->pBookmark = $pBookmark;
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

    function setBTCPrice($pBTCPrice) {
        $this->pBTCPrice = round($pBTCPrice, 8);
    }

    function setInvoiceAddress($pInvoiceAddress) {
        if (empty($this->pInvoiceAddress)) {
            $this->pInvoiceAddress = $pInvoiceAddress;
        } else {
            throw new \LogicException("Try to set invoice address, while already set");
        }
    }

    public function CheckPaymentAddress()
    {
	$root_url = 'http://blockchain.info/address/';
        $parameters = 'format=json';
        $response = file_get_contents($root_url . $this->pInvoiceAddress . '?' . $parameters);
        $object = json_decode($response);
        $inBtc = $object->total_received / 100000000;
        return $inBtc;
    }    


    
    public function __toString() {
        $result = null;
        if (!empty($this->pIdOrder)) {$result .= \sprintf("OrderId:%s|", $this->pIdOrder ); }
        if (!empty($this->pCreateDate)) {$result .= sprintf("Created:%s|", $this->pCreateDate->format('Y-m-d H:i:s') );}
        if (!empty($this->pState)) {$result .= sprintf("State:%s|", $this->pState); }
        if (!empty($this->pBTCPrice)) {$result .= sprintf("Price:%s|", $this->pBTCPrice); }
        if (!empty($this->pPricingDate)) {$result .= \sprintf("Priced:%s|", $this->pPricingDate->format('Y-m-d H:i:s') ); }
        if (!empty($this->pInvoiceAddress)) {$result .= sprintf("InvoiceAddress:%s|", $this->pInvoiceAddress ); }
        if (!empty($this->pCreator)) {$result .= sprintf("Creator:%s|", $this->pCreator); }
        if (!empty($this->pDeliveryMethod)) {$result .= sprintf("Delivery:%s|", $this->pDeliveryMethod); }
        if (!empty($this->pInvoiceBalance)) {$result .= \sprintf("Balance:%s|", $this->pInvoiceBalance); }
    return $result;
    }
}

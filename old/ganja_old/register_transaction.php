<?php
    require_once 'ganja_core.php';
    require_once 'ganja_order.php';
    //header("Access-Control-Allow-Origin: *");
    //header("Content-Type: application/json; charset=UTF-8");
	error_log(date('m-d-Y H:i:s') . ' ' . $_SERVER['REMOTE_ADDR'].PHP_EOL ,3, $_SERVER["DOCUMENT_ROOT"] . "/zyro/ganja/callbacks.log");
    error_log('$_POST: ' . print_r($_POST, true) .PHP_EOL ,3, $_SERVER["DOCUMENT_ROOT"] . "/zyro/ganja/callbacks.log");
    error_log('$_GET:  ' . print_r($_GET, true) .PHP_EOL ,3, $_SERVER["DOCUMENT_ROOT"] . "/zyro/ganja/callbacks.log");
      
       
    $real_secret = $_GET['secret'];
    $order_id = $_GET['order_id']; //invoice_id is passed back to the callback URL
    $transaction_hash = $_GET['transaction_hash'];
    $input_transaction_hash = $_GET['input_transaction_hash'];
    $input_address = $_GET['input_address'];
    $value_in_satoshi = $_GET['value'];
    $value_in_btc = $value_in_satoshi / 100000000;  
    $error = 'Unknown error'; 
    
    $curOrder = OrderFactory::create($order_id);
    if (isset($curOrder))
    {
        if ($curOrder->checkSecurityCode($real_secret))
        {
            try
            {
                 if ( $_GET['confirmations'] >= 1 )
						{
							if (GetDbConnector($dbCon)  )
   	               {
                        // Insert here 
                        $query = "CALL `AddPaymentInfo`(?,?,?);";                
                        $stmt = $dbCon->prepare($query);
                        $stmt->bind_param('isd', $order_id, $transaction_hash , $value_in_btc);
                        $stmt->execute();
                        $stmt->close();             
      	            };
         	         if ($_GET['confirmations'] <= 99) {
           	             settype($transaction_hash , "string");
                        $curOrder->notifyOrderPayment($transaction_hash );
  		               };
                };
                         
            } 
            catch(Exception $e)
            {
               die($error);  
            };             
        } 
    }

 
?>

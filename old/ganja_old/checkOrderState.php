<?php
    include 'ganja_core.php';
    include 'ganja_order.php';
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    $vStateId = null;
    $vOrderId = null;
    $isDone = false;
    $vResult = true;
    $curOrder = null;
    if (isset($_GET["idOrder"]))
    {
        if ( is_numeric($_GET["idOrder"]))
        {

            $vOrderId =   $_GET["idOrder"];
            $curOrder = OrderFactory::create($vOrderId);
            if (isset($curOrder))
            {
                $isDone = true;    
            }
        }
    };
    if ($isDone)
    {
        //$curOrder->getReceivedFunds(true);	
        if (isset($_GET["stateHash"]))
		{
			 $vOldHash = $_GET["stateHash"]; 	
			 $ordState = $curOrder->getOrderState();		 
			 if ($ordState == 2 )
			 {
			 	$curOrder->getReceivedFunds(true);
			 }
			 $vResult = $curOrder->getStateHash() == $vOldHash ? 1: 0; 
			 //$vResult = $vOldHash;
		};
		if (isset($_GET["orderState"]) && $vResult)
		{
		        $vStateId  =  $_GET["orderState"];	
		        if ($vStateId == 2)
		        {
					$vResult = 1;
					//$vResult = $vFundsNow == $vNewFunds ? 1: 0 ;	
		        }
				else {
				     $vResult = $curOrder->getOrderState() == $vStateId ? 1: 0;    	
				};			
		}; 
		$outp = '[{"isStateActual":"'  . $vResult . '"}]';		    		   
        echo($outp); 
    }
    else
    {
        new Response('Error', 404 , array('X-Status-Code' => 404));
		error_log('Check order state error. For order Id: ' . vardump($_GET["idOrder"]));
    }    
?>

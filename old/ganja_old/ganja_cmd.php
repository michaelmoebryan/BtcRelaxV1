<?php
  include 'ganja_order.php';

  if (isset($_GET['OrderId']) && isset($_GET['SecurityCode']) && isset($_GET['Action']))
  {
      $orderId = $_GET['OrderId'];
      $securityCode = $_GET['SecurityCode'];
      $action = $_GET['Action']; 
      $curOrder = OrderFactory::create($orderId);
      if (isset($curOrder))
      {
          if ($curOrder->checkSecurityCode($securityCode))
          {
            
            switch ($action) {
               case 'FinishOrder':
                 echo('call FinishOrder .... ');            
                 if ($curOrder->FinishOrder())
                 {
                      echo('Done!');                                         
                 }
                 else
                 {
                     echo('Failed!');
                 }                 
                 break;
               case 'RejectOrderPayment':
                 echo('call RejectOrderPayment .... ');            
                 if ($curOrder->rejectOrderPayment())
                 {
                     echo('Done!');                                         
                 }
                 else
                 {
                     echo('Failed!');
                 }
                 break;
               case 'GetControlPage':
                 echo('call GetControlPage .... ');            
			        session_start();
   					$_SESSION["hasControl"]  = true;
						$redirLoc = 'Location: /cntrl/';
                 header($redirLoc, true);
                 break;
               case 'Test':
                echo('def_time_zone:' . date_default_timezone_get() );
                break;
               case 'AcceptOrderPayment':
                 echo('call AcceptOrderPayment .... ');            
                 if ($curOrder->acceptOrderPayment())
                 {

                    
                    echo('Done!');                                         
                 }
                 else
                 {
                     echo('Failed!');
                 }
                 break;
               default:
                 echo('Unkown command');
                 break;
            }  
          }
          else
          {
            echo ('Access denied!');
          };
      }
      else
      {
          echo ('Order not found!');
      };
      
  }
  else
  {
      echo ('Invalid parameters!');
  }
?>

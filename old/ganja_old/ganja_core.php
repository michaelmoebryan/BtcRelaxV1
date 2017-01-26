<?php
	  require_once 'global_vars.php';
	  require_once 'ganja_acm.php';
	  if (!class_exists('PHPMailer')) {
					$path = $_SERVER["DOCUMENT_ROOT"] . '/phpmailer/class.phpmailer.php'  ;
					include $path;
					};

	  function __autoload($name)
	  {
		  switch ($name) {
			 case 'acessControlManager':
				$path = $_SERVER["DOCUMENT_ROOT"] . '/ganja/ganja_acm.php';
				include $path;          
			   break;
			 case 'GanjaOrder':
				$path = $_SERVER["DOCUMENT_ROOT"] . '/ganja/ganja_order.php';
				include $path;             
			   break;
			 case 'PaymentFactory':
				$path = $_SERVER["DOCUMENT_ROOT"] . '/ganja/ganja_payment.php';
				include $path;         
			   break;
			 case 'BookmarkFactory':
				$path = $_SERVER["DOCUMENT_ROOT"] . '/ganja/ganja_bookmark.php';
				include $path;         
			   break;
			 case 'PHPMailer':
				$path = $_SERVER["DOCUMENT_ROOT"] . '/phpmailer/class.phpmailer.php';
				include $path;         
			   break;

			 
		  }
	  };
	  
	  /* Public functions */
	  function GetDbConnector(&$dbCon)
	  {
			try
			  {
			   $con = new mysqli(mysql_host, mysql_user , mysql_password
			   , mysql_database, mysql_port , mysql_socket )
				or die ('Could not connect to the database server' . mysqli_connect_error());

				if ($con != null)
				{
				   if ($con->ping())
				   {
								  $dbCon = $con;
								  /* изменение набора символов на utf8 */
									if ($con->set_charset("utf8")) {
										return true;
									};
				   }
				};
				$dbCon = NULL;
			}
			catch (Exception $e)
			{
				error_log($e->message, 3, 'core_errors.txt');
			}
			return false;
	  };

	  function GetAvailableCount()
	  {
		  if (GetDbConnector($dbCon))
		  {
				$query = "select `GetAvailableCount`() from dual";
				$availCount = 0;

				if ($stmt = $dbCon->prepare($query)) {
					$stmt->execute();
					$stmt->bind_result($result_count);
					while ($stmt->fetch()) {
						$availCount = $result_count;
					}
					$stmt->close();
					}              return $availCount;
		  };
	  };
	  
	  function GetHotpointInfo()
	  {
		  if (GetDbConnector($dbCon))
		 {
			 $result_info = array();
			 $query = "SELECT `HotPoints`.`RegionTitle`,`HotPoints`.`RegionTitle_ru`,
						`HotPoints`.`Quantity`,
						`HotPoints`.`PointsCount`
						FROM `HotPoints`;"; 
				if ($stmt = $dbCon->prepare($query)) {
					$stmt->execute();
					$stmt->bind_result($region_title,$region_title_ru, $quantity, $pointsCount);
					while ($stmt->fetch()) {
						 array_push($result_info, array('RegionTitle' => $region_title, 'RegionTitle_ru' => $region_title_ru, 'Quantity' => $quantity, 'PointsCount' => $pointsCount ));
						
					}
					$stmt->close();
					}
					return $result_info;
		 };
	  }
	  
	  function GetFreeHotpoints($orderQuantity)
	  {
		 if (GetDbConnector($dbCon))
		 {
			 $result_info = array();
			 $query = "SELECT `ActiveBookmarks`.`idBookmark`,
						`ActiveBookmarks`.`Quantity`,
						`ActiveBookmarks`.`RegionTitle`,
						`ActiveBookmarks`.`RegionTitle_ru`,
						`ActiveBookmarks`.`AdvertiseTitle`,
						`ActiveBookmarks`.`CustomPrice`                        
					FROM `ActiveBookmarks`";
			 if (!is_null($orderQuantity))
			 {
					$query = $query .  'where `ActiveBookmarks`.`Quantity` <= ?"; ';
			 };                    
					
				if ($stmt = $dbCon->prepare($query)) {
					if (!is_null($orderQuantity))
					{
						$stmt->bind_param('i', $orderQuantity );                        
					} 
					$stmt->execute();
					$stmt->bind_result($idBookmark, $quantity, $region_title,$region_title_ru , $adv_title, $cust_price);
					while ($stmt->fetch()) {
						 array_push($result_info, array('idBookmark' => $idBookmark, 'Quantity' => $quantity, 'RegionTitle' => $region_title, 
						 'RegionTitle_ru' => $region_title_ru , 'AdvertiseTitle' => $adv_title, 'CustomPrice' => $cust_price ));                        
					}
					$stmt->close();
					}
					return $result_info;
		 };
	  }
	  
	  function RegisterNewOrder($emailTo, $vLang)
	  {
		  $resultMessage = "Error";
		  if (GetDbConnector($dbCon))
		  {
			  $pDefDeliveryMethod = defaultDeliveryMethod;
			  $pIpAddress = $_SERVER['REMOTE_ADDR'];
			  $pCustomerMail = $emailTo;
			  $callQuery = "CALL `RegisterOrder`(?, ?, ?, @pResultId, @pOrderId)";
			  $call = $dbCon->prepare($callQuery);
			  $call->bind_param('ssi', $pIpAddress, $pCustomerMail, $pDefDeliveryMethod);
			  $call->execute();

			  $select = $dbCon->query("SELECT  @pResultId, @pOrderId");
			  $result = $select->fetch_assoc();
			  $pResultId    = $result['@pResultId'];
			  $pOrderId = $result['@pOrderId'];            		  
					  $v_accessCode =  GetAccessCodeByOrderId($pOrderId);
			  switch ($pResultId) {
					   case 100:
						  // Case when result is 100, mean Ok
						  if ($v_accessCode != null)
						  {
							   $resultMessage = SendConfirmation($pOrderId, $pCustomerMail, $v_accessCode, $vLang );
						  };                
						break;
					   case 101:
						  if ($v_accessCode != null)
						  {
							   $resultMessage = SendConfirmation($pOrderId, $pCustomerMail, $v_accessCode, $vLang );                          
						  };
						  InitSession();
						  $_SESSION["currentOrder"] = null;                        
						break;
					   case 102:
						  if ($v_accessCode != null)
						  {
							   $resultMessage = SendConfirmation($pOrderId, $pCustomerMail, $v_accessCode, $vLang );                          
						  };
						  InitSession();
						  $_SESSION["currentOrder"] = null;   
						break;                                      
					   default:
						  $resultMessage =  $pResultId;
						}                 	         
		  }
		  return $resultMessage;
	  };

	  function  GetAccessCodeByOrderId($orderId)
	  {
		  if (GetDbConnector($dbCon))
		  {
				$query = "select `GetOrderAcceessCodeByOrderId`(?) from dual";

				if ($stmt = $dbCon->prepare($query))
					{
						$stmt->bind_param('i', $orderId );
						$stmt->execute();
						$stmt->bind_result($spetsnaz_vladtest);
						while ($stmt->fetch()) {
							$accessCode = $spetsnaz_vladtest;
						}
						$stmt->close();
					};
					return $accessCode;
		  };
	  };
			  
	  function  SendConfirmation($orderId, $email, $accessCode, $langCode)
	   {
		   $result = "Ok";
		   $mail = new PHPMailer;
		   // Set PHPMailer to use the sendmail transport
			//$mail->isSendmail();
			//Set who the message is to be sent from
			$mail->setFrom(response_mail, 'Bit Ganj');
			//Set an alternative reply-to address
			//$mail->addReplyTo('admin@ganga.com', 'Ganga admin');
			//Set who the message is to be sent to
			$mail->addAddress($email);
			//$mail->AddCC(admin_mail, 'God Jah');
			$curOrder = OrderFactory::create($orderId);
			if (isset($curOrder ))
			{   session_start();
				$_SESSION["currentOrder"] = $curOrder;
				$curOrder->setLang($langCode);
				//Set the subject line
				switch ($langCode) {
				   case 'ru':
				   $mail->Subject = 'Подтверждение заказа №' . $orderId;                    
					break;
				   default:
				   $mail->Subject = 'Підтверження замовленя №' . $orderId ;
				}

				//Read an HTML message body from an external file, convert referenced images to embedded,
				//convert HTML into a basic plain-text alternative body
				switch ($langCode) {
				   case 'ru':
					 $bodyTempl = file_get_contents('ganja/confirm_ru.html');
					 break;
				   default:
				   $bodyTempl = file_get_contents('ganja/confirm.html');
				}
				

				$bodyTempl = str_replace( '@@orderId@@' ,$orderId ,$bodyTempl);
				$bodyTempl = str_replace( '@@accessСode@@' ,$accessCode ,$bodyTempl);
				$bodyTempl = str_replace( '@@orderUrl@@',$curOrder->getOrderUrl(),$bodyTempl);
				//$mail->msgHTML(file_get_contents('confirm.html'), dirname(__FILE__));
				$mail->CharSet = 'utf8';
				$mail->Body = $bodyTempl;
				//Replace the plain text body with one created manually
				$mail->AltBody = 'Confirmation message! You was created Order with Id: ' . $orderId .
				' Access code for that order is:' . $accessCode ;
				//Attach an image file
				//$mail->addAttachment('images/phpmailer_mini.png');

				//send the message, check for errors
				if (!$mail->send()) {
					$result = "Mailer Error: " . $mail->ErrorInfo;
				} else {
					$result = "Ok";
				}  
			}
			else
			{
				$result = 'Order id:' . $orderId . ' not found!';
			}
		   return $result;
	   };

	   function  SendNewOrderNotification($orderId)
	   {
		   $result = "Ok";
		   $mail = new PHPMailer;
			$mail->setFrom(response_mail, 'Bit Ganj');
			$mail->addAddress(admin_mail);
			$curOrder = OrderFactory::create($orderId);
			if (isset($curOrder))
			{   
				$langCode = $curOrder->getLang();                 
				switch ($langCode) 
				{
				   case 'ru':
				   $mail->Subject = 'Новый заказ №' . $orderId;                    
					break;
				   default:
				   $mail->Subject = 'Нове замовлення №' . $orderId;
				}

				//Read an HTML message body from an external file, convert referenced images to embedded,
				//convert HTML into a basic plain-text alternative body
				switch ($langCode) {
				   case 'ru':
					 $bodyTempl = file_get_contents(dirname(__FILE__) . '/order_start_ru.html');
					 break;
				   default:
				   $bodyTempl = file_get_contents(dirname(__FILE__) . '/order_start.html');
				}
								
				$bodyTempl = str_replace( '@@CustomerMail@@', $curOrder->getCustomerMail() , $bodyTempl);
				$bodyTempl = str_replace( '@@Quantity@@', $curOrder->getQuantity() ,$bodyTempl);                
				$bodyTempl = str_replace( '@@TargetTo@@', $curOrder->getTargetTo() ,$bodyTempl);
				$bodyTempl = str_replace( '@@orderId@@' , $curOrder->pOrderId ,$bodyTempl);
				$bodyTempl = str_replace( '@@accessСode@@' , $curOrder->getAccessCode() ,$bodyTempl);
				$bodyTempl = str_replace( '@@orderUrl@@',$curOrder->getOrderUrl(),$bodyTempl);
				$bodyTempl = str_replace( '@@TotalPrice@@',$curOrder->getTotalPrice(),$bodyTempl);
				$bodyTempl = str_replace( '@@InvoiceAddress@@',$curOrder->getInvoiceAddress(),$bodyTempl);
				$bodyTempl = str_replace( '@@acceptOrderPaymentUrl@@', $curOrder->getAcceptOrderPaymentUrl(),$bodyTempl );
				$bodyTempl = str_replace( '@@finishOrderUrl@@', $curOrder->getFinishOrderUrl(), $bodyTempl); 
				$bodyTempl = str_replace( '@@controlPageUrl@@', $curOrder->getControlPageUrl(), $bodyTempl); 
				$url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . "/cntrl/AddBookmark.php"; 
				$bodyTempl = str_replace( '@@addPointURL@@', $url  , $bodyTempl); 
				$mail->CharSet = 'utf8';
				$mail->Body = $bodyTempl;
				//Replace the plain text body with one created manually
				$mail->AltBody = 'Created new order with id:' . $orderId .
				' Access code for that order is:' . $curOrder->getAccessCode() ;
				//Attach an image file
				//$mail->addAttachment('images/phpmailer_mini.png');

				//send the message, check for errors
				if (!$mail->send()) {
					$result = "Mailer Error: " . $mail->ErrorInfo;
				} else {
					$result = "Ok";
				}  
			}
			else
			{
				$result = 'Order id:' . $orderId . ' not found!';
			}
		   return $result;
	   };       
 
	   function  SendPaymentNotification($orderId, $value_in_btc) 
	   {
		   $result = "Ok";
		   $mail = new PHPMailer;
			$mail->setFrom(response_mail, 'FastFen');
			$mail->addAddress(admin_mail);
			$curOrder = OrderFactory::create($orderId);
			if (isset($curOrder))
			{   
				$langCode = $curOrder->getLang();                 
				switch ($langCode) 
				{
				   case 'ru':
				   $mail->Subject = 'Принят платёж по заказу №' . $orderId;                    
					break;
				   default:
				   $mail->Subject = 'Прийнято платіж по замовленню №' . $orderId;
				}

				//Read an HTML message body from an external file, convert referenced images to embedded,
				//convert HTML into a basic plain-text alternative body
				switch ($langCode) {
				   case 'ru':
					 $bodyTempl = file_get_contents(dirname(__FILE__) . '/payment_accepted_ru.html');
					 break;
				   default:
				   $bodyTempl = file_get_contents(dirname(__FILE__) . '/payment_accepted.html');
				}
								
				$bodyTempl = str_replace( '@@CustomerMail@@', $curOrder->getCustomerMail() , $bodyTempl);
				$bodyTempl = str_replace( '@@Quantity@@', $curOrder->getQuantity() ,$bodyTempl);                
				$bodyTempl = str_replace( '@@TargetTo@@', $curOrder->getTargetTo() ,$bodyTempl);
				$bodyTempl = str_replace( '@@orderId@@' , $curOrder->pOrderId ,$bodyTempl);
				$bodyTempl = str_replace( '@@accessСode@@' , $curOrder->getAccessCode() ,$bodyTempl);
				$bodyTempl = str_replace( '@@orderUrl@@',$curOrder->getOrderUrl(),$bodyTempl);
				$bodyTempl = str_replace( '@@TotalPrice@@',$curOrder->getTotalPrice(),$bodyTempl);
				$bodyTempl = str_replace( '@@InvoiceAddress@@',$curOrder->getInvoiceAddress(),$bodyTempl);
				$bodyTempl = str_replace( '@@acceptOrderPaymentUrl@@', $curOrder->getAcceptOrderPaymentUrl(),$bodyTempl );
				$bodyTempl = str_replace( '@@finishOrderUrl@@', $curOrder->getFinishOrderUrl(), $bodyTempl); 
				$bodyTempl = str_replace( '@@ValueInBtc@@', $value_in_btc , $bodyTempl); 
				$mail->CharSet = 'utf8';
				$mail->Body = $bodyTempl;
				//Replace the plain text body with one created manually
				$mail->AltBody = 'Payment accepted by order with id:' . $orderId .
				' Access code for that order is:' . $curOrder->getAccessCode() ;
				//Attach an image file
				//$mail->addAttachment('images/phpmailer_mini.png');

				//send the message, check for errors
				if (!$mail->send()) {
					$result = "Mailer Error: " . $mail->ErrorInfo;
				} else {
					$result = "Ok";
				}  
			}
			else
			{
				$result = 'Order id:' . $orderId . ' not found!';
			}
		   return $result;           
	   };
			   
	   function isHasAccessToOrder($orderId, $accessCode)
	   {
		   //wf_log("Try to access order: ".$orderId,'INFO' );
		   $result = false;
		   $realAccessCode = GetAccessCodeByOrderId($orderId);
		   if (isset($realAccessCode) && isset($accessCode) )
		   {
				if ( $realAccessCode == $accessCode)
				{
					   $result = true;
				};
		   };
		   return $result;
	   };

		function GetTotalPriceUAH($vQuantity = 1)
		{
			if (GetDbConnector($dbCon))
			{
				$query = "select `GetItemLocalPrice`() from dual";
				if ($stmt = $dbCon->prepare($query))
						{
							$stmt->execute();
							$stmt->bind_result($spetsnaz_vladtest);
							while ($stmt->fetch()) {
								$vItemPrice = $spetsnaz_vladtest;
							}
							$stmt->close();
							if (isset($vItemPrice))
							{
								if(is_numeric($vItemPrice))
								{
									if($vItemPrice > 0)
									{
										$vPrice = round($vQuantity * $vItemPrice, 4, PHP_ROUND_HALF_UP);
										return $vPrice;
									}
								}
							}
						};
			};
		}

		function GetBTCPriceByUAH($pUAHPrice)
		{
			$vExchangeRate = GetExchangeRate();
			if (isset($vExchangeRate))
			{
				if (is_numeric($vExchangeRate))
				{
					$vOneUAHPrice = round( 1 / $vExchangeRate, 8, PHP_ROUND_HALF_UP);
					$resultPrice = round($pUAHPrice * $vOneUAHPrice , 4, PHP_ROUND_HALF_UP);
					if (is_numeric($resultPrice))
					{
						if ($resultPrice > 0)
						return $resultPrice;
					}
				}
			}
		}

		function GetRateBy_btcbank()
		{
			$url = "http://btcbank.com.ua/rates.xml";
			$xmlfile = file_get_contents($url);         
			$rates =  simplexml_load_string($xmlfile);
			$inPrice ; 
			$outPrice;
			foreach ($rates->item as $item)
			{
				if ($item->from == 'BTC' && $item->to == 'P24UAH')
				{   
					$outPrice = $item->out;  
				};
				
				if ($item->from == 'P24UAH' && $item->to == 'BTC')
				{   
					$inPrice = $item->in; 
				};
							 
			}; 
			if (isset($inPrice) && isset($outPrice))
			{
				$avgPrice = round ( ($inPrice + $outPrice) / 2, 2, PHP_ROUND_HALF_UP); 
				return (array('out' => $outPrice, 'in' => $inPrice , 'avg' => $avgPrice ));                 
			};   

		};
		
		function GetExchangeRate()
		{
			if (GetDbConnector($dbCon))
			{
				$query = "select `GetExchangeRate`() from dual";
				if ($stmt = $dbCon->prepare($query))
						{
							$stmt->execute();
							$stmt->bind_result($spetsnaz_vladtest);
							while ($stmt->fetch()) {
								$vExchangeRate = $spetsnaz_vladtest;
							}
							$stmt->close();
							if (isset($vExchangeRate))
							{
								if (is_numeric($vExchangeRate))
								{
									return $vExchangeRate;
								}
							}

						}
			};

		}
		
		function SetExchangeRate($pRate, $pComments = "btcbank.com.ua" )
		{
		  if (GetDbConnector($dbCon))
		  {
			  $callQuery = "CALL `SetExchangeRate`(?, ? )";
			  $call = $dbCon->prepare($callQuery);
			  $call->bind_param('ds', $pRate, $pComments);
			  $call->execute();
		  }                
		}

		function InitSession()
		{
			$res = false;
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
				$res = true;
			}
			return $res;
		}
		
		function isWasPostback()
		{
		   $isPostBack = false;

			$referer = "";
			$thisPage = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

			if (isset($_SERVER['HTTP_REFERER'])){
				$referer = $_SERVER['HTTP_REFERER'];
			};

			if ($referer == $thisPage){
				$isPostBack = true;
			};
			
			return ($isPostBack);  
		}
?>
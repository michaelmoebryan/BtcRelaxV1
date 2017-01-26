<?php
require_once 'ganja_core.php';
require_once dirname(__FILE__) . "/config.php";

if (!class_exists('BookmarkFactory')) {
	$path = $_SERVER["DOCUMENT_ROOT"] . '/ganja/ganja_bookmark.php';
	include $path;
};

if (!class_exists('PaymentFactory')) {
	$path = $_SERVER["DOCUMENT_ROOT"] . '/ganja/ganja_payment.php';
	include $path;
};

class OrderFactory {
	public static function create($orderId, $lang = 'uk') {
		$result = new GanjaOrder($orderId, $lang);
		if ($result -> pOrderId > 0) {
			return $result;
		} else
			{
				
			error_log('Cannot create order Id: ' . $orderId , 3 , 'order_error.log');
			return null;
			}

	}

}

class GanjaOrder {

		  // Local variables  
		  private $_mysqli;
		  private $pInterLang ;
		  public  $pOrderId ;
		  private $pIPAddress ;
		  private $pOrderState ;
		  private $pCreateDate  ;
		  private $pCustomerMail ;
		  private $pOrderAccessCode;
		  private $pOrderQuantity;
			  private $pReceivedByOrder;
		  private $pTargetLocation;
		  private $pInvoiceAddress;
		  private $pTotalPrice;
			  private $pInvoiceSecurityCode;
		  private $pFundsReceived;
		  private $pTotalFunds;
			  private $pIsCheckPaymentClick = false;
			  private $pDeliveryMethod; 
		  
		  public function __construct($orderId, $lang = 'uk') {
			 try
			  {
				  $this->pInterLang = $lang;
				  $this->_mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
				  $result = $this->_mysqli->query(sprintf("SELECT `OrdersInfo`.`idOrder`,
									`OrdersInfo`.`IPAddress`,
									`OrdersInfo`.`OrderState`,
									`OrdersInfo`.`CreateDate`,
									`OrdersInfo`.`CustomerMail`,
									`OrdersInfo`.`OrderAccessCode`,
									`OrdersInfo`.`OrderQuantity`,
									`OrdersInfo`.`TargetLocation`,
									`OrdersInfo`.`InvoiceAddress`,
									`OrdersInfo`.`TotalPrice`,
									`OrdersInfo`.`InvoiceSecurityCode`,
									`OrdersInfo`.`FundsReceived`,
									`OrdersInfo`.`ReceivedByOrder`,
									`OrdersInfo`.`DeliveryMethod`,
									`OrdersInfo`.`TotalFunds`
									FROM `OrdersInfo` where `OrdersInfo`.`idOrder` = '%s'
				  LIMIT 1 ", $orderId ));
				  if($result) {
					$row = $result->fetch_assoc();
					$this->pOrderId = $orderId ;
					$this->pIPAddress = $row['IPAddress'];
					$this->pOrderState = $row['OrderState'];
					$this->pCreateDate = $row['CreateDate'] ;
					$this->pCustomerMail = $row['CustomerMail'];
					$this->pOrderAccessCode = $row['OrderAccessCode'];
					$this->pOrderQuantity = $row['OrderQuantity'];
					$this->pTargetLocation = $row['TargetLocation'];
					$this->pInvoiceAddress = $row['InvoiceAddress'];
					$this->pTotalPrice = $row['TotalPrice'];
					$this->pInvoiceSecurityCode = $row['InvoiceSecurityCode'];
					$this->pFundsReceived = $row['FundsReceived'];
					$this->pReceivedByOrder = $row['ReceivedByOrder'];
					$this->pDeliveryMethod = $row['DeliveryMethod'];
					$this->pTotalFunds = $row['TotalFunds'];
				  }
/*				  if (GetDbConnector($dbCon))
					{
						$query = "SELECT `OrdersInfo`.`idOrder`,
									`OrdersInfo`.`IPAddress`,
									`OrdersInfo`.`OrderState`,
									`OrdersInfo`.`CreateDate`,
									`OrdersInfo`.`CustomerMail`,
									`OrdersInfo`.`OrderAccessCode`,
									`OrdersInfo`.`OrderQuantity`,
									`OrdersInfo`.`TargetLocation`,
									`OrdersInfo`.`InvoiceAddress`,
									`OrdersInfo`.`TotalPrice`,
									`OrdersInfo`.`InvoiceSecurityCode`,
									`OrdersInfo`.`FundsReceived`,
									`OrdersInfo`.`ReceivedByOrder`,
									`OrdersInfo`.`DeliveryMethod`,
									`OrdersInfo`.`TotalFunds`
									FROM `OrdersInfo` where `OrdersInfo`.`idOrder` = ?" ;
						if ($stmt = $dbCon->prepare($query)) {
							$stmt->bind_param('i', $orderId );
							$stmt->execute();
							$rows_cnt = $stmt->num_rows; 
							if ($rows_cnt == 1)
							{
								$bnd_res = $stmt->bind_result($orderId, $IPAddress,
								$OrderState, $CreateDate,
								$CustomerMail, $OrderAccessCode,
								$OrderQuantity,$TargetLocation,
								$InvoiceAddress, $TotalPrice, $InvoiceSecurityCode, $FundsReceived, $ReceivedByOrder, $DeliveryMethod, $TotalFunds);
								if ($bnd_res)
								{
									while ($stmt->fetch()) {
										  $this->pOrderId = $orderId ;
										  $this->pIPAddress = $IPAddress;
										  $this->pOrderState = $OrderState;
										  $this->pCreateDate = $CreateDate ;
										  $this->pCustomerMail = $CustomerMail;
										  $this->pOrderAccessCode = $OrderAccessCode;
										  $this->pOrderQuantity = $OrderQuantity;
										  $this->pTargetLocation = $TargetLocation;
										  $this->pInvoiceAddress = $InvoiceAddress;
										  $this->pTotalPrice = $TotalPrice;
										  $this->pInvoiceSecurityCode = $InvoiceSecurityCode;
										  $this->pFundsReceived = $FundsReceived;
										  $this->pReceivedByOrder = $ReceivedByOrder;
										  $this->pDeliveryMethod = $DeliveryMethod;
										  $this->pTotalFunds = $TotalFunds;
																	};                                    
								}
								else
								{
									throw new ErrorException("Cannot bind DB result to local vars.");                                    
								}; 
								  
								  //							    error_log('Object for order Id: ' . $this->pOrderId . ' created.' , 3 , 'order_info.txt');
							};
							$stmt->close();
							return ($this->pOrderId);
							//wf_log("Order ".$orderId." initialized.", 'INFO') ;
						}
					}*/
			  }
			  catch (Exception $e )
			  {
				   error_log('Error while try to create order object! Message:' . $e->message  , 3, 'order_error.txt');
				  
			  };
		  }

		  // Public functions

		  public function getCustomerMail()
		  {
			  return $this->pCustomerMail ;
		  }
		  
		  public function getOrderId()
		  {
			  return $this->pOrderId;
		  }

		  public function getAccessCode()
		  {
			  return $this->pOrderAccessCode;
		  }
		  
		  public function getOrderState()
		  {
			  $this->Refresh();
			  return $this->pOrderState;
		  }
		  
		  public function checkSecurityCode($pSecurityCode)
		  {
			  $result = strcmp($pSecurityCode, $this->pInvoiceSecurityCode) == 0;
			  return $result;
		  }

		  public function getQuantity()
		  {
			  return $this->pOrderQuantity ;
		  }
		  
		  public function getTargetTo()
		  {
			  return $this->pTargetLocation ;
		  }

		  public function setLang($pLang)
		  {
			  $this->pInterLang = $pLang;
		  }
		  
		  private function setTotalFunds($pTotalFunds)
		  {
			 //$result = false;         	
			 if ($this->pTotalFunds < $pTotalFunds )
			 {
				// Prepare all for the transaction
				if (GetDbConnector($dbCon))
					   {
							try
							{
								$dbCon->autocommit(FALSE);
								//$this->Refresh();
								
								/*if ($this->pDeliveryMethod = 1)
								{
									$newState = 3;
									
								}
								else
								{
									$newState = 50;
								}*/

								$query = "UPDATE `Orders`
											SET
											`TotalFunds` = ?
											WHERE idOrder = ?;";
								if ($stmt = $dbCon->prepare($query))
									{
												/* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
												$stmt->bind_param('di', $pTotalFunds ,$this->pOrderId  );
												$stmt->execute();
												$dbCon->commit();
												$dbCon->autocommit(TRUE);
												// All Ok. Save state to object
												$this->pTotalFunds = $pTotalFunds;
												// Need to send notification
												
												//$result = true;
									};
							 
							}
							catch ( Exception $e ) {
								// before rolling back the transaction, you'd want
								// to make sure that the exception was db-related
								$dbCon->rollback();
								$dbCon->autocommit(TRUE);
							 }
						};
				}
				//else {
					//$result = true;					
				//};                                            
			 //return $result;  
		  }

		  public function getLang()
		  {
			  return $this->pInterLang ;
		  }

		  public function getUser()
		  {
			  return  $this->pCustomerMail;
		  }

		  public function getTotalPrice()
		  {
			  return  $this->pTotalPrice;
		  }
		  
		  public function getReceivedFunds($isNeedCheck = false)
		  {
			   if ($isNeedCheck && $this->pOrderState > 1 && empty($this->pInvoiceAddress) == false  )
			   {
					$this->CheckPaymentAddress();
			   } 										  
			   return $this->pFundsReceived <= $this->pTotalFunds ? $this->pTotalFunds: $this->pFundsReceived; 
		  }
		  
		  public function getStateHash()
		  {
			//$hash = new QuickHashStringIntHash((string)$this->pOrderState);
			//$vKey =  (string) $this->getReceivedFunds();
			$result = md5($this->pOrderState + $this->getReceivedFunds());
			return $result;
		  }
		  
		  public function getInvoiceAddress()
		  {
			  return  $this->pInvoiceAddress;
		  }
				 
		  public function getOrderUrl()
		  {              
			  $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . "/" ;
			  return($url);
		  }

		  public function getNotEnoughFunds()
		  {
			$catched = $this->getReceivedFunds(); 
			if ( $catched < $this->pTotalPrice 
			and $catched > 0)
			{
				return $this->pTotalPrice - $catched;
			}    
		  }
 
		  public function getAcceptOrderPaymentUrl()
		  {
			 $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . "/zyro/ganja/ganja_cmd.php?OrderId=" . $this->pOrderId . "&SecurityCode=" .$this->pInvoiceSecurityCode . "&Action=AcceptOrderPayment"; 
			 return($url);
		  }

		  public function getFinishOrderUrl()
		  {
			 $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . "/zyro/ganja/ganja_cmd.php?OrderId=" . $this->pOrderId . "&SecurityCode=" .$this->pInvoiceSecurityCode . "&Action=FinishOrder"; 
			 return($url);
		  }

		  public function getControlPageUrl()
		  {
			 $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . "/zyro/ganja/ganja_cmd.php?OrderId=" . $this->pOrderId . "&SecurityCode=" .$this->pInvoiceSecurityCode . "&Action=GetControlPage"; 
			 return($url);
		  }



		  
		  public function getForm()
		  {
			  $result = null;
			  $this->Refresh();
			  $actionsErrors = $this->ProcessActions();
			  $UAHPricePerItem =  GetTotalPriceUAH();
			  $notEnoughFunds = number_format($this->getNotEnoughFunds(),8);
			  switch ($this->pInterLang) {
				case 'ru':
					$lHeader = 'Заказ № <b>' . $this->pOrderId . '</b>' ;
					$lState = 'Состояние: ';
					$lOrderOwner = 'Заказчик: ' . $this->pCustomerMail;
					$lSetOrder = 'Получить счёт';
					$lQuanity = 'Необходимое кол-во папирос: ';
					$lTargetAddress = 'Адресс доставки: ';
					$lInputAddress = 'Адресс для оплаты: ';
					$lTotalPrice = 'Необходимая сумма: ';
					$lGetInvoiceInfo = 'Просмотреть состояние счёта.';
					$lCancelOrder = 'Отменить заказ';
					$lForgetOrder = 'Забыть о заказе!';
					$lPriceDesc = 'Цена за курсом: ' . $UAHPricePerItem . ' грн./шт.';
					$lBTCPrice = 'Цена в BTC: ' . GetBTCPriceByUAH($UAHPricePerItem) . '/шт.' ;
					$lFundsReceived = 'Принято сервером: ';
					$lnotEnoughFunds = 'По заказу не доплачено: ';
					$lCancelWarning = 'Если Вы передумали делать заказ, ОБЯЗАТЕЛЬНО отмените его. Поскольку, продавец отменить его не может. Ведь может так выйти, что Вы создадите транзакцию, и в этот момент будет отменён заказ! Следовательно отменить заказ можете только Вы, так как только Вам известно была отправка биткоинов или нет. Нарушение этого правила будет ЖЁСТКО караться, в связи с тем что таким образом Вы создаёте резерв на складе, и не выполняя заказ до конца, мешаете другим.';
					$lCheckPayment = 'Сделал платёж';
					$lCheckPaymentInProgress = 'Платёж проверяется';
					break;
				default:
					$lHeader = 'Замовлення № <b>' . $this->pOrderId . '</b>' ;
					$lState = 'Стан: ';
					$lOrderOwner = 'Замовник: ' . $this->pCustomerMail;
					$lSetOrder = 'Отримати рахунок ';
					$lQuanity = 'Необхідна кількість папір: ';
					$lTargetAddress = 'Адреса доставки: ';
					$lInputAddress = 'Адресса для сплати: ';
					$lTotalPrice = 'Необхідна кількість: ';
					$lGetInvoiceInfo = 'Переглянути стан рахунку.';
					$lCancelOrder = 'Відмінити замовлення';                    
					$lForgetOrder = 'Забуть про замовлення!'; 
					$lPriceDesc = 'Ціна за курсом: ' . $UAHPricePerItem .  ' грн./шт.';
					$lBTCPrice = 'Ціна у BTC: ' . GetBTCPriceByUAH($UAHPricePerItem) . '/шт.' ;
					$lFundsReceived = 'Прийнято сервером: ';
					$lnotEnoughFunds = 'По замовленю не доплачено: ';
					$lCancelWarning = 'Якщо Ви передумали робити замовлення, ОБОВЬЯЗКОВО відмініть його. Оскільки, продавець його відмінити не може. Адже може так вийти, що Ви створите транзакцію, і в цей час буде відмінено замовлення! З цього слідує, що відмінити замовлення можете тільки Ви, оскільки тільки Вам відомо, збираєтесь Ви відправляти біткоіни чи ні. Порушення цього правила буде ЖОРСТОКО каратися, в зв"язку з тим, що Ви створюєте резерв у складі, і не виконуючи замовлення , заважаєте іншим.';
					$lCheckPayment = 'Зробив платіж';
					$lCheckPaymentInProgress = 'Платіж перевіряється';
			  };

			  if (isset($actionsErrors) && isset($_POST))
			  {
				$result = '<div class="alert alert-error">' . $actionsErrors . '</div>';
			  }

			  switch ($this->pOrderState) {
				 case 1:
					$result = $result . '<form name="setOrder" id="getOrder" action="" method="post" >';
				   break;
/*				 case 2:
					$result = $result . '<form target="_blank" name="setOrder" id="getOrderInvoiceInfo" action="https://blockchain.info/address/'. $this->pInvoiceAddress .'"  method="get" > ';
				   break;     */
				   default:
					  $result = $result . '<form name="setOrder" id="getOrder" action="" method="post" >';                    
			  }

			  $result = $result . '
						<span id="topControl" class="badge badge-success" style="margin:10px">
						<div class="form-group">
							<div class="pagination pagination-centered" >
								<label id="labelOrderHeader" class="control-label span3" >' . $lHeader . '</label>                          
								<label id="labelOrderState" class="control-label span4" style="margin-top:-5px" >' . $lState . 
									'<button id="btnOrderRefresh" type="button" class="btn btn-info"  onclick="location.reload(true);"><i class="fa fa-refresh fa-spin fa-fw"></i> ' . $this->getStatusDescription() . '</button>                            
									<button id="btnExitOrder" type="submit" name="actionCode" value="forgetOrder" class="btn btn-warning"  onclick="exitFromOrder();"><i class="fa fa-sign-out"></i></button>
								</label>
								<label id="labelOrderOwner" class="control-label span3" >' . $lOrderOwner . '</label>';
			if (($this->pOrderState >= 3) && ($this->pDeliveryMethod == 1))
			{

				$result = $result . '<table >
					  <tr>
						 <td>
							<img src="img/cigarette.png" border="0" width="32" height="32" alt="cigarette.png (1,642 bytes)">
						 </td>
						 <td>
							<img src="img/20150729040137420_easyicon_net_32.png" border="0" width="32" height="32" alt="20150729040137420_easyicon_net_32.png (2,303 bytes)">                           
						 </td>
					  </tr>
					  <tr>
						 <td>
							<label id="labelOrderCount" class="control-label span1" ><span class="badge">' . $this->pReceivedByOrder . '/' . $this->pOrderQuantity  . '</span></strong></label>
						 </td>
						 <td>
							<label id="labelPayedFunds" class="control-label span1" ><span class="badge">' . round($this->getReceivedFunds(),5) . '</span></strong></label>
						 </td>
					  </tr>
				   </table>';
				   /*$result = $result .  '<label id="labelOrderCount" class="control-label span3" >' . $this->pOrderQuantity  . ':</label><img src="img/cigarette.png" border="0" width="32" height="32" alt="cigarette.png (1,642 bytes)">';
				   $result = $result .  '<label id="labelPayedFunds" class="control-label span3" >' . $this->pFundsReceived . ':</label><img src="img/20150729040137420_easyicon_net_32.png" border="0" width="32" height="32" alt="20150729040137420_easyicon_net_32.png (2,303 bytes)">' ;                 
			*/};
			$result = $result .	'</div>                            
							<input type="hidden" name="langCode" value="' . $this->pInterLang . '" >
							<input type="hidden" name="orderId" value="' . $this->pOrderId . '" >
							<input type="hidden" name="accessCode" value="' . $this->pOrderAccessCode . '" >
							<input type="hidden" name="orderStateId" value="' . $this->pOrderState . '">
							<input type="hidden" name="stateHash" value="' . $this->getStateHash() . '">
						</div></span>';

		switch ($this->pOrderState) {
			case -1 :
				$result = $result . '<div class="form-group"></div>';
				break;
			case 1 :
				if ($this -> pDeliveryMethod == 0) {
					$points = BookmarkFactory::GetActiveBookmarks();
					//$points = GetFreeHotpoints(null);
					if (!empty($points)) 
					{
						$result = $result . '<div class="container-fluid "><div class="row" >';
						foreach ($points as $curPoint) 
						{
							$curPointHtml  = $curPoint->GetPublicForm($this->pInterLang);								$result .= $curPointHtml ;  																
						};					
						$result = $result . '</div></div>';
					};
				} else {
					$result = $result . '
								<span>
								<div class="form-group">
									<label for="InputQuantityId" class="control-label" >' . $lQuanity . '</label>
									<input type="number" min="1" max="' . GetAvailableCount() . '" name="orderQuantity" class="form-control" id="InputQuantityId">
								</div>
								<div class="form-group">							
									<label for="inputTargetLocationId" class="control-label" >' . $lTargetAddress . '</label>
									<textarea type="text" rows="4" maxlength="512" name="targetLocation" class="form-control"
									id="inputTargetLocationId" required="required"></textarea>
								   <input type="hidden" name="actionCode" value="confirmOrder" >
								</div>
								   <section class="alert-danger">
								   <p>' . $this -> GetPriceDescription() . '</p>
								   <p>' . $lPriceDesc . '</p>                                
								   <p>' . $lBTCPrice . '</p>
								   </section>
								<div class="form-group">                       
									<button id="getOrderButton" type="submit" class="btn btn-success">' . $lSetOrder . '!</button>
								</div>
								';
				}
				break;
			case 2 :
				$result = $result . '<div class="form-group">
								<label class="control-label" >' . $lQuanity . $this -> pOrderQuantity . '</label>
								<label class="control-label" >' . $lTargetAddress . $this -> pTargetLocation . '</label>
								<label class="control-label" >' . $lInputAddress . '<strong>' . $this -> pInvoiceAddress . '</strong></label>
								<label class="control-label" >' . $lTotalPrice . $this -> pTotalPrice . ' BTC</label>
								<label class="control-label" >' . $lFundsReceived . $this -> pFundsReceived . ' BTC</label>';

				if (isset($notEnoughFunds)) {
					$result = $result . '<div class="alert alert-error"><label class="control-label" ><i class="icon-exclamation-sign icon-white"></i>  ' . $lnotEnoughFunds . '<b>' . $notEnoughFunds . '</b>' . ' BTC</label></div>';
				};

				$result = $result . '</div>
													<div class="form-group">';
				if (!$this -> pIsCheckPaymentClick) {
					$result = $result . '                          
									<a target="_blank" href="https://blockchain.info/address/' . $this -> pInvoiceAddress . '">
									<button id="getOrderStatusButton" type="button" class="btn btn-info span3" style="margin-bottom:10px">' . $lGetInvoiceInfo . '</button>
									</a>
									<button id="setOrderCancelButton" type="submit" name="actionCode" value="cancelOrder" class="btn btn-danger span3" >' . $lCancelOrder . '</button>                                
									<button type="submit" name="actionCode" value="checkPayment"   class="btn btn-success span3">' . $lCheckPayment . '</button>
									</div>
									<div class="span10 pagination-centered alert alert-error">' . $lCancelWarning . '</div>';
				} else {
					$result = $result . '
											</div>
											<div class="span10 pagination-centered alert alert-info">' . $lCheckPaymentInProgress . '</div>';
					$this -> pIsCheckPaymentClick = $this -> CheckPaymentAddress() == null;
				};
				break;
			case 3 :
				$points = GetFreeHotpoints($this -> pOrderQuantity - $this -> pReceivedByOrder);
				$lHotP = null;
				if ($this -> pInterLang == 'ru') {
					$nullPanelContent = 'Гарячих закладок с требуемым кол-вом по Вашему заказу, нет! Ожидайте доставки.';
					$hotPointsWarning = 'Прежде чем брать гарячую закладку дважды подумайте! Естественно, она ни чем не отличается от других закладок. Но есть факт, она лежит дольше, а т.е. вероятность её попадания в том виде что Вам бы хотелось, падает.
								Если Вам не к спеху, не стоит. Лучше подождать новую закладку. Об этом, Вы будете тут же уведомлены. Так же как и если в обратном случае, Вы возьмёте гарячую закладку. Буду тут же уведомлён я. А т.е. фактически сделка окончена.
								Так или иначе. Решать Вам !!!';
				} else {
					$nullPanelContent = 'Гарячих закладинок з необхідною кількістью для Вашого замовлення, немає! Чекайте доставки.';
					$hotPointsWarning = 'Перш ніж узяти гарячу закладинку, подумайте двічі! Звісно, вона нічим не відрізняється від інших закладинок. Але є факт, вона лежить довше, а тому ймовірність ії потрапляння в тому вигляді що Вам би хотілось, падає.
								Якщо Вам не пече, не варто. Краще зачекайте на нову закладинку. Про це, Ви будете миттєво повідомлені. Також як і в випадку, якщо Ви візьмете гарячу закладинку. Я буду одразу повідомлений. А отже фактично замовлення виконано.
								Так чи інакше, вирішувати Вам !!!';

				};
				if (!empty($points)) {

					$result = $result . '<div  class="hot_points" style="">
							<table id="idTblHotPoints" width="95%" ><caption>Гарячі закладинки</caption><tbody>';
					foreach ($points as $value) {
						if ($this -> pInterLang == 'ru') {
							$result = $result . '<tr><td><label class="RegionName" >' . $value['RegionTitle_ru'] . '</label></td><td><span class="badge">' . $value['Quantity'] . '</span><img src="img/cigarette.png" height="32" width="32"><img></td><td><button type="submit" class="btn btn-primary" onclick="selectBookmarkId(' . $value['idBookmark'] . ');" ><i class="icon-shopping-cart icon-white"> </i>Просмотреть и забрать</button></td></tr>';
						} else {
							$result = $result . '<tr><td><label class="RegionName" >' . $value['RegionTitle'] . '</label></td><td><span class="badge">' . $value['Quantity'] . '</span><img src="img/cigarette.png" height="32" width="32"><img></td><td><button type="submit" class="btn btn-primary" onclick="selectBookmarkId(' . $value['idBookmark'] . ');" ><i class="icon-shopping-cart icon-white"> </i>Переглянути та забрати</button></td></tr>';
						}
					};
					$result = $result . '</tdbody></table></div>
					   <input type="hidden" name="actionCode" value="tryToGetBookmark">
					   <input id="IdSelectedBookMark" type="hidden" name="selectedBookMarkId" value="">
					   <div class="alert alert-danger" >' . $hotPointsWarning . '</div>';

				} else {
					$result = $result . '<div class="panel panel-default">
							  <div class="panel-body"><h5>' . $nullPanelContent . '</h5></div>
							</div>';

				};

				break;
			case 4 :
				$points = BookmarkFactory::GetBookmarksForOrder($this -> pOrderId);
				foreach ($points as $value) {
					$result = $result . $value -> GetFormResult($this -> pInterLang);
				}
				break;
			case 100 :
				break;
		}
		$result = $result . '</form>';
		unset($_POST);
		return $result;
	}

		  // Private internal only functions
		  public function getStatusDescription()
		  {
		   switch ($this->pOrderState) {
			case -1:
			   switch ($this->pInterLang) {
				case 'ru':
					return "Отменён";
					break;
				default:
					return "Відмінено";
				};
				break;
			case 1:
			   switch ($this->pInterLang) {
				case 'ru':
					return "Ожидание подтверждения";
					break;
				default:
					return "Очікування підтвердження";
				};
				break;
			case 2:
			   switch ($this->pInterLang) {
				case 'ru':
					return "Ожидание оплаты";
					break;
				default:
					return "Очікування сплати";
				};
				break;
			case 3 :
				switch ($this->pInterLang) {
					case 'ru' :
						return "Оплачен";
						break;
					default :
						return "Сплачено";
				};
				break;
			case 4 :
				switch ($this->pInterLang) {
					case 'ru' :
						return "Доставлено";
						break;
					default :
						return "Доставлено";
				};
				break;
			case 50 :
				switch ($this->pInterLang) {
					case 'ru' :
						return "В процессе отправки";
						break;
					default :
						return "У процессі відправки";
				};
				break;
			case 100 :
				switch ($this->pInterLang) {
					case 'ru' :
						return "Выполнен";
						break;
					default :
						return "Виконано";
				};
				break;
			default :
				switch ($this->pInterLang) {
					case 'ru' :
						return "Не определён";
						break;
					default :
						return "Не встановлений";
				};
			};
		  }

		  private function ProcessActions()
		  {
			  $result = null;
			  if (isset($_POST['actionCode']))
			  {
				 $actionType = $_POST['actionCode'];
			  }
			  if (isset($actionType))
				  {
					switch ($actionType)
					{
						case 'confirmOrder':
							if (isset($_POST['targetLocation']))
							{
							  $targetLoc =   $_POST['targetLocation']; 
							};
							if (isset($_POST['orderQuantity']))
							{
							  $orderQnty =   $_POST['orderQuantity']; 
							};
							if (isset($_POST['BookmarkId']))
							{
							  $bookmarkId =   $_POST['BookmarkId']; 
							};							
														
							
							if (isset($orderQnty) && (isset($targetLoc) || isset($bookmarkId)))
							{
								if ($this->getOrderState() == 1)
								{
									$result =  $this->ConfirmOrder($orderQnty, $targetLoc, 2, $bookmarkId );                                    
								}
							}
							else
							{
							   $result = "Error: Empty parameters for action ConfirmOrder";
							}
							break;
						case 'cancelOrder':
								$result = $this->CancelOrder();
							
							break;
						case 'forgetOrder':
								$this->ForgetOrder();
							break;
						case 'tryToGetBookmark':
							   $result =  $this->TryToGetBookmarkId($_POST['selectedBookMarkId']);
							   if ($result == null)
							   {
									$this->Refresh();  
							   };
							break;
						case 'checkPayment':
								$this->pIsCheckPaymentClick = true;
							break;
						default:
							$result = "Error:Unidentified action code!";
					};
				  };
			  $_POST['actionCode'] = null;
			  return $result;
		  }

		  
		  
		  
		  public function ConfirmOrder($pOrderQuantity, $pTargetLocation = null, $pNewState = 2, $idBookmark = null)
		  {
			$result = null;
			if (is_numeric($pOrderQuantity))
			 {
				if ($pOrderQuantity <= GetAvailableCount())
				{
					// Prepare all for the transaction
					$vTotalPrice =  GetBTCPriceByUAH(GetTotalPriceUAH($pOrderQuantity));
					if (isset($vTotalPrice))
					{
						if (GetDbConnector($dbCon))
						{
							try
							{
					
								$dbCon->autocommit(FALSE);
								$this->Refresh();

								$query = "UPDATE `Orders`
											SET
											`OrderState` = $pNewState ,
											`OrderQuantity` = ?,
											`TargetLocation` = ?,
											`InvoiceAddress` = ?,
											`TotalPrice` = ?
											WHERE idOrder = ?;";
								if ($stmt = $dbCon->prepare($query))
									{
										if ($this->pInvoiceAddress == null)
										{                                        
											$vInvoiceAddress = $this->GetPaymentAddress();
										}
										else
										{   
											$vInvoiceAddress = $this->pInvoiceAddress;
										};
										
										if (isset($vInvoiceAddress))
										{
												if (is_numeric($idBookmark))
												{
													$assignRes = $this->TryToGetBookmarkId($idBookmark);
													if (is_numeric($assignRes))
													{
													   throw new Exception('Error while try to assign bookmark to order!Error code:' . $assignRes); 
													}
												}
												$stmt->bind_param('issdi', $pOrderQuantity, $pTargetLocation, $vInvoiceAddress, $vTotalPrice, $this->pOrderId  );
												$stmt->execute();
												$dbCon->commit();
												$dbCon->autocommit(TRUE);
												// All Ok. Save state to object
												$this->pOrderState = $pNewState;
												$this->pOrderQuantity = $pOrderQuantity;
												$this->pTargetLocation = $pTargetLocation;
												$this->pInvoiceAddress = $vInvoiceAddress;
												$this->pTotalPrice = $vTotalPrice;
												// Send notification to saller 
												SendNewOrderNotification($this->pOrderId);
											}
										else
										{
											  switch ($this->pInterLang) {
												case 'ru':
													$result = 'Счёт на оплату не сформирован, не доступен сервер платежей. Попробуйте позже.';
													break;
												default:
													$result = 'Рахунок на сплату не сформовано, не доступний сервер платежів. Спробуйте пізніше.';
											   };
											   throw new Exception('Error while get input address!');
											} ;
									}
										else
									{
										throw new Exception($conn->error);
									}                                
							}
							catch ( Exception $e ) {
								// before rolling back the transaction, you'd want
								// to make sure that the exception was db-related
								$dbCon->rollback();
								$dbCon->autocommit(TRUE);
								if (!isset($result))
								{
									 switch ($this->pInterLang) {
											case 'ru':
												$result = 'Ошибка доступа к базе данных! Попробуйте позже.';
												break;
											default:
												$result = 'Помилка доступу до бази даних! Спробуйте пізніше.';
										   };
								};
							 };
						}
						else
						{
							 switch ($this->pInterLang) {
									case 'ru':
										$result = 'Ошибка доступа к базе данных! Попробуйте позже.';
										break;
									default:
										$result = 'Помилка доступу до бази даних! Спробуйте пізніше.';
								   };
						}
					}
					else
					{
					switch ($this->pInterLang) {
								case 'ru':
									$result = 'Ошибка при рассчёте стоимости заказ! Попробуйте позже.';
									break;
								default:
									$result = 'Помилка при розрахунку вартосі замовлення! Спробуйте пізніше.';
							   };
					}

				}
				else
				{
				  switch ($this->pInterLang) {
					case 'ru':
						$result = 'К сожалению такого количества нет в наличии. Закажите меньше.';
						break;
					default:
						$result = 'На жаль, такої кількості немає в наявності. Замовте менше.';
				   };
				}
			 }
			else
			 {
				  switch ($this->pInterLang) {
					case 'ru':
						$result = 'Не корректно указано количество!';
						break;
					default:
						$result = 'Не вірно вказана кількість!';
				   };
			 };
			return $result;
		  }
		  
		  private function TryToGetBookmarkId($pBookMarkId)
		  {
			$resultMessage = "Error";
			  if (GetDbConnector($dbCon))
			  {
				  $callQuery = "CALL `AttachPointToOrder`(?, ?, @pResultId)";
				  $call = $dbCon->prepare($callQuery);
				  $call->bind_param('ii', $this->pOrderId, $pBookMarkId);
				  $call->execute();

				  $select = $dbCon->query("SELECT  @pResultId");
				  $result = $select->fetch_assoc();
				  $pResultId    = $result['@pResultId'];
				  if ( $pResultId == 100)
				  {
					$resultMessage = null;
				  }
				  else
				  {
					$resultMessage =   $pResultId;                    
				  }
			  }
			  return $resultMessage;       
		  }

		  public function TryToSetBookmarkIdCatched($pBookMarkId)
		  {
			$resultMessage = "Error";
			  if (GetDbConnector($dbCon))
			  {
				  $callQuery = "CALL `SetPointCatchedToOrder`(?, @pResultId)";
				  $call = $dbCon->prepare($callQuery);
				  $call->bind_param('i', $pBookMarkId);
				  $call->execute();

				  $select = $dbCon->query("SELECT  @pResultId");
				  $result = $select->fetch_assoc();
				  $pResultId    = $result['@pResultId'];
				  if ( $pResultId == 100)
				  {
					$resultMessage = null;
				  }
				  else
				  {
					$resultMessage =   $pResultId;                    
				  }
			  }
			  return $resultMessage;                
		  }          
		  
		  public function acceptOrderPayment()
		  {
			$result = false;            
		// Prepare all for the transaction
						if (GetDbConnector($dbCon))
						{
							try
							{
								$dbCon->autocommit(FALSE);
								$this->Refresh();
								
								switch ($this->pDeliveryMethod) {
									case '0':
										$newState = 4;
										break;
									case '1':
										$newState = 3;
										break;
									
									default:
										$newState = 50;
										break;
								}
								
								/*if ($this->pDeliveryMethod = 1)
								{
									$newState = 3;
									
								}
								else
								{
									$newState = 50;
								}*/

								$query = "UPDATE `Orders`
											SET
											`OrderState` = ?
											WHERE idOrder = ?;";
								if ($stmt = $dbCon->prepare($query))
									{
												/* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
												$stmt->bind_param('ii', $newState ,$this->pOrderId  );
												$stmt->execute();
												$dbCon->commit();
												$dbCon->autocommit(TRUE);
												// All Ok. Save state to object
												$this->pOrderState = $newState;
												// Need to send notification
												
												$result = true;
									};
							 
							}
							catch ( Exception $e ) {
								// before rolling back the transaction, you'd want
								// to make sure that the exception was db-related
								$dbCon->rollback();
								$dbCon->autocommit(TRUE);
							 }
						}                                            
			return $result;  
		  }

		  public function notifyOrderPayment($transaction_hash)
		  {
		  
			$paymentObj = PaymentFactory::create($this->pOrderId, $transaction_hash);
			if (isset($paymentObj))
			{
				if ($paymentObj->pIsAdminNotified == 0)
				{
					$result = SendPaymentNotification($this->pOrderId, $paymentObj->pValue_in_btc);                      
					if ($result = 'Ok')
					{
						$paymentObj->setIsAdminNotify();
					}
				}
				else
				{
					   $result = 'Already notified';
				}
				
			}        
			return $result;
		  }
		  
		  public function rejectOrderPayment()
		  {
			$result = false;
			 

					// Prepare all for the transaction
						if (GetDbConnector($dbCon))
						{
							try
							{
								$dbCon->autocommit(FALSE);
								$this->Refresh();

								$query = "UPDATE `Orders`
											SET
											`OrderState` = 2
											WHERE idOrder = ?;";
								if ($stmt = $dbCon->prepare($query))
									{
												/* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
												$stmt->bind_param('i', $this->pOrderId  );
												$stmt->execute();
												$dbCon->commit();
												$dbCon->autocommit(TRUE);
												// All Ok. Save state to object
												$this->pOrderState = 2;
												// Need to send notification
												
												$result = true;
									};
							 
							}
							catch ( Exception $e ) {
								// before rolling back the transaction, you'd want
								// to make sure that the exception was db-related
								$dbCon->rollback();
								$dbCon->autocommit(TRUE);
							 }
						}                                            
			return $result;  
		  }          

	  public function SetDeliveryMethod($deliveryMethod)
	  {
			$result = false;
			 

					// Prepare all for the transaction
						if (GetDbConnector($dbCon))
						{
							try
							{
								$newState = $this->getOrderState();
								if ($deliveryMethod == 0)
								{
									if (($this->getOrderState() < 25) && ($this->getOrderState() > 0 ))
									{
										$newState = 25;
									}
								} 
								
								
								$dbCon->autocommit(FALSE);
								$this->Refresh();
								$date = date('Y-m-d H:i:s');
								$query = "UPDATE `Orders`
											SET
											`DeliveryMethod` = " . $deliveryMethod . ", 
											`OrderState` = " . $newState . "WHERE `idOrder` = ?;";
								if ($stmt = $dbCon->prepare($query))
									{
												/* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */

												$stmt->bind_param('i', $this->pOrderId );
												$stmt->execute();
												$dbCon->commit();
												$dbCon->autocommit(TRUE);
												// All Ok. Save state to object
												$this->pDeliveryMethod = $deliveryMethod;
												$result = true;
									};
							 
							}
							catch ( Exception $e ) {
								// before rolling back the transaction, you'd want
								// to make sure that the exception was db-related
								$dbCon->rollback();
								$dbCon->autocommit(TRUE);
							}
						}                                            
			return $result;  
	  }

		  public function FinishOrder()
		  {
			$result = false;
			 

					// Prepare all for the transaction
						if (GetDbConnector($dbCon))
						{
							
								$callQuery = "CALL `FinishOrder`(?)";
								  $call = $dbCon->prepare($callQuery);
								  $call->bind_param('i', $this->pOrderId);
								  $call->execute();
								  $result = true;
						}                                            
			return $result;  
		  }          
		  
		  private function CancelOrder()
		  {
			$result = 'Error';
				  if (GetDbConnector($dbCon))
				  {
					  $callQuery = "CALL `CancelOrder`(?, ?)";
					  $call = $dbCon->prepare($callQuery);
					  $call->bind_param('si', $this->pCustomerMail, $this->pOrderId);
					  $call->execute();
					  
					  $this->ForgetOrder();
					  $result = null;
				  }            
			return $result; 
		  }

		  private function Refresh()
		  {
				  if (GetDbConnector($dbCon))
					{
						$query = "SELECT 
									`OrdersInfo`.`OrderState`,
									`OrdersInfo`.`CustomerMail`,
									`OrdersInfo`.`OrderQuantity`,
									`OrdersInfo`.`TargetLocation`,
									`OrdersInfo`.`TotalPrice`,
									`OrdersInfo`.`InvoiceAddress`,
									`OrdersInfo`.`FundsReceived`,
									`OrdersInfo`.`ReceivedByOrder`,         
									`OrdersInfo`.`DeliveryMethod`,
									`OrdersInfo`.`TotalFunds`
									FROM `OrdersInfo` where idOrder = ?" ;
						if ($stmt = $dbCon->prepare($query)) {
							$stmt->bind_param('i', $this->pOrderId );
							$stmt->execute();
							$stmt->bind_result($OrderState,$CustomerMail, $OrderQuantity,$TargetLocation, 
							$TotalPrice, $InvoiceAddress, $FundsReceived, $ReceivedByOrder, $DeliveryMethod, $TotalFunds);
							while ($stmt->fetch()) {
								  $this->pOrderState = $OrderState;
								  $this->pCustomerMail = $CustomerMail;
								  $this->pOrderQuantity = $OrderQuantity;
								  $this->pTargetLocation = $TargetLocation;
								  $this->pTotalPrice = $TotalPrice;
								  $this->pInvoiceAddress = $InvoiceAddress;
								  $this->pFundsReceived = $FundsReceived;
								  $this->pReceivedByOrder = $ReceivedByOrder;
								  $this->pDeliveryMethod = $DeliveryMethod;
								  $this->pTotalFunds = $TotalFunds;
								  //if ($this->pFundsReceived <= $TotalFunds) 
								  //	  {
								  //  	$this->pFundsReceived = $TotalFunds;
								  //  };  
							}
							$stmt->close();
						}
					}              
		  }
		  
		  private function ForgetOrder()
		  {
					  $_SESSION = array();
					  
						// If it's desired to kill the session, also delete the session cookie.
						// Note: This will destroy the session, and not just the session data!
						if(isset($_COOKIE[session_name()])):
								setcookie(session_name(), '', time()-7000000, '/');
							endif;       
				  
							// Finally, destroy the session.
							session_destroy();

							// Take the user to the successive page if no errors
							header("location: /");              
		  }
		  
	  private function GetPaymentAddress()
	  {
			require_once 'global_vars.php';		
			  $result = null;
			  $secret = $this->pInvoiceSecurityCode;
			  $mycallback_url = callback_url . $this->pOrderId . '&secret=' . $secret ;
			  
						  try
						  {
						  switch (paymentApiType)
							  {
								case 'block.io':
										require_once 'block_io.php';
										
										$apiKey = "eb79-6015-7777-f428";
										$version = 2; // API version
										$pin = "06102010";
										$block_io = new BlockIo($apiKey, $pin, $version);
										$newAddr = $block_io->create_forwarding_address(array('to_address' => targetWallet));
										if ($newAddr->status == "success" )
										{
											$result = $newAddr->data->forwarding_address; 
											//$block_io->create_notification(array('type' => 'address', 'address' => $result , 'url' => urlencode($mycallback_url) ));   
										};						    
									break;
								default:

									  $root_url = 'https://blockchain.info/api/receive';
									  $parameters = 'method=create&address=' . targetWallet .'&callback='. urlencode($mycallback_url);
									  $finalUrl  = $root_url . '?' . $parameters;
									  $response = file_get_contents($finalUrl);
									  $object = json_decode($response);
									  if (isset($object))
									  {
										  $result = $object->input_address;
									  }
									  else
										 {
											 throw new Exception('Request failed:'. $finalUrl);
										 };
								};
						  }
						  catch (Exception $e)
						  {
							  error_log("Error on GetPaymentAddress:" . $e->getMessage().PHP_EOL );
						  }; 

			 
			  return $result;
		  }
		  
		  private function CheckPaymentAddress()
		  {
			  $result = null;
			  try
			  {
				  $root_url = 'https://blockchain.info/address/';
				  $parameters = 'format=json';
				  $response = file_get_contents($root_url . $this->pInvoiceAddress . '?' . $parameters);
				  $object = json_decode($response);
				  $inBtc = $object->total_received / 100000000;
				  $this->setTotalFunds($inBtc);
				  if ($inBtc >= $this->pTotalPrice)
				  {
					$this->acceptOrderPayment();
				  };                  
			  }
			  catch ( Exception $e)              
			  {
				 error_log("Error on checkPaymentAddress:" . $e->getMessage().PHP_EOL ,3, $_SERVER["DOCUMENT_ROOT"] . "/ganja/errors.log");
				 $result = 'Error';                                  
			  }
			  return $result;
		  }
		  
		  public function GetPriceDescription()
		  {
			  switch ($this->pInterLang) {
				case 'ru':
					$vAvgRateLabel = "Средний курс ";
					break;
				default:
					$vAvgRateLabel =  "Середній курс ";                
			  }            
			  $vRate = GetRateBy_btcbank();
			  if (isset($vRate))
			  {
				  $vComments = 'Get for OrderId: ' . $this->pOrderId ;
				  SetExchangeRate($vRate['avg'], $vComments);
				  return ($vAvgRateLabel . '<a href="http://btcbank.com.ua" target="_blank" >BTCBANK.COM.UA: </a>' . $vRate['avg'] );  
			  }
				
		  }

		};


?>
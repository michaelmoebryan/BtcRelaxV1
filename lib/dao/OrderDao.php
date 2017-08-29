<?php
    namespace BtcRelax;
	
    use \PDO;
    use BtcRelax\Dao\OrderSearchCriteria;
    use BtcRelax\NotFoundException;    	
    use BtcRelax\Model\Order;
    use BtcRelax\Mapping\OrderMapper;
    use Exception;

final class OrderDao extends \BtcRelax\BaseDao
	{
	
            public function registerOrder(Order $newOrder)   {
                $result = false;
                try {
                    $preorderedBookmark = $newOrder->getBookmark();
                    $db = $this->getDb(); 		 
                    // execute the stored procedure
                    $callQuery = "CALL `RegisterOrder4HotPoint`(:pIdCustomer,:pBTCPrice,:pBookMarkId,:pInvoiceAddress,@out_id,@pOrderId)";
                    $call = $db->prepare($callQuery);
                    $creatorId = $newOrder->getCreator() ;
                    $btcPrice = $newOrder->getBTCPrice();
                    $invoiceAddress = $newOrder->getInvoiceAddress();
                    $preorderedBookmarkId = $preorderedBookmark->getIdBookmark() ;
                    $call->bindParam(':pIdCustomer',$creatorId,PDO::PARAM_STR);
                    $call->bindParam(':pBTCPrice',$btcPrice,PDO::PARAM_STR);
                    $call->bindParam(':pBookMarkId',$preorderedBookmarkId,PDO::PARAM_INT);
                    $call->bindParam(':pInvoiceAddress',$invoiceAddress,PDO::PARAM_STR);
                    //$call->bind_param('ss', $vId, $vRecoveryMail);
                    $call->execute();
                    // execute the second query to get values from OUT parameter
                    $select = $db->query("SELECT  @out_id, @pOrderId");
                    $selResult = $select->fetch(PDO::FETCH_ASSOC);
                    if ($selResult)
                        {
                            $pResultId    = $selResult['@out_id'];
                            $pOrderId = $selResult['@pOrderId'];      
                            if ($pResultId == 0)
                            {
                                $dao = new OrderDao();
                                $savedOrder = $dao->findById($pOrderId);
                                $result = $savedOrder;
                            }
                            else
                            {
                                $errorMsg = sprintf("Error registering order:%s Procedure result:%s", $newOrder , $pResultId);
                                $newOrder->setLastError($errorMsg);
                                Log::general($errorMsg, Log::WARN ); 
                            }
                        }
                } catch (PDOException $pe) {
                        Log::general($pe->getMessage(), Log::WARN ); 
                        $newOrder->setLastError($pe->getMessage());
                        $result = $newOrder;
                }
            return $result;
            }

            public function updateHotBalance(Order $updOrder)  {
               $result = false;
               try {
                    $db = $this->getDb();
                    $orderId = $updOrder->getIdOrder();
                    $balance = $updOrder->getInvoiceBalance();
                    $callQuery = 'select `UpdateHotBalance`(:pOrderId, :pBalance)';
                    $call = $db->prepare($callQuery);
                    $call->bindParam(':pOrderId',$orderId,PDO::PARAM_INT);
                    $call->bindParam(':pBalance',$balance,PDO::PARAM_STR);
                    $call->execute();
                    $selResult = $call->fetch();
                    if ($selResult)
                    {
                        $pResultId    = $selResult[0];
                        if ($pResultId == 1)
                        {
                            $result = $this->findById($updOrder->getIdOrder());
                        }
                    }
               }
               catch (PDOException $pe) {
                        Log::general($pe->getMessage(), Log::WARN ); 
                        $updOrder->setLastError($pe->getMessage());
                        $result = $updOrder;
                }
                return $result;
            }
                    
            public function find(OrderSearchCriteria $search = null) {
			$result = [];
			$cnt = 0;
			foreach ($this->query($this->getFindSql($search)) as $row) {
				$order = new Order();
				OrderMapper::map($order, $row);
				$cnt = $cnt + 1;  
				$result[$cnt] = $order;
			}
			return $result;
		}
		
            private function getFindSql(OrderSearchCriteria $search = null) {		
				$sql = 'SELECT idOrder, CreateDate, EndDate, OrderState,  BTCPrice, PricingDate, InvoiceAddress, idCreator, '
                                        . 'DeliveryMethod, InvoiceBalance, BalanceDate,OrderHash FROM vwOrders '; 
                                $orderBy = 'CreateDate';
                                        $filter = '';
                                        if ($search !== null) {
                                                if ($search->getIsActive())
                                                {
                                                    $filter = $this->addToFilter($filter, ' NOW() BETWEEN CreateDate AND  COALESCE(EndDate, NOW())');
                                                }
                                                if ($search->getStatus() !== null) {
							//$sql .= 'AND State = ' . $this->getDb()->quote($search->getStatus());
                                                        switch ($search->getStatus()) {
								case Order::STATUS_CREATED:
									$where = 'Created';
									break;
                                                                case Order::STATUS_CONFIRMED:
									$where = 'Confirmed';
									break;
                                                                case Order::STATUS_PAID:
									$where = 'Paid';
									break;    
                                                                case Order::STATUS_WAIT_FOR_PAY:
									$where = 'WaitForPayment';
									break;
								case Order::STATUS_CANCELED:
									$where = 'Canceled';
									break;
                                                                case Order::STATUS_FINISHED:
									$where = 'Finished';
									break;
								default:
									throw new NotFoundException('No order for status: ' . $search->getStatus());
							}
                                                        $filter = $this->addToFilter($filter, sprintf('OrderState = \'%s\'', $where ));
						}
                                                if ($search->getCustomerId() !== null)
                                                {
                                                   $filter = $this->addToFilter($filter, sprintf('idCreator = \'%s\'',$search->getCustomerId() ) ); 
                                                }
                                         }
                                        $sql .= sprintf('%s ORDER BY %s',$filter , $orderBy);
                                        $msg = sprintf('Final query generated by OrderDao is: %s',$sql );
                            LOG::general($msg, LOG::INFO);
                            return $sql;
			}
		
            public function findById($id) {
		            $result=false;
							  $row = parent::query(sprintf("SELECT idOrder, CreateDate, EndDate, OrderState,  BTCPrice, PricingDate, InvoiceAddress, idCreator, "
                        . "DeliveryMethod, InvoiceBalance, BalanceDate,OrderHash  FROM vwOrders WHERE IdOrder = '%s' LIMIT 1 ", $id))->fetch();
                if (!$row) { $result=null;}
                else
                {
                    $order = new Order();
                    OrderMapper::map($order, $row);
                    $result = $order;   
                }
                return $result;
            }
            
            public function getHashByOrderId($orderId)
            {
               $result = false;
               try {
                    $db = $this->getDb();
                    $callQuery = 'select `GetHashByOrderId`(:pOrderId)';
                    $call = $db->prepare($callQuery);
                    $call->bindParam(':pOrderId',$orderId,PDO::PARAM_INT);
                    $call->execute();
                    $selResult = $call->fetch();
                    if ($selResult)
                    {
                        $result    = $selResult[0];
                    }
               }
               catch (PDOException $pe) {
                        Log::general($pe->getMessage(), Log::ERROR ); 
                }
                return $result;
            }
} 


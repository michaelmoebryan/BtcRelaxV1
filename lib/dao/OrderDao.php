<?php
	namespace BtcRelax;
	
	use \PDO;
	use BtcRelax\Config;
	use BtcRelax\BaseDao;
	use BtcRelax\Dao\OrderSearchCriteria;
	use BtcRelax\NotFoundException;    	
	use BtcRelax\Model\Order;
	use BtcRelax\Mapping\OrderMapper;
	 
	final class OrderDao extends \BtcRelax\BaseDao
	{
	
	
		
		public function find(OrderSearchCriteria $search = null) 
		{
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
				$sql = 'SELECT IdOrder, CreateDate, EndDate, OrderState, idSaller, BTCPrice, InvoiceAddress, idCreator FROM vwOrders ';
					$orderBy = 'CreateDate';
                                        if ($search !== null) {
                                                if ($search->getIsActive())
                                                {
                                                    $whereActual = 'NOW() BETWEEN CreateDate AND  COALESCE(EndDate, NOW())';
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
								default:
									throw new NotFoundException('No order for status: ' . $search->getStatus());
							}
						}
					}
                                        if (isset($whereActual))
                                        {
                                            $sql .= sprintf('WHERE %s', $whereActual );    
                                            if (isset($where))
                                            {
                                                $sql .= sprintf('AND OrderState = \'%s', $where );
                                            }
                                        } else {
                                            if (isset($where))
                                            {
                                                $sql .= sprintf('WHERE OrderState = \'%s', $where );
                                            }
                                        }
                                        $sql .= ' ORDER BY ' . $orderBy;     
                            return $sql;
			}
		
		public function findById($id) {
			$row = parent::query(sprintf("SELECT IdOrder, CreateDate, EndDate, OrderState, idSaller, BTCPrice, InvoiceAddress, idCreator "
                                . " FROM vwOrders WHERE IdOrder = '%s' LIMIT 1 ", $id))->fetch();
			if (!$row) {
				return null;
			}
			$order = new Order();
			OrderMapper::map($order, $row);
			return $order;
		}
		
	} 


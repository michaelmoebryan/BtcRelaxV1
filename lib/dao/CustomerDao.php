<?php
	namespace BtcRelax;

        use BtcRelax\Mapping\CustomerMapper;
        use BtcRelax\Model\Customer;
        use PDO;
        
	final class CustomerDao extends BaseDao
	{
		
//		public function setNewNick($pNick, $pCustomer)
//		{
//			  try {
//					$pdo = $this->getDb();			 
//					// execute the stored procedure
//					$sql = 'CALL SetNick(:newNick, :Customer ,@out_result,@error_msg)';
//					$stmt = $pdo->prepare($sql);
//					$pCustId = $pCustomer->getIdCustomer();
//					$stmt->bindParam(':newNick', $pNick, PDO::PARAM_STR);
//					$stmt->bindParam(':Customer', $pCustId, PDO::PARAM_STR);
//					$stmt->execute();
//					$stmt->closeCursor();
//			 
//					// execute the second query to get values from OUT parameter
//					$r = $pdo->query("SELECT @out_result,@error_msg")
//							  ->fetch(PDO::FETCH_ASSOC);
//					if ($r) {
//							if ($r['@out_result'] == 1 )
//							{
//								$pCustomer->setNick($pNick);
//							}
//							else
//							{
//								die("Error occurred:" . $r['@error_msg']);  
//							}
//					}
//			 } catch (PDOException $pe) {
//						die("Error occurred:" . $pe->getMessage());
//			 }
//		}
//		
                public function registerUserId($vId)
                {
 			$result = false;  
                        try {
                                        $vRecoveryMail = '';
                                        $db = $this->getDb(); 		 
					// execute the stored procedure
                                        $callQuery = "CALL `CreateCustomerByBitId`(:pBitId,:pRecoveryMail, @out_id, @error_msg)";
                                        $call = $db->prepare($callQuery);
                                        $call->bindParam(':pBitId', $vId, PDO::PARAM_STR);
					$call->bindParam(':pRecoveryMail', $vRecoveryMail , PDO::PARAM_STR);
                                        //$call->bind_param('ss', $vId, $vRecoveryMail);
                                        $call->execute();
					// execute the second query to get values from OUT parameter
                                        $select = $db->query("SELECT  @out_id, @error_msg");
                                        $result = $select->fetch(PDO::FETCH_ASSOC);
                                        if ($result)
                                        {
                                            $pResultId    = $result['@out_id'];
                                            $pPResultMsg = $result['@error_msg'];      
                                            if ($pResultId)
                                            {
                                                $result = true;
                                            }
                                        }					
			 } catch (PDOException $pe) {
					$result = false;	
                                        //die("Error occurred:" . $pe->getMessage());
			 };
                         return $result;
                }
            
            
		public function findById($id)
		{
			$row = parent::query(sprintf("SELECT * FROM Customers WHERE idCustomer = '%s' LIMIT 1 ", $id))->fetch();
			if (!$row) {
				return null;
			}
			$cust = new Customer();
			CustomerMapper::map($cust, $row);
			return $cust;
		}
		
	} 
?>

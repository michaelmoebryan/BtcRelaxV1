<?php
	namespace BtcRelax;

        use BtcRelax\Mapping\CustomerMapper;
        use BtcRelax\Model\Customer;
        use PDO;
        
	final class CustomerDao extends BaseDao
	{
		
        public function registerUserId($vId)
        {
            $result = false;  
                try {
                    $vRecoveryMail = '';
                    $db = $this->getDb(); 		 
                    // execute the stored procedure
                    $callQuery = "CALL `CreateCustomerByBitId`(:pBitId,:pRecoveryMail, @out_id)";
                    $call = $db->prepare($callQuery);
                    $call->bindParam(':pBitId', $vId, PDO::PARAM_STR);
                    $call->bindParam(':pRecoveryMail', $vRecoveryMail , PDO::PARAM_STR);
                    //$call->bind_param('ss', $vId, $vRecoveryMail);
                    $call->execute();
                    // execute the second query to get values from OUT parameter
                    $select = $db->query("SELECT  @out_id");
                    $result = $select->fetch(PDO::FETCH_ASSOC);
                    if ($result)
                        {
                            $pResultId = $result['@out_id'];
     
                            if ($pResultId)
                            {
                                $result = true;
                            }
                        }					
                } catch (PDOException $pe) {
                    Log::general($pe->getMessage(), LOG::WARN );
                    $result = false;	
                }
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

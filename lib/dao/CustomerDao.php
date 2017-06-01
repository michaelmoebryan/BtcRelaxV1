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
            
         public function getUserByToken(\BtcRelax\Model\token $vToken)
        {
            $result = false;  
                try {
                    $vCustId = $vToken->getIdCustomer();
                    $resultObject = $this->findById($vCustId);
                    if (FALSE !== $resultObject)
                    {
                        $result = $resultObject;
                    };
//                      $db = $this->getDb(); 		 
//                    $callQuery = "CALL `GetCustomerByActiveToken`(:pToken, @pIdCustomer, @pResult)";
//                    $call = $db->prepare($callQuery);
//                    $call->bindParam(':pToken', $vToken, PDO::PARAM_STR);
//                    $call->execute();
//                    // execute the second query to get values from OUT parameter
//                    $select = $db->query("SELECT @pIdCustomer,@pResult");
//                    $result = $select->fetch(PDO::FETCH_ASSOC);
//                    if ($result)
//                        {
//                            $pResultId = $result['@pResult'];
//                            if ($pResultId == 0)
//                            {
//                                $customerId =  $result['@pIdCustomer'];
//                                $result = $customerId;
//                            }
//                        }					
                } catch ( Exception $pe) 
                {
                  BtcRelax\Log::general($pe->getMessage(), LOG::WARN );
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
		
	public function GetPubKeyByCustomer($customerId)
        {
               $result = false;
               try {
                    $db = $this->getDb();
                    $callQuery = 'select `GetPubKeyByCustomer`(:pIdCustomer )';
                    $call = $db->prepare($callQuery);
                    $call->bindParam(':pIdCustomer',$customerId ,PDO::PARAM_STR);
                    $call->execute();
                    $selResult = $call->fetch(PDO::FETCH_NUM);
                    if ($selResult)
                    {
                        $result    = $selResult[0];
                    	Log::general(sprintf('PubKey:%s received for CustomerId:%s',$result,$customerId ), Log::INFO);
		    }
               }
               catch (PDOException $pe) {
                        Log::general($pe->getMessage(), Log::ERROR ); 
                }
                return $result;
        }

        public function GetInvoiceAddressCountByXPub($xPubKey)
        {
                $result = false;
               try {
                    $db = $this->getDb();
                    $callQuery = 'select `GetInvoiceAddressCountByXPub`(:pXPubKey )';
                    $call = $db->prepare($callQuery);
                    $call->bindParam(':pXPubKey',$xPubKey ,PDO::PARAM_STR);
                    $call->execute();
                    $selResult = $call->fetch(PDO::FETCH_NUM);
                    if ($selResult)
                    {
                        $invoicesCount = $selResult[0];
                        $result = $this->get_numeric($invoicesCount);
                        
                    }
               }
               catch (PDOException $pe) {
                        Log::general($pe->getMessage(), Log::ERROR ); 
                }
            return $result;           
        }
        
        public function AddInvoiceAddressToXPub($xPubKey,$invoiceAddres, $inBalance)
        {
                $result = false;
               try {
                    $db = $this->getDb();
                    $callQuery = 'select `AddInvoiceAddressToXPub`(:pXPubKey , :pInvoiceAddres, :pBalance)';
                    $call = $db->prepare($callQuery);
                    $call->bindParam(':pXPubKey',$xPubKey ,PDO::PARAM_STR);
                    $call->bindParam(':pInvoiceAddres',$invoiceAddres ,PDO::PARAM_STR);
                    $call->bindParam(':pBalance', $inBalance ,PDO::PARAM_STR);
                    $call->execute();
                    $selResult = $call->fetch(PDO::FETCH_NUM);
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
        
        
        
        


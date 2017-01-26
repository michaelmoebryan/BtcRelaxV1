<?php
	namespace BtcRelax;
	
	use \PDO;
	use BtcRelax\Config;
	use BtcRelax\BaseDao;
	use BtcRelax\NotFoundException;
	use BtcRelax\Model\Customer;
	use BtcRelax\Mapping\CustomerMapper;
	
	final class CustomerDao extends \BtcRelax\BaseDao
	{
		
		public function setNewNick($pNick, $pCustomer)
		{
			  try {
					$pdo = $this->getDb();			 
					// execute the stored procedure
					$sql = 'CALL SetNick(:newNick, :Customer ,@out_result,@error_msg)';
					$stmt = $pdo->prepare($sql);
					$pCustId = $pCustomer->getIdCustomer();
					$stmt->bindParam(':newNick', $pNick, PDO::PARAM_STR);
					$stmt->bindParam(':Customer', $pCustId, PDO::PARAM_STR);
					$stmt->execute();
					$stmt->closeCursor();
			 
					// execute the second query to get values from OUT parameter
					$r = $pdo->query("SELECT @out_result,@error_msg")
							  ->fetch(PDO::FETCH_ASSOC);
					if ($r) {
							if ($r['@out_result'] == 1 )
							{
								$pCustomer->setNick($pNick);
							}
							else
							{
								die("Error occurred:" . $r['@error_msg']);  
							}
					}
			 } catch (PDOException $pe) {
						die("Error occurred:" . $pe->getMessage());
			 }
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

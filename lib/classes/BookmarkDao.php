<?php
	namespace BtcRelax;
	
	use \PDO;
	use BtcRelax\Config;
	use BtcRelax\BaseDao;
	use BtcRelax\NotFoundException;
	use BtcRelax\Model\Customer;
	use BtcRelax\Model\Bookmark;
	use BtcRelax\Mapping\CustomerMapper;
	 
	final class BookmarkDao extends \BtcRelax\BaseDao
	{
		

		public function findById($id) {
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

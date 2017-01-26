<?php
  namespace BtcRelax\Mapping;
  
  use \DateTime;
  use \BtcRelax\Model\Customer;
  
  
  final class CustomerMapper {

	private function __construct() {
	}


	public static function map(\BtcRelax\Model\Customer $cust, array $properties) {
		if (array_key_exists('CreateDate', $properties)) {
			$createdOn = self::createDateTime($properties['CreateDate']);
			if ($createdOn)
			{
				$cust->setCreateDate($createdOn);  
			}    

		}
		if (array_key_exists('idCustomer', $properties)) {
			$cust->setIdCustomer($properties['idCustomer']);
		}
/*		if (array_key_exists('Nick', $properties)) {
			if (is_null($properties['Nick']) === false)
			{
				$nick = ($properties['Nick']);     
				$cust->setNick($nick);			
			};


		}  */
		
		
		if (array_key_exists('isBaned', $properties)) {
			$isBaned = true;
			if ($properties['isBaned'] == '' || $properties['isBaned'] == 0)
			{
				$isBaned = false;
			};			
			$cust->setIsBaned($isBaned);

		}
		
		
		
		
		

	}

	private static function createDateTime($input) {
		return DateTime::createFromFormat('Y-n-j H:i:s', $input);
	}

}

  
  
?>

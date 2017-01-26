<?php
  namespace BtcRelax\Mapping;
  
  use \DateTime;
  use \BtcRelax\Model\Bookmark;
  
  
  final class BookmarkMapper {

	private function __construct() {
	}


	public static function map(\BtcRelax\Model\Bookmark $bookmark, array $properties) {
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

        if (array_key_exists('OpenFrom', $properties)) {
            $cust->setOpenFrom($properties['OpenFrom']);
        }        
      
      if (array_key_exists('EndDate', $properties)) {
          $cust->setEndDate($properties['EndDate']);
      }
      
      if (array_key_exists('UnlockDate', $properties)) {
          $cust->setUnlockDate($properties['UnlockDate']);
      }
          
      if (array_key_exists('IdBookmark', $properties)) {
          $cust->setIdBookmark($properties['IdBookmark']);
      } 
      if (array_key_exists('State', $properties)) {
          $cust->setState($properties['State']);
      }
            
      if (array_key_exists('IdOrder', $properties)) {
          $cust->setIdOrder($properties['IdOrder']);
      };
      if (array_key_exists('Quantity', $properties)) {
          $cust->setQuantity($properties['Quantity']);
      };
      if (array_key_exists('Latitude', $properties)) {
          $cust->setLatitude($properties['Latitude']);
      };
      if (array_key_exists('Longitude', $properties)) {
          $cust->setLongitude($properties['Longitude']);
      };
      if (array_key_exists('Link', $properties)) {
          $cust->setLink($properties['Link']);
      };
      if (array_key_exists('Description', $properties)) {
          $cust->setDescription($properties['Description']);
      };
      if (array_key_exists('RegionTitle', $properties)) {
          $cust->setRegionTitle($properties['RegionTitle']);
      };          
      if (array_key_exists(IdDroper, $properties)) {
          $cust->setIdDroper($properties['IdDroper']);
      };             
      if (array_key_exists('CustomPrice', $properties)) {
          $cust->setCustomPrice($properties['CustomPrice']);
      };
      if (array_key_exists('AdvertiseTitle', $properties)) {
          $cust->setAdvertiseTitle($properties['AdvertiseTitle']);
      };
             
        
        
        
	}

	private static function createDateTime($input) {
		return DateTime::createFromFormat('Y-n-j H:i:s', $input);
	}

}

  
  
?>

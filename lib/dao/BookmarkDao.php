<?php
	namespace BtcRelax;
	
	use \PDO;
	use BtcRelax\Config;
	use BtcRelax\BaseDao;
	use BtcRelax\Dao\BookmarkSearchCriteria;
	use BtcRelax\NotFoundException;    	
	use BtcRelax\Model\Bookmark;
	use BtcRelax\Mapping\BookmarkMapper;
	 
	final class BookmarkDao extends \BtcRelax\BaseDao
	{
		public function find(BookmarkSearchCriteria $search = null) 
		{
			$result = [];
			$cnt = 0;
			foreach ($this->query($this->getFindSql($search)) as $row) {
				$bookmark = new Bookmark();
				BookmarkMapper::map($bookmark, $row);
				$cnt = $cnt + 1;  
				$result[$cnt] = $bookmark;
			}
			return $result;
		}
		
                private function getFindSql(BookmarkSearchCriteria $search = null) {
			   //TODO: Revork for bookmark entity  
				
				$sql = 'SELECT IdBookmark, CreateDate, IdOrder, Quantity, EndDate,Latitude, Longitude, Link, Description, RegionTitle,  '
                                        . 'CustomPrice, PriceCurrency , AdvertiseTitle, UnlockDate  , State, IdDroper , TargetAddress , BookmarkHash  '
                                        . ' FROM vwBookmarks ';
					$orderBy = ' CreateDate  ';
                                        $filter = '';
					if ($search !== null) { 
                                                if (!empty($search->getOrderId()))
                                                {
                                                    $filter = $this->addToFilter( $filter , sprintf (' IdOrder = %d', $search->getOrderId() ));
                                                }
						if ($search->getStatus() !== null) {
							//$sql .= 'AND State = ' . $this->getDb()->quote($search->getStatus());
							switch ($search->getStatus()) {
								
								case Bookmark::STATUS_PREPARING:
									$where = 'Preparing';
									break;
								case Bookmark::STATUS_CHECKING:
									$where = 'Checking';
									break;
								case Bookmark::STATUS_PUBLISHED:
									$where = 'Published';
                                                                        break;
								default:
									throw new NotFoundException('No order for status: ' . $search->getStatus());
							}
                                                        $filter = $this->addToFilter($filter, sprintf('State = \'%s\'', $where ));
                                                }
                                                
					}
					$sql .= sprintf('%s ORDER BY %s',$filter , $orderBy);  
                                        $msg = sprintf('Final query generated by BookmarkDao is: %s',$sql );
                                        LOG::general($msg, LOG::INFO);
					return $sql;
			}
		
		public function createNew(\BtcRelax\Model\Bookmark $bookmark)
                {
                    $result = false;
                        try {
                             $db = $this->getDb();
                             $callQuery = 'CALL `RegisterNewPoint`(:pIdCustomer, :pLatitude, :pLongitude, :pLink , :pDescription, '
                                     . ':pRegionTitle,:pPrice,:pAdvertiseTitle,@out_id,@pBookMarkId);';
                             $call = $db->prepare($callQuery);
                             
                             $vIdDroper = $bookmark->getIdDroper();
                             $vLat = $bookmark->getLatitude();
                             $vLon = $bookmark->getLongitude();
                             $vLink = $bookmark->getLink();
                             $vDescr = $bookmark->getDescription();
                             $vRegTitle = $bookmark->getRegionTitle();
                             $vPrice = $bookmark->getCustomPrice();
                             $vAdTitle = $bookmark->getAdvertiseTitle();
                            
                             $call->bindParam(':pIdCustomer',$vIdDroper ,PDO::PARAM_STR);
                             $call->bindParam(':pLatitude', $vLat ,PDO::PARAM_STR);
                             $call->bindParam(':pLongitude',$vLon ,PDO::PARAM_STR);
                             $call->bindParam(':pLink',$vLink  ,PDO::PARAM_STR);
                             $call->bindParam(':pDescription', $vDescr ,PDO::PARAM_STR);
                             $call->bindParam(':pRegionTitle',$vRegTitle ,PDO::PARAM_STR);
                             $call->bindParam(':pPrice',$vPrice,PDO::PARAM_STR);
                             $call->bindParam(':pAdvertiseTitle', $vAdTitle ,PDO::PARAM_STR);
                             Log::general(sprintf("'CALL `RegisterNewPoint`(%s, %s, %s, %s , %s, %s,%s,%s,@out_id,@pBookMarkId)", $vIdDroper , $vLat ,$vLon,
                                     $vLink, $vDescr, $vRegTitle , $vPrice, $vAdTitle), Log::DEBUG);
                             $call->execute();
                            $select = $db->query("SELECT  @out_id, @pBookMarkId");
                            $selResult = $select->fetch(PDO::FETCH_ASSOC);
                             if ($selResult)
                             {
                                    $pResultId    = $selResult['@out_id'];
                                    $pBookMarkId = $selResult['@pBookMarkId'];      
                                    if ($pResultId == 0)
                                    {
                                        $savedPoint = $this->findById($pBookMarkId);
                                        $result = $savedPoint;
                                    }
                                    else
                                    {
                                        $errorMsg = sprintf("Error registering order:%s Procedure result:%s", $newOrder , $pResultId);
                                        $newOrder->setLastError($errorMsg);
                                        Log::general($errorMsg, Log::WARN ); 
                                    }
                             }
                        }
                        catch (PDOException $pe) {
                                 LOG::general($pe->getMessage(), LOG::ERROR ); 
                         }
                     return $result; 
                    
                }
                        
		public function findById($id) {
			$query = sprintf("SELECT IdBookmark, CreateDate, IdOrder, Quantity, EndDate,"
                                . "Latitude, Longitude, Link, Description, RegionTitle,  CustomPrice, PriceCurrency , AdvertiseTitle, UnlockDate  ,"
                                . "State, IdDroper, TargetAddress, BookmarkHash "
                                . " FROM vwBookmarks WHERE idBookmark = '%s' LIMIT 1 ", $id);
			LOG::general(sprintf("Result query:%s",$query ),LOG::INFO);			
			$row = parent::query($query)->fetch();
			if (!$row) {
				LOG::general("Searched bookmark id, not found!",LOG::ERROR);
				return null;
			}
			$bookmark = new Bookmark();
			BookmarkMapper::map($bookmark, $row);
			return $bookmark;
		}
		
                public function setBookmarkSaled($pIdOrder, $pIdBookmark)
                {
                    $result = false;
                        try {
                             $db = $this->getDb();
                             $callQuery = 'select `setBookmarkSaled`(:pIdBookmark , :pIdOrder)';
                             $call = $db->prepare($callQuery);
                             $call->bindParam(':pIdBookmark',$pIdBookmark ,PDO::PARAM_INT);
                             $call->bindParam(':pIdOrder',$pIdOrder ,PDO::PARAM_INT);
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
?>

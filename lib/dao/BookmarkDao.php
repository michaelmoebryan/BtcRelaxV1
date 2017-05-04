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
		
		
		public function findById($id) {
			$row = parent::query(sprintf("SELECT IdBookmark, CreateDate, IdOrder, Quantity, EndDate,"
                                . "Latitude, Longitude, Link, Description, RegionTitle,  CustomPrice, PriceCurrency , AdvertiseTitle, UnlockDate  ,"
                                . "State, IdDroper, TargetAddress, BookmarkHash "
                                . " FROM vwBookmarks WHERE idBookmark = '%s' LIMIT 1 ", $id))->fetch();
			if (!$row) {
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

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
                                        . 'CustomPrice, PriceCurrency , AdvertiseTitle, UnlockDate  , State, IdDroper    '
                                        . ' FROM Bookmarks WHERE EndDate IS NULL ';
					$orderBy = ' CreateDate  ';
					if ($search !== null) {
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
                                                        $sql .= sprintf('AND State = \'%s\'', $where );
						}
					}
					$sql .= ' ORDER BY ' . $orderBy;     
					return $sql;
			}
		
		
		public function findById($id) {
			$row = parent::query(sprintf("SELECT IdBookmark, CreateDate, IdOrder, Quantity, EndDate,Latitude, Longitude, Link, Description, RegionTitle,  CustomPrice, PriceCurrency , AdvertiseTitle, UnlockDate  , State, IdDroper    "
                                . " FROM Bookmarks WHERE idBookmark = '%s' LIMIT 1 ", $id))->fetch();
			if (!$row) {
				return null;
			}
			$bookmark = new Bookmark();
			BookmarkMapper::map($bookmark, $row);
			return $bookmark;
		}
		
	} 
?>

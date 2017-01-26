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
				
				$sql = 'SELECT  OpenFrom, EndDate, UnlockDate, IdBookmark, State, CreateDate, IdOrder, Quantity, Latitude, Longitude, 
					Link, Description, RegionTitle, IdDroper, CustomPrice, AdvertiseTitle FROM Bookmarks WHERE EndDate IS NULL ';
					$orderBy = ' OpenFrom  ';
					if ($search !== null) {
						if ($search->getStatus() !== null) {
							$sql .= 'AND State = ' . $this->getDb()->quote($search->getStatus());
							switch ($search->getStatus()) {
								
								case Bookmark::STATUS_PREPARING:
									$orderBy = 'due_on, priority';
									break;
								case Bookmark::STATUS_CHECKING:
									$orderBy = 'due_on DESC, priority';
									break;
								case Bookmark::STATUS_PUBLISHED:
									break;
								default:
									throw new NotFoundException('No order for status: ' . $search->getStatus());
							}
						}
					}
					$sql .= ' ORDER BY ' . $orderBy;     
					return $sql;
			}
		
		
		public function findById($id) {
			$row = parent::query(sprintf("SELECT OpenFrom, EndDate, UnlockDate, IdBookmark, State, CreateDate, IdOrder, Quantity, Latitude, Longitude, 
					Link, Description, RegionTitle, IdDroper, CustomPrice, AdvertiseTitle FROM Bookmarks WHERE idBookmark = '%s' LIMIT 1 ", $id))->fetch();
			if (!$row) {
				return null;
			}
			$bookmark = new Bookmark();
			BookmarkMapper::map($bookmark, $row);
			return $bookmark;
		}
		
	} 
?>

<?php
		 require_once 'ganja_core.php';
		 require_once 'ganja_regions.php';
		 require_once dirname(__FILE__) . "/config.php";
		 /* Global business objects */
		 
		 class BookmarkFactory 
		 {
			 
			 public static function GetBookmarksForOrder($orderId)
				{
					/*result = array();
					$this->_mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
					$qresult = $this->_mysqli->query(sprintf("SELECT `Bookmarks`.`idBookmark`
							FROM `Bookmarks` WHERE `idOrder` = '%s'", $orderId ));
						if ($stmt = $dbCon->prepare($query)) {
							$stmt->bind_param('i', $orderId );
							$stmt->execute();
							$rows_cnt = $stmt->num_rows; 
							$stmt->bind_result($vIdBookmark);
								while ($stmt->fetch()) {
									$newBookMark = BookmarkFactory::Create($vIdBookmark);
									if (isset($newBookMark))
									{
									   array_push($result,$newBookMark); 
									}                                    
								}
							};
							$stmt->close();    

					return $result; */                   
				}
				
			public static function GetActiveBookmarks()
			{
				$result = array();
				$_mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
				$qresult = $_mysqli->query("SELECT `ActiveBookmarks`.`idBookmark` FROM `ActiveBookmarks`" );
				if ($qresult)
				{
					while ($row = $qresult->fetch_assoc())
					{
						$newBookMark = BookmarkFactory::Create($row['idBookmark']);
						if (isset($newBookMark))
							{
								array_push($result,$newBookMark); 
							} 
					}
				}
				return $result;
			}            
		
			public static function create($idBookmark)
			{
				$result = new Bookmark ($idBookmark); 
						if ($result->pIdBookmark > 0)
						{
							return $result;                    
						}
						else
							{
								$errorMsg = 'Error.Bookmark Id:' . $idBookmark . 'Not found!' ; 
								error_log($errorMsg);
								return $errorMsg ;  									
							}                
			}
		 }
		
		 class Bookmark {
			 public $pIdBookmark = null;
			 private $pCreateDate;
			 private $pIdOrder;
			 public  $pQuantity;
			 private $pEndDate;
			 private $pLatitude;
			 private $pLongitude;
			 public  $pLink;
			 private $pDescription;
			 public $pRegionTitle;
			 public $pRegionTitle_ru;
			 private $pLocationLink = null;
			 public  $pIsCatched; 
			 public $pIdDroper;
			 private $pUnlock_date;
			 private $pCustomPrice;
			 private $pOpenFrom;
			 private $pAdvertiseTitle;
			 private $pIsPublished;
			 private $pFault_date;
			 private $pFault_descr;
			 private $pSelectedRegion;

			 
			public function __construct($idBookmark) 
			  {
				if ($idBookmark !== null)
				{
					try
					  {
					  if (GetDbConnector($dbCon))
						{
							$query = "SELECT `Bookmarks`.`idBookmark`,
											`Bookmarks`.`CreateDate`,
											`Bookmarks`.`idOrder`,
											`Bookmarks`.`Quantity`,
											`Bookmarks`.`EndDate`,
											`Bookmarks`.`Latitude`,
											`Bookmarks`.`Longitude`,
											`Bookmarks`.`Link`,
											`Bookmarks`.`Description`,
											`Bookmarks`.`RegionTitle`,
											`Bookmarks`.`RegionTitle_ru`,
											`Bookmarks`.`LocationLink`,
											`Bookmarks`.`isCatched`,
											`Bookmarks`.`idDroper`,
											`Bookmarks`.`CustomPrice`,
											`Bookmarks`.`OpenFrom`,
											`Bookmarks`.`AdvertiseTitle`,
											`Bookmarks`.`isPublished`,
											`Bookmarks`.`unlock_date`,
											`Bookmarks`.`fault_date`,
											`Bookmarks`.`fault_descr`
										FROM `Bookmarks` 
										WHERE idBookmark = ?" ;
							if ($stmt = $dbCon->prepare($query)) {
								$stmt->bind_param('i', $idBookmark );
								$stmt->execute();
								$rows_cnt = $stmt->num_rows; 
								if ($rows_cnt = 1)
								{
									$stmt->bind_result($idBookmark, $CreateDate,
									$idOrder, $Quantity,
									$EndDate, $Latitude , $Longitude , $Link,
									$Description, $RegionTitle,
									$RegionTitle_ru, $LocationLink, $IsCatched,
									$idDroper, $CustomPrice , $OpenFrom , $AdvertiseTitle  ,$isPublished, $unlock_date, $fault_date, $fault_descr);
									while ($stmt->fetch()) {
										  $this->pIdBookmark = $idBookmark;
										  $this->pCreateDate = $CreateDate;
										  $this->pIdOrder = $idOrder;
										  $this->pQuantity = $Quantity;
										  $this->pEndDate = $EndDate;
										  $this->pLatitude = $Latitude;
										  $this->pLongitude = $Longitude;
										  $this->pLink = $Link;
										  $this->pDescription = $Description;
										  $this->pRegionTitle = $RegionTitle;
										  $this->pRegionTitle_ru = $RegionTitle_ru;
										  $this->pLocationLink = $LocationLink;
										  $this->pIsCatched = $IsCatched;
										  $this->pIdDroper = $idDroper;
										  $this->pCustomPrice = $CustomPrice;
										  $this->pOpenFrom = $OpenFrom;
										  $this->pAdvertiseTitle = $AdvertiseTitle;
										  $this->pIsPublished = $isPublished;
										  $this->pUnlock_date = $unlock_date;
										  $this->pFault_date  = $fault_date;
										  $this->pFault_descr = $fault_descr;
									};
								};
								$stmt->close();
								//wf_log("Order ".$orderId." initialized.", 'INFO') ;
							};
						};
					  }
					  catch (Exception $e )
					  {
						   error_log('Error while creating bookmark object. Error: ' . $e->getMessage());
					  };					
				}; 
			 }

			public function GetPublicForm($plang = "uk")
			 {
				$result = null;
				switch ($plang) {
				case 'ru':
					$lOrderQuantity = 'Заказано:';
					$lLink = 'Купить';
					$lTitle = $this->pRegionTitle_ru; 
					$lAdverTitle = $this->pAdvertiseTitle;                    
					break;
				default:
					$lOrderQuantity = 'Замовлено:';
					$lLink = 'Придбать';
					$lTitle = $this->pRegionTitle;
					$lAdverTitle = $this->pAdvertiseTitle;
					
				};
				
				// TODO: Implement getting local price 
				$lLocalPrice = $this->pCustomPrice;
				//
				
/*				$result = '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3"><ul class="list-group storefronItem">
							<li class="list-group-item list-group-item-info"><p>' . $lAdverTitle . '</p><p><span class="badge">' . 
							$lTitle . '</span></p></li><li class="list-group-item list-group-item-warning ">~' . $lLocalPrice . 
							' UAH<button type="submit" name="getBookmark" value="' . $this->pIdBookmark . '"   class="btn btn-danger btn-xs pull-right">' . $lLink . '</button></li></ul></div>';*/
				$result = sprintf("<div class=\"col-xs-12 col-sm-6 col-md-4 col-lg-3\"><ul class=\"list-group storefronItem\"><li class=\"list-group-item list-group-item-info\"><p>%s</p><p><span class=\"badge\">%s</span></p></li><li class=\"list-group-item list-group-item-warning \">~%s UAH<button type=\"submit\" name=\"getBookmark\" value=\"%s\"   class=\"btn btn-danger btn-xs pull-right\">%s</button></li></ul></div>", $lAdverTitle, $lTitle, $lLocalPrice, $this->pIdBookmark,$lLink  );
				return $result;			
			 }
	   
			public function GetFormResult($plang = "uk")
			 {
				$result = null;
				switch ($plang) {
				case 'ru':
					$lOrderQuantity = 'Заказано:';
					$lQuanity = 'Количество папир в кладе: ';
					$lLink = 'Фото';
					$lLocation = 'Карта';
					$lCatch = 'Забрал';
					$lCatchDone = 'Принято';
					break;
				default:
					$lOrderQuantity = 'Замовлено:';
					$lQuanity = 'Кількість папір в закладинці: ';
					$lLink = 'Фото';
					$lLocation = 'Мапа';
					$lCatch = 'Забрав';
					$lCatchDone = 'Прийнято';
				};
				
				$catchBtn = '';
				 
				 if ($this->pIsCatched)
						{
						  $catchBtn = '
						  <img src="img/catch_done.png" border="0" width="64" height="64" alt="Catch bookmark done" >
						  <p>' . $lCatchDone . '</p>';                                                         
						}
						else
						{
						  $catchBtn = '<button class="pointCatch" align="right" onclick="setPointCatched(' . $this->pIdBookmark .  ',' . $this->pIdOrder . ');">
							<img src="img/Catched1.png" border="0" width="64" height="64" alt="Catch bookmark" >
							<p>' . $lCatch . '</p>                                                      
						  </button>' ;
						  /*                          $catchBtn = '<a  class="pointCatch" ><img src="img/Catched1.png" border="0" width="64" height="64" alt="Catch bookmark" align="right">
							<p>' . $lCatch . '</p>
						  </a>';
						  */                                                      
						};                  
				$result = $result . 
				'<div class="pointInfo">
				<table  width="100%" >
				   <tr>
					  <td width="60%">
						 ' . $this->pDescription . '
					  </td>
					  <td>
						<a  target="_blank" class="pointLocation" href="' . $this->getLocationLink() . '"><img src="img/Google_Maps_Icon.png" border="0" width="64" height="64" alt="Look map" align="right">
						<p>' . $lLocation . '</p>
						</a>
					  </td>
					  <td>                  
						<a  target="_blank" class="pointInfoLink" href="' . $this->pLink . '"><img src="img/Search-Images-icon.png" border="0" width="64" height="64" alt="Look map" align="right">
							<p>' . $lLink . '</p>
						</a>
					  </td>
					  <td>' . $catchBtn  . '</td>
				   </tr>
				   <tr>
					 <td colspan="4">
					   <p class="pointInfoBottom">' . $lQuanity . '<font size="5" color="red">' . $this->pQuantity . '</font></p>  
					 </td>
				   </tr>
				</table>


				</div>';
				return $result;  
			 }
		 
			public function getLocationLink()
			{
				if (empty($this->pLocationLink))
				{
					if (isset($this->pLatitude) && isset($this->pLongitude))
					{
						if (is_numeric($this->pLatitude) && is_numeric($this->pLongitude))
						{
							return  'http://maps.google.com/maps?f=q&q=loc:' .$this->pLatitude .  ',' . $this->pLongitude . '&t=k&spn=0.5,0.5';
						};
					};				
				}
				else {
					return $this->pLocationLink;
				};
			}
			
			public function getDescription()
			{
				return $this->pDescription;
			}
  
			public function getResourceLink()
			{
				return $this->pLink;
			}
			
			public function getQuantity()
			{
				return $this->pQuantity;
			}
			
			public function getLatitude()
			{
				return $this->pLatitude;
			}
			
			public function getLongitude()
			{
				return $this->pLongitude;
			}
			
			public function setResourceLink($pLink)
			{
				$this->pLink = $pLink  ;
			}
			
			public function setDroperId($pDroperId)
			{
				$this->pIdDroper =  $pDroperId  ;
			}
			
			public function setRegionNames($pRegionTitleUkr, $pRegionTitleRus)
			{
				$this->pRegionTitle =  $pRegionTitleUkr  ;
				$this->pRegionTitle_ru =  $pRegionTitleRus  ;
			}
  
			public function setDescription($pDescr)
			{
				$this->pDescription = $pDescr;
			}
  
			public function setLocation($pLatitude, $pLongitude  )
			{
				$this->pLatitude = $pLatitude;
				$this->pLongitude = $pLongitude;
			}

			public function setRegion($selectedRegion)
			{
				$curReg = Region::getRegionList();
				$regionInfo = $curReg[$selectedRegion];
				$this->pRegionTitle  = $regionInfo['TitleUkr'] ;
				$this->pRegionTitle_ru  = $regionInfo['TitleRus'] ;				
			}

			public function saveToDb()
			{
				$result = '<div id="dialog-message" class="modalDialog" style="display: none;">        								
											<div>
												<h2>Point need to update!</h2>
												<button id="closeMessage" class="btn_msg" onclick="CloseMessage();">Ok</button>								        
												</div>
											</div>';
				

			
					// Need to insert, else update
					try
					{
								GetDbConnector($dbCon);
								$query = 'INSERT INTO `Bookmarks` 
								( `CreateDate`, `Quantity`, `Link`, `Description`, 
									`RegionTitle`, `RegionTitle_ru`, `LocationLink`, `idDroper`, `Latitude`, `Longitude` ) 
									VALUES (NOW(), ?, ?, ?, ?,? ,? ,?, ?, ?);';
								if ($stmt = $dbCon->prepare($query))
								{
								/* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
									$curUser = 	$_SESSION["userCurrent"];
									$usrMail = $curUser->getId();
									
									
									$stmt->bind_param('issssssdd', $this->pQuantity , $this->getResourceLink(), $this->getDescription(), 
									$this->pRegionTitle , $this->pRegionTitle_ru, $this->getLocationLink()  , 
									$usrMail , $this->pLatitude, $this->pLongitude);
									$stmt->execute();	
									$this->pIdBookmark = $dbCon->insert_id;
								
									$result = '<div id="dialog-message" class="modalDialog" style="display: none;">        								
											<div>
												<h2>Point added!</h2>
												<p>New point Id:' . $dbCon->insert_id . '</p>
												<button id="closeMessage" class="btn_msg" onclick="CloseMessage();">Ok</button>								        
												</div>
											</div>';
								};		
												
					} 
					catch (Exception $e)
					{
						error_log('Error while inserting new bookmark:' . $e->getMessage());
						$result = '<div id="dialog-message" class="modalDialog" style="display: none;">        								
											<div>
												<h2>Point adding error!</h2>
												<p>Message: '. $e->getMessage(). '</p>
												<button id="closeMessage" class="btn_msg" onclick="CloseMessage();">Ok</button>								        
											</div>
									 </div>';
					};
				
				return ($result);
				
				
			}

		 }
?>




<?php

namespace BtcRelax\Model;

class Bookmark {

    const STATUS_PREPARING = "Preparing";
    const STATUS_CHECKING = "Checking";
    const STATUS_REJECTED = "Rejected";
    const STATUS_READY = "Ready";
    const STATUS_PUBLISHED = "Published";
    const STATUS_SALED = "Saled";
    const STATUS_LOST = "Lost";
    const STATUS_PREORDERED = "PreOrdered";

    private $pIdBookmark;
    private $pCreateDate;
    private $pIdOrder;
    private $pQuantity;
    private $pEndDate;
    private $pLatitude;
    private $pLongitude;
    private $pLink;
    private $pDescription;
    private $pRegionTitle;
    private $pCustomPrice;
    private $pPriceCurrency;
    private $pAdvertiseTitle;
    private $pUnlockDate;
    private $pState = self::STATUS_PREPARING;
    private $pIdDroper;
    private $pTargetAddress;
    private $pBookmarkHash;
    
    public function __construct() {
        $this->pState = self::STATUS_PREPARING;
        $this->pIdBookmark = null;
        $this->pDescription = "";
    }
    
    public function getTargetAddress() {
        return $this->pTargetAddress;
    }

    public function setTargetAddress($pTargetAddress) {
        $this->pTargetAddress = $pTargetAddress;
    }

    public function getBookmarkHash() {
        return $this->pBookmarkHash;
    }

    function setBookmarkHash($pBookmarkHash) {
        $this->pBookmarkHash = $pBookmarkHash;
    }

        
    public function getIdBookmark() {
        return $this->pIdBookmark;
    }

    function setIdBookmark($pValue) {
        $this->pIdBookmark = $pValue;
    }

    function getState() {
        return $this->pState;
    }

    function setState($pValue) {
        $this->pState = $pValue;
    }

    function setQuantity($pValue) {
        $this->pQuantity = $pValue;
    }

    function getCreateDate() {
        return $this->pCreateDate;
    }

    function setCreateDate($pValue) {
        $this->CreateDate = $pValue;
    }

    function getIdOrder() {
        return $this->pIdOrder;
    }

    function setIdOrder($pValue) {
        $this->pIdOrder = $pValue;
    }

    function getLatitude() {
        return $this->pLatitude;
    }

    function setLatitude($pValue) {
        $this->pLatitude = $pValue;
    }

    function getLongitude() {
        return $this->pLongitude;
    }

    function setLongitude($pValue) {
        $this->pLongitude = $pValue;
    }

    function getLink() {
        return $this->pLink;
    }

    function setLink($pValue) {
        $this->pLink = $pValue;
    }

    function getDescription() {
        return $this->pDescription;
    }

    function setDescription($pValue) {
        $this->pDescription = $pValue;
    }

    function getRegionTitle() {
        return $this->pRegionTitle;
    }

    function setRegionTitle($pValue) {
        $this->pRegionTitle = $pValue;
    }

    function getIdDroper() {
        return $this->pIdDroper;
    }

    function setIdDroper($pValue) {
        $this->pIdDroper = $pValue;
    }

    function getCustomPrice() {
        return $this->pCustomPrice;
    }

    function setCustomPrice($pValue) {
        $this->pCustomPrice = $pValue;
    }
    
    function getPriceCurrency() {
        return $this->pPriceCurrency;
    }

    function setPriceCurrency($pPriceCurrency) {
        $this->pPriceCurrency = $pPriceCurrency;
    }

    function getAdvertiseTitle() {
        return $this->pAdvertiseTitle;
    }

    function setAdvertiseTitle($pValue) {
        $this->pAdvertiseTitle = $pValue;
    }

    function getUnlockDate() {
        return $this->UnlockDate;
    }

    function setUnlockDate($pValue) {
        $this->UnlockDate = $pValue;
    }

    function getEndDate() {
        return $this->EndDate;
    }

    function setEndDate($pValue) {
        $this->EndDate = $pValue;
    }

    public static function allStatuses() {
        return [
            self::STATUS_PREPARING,
            self::STATUS_CHECKING,
            self::STATUS_REJECTED,
            self::STATUS_READY,
            self::STATUS_PUBLISHED,
            self::STATUS_SALED,
            self::STATUS_LOST
        ];
    }

    public function GetPublicForm($plang = "uk") {
        $result = null;
        switch ($plang) {
            case 'ru':
                $lOrderQuantity = 'Заказано:';
                $lLink = 'Купить';
                $lTitle = $this->pRegionTitle;
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

        /* 				$result = '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3"><ul class="list-group storefronItem">
          <li class="list-group-item list-group-item-info"><p>' . $lAdverTitle . '</p><p><span class="badge">' .
          $lTitle . '</span></p></li><li class="list-group-item list-group-item-warning ">~' . $lLocalPrice .
          ' UAH<button type="submit" name="getBookmark" value="' . $this->pIdBookmark . '"   class="btn btn-danger btn-xs pull-right">' . $lLink . '</button></li></ul></div>'; */
        $result = sprintf("<div class=\"col-xs-12 col-sm-6 col-md-4 col-lg-3\">
							<form id=\"frmBookmarkId%s\" method=\"post\" >
								<ul class=\"list-group storefronItem\">
									<li class=\"list-group-item list-group-item-info\">
										<p>%s</p>
										<p><span class=\"badge\">%s</span></p>
									</li>
									<li class=\"list-group-item list-group-item-warning \">~%s UAH
									<input type=\"hidden\"  />
                                                                        <button type=\"submit\" name=\"getBookmark\" value=\"%s\"   class=\"btn btn-danger btn-xs pull-right\">%s</button>
									</li>
								</ul>
							</form>
							</div>", $this->getBookmarkHash(), $lAdverTitle, $lTitle, $lLocalPrice, $this->getIdBookmark(), $lLink);
        return $result;
        
    }
			
    public function GetFormResult($plang = "uk") {
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

        if ($this->pIsCatched) {
            $catchBtn = '
						  <img src="img/catch_done.png" border="0" width="64" height="64" alt="Catch bookmark done" >
						  <p>' . $lCatchDone . '</p>';
        } else {
            $catchBtn = '<button class="pointCatch" align="right" onclick="setPointCatched(' . $this->pIdBookmark . ',' . $this->pIdOrder . ');">
							<img src="img/Catched1.png" border="0" width="64" height="64" alt="Catch bookmark" >
							<p>' . $lCatch . '</p>                                                      
						  </button>';
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
					  <td>' . $catchBtn . '</td>
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

    public function getPrivateForm()
    {
        $plang = 'ru';
        $result = null;
        switch ($plang) {
            case 'ru':
                $lOrderQuantity = 'Заказано:';
                $lLink = 'Фото';
                $lLocation = 'Карта';
                $lCatch = 'Забрал';
                $lCatchDone = 'Принято';
                break;
            default:
                $lOrderQuantity = 'Замовлено:';
                $lLink = 'Фото';
                $lLocation = 'Мапа';
                $lCatch = 'Забрав';
                $lCatchDone = 'Прийнято';
        };
        if ($this->getState() === Bookmark::STATUS_SALED) {
            $catchBtn = '
						  <img src="img/catch_done.png" border="0" width="64" height="64" alt="Catch bookmark done" >
						  <p>' . $lCatchDone . '</p>';
        } else {
            $catchBtn = '<button type="submit" class="pointCatch" align="right" name="setPointCatched" value="'. $this->getIdBookmark() . '" >
							<img src="img/Catched1.png" border="0" width="64" height="64" alt="Catch bookmark" >
							<p>' . $lCatch . '</p>                                                      
						  </button>';
            };
        $result = $result .
                '<div class="pointInfo storefronItem">
                    <form  id="frmPointInfo" action="#"  method="post">>
				<table  width="80%" >
				   <tr>
					  <td width="60%">
						 ' . $this->getDescription() . '
					  </td>
					  <td>
						<a  target="_blank" class="pointLocation" href="' . $this->getLocationLink() . '"><img src="img/Google_Maps_Icon.png" border="0" width="64" height="64" alt="Look map" align="right">
						<p>' . $lLocation . '</p>
						</a>
					  </td>
					  <td>                  
						<a  target="_blank" class="pointInfoLink" href="' . $this->getLink(). '"><img src="img/Search-Images-icon.png" border="0" width="64" height="64" alt="Look map" align="right">
							<p>' . $lLink . '</p>
						</a>
					  </td>
					  <td>' . $catchBtn . '</td>
				   </tr>
				   <tr>
					 <td colspan="4">
					   <p class="pointInfoBottom"><font size="5" color="red">' . $this->getAdvertiseTitle() . '</font></p>  
					 </td>
				   </tr>
				</table>

                    </form>
                </div>';
        return $result;
    }
    
    public function getLocationLink() {
        if (empty($this->pLocationLink)) {
            if (isset($this->pLatitude) && isset($this->pLongitude)) {
                if (is_numeric($this->pLatitude) && is_numeric($this->pLongitude)) {
                    return 'http://maps.google.com/maps?f=q&q=loc:' . $this->pLatitude . ',' . $this->pLongitude . '&t=k&spn=0.5,0.5';
                };
            };
        } else {
            return $this->pLocationLink;
        };
    }

    public function getResourceLink() {
        return $this->pLink;
    }

    public function getQuantity() {
        return $this->pQuantity;
    }

    //public function setResourceLink($pLink) {
    //    $this->pLink = $pLink;
    //}

    public function setDroperId($pDroperId) {
        $this->pIdDroper = $pDroperId;
    }

    public function setRegionNames($pRegionTitleUkr, $pRegionTitleRus) {
        $this->pRegionTitle = $pRegionTitleUkr;
        $this->pRegionTitle_ru = $pRegionTitleRus;
    }

    public function setLocation($pLatitude, $pLongitude) {
        $this->pLatitude = $pLatitude;
        $this->pLongitude = $pLongitude;
    }

    public function setRegion($selectedRegion) {
        $curReg = Region::getRegionList();
        $regionInfo = $curReg[$selectedRegion];
        $this->pRegionTitle = $regionInfo['TitleUkr'];
        $this->pRegionTitle_ru = $regionInfo['TitleRus'];
    }

    public function saveToDb() {
        $result = false;
        if ($this->pIdBookmark === null)
        {
            $dao = \BtcRelax\BookmarkDao();
            $daoRes = $dao->createNew($this);
            if ($daoRes instanceof \BtcRelax\Model\Bookmark)
            {
                $result = $daoRes;
            }
            // Need to insert, else update
        }
        return ($result);
    }

}

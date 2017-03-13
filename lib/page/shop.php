<?php
namespace BtcRelax;

use BtcRelax\BookmarkDao;
use BtcRelax\Utils;
use BtcRelax\Model\Bookmark;
use BtcRelax\Dao\BookmarkSearchCriteria;
use BtcRelax\Validation\BookmarkValidator;

//$status = Utils::getUrlParam('status');
//BookmarkValidator::validateStatus($status);
global $core;

if ($core->isAuthenticated() == false)
{
	Utils::Redirect('main');
}
else
{
	$cust = $core->getCustomer();
	if (isset($cust))
		{
			$header = new \BtcRelax\LayoutHeader($cust);
			$dao = new BookmarkDao();
			//$search = (new BookmarkSearchCriteria())->setStatus($status);

			$search = new BookmarkSearchCriteria();
			$search->setStatus(Bookmark::STATUS_PUBLISHED);
			// data for template
			//$title = Utils::capitalize($status) . ' StoreFront';
			$bookmarks = $dao->find($search);   
		};    

};   
?>

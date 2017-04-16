<?php
  namespace BtcRelax;
  use BtcRelax\Model\Customer;
  use BtcRelax\Model\Bookmark;
  
  final class LayoutHeader
	{

		const HEADER_BEGIN = '<div class="collapse navbar-collapse">
		  <ul class="nav navbar-nav">';
		const HEADER_END = '</ul></div>';
                const I_PREFIX = '/img/';
		
		private $headerHTML = null;
		private $SiteMap;
		
		
		public function __construct($customer) 
		{
			if ($customer !== null)
			{
				$this->SiteMap = array
				  (
				  array("user",Self::I_PREFIX. "/icons/32/wait.gif"),
				  array("info",Self::I_PREFIX. "/icons/32/info.gif")
				  );
			}
		} 
		
		public function GetHeaderHtml($page)
		{
			$result = null;
			if (isset($this->SiteMap))
			{
				foreach ($this->SiteMap as $pageHead) {				  
					  if ( $pageHead[0] === $page)
						  {
							        ///
                                    ///
                                    ///
                                    ///
                                    ///
                                    ///
                              $result = $result . '<li><a class="active" href="' . Utils::createLink($pageHead[0]) . '"><img src="' . $pageHead[1] . '"></img></a></li>';                               
                              //$result = $result . '<li><a class="active" href="' . Utils::createLink($pageHead[0]) . '">' . $pageHead[1] . '</a></li>';
						  }
						  else
						  {
                              $result = $result . '<li><a href="' . Utils::createLink($pageHead[0]) . '"><img src="' . $pageHead[1] . '"></img></a></li>'; 
						      //$result = $result . '<li><a href="' . Utils::createLink($pageHead[0]) . '">' .$pageHead[1]  . '</a></li>'; 
						  }               
				};
				$result = Self::HEADER_BEGIN . $result  . Self::HEADER_END; 
		   };                						
		   return $result;
		}
	}
?>

<?php
  namespace BtcRelax\Model;
  
  

  class Customer {

		// private members
		private $m_CreateDate;
		private $m_idCustomer;
		private $m_isBaned;

		/**
		* Constructor
		*
		* Example:
		* $myCustomers = new Customers();
		*/
		public function __construct() {
			//--
		}

		/**
		* Constructor
		*
		* Example:
		* $myCustomers = Customers::WithParams( val1, val2,.. );
		*/
		public static function WithParams($CreateDate, $idCustomer, $isConfirmed, $isBaned) {
			$instance = new self();
			$instance->setCreateDate($CreateDate);
			$instance->setIdCustomer($idCustomer);
			$instance->setIsBaned($isBaned);
			return $instance;
		}

		/**
		* Getters and Setters
		*/

		public function getCreateDate() {
			return $this->m_CreateDate;
		}

		public function setCreateDate($CreateDate) {
			$this->m_CreateDate = $CreateDate;
		}

		public function getIdCustomer() {
			return $this->m_idCustomer;
		}

		public function setIdCustomer($idCustomer) {
			$this->m_idCustomer = $idCustomer;
		}

		public function getIsBaned() {
			return $this->m_isBaned;
		}

		public function setIsBaned($isBaned) {
			$this->m_isBaned = $isBaned;
		}

		public function isHasAccessToPage($page)
		{
			$result = false;
			if ($page == 'user' || $page == 'kill' || $page == 'shop')
			{
				$result = true;
			}
			return $result;
		}
		
		/**
		* Methods
		*/

		public function __toString() {
			return "Id:" . $this->m_idCustomer; 
		}

	}

?>

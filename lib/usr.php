<?php
	namespace BtcRelax;
	use BtcRelax\CustomerDao;
        
	class User {
		private $customer;
		private $xPub;
                private $InvoicesCount;
                
                public function __construct(){
                    $InvoicesCount = -1;
                }
                                
		public function init($user_id){
			$custDao=new CustomerDao();
			$this->customer=$custDao->findById($user_id);
			if($this->customer===null){
				throw new \LogicException('Critical error! Customer with Id "'.$user_id.'" was not found.');
			}
		}
                
                function getCustomer()
                {
                    return $this->customer;
                }
                
                function getXPub() {
                    return $this->xPub;
                }

                function getInvoicesCount() {
                    return $this->InvoicesCount;
                }

                function setXPub($xPub) {
                    $this->xPub = $xPub;
                }

                function setInvoicesCount($InvoicesCount) {
                    $this->InvoicesCount = $InvoicesCount;
                }
                              
		public function getUserHash(){
			$cId=$this->customer->getIdCustomer();
			$vEnd=substr($cId,-4,4);
			$vBegin=substr($cId,1,4);
			$uh=$vBegin.'*'.$vEnd;
			return $uh;
		}
                
                public function getCustomerId() {
                    return $this->customer->getIdCustomer();
                }
              
		public function RegisterNewUserId($id){
			$custDao=new CustomerDao();
			$result=$custDao->registerUserId($id);
			if($result){
				$this->init($id);
			}
			;
			return $result;
		}
	}
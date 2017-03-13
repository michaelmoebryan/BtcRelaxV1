<?php


namespace BtcRelax;
use BtcRelax\CustomerDao;

class User {
   
    private $customer;
      

    public function __construct() {
    }
    
    public function init($user_id)
    {
        $custDao = new CustomerDao();
        $this->customer = $custDao->findById($user_id);
        if ($this->customer === null)
        {
            throw new NotFoundException('Critical error! Customer with Id "' . $user_id . '" was not found.');
        }      
    }
    
    public function getUserHash()
    {
        $cId = $this->customer->getIdCustomer();
        $uh =  md5($cId);
        return $uh ;
    }
    
    public function RegisterNewUserId($id)
    {
        $custDao = new CustomerDao();
        $result  = $custDao->registerUserId($id);
        if ($result)
            {
                $this->init($id);
            };
        return $result;        
    }
    
    
    
}

<?php
namespace BtcRelax;

use BtcRelax\User;
use Exception;
use PDO;

require 'AMInterface.php';

class AM implements IAM {
    protected $rootUserId;
    protected $rootUser;
    
    
    public function __construct() { 
        try {
            if (!empty(rootUserId))
            {    
                $root_u = new User();
                $this->rootUser = $root_u->Init(rootUserId);
            }
        } 
        catch (Exception $ex) {
		throw new Exception('AM init error: ' . $ex->getMessage());
            };
    }
    
    
    public function CreateNewUser($parent, $child) {
        $result = false;
        if (($parent == null) && (isAllowFreeRegistration))
        {           
            $n_user = new \BtcRelax\User();
            $result = $n_user->RegisterNewUserId($child);
            
        };
        return $result;
    }

    public function getUserByBitId($userBitId) {
        $result = new user();
        return $result;
    }

    public function getUserById($customerId) {
        $result = new User();
        $result->init($customerId);
        $dao = new CustomerDao();
        $result->setXPub($dao->GetPubKeyByCustomer($customerId));
        $invoicesCount = $dao->GetInvoiceAddressCountByXPub($result->getXPub());
        $result->setInvoicesCount($invoicesCount);        
        return $result;
    }

    public function loginUserByToken($token) {
        $result = false;
        $dao = new CustomerDao();
        $customerId = $dao->getUserByToken($token);
        if (FALSE !== $customerId)
        {
            global $core;
            $user = $this->getUserById($customerId);
            $result = $core->setAuthenticate($user);
            if ($result === true)
            {
                /// TODO: add used count to $token
            }
        }
        return $result;
    }

}

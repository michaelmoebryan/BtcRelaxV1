<?php
namespace BtcRelax;

use BtcRelax\Config;
use BtcRelax\User;
use Exception;
use PDO;

require 'AMInterface.php';

class AM implements IAM {
    protected $root_user_id;
    protected $isAllowFreeRegistration;
    protected $rootUser;
    
    
    public function __construct($rootUserId,$isAFR) { 
        $this->root_user_id = $rootUserId;
        $this->isAllowFreeRegistration = $isAFR;

        try {
  
            if ($this->root_user_id !== "")
            {    
                $root_u = new User();
                $this->rootUser = $root_u->Init($this->root_user_id);
            }
        } 
        catch (Exception $ex) {
		throw new Exception('AM init error: ' . $ex->getMessage());
            };
    }
    
    
    public function CreateNewUser($parent, $child) {
        $result = false;
        if (($parent == null) && ($this->isAllowFreeRegistration))
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

    public function getUserById($userId) {
        $result = new user();
        return $result;
    }

}

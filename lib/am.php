<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BtcRelax;

use BtcRelax\Config;
use Exception;
use PDO;

/**
 * Description of am
 *
 * @author Chronos
 */
class AM implements IAM {
    private $root_user_id;
    private $isAllowFreeRegistration;
    
    public function __construct() {    
	$config = Config::getConfig('customize');
        try {
            $this->root_user_id = $config['HUB_ROOT'];
            $this->isAllowFreeRegistration = $config['IS_FREE_REGISTER'];
        } 
        catch (Exception $ex) {
		throw new Exception('AM init error: ' . $ex->getMessage());
            };
    }
    
    
    public function CreateUser($parent, $child) {
        $result = new user();
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

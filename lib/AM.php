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
                $this->rootUser = $root_u->Init(\rootUserId);
            }
        }
        catch (\LogicException $ex)
        {
                $vIsCreatedRoot = $this->createNewUser(null, rootUserId);
                if (FALSE!=$vIsCreatedRoot)
                {
                    throw new Exception('AM create user about error: ' . $ex->getMessage() );
                }
        }
        catch (Exception $ex) {
                throw new Exception('AM init error: ' . $ex->getMessage());
        }
    }
    
    
    public function createNewUser($parent, $child) {
        $result = false;
        if (($parent == null) && (isAllowFreeRegistration))
        {           
            $n_user = new \BtcRelax\User();
            $result = $n_user->RegisterNewUserId($child);
            
        }
        return $result;
    }

    public function getUserByBitId($userBitId) {
        $dao = new DAO();
        $idCustomer = $dao->customerByBitId($vBitId);
        $result = $this->getUserById($idCustomer);
        return $result;
    }

    public function getUserById($customerId) {
        $result = new User();
        $result->init($customerId);
        $dao = new CustomerDao();
        $this->fillUserInfo($result);
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
    
    private function fillUserInfo(\BtcRelax\User $pUser)
    {
        $dao = new CustomerDao();
        $vCustId = $pUser->getCustomerId();
        $vXPub = $pUser->getXPub();
        $pUser->setXPub($dao->GetPubKeyByCustomer($vCustId));
        $invoicesCount = $dao->GetInvoiceAddressCountByXPub($vXPub);
        $pUser->setInvoicesCount($invoicesCount);     
        return $pUser;
    }

}

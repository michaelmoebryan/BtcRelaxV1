<?php

namespace BtcRelax;

interface IAM {
    //put your code here
    public function CreateNewUser($parent, $child);

    public function getUserByBitId($userBitId);
    
    public function getUserById($userId);
    
    public function loginUserByToken($token);
}

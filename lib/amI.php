<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BtcRelax;

/**
 *
 * @author Chronos
 */
interface IAM {
    //put your code here
    public function CreateUser($parent, $child);

    public function getUserByBitId($userBitId);
    
    public function getUserById($userId);
}
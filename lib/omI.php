<?php
namespace BtcRelax;

interface IOM {

    public function CreateNewOrder($user, $storefrontItem);

    public function getOrderById($orderId);
    
    public function getOrdersByUser($userId, $onlyActive = fasle);
}

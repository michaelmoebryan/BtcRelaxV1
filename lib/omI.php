<?php
namespace BtcRelax;

interface IOM {

    public function CreateNewOrder(\BtcRelax\User $user, \BtcRelax\Model\Bookmark $bookmark);

    public function getOrderById($orderId);
    
    public function getOrdersByUser(\BtcRelax\User $user, $onlyActive = true);
}

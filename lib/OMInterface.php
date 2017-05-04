<?php
namespace BtcRelax;

interface IOM {

    public function createNewOrder(\BtcRelax\User $user, \BtcRelax\Model\Bookmark $bookmark);

    public function getOrderById($orderId);
    
    public function getOrdersByUser(\BtcRelax\User $user, $onlyActive = true);

    public function tryConfirmOrder(\BtcRelax\Model\Order $order);
    
    public function setPointCatched(\BtcRelax\Model\Order $order, $bookmarkId );
}

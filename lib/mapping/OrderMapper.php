<?php  namespace BtcRelax\Mapping;    use \DateTime;  use \BtcRelax\Model\Order;      final class OrderMapper {	private function __construct() {	}        public static function map(\BtcRelax\Model\Order $order, array $properties) {		if (array_key_exists('CreateDate', $properties)) {			$createdOn = self::createDateTime($properties['CreateDate']);			if ($createdOn)			{				$order->setCreateDate($createdOn);  			}    		};                if (array_key_exists('EndDate', $properties)) {                        $order->setEndDate($properties['EndDate']);                };                              if (array_key_exists('IdOrder', $properties)) {                    $order->IdOrder($properties['IdOrder']);                                    };                 if (array_key_exists('OrderState', $properties)) {                    $order->$pState($properties['OrderState']);                };                if (array_key_exists('idSaller', $properties)) {                    $order->setSaller($properties['idSaller']);                };                if (array_key_exists('BTCPrice', $properties)) {                    $order->setBTCPrice($properties['BTCPrice']);                };                if (array_key_exists('InvoiceAddress', $properties)) {                    $order->setInvoiceAddress($properties['InvoiceAddress']);                };        }                private static function createDateTime($input) {		return DateTime::createFromFormat('Y-n-j H:i:s', $input);        }  }
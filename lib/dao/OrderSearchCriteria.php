<?php namespace BtcRelax\Dao;  final class OrderSearchCriteria {	private $status = null;        private $customerId = null;        private $isActive = true;                public function __construct($customerId = null, $isActive = true) {            $this->customerId = $customerId;            $this->isActive = $isActive;        }        	public function getStatus() {		return $this->status;	}        public  function getIsActive() {            return $this->isActive;        }        public function setIsActive($isActive) {            $this->isActive = $isActive;        }        public function getCustomerId() {            return $this->customerId;        }        public function setCustomerId($customerId) {            $this->customerId = $customerId;        }	public function setStatus($status) {		$this->status = $status;		return $this;	}} ?>
<?php
	namespace BtcRelax;

	use \BtcRelax\Core;
	use \BtcRelax\Config;
	use \BtcRelax\DAO;
	use \BtcRelax\Flash;
	use \BtcRelax\Utils;
	use \BtcRelax\BitID;

  if ( !SecureSession::hasBitid())
  {
	   Utils::Redirect('main');
  }
  else
  {     
	  $nonce  = SecureSession::getValue('nonce');
	  $userid = SecureSession::getBitid(); 
	  //$email = SecureSession::getValue('email');
	  //$email = "temp@mail.ru";
  }
?>

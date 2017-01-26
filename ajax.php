<?php

namespace BtcRelax;

require('lib/core.inc');
	
$core = new \BtcRelax\Core();
$core->init();    
$dao = new \BtcRelax\DAO();
//$nonce = $_POST['nonce'];
$nonce = \BtcRelax\SecureSession::getValue("nonce");
$result= $dao->checkNonceAddr($nonce);
//if($result) {
//	$core->setBitId($result);       
//        $result = true;
//}
//return address/false to tell the VIEW it could log in now or not
echo json_encode($result);
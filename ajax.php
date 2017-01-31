<?php

namespace BtcRelax;

require('lib/core.inc');
	
$core = new \BtcRelax\Core();
$core->init();    
$dao = new \BtcRelax\DAO();
$nonce = $core->getNonce();
if (!isset($nonce) && isset($_POST))
{
    $nonce = $_POST["nonce"];
};


$result= $dao->checkNonceAddr($nonce);
//if($result) {
//	$core->setBitId($result);       
//        $result = true;
//}
//return address/false to tell the VIEW it could log in now or not
echo json_encode($result);
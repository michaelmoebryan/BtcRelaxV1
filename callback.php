<?php

namespace BtcRelax;

use \BtcRelax\Core;
use \BtcRelax\Config;
use \BtcRelax\DAO;

require('lib/core.inc');
	
$core = new Core();
$core->init();    

$bitid = new \BtcRelax\BitID();
$dao = new DAO();

$variables = $_POST;

$post_data = json_decode(file_get_contents('php://input'), true);
// SIGNED VIA PHONE WALLET (data is send as payload)
if($post_data!==null) {
	$variables = $post_data;
}

$signValid = $bitid->isMessageSignatureValidSafe(@$variables['address'], @$variables['signature'], @$variables['uri'], false);
$nonce = $bitid->extractNonce($variables['uri']);


$config = Config::getConfig();  
$vServerUrl = $config['SERVER_URL'];

if(($signValid != false) && $dao->checkNonce($nonce) && ($bitid->buildURI($vServerUrl . 'callback.php', $nonce) === $variables['uri'])) {
	$dao->update($nonce, $variables['address']);
	
	// SIGNED VIA PHONE WALLET (data is send as payload)
	if($post_data!==null) {
            
            //DO NOTHING
	} else {
		// SIGNED MANUALLY (data is stored in $_POST+$_REQUEST vs payload)
		// SHOW SOMETHING PRETTY TO THE USER
		$_SESSION['bitid'] = $variables['address'];
		header("Location: user.php");
	}


}

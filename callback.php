<?php

namespace BtcRelax;

use \BtcRelax\Core;
use \BtcRelax\Config;
use \BtcRelax\DAO;
use \BtcRelax\Flash;
use \BtcRelax\Utils;

require('lib/core.inc');
	
$core = new \BtcRelax\Core();
$core->init();    

$bitid = new \BtcRelax\BitID();
$dao = new DAO();

$variables = $_POST;

$post_data = json_decode(file_get_contents('php://input'), true);
// SIGNED VIA PHONE WALLET (data is send as payload)
if($post_data!==null) {
	$variables = $post_data;
}

//error_log('Address:' . @$variables['address'] . ' Sign: ' . @$variables['signature'] . ' Uri: ' . @$variables['uri']);
// ALL THOSE VARIABLES HAVE TO BE SANITIZED !
/*$variables = array('address' => '1Hsp3eAsemWtFswCCYH87mGkNQ63Xc7nwV' , 'signature' => 'H++eeFunfJMbRW6+W1c4WQX9EAym8k1z3t3sxnChZB0pAKT9dlRGJqE3UDivHy8OXDs0E9A8xYi8bFGtChOBNFI='
,'uri' => 'bitid://fastfen.club/callback.php?x=b406a2e7ad179a7c688ac48e8c0b93d6'
);
*/

$signValid = $bitid->isMessageSignatureValidSafe(@$variables['address'], @$variables['signature'], @$variables['uri'], false);
$nonce = $bitid->extractNonce($variables['uri']);

//error_log('isValidSign: ' . $signValid . ' Extracted nonce: ' . $nonce );

$config = Config::getConfig('BitId');  
$vServerUrl = $config['SERVER_URL'];

if(($signValid != false) && $dao->checkNonce($nonce) && ($bitid->buildURI($vServerUrl . 'callback.php', $nonce) === $variables['uri'])) {
	//error_log('Address: ' . $variables['address'] . ' accepted! ' );   
	$dao->update($nonce, $variables['address']);
	//$core->trySetCustomer($variables['address']);	
	// SIGNED VIA PHONE WALLET (data is send as payload)
	if($post_data!==null) {
            
            //DO NOTHING
	} else {
		// SIGNED MANUALLY (data is stored in $_POST+$_REQUEST vs payload)
		// SHOW SOMETHING PRETTY TO THE USER
		#session_start();
		$_SESSION['bitid'] = $variables['address'];
		header("Location: user.php");
	}


}

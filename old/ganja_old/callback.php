<?php
/*
Copyright 2014 Daniel Esteban

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

	http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

require_once dirname(__FILE__) . "/BitID.php";
require_once dirname(__FILE__) . "/DAO.php";
$bitid = new BitID();
$dao = new DAO();

$variables = $_POST;

$post_data = json_decode(file_get_contents('php://input'), true);
// SIGNED VIA PHONE WALLET (data is send as payload)
if($post_data!==null) {
	$variables = $post_data;
};


//$test =  var_dump($variables);
//file_put_contents('res.txt',$test,FILE_APPEND );
 $stateDescr = "New callback at:" . date('Y-m-d H:i:s') . " WIth data:" . $variables['signature'] . " addr:" . $variables['address'] . " Uri:" . $variables['uri'] ;
error_log($stateDescr . PHP_EOL,3,'log/callback.log');

// ALL THOSE VARIABLES HAVE TO BE SANITIZED !

$signValid = $bitid->isMessageSignatureValidSafe(@$variables['address'], @$variables['signature'], @$variables['uri']);
$nonce = $bitid->extractNonce($variables['uri']);

if($signValid != false && $dao->checkNonce($nonce))
{
	$dao->update($nonce, $variables['address']);
	
	/*if ( isset($variables['action']) ) {
	    	
	    $stateDescr = "Confirmed address: " . $variables['address'] . " action:" . $variables['action'] ;
	 	error_log($stateDescr, 3, 'log/jlog.log');
	 
		$dao->update($nonce, $variables['address']);
	
		session_start();
		$_SESSION['BitId'] = $variables['address'];
		header("Location: user.php");
	};*/
}
else
{
   $stateDescr = "Wrong callback." . date('Y-m-d H:i:s') . "Signature not valid or nonce ont exists";
   error_log($stateDescr . PHP_EOL ,3,'log/callback.log');
};
?>
<?php 
   
        require('lib/core.inc');
        $core = new \BtcRelax\Core();
	$core->init();
	$status = $core->getSessionState();     
        if ($status == BtcRelax\SecureSession::STATUS_ROOT)
        {
        	require_once('src/common.php');

                $_SESSION['user'] = 'chronos';
		$_SESSION['lang'] = 'en';
        	$_SESSION['theme'] = 'default';
        	$_SESSION['project'] = 'BtcRelax';        	
        }
?>

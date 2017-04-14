<?php 
        require('../lib/core.inc');
        $core = new \BtcRelax\Core();
		$core->init();
		$status = $core->getSessionState();
        if ($status == SecureSession::STATUS_ROOT)
        {
            $_SESSION['user'] = 'chronos';

        	$_SESSION['lang'] = 'en';
        	$_SESSION['theme'] = 'default';
        	//$_SESSION['project'] = 'Codiad';        	
        }
?>

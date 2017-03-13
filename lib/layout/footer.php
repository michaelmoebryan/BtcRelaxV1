<?php
    use BtcRelax\User;
    use BtcRelax\Core;
    
    global $core;
    $sessionState = $core->getSessionState();
    $userHash = null;
    if ($sessionState == BtcRelax\SecureSession::STATUS_USER || $sessionState == BtcRelax\SecureSession::STATUS_ROOT || $sessionState == BtcRelax\SecureSession::STATUS_BANNED)
    {
        $cUser = $core->getUser();       
        $userHash = $cUser->getUserHash();        
    }
?>
        <script>
			$( document ).ready( function() {
                               updateSessionState('<?php  echo(sprintf('%s', $sessionState)); ?>');
                            });
	
        </script>  
 

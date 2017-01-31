<?php

   namespace BtcRelax;

    use \BtcRelax\Utils;

    global $core;

            $status = $core->getSessionState();

            if ($status == SecureSession::STATUS_GUEST)
            {
                $guestContent = "<h1>Hello guest</h1>";
            }
            else
            {
                       Utils::Redirect('main');

            }

?>

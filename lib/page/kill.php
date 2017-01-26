
<?php

    /* @var $killSession type */
    $killSession = \BtcRelax\SecureSession::killSession();
    $redirect = Utils::redirect('main');


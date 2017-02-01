<?php
	
    namespace BtcRelax;
    /* @var $killSession type */
    global $core;
    $core->killSession();
    $redirect = Utils::redirect('main');


<?php
namespace BtcRelax;
require('lib/core.inc');
	
$core = new \BtcRelax\Core();
$core->init();	
$api=$core->getAPI();
$api->processApi();
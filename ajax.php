<?php
namespace BtcRelax;
require('lib/core.inc');
$core = new \BtcRelax\Core();
$core->init();    
$nonce = $_POST["nonce"];
$result["Result"]=$core->checkNonceAddress($nonce);
header('Content-type: application/json; charset=utf-8');
echo json_encode($result, JSON_PRETTY_PRINT | 
        JSON_UNESCAPED_UNICODE );
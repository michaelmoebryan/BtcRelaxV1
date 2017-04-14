<?php
namespace BtcRelax;
require('lib/core.inc');
$core = new \BtcRelax\Core();
$core->init();    
$dao = new \BtcRelax\DAO();
$nonce = $core->getNonce();
if (!isset($nonce) && isset($_POST))
{
    $nonce = $_POST["nonce"];
};
$result["Result"]=$dao->checkNonceAddr($nonce);
header('Content-type: application/json; charset=utf-8');
echo json_encode($result, JSON_PRETTY_PRINT | 
        JSON_UNESCAPED_UNICODE );
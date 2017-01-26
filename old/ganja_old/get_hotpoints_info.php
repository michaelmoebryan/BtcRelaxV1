<?php
    require_once 'ganja_core.php';
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    $hotpoinInf = GetHotpointInfo();
    
    if (isset($hotpoinInf))
    {
            echo(json_encode($hotpoinInf));
    }
    else
        new Response('Error', 404 , array('X-Status-Code' => 404));

?>

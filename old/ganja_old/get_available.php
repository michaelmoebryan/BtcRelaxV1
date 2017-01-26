<?php
    require_once 'ganja_core.php';
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    $avail = GetAvailableCount();
    if (is_int($avail))
    {
        $outp = '[{"AvailableItems":"'  . GetAvailableCount() . '"}]';
        echo($outp);
    }
    else
        new Response('Error', 404 /* ignored */, array('X-Status-Code' => 404)); ;
?>
<?php
    require_once 'ganja_core.php';
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    $vQuantity = 1;
    if (isset($_GET["quantity"]))
    {
        if (is_numeric($_GET["quantity"]))
        {
            $vQuantity  =  $_GET["quantity"];
        }
    }

    $vUAHPrice = GetTotalPriceUAH($vQuantity);
    $vBTCPrice = GetBTCPriceByUAH($vUAHPrice);
    if (isset($vBTCPrice) && isset($vUAHPrice))
    {

        $outp = '[
          {
            "BTC": ' . $vBTCPrice . ',
            "UAH": '. $vUAHPrice .'
          }
        ]]';
            echo($outp);
    }
    else
        new Response('Error', 404 , array('X-Status-Code' => 404));

?>

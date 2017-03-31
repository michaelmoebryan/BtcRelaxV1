<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        include_once "org_netbeans_saas_exchangerates/ExchangeRateskuna.php";
        try {

            $result = ExchangeRateskuna::btcuahByKuna();
            echo $result;
        } catch (Exception $e) {
            echo "Exception occured: " . $e;
        }
        ?>
    </body>
</html>

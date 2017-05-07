<?php
namespace BtcRelax;

class FIGA 
{
    function actionIsAuth() {
    $result = false;
    require '\BtcRelax\Core';
    $core = new \BtcRelax\Core();
    $core->init();
    $result = $core->isAuthenticated();
    return $result;
    }
}

?>
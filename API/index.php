<?php
namespace BtcRelax;
header("Access-Control-Allow-Origin: *");
ini_set("display_errors", 0);
$message = [];
    
if (!empty($_REQUEST["Token"]))
{
  $message["code"] = "-501";
  require('lib/core.inc');	
  $core = new \BtcRelax\Core();
  $core->init();
    
  $token = (string)filter_input(INPUT_POST, 'Token');
  $am = $core->getAM();
  $loginReult = $am->loginUserByToken($token);
  if ($loginReult === true)
  {
    $action = (string)filter_input(INPUT_POST, 'Action');
    switch ($action)
    {
        case'AddPoint':
            break;
        default:
           $message["code"] = 0;
           $message["ActionTypes"] = array("AddPoint","GetVersion");
            break;
    }
            
    }
  }
  else
  {
      Log::general('Incorrect request to PointsApi.',Log::WARN);
      die;
  }

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
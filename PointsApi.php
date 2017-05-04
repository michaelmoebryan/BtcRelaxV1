<?php
  namespace BtcRelax;
  $req_dump = print_r( $_REQUEST, true );
  $fp = file_put_contents( 'request.log', $req_dump );
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


  //the JSON message
header('Content-type: application/json; charset=utf-8');
echo json_encode($message, JSON_PRETTY_PRINT | 
        JSON_UNESCAPED_UNICODE );

<?php
  namespace BtcRelax;


  header("Access-Control-Allow-Origin: *");
  ini_set("display_errors", 0);
  
  require('lib/core.inc');
	
  $core = new \BtcRelax\Core();
  $message = [];
  //$req_sess = \BtcRelax\Utils::tryGetParam('PHPSESSID');

  //if (isset($req_sess))
  //{
  //    $message["session_id"]  = $req_sess;
  //	  session_id($req_sess);
  //	
  //};
  $core->init();    

  
  switch($_REQUEST["action"])
  {
        case 'createUser':
            $core->createUser();
            break;
        case 'kill':
                try
                {
                    $core->killSession();
                    $message["code"] = "0";
                    $message["message"] = "Session was killed";                    
                    break;
                }
                catch (Exception $ex)
                {
                    $message["code"] = "-2";
                    $message["message"] = "Unable to kill session";                    
                }
        case 'ping':
            $message["code"] = "0";
            $message["message"] = $core->getSessionState();
            $message["session_id"] = session_id(); 
            break;
	default:
            $message["code"] = "-1";
            $message["message"] = "Unknown method " . $_POST["action"];
            break;
  }

  
  //the JSON message
header('Content-type: application/json; charset=utf-8');
echo json_encode($message, JSON_PRETTY_PRINT | 
        JSON_UNESCAPED_UNICODE );

  


<?php

  header("Access-Control-Allow-Origin: *");
  ini_set("display_errors", 0);
  
  require('lib/core.inc');
	
  $core = new \BtcRelax\Core();
  $core->init();    
  $message = array();
  
  switch($_REQUEST["action"])
  {
        case 'kill':
                try
                {
                    $core->killSession();
                    $message["code"] = "0";
                }
                catch (Exception $ex)
                {
                    $message["code"] = "-2";
                    $message["message"] = "Unable to kill session";                    
                };
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

  


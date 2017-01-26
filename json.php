<?php

  header("Access-Control-Allow-Origin: *");
  ini_set("display_errors", 0);
  
  require('lib/core.inc');
	
    $core = new \BtcRelax\Core();
    $core->init();    
  $message = array();
  
  switch($_REQUEST["action"])
  {
        case 'getSession':
            require('lib/secsessions.inc');
            if (BtcRelax\SecureSession::is_session_started() === true)
            {
                $message["code"] = "0";
                $message["message"] = session_id();
            }
            else 
            {
                $message["code"] = "-2";
                $message["message"] = "Session not started";
            };
            
            break;
        case 'ping':
            require('lib/secsessions.inc');
            $message["code"] = "0";
            $message["message"] = BtcRelax\SecureSession::getActualStatus();
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

  


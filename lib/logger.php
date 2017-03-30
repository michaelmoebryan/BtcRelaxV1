<?php
namespace BtcRelax;
//For break use "\n" instead '\n' 

Class Log { 
  // 
  const USER_ERROR_DIR = '/logic-errors.log'; 
  const GENERAL_ERROR_DIR = '/general_errors.log'; 

  /* 
   User Errors... 
  */ 
    static public function user($msg,$username) 
    { 
    $date = date('d.m.Y h:i:s'); 
    $log = $msg."   |  Date:  ".$date."  |  User:  ".$username."\n"; 
    error_log($log, 3, self::USER_ERROR_DIR); 
    } 
    /* 
   General Errors... 
  */ 
    static public function general($msg) 
    { 
    $date = date('d.m.Y h:i:s'); 
    $log = $msg."   |  Date:  ".$date."\n"; 
    error_log($log, 3, self::GENERAL_ERROR_DIR); 
    } 

} 

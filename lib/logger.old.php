<?php
namespace BtcRelax;
use BtcRelax\Config;


Class Log { 
  // 
    const USER_ERROR_DIR = './logic-errors.log'; 
    const GENERAL_ERROR_DIR = './general_errors.log'; 

    const FATAL = -1;
    const ERROR = 0;
    const WARN = 1;
    const INFO = 2;
    const DEBUG = 3;
    
    static public function user($msg,$username,$logLevel=0) 
    { 
    $max = self::getMaxLogLevel();
    if ($logLevel >= $max)
    {
        $date = date('d.m.Y h:i:s'); 
        $log = $msg."   |  Date:  ".$date."  |  User:  ".$username."\n"; 
        error_log($log, 3, self::USER_ERROR_DIR);        
    }
    } 

    static public function general($msg,$logLevel=0) 
    { 
        $max = self::getMaxLogLevel();
        if ($logLevel <= $max)
        {
            $date = date('d.m.Y h:i:s'); 
            $log = $msg."   |  Date:  ".$date."\n"; 
            error_log($log, 3, self::GENERAL_ERROR_DIR); 
        }
    } 

    static public function getMaxLogLevel() 
    {
        $result = 0;
        if(!defined('MAX_LOG_LEVEL')) {
                $result = MAX_LOG_LEVEL;
           }
        return $result;
    }
} 

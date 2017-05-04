<?php
namespace BtcRelax;

require_once __DIR__.'/config/config.php';

Class Log {

    // 
    const USER_ERROR_DIR = './logic-errors.log';
    const GENERAL_ERROR_DIR = './general_errors.log';
    const FATAL = -1;
    const ERROR = 0;
    const WARN = 1;
    const INFO = 2;
    const DEBUG = 3;



    static public function user($msg, $username, $logLevel = 0) {
        $max = self::getMaxLogLevel();
        if ($logLevel >= $max) {
            $date = date('d.m.Y h:i:s');
            $log = $msg . "   |  Date:  " . $date . "  |  User:  " . $username . "\n";
            error_log($log, 3, self::USER_ERROR_DIR);
        }
    }

    static public function general($msg, $logLevel = 0) {
        $max = self::getMaxLogLevel();
        if ($logLevel <= $max) {
            $date = date('d.m.Y h:i:s');
            $log = $msg . "   |  Date:  " . $date . "\n";
            error_log($log, 3, self::GENERAL_ERROR_DIR);
        }
    }

    static public function getMaxLogLevel() {
        if (!defined('maxLogLevel')) {
            $config = Config::getConfig();
            if (is_numeric($config['LOG_LEVEL'])) {
                $maxLogLevel = filter_var($config['LOG_LEVEL'], FILTER_VALIDATE_INT);
                define('maxLogLevel', $maxLogLevel);                
            }
        }
        return maxLogLevel;
    }

}

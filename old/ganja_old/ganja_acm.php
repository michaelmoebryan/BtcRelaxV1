<?php
    if (!isset($_SESSION['acm']))
    {
         $acm = new acessControlManager();
         $_SESSION['acm'] = $acm;
    }

    //Access control manager
    function GetACM()
    {
        if (!isset($_SESSION['acm']))
        {
            $acm = new acessControlManager();
            $_SESSION['acm'] = $acm;
        }
        return $_SESSION['acm'];
    }

    class acessControlManager
    {
        private $RemoteAddress ;
        private $userLogin;

        public function __construct() {
           $this->RemoteAddress = new RemoteAddress();
           $this->wf_log('Created new ACM for Ip' . $this->RemoteAddress->getIpAddress() , 'INFO');
        }

        public function isAuthorized()
        {
            return isset($this->userLogin);
        }

        public function isAdmin()
        {
            return (strcasecmp($UserName, admin_mail ));
        }

        public function autorizeUser($pUserName)
        {
           $this->userLogin = $pUserName;
        }

        function wf_log($msg, $type = 'LOG') {
            global $Wildfire_header_sent, $Wildfire_msg_idx;
            $types = Array('LOG', 'INFO', 'WARN', 'ERROR');
            $type = in_array(strtoupper($type), $types) ? strtoupper($type) : $types[0];
            $escape = "\"\0\n\r\t\\";

            $trs = debug_backtrace();
            $last = Array();
            foreach ($trs as $li) {
                if (isset($li['class']) && $li['class'] == __CLASS__) { $last = $li; continue; }
                $last = $li;
                break;
            }

            $message = '[{"Type":"'.$type.'","File":"'.addcslashes($last['file'], $escape).'",'.
                '"Line":'.$last['line'].'},"'.addcslashes($msg, $escape).'"]';
            if ($Wildfire_msg_idx === false) $Wildfire_msg_idx = 0;
            if (!$Wildfire_header_sent) {
                $Wildfire_header_sent = true;
                header('X-Wf-Protocol-1: http://meta.wildfirehq.org/Protocol/JsonStream/0.2');
                header('X-Wf-1-Plugin-1: http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/0.3');
                header('X-Wf-1-Structure-1: http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1');
            }
            $count = ceil(strlen($message) / 5000);
            for ($i = 0; $i < $count; $i++) {
                $Wildfire_msg_idx++;
                $part = substr($message, ($i * 5000), 5000);
                header('X-Wf-1-1-1-'.$Wildfire_msg_idx.': '.(($i == 0) ? strlen($message) : '').
                    '|'.$part.'|'.(($i < ($count - 1)) ? '\\' : ''));
            }
        }

    }

    class RemoteAddress
    {
    /**
     * Whether to use proxy addresses or not.
     *
     * As default this setting is disabled - IP address is mostly needed to increase
     * security. HTTP_* are not reliable since can easily be spoofed. It can be enabled
     * just for more flexibility, but if user uses proxy to connect to trusted services
     * it's his/her own risk, only reliable field for IP address is $_SERVER['REMOTE_ADDR'].
     *
     * @var bool
     */
    protected $useProxy = false;

    /**
     * List of trusted proxy IP addresses
     *
     * @var array
     */
    protected $trustedProxies = array();

    /**
     * HTTP header to introspect for proxies
     *
     * @var string
     */
    protected $proxyHeader = 'HTTP_X_FORWARDED_FOR';

    // [...]

    /**
     * Returns client IP address.
     *
     * @return string IP address.
     */
    public function getIpAddress()
    {
        $ip = $this->getIpAddressFromProxy();
        if ($ip) {
            return $ip;
        }

        // direct IP address
        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return '';
    }

    /**
     * Attempt to get the IP address for a proxied client
     *
     * @see http://tools.ietf.org/html/draft-ietf-appsawg-http-forwarded-10#section-5.2
     * @return false|string
     */
    protected function getIpAddressFromProxy()
    {
        if (!$this->useProxy
            || (isset($_SERVER['REMOTE_ADDR']) && !in_array($_SERVER['REMOTE_ADDR'], $this->trustedProxies))
        ) {
            return false;
        }

        $header = $this->proxyHeader;
        if (!isset($_SERVER[$header]) || empty($_SERVER[$header])) {
            return false;
        }

        // Extract IPs
        $ips = explode(',', $_SERVER[$header]);
        // trim, so we can compare against trusted proxies properly
        $ips = array_map('trim', $ips);
        // remove trusted proxy IPs
        $ips = array_diff($ips, $this->trustedProxies);

        // Any left?
        if (empty($ips)) {
            return false;
        }

        // Since we've removed any known, trusted proxy servers, the right-most
        // address represents the first IP we do not know about -- i.e., we do
        // not know if it is a proxy server, or a client. As such, we treat it
        // as the originating IP.
        // @see http://en.wikipedia.org/wiki/X-Forwarded-For
        $ip = array_pop($ips);
        return $ip;
    }

    // [...]
}
?>

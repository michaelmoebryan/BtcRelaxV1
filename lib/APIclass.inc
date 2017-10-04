<?php
namespace BtcRelax;
    
class API {
     
    public $data = "";
    public $_allow = array();
    public $_content_type = "application/json";
    public $_request = array();
        
    private $_method = "";      
    private $_code = 200;
          
    
    
    public function __construct(){
        $this->inputs();           // Init parent contructor
    }

    public function get_referer(){
           return $_SERVER['HTTP_REFERER'];
    }
         
        public function response($data,$status){
            $this->_code = ($status)?$status:200;
            $this->set_headers();
            echo json_encode($data, JSON_PRETTY_PRINT | 
        JSON_UNESCAPED_UNICODE );
        }
         
        private function get_status_message(){
            $status = array(
                        100 => 'Continue',  
                        101 => 'Switching Protocols',  
                        200 => 'OK',
                        201 => 'Created',  
                        202 => 'Accepted',  
                        203 => 'Non-Authoritative Information',  
                        204 => 'No Content',  
                        205 => 'Reset Content',  
                        206 => 'Partial Content',  
                        300 => 'Multiple Choices',  
                        301 => 'Moved Permanently',  
                        302 => 'Found',  
                        303 => 'See Other',  
                        304 => 'Not Modified',  
                        305 => 'Use Proxy',  
                        306 => 'Session have not nonce',  
                        307 => 'Temporary Redirect',  
                        400 => 'Bad Request',  
                        401 => 'Unauthorized',  
                        402 => 'Payment Required',  
                        403 => 'Forbidden',  
                        404 => 'Not Found',  
                        405 => 'Method Not Allowed',  
                        406 => 'Not Acceptable',  
                        407 => 'Proxy Authentication Required',  
                        408 => 'Request Timeout',  
                        409 => 'Conflict',  
                        410 => 'Gone',  
                        411 => 'Length Required',  
                        412 => 'Precondition Failed',  
                        413 => 'Request Entity Too Large',  
                        414 => 'Request-URI Too Long',  
                        415 => 'Unsupported Media Type',  
                        416 => 'Requested Range Not Satisfiable',  
                        417 => 'Expectation Failed',  
                        500 => 'Internal Server Error',  
                        501 => 'Not Implemented',  
                        502 => 'Bad Gateway',  
                        503 => 'Service Unavailable',  
                        504 => 'Gateway Timeout',  
                        505 => 'HTTP Version Not Supported');
            return ($status[$this->_code])?$status[$this->_code]:$status[500];
        }
         
        public function get_request_method(){
            return $_SERVER['REQUEST_METHOD'];
        }
         
        private function inputs(){
            switch($this->get_request_method()){
                case "POST":
                    $this->_request = $this->cleanInputs($_POST);
                    break;
                case "GET":
                case "DELETE":
                    $this->_request = $this->cleanInputs($_GET);
                    break;
                case "PUT":
                    parse_str(file_get_contents("php://input"),$this->_request);
                    $this->_request = $this->cleanInputs($this->_request);
                    break;
                default:
                    $this->response('',406);
                    break;
            }
        }       
         
        private function cleanInputs($data){
            $clean_input = array();
            if(is_array($data)){
                foreach($data as $k => $v){
                    $clean_input[$k] = $this->cleanInputs($v);
                }
            }else{
                if(get_magic_quotes_gpc()){
                    $data = trim(stripslashes($data));
                }
                $data = strip_tags($data);
                $clean_input = trim($data);
            }
            return $clean_input;
        }       
         
        private function set_headers(){
            header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
            header("Content-Type:".$this->_content_type);
        }

    /*
    *  Encode array into JSON
    */
    private function json($data){
        if(is_array($data)){
            return json_encode($data);
        }
    }

    /*
     * Public method for access api.
     * This method dynmically call the method based on the query string
     *
     */
    public function processApi(){
        $func = \strtolower(\trim(\str_replace("/","",$_REQUEST['request'])));
        if ((int) method_exists($this, $func) > 0) {
            try {
              Log::general(sprintf('Method %s was called by api', $func), Log::DEBUG);
              $this->$func();  
            } catch (Exception $exc) {
               $vMsg = $exc->getTraceAsString();
               echo $vMsg;
               Log::general($vMsg, Log::WARN ); 
            };
        } else {
            $this->response('Error code 404, method not found', 404);
        }   // If the method not exist with in this class, response would be "Page not found".
    }

    /*
     * External methods:
     * Get core version. And session state.
     * 
     */
    private function V1(){    
        if($this->get_request_method() != "GET"){
            $this->response('',406);
        }
        
        global $core;
        if ($core instanceof \BtcRelax\Core)
        {
            $vVer = $core->getVersion();
            $vSessionState = $core->getSessionState();
            $param["Core version"]=$vVer;
            $param["SessionState"]=$vSessionState;
            $this->response($param, 200);    
        }
    }
 
    /*
     * External methods:
     * checkNonce from arguments
     */
    public function checkNonce()
    {
        global $core;
        if ($core instanceof \BtcRelax\Core)
        {    
            $nonce=$core->getNonce();
            if (!FALSE==$nonce)
            {
                $result["Result"]=$core->checkNonceAddress($nonce);
                $this->response($result, 200);
            }
            else
            {
                $this->response($result, 306);
            }
        }       
    }
    
    /*
     * External methods:
     * Get authentication rules.
     */
    public function getAuth()
    {        
        global $core;
        if ($core instanceof \BtcRelax\Core)
        {
            if (!$core->isAuthenticated())
            {
                $vAuthParams = $core->setAuthenticate();
                $this->response($vAuthParams, 200);
            }
            else
            {
                $this->response('You already authenticated. Logoff before.', 405);                
            }
        }      
    }
    
    /*
     * External methods:
     * Get Orders info
     */
    public function getOrders()
    {
        global $core;
        if ($core instanceof \BtcRelax\Core)
        {
            $vOM = $core->getOM();
        }
    }
    
    }
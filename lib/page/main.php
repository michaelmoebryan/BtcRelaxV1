<?php	namespace BtcRelax;        use \BtcRelax\Config;	use \BtcRelax\DAO;        use \BtcRelax\Utils;	use \BtcRelax\BitID;	use \BtcRelax\SecureSession; 	        global $core;	        $current_state = clone $core->current_session;        $vState = $current_state->getAcualStatus();	if ($vState === SecureSession::STATUS_UNAUTH)        {                        $authenticate = $current_state->Authenticate();//            $bitid = new Bi3tID();//            $nonce = $bitid->generateNonce();//            $config = Config::getConfig('BitId');      //            $bitid_uri = $bitid->buildURI( $config['SERVER_URL'] . 'callback.php', $nonce);//            $qr_uri = $bitid->qrCode($bitid_uri);//            $ajax_uri = $config['SERVER_URL'] . 'ajax.php';//            $user_uri = Utils::createLink('user'); //            $dao = new DAO();//            $result = $dao->insert($nonce, $_SERVER['REMOTE_ADDR']);                        /* @var $result type *///                if(!$result)//                        {//                                echo "<pre>";//                                echo "Database failer\n";//                                var_dump($dao);//                                die();//                        }//                        else//                        {//                                 SecureSession::setVal("nonce",$nonce);  //                        }        }         
<?phpuse BtcRelax\Config;namespace BtcRelax;global $core;$current_state = $core->getSessionState();if ($current_state === \BtcRelax\SecureSession::STATUS_UNAUTH)        {                          /* @var $authParams type */              $authParams = $core->setAuthenticate();         }         
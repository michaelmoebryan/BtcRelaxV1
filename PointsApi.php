<?php
  namespace BtcRelax;
  header("Access-Control-Allow-Origin: *");
  ini_set("display_errors", 0);
  $message = [];
            
  require('lib/core.inc');	
  $core = new \BtcRelax\Core();
  $core->init();
  $action = json_decode(filter_input( INPUT_GET , 'action'));
  $tokenId = \intval(filter_input( INPUT_GET , 'tokenId'));
  $tokenKey = filter_input( INPUT_GET , 'tokenKey');
 
  $ActionType = $action->type;
  if (is_integer($tokenId) && is_string($tokenKey))
  {
      
  }
  
  //
  //
  //
  // die if SQL statement failed

  //if (!$result) {
  //
  //  http_response_code(404);
  //
  //  die(mysqli_error());
  //
  //}
  switch ($ActionType)
        {
            case'ActivatePoint':
                $dao = new \BtcRelax\BookmarkDao();
                $v_bookmark_id =  intval($action->bookmarkId);
                $daoRes = $dao->findById($v_bookmark_id);
                if ($daoRes instanceof \BtcRelax\Model\Bookmark)
                {
                    $message["code"] = 0;
                    $vOpResult = $daoRes->setPointStateById(Model\Bookmark::STATUS_PUBLISHED);
                    $message["operationResult"] =$vOpResult;
                }
                else
                {
                    $message["code"] = -1;
                    $message["Message"] = array("Description, ",sprintf("Bookmark id: %s not found" , $v_bookmark_id) );                   
                };
            case'DisactivatePoint':
                $dao = new \BtcRelax\BookmarkDao();
                $v_bookmark_id =  intval($action->bookmarkId);
                $daoRes = $dao->findById($v_bookmark_id);
                if ($daoRes instanceof \BtcRelax\Model\Bookmark)
                {
                    $message["code"] = 0;
                    $vOpResult = $daoRes->setPointStateById(Model\Bookmark::STATUS_PREPARING);
                    $message["operationResult"] =$vOpResult;
                }
                else
                {
                    $message["code"] = -1;
                    $message["Message"] = array("Description, ",sprintf("Bookmark id: %s not found" , $v_bookmark_id) );                   
                };
                break;                           
            case'GetPointState':
                $dao = new \BtcRelax\BookmarkDao();
                $v_bookmark_id =  intval($action->bookmarkId);
                $daoRes = $dao->findById($v_bookmark_id);
                if ($daoRes instanceof \BtcRelax\Model\Bookmark)
                {
                    $message["code"] = 0;
                    $vState = $daoRes->getState();
                    $message["serverState"] =$vState;
                    if (($vState == Model\Bookmark::STATUS_PREORDERED) || ($vState == Model\Bookmark::STATUS_LOST) || ($vState == Model\Bookmark::STATUS_SALED  ))
                    {
                        $vOrder = $daoRes->getIdOrder();
                        $message["OrderId"] = $vOrder;                        
                    };
                }
                else
                {
                    $message["code"] = -1;
                    $message["Message"] = array("Description, ",sprintf("Bookmark id: %s not found" , $v_bookmark_id) );                   
                };
                break;
            case'AddPoint':
                $new = new \BtcRelax\Model\Bookmark();
                $new->setLocation($action->inf->lat, $action->inf->lng);
                $new->setRegionTitle($action->inf->region);
                $new->setLink($action->inf->link);
                $new->setAdvertiseTitle($action->inf->advName);
                $new->setCustomPrice($action->inf->price);
                $new->setIdDroper('1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN');
                $new->setDescription($action->inf->descr);
                $dao = new \BtcRelax\BookmarkDao();
                $daoRes = $dao->createNew($new);
                if ($daoRes instanceof \BtcRelax\Model\Bookmark)
                {
                    $message["code"] = 0;
                    $message["bookmarkId"] = $daoRes->getIdBookmark();
                }
                else
                {
                    $message["code"] = -1;
                    $message["Message"] = array("Description","Bookmark not found");                   
                };
                break;
            default:
                $message["code"] = 0;
                $message["ActionTypes"] = array("AddPoint","GetVersion","ActivatePoint","DisactivatePoint");
                break;
        };
  
  


  //the JSON message
header('Content-type: application/json; charset=utf-8');
echo json_encode($message, JSON_PRETTY_PRINT | 
        JSON_UNESCAPED_UNICODE );
file_put_contents('request.txt', file_get_contents('php://input'));

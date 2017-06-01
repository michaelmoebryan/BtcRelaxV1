<?php
  namespace BtcRelax;
  header("Access-Control-Allow-Origin: *");
  ini_set("display_errors", 0);
  file_put_contents('request.txt', file_get_contents('php://input'));
  $message = [];
            
  require('lib/core.inc');	
  $core = new \BtcRelax\Core();
  $core->init();
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
  $rawAction = json_decode($_REQUEST["action"]);
  $tokenId = $_REQUEST["tokenId"];
  $tokenKey = $_REQUEST["tokenKey"];
  $action = $rawAction[0];
  $ActionType = $action->type;
  switch ($ActionType)
        {
            case'item_add':
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
                $message["ActionTypes"] = array("AddPoint","GetVersion");
                break;
        };
  
  


  //the JSON message
header('Content-type: application/json; charset=utf-8');
echo json_encode($message, JSON_PRETTY_PRINT | 
        JSON_UNESCAPED_UNICODE );

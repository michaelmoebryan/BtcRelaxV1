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
            case'GetOrderById':
                $vOm = $core->getOM();
                $vOrderId =  intval($action->OrderId);
                if ($vOrderId>0)
                {
                    $vOrder = $vOm->getOrderById($vOrderId);
                    if ($vOrder instanceof \BtcRelax\Model\Order)
                            {
                                $message["code"] = 0;
                                $vState = $vOrder->getState();
                                $vInvoiceAddress = $vOrder->getInvoiceAddress();
                                $message["serverState"] =$vState;
                                $message["invoiceAddress"] =$vInvoiceAddress;
                            }
                            else
                            {
                                $message["code"] = -1;
                                $message["Message"] = array("Description",sprintf("Order id:%s not found" , $vOrderId) );                   
                            };

                }
                else
                {
                                $message["code"] = -2;
                                $message["Message"] = array("Description",sprintf("Incorrect parameter OrderId:" , $vOrderId));                                                       };
                break;
            case'GetOrdersList':
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
                $message["ActionTypes"] = array("GetOrderById","GetOrdersList");
                break;
        };
  
  


  //the JSON message
header('Content-type: application/json; charset=utf-8');
echo json_encode($message, JSON_PRETTY_PRINT | 
        JSON_UNESCAPED_UNICODE );
file_put_contents('request.txt', file_get_contents('php://input'));

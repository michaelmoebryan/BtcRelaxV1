<?php
require("../codebase/connector/grid_connector.php");//adds the connector engine
require('../lib/core.inc');
$core = new \BtcRelax\Core();
$core->init();
$dao = new BtcRelax\SessionsDao();
#$host = $core->getConfigParam('DB_HOST');
#$user = $core->getConfigParam('DB_USER');
#$pass = $core->getConfigParam('DB_PASS');
#$name = $core->getConfigParam('DB_NAME');
#$res=mysql_connect($host,$user,$pass);//connects to server with  db
#mysql_select_db($name);//connects to db with name "dhtmlx_tutorial"  
 
try
{
    $conn = new GridConnector($dao->getDb());             //initializes the connector object
    $conn->render_table("vwSessionsInfo","sid","sid,expires");  //l
}
catch (Exception $exc)
{
    BtcRelax\Log::general($exc->getMessage(), BtcRelax\Log::WARN ); 
}


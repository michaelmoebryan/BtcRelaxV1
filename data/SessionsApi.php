<?php
require("../codebase/connector/grid_connector.php");//adds the connector engine
require('../lib/core.inc');
$core = new \BtcRelax\Core();
$core->init();
$host = $config['DB_HOST'];
$user = $config['DB_USER'];
$pass = $config['DB_PASS'];
$name = $config['DB_NAME'];
$res=mysql_connect($host,$user,$pass);//connects to server with  db
mysql_select_db($name);//connects to db with name "dhtmlx_tutorial"  
 
$conn = new GridConnector($res);             //initializes the connector object
$conn->render_table("mySession_Sessions","sid","sid,expires");  //l
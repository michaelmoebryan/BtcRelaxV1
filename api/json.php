<?php

  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  
  if (!empty($_GET))
  {
	  $method = $_GET['method'];
	  if (isset($method))
	  {
		  switch ($method) {
				   case 'ping':
						$outp = '[{"Result":"' . "Ok" . '"}]';
						echo($outp);
						break;
				   default:
					echo ('Fuck off!');
		  }
	  }
  }
  
?>

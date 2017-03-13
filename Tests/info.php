<?php

 			  try {
                                        $vId = '1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN';
                                        $vRecoveryMail = '';
                                        $db = new PDO("mysql:host=dev.btcrelax.xyz;dbname=Ident", "ident", "ident");		 
					// execute the stored procedure
                                        $callQuery = "CALL `CreateCustomerByBitId`(:pBitId,:pRecoveryMail , @out_id, @error_msg)";
                                        $call = $db->prepare($callQuery);
                                        $call->bindParam(':pBitId', $vId, PDO::PARAM_STR);
					$call->bindParam(':pRecoveryMail', $vRecoveryMail , PDO::PARAM_STR);
                                        //$call->bind_param('ss', $vId, $vRecoveryMail);
                                        $call->execute();
					// execute the second query to get values from OUT parameter
                                        $select = $db->query("SELECT  @out_id, @error_msg");
                                        $result = $select->fetch(PDO::FETCH_ASSOC);
                                        if ($result)
                                        {
                                            $pResultId    = $result['@out_id'];
                                            $pPResultMsg = $result['@error_msg'];      
                                        }					
			 } catch (PDOException $pe) {
						die("Error occurred:" . $pe->getMessage());
			 };    

?>

<?php
         require_once 'ganja_core.php';
         /* Global business objects */
         
         class PaymentFactory
         {

            public static function create($idOrder, $transaction_hash)
            {
                $result = new Payment ($idOrder,$transaction_hash); 
                return $result;                    
                
            }
         }
        
         class Payment {
                public $pIdOrder; 
                public $pTransaction_hash;
                public $pValue_in_btc;
                public $PAdd_date;
                public $pIsUserNotified;
                public $pIsAdminNotified;

             
             public function __construct($idOrder, $transaction_hash) {
             try
              {
                  if (GetDbConnector($dbCon))
                    {
                        $query = "SELECT `Payments`.`idOrder`,
                                        `Payments`.`transaction_hash`,
                                        `Payments`.`value_in_btc`,
                                        `Payments`.`add_date`,
                                        `Payments`.`isUserNotified`,
                                        `Payments`.`isAdminNotified`
                                    FROM `Payments`
                                    WHERE    `Payments`.`idOrder`  = ? 
                                    AND `Payments`.`transaction_hash` = ?" ;
                        if ($stmt = $dbCon->prepare($query)) {
                            $stmt->bind_param('is', $idOrder, $transaction_hash );
                            $stmt->execute();
                            $rows_cnt = $stmt->num_rows; 
                            if ($rows_cnt = 1)
                            {
                                $stmt->bind_result($idOrder, $transaction_hash,
                                $value_in_btc, $add_date,
                                $isUserNotified, $isAdminNotified);
                                while ($stmt->fetch()) {
                                      $this->pIdOrder = $idOrder;
                                      $this->pTransaction_hash = $transaction_hash;
                                      $this->pValue_in_btc = $value_in_btc;
                                      $this->PAdd_date = $add_date;
                                      $this->pIsUserNotified = $isUserNotified;
                                      $this->pIsAdminNotified = $isAdminNotified;
                                }
                            };
                            $stmt->close();
                            //wf_log("Order ".$orderId." initialized.", 'INFO') ;
                        }
                    }
              }
              catch (Exception $e )
              {
                   wf_log($e->message, 'ERROR');
              }; 
             }
  
         
                      public function setIsAdminNotify()
                      {
                        try
                          {
                              if (GetDbConnector($dbCon))
                                {
                                    $query = "UPDATE `Payments`
                                            SET  `Payments`.`isAdminNotified` = 1
                                            WHERE   `Payments`.`idOrder`  = ? 
                                            AND `Payments`.`transaction_hash` = ?";
                                            
                                    if ($stmt = $dbCon->prepare($query))
                                    {       /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                                                $stmt->bind_param('is', $this->pIdOrder, $this->pTransaction_hash);
                                                $stmt->execute();
                                                $dbCon->commit();
                                        
                                        $this->pIsAdminNotified = 1;
                                    }
                                }
                          }
                          catch (Exception $e )
                          {
                               wf_log($e->message, 'ERROR');
                          }; 
                      }
         }
         
?>

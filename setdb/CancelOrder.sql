CREATE PROCEDURE `CancelOrder` (IN pOrderId int, OUT pResult int)
BEGIN
   DECLARE vResult int default 0;
   DECLARE vDeliveryMethod varchar(25) ;
   DECLARE vCurrentState varchar(50) ;
   DECLARE vPreorderedPointsCount int;
   DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET error = TRUE;
   SET autocommit= 0;
   
   
		START TRANSACTION;
        
			SELECT 
    `OrderState`, `DeliveryMethod`
INTO vCurrentState , vDeliveryMethod FROM
    `Orders`
WHERE
    `Orders`.`idOrder` = pOrderId;
		   
			SELECT 
    COUNT(1)
INTO vPreorderedPointsCount FROM
    `Bookmarks`
WHERE
    `State` = 'Preordered'
        AND `idOrder` = pOrderId;
		  
			IF ((vCurrentState = 'WaitForPayment')  AND (vPreorderedPointsCount  > 0 ) AND (vDeliveryMethod = 'HotHiddenPoint') ) THEN 
				begin
					UPDATE `Bookmarks`
					SET `State` = 'Published'
					WHERE `State` = 'Preordered' AND `idOrder` = pOrderId;	
				end;
			END IF;
		   
			UPDATE `Orders` 
SET 
    `OrderState` = 'Canceled',
    `EndDate` = NOW()
WHERE
    `idOrder` = pOrderId;

    
		IF error THEN
				ROLLBACK;
				SET out_id = -1;
		ELSE
				COMMIT;   
		END IF;
		SET autocommit= 1;
END

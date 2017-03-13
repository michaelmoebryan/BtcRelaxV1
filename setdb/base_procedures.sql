CREATE DEFINER=`ident`@`%` PROCEDURE `CreateCustomerByBitId`(IN pBitId VARCHAR(50), 
 IN pRecoveryMail VARCHAR(50),
 OUT out_id integer,
 OUT error_msg VARCHAR(500))
BEGIN

		DECLARE newCust Varchar(34);
		DECLARE error BOOLEAN DEFAULT FALSE;
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET error = TRUE;

		SET error_msg = '';
		SET out_id = '';
		SET newCust = pBitId;

		START TRANSACTION;
		INSERT INTO `Customers`
			(`idCustomer`)
			VALUES
			(newCust);
	
			INSERT INTO Idetities( IdentTypeCode, IdentityKey, idCustomer)
				VALUES( 'bitid', pBitId  , newCust );

		
			IF (not (pRecoveryMail = '' OR pRecoveryMail is null) ) THEN
				INSERT INTO Idetities( IdentTypeCode, IdentityKey, idCustomer)
				VALUES( 'email', LOWER(pRecoveryMail)  , newCust );

			END IF;
		
		SET out_id  =  1;

		IF error THEN
				ROLLBACK;
				SET out_id = -1;
				SET error_msg = 'Error';
		ELSE
				COMMIT;   
		END IF;
END
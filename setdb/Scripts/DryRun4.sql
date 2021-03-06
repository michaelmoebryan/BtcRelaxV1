-- MySQL Workbench Forward Engineering

-- begin attached script 'PrimarySettings'
SET NAMES 'UTF8';
-- end attached script 'PrimarySettings'
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- begin attached script 'PrepareBackups'
DROP TABLE IF EXISTS bck_Customers ;
DROP TABLE IF EXISTS bck_Idetities ;

CREATE TABLE bck_Customers AS SELECT * FROM Customers  ;
CREATE TABLE bck_Idetities AS SELECT * FROM Idetities  ;


-- end attached script 'PrepareBackups'
-- -----------------------------------------------------
-- Schema Ident
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `Customers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Customers` ;

CREATE TABLE IF NOT EXISTS `Customers` (
  `idCustomer` VARCHAR(34) NOT NULL,
  `CreateDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isBaned` BIT(1) NOT NULL DEFAULT b'0',
  `ChangeDate` TIMESTAMP NULL,
  `Preferences` NVARCHAR(1024) NULL,
  PRIMARY KEY (`idCustomer`),
  UNIQUE INDEX `idCustomer_UNIQUE` (`idCustomer` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `Rights`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Rights` ;

CREATE TABLE IF NOT EXISTS `Rights` (
  `idRight` INT NOT NULL AUTO_INCREMENT,
  `CreateDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EndDate` TIMESTAMP NULL,
  `RightCode` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idRight`),
  UNIQUE INDEX `RightCode_UNIQUE` (`RightCode` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `AccessRights`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `AccessRights` ;

CREATE TABLE IF NOT EXISTS `AccessRights` (
  `AccessId` INT NOT NULL AUTO_INCREMENT,
  `RightId` INT NOT NULL,
  `idCustomer` VARCHAR(34) NOT NULL,
  `EndDate` DATETIME NULL,
  `CreateDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `fk_AccessRights_Customers1_idx` (`idCustomer` ASC),
  INDEX `fk_AccessRights_Rights1_idx` (`RightId` ASC),
  PRIMARY KEY (`AccessId`),
  UNIQUE INDEX `EndDate_UNIQUE` (`EndDate` ASC),
  UNIQUE INDEX `idCustomer_UNIQUE` (`idCustomer` ASC),
  UNIQUE INDEX `RightId_UNIQUE` (`RightId` ASC),
  CONSTRAINT `fk_AccessRightsCustomers`
    FOREIGN KEY (`idCustomer`)
    REFERENCES `Customers` (`idCustomer`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_AccessRights`
    FOREIGN KEY (`RightId`)
    REFERENCES `Rights` (`idRight`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `AccessTokens`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `AccessTokens` ;

CREATE TABLE IF NOT EXISTS `AccessTokens` (
  `tokenId` INT(11) NOT NULL AUTO_INCREMENT,
  `idCustomer` VARCHAR(34) NOT NULL,
  `CreateDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EndDate` TIMESTAMP NULL DEFAULT NULL,
  `UseCount` SMALLINT(11) NULL DEFAULT '0',
  `MaxUseCount` SMALLINT(11) NULL DEFAULT '1',
  `AccessRightsId` INT NULL,
  PRIMARY KEY (`tokenId`),
  INDEX `dt_datetime` (`tokenId` ASC),
  INDEX `fk_AccessTokens_Customers1_idx` (`idCustomer` ASC),
  INDEX `fk_AccessTokens_AccessRights1_idx` (`AccessRightsId` ASC),
  CONSTRAINT `fk_AccessTokens_Customers1`
    FOREIGN KEY (`idCustomer`)
    REFERENCES `Customers` (`idCustomer`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_AccessTokens_AccessRights1`
    FOREIGN KEY (`AccessRightsId`)
    REFERENCES `AccessRights` (`AccessId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `PubKeys`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `PubKeys` ;

CREATE TABLE IF NOT EXISTS `PubKeys` (
  `idPubKey` INT NOT NULL AUTO_INCREMENT,
  `idOwner` VARCHAR(34) NOT NULL,
  `CreateDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `xPubKey` VARCHAR(112) NOT NULL,
  `EndDate` TIMESTAMP NULL,
  PRIMARY KEY (`idPubKey`),
  INDEX `fk_PubKeysOwners_idx` (`idOwner` ASC),
  UNIQUE INDEX `xPubKey_UNIQUE` (`xPubKey` ASC),
  CONSTRAINT `fk_PubKeys_Customers1`
    FOREIGN KEY (`idOwner`)
    REFERENCES `Customers` (`idCustomer`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `InvoiceAddress`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `InvoiceAddress` ;

CREATE TABLE IF NOT EXISTS `InvoiceAddress` (
  `idInvoiceAddress` INT NOT NULL AUTO_INCREMENT,
  `idPubKey` INT NOT NULL,
  `InvoiceAddres` VARCHAR(34) NOT NULL,
  `CreateDate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `Balance` DECIMAL(12,8) NULL DEFAULT 0,
  PRIMARY KEY (`idInvoiceAddress`),
  UNIQUE INDEX `InvoiceAddres_UNIQUE` (`InvoiceAddres` ASC),
  INDEX `fk_InvoiceAddress_PubKeys1_idx` (`idPubKey` ASC),
  CONSTRAINT `fk_InvoiceAddress_PubKeys1`
    FOREIGN KEY (`idPubKey`)
    REFERENCES `PubKeys` (`idPubKey`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Orders`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Orders` ;

CREATE TABLE IF NOT EXISTS `Orders` (
  `idOrder` INT(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EndDate` TIMESTAMP NULL,
  `OrderState` ENUM('Created', 'Confirmed', 'Paid', 'WaitForPayment', 'Canceled', 'Finished') NULL DEFAULT 'Created',
  `BTCPrice` DECIMAL(12,8) NOT NULL,
  `PricingDate` TIMESTAMP NULL,
  `InvoiceAddress` VARCHAR(34) NOT NULL,
  `idCreator` VARCHAR(34) NOT NULL,
  `DeliveryMethod` ENUM('HotHiddenPoint', 'OrderedHotPoint', 'PostOffice') NULL DEFAULT 'HotHiddenPoint',
  `InvoiceBalance` DECIMAL(12,8) NULL,
  `BalanceDate` TIMESTAMP NULL,
  PRIMARY KEY (`idOrder`, `idCreator`),
  INDEX `fk_Orders_CustomersCreator_idx` (`idCreator` ASC),
  UNIQUE INDEX `InvoiceAddress_UNIQUE` (`InvoiceAddress` ASC),
  CONSTRAINT `fk_Orders_CustomersCreator`
    FOREIGN KEY (`idCreator`)
    REFERENCES `Customers` (`idCustomer`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_inv_addr`
    FOREIGN KEY (`InvoiceAddress`)
    REFERENCES `InvoiceAddress` (`InvoiceAddres`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Bookmarks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Bookmarks` ;

CREATE TABLE IF NOT EXISTS `Bookmarks` (
  `idBookmark` INT(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `idOrder` INT(11) NULL,
  `Quantity` DECIMAL(12,2) NOT NULL,
  `EndDate` TIMESTAMP NULL DEFAULT NULL,
  `Latitude` DECIMAL(12,8) NOT NULL,
  `Longitude` DECIMAL(12,8) NOT NULL,
  `Link` VARCHAR(256) NULL DEFAULT NULL,
  `Description` VARCHAR(1024) NULL DEFAULT NULL,
  `RegionTitle` VARCHAR(45) NULL DEFAULT NULL,
  `CustomPrice` DECIMAL(12,2) NULL DEFAULT NULL,
  `PriceCurrency` ENUM('UAH', 'BTC', 'EUR', 'USD') NOT NULL DEFAULT 'UAH',
  `AdvertiseTitle` VARCHAR(45) NOT NULL,
  `UnlockDate` TIMESTAMP NULL,
  `State` ENUM('Preparing', 'Checking', 'Rejected', 'Ready', 'Published', 'Saled', 'Lost', 'PreOrdered') NULL DEFAULT 'Preparing',
  `IdDroper` VARCHAR(34) NOT NULL,
  `TargetAddress` VARCHAR(34) NOT NULL,
  PRIMARY KEY (`idBookmark`, `IdDroper`),
  UNIQUE INDEX `Latitude` (`Latitude` ASC, `Longitude` ASC),
  INDEX `fk_droper_idx` (`IdDroper` ASC),
  INDEX `fk_OrderId_idx` (`idOrder` ASC),
  CONSTRAINT `fk_droper`
    FOREIGN KEY (`IdDroper`)
    REFERENCES `Customers` (`idCustomer`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_OrderId`
    FOREIGN KEY (`idOrder`)
    REFERENCES `Orders` (`idOrder`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `CustomersHierarhy`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `CustomersHierarhy` ;

CREATE TABLE IF NOT EXISTS `CustomersHierarhy` (
  `CustomersParent` VARCHAR(34) NOT NULL,
  `CustomersChild` VARCHAR(34) NOT NULL,
  `CreateDate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CustomersParent`, `CustomersChild`),
  INDEX `CustomersParent_idx` (`CustomersParent` ASC),
  INDEX `fk_CustomersHierarhy_Child_idx` (`CustomersChild` ASC),
  CONSTRAINT `fk_CustomersHierarhy_Parent`
    FOREIGN KEY (`CustomersParent`)
    REFERENCES `Customers` (`idCustomer`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_CustomersHierarhy_Child`
    FOREIGN KEY (`CustomersChild`)
    REFERENCES `Customers` (`idCustomer`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `Idetities`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Idetities` ;

CREATE TABLE IF NOT EXISTS `Idetities` (
  `CreateDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `idIdentity` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `IdentTypeCode` ENUM('bitid', 'email') NULL DEFAULT NULL,
  `IdentityKey` VARCHAR(50) NULL DEFAULT NULL,
  `EndDate` TIMESTAMP NULL DEFAULT NULL,
  `idCustomer` VARCHAR(34) NOT NULL,
  PRIMARY KEY (`idIdentity`, `idCustomer`),
  UNIQUE INDEX `uq_ident` (`IdentTypeCode` ASC, `IdentityKey` ASC),
  INDEX `idx_Idetities_IdentTypeCode` (`IdentTypeCode` ASC),
  INDEX `fk_customer_idx` (`idCustomer` ASC),
  CONSTRAINT `fk_customer`
    FOREIGN KEY (`idCustomer`)
    REFERENCES `Customers` (`idCustomer`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 9
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `tbl_auth_log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tbl_auth_log` ;

CREATE TABLE IF NOT EXISTS `tbl_auth_log` (
  `s_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `s_address` VARCHAR(34) NOT NULL,
  `s_ip` VARCHAR(46) NOT NULL,
  `s_description` VARCHAR(4069) NULL DEFAULT NULL)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `tbl_nonces`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tbl_nonces` ;

CREATE TABLE IF NOT EXISTS `tbl_nonces` (
  `s_ip` VARCHAR(46) NOT NULL,
  `dt_datetime` DATETIME NOT NULL,
  `s_nonce` VARCHAR(32) NOT NULL,
  `s_address` VARCHAR(34) NULL DEFAULT NULL,
  UNIQUE INDEX `s_nonce` (`s_nonce` ASC),
  INDEX `dt_datetime` (`dt_datetime` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `RootMessages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `RootMessages` ;

CREATE TABLE IF NOT EXISTS `RootMessages` (
  `idRootMessage` INT(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `ReceiveDate` TIMESTAMP NULL,
  `RootMessage` VARCHAR(1024) NULL,
  `idCustomer` VARCHAR(34) NOT NULL,
  PRIMARY KEY (`idRootMessage`, `idCustomer`),
  INDEX `fk_RootMessages_Customers1` (`idCustomer` ASC),
  CONSTRAINT `fk_RootMessages_Customers1`
    FOREIGN KEY (`idCustomer`)
    REFERENCES `Customers` (`idCustomer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `errorMessages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `errorMessages` ;

CREATE TABLE IF NOT EXISTS `errorMessages` (
  `ErrorId` INT(11) NOT NULL AUTO_INCREMENT,
  `Message` VARCHAR(1024) NOT NULL,
  PRIMARY KEY (`ErrorId`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `PropertyType`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `PropertyType` ;

CREATE TABLE IF NOT EXISTS `PropertyType` (
  `CreateDate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `EndDate` TIMESTAMP NULL,
  `PropertyTypeCode` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`PropertyTypeCode`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `CustomerProperty`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `CustomerProperty` ;

CREATE TABLE IF NOT EXISTS `CustomerProperty` (
  `CreateDate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `idCustomer` VARCHAR(34) NOT NULL,
  `PropertyTypeCode` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idCustomer`, `PropertyTypeCode`),
  INDEX `fk_AccessRights_Customers1_idx` (`idCustomer` ASC),
  INDEX `fk_CustomerProperty_PropertyType1_idx` (`PropertyTypeCode` ASC),
  CONSTRAINT `fk_AccessRights_Customers10`
    FOREIGN KEY (`idCustomer`)
    REFERENCES `Customers` (`idCustomer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_CustomerProperty_PropertyType1`
    FOREIGN KEY (`PropertyTypeCode`)
    REFERENCES `PropertyType` (`PropertyTypeCode`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Placeholder table for view `vwActiveTokens`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vwActiveTokens` (`tokenId` INT, `idCustomer` INT, `CreateDate` INT, `EndDate` INT, `UseCount` INT, `MaxUseCount` INT, `AccessRightsId` INT);

-- -----------------------------------------------------
-- Placeholder table for view `vwActualPoint`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vwActualPoint` (`IdBookmark` INT, `CreateDate` INT, `IdOrder` INT, `Quantity` INT, `EndDate` INT, `Latitude` INT, `Longitude` INT, `Link` INT, `Description` INT, `RegionTitle` INT, `CustomPrice` INT, `PriceCurrency` INT, `AdvertiseTitle` INT, `UnlockDate` INT, `State` INT, `IdDroper` INT);

-- -----------------------------------------------------
-- Placeholder table for view `vwCustomerBitid`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vwCustomerBitid` (`idCustomer` INT, `BitId` INT);

-- -----------------------------------------------------
-- Placeholder table for view `vwOrders`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vwOrders` (`idOrder` INT, `CreateDate` INT, `EndDate` INT, `OrderState` INT, `BTCPrice` INT, `PricingDate` INT, `InvoiceAddress` INT, `idCreator` INT, `DeliveryMethod` INT, `InvoiceBalance` INT, `BalanceDate` INT, `OrderHash` INT);

-- -----------------------------------------------------
-- Placeholder table for view `vwBookmarks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vwBookmarks` (`idBookmark` INT, `CreateDate` INT, `idOrder` INT, `Quantity` INT, `EndDate` INT, `Latitude` INT, `Longitude` INT, `Link` INT, `Description` INT, `RegionTitle` INT, `CustomPrice` INT, `PriceCurrency` INT, `AdvertiseTitle` INT, `UnlockDate` INT, `State` INT, `IdDroper` INT, `TargetAddress` INT, `BookmarkHash` INT);

-- -----------------------------------------------------
-- Placeholder table for view `vwActiveRights`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vwActiveRights` (`idRight` INT, `RightCode` INT);

-- -----------------------------------------------------
-- Placeholder table for view `vwActiveAccessRights`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vwActiveAccessRights` (`AccessId` INT, `RightId` INT, `RightCode` INT, `idCustomer` INT, `EndDate` INT, `CreateDate` INT);

-- -----------------------------------------------------
-- Placeholder table for view `vwTokensCheck`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vwTokensCheck` (`tokenId` INT, `idCustomer` INT, `RightCode` INT, `GetTokenKey(tokenId)` INT);

-- -----------------------------------------------------
-- function CreateAccessTokenForCustomer
-- -----------------------------------------------------
DROP function IF EXISTS `CreateAccessTokenForCustomer`;

DELIMITER $$
CREATE FUNCTION CreateAccessTokenForCustomer (pIdCustomer varchar(34), pRightCode varchar(45)  ,pValidDate TIMESTAMP, pMaxUseCount int ) RETURNS int(11)
BEGIN
  DECLARE vResult int default 0;
  DECLARE vCreateDate TIMESTAMP;
  DECLARE vEndDate TIMESTAMP;
  DECLARE vMaxUseCount int;
  DECLARE vAccessRightsId int;
  
  SET vCreateDate = CURRENT_TIMESTAMP;
  SET vEndDate = ifnull(pValidDate,ADDDATE(vCreateDate, 7) );
  SET vMaxUseCount = ifnull(pMaxUseCount, 10);
  SET vAccessRightsId = GetCustomerAccessRight(pIdCustomer, pRightCode);

  IF (vAccessRightsId > 0) THEN
  INSERT INTO AccessTokens
  (idCustomer,  MaxUseCount, AccessRightsId) 
  VALUES (pIdCustomer, vMaxUseCount, vAccessRightsId);
  ELSE
  SET vResult = vAccessRightsId;
  END IF;


RETURN vResult;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure CreateCustomerByBitId
-- -----------------------------------------------------
DROP procedure IF EXISTS `CreateCustomerByBitId`;

DELIMITER $$
CREATE PROCEDURE `CreateCustomerByBitId`(IN pBitId VARCHAR(50), 
 IN pRecoveryMail VARCHAR(50),
 OUT out_id int)
BEGIN

		DECLARE newCust Varchar(34);
		DECLARE error BOOLEAN DEFAULT FALSE;
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET error = TRUE;
		SET autocommit= 0;
		SET out_id = 0;
		SET newCust = pBitId;

		START TRANSACTION;
		INSERT INTO Customers (idCustomer)	
			VALUES	(newCust);
	
		INSERT INTO Idetities( IdentTypeCode, IdentityKey, idCustomer)
			VALUES( 'bitid', pBitId  , newCust );

		
		IF (not (pRecoveryMail = '' OR pRecoveryMail is null) ) THEN
			INSERT INTO Idetities( IdentTypeCode, IdentityKey, idCustomer)
				VALUES( 'email', LOWER(pRecoveryMail)  , newCust );

		END IF;

		IF error THEN
				ROLLBACK;
				SET out_id = -1;
		ELSE
				COMMIT;   
		END IF;
		SET autocommit= 1;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure RegisterOrder4HotPoint
-- -----------------------------------------------------
DROP procedure IF EXISTS `RegisterOrder4HotPoint`;

DELIMITER $$
CREATE PROCEDURE RegisterOrder4HotPoint (IN pIdCustomer varchar(34), IN pBTCPrice decimal(12,8),
IN pBookMarkId int(11), IN pInvoiceAddress varchar(34)  , OUT out_id int, OUT pOrderId int )
BEGIN
		DECLARE vUpdateCnt int default 0;
		DECLARE error BOOLEAN DEFAULT FALSE;
		DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET error = TRUE;
        
		SET autocommit= 0;        
		SET out_id = 0;
        
		START TRANSACTION;
        
        INSERT INTO Orders
			(`OrderState`,
			`BTCPrice`,
			`InvoiceAddress`,
            `idCreator`
            )
			VALUES
			('WaitForPayment',
            pBTCPrice,
            pInvoiceAddress,
            pIdCustomer);
		
		SELECT LAST_INSERT_ID() INTO pOrderId;
        
		UPDATE Bookmarks 
		SET 
			IdOrder = pOrderId,
			State = 'PreOrdered'
		WHERE
			IdBookmark = pBookMarkId
        AND IdOrder IS NULL
        AND UnlockDate IS NULL;
                
		SELECT ROW_COUNT() INTO vUpdateCnt;
               
		IF error THEN
				ROLLBACK;
				SET out_id = -1;
		ELSE
			begin
				IF (vUpdateCnt = 1) then					
					COMMIT;
				else
					rollback;
                    SET pOrderId = 0;
                    SET out_id = 101;
				END IF;
            
            end;
		END IF;
		SET autocommit= 1;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure CancelOrder
-- -----------------------------------------------------
DROP procedure IF EXISTS `CancelOrder`;

DELIMITER $$
CREATE  PROCEDURE CancelOrder(IN pOrderId int, OUT pResult int)
BEGIN

UPDATE `Orders`
SET
`OrderState` = 'Canceled', 
`EndDate` = NOW()
WHERE `idOrder` = pOrderId;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure FinishOrder
-- -----------------------------------------------------
DROP procedure IF EXISTS `FinishOrder`;

DELIMITER $$
CREATE  PROCEDURE FinishOrder(IN pOrderId int, OUT pResult int)
BEGIN

UPDATE `Orders`
SET
`OrderState` = 'Finished', 
`EndDate` = NOW()
WHERE `idOrder` = pOrderId;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- function UpdateHotBalance
-- -----------------------------------------------------
DROP function IF EXISTS `UpdateHotBalance`;

DELIMITER $$
CREATE  FUNCTION UpdateHotBalance( pOrderId int, pBalance decimal(12,8)) RETURNS boolean
BEGIN		
    DECLARE vStateChanged bool default 0;
    DECLARE vPrice decimal(12,8);
    DECLARE vInvoiceBalance decimal(12,8);
    
    SELECT BTCPrice, InvoiceBalance
	INTO vPrice , vInvoiceBalance
	FROM Orders 
	WHERE idOrder  = pOrderId;
	
    IF (pBalance = vInvoiceBalance) THEN
				SET vStateChanged = 0;
			ELSE 
			    SET vStateChanged = 1;
                
			
				IF (pBalance >= vPrice) THEN
					update Orders
						SET InvoiceBalance = pBalance , BalanceDate = NOW(), OrderState = 'Paid' 
						WHERE idOrder = pOrderId;
				ELSE
					update Orders
						SET InvoiceBalance = pBalance , BalanceDate = NOW()
						WHERE idOrder = pOrderId;               
                END if;
            end if;
	RETURN vStateChanged;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- function GetHashByOrderId
-- -----------------------------------------------------
DROP function IF EXISTS `GetHashByOrderId`;

DELIMITER $$
CREATE FUNCTION GetHashByOrderId(pIdOrder integer) RETURNS varchar(32) CHARSET utf8
BEGIN
	DECLARE vidOrder int;
	DECLARE vOrderState varchar(20);
	DECLARE vBTCPrice  decimal(12,8);
	DECLARE vInvoiceAddress varchar(34);
	DECLARE vInvoiceBalance decimal(12,8);
	DECLARE vResult varchar(32);

SELECT 
    idOrder,
    OrderState,
    BTCPrice,
    InvoiceAddress,
    InvoiceBalance
INTO vidOrder , vOrderState , vBTCPrice , vInvoiceAddress , vInvoiceBalance FROM
    Orders
WHERE
    idOrder = pIdOrder;
	
    SELECT MD5(CONCAT(vidOrder, vOrderState,vBTCPrice,  vInvoiceAddress,vInvoiceBalance))
    INTO vResult;
    
RETURN vResult;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure GetCustomerByActiveToken
-- -----------------------------------------------------
DROP procedure IF EXISTS `GetCustomerByActiveToken`;

DELIMITER $$
CREATE PROCEDURE `GetCustomerByActiveToken`(IN pToken varchar(1024), OUT pIdCustomer varchar(34),OUT pResult int)
BEGIN
	SET pResult = 0;
    
	SELECT idCustomer
    INTO pIdCustomer
	FROM vwActiveTokens
	WHERE  Digest = pToken;
    
    IF (pIdCustomer = NULL) THEN
		SET pResult = 501;
    END IF;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- function GetHashByBookmarkId
-- -----------------------------------------------------
DROP function IF EXISTS `GetHashByBookmarkId`;

DELIMITER $$
CREATE FUNCTION GetHashByBookmarkId (pIdBookmark int) RETURNS varchar(32)
BEGIN
  DECLARE vResult varchar(32) default 'Error';
 
SELECT MD5(CONCAT(IFNULL(`idBookmark`,0),
    IFNULL(`CreateDate`,0),
    IFNULL(`idOrder`,0),
    IFNULL(`Quantity`,0),
    IFNULL(`EndDate`,0),
    IFNULL(`Latitude`,0),
    IFNULL(`Longitude`,0),
    IFNULL(`Link`,0),
    IFNULL(`Description`,0),
    IFNULL(`RegionTitle`,0),
    IFNULL(`CustomPrice`,0),
    IFNULL(`PriceCurrency`,0),
    IFNULL(`AdvertiseTitle`,0),
    IFNULL(`UnlockDate`,0),
    IFNULL(`State`,0),
    IFNULL(`IdDroper`,0),
    IFNULL(`TargetAddress`,0)))
INTO vResult
FROM Bookmarks 
WHERE idBookmark = pIdBookmark;


RETURN vResult;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- function GetPubKeyByCustomer
-- -----------------------------------------------------
DROP function IF EXISTS `GetPubKeyByCustomer`;

DELIMITER $$
CREATE FUNCTION GetPubKeyByCustomer (pIdCustomer varchar(34)) RETURNS varchar(112)
BEGIN
	DECLARE vResult varchar(112);
  
SELECT xPubKey
INTO 	vResult 
FROM    PubKeys
WHERE idOwner = pIdCustomer 
AND (now() <= coalesce( EndDate,now())) AND (now()>CreateDate);

RETURN vResult;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- function AddInvoiceAddressToXPub
-- -----------------------------------------------------
DROP function IF EXISTS `AddInvoiceAddressToXPub`;

DELIMITER $$
CREATE FUNCTION AddInvoiceAddressToXPub (pXPubKey varchar(112), pInvoiceAddres varchar(34), pBalance decimal(12,8)) RETURNS int(11)
BEGIN
	DECLARE vIdPubKey int;
    DECLARE vAddressId int;
    
    SELECT idPubKey
    INTO vIdPubKey
    FROM PubKeys
    WHERE xPubKey = pXPubKey;
    
    INSERT INTO InvoiceAddress (idPubKey, InvoiceAddres, CreateDate, Balance) 
	VALUES (vIdPubKey, pInvoiceAddres , DEFAULT, pBalance);
    
	RETURN LAST_INSERT_ID();
END$$

DELIMITER ;

-- -----------------------------------------------------
-- function GetInvoiceAddressCountByXPub
-- -----------------------------------------------------
DROP function IF EXISTS `GetInvoiceAddressCountByXPub`;

DELIMITER $$
CREATE FUNCTION GetInvoiceAddressCountByXPub(pXPubKey varchar(112)) RETURNS int(11)
BEGIN
	DECLARE vResult int;
    DECLARE vPubPK int;
    
	SELECT idPubKey
	INTO vPubPK 
	FROM PubKeys
	WHERE xPubKey = pXPubKey;

    
    SELECT count(*)
    INTO vResult
    FROM InvoiceAddress
    WHERE idPubkey = vPubPK;
    
    RETURN vResult ;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- function setBookmarkSaled
-- -----------------------------------------------------
DROP function IF EXISTS `setBookmarkSaled`;

DELIMITER $$
CREATE FUNCTION setBookmarkSaled(pIdBookmark int, pIdOrder int) returns int
BEGIN
DECLARE vResult int default 0;
DECLARE vUnfinishedPointCount int default 0;

UPDATE Bookmarks
SET State = 'Saled'
WHERE idBookmark = pIdBookmark ;

SELECT COUNT(1)
INTO  vUnfinishedPointCount
FROM Bookmarks
WHERE idOrder = pIdOrder 
AND State not in ('Saled','Lost');

IF NOT(vUnfinishedPointCount>0) THEN
	CALL FinishOrder(pIdOrder, vResult);
END IF;

RETURN vResult;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- function GetCustomerAccessRight
-- -----------------------------------------------------
DROP function IF EXISTS `GetCustomerAccessRight`;

DELIMITER $$
CREATE FUNCTION GetCustomerAccessRight (pIdCustomer varchar(34), pRightCode varchar(45)) RETURNS INTEGER
BEGIN
	DECLARE vResult INTEGER default 0;
  
  SELECT vwActiveAccessRights.AccessId
  INTO vResult
  FROM vwActiveAccessRights
  WHERE RightCode = pRightCode AND idCustomer = pIdCustomer;

  RETURN vResult;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- function AddARightToCustomer
-- -----------------------------------------------------
DROP function IF EXISTS `AddARightToCustomer`;

DELIMITER $$
CREATE FUNCTION AddARightToCustomer (pIdCustomer varchar(34), pIsRoot boolean, pCustomerTo varchar(34), pRightCode varchar(45)    ) RETURNS INTEGER
BEGIN
  DECLARE vIdRight int default 0;
  DECLARE vIsAccessExists int default 0;

  SELECT idRight 
  INTO vIdRight
  FROM vwActiveRights
  WHERE RightCode = pRightCode limit 1;

  IF (vIdRight = 0) THEN
    INSERT INTO Rights
      (RightCode) 
      VALUES (RightCode);
      
      SELECT LAST_INSERT_ID() INTO vIdRight;
  END IF;
  
  IF NOT(pIsRoot)
  THEN
		begin
			SET vIsAccessExists = GetCustomerAccessRight(pIdCustomer, 'ADD_ACCESS_RIGHT');
			IF (vIsAccessExists = 0) THEN RETURN -501; END IF;
		end;
  END IF;
  
  INSERT INTO AccessRights (RightId,idCustomer) 
  VALUES (vIdRight, pCustomerTo);

  RETURN 0;
  END$$

DELIMITER ;

-- -----------------------------------------------------
-- function GetTokenKey
-- -----------------------------------------------------
DROP function IF EXISTS `GetTokenKey`;

DELIMITER $$
CREATE FUNCTION GetTokenKey(vTokenId int) RETURNS varchar(128) CHARSET utf8
BEGIN
	declare vResult varchar(128) ;
  
  SELECT MD5(concat(tokenId,idCustomer, ifnull(EndDate,0), MaxUseCount, AccessRightsId)) 
  INTO vResult
  FROM AccessTokens
  WHERE tokenId = vTokenId;
	
  
  RETURN vResult;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure RegisterNewPoint
-- -----------------------------------------------------
DROP procedure IF EXISTS `RegisterNewPoint`;

DELIMITER $$
CREATE PROCEDURE `RegisterNewPoint`(IN pIdCustomer varchar(34), IN pLatitude decimal(12,8), IN pLongitude decimal(12,8), IN pLink varchar(256) ,
IN pDescription varchar(1024), IN pRegionTitle varchar(256),IN pPrice decimal(12,8), IN pAdvertiseTitle varchar(45) ,OUT out_id int, OUT pBookMarkId int )
BEGIN


INSERT INTO `Bookmarks`
(`Latitude`,
`Longitude`,
`Link`,
`Description`,
`RegionTitle`,
`CustomPrice`,
`AdvertiseTitle`,
`IdDroper`)
VALUES
( pLatitude,
pLongitude,
pLink,
pDescription,
pRegionTitle,
pPrice,
pAdvertiseTitle,
pIdCustomer)
;
SET  out_id = 0;
		SELECT LAST_INSERT_ID() INTO pBookMarkId;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- View `vwActiveTokens`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `vwActiveTokens` ;
DROP TABLE IF EXISTS `vwActiveTokens`;
CREATE  OR REPLACE VIEW `vwActiveTokens` AS
    SELECT 
        `AccessTokens`.`tokenId` AS `tokenId`,
        `AccessTokens`.`idCustomer` AS `idCustomer`,
        `AccessTokens`.`CreateDate` AS `CreateDate`,
        `AccessTokens`.`EndDate` AS `EndDate`,
        `AccessTokens`.`UseCount` AS `UseCount`,
        `AccessTokens`.`MaxUseCount` AS `MaxUseCount`,
        `AccessTokens`.`AccessRightsId` AS `AccessRightsId`
    FROM
        `AccessTokens`
    WHERE
        (NOW() BETWEEN `AccessTokens`.`CreateDate` AND COALESCE(`AccessTokens`.`EndDate`, NOW()));

-- -----------------------------------------------------
-- View `vwActualPoint`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `vwActualPoint` ;
DROP TABLE IF EXISTS `vwActualPoint`;
CREATE  OR REPLACE VIEW `vwActualPoint` AS
    SELECT 
        `Bookmarks`.`IdBookmark` AS `IdBookmark`,
        `Bookmarks`.`CreateDate` AS `CreateDate`,
        `Bookmarks`.`IdOrder` AS `IdOrder`,
        `Bookmarks`.`Quantity` AS `Quantity`,
        `Bookmarks`.`EndDate` AS `EndDate`,
        `Bookmarks`.`Latitude` AS `Latitude`,
        `Bookmarks`.`Longitude` AS `Longitude`,
        `Bookmarks`.`Link` AS `Link`,
        `Bookmarks`.`Description` AS `Description`,
        `Bookmarks`.`RegionTitle` AS `RegionTitle`,
        `Bookmarks`.`CustomPrice` AS `CustomPrice`,
        `Bookmarks`.`PriceCurrency` AS `PriceCurrency`,
        `Bookmarks`.`AdvertiseTitle` AS `AdvertiseTitle`,
        `Bookmarks`.`UnlockDate` AS `UnlockDate`,
        `Bookmarks`.`State` AS `State`,
        `Bookmarks`.`IdDroper` AS `IdDroper`
    FROM
        `Bookmarks`
    WHERE
        (ISNULL(`Bookmarks`.`IdOrder`)
            AND (NOW() <= COALESCE(`Bookmarks`.`EndDate`, NOW())));

-- -----------------------------------------------------
-- View `vwCustomerBitid`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `vwCustomerBitid` ;
DROP TABLE IF EXISTS `vwCustomerBitid`;
CREATE  OR REPLACE VIEW `vwCustomerBitid` AS
    SELECT 
        `Customers`.`idCustomer` AS `idCustomer`,
        `Idetities`.`IdentityKey` AS `BitId`
    FROM
        (`Customers`
        JOIN `Idetities` ON (((`Idetities`.`IdentTypeCode` = 'bitid')
            AND (`Idetities`.`idCustomer` = `Customers`.`idCustomer`))));

-- -----------------------------------------------------
-- View `vwOrders`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `vwOrders` ;
DROP TABLE IF EXISTS `vwOrders`;
CREATE  OR REPLACE VIEW vwOrders AS
    SELECT 
        idOrder,
        CreateDate,
        EndDate,
        OrderState,
        BTCPrice,
        PricingDate,
        InvoiceAddress,
        idCreator,
        DeliveryMethod,
        InvoiceBalance,
        BalanceDate,
        GETHASHBYORDERID(idOrder) AS OrderHash
    FROM
        Orders;

-- -----------------------------------------------------
-- View `vwBookmarks`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `vwBookmarks` ;
DROP TABLE IF EXISTS `vwBookmarks`;
CREATE  OR REPLACE VIEW `vwBookmarks` AS
SELECT `Bookmarks`.`idBookmark`,
    `Bookmarks`.`CreateDate`,
    `Bookmarks`.`idOrder`,
    `Bookmarks`.`Quantity`,
    `Bookmarks`.`EndDate`,
    `Bookmarks`.`Latitude`,
    `Bookmarks`.`Longitude`,
    `Bookmarks`.`Link`,
    `Bookmarks`.`Description`,
    `Bookmarks`.`RegionTitle`,
    `Bookmarks`.`CustomPrice`,
    `Bookmarks`.`PriceCurrency`,
    `Bookmarks`.`AdvertiseTitle`,
    `Bookmarks`.`UnlockDate`,
    `Bookmarks`.`State`,
    `Bookmarks`.`IdDroper`,
    `Bookmarks`.`TargetAddress`,
     GetHashByBookmarkId(idBookmark) as BookmarkHash
FROM `Bookmarks`;

-- -----------------------------------------------------
-- View `vwActiveRights`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `vwActiveRights` ;
DROP TABLE IF EXISTS `vwActiveRights`;
CREATE  OR REPLACE VIEW `vwActiveRights` AS
  SELECT idRight, RightCode 
FROM Rights  WHERE (NOW()>`CreateDate`) AND (NOW() <= COALESCE(`EndDate`, NOW()));

-- -----------------------------------------------------
-- View `vwActiveAccessRights`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `vwActiveAccessRights` ;
DROP TABLE IF EXISTS `vwActiveAccessRights`;
CREATE  OR REPLACE VIEW `vwActiveAccessRights` AS
   SELECT AccessRights.AccessId, AccessRights.RightId, vwActiveRights.RightCode , AccessRights.idCustomer, AccessRights.EndDate, AccessRights.CreateDate 
  FROM AccessRights 
  JOIN vwActiveRights ON (RightId = idRight)
  WHERE (NOW()>`CreateDate`) AND (NOW() <= COALESCE(`EndDate`, NOW()));

-- -----------------------------------------------------
-- View `vwTokensCheck`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `vwTokensCheck` ;
DROP TABLE IF EXISTS `vwTokensCheck`;
CREATE  OR REPLACE VIEW `vwTokensCheck` AS
SELECT tokenId,vwActiveTokens.idCustomer, RightCode, GetTokenKey(tokenId)
FROM vwActiveTokens
JOIN vwActiveAccessRights ON (AccessRightsId = AccessId);

-- -----------------------------------------------------
-- Data for table `Customers`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `Customers` (`idCustomer`, `CreateDate`, `isBaned`, `ChangeDate`, `Preferences`) VALUES ('1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN', DEFAULT, 0, NULL, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `Rights`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `Rights` (`idRight`, `CreateDate`, `EndDate`, `RightCode`) VALUES (DEFAULT, DEFAULT, NULL, 'CREATE_TOKEN');
INSERT INTO `Rights` (`idRight`, `CreateDate`, `EndDate`, `RightCode`) VALUES (DEFAULT, DEFAULT, NULL, 'ADD_POINT');
INSERT INTO `Rights` (`idRight`, `CreateDate`, `EndDate`, `RightCode`) VALUES (DEFAULT, DEFAULT, NULL, 'ADD_ACCESS_RIGHT');
INSERT INTO `Rights` (`idRight`, `CreateDate`, `EndDate`, `RightCode`) VALUES (DEFAULT, DEFAULT, NULL, 'GET_POINT_STATE');
INSERT INTO `Rights` (`idRight`, `CreateDate`, `EndDate`, `RightCode`) VALUES (DEFAULT, DEFAULT, NULL, 'SET_POINT_STATE');
INSERT INTO `Rights` (`idRight`, `CreateDate`, `EndDate`, `RightCode`) VALUES (DEFAULT, DEFAULT, NULL, 'GET_POINTS');

COMMIT;


-- -----------------------------------------------------
-- Data for table `PubKeys`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `PubKeys` (`idPubKey`, `idOwner`, `CreateDate`, `xPubKey`, `EndDate`) VALUES (DEFAULT, '1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN', '2016-03-30 03:41:28', 'xpub661MyMwAqRbcGgwGvP3MbDaUKTEpppjwJZoqZLGS59ystwUKVNhbueXEwRH19nTFm9jFC2fZtgcmkj8a77de1HudQ8Uw4sdq9pA4deTMVdh', NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `Bookmarks`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `Bookmarks` (`idBookmark`, `CreateDate`, `idOrder`, `Quantity`, `EndDate`, `Latitude`, `Longitude`, `Link`, `Description`, `RegionTitle`, `CustomPrice`, `PriceCurrency`, `AdvertiseTitle`, `UnlockDate`, `State`, `IdDroper`, `TargetAddress`) VALUES (DEFAULT, '2016-03-30 03:41:28', NULL, 1, NULL, 50.4594524, 30.4213277, 'https://fastfen.club/cloud/index.php/s/9Fj4xrS1shlRs4k', '', 'Киев, м.Берестейская', 50, DEFAULT, 'Папироса ганджубаса', NULL, 'Published', '1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN', '19obTLBfmhyiirETcThD1ydMkWGFLuDaqe');
INSERT INTO `Bookmarks` (`idBookmark`, `CreateDate`, `idOrder`, `Quantity`, `EndDate`, `Latitude`, `Longitude`, `Link`, `Description`, `RegionTitle`, `CustomPrice`, `PriceCurrency`, `AdvertiseTitle`, `UnlockDate`, `State`, `IdDroper`, `TargetAddress`) VALUES (DEFAULT, '2016-03-30 03:41:28', NULL, 2, NULL, 50.4590367, 30.6299849, 'https://fastfen.club/cloud/index.php/s/hthgwc2vPGLA9QT', NULL, 'Киев, м.Черниговская', 100, DEFAULT, 'Пол пакета ганджубаса', NULL, 'Published', '1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN', '19obTLBfmhyiirETcThD1ydMkWGFLuDaqe');
INSERT INTO `Bookmarks` (`idBookmark`, `CreateDate`, `idOrder`, `Quantity`, `EndDate`, `Latitude`, `Longitude`, `Link`, `Description`, `RegionTitle`, `CustomPrice`, `PriceCurrency`, `AdvertiseTitle`, `UnlockDate`, `State`, `IdDroper`, `TargetAddress`) VALUES (DEFAULT, '2016-03-30 03:41:28', NULL, 1, NULL, 50.4620083, 30.4174780, 'https://fastfen.club/cloud/index.php/s/1Xhds3PuEpmK1We', NULL, 'Киев, м.Берестейская', 100, DEFAULT, '0,25 амфетамина', NULL, 'Published', '1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN', '19obTLBfmhyiirETcThD1ydMkWGFLuDaqe');
INSERT INTO `Bookmarks` (`idBookmark`, `CreateDate`, `idOrder`, `Quantity`, `EndDate`, `Latitude`, `Longitude`, `Link`, `Description`, `RegionTitle`, `CustomPrice`, `PriceCurrency`, `AdvertiseTitle`, `UnlockDate`, `State`, `IdDroper`, `TargetAddress`) VALUES (DEFAULT, '2016-03-30 03:41:28', NULL, 2, NULL, 50.4569520, 30.4346062, 'https://fastfen.club/cloud/index.php/s/rD7rIToXz1gXGHZ', NULL, 'Киев, м.Шулявка', 100, DEFAULT, 'Пол пакета ганджубаса', NULL, 'Published', '1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN', '19obTLBfmhyiirETcThD1ydMkWGFLuDaqe');

COMMIT;


-- -----------------------------------------------------
-- Data for table `Idetities`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `Idetities` (`CreateDate`, `idIdentity`, `IdentTypeCode`, `IdentityKey`, `EndDate`, `idCustomer`) VALUES (DEFAULT, DEFAULT, 'bitid', '1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN', NULL, '1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN');

COMMIT;


-- -----------------------------------------------------
-- Data for table `errorMessages`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `errorMessages` (`ErrorId`, `Message`) VALUES (101, 'Error when assign hot point to order');
INSERT INTO `errorMessages` (`ErrorId`, `Message`) VALUES (501, 'User don\'t has a needed access rights');
INSERT INTO `errorMessages` (`ErrorId`, `Message`) VALUES (601, 'Customer not found');

COMMIT;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
-- begin attached script 'PrepareTestData'
SELECT AddARightToCustomer('1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN', true, '1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN', 'ADD_POINT');
SELECT CreateAccessTokenForCustomer('1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN','ADD_POINT', null, null);

-- end attached script 'PrepareTestData'

-- MySQL Workbench Forward Engineering

-- begin attached script 'script1'
SET NAMES 'UTF8';
-- end attached script 'script1'
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema Ident
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema Ident
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `fastksjq_fastfen1` DEFAULT CHARACTER SET utf8 ;
USE `fastksjq_fastfen1` ;

-- -----------------------------------------------------
-- Table `Customers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Customers` ;

CREATE TABLE IF NOT EXISTS `Customers` (
  `idCustomer` VARCHAR(34) NOT NULL,
  `CreateDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isBaned` BIT(1) NOT NULL DEFAULT b'0',
  `ChangeDate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idCustomer`),
  UNIQUE INDEX `idCustomer_UNIQUE` (`idCustomer` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `Orders`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Orders` ;

CREATE TABLE IF NOT EXISTS `Orders` (
  `IdOrder` INT(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EndDate` TIMESTAMP NULL,
  `OrderState` ENUM('Created', 'Confirmed') NULL DEFAULT 'Created',
  `idSaller` VARCHAR(34) NULL,
  `BTCPrice` DECIMAL(12,8) NOT NULL,
  `PricingDate` TIMESTAMP NULL,
  `InvoiceAddress` VARCHAR(34) NOT NULL,
  `idCreator` VARCHAR(34) NOT NULL,
  PRIMARY KEY (`IdOrder`, `idCreator`),
  INDEX `fk_Orders_Customers1_idx` (`idSaller` ASC),
  INDEX `fk_Orders_CustomersCreator_idx` (`idCreator` ASC),
  CONSTRAINT `fk_Orders_CustomersSaller`
    FOREIGN KEY (`idSaller`)
    REFERENCES `Customers` (`idCustomer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Orders_CustomersCreator`
    FOREIGN KEY (`idCreator`)
    REFERENCES `Customers` (`idCustomer`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `Bookmarks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Bookmarks` ;

CREATE TABLE IF NOT EXISTS `Bookmarks` (
  `IdBookmark` INT(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `IdOrder` INT(11) NULL DEFAULT NULL,
  `Quantity` DECIMAL(12,2) NOT NULL,
  `EndDate` TIMESTAMP NULL DEFAULT NULL,
  `Latitude` DECIMAL(12,8) NOT NULL,
  `Longitude` DECIMAL(12,8) NOT NULL,
  `Link` VARCHAR(256) NULL DEFAULT NULL,
  `Description` VARCHAR(1024) NULL DEFAULT NULL,
  `RegionTitle` VARCHAR(45) NULL DEFAULT NULL,
  `CustomPrice` DECIMAL(12,2) NULL DEFAULT NULL,
  `PriceCurrency` ENUM('BTC', 'UAH') NOT NULL DEFAULT 'UAH',
  `AdvertiseTitle` VARCHAR(45) NOT NULL,
  `UnlockDate` TIMESTAMP NULL,
  `State` ENUM('Preparing', 'Checking', 'Rejected', 'Ready', 'Published', 'Saled', 'Lost') CHARACTER SET 'ucs2' NULL DEFAULT 'Preparing',
  `IdDroper` VARCHAR(34) NOT NULL,
  `TargetAddress` VARCHAR(34) NOT NULL,
  PRIMARY KEY (`IdBookmark`, `IdDroper`),
  UNIQUE INDEX `Latitude` (`Latitude` ASC, `Longitude` ASC),
  INDEX `fk_droper_idx` (`IdDroper` ASC),
  INDEX `fk_OrderId_idx` (`IdOrder` ASC),
  CONSTRAINT `fk_droper`
    FOREIGN KEY (`IdDroper`)
    REFERENCES `Customers` (`idCustomer`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_OrderId`
    FOREIGN KEY (`IdOrder`)
    REFERENCES `Orders` (`IdOrder`)
    ON DELETE NO ACTION
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
  INDEX `fk_Customers_has_Customers_Customers1_idx` (`CustomersParent` ASC),
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
-- Table `Rights`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Rights` ;

CREATE TABLE IF NOT EXISTS `Rights` (
  `CreateDate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `EndDate` TIMESTAMP NULL,
  `RightCode` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`RightCode`));


-- -----------------------------------------------------
-- Table `AccessRights`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `AccessRights` ;

CREATE TABLE IF NOT EXISTS `AccessRights` (
  `CreateDate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `idCustomer` VARCHAR(34) NOT NULL,
  `RightCode` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idCustomer`, `RightCode`),
  INDEX `fk_AccessRights_Customers1_idx` (`idCustomer` ASC),
  INDEX `fk_AccessRights_Rights1_idx` (`RightCode` ASC),
  CONSTRAINT `fk_AccessRights_Customers1`
    FOREIGN KEY (`idCustomer`)
    REFERENCES `Customers` (`idCustomer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_AccessRights_Rights1`
    FOREIGN KEY (`RightCode`)
    REFERENCES `Rights` (`RightCode`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


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
  CONSTRAINT `fk_RootMessages_Customers1`
    FOREIGN KEY (`idCustomer`)
    REFERENCES `Customers` (`idCustomer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

USE `fastksjq_fastfen1` ;

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
CREATE TABLE IF NOT EXISTS `vwOrders` (`IdOrder` INT, `CreateDate` INT, `EndDate` INT, `OrderState` INT, `idSaller` INT, `BTCPrice` INT, `InvoiceAddress` INT, `idCreator` INT);

-- -----------------------------------------------------
-- procedure CreateCustomerByBitId
-- -----------------------------------------------------

USE `fastksjq_fastfen1`;
DROP procedure IF EXISTS `CreateCustomerByBitId`;

DELIMITER $$
USE `fastksjq_fastfen1`$$
CREATE DEFINER=`fastksjq_fastfen1`@`%` PROCEDURE `CreateCustomerByBitId`(IN pBitId VARCHAR(50), 
 IN pRecoveryMail VARCHAR(50),
 OUT out_id VARCHAR(34),
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
		
		SET out_id  =  newCust;

		IF error THEN
				ROLLBACK;
				SET out_id = '';
				SET error_msg = 'Error';
		ELSE
				COMMIT;   
		END IF;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- function getActiveOrderByCustomer
-- -----------------------------------------------------

USE `fastksjq_fastfen1`;
DROP function IF EXISTS `getActiveOrderByCustomer`;

DELIMITER $$
USE `fastksjq_fastfen1`$$
CREATE DEFINER=`fastksjq_fastfen1`@`%` FUNCTION `getActiveOrderByCustomer`(idCustomeromer varchar(34)) RETURNS int(11)
BEGIN

RETURN 1;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- View `vwActualPoint`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `vwActualPoint` ;
DROP TABLE IF EXISTS `vwActualPoint`;
USE `fastksjq_fastfen1`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`fastksjq_fastfen1`@`%` SQL SECURITY DEFINER VIEW `vwActualPoint` AS select `Bookmarks`.`IdBookmark` AS `IdBookmark`,`Bookmarks`.`CreateDate` AS `CreateDate`,`Bookmarks`.`IdOrder` AS `IdOrder`,`Bookmarks`.`Quantity` AS `Quantity`,`Bookmarks`.`EndDate` AS `EndDate`,`Bookmarks`.`Latitude` AS `Latitude`,`Bookmarks`.`Longitude` AS `Longitude`,`Bookmarks`.`Link` AS `Link`,`Bookmarks`.`Description` AS `Description`,`Bookmarks`.`RegionTitle` AS `RegionTitle`,`Bookmarks`.`CustomPrice` AS `CustomPrice`,`Bookmarks`.`PriceCurrency` AS `PriceCurrency`,`Bookmarks`.`AdvertiseTitle` AS `AdvertiseTitle`,`Bookmarks`.`UnlockDate` AS `UnlockDate`,`Bookmarks`.`State` AS `State`,`Bookmarks`.`IdDroper` AS `IdDroper` from `Bookmarks` where (isnull(`Bookmarks`.`IdOrder`) and (now() <= coalesce(`Bookmarks`.`EndDate`,now())));

-- -----------------------------------------------------
-- View `vwCustomerBitid`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `vwCustomerBitid` ;
DROP TABLE IF EXISTS `vwCustomerBitid`;
USE `fastksjq_fastfen1`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`fastksjq_fastfen1`@`%` SQL SECURITY DEFINER VIEW `vwCustomerBitid` AS select `Customers`.`idCustomer` AS `idCustomer`,`Idetities`.`IdentityKey` AS `BitId` from (`Customers` join `Idetities` on(((`Idetities`.`IdentTypeCode` = 'bitid') and (`Idetities`.`idCustomer` = `Customers`.`idCustomer`))));

-- -----------------------------------------------------
-- View `vwOrders`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `vwOrders` ;
DROP TABLE IF EXISTS `vwOrders`;
USE `fastksjq_fastfen1`;
CREATE  OR REPLACE VIEW `vwOrders` AS
SELECT 
    IdOrder,
    CreateDate,
    EndDate,
    OrderState,
    idSaller,
    BTCPrice,
    InvoiceAddress,
    idCreator
FROM
    Orders;

-- -----------------------------------------------------
-- Data for table `Customers`
-- -----------------------------------------------------
START TRANSACTION;
USE `fastksjq_fastfen1`;
INSERT INTO `Customers` (`idCustomer`, `CreateDate`, `isBaned`, `ChangeDate`) VALUES ('1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN', DEFAULT, 0, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `Bookmarks`
-- -----------------------------------------------------
START TRANSACTION;
USE `fastksjq_fastfen1`;
INSERT INTO `Bookmarks` (`IdBookmark`, `CreateDate`, `IdOrder`, `Quantity`, `EndDate`, `Latitude`, `Longitude`, `Link`, `Description`, `RegionTitle`, `CustomPrice`, `PriceCurrency`, `AdvertiseTitle`, `UnlockDate`, `State`, `IdDroper`, `TargetAddress`) VALUES (DEFAULT, '2016-03-30 03:41:28', NULL, 1, NULL, 50.11, 30.11, 'test', 'test', 'test', 150, DEFAULT, 'Fen', NULL, 'Published', '1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN', '1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN');

COMMIT;


-- -----------------------------------------------------
-- Data for table `Idetities`
-- -----------------------------------------------------
START TRANSACTION;
USE `fastksjq_fastfen1`;
INSERT INTO `Idetities` (`CreateDate`, `idIdentity`, `IdentTypeCode`, `IdentityKey`, `EndDate`, `idCustomer`) VALUES (DEFAULT, DEFAULT, 'bitid', '1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN', NULL, '1Fk8Q3LWcEaqcfpQp6Zv4jNJwdUMutttmN');

COMMIT;

-- begin attached script 'script'

-- end attached script 'script'

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

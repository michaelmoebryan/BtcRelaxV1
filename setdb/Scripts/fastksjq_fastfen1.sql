-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
CREATE SCHEMA `Ident` DEFAULT CHARACTER SET utf32 ;

-- Host: localhost:3306
-- Generation Time: Dec 05, 2016 at 08:46 AM
-- Server version: 10.1.18-MariaDB-cll-lve
-- PHP Version: 5.4.31

USE `Ident`; 

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+03:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `fastksjq_fastfen1`
--

DELIMITER $$
--
-- Procedures
--
CREATE PROCEDURE `CreateCustomerByBitId`(IN pBitId VARCHAR(50), 
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

-- --------------------------------------------------------

--
-- Table structure for table `Bookmarks`
--

CREATE TABLE IF NOT EXISTS `Bookmarks` (
  `IdBookmark` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` datetime DEFAULT NULL,
  `IdOrder` int(11) DEFAULT NULL,
  `Quantity` decimal(12,2) NOT NULL,
  `EndDate` datetime DEFAULT NULL,
  `Latitude` decimal(12,8) NOT NULL,
  `Longitude` decimal(12,8) NOT NULL,
  `Link` varchar(256) DEFAULT NULL,
  `Description` varchar(1024) DEFAULT NULL,
  `RegionTitle` varchar(45) DEFAULT NULL,
  `CustomPrice` decimal(12,2) DEFAULT NULL,
  `PriceCurrency` ENUM('BTC', 'UAH') NOT NULL DEFAULT 'UAH' ,
  `AdvertiseTitle` varchar(45) NOT NULL,
  `UnlockDate` datetime DEFAULT NULL,
  `State` enum('Preparing','Checking','Rejected','Ready','Published','Saled','Lost') CHARACTER SET ucs2 DEFAULT 'Preparing',
  `IdDroper` varchar(34) NOT NULL,
  PRIMARY KEY (`IdBookmark`),
  UNIQUE KEY `Latitude` (`Latitude`,`Longitude`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `Customers`
--

CREATE TABLE IF NOT EXISTS `Customers` (
  `idCustomer` varchar(34) NOT NULL,
  `CreateDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isBaned` bit(1) NOT NULL DEFAULT b'0',
  `ChangeDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idCustomer`),
  UNIQUE KEY `idCustomer_UNIQUE` (`idCustomer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `CustomersHierarhy`
--

CREATE TABLE IF NOT EXISTS `CustomersHierarhy` (
  `CustomersParent` varchar(34) NOT NULL,
  `CustomersChild` varchar(34) NOT NULL,
  `CreateDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CustomersParent`,`CustomersChild`),
  KEY `fk_Customers_has_Customers_Customers1_idx` (`CustomersParent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Idetities`
--

CREATE TABLE IF NOT EXISTS `Idetities` (
  `CreateDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `idIdentity` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IdentTypeCode` enum('bitid','email') DEFAULT NULL,
  `IdentityKey` varchar(50) DEFAULT NULL,
  `EndDate` timestamp NULL DEFAULT NULL,
  `idCustomer` varchar(34) NOT NULL,
  PRIMARY KEY (`idIdentity`),
  KEY `idx_Idetities_IdentTypeCode` (`IdentTypeCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

ALTER TABLE `Idetities` 
ADD UNIQUE INDEX `uq_ident` (`IdentTypeCode` ASC, `IdentityKey` ASC);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_auth_log`
--

CREATE TABLE IF NOT EXISTS `tbl_auth_log` (
  `s_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `s_address` varchar(34) NOT NULL,
  `s_ip` varchar(46) NOT NULL,
  `s_description` varchar(4069) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_nonces`
--

CREATE TABLE IF NOT EXISTS `tbl_nonces` (
  `s_ip` varchar(46) NOT NULL,
  `dt_datetime` datetime NOT NULL,
  `s_nonce` varchar(32) NOT NULL,
  `s_address` varchar(34) DEFAULT NULL,
  UNIQUE KEY `s_nonce` (`s_nonce`),
  KEY `dt_datetime` (`dt_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwCustomerBitid`
--
CREATE TABLE IF NOT EXISTS `vwCustomerBitid` (
`idCustomer` varchar(34)
,`BitId` varchar(50)
);
-- --------------------------------------------------------

--
-- Structure for view `vwCustomerBitid`
--
DROP TABLE IF EXISTS `vwCustomerBitid`;

CREATE VIEW `vwCustomerBitid` AS select `Customers`.`idCustomer` AS `idCustomer`,`Idetities`.`IdentityKey` AS `BitId` from (`Customers` join `Idetities` on(((`Idetities`.`IdentTypeCode` = 'bitid') and (`Idetities`.`idCustomer` = `Customers`.`idCustomer`))));

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

ALTER TABLE `Idetities` 
ADD INDEX `fk_customer_idx` (`idCustomer` ASC);
ALTER TABLE `Idetities` 
ADD CONSTRAINT `fk_customer`
  FOREIGN KEY (`idCustomer`)
  REFERENCES `Customers` (`idCustomer`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
  
ALTER TABLE `Bookmarks` 
ADD INDEX `fk_droper_idx` (`IdDroper` ASC);
ALTER TABLE `Bookmarks` 
ADD CONSTRAINT `fk_droper`
  FOREIGN KEY (`IdDroper`)
  REFERENCES `Customers` (`idCustomer`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;



CREATE  OR REPLACE VIEW `vwActualPoint` AS
    SELECT 
        `Bookmarks`.`IdBookmark`,
		`Bookmarks`.`CreateDate`,
		`Bookmarks`.`IdOrder`,
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
		`Bookmarks`.`IdDroper`
    FROM
        `Ident`.`Bookmarks`
    WHERE   `Bookmarks`.`IdOrder` IS NULL
            AND NOW() <= COALESCE(`Bookmarks`.`EndDate`, NOW());


DELIMITER $$
CREATE DEFINER=`ident`@`%` FUNCTION `getActiveOrderByCustomer`(idCustomeromer varchar(34)) RETURNS int(11)
BEGIN

RETURN 1;
END$$
DELIMITER ;


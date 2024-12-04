-- MySQL dump 10.13  Distrib 9.0.1, for Linux (x86_64)
--
-- Host: localhost    Database: Foody
-- ------------------------------------------------------
-- Server version	9.0.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `code` varchar(5) NOT NULL,
  `name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES ('ECN','Económica'),('EST','Estándar'),('PRM','Premium');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `city`
--

DROP TABLE IF EXISTS `city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `city` (
  `code` varchar(5) NOT NULL,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `city`
--

LOCK TABLES `city` WRITE;
/*!40000 ALTER TABLE `city` DISABLE KEYS */;
INSERT INTO `city` VALUES ('ENS','Ensenada'),('MEX','Mexicali'),('ROS','Rosarito'),('TIJ','Tijuana');
/*!40000 ALTER TABLE `city` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `diningRoom`
--

DROP TABLE IF EXISTS `diningRoom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `diningRoom` (
  `num` int NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `ubication` varchar(30) NOT NULL,
  `factory` varchar(8) NOT NULL,
  PRIMARY KEY (`num`),
  KEY `factory` (`factory`),
  CONSTRAINT `diningRoom_ibfk_1` FOREIGN KEY (`factory`) REFERENCES `factory` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diningRoom`
--

LOCK TABLES `diningRoom` WRITE;
/*!40000 ALTER TABLE `diningRoom` DISABLE KEYS */;
INSERT INTO `diningRoom` VALUES (1,'Comiditas','area de comida','FERROS'),(3,'Comedor 1','Segunda planta','MADERB'),(5,'Dining Kansas','presa','KANSASCY');
/*!40000 ALTER TABLE `diningRoom` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `diningRoomManager`
--

DROP TABLE IF EXISTS `diningRoomManager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `diningRoomManager` (
  `num` int NOT NULL AUTO_INCREMENT,
  `firstName` varchar(20) NOT NULL,
  `middleName` varchar(20) NOT NULL,
  `lastName` varchar(20) DEFAULT NULL,
  `tel` varchar(10) DEFAULT NULL,
  `diningRoom` int NOT NULL,
  `userNumber` int NOT NULL,
  PRIMARY KEY (`num`),
  KEY `diningRoom` (`diningRoom`),
  KEY `userNumber` (`userNumber`),
  CONSTRAINT `diningRoomManager_ibfk_1` FOREIGN KEY (`diningRoom`) REFERENCES `diningRoom` (`num`),
  CONSTRAINT `userNumber` FOREIGN KEY (`userNumber`) REFERENCES `users` (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diningRoomManager`
--

LOCK TABLES `diningRoomManager` WRITE;
/*!40000 ALTER TABLE `diningRoomManager` DISABLE KEYS */;
INSERT INTO `diningRoomManager` VALUES (11,'Carlos','Zepeda','Cortes','2515698745',1,11),(13,'Gadiel','Barrios','Uriarte','1521254155',5,25);
/*!40000 ALTER TABLE `diningRoomManager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dining_menu`
--

DROP TABLE IF EXISTS `dining_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dining_menu` (
  `diningRoom` int NOT NULL,
  `menu` varchar(5) NOT NULL,
  PRIMARY KEY (`menu`,`diningRoom`),
  KEY `diningRoom` (`diningRoom`),
  CONSTRAINT `diningRoom` FOREIGN KEY (`diningRoom`) REFERENCES `diningRoom` (`num`),
  CONSTRAINT `menu` FOREIGN KEY (`menu`) REFERENCES `menu` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dining_menu`
--

LOCK TABLES `dining_menu` WRITE;
/*!40000 ALTER TABLE `dining_menu` DISABLE KEYS */;
INSERT INTO `dining_menu` VALUES (1,'M002'),(1,'M003'),(1,'M004'),(1,'M005'),(1,'M006');
/*!40000 ALTER TABLE `dining_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dish`
--

DROP TABLE IF EXISTS `dish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dish` (
  `code` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(150) DEFAULT NULL,
  `price` float NOT NULL,
  `discountPercentage` float NOT NULL,
  `category` varchar(5) NOT NULL,
  `menu` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`code`),
  KEY `category` (`category`),
  KEY `menu` (`menu`),
  CONSTRAINT `dish_ibfk_1` FOREIGN KEY (`category`) REFERENCES `category` (`code`),
  CONSTRAINT `dish_ibfk_2` FOREIGN KEY (`menu`) REFERENCES `menu` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dish`
--

LOCK TABLES `dish` WRITE;
/*!40000 ALTER TABLE `dish` DISABLE KEYS */;
INSERT INTO `dish` VALUES ('Corde','Cordero a la plancha','cordero asado a la plancha',100,0.05,'ECN','M004'),('Enchi','Enchiladas','Enchiladas y jugo de naranja',160,0.02,'EST','M004'),('FRUTI','frutiloops','frutiloops con platano y leche',70,0.04,'ECN','M004'),('Leche','leche y pan','pan de chocolate con leche',70,0.04,'ECN','M004'),('MOLLE','Molletes','Birotes con frijol y pico de gallo',80,0.05,'ECN','M006'),('PANCO','Pan con leche','pan con leche deslactosada o cafe',100,0.02,'ECN',NULL),('QUESA','Quesadilla','quesadillas con queso',200,0.06,'PRM','M004'),('Tamal','Tamales','Tamales de res y de pollo en salsa verde',125,0.04,'EST','M003'),('Torta','Tortas de jamon','Torta de jamon con un jugo',145,0.05,'EST','M002');
/*!40000 ALTER TABLE `dish` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `precioPlatillo` BEFORE INSERT ON `dish` FOR EACH ROW BEGIN

    IF NEW.price < 60 OR NEW.price > 220 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El precio debe estar entre 60 y 220';
    END IF;
    IF NEW.price <= 110 THEN
        SET NEW.category = 'ECN';
    ELSEIF NEW.price > 110 AND NEW.price <= 180 THEN
        SET NEW.category = 'EST';
    ELSE
        SET NEW.category = 'PRM';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `dish_ingred`
--

DROP TABLE IF EXISTS `dish_ingred`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dish_ingred` (
  `dish` varchar(5) NOT NULL,
  `ingredients` int NOT NULL,
  `numberIngred` int NOT NULL,
  PRIMARY KEY (`dish`,`ingredients`),
  KEY `ingredients` (`ingredients`),
  CONSTRAINT `dish_ingred_ibfk_1` FOREIGN KEY (`dish`) REFERENCES `dish` (`code`),
  CONSTRAINT `dish_ingred_ibfk_2` FOREIGN KEY (`ingredients`) REFERENCES `ingredients` (`num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dish_ingred`
--

LOCK TABLES `dish_ingred` WRITE;
/*!40000 ALTER TABLE `dish_ingred` DISABLE KEYS */;
INSERT INTO `dish_ingred` VALUES ('Corde',10,2),('Enchi',2,1),('Enchi',4,2),('Enchi',6,3),('FRUTI',3,2),('FRUTI',5,2),('FRUTI',7,2),('Leche',6,1),('Leche',7,2),('MOLLE',4,1),('MOLLE',7,1),('PANCO',2,2),('PANCO',7,2),('QUESA',3,2),('QUESA',7,2),('Tamal',2,2),('Tamal',4,2),('Tamal',7,1),('Torta',3,2),('Torta',4,2),('Torta',7,1);
/*!40000 ALTER TABLE `dish_ingred` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `restarStockIngrediente` AFTER INSERT ON `dish_ingred` FOR EACH ROW BEGIN
    DECLARE stockActual INT;
    DECLARE msg VARCHAR(100);

    SELECT stock INTO stockActual
    FROM ingredients
    WHERE num = NEW.ingredients;

    IF stockActual >= NEW.numberIngred THEN
        UPDATE ingredients
        SET stock = stock - NEW.numberIngred
        WHERE num = NEW.ingredients;
    ELSE
        SET msg = CONCAT('Stock insuficiente para el ingrediente con ID: ', NEW.ingredients);
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee` (
  `num` int NOT NULL AUTO_INCREMENT,
  `firstName` varchar(20) NOT NULL,
  `middleName` varchar(20) NOT NULL,
  `lastName` varchar(20) DEFAULT NULL,
  `tel` varchar(10) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `factory` varchar(8) NOT NULL,
  `jobPosition` varchar(5) NOT NULL,
  `userNum` int DEFAULT NULL,
  PRIMARY KEY (`num`),
  UNIQUE KEY `email` (`email`),
  KEY `factory` (`factory`),
  KEY `jobPosition` (`jobPosition`),
  KEY `userNum` (`userNum`),
  CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`factory`) REFERENCES `factory` (`code`),
  CONSTRAINT `employee_ibfk_2` FOREIGN KEY (`jobPosition`) REFERENCES `jobPosition` (`code`),
  CONSTRAINT `userNum` FOREIGN KEY (`userNum`) REFERENCES `users` (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee`
--

LOCK TABLES `employee` WRITE;
/*!40000 ALTER TABLE `employee` DISABLE KEYS */;
INSERT INTO `employee` VALUES (1,'Jabes','Llamas','Zamudio','4444444444','jabesemp@gmail.com','FERROS','INGN',7),(3,'Edgar','Nevarez','Arias','2581891871',NULL,'FERROS','INGN',21),(4,'Marcos','Aguirre','Gonzalez','1898787181',NULL,'FERROS','INGN',22),(5,'Camila','Navarrate','Mata','1561819161',NULL,'FERROS','INGN',23),(6,'Arturo','Herreraa','Luevano','8989661899',NULL,'KANSASCY','GRNT',24),(7,'Francisco','Gonzalez','Messi','6654789535',NULL,'FERROS','SCRT',26);
/*!40000 ALTER TABLE `employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factory`
--

DROP TABLE IF EXISTS `factory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factory` (
  `code` varchar(8) NOT NULL,
  `name` varchar(16) NOT NULL,
  `tel` varchar(10) DEFAULT NULL,
  `email` varchar(25) DEFAULT NULL,
  `streetAddr` varchar(50) DEFAULT NULL,
  `numAddr` int DEFAULT NULL,
  `colonyAddr` varchar(20) DEFAULT NULL,
  `numberEmp` int NOT NULL,
  `city` varchar(5) NOT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `email` (`email`),
  KEY `city` (`city`),
  CONSTRAINT `factory_ibfk_1` FOREIGN KEY (`city`) REFERENCES `city` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factory`
--

LOCK TABLES `factory` WRITE;
/*!40000 ALTER TABLE `factory` DISABLE KEYS */;
INSERT INTO `factory` VALUES ('ACEROS','Aceros del Norte','6641589814','acerosnorte@gmail.com','Reforma',5,'b',20,'ENS'),('ALUMEX','Alumex Corp','6643584872','aluminiosmexico@gmail.com','Insurgentes',15,'c',20,'TIJ'),('ANDA','And','7755884477','anda@gmail.com','Revo',95,'Ce',25,'ENS'),('ANDATTI','Andatti','7755884433','andatti@gmail.com','Revolucion',85,'Centro',65,'ENS'),('BANA','Banam','4848965423','bana@gmail.com','lolo',7265,'casa',88,'TIJ'),('BANAMEX','Banamex','5848965423','banamex@gmail.com','Cerezo',2651,'Altiplano',73,'TIJ'),('BIMBO','Bimbo','7755774477','bimbo@gmail.com','Alemania',958,'villa del sol',272,'ENS'),('CMTNOR','CMT Norte','6634228879','CMTNORTE@gmail.com','Jamaica',54,'j',20,'MEX'),('DULMEX','Dulmex Paletas','6648498742','dulmexpaletas@gmail.com','Monterrey',45,'i',20,'ROS'),('ELECTNO','Electro Nova','6641243257','electronova142@gmail.com','Castaños',54,'g',20,'TIJ'),('FERROS','Ferros Inc','6642588677','ferrostijuana@gmail.com','Brisa',2,'a',20,'TIJ'),('KANSASCY','Kansas','6542398751','kansas@gmail.com','Labello',5926,'Andatti',83,'ENS'),('MADERB','Maderas Benitez','6641651487','madebenitez@gmail.com','Las Rosas',14,'d',20,'ROS'),('MARINELA','Marinela','7849312569','marinela@gmail.com','Riveras',20000,'Real de Sanfrancisco',70,'TIJ'),('MUEBLES','Muebles Elvia','6631478799','muebleselvia@gmail.com','Colinas',72,'h',20,'ENS'),('Panditas','Panditas','8745635278','panditas@gmail.com','Venecia',8165,'Matamoros',45,'TIJ'),('PLASTI','Plastica Mex','6646585478','plasticosmex123@gmail.com','Blvd. Diaz Ordaz',10,'f',20,'TIJ'),('SABRITAS','Sabritas Mexico','7423695841','sabritas@gmail.com','Martinez',220002,'villa fontana',49,'TIJ'),('SAMSUNG','Samusung Mexico','8986435891','samsung@gmail.com','Mazapan',222024,'Villa del campo',80,'TIJ'),('TEXTIL','Textiles SA','6644585759','textilesssa@gmail.com','Alameda',12,'e',20,'MEX'),('TOSTITOS','Tostitos nacho','6589568956','tostitosnacho@gmail.com','accord',1111,'0',20,'ROS');
/*!40000 ALTER TABLE `factory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factoryAdmin`
--

DROP TABLE IF EXISTS `factoryAdmin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factoryAdmin` (
  `num` int NOT NULL AUTO_INCREMENT,
  `firstName` varchar(20) NOT NULL,
  `middleName` varchar(20) NOT NULL,
  `lastName` varchar(20) DEFAULT NULL,
  `tel` varchar(10) DEFAULT NULL,
  `factory` varchar(8) NOT NULL,
  `user_num` int NOT NULL,
  PRIMARY KEY (`num`),
  KEY `factory` (`factory`),
  KEY `user_num` (`user_num`),
  CONSTRAINT `factoryAdmin_ibfk_1` FOREIGN KEY (`factory`) REFERENCES `factory` (`code`),
  CONSTRAINT `user_num` FOREIGN KEY (`user_num`) REFERENCES `users` (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factoryAdmin`
--

LOCK TABLES `factoryAdmin` WRITE;
/*!40000 ALTER TABLE `factoryAdmin` DISABLE KEYS */;
INSERT INTO `factoryAdmin` VALUES (9,'Genesis','Brito','Machado','5555555554','FERROS',9),(10,'Kimberly','Soto','Garcia','1111111111','Panditas',13),(11,'Mark','Aguirre','Lopez','6668888444','SABRITAS',14),(12,'pepe','Juan','armenta','0988790000','BANAMEX',15),(13,'Lucas','perez','Garcia','8964235475','SAMSUNG',16),(15,'Itzel','Soto','Garcia','1234567895','ELECTNO',18);
/*!40000 ALTER TABLE `factoryAdmin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `infoDinningRoomManager`
--

DROP TABLE IF EXISTS `infoDinningRoomManager`;
/*!50001 DROP VIEW IF EXISTS `infoDinningRoomManager`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `infoDinningRoomManager` AS SELECT 
 1 AS `Name`,
 1 AS `id`,
 1 AS `E-mail`,
 1 AS `phone`,
 1 AS `diningRoom`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `infoEmployee`
--

DROP TABLE IF EXISTS `infoEmployee`;
/*!50001 DROP VIEW IF EXISTS `infoEmployee`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `infoEmployee` AS SELECT 
 1 AS `Name`,
 1 AS `id`,
 1 AS `E-mail`,
 1 AS `phone`,
 1 AS `factory`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `infoGeneralManager`
--

DROP TABLE IF EXISTS `infoGeneralManager`;
/*!50001 DROP VIEW IF EXISTS `infoGeneralManager`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `infoGeneralManager` AS SELECT 
 1 AS `Name`,
 1 AS `id`,
 1 AS `E-mail`,
 1 AS `phone`,
 1 AS `factory`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ingred_purcha`
--

DROP TABLE IF EXISTS `ingred_purcha`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ingred_purcha` (
  `ingredients` int NOT NULL,
  `purchaseOrder` int NOT NULL,
  `numberIngred` int NOT NULL,
  `amount` float NOT NULL,
  PRIMARY KEY (`ingredients`,`purchaseOrder`),
  KEY `purchaseOrder` (`purchaseOrder`),
  CONSTRAINT `ingred_purcha_ibfk_1` FOREIGN KEY (`purchaseOrder`) REFERENCES `purchaseOrder` (`num`),
  CONSTRAINT `ingred_purcha_ibfk_2` FOREIGN KEY (`ingredients`) REFERENCES `ingredients` (`num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingred_purcha`
--

LOCK TABLES `ingred_purcha` WRITE;
/*!40000 ALTER TABLE `ingred_purcha` DISABLE KEYS */;
INSERT INTO `ingred_purcha` VALUES (2,3,1,20),(2,4,1,20),(3,3,2,18),(3,4,1,9),(4,3,3,9),(5,3,4,12),(7,4,2,8),(10,11,5,20);
/*!40000 ALTER TABLE `ingred_purcha` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `calcularImporteIngrediente` BEFORE INSERT ON `ingred_purcha` FOR EACH ROW BEGIN
    DECLARE precioIngred FLOAT;

    SELECT price INTO precioIngred
    FROM ingredients
    WHERE num = NEW.ingredients;

    SET NEW.amount = precioIngred * NEW.numberIngred;


    update purchaseOrder
    set amountPayment = amountPayment + new.amount
    where num = new.purchaseOrder;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `sumarStockIngrediente` AFTER INSERT ON `ingred_purcha` FOR EACH ROW BEGIN
    UPDATE ingredients
    SET stock = stock + NEW.numberIngred
    WHERE num = NEW.ingredients;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `ingredients`
--

DROP TABLE IF EXISTS `ingredients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ingredients` (
  `num` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `experitionDate` date DEFAULT NULL,
  `stock` int NOT NULL,
  `price` float NOT NULL,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingredients`
--

LOCK TABLES `ingredients` WRITE;
/*!40000 ALTER TABLE `ingredients` DISABLE KEYS */;
INSERT INTO `ingredients` VALUES (2,'pechuga de pollo','2024-11-30',4,20),(3,'Lechuga','2024-12-01',5,9),(4,'Tomate','2024-12-01',10,3),(5,'zanahoria','2024-12-04',5,3),(6,'pan Integral','2024-12-08',6,7),(7,'Pan bimbo','2024-12-31',8,4),(9,'pato','2024-11-27',0,10),(10,'cordero','2024-11-29',5,4);
/*!40000 ALTER TABLE `ingredients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobPosition`
--

DROP TABLE IF EXISTS `jobPosition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobPosition` (
  `code` varchar(5) NOT NULL,
  `description` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobPosition`
--

LOCK TABLES `jobPosition` WRITE;
/*!40000 ALTER TABLE `jobPosition` DISABLE KEYS */;
INSERT INTO `jobPosition` VALUES ('ASTN','Asistente'),('GRNT','Gerente'),('INGN','Ingeniero'),('INTN','Intendente'),('OPRD','Operador'),('OPRC','Operador Clave'),('SCRT','Secretario'),('SPVR','Supervisor');
/*!40000 ALTER TABLE `jobPosition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu` (
  `code` varchar(5) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(60) NOT NULL,
  `menu_type` int NOT NULL,
  PRIMARY KEY (`code`),
  KEY `menu_type` (`menu_type`),
  CONSTRAINT `menu_type` FOREIGN KEY (`menu_type`) REFERENCES `menu_type` (`num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES ('M002','Comida intermedio','Este es menu para la pruebaaaa',2),('M003','Nocturno','Este menu es para los de la noche',1),('M004','menu navidad noooo','solo platillos de temporada siuuu',1),('M005','Desayunos ','comida para iniciar el dia',1),('M006','Noche','Menu para trabajadores de la noche',3);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_type`
--

DROP TABLE IF EXISTS `menu_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_type` (
  `num` int NOT NULL AUTO_INCREMENT,
  `description` varchar(10) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_type`
--

LOCK TABLES `menu_type` WRITE;
/*!40000 ALTER TABLE `menu_type` DISABLE KEYS */;
INSERT INTO `menu_type` VALUES (1,'Matutino','07:00:00','15:00:00'),(2,'Vespertino','15:00:00','23:00:00'),(3,'Nocturno','23:00:00','07:00:00');
/*!40000 ALTER TABLE `menu_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ord_dish`
--

DROP TABLE IF EXISTS `ord_dish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ord_dish` (
  `numberDishes` int NOT NULL,
  `amount` int NOT NULL,
  `dish` varchar(5) NOT NULL,
  `orderEmp` int NOT NULL,
  `dishDiscount` float DEFAULT NULL,
  PRIMARY KEY (`dish`,`orderEmp`),
  KEY `orderemp` (`orderEmp`),
  CONSTRAINT `ord_dish_ibfk_1` FOREIGN KEY (`dish`) REFERENCES `dish` (`code`),
  CONSTRAINT `ord_dish_ibfk_2` FOREIGN KEY (`orderEmp`) REFERENCES `orderEmp` (`num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ord_dish`
--

LOCK TABLES `ord_dish` WRITE;
/*!40000 ALTER TABLE `ord_dish` DISABLE KEYS */;
INSERT INTO `ord_dish` VALUES (0,0,'Corde',69,0),(1,100,'Corde',83,5),(1,100,'Corde',87,5),(1,100,'Corde',92,5),(1,100,'Corde',98,5),(1,100,'Corde',109,5),(2,200,'Corde',111,0),(1,100,'Corde',112,0),(1,100,'Corde',117,0),(2,200,'Corde',119,0),(2,200,'Corde',123,10),(1,160,'Enchi',10,0),(1,160,'Enchi',13,3.2),(2,320,'Enchi',19,6.4),(2,320,'Enchi',20,6.4),(2,320,'Enchi',21,6.4),(1,160,'Enchi',31,0),(1,160,'Enchi',68,0),(2,320,'Enchi',80,6.4),(1,160,'Enchi',86,3.2),(1,160,'Enchi',92,3.2),(1,160,'Enchi',98,3.2),(1,160,'Enchi',109,3.2),(1,160,'Enchi',112,0),(2,320,'Enchi',113,0),(2,320,'Enchi',114,0),(3,480,'Enchi',115,0),(3,480,'Enchi',116,9.6),(2,320,'Enchi',118,6.4),(2,320,'Enchi',124,6.4),(1,160,'Enchi',126,3.2),(1,160,'Enchi',130,3.2),(1,70,'FRUTI',31,0),(1,70,'FRUTI',68,0),(0,0,'FRUTI',69,0),(2,140,'FRUTI',79,5.6),(1,70,'FRUTI',83,2.8),(1,70,'FRUTI',86,2.8),(1,70,'FRUTI',109,2.8),(1,70,'FRUTI',119,0),(2,140,'FRUTI',121,0),(1,70,'FRUTI',126,2.8),(1,70,'Leche',10,0),(1,70,'Leche',87,2.8),(1,70,'Leche',109,2.8),(1,70,'Leche',117,0),(1,70,'Leche',118,2.8),(1,70,'Leche',130,2.8),(2,160,'MOLLE',69,0),(1,80,'MOLLE',81,4),(3,240,'MOLLE',82,0),(2,160,'MOLLE',84,0),(2,160,'MOLLE',85,0),(2,160,'MOLLE',88,0),(2,160,'MOLLE',89,0),(4,320,'MOLLE',90,0),(2,160,'MOLLE',91,0),(2,160,'MOLLE',93,8),(2,160,'MOLLE',95,8),(2,160,'MOLLE',96,8),(1,80,'MOLLE',99,4),(1,80,'MOLLE',100,4),(1,80,'MOLLE',101,4),(2,160,'MOLLE',102,0),(2,160,'MOLLE',103,0),(2,160,'MOLLE',105,0),(3,240,'MOLLE',106,0),(2,160,'MOLLE',110,0),(1,80,'MOLLE',130,4),(1,200,'QUESA',5,0),(1,200,'QUESA',130,12),(10,1250,'Tamal',13,5),(1,125,'Tamal',14,0),(2,250,'Tamal',15,0),(2,250,'Tamal',16,0),(1,125,'Tamal',17,5),(3,375,'Tamal',18,0),(1,125,'Tamal',68,0),(2,250,'Tamal',120,0),(1,145,'Torta',10,0),(1,145,'Torta',11,0),(1,145,'Torta',13,7.25),(0,0,'Torta',69,0),(2,290,'Torta',94,14.5),(2,290,'Torta',97,14.5),(1,145,'Torta',122,0),(1,145,'Torta',125,0),(5,725,'Torta',127,0),(1,145,'Torta',128,0);
/*!40000 ALTER TABLE `ord_dish` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `importeyDescuento` BEFORE INSERT ON `ord_dish` FOR EACH ROW BEGIN
    DECLARE amount FLOAT;
    DECLARE discount FLOAT;
    DECLARE msg VARCHAR(50);
    DECLARE platoExiste INT;
    DECLARE horaOrden TIME;
    DECLARE startMenu TIME;
    DECLARE endMenu TIME;

    SET platoExiste = (SELECT COUNT(*) 
                      FROM dish 
                      WHERE code = new.dish);

    IF (platoExiste = 0) THEN
        SET msg = CONCAT('No existe el plato con código: ', new.dish);
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
    END IF;
    
    IF(new.numberDishes <= 0) THEN
        SET msg = 'La cantidad de platillos debe ser mayor a 0';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
    ELSE

        SET amount = new.numberDishes * (SELECT price FROM dish WHERE code = new.dish);
        SET new.amount = amount;
    END IF;

    SET horaOrden = (SELECT TIME(dateOrde) FROM orderEmp where num = new.orderEmp);

    SET startMenu = (SELECT start_time from menu_type as mt 
                    INNER JOIN menu as m on m.menu_type = mt.num
                    INNER JOIN dish as d on d.menu = m.code
                    where d.code = new.dish);

    SET endMenu = (SELECT end_time from menu_type as mt 
                    INNER JOIN menu as m on m.menu_type = mt.num
                    INNER JOIN dish as d on d.menu = m.code
                    where d.code = new.dish);
    
    IF horaOrden BETWEEN startMenu AND endMenu THEN
        SET new.dishDiscount = 0;
    ELSE
        SET discount = (SELECT discountPercentage FROM dish where code = new.dish);
        SET new.dishDiscount = new.amount * discount;
    END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `totalPayment` AFTER INSERT ON `ord_dish` FOR EACH ROW BEGIN
    DECLARE totalDesc float;
    DECLARE total float;

    SET totalDesc = (SELECT SUM(dishDiscount) from ord_dish where orderEmp = new.orderEmp);

    SET total =  (SELECT SUM(amount) from ord_dish where orderemp = new.orderEmp);

    update orderEmp
    set paymentAmount =  total - totalDesc, totalDiscount = totalDesc
    where num = new.orderEmp;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `orderEmp`
--

DROP TABLE IF EXISTS `orderEmp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orderEmp` (
  `num` int NOT NULL AUTO_INCREMENT,
  `paymentAmount` float NOT NULL,
  `dateOrde` datetime DEFAULT CURRENT_TIMESTAMP,
  `totalDiscount` float NOT NULL,
  `employee` int NOT NULL,
  `status` varchar(5) NOT NULL,
  PRIMARY KEY (`num`),
  KEY `employee` (`employee`),
  KEY `status` (`status`),
  CONSTRAINT `orderEmp_ibfk_1` FOREIGN KEY (`employee`) REFERENCES `employee` (`num`),
  CONSTRAINT `orderEmp_ibfk_2` FOREIGN KEY (`status`) REFERENCES `status` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orderEmp`
--

LOCK TABLES `orderEmp` WRITE;
/*!40000 ALTER TABLE `orderEmp` DISABLE KEYS */;
INSERT INTO `orderEmp` VALUES (5,200,'2024-11-24 18:57:30',0,3,'ETR'),(10,375,'2024-11-24 21:50:55',0,3,'ETR'),(11,145,'2024-11-24 21:51:07',0,3,'ETR'),(13,1555,'2024-11-25 01:24:59',15.45,3,'ETR'),(14,125,'2024-11-25 01:49:07',0,3,'ETR'),(15,250,'2024-11-25 04:11:49',0,5,'ETR'),(16,250,'2024-11-25 04:16:13',0,5,'ETR'),(17,125,'2024-11-25 04:20:41',5,5,'ETR'),(18,375,'2024-11-25 04:23:36',0,5,'ETR'),(19,320,'2024-11-25 08:05:28',0,5,'PND'),(20,320,'2024-11-25 08:06:21',0,5,'PND'),(21,320,'2024-11-25 08:22:35',0,5,'ETR'),(31,230,'2024-11-25 08:33:49',0,3,'ETR'),(67,0,'2024-11-25 09:39:28',0,6,'PND'),(68,355,'2024-11-25 09:55:10',0,3,'ETR'),(69,160,'2024-11-26 21:00:33',0,3,'ETR'),(79,139.944,'2024-11-26 21:41:32',0.056,3,'ETR'),(80,319.936,'2024-11-26 22:14:01',0.064,3,'ETR'),(81,79.96,'2024-11-26 23:22:51',0.04,3,'ETR'),(82,240,'2024-11-26 23:25:51',0,3,'ETR'),(83,170,'2024-11-26 23:28:09',0,3,'ETR'),(84,160,'2024-11-26 23:29:41',0,3,'ETR'),(85,160,'2024-11-26 23:53:33',0,3,'ETR'),(86,230,'2024-11-27 00:04:57',0,3,'ETR'),(87,170,'2024-11-27 00:05:40',0,3,'ETR'),(88,160,'2024-11-27 00:06:05',0,3,'ETR'),(89,160,'2024-11-27 00:06:15',0,3,'ETR'),(90,320,'2024-11-27 00:07:25',0,3,'ETR'),(91,160,'2024-11-27 00:15:33',0,3,'ETR'),(92,260,'2024-11-27 00:16:14',0,3,'ETR'),(93,159.92,'2024-11-27 00:24:28',0.08,3,'ETR'),(94,289.855,'2024-11-27 00:28:34',0.145,3,'ETR'),(95,159.92,'2024-11-27 00:31:14',0.08,3,'ETR'),(96,159.92,'2024-11-27 00:34:08',0.08,3,'ETR'),(97,289.855,'2024-11-27 00:35:40',0.145,3,'ETR'),(98,260,'2024-11-27 00:41:10',0,3,'ETR'),(99,80,'2024-11-27 00:45:44',4,3,'ETR'),(100,80,'2024-11-27 00:53:09',4,3,'ETR'),(101,80,'2024-11-27 00:55:28',4,3,'ETR'),(102,76,'2024-11-27 01:04:37',4,3,'ETR'),(103,76,'2024-11-27 01:05:51',4,3,'ETR'),(104,0,'2024-11-27 01:06:02',0,3,'ETR'),(105,76,'2024-11-27 01:09:10',4,3,'ETR'),(106,76,'2024-11-27 01:14:50',4,3,'ETR'),(108,0,'2024-11-27 01:26:55',0,3,'ETR'),(109,399.862,'2024-11-27 01:28:40',0.138,3,'ETR'),(110,76,'2024-11-27 01:29:00',4,3,'ETR'),(111,199.9,'2024-11-27 08:23:57',0.1,3,'ETR'),(112,259.918,'2024-11-27 09:34:12',0.082,3,'ETR'),(113,320,'2024-11-29 08:00:00',0,3,'ETR'),(114,320,'2024-11-30 09:00:00',0,3,'ETR'),(115,480,'2024-11-30 08:00:00',0,3,'ETR'),(116,470.4,'2024-11-29 18:00:00',9.6,3,'ETR'),(117,170,'2024-11-27 10:05:24',0,3,'ETR'),(118,380.8,'2024-11-30 17:00:00',9.2,3,'ETR'),(119,270,'2024-11-27 10:08:52',0,3,'ETR'),(120,125,'2024-11-27 10:17:16',0,3,'ETR'),(121,70,'2024-11-27 10:17:27',0,3,'ETR'),(122,145,'2024-11-27 19:09:26',0,3,'ETR'),(123,190,'2024-11-29 17:00:00',10,3,'ETR'),(124,313.6,'2024-11-30 17:00:00',6.4,3,'ETR'),(125,145,'2024-11-28 19:53:20',0,3,'ETR'),(126,224,'2024-11-29 17:00:00',6,3,'ETR'),(127,145,'2024-11-28 20:01:31',0,7,'ETR'),(128,145,'2024-11-28 22:56:00',0,3,'ETR'),(130,488,'2024-11-28 23:07:14',22,3,'PND');
/*!40000 ALTER TABLE `orderEmp` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `inicializarOrden` BEFORE INSERT ON `orderEmp` FOR EACH ROW BEGIN

set new.status = 'PND';
set new.paymentAmount = 0;
set new.totalDiscount = 0;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `purchaseOrder`
--

DROP TABLE IF EXISTS `purchaseOrder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchaseOrder` (
  `num` int NOT NULL AUTO_INCREMENT,
  `amountPayment` float NOT NULL,
  `datePurs` date NOT NULL,
  `diningroommanager` int NOT NULL,
  `supplier` varchar(7) NOT NULL,
  PRIMARY KEY (`num`),
  KEY `supplier` (`supplier`),
  KEY `diningRoomManager` (`diningroommanager`),
  CONSTRAINT `diningRoomManager` FOREIGN KEY (`diningroommanager`) REFERENCES `diningRoomManager` (`num`),
  CONSTRAINT `purchaseOrder_ibfk_1` FOREIGN KEY (`supplier`) REFERENCES `supplier` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchaseOrder`
--

LOCK TABLES `purchaseOrder` WRITE;
/*!40000 ALTER TABLE `purchaseOrder` DISABLE KEYS */;
INSERT INTO `purchaseOrder` VALUES (3,59,'2024-11-23',11,'ELGRA'),(4,37,'2024-11-24',11,'Frute'),(5,0,'2024-11-24',11,'ELGRA'),(6,0,'2024-11-24',11,'Frute'),(7,0,'2024-11-23',11,'ELGRA'),(8,0,'2024-11-23',11,'ELGRA'),(9,0,'2024-11-23',11,'ELGRA'),(10,0,'2024-11-23',11,'Frute'),(11,20,'2024-03-03',11,'ELGRA'),(12,0,'2024-11-23',11,'ELGRA'),(14,0,'2024-11-23',11,'Frute');
/*!40000 ALTER TABLE `purchaseOrder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `purchaseOrderDetails`
--

DROP TABLE IF EXISTS `purchaseOrderDetails`;
/*!50001 DROP VIEW IF EXISTS `purchaseOrderDetails`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `purchaseOrderDetails` AS SELECT 
 1 AS `orderNumber`,
 1 AS `orderDate`,
 1 AS `managerFullName`,
 1 AS `diningRoomName`,
 1 AS `supplierName`,
 1 AS `ingredients`,
 1 AS `totalAmount`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report` (
  `num` int NOT NULL AUTO_INCREMENT,
  `totalSales` int NOT NULL,
  `dateStart` date NOT NULL,
  `endDate` date NOT NULL,
  `productionDate` date NOT NULL,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report`
--

LOCK TABLES `report` WRITE;
/*!40000 ALTER TABLE `report` DISABLE KEYS */;
INSERT INTO `report` VALUES (5,2330,'2024-11-23','2024-11-25','2024-11-24');
/*!40000 ALTER TABLE `report` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `cambiarEstadoReporte` AFTER INSERT ON `report` FOR EACH ROW BEGIN
    UPDATE ticket 
    SET report = NEW.num
    WHERE dateTick BETWEEN NEW.dateStart AND NEW.endDate
    AND report IS NULL;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `status` (
  `code` varchar(5) NOT NULL,
  `description` varchar(10) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status`
--

LOCK TABLES `status` WRITE;
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
INSERT INTO `status` VALUES ('CNL','Cancelada'),('ETR','Entregado'),('PND','Pendiente');
/*!40000 ALTER TABLE `status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier`
--

DROP TABLE IF EXISTS `supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier` (
  `code` varchar(7) NOT NULL,
  `name` varchar(25) NOT NULL,
  `tel` varchar(10) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier`
--

LOCK TABLES `supplier` WRITE;
/*!40000 ALTER TABLE `supplier` DISABLE KEYS */;
INSERT INTO `supplier` VALUES ('ELGRA','El grano de oro 2','6677448822','elgranodeoro2@gmail.com'),('Frute','Fruteria los Pepes','6455515555','fruteria@gmail.com');
/*!40000 ALTER TABLE `supplier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket`
--

DROP TABLE IF EXISTS `ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket` (
  `num` int NOT NULL AUTO_INCREMENT,
  `total` float NOT NULL,
  `dateTick` date NOT NULL,
  `reportStatus` varchar(20) DEFAULT NULL,
  `report` int DEFAULT NULL,
  `orderEmp` int NOT NULL,
  PRIMARY KEY (`num`),
  KEY `report` (`report`),
  KEY `orderEmp` (`orderEmp`),
  CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`report`) REFERENCES `report` (`num`),
  CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`orderEmp`) REFERENCES `orderEmp` (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket`
--

LOCK TABLES `ticket` WRITE;
/*!40000 ALTER TABLE `ticket` DISABLE KEYS */;
INSERT INTO `ticket` VALUES (1,200,'2024-11-24','add a report',5,5),(2,375,'2024-11-24','add a report',5,10),(3,145,'2024-11-24','add a report',5,11),(4,305,'2024-11-25','agregar a reporte',5,13),(5,444.84,'2024-11-25','agregar a reporte',5,14),(6,250,'2024-11-25',NULL,5,15),(7,250,'2024-11-25',NULL,5,16),(8,125,'2024-11-25',NULL,5,17),(9,375,'2024-11-25',NULL,5,18),(10,320,'2024-11-25','agregar a reporte',5,19),(11,320,'2024-11-25','agregar a reporte',5,20),(12,320,'2024-11-25','agregar a reporte',5,21),(13,320,'2024-11-25','add a report',5,21),(14,390,'2024-11-25','agregar a reporte',5,31),(15,230,'2024-11-25',NULL,5,31),(16,125,'2024-11-25',NULL,5,14),(17,1555,'2024-11-25',NULL,5,13),(18,229.94,'2024-11-25','agregar a reporte',5,68),(19,355,'2024-11-25',NULL,5,68),(65,320,'2024-11-29','agregar a reporte',NULL,113),(66,320,'2024-11-30','agregar a reporte',NULL,114),(67,480,'2024-11-30','agregar a reporte',NULL,115),(68,470,'2024-11-29','agregar a reporte',NULL,116),(70,381,'2024-11-30','agregar a reporte',NULL,118),(72,125,'2024-11-27','agregar a reporte',NULL,120),(73,70,'2024-11-27','agregar a reporte',NULL,121),(74,145,'2024-11-27','agregar a reporte',NULL,122),(75,190,'2024-11-29','agregar a reporte',NULL,123),(76,314,'2024-11-30','agregar a reporte',NULL,124),(77,145,'2024-11-28','agregar a reporte',NULL,125),(78,224,'2024-11-29','agregar a reporte',NULL,126),(79,145,'2024-11-28','agregar a reporte',NULL,127),(80,145,'2024-11-28','agregar a reporte',NULL,128);
/*!40000 ALTER TABLE `ticket` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `numReporteXTicket` BEFORE INSERT ON `ticket` FOR EACH ROW BEGIN
    DECLARE reportNum int;

    SELECT num INTO reportNum
    FROM report
    WHERE NEW.dateTick BETWEEN dateStart AND endDate
    LIMIT 1;

    SET NEW.report = reportNum;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `actualizarTotalVentas` AFTER INSERT ON `ticket` FOR EACH ROW BEGIN
    IF NEW.reportStatus = 'agregar a reporte' AND NEW.report IS NOT NULL THEN
        UPDATE report
        SET totalSales = COALESCE(
            (SELECT SUM(total)
             FROM ticket
             WHERE report = NEW.report
             AND reportStatus = 'agregar a reporte'),
            0)
        WHERE num = NEW.report;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `num` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `hash_password` varchar(255) NOT NULL,
  `rol` enum('employee','diningRoomManager','generalManager') DEFAULT NULL,
  PRIMARY KEY (`num`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (7,'jabesemp@gmail.com','$2y$10$tKQF/534ulTcT6/sdaH53.FSZS6/PIY36kYgaFVhyTEQOnh5.7V4G','employee'),(9,'genegeneral@gmail.com','$2y$10$DrW6d8nF3mKDHRxPKuinn.WuVFtY1pHpEp1AvwIh2DSF.y25zINxy','generalManager'),(11,'zepeda@gmail.com','$2y$10$PgMavJVyyZPOJCbpebPVV.sSYpwIvLfnm97yQSAGEP6DgSBrTVB/2','diningRoomManager'),(13,'kim1@gmail.com','$2y$10$JcxGEz2PSFRaZccEHOFJs.5Dz8nb.SONc/6q3wP9QKRB/yA1Wj5.q','generalManager'),(14,'marcos12@gmail.com','$2y$10$U6ksbtKlEmk9KLArK82z7eW8on8NIckEOU6UmqeKh362kh.pLttfe','generalManager'),(15,'pepe33@gmail.com','$2y$10$sVORqQL1PFP9A27C8CQI2eGKBbBxHfU6CSqMqFLXbxleNlBvlLW..','generalManager'),(16,'lucas3@gmail.com','$2y$10$U2pR.1/840AXykjreN6PV.D2qkbw2wepagK4khoQQymzt2hBjmJtW','generalManager'),(18,'itzelca@gmail.com','$2y$10$S9FDf4znMN1RoB1D3Z9Giu8Ri3Z1v5rjPBt26N9t9BpphVd5OY6Z6','generalManager'),(21,'edgar@gmail.com','$2y$10$QF42N/v3Aga/K2RVf5Jz5.MvuHWCH5ljpMa5h.6LNa41OHDVH5e.e','employee'),(22,'marcos13@gmail.com','$2y$10$0YvNSRPFBlGw2CdOuSuk.efhdCkyvWveGB1TGfD.eHngw6OBANeU2','employee'),(23,'camila@gmail.com','$2y$10$5p4atyjVHdM2R2KXcX26QuPpMeyj3MJh5f4IEsVAqpGclihTyyAB.','employee'),(24,'arturo@gmail.com','$2y$10$cQvwWKS/QLRFEeCzFvrAguaRoGrJN9EuIbKATKGpsH5kdaNLtE36G','employee'),(25,'barrios@gmail.com','$2y$10$hy5px3p8Q5/iA2M2fMH62u4qWPJ59NUyQejbLX.Vv0YDAQoh6kJme','diningRoomManager'),(26,'messi@gmail.com','$2y$10$8GPfs6ZLPFOUD.RX8wxeIui9xdiOy4DIWI2w9ThjRVcOFFJNDx08S','employee');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'Foody'
--
/*!50003 DROP PROCEDURE IF EXISTS `addDishIngredient` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `addDishIngredient`(
    IN p_dish_code varchar(5),
    IN p_ingredient_num int,
    IN p_amount int
)
BEGIN
    INSERT INTO dish_ingred (dish, ingredients, numberIngred)
    VALUES(p_dish_code, p_ingredient_num, p_amount);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `addPurchaseOrderIngredient` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `addPurchaseOrderIngredient`(
    IN p_orderId INT,
    IN p_ingredientId INT,
    IN p_quantity INT,
    IN p_amount FLOAT
)
BEGIN
    DECLARE exit handler FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;
    
    -- Insert the ingredient into the purchase order
    INSERT INTO ingred_purcha (
        ingredients,
        purchaseOrder,
        numberIngred,
        amount
    )
    VALUES (
        p_ingredientId,
        p_orderId,
        p_quantity,
        p_amount
    );
    
    COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `crearRecibo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `crearRecibo`(IN p_orderEmp int)
BEGIN
    declare p_total int;
    declare p_dateTick date;
    declare p_status VARCHAR(20);

    set p_total = (SELECT paymentAmount FROM orderEmp where num = p_orderEmp);
    set p_status = 'agregar a reporte';
    set p_dateTick = (SELECT dateOrde FROM orderEmp where num = p_orderEmp);

    insert into ticket(total, dateTick, reportStatus, orderEmp)
    values (p_total, p_dateTick, p_status, p_orderEmp);

    update orderEmp set status = 'ETR' where num = p_orderEmp;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `creatediningroom` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `creatediningroom`(
    IN p_name VARCHAR(30),
    IN p_ubication VARCHAR(30),
    IN p_factory VARCHAR(8)
)
BEGIN
    INSERT INTO diningRoom (name, ubication, factory)
    VALUES (p_name, p_ubication, p_factory);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `createDish` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `createDish`(
    IN p_code varchar(5),
    IN p_name varchar(50),
    IN p_description VARCHAR(50),
    IN p_price float,
    IN p_discountPercentage float,
    IN p_menu varchar(5)
)
BEGIN
    -- Insertar el plato
    INSERT INTO dish (code, name, description, price, discountPercentage, menu)
    VALUES(p_code, p_name, p_description, p_price, p_discountPercentage, p_menu);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `createfactory` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `createfactory`(
    IN f_code varchar(8),
    IN f_name varchar(16),
    IN f_tel varchar(10),
    IN f_email varchar(25),
    IN f_street varchar(15),
    IN f_numAddr int,
    IN f_colony varchar(20),
    IN f_Emps int,
    IN cityName VARCHAR(20)
)
BEGIN

set @citycode = (SELECT code from city where name = cityName);
IF @citycode IS NOT NULL THEN
select @citycode;
insert into factory(code, name, tel, email, streetAddr, numAddr, colonyAddr, numberEmp, city)
values( f_code, f_name, f_tel, f_email, f_street, f_numAddr, f_colony, f_Emps, @citycode);
    ELSE
        SELECT 'The entered city does not exist';
END IF;

end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `createMenu` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `createMenu`(
    in p_code VARCHAR(5),
    in p_Name VARCHAR(30),
    in p_description varchar(68),
    in p_MenuType int
)
begin
    INSERT into menu VALUES (p_code,p_Name,p_description,p_MenuType);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `createPurchaseOrder` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `createPurchaseOrder`(
    IN p_date DATE,
    IN p_diningRoom INT,
    IN p_supplier VARCHAR(7)
)
BEGIN
    DECLARE v_orderId INT;
    DECLARE exit handler FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;
    
    -- Insert the purchase order with initial amount of 0
    INSERT INTO purchaseOrder (
        amountPayment,
        datePurs,
        diningRoomManager,
        supplier
    )
    VALUES (
        0,
        p_date,
        p_diningRoom,
        p_supplier
    );
    
    -- Get the ID of the newly created purchase order
    SET v_orderId = LAST_INSERT_ID();
    
    -- After inserting all ingredients, we can create another procedure to add ingredients:
    -- This would be called separately for each ingredient:
    
    COMMIT;
    
    -- Return the new order ID
    SELECT v_orderId AS orderId;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `deleteUser` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteUser`(IN p_user_id INT)
BEGIN
    -- Declare variables to store user role
    DECLARE user_role VARCHAR(20);
    
    -- Start transaction
    START TRANSACTION;
    
    -- Get user role
    SELECT rol INTO user_role 
    FROM users 
    WHERE num = p_user_id;
    
    -- Delete based on role
    CASE user_role
        WHEN 'employee' THEN
            DELETE FROM employee WHERE userNum = p_user_id;
            
        WHEN 'diningRoomManager' THEN
            DELETE FROM diningRoomManager WHERE userNumber = p_user_id;
            
        WHEN 'generalManager' THEN
            DELETE FROM factoryAdmin WHERE user_num = p_user_id;
            
        ELSE
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Invalid user role';
    END CASE;
    
    -- Delete from users table
    DELETE FROM users WHERE num = p_user_id;
    
    -- Commit transaction
    COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `infoReporte` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `infoReporte`(
    IN p_fechaInicio DATE,
    IN p_fechaFinal DATE
)
BEGIN
    SELECT 
        re.num AS 'Numero del Reporte',
        re.dateStart AS 'Fecha de inicio',
        re.endDate as 'Fecha final',
        e.firstName AS Empleado,
        oe.num AS NumeroOrden,
        oe.paymentAmount AS MontoPago,
        oe.totalDiscount AS DescuentoTotal,
        t.num AS NumeroTicket,
        t.dateTick AS FechaTicket,
        t.total AS TotalTicket
    FROM orderEmp oe
    INNER JOIN employee e ON e.num = oe.employee
    INNER JOIN ticket t ON oe.num = t.orderEmp
    INNER JOIN report re ON t.report = re.num
    WHERE re.dateStart >= p_fechaInicio 
    AND re.endDate <= p_fechaFinal
    ORDER BY re.dateStart DESC;

    -- Si no hay resultados, mostrar mensaje
    IF NOT EXISTS (
        SELECT 1 
        FROM report re
        WHERE re.dateStart >= p_fechaInicio 
        AND re.endDate <= p_fechaFinal
    ) THEN
        SELECT CONCAT('No reports were found between the dates ',
                    p_fechaInicio, ' y ', p_fechaFinal) AS Mensaje;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `registerEmployee` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `registerEmployee`(
    IN p_firstName VARCHAR(255),
    IN p_maternalName VARCHAR(255),
    IN p_lastName VARCHAR(255),
    IN p_phone VARCHAR(10),
    IN p_factory VARCHAR(255),
    IN p_position_description VARCHAR(255),
    IN p_email VARCHAR(255),
    IN p_password VARCHAR(255),
    OUT p_new_user_id INT
)
BEGIN
    DECLARE v_user_id INT;
    DECLARE v_position_code varchar(5);

    select code into v_position_code
    from jobPosition
    where description = p_position_description;

    INSERT INTO users (email, hash_password, rol) 
    VALUES (p_email, p_password, 'employee');

    SET v_user_id = LAST_INSERT_ID();

    INSERT INTO employee (firstName, middleName, lastName, tel, factory, jobPosition, userNum)
    VALUES (p_firstName, p_maternalName, p_lastName, p_phone, p_factory, v_position_code, v_user_id);

    SET p_new_user_id = v_user_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `registerGenManager` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `registerGenManager`(
    IN p_firstName VARCHAR(255),
    IN p_maternalName VARCHAR(255),
    IN p_lastName VARCHAR(255),
    IN p_phone VARCHAR(10),
    IN p_factory VARCHAR(255),
    IN p_email VARCHAR(255),
    IN p_password VARCHAR(255),
    OUT p_new_user_id INT
)
BEGIN
    DECLARE v_user_id INT;
    DECLARE v_existing_GENmanager INT;
    DECLARE msg VARCHAR(100);

    SELECT COUNT(*) INTO v_existing_GENmanager
    FROM factoryAdmin
    WHERE factory = p_factory;

    IF v_existing_GENmanager > 0 THEN
        SET msg = CONCAT('A general manager is already assigned to factory ');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
    ELSE
    INSERT INTO users (email, hash_password, rol) 
    VALUES (p_email, p_password, 'generalManager');

    SET v_user_id = LAST_INSERT_ID();

    INSERT INTO factoryAdmin (firstName, middleName, lastName, tel, factory, user_num)
    VALUES (p_firstName, p_maternalName, p_lastName, p_phone, p_factory, v_user_id);

    SET p_new_user_id = v_user_id;
        END IF;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `registerIngred` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `registerIngred`(
    in p_name VARCHAR (20),
    in p_expirationDate date,
    in p_stock int,
    in p_price float
)
begin
    insert into ingredients (name,experitionDate,stock,price)values
    (p_name,p_expirationDate,p_stock,p_price);
end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `registerSupplier` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `registerSupplier`(
    in code VARCHAR(5),
    in name VARCHAR(50),
    in tel VARCHAR(10),
    in email VARCHAR(50)
)
begin

    insert into supplier(code,name,tel,email)values
    (code,name,tel,email);
end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `regiterDininManager` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `regiterDininManager`(
    IN p_firstName VARCHAR(255),
    IN p_maternalName VARCHAR(255),
    IN p_lastName VARCHAR(255),
    IN p_phone VARCHAR(10),
    IN p_diningRoom INT,
    IN p_email VARCHAR(255),
    IN p_password VARCHAR(255),
    OUT p_new_user_id INT
)
BEGIN
    DECLARE v_user_id INT;
    DECLARE v_existing_manager INT;
    DECLARE msg VARCHAR(100);
    
    SELECT COUNT(*) INTO v_existing_manager
    FROM diningRoomManager
    WHERE diningRoom = p_diningRoom;
    
    IF v_existing_manager > 0 THEN
        SET msg = CONCAT('A manager is already assigned to dining room ');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
    ELSE
        INSERT INTO users (email, hash_password, rol)
        VALUES (p_email, p_password, 'diningRoomManager');
        
        SET v_user_id = LAST_INSERT_ID();
        
        INSERT INTO diningRoomManager (firstName, middleName, lastName, tel, diningRoom, userNumber)
        VALUES (p_firstName, p_maternalName, p_lastName, p_phone, p_diningRoom, v_user_id);
        
        SET p_new_user_id = v_user_id;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_infoRecibo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_infoRecibo`(IN emp_num INT)
BEGIN
    SELECT 
        CONCAT(e.firstName, ' ', e.middleName, ' ', COALESCE(e.lastName, '')) AS Name,
        oe.num AS NumeroOrden,
        oe.dateOrde AS FechaOrden,
        t.num AS NumeroTicket,
        d.name AS NombrePlatillo,
        od.numberDishes AS CantPlatillos,
        d.price AS PrecioPlatillo,
        od.dishDiscount AS DescuentoPlatillo,
        (od.amount - od.dishDiscount) AS PrecioConDescuento,
        oe.totalDiscount AS DescuentoTotal,
        oe.paymentAmount AS TotalTicket
    FROM orderEmp oe
    INNER JOIN employee e ON oe.employee = e.num
    INNER JOIN ticket t ON t.orderEmp = oe.num
    INNER JOIN ord_dish od ON od.orderEmp = oe.num
    INNER JOIN dish d ON od.dish = d.code
    WHERE e.num = emp_num
    ORDER BY oe.dateOrde DESC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_reciboOrden` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_reciboOrden`(
ordNum int
)
begin
select 
    CONCAT(e.firstName, ' ', e.middleName, ' ', e.lastName) as Name, 
   oe.num AS NumeroOrden, 
   oe.dateOrde AS FechaOrden, 
   oe.totalDiscount AS DescuentoTotal, 
   t.num AS NumeroTicket, 
   t.dateTick AS FechaTicket,
   t.total AS TotalTicket,
   d.name as NombrePlatillo,
   rd.numberDishes as CantPlatillos,
   d.price as PrecioPlatillo
from orderEmp oe
inner join ticket t on oe.num = t.orderEmp
inner join employee e ON oe.employee = e.num
inner join ord_dish rd on rd.orderEmp = oe.num
inner join dish d on rd.dish = d.code
where oe.num = ordNum;
end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `infoDinningRoomManager`
--

/*!50001 DROP VIEW IF EXISTS `infoDinningRoomManager`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `infoDinningRoomManager` AS select concat(`d`.`firstName`,' ',`d`.`middleName`,' ',`d`.`lastName`) AS `Name`,`d`.`userNumber` AS `id`,`u`.`email` AS `E-mail`,`d`.`tel` AS `phone`,`dg`.`name` AS `diningRoom` from ((`users` `u` join `diningRoomManager` `d` on((`d`.`userNumber` = `u`.`num`))) join `diningRoom` `dg` on((`d`.`diningRoom` = `dg`.`num`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `infoEmployee`
--

/*!50001 DROP VIEW IF EXISTS `infoEmployee`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `infoEmployee` AS select concat(`e`.`firstName`,' ',`e`.`middleName`,' ',`e`.`lastName`) AS `Name`,`e`.`userNum` AS `id`,`u`.`email` AS `E-mail`,`e`.`tel` AS `phone`,`fc`.`name` AS `factory` from ((`users` `u` join `employee` `e` on((`e`.`userNum` = `u`.`num`))) join `factory` `fc` on((`e`.`factory` = `fc`.`code`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `infoGeneralManager`
--

/*!50001 DROP VIEW IF EXISTS `infoGeneralManager`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `infoGeneralManager` AS select concat(`f`.`firstName`,' ',`f`.`middleName`,' ',`f`.`lastName`) AS `Name`,`f`.`user_num` AS `id`,`u`.`email` AS `E-mail`,`f`.`tel` AS `phone`,`fac`.`name` AS `factory` from ((`users` `u` join `factoryAdmin` `f` on((`u`.`num` = `f`.`user_num`))) join `factory` `fac` on((`f`.`factory` = `fac`.`code`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `purchaseOrderDetails`
--

/*!50001 DROP VIEW IF EXISTS `purchaseOrderDetails`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `purchaseOrderDetails` AS select `po`.`num` AS `orderNumber`,`po`.`datePurs` AS `orderDate`,concat(`drm`.`firstName`,' ',`drm`.`middleName`,' ',`drm`.`lastName`) AS `managerFullName`,`dr`.`name` AS `diningRoomName`,`s`.`name` AS `supplierName`,group_concat(concat(`i`.`name`,' (',`ip`.`numberIngred`,')') order by `i`.`name` ASC separator ', ') AS `ingredients`,`po`.`amountPayment` AS `totalAmount` from (((((`purchaseOrder` `po` join `diningRoomManager` `drm` on((`po`.`diningroommanager` = `drm`.`num`))) join `diningRoom` `dr` on((`drm`.`diningRoom` = `dr`.`num`))) join `supplier` `s` on((`po`.`supplier` = `s`.`code`))) join `ingred_purcha` `ip` on((`po`.`num` = `ip`.`purchaseOrder`))) join `ingredients` `i` on((`ip`.`ingredients` = `i`.`num`))) group by `po`.`num`,`po`.`datePurs`,`managerFullName`,`dr`.`name`,`s`.`name`,`po`.`amountPayment` order by `po`.`num` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-11-29  7:53:46

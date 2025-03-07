-- MySQL dump 10.13  Distrib 5.7.24, for Win64 (x86_64)
--
-- Host: localhost    Database: meathead
-- ------------------------------------------------------
-- Server version	5.7.24

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `kills_counter`
--

DROP TABLE IF EXISTS `kills_counter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kills_counter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kill_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kills_counter`
--

LOCK TABLES `kills_counter` WRITE;
/*!40000 ALTER TABLE `kills_counter` DISABLE KEYS */;
/*!40000 ALTER TABLE `kills_counter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registration`
--

DROP TABLE IF EXISTS `registration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registration` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) DEFAULT NULL,
  `password` varchar(72) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registration`
--

LOCK TABLES `registration` WRITE;
/*!40000 ALTER TABLE `registration` DISABLE KEYS */;
INSERT INTO `registration` VALUES (1,'admin','$2y$10$tozxXbo1Tg09.LGbXsbWaOKpuhwhrs3vntLLePBn3fnlC6Fm61xVC');
/*!40000 ALTER TABLE `registration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trait_drop_times`
--

DROP TABLE IF EXISTS `trait_drop_times`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trait_drop_times` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `trait$id` int(10) unsigned NOT NULL,
  `drop_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `trait$id` (`trait$id`),
  CONSTRAINT `trait_drop_times_ibfk_1` FOREIGN KEY (`trait$id`) REFERENCES `traits` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trait_drop_times`
--

LOCK TABLES `trait_drop_times` WRITE;
/*!40000 ALTER TABLE `trait_drop_times` DISABLE KEYS */;
/*!40000 ALTER TABLE `trait_drop_times` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traits`
--

DROP TABLE IF EXISTS `traits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `times_dropped` int(10) unsigned DEFAULT NULL,
  `trait_points` varchar(10) DEFAULT NULL,
  `is_burn` tinyint(1) DEFAULT NULL,
  `is_scarce` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traits`
--

LOCK TABLES `traits` WRITE;
/*!40000 ALTER TABLE `traits` DISABLE KEYS */;
INSERT INTO `traits` VALUES (1,'Adrenaline',0,'1',0,0),(2,'Assailant',0,'1',0,0),(3,'Blade Seer',0,'1',0,0),(4,'Dauntless',0,'1',0,0),(5,'Decoy Supply',0,'1',0,0),(6,'Kiteskin',0,'1',0,0),(7,'Magpie',0,'1',0,0),(8,'Poacher',0,'1',0,0),(9,'Poison Sense',0,'1',0,0),(10,'Vigilant',0,'1',0,0),(11,'Whispersmith',0,'1',0,0),(12,'Bulwark',0,'2',0,0),(13,'Hundred Hands',0,'2',0,0),(14,'Martialist',0,'2',0,0),(15,'Scopesmith',0,'2',0,0),(16,'Steady Aim',0,'2',0,0),(17,'Ambidextrious',0,'3',0,0),(18,'Bloodless',0,'3',0,0),(19,'Bolt Thrower',0,'3',0,0),(20,'Gator Legs',0,'3',0,0),(21,'Ghoul',0,'3',0,0),(22,'Hornskin',0,'3',0,0),(23,'Iron Eye',0,'3',0,0),(24,'Mithridatist',0,'3',0,0),(25,'Resiliance',0,'3',0,0),(26,'Salveskin',0,'3',0,0),(27,'Vigor',0,'3',0,0),(28,'Vulture',0,'3',0,0),(29,'Beastface',0,'4',0,0),(30,'Bulletgrubber',0,'4',0,0),(31,'Determination',0,'4',0,0),(32,'Levering',0,'4',0,0),(33,'Necromancer',0,'4',1,0),(34,'Packmule',0,'4',0,0),(35,'Serpent',0,'4',0,0),(36,'Silent Killer',0,'4',0,0),(37,'Conduit',0,'5',0,0),(38,'Greyhound',0,'5',0,0),(39,'Lightfoot',0,'5',0,0),(40,'Physician',0,'5',0,0),(41,'Witness',0,'5',0,0),(42,'Pitcher',0,'6',0,0),(43,'Quartermaster',0,'6',0,0),(44,'Frontiersman',0,'7',0,0),(45,'Fanning',0,'8',0,0),(46,'Doctor',0,'9',0,0),(47,'Death Cheat',0,'NULL',1,1),(48,'Rampage',0,'NULL',1,1),(49,'Relentless',0,'NULL',1,1),(50,'Remedy',0,'NULL',1,1),(51,'Shadow Leap',0,'NULL',0,1),(52,'Shadow',0,'NULL',0,1);
/*!40000 ALTER TABLE `traits` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-08-15  8:48:51

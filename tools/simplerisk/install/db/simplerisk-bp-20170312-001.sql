SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

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
-- Table structure for table `CVSS_scoring`
--

DROP TABLE IF EXISTS `CVSS_scoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CVSS_scoring` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `metric_name` varchar(30) NOT NULL,
  `abrv_metric_name` varchar(3) NOT NULL,
  `metric_value` varchar(30) NOT NULL,
  `abrv_metric_value` varchar(3) NOT NULL,
  `numeric_value` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CVSS_scoring`
--

LOCK TABLES `CVSS_scoring` WRITE;
/*!40000 ALTER TABLE `CVSS_scoring` DISABLE KEYS */;
INSERT INTO `CVSS_scoring` VALUES (1,'AccessComplexity','AC','Alto','H',0.35),(2,'AccessComplexity','AC','Médio','M',0.61),(3,'AccessComplexity','AC','Baixo','L',0.71),(4,'AccessVector','AV','Local','L',0.395),(5,'AccessVector','AV','Rede adjacente','A',0.646),(6,'AccessVector','AV','Rede','N',1),(7,'Authentication','Au','Nenhum','N',0.704),(8,'Authentication','Au','Única instância','S',0.56),(9,'Authentication','Au','Múltiplas instâncias','M',0.45),(10,'AvailabilityRequirement','AR','Indefinido','ND',1),(11,'AvailabilityRequirement','AR','Baixo','L',0.5),(12,'AvailabilityRequirement','AR','Médio','M',1),(13,'AvailabilityRequirement','AR','Alto','H',1.51),(14,'AvailImpact','A','Nenhum','N',0),(15,'AvailImpact','A','Parcial','P',0.275),(16,'AvailImpact','A','Completo','C',0.66),(17,'CollateralDamagePotential','CDP','Indefinido','ND',0),(18,'CollateralDamagePotential','CDP','Nenhum','N',0),(19,'CollateralDamagePotential','CDP','Baixo (baixa perda)','L',0.1),(20,'CollateralDamagePotential','CDP','Baixo-Médio','LM',0.3),(21,'CollateralDamagePotential','CDP','Médio-Alto','MH',0.4),(22,'CollateralDamagePotential','CDP','Alto','H',0.5),(23,'ConfidentialityRequirement','CR','Indefinido','ND',1),(24,'ConfidentialityRequirement','CR','Baixo','L',0.5),(25,'ConfidentialityRequirement','CR','Médio','M',1),(26,'ConfidentialityRequirement','CR','Alto','H',1.51),(27,'ConfImpact','C','Nenhum','N',0),(28,'ConfImpact','C','Parcial','P',0.275),(29,'ConfImpact','C','Completo','C',0.66),(30,'Exploitability','E','Indefinido','ND',1),(31,'Exploitability','E','Sem exploração existente','U',0.85),(32,'Exploitability','E','Prova de conceito de código','POC',0.9),(33,'Exploitability','E','Exploração funcional existente','F',0.95),(34,'Exploitability','E','Muito difundido','H',1),(35,'IntegImpact','I','Nenhum','N',0),(36,'IntegImpact','I','Parcial','P',0.275),(37,'IntegImpact','I','Completo','C',0.66),(38,'IntegrityRequirement','IR','Indefinido','ND',1),(39,'IntegrityRequirement','IR','Baixo','L',0.5),(40,'IntegrityRequirement','IR','Médio','M',1),(41,'IntegrityRequirement','IR','Alto','H',1.51),(42,'RemediationLevel','RL','Indefinido','ND',1),(43,'RemediationLevel','RL','Oficialmente corrigido','OF',0.87),(44,'RemediationLevel','RL','Temporariamente corrigido','TF',0.9),(45,'RemediationLevel','RL','Solução de contorno','W',0.95),(46,'RemediationLevel','RL','Indisponível','U',1),(47,'ReportConfidence','RC','Indefinido','ND',1),(48,'ReportConfidence','RC','Não confirmado','UC',0.9),(49,'ReportConfidence','RC','Não comprovado','UR',0.95),(50,'ReportConfidence','RC','Confirmado','C',1),(51,'TargetDistribution','TD','Indefinido','ND',1),(52,'TargetDistribution','TD','Nenhum (0%)','N',0),(53,'TargetDistribution','TD','Baixo (0-25%)','L',0.25),(54,'TargetDistribution','TD','Médio (26-75%)','M',0.75),(55,'TargetDistribution','TD','Alto (76-100%)','H',1);
/*!40000 ALTER TABLE `CVSS_scoring` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_answers`
--

DROP TABLE IF EXISTS `assessment_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assessment_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` varchar(200) NOT NULL,
  `submit_risk` tinyint(1) NOT NULL DEFAULT '0',
  `risk_subject` blob NOT NULL,
  `risk_score` int(11) NOT NULL,
  `risk_owner` int(11) DEFAULT NULL,
  `assets` varchar(200) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '999999',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_answers`
--

LOCK TABLES `assessment_answers` WRITE;
/*!40000 ALTER TABLE `assessment_answers` DISABLE KEYS */;
INSERT INTO `assessment_answers` VALUES (1,1,1,'Yes',0,'',0,0,'',1),(2,1,1,'No',1,'Attackers can use unauthorized and unmanaged devices to gain access to network',10,0,'System',2),(3,1,2,'Yes',0,'',0,0,'',1),(4,1,2,'No',1,'Attackers can use unauthorized and unmanaged software to collect sensitive information from compromised systems and other systems connected to them',10,0,'System',2),(5,1,3,'Yes',0,'',0,0,'',1),(6,1,3,'No',1,'Attackers can exploit vulnerable services and settings to compromise operating systems and applications',10,0,'System',2),(7,1,4,'Yes',0,'',0,0,'',1),(8,1,4,'No',1,'Attackers can take advantage of gaps between the appearance of new knowledge and remediation to compromise computer systems',10,0,'System',2),(9,1,5,'Yes',0,'',0,0,'',1),(10,1,5,'No',1,'Attackers can misuse administrative privileges to spread inside the enterprise',10,0,'System',2),(11,1,6,'Yes',0,'',0,0,'',1),(12,1,6,'No',1,'Attackers can hide their location, malicious software, and activities on victim machines due to deficiencies in security logging and analysis',10,0,'System',2),(13,1,7,'Yes',0,'',0,0,'',1),(14,1,7,'No',1,'Attackers can craft content to entice or spoof users into taking actions that greatly increase risk and allow introduction of malicious code, loss of valuable data, and other attacks',10,0,'System',2),(15,1,8,'Yes',0,'',0,0,'',1),(16,1,8,'No',1,'Attackers can use malicious software to attack our systems, devices, and data',10,0,'System',2),(17,1,9,'Yes',0,'',0,0,'',1),(18,1,9,'No',1,'Attackers can scan for remotely accessible network services that are vulnerable to exploitation',10,0,'System',2),(19,1,10,'Yes',0,'',0,0,'',1),(20,1,10,'No',1,'Attackers can make significant changes to configurations and software on compromised machines and it may be extremely difficult to remove all aspects of their presence',10,0,'System',2),(21,1,11,'Yes',0,'',0,0,'',1),(22,1,11,'No',1,'Attackers can gain access to sensitive data, alter important information, or use compromised machines to pose as trusted systems on our network by exploiting vulnerable services and settings',10,0,'Network',2),(23,1,12,'Yes',0,'',0,0,'',1),(24,1,12,'No',1,'Attackers can exploit vulnerable systems on extranet perimeters to gain access inside our network',10,0,'Network',2),(25,1,13,'Yes',0,'',0,0,'',1),(26,1,13,'No',1,'Attackers can exfiltrate data from our networks compromising the privacy and integrity of sensitive information',10,0,'Network',2),(27,1,14,'Yes',0,'',0,0,'',1),(28,1,14,'No',1,'Attackers can find and exfiltrate important information, cause physical damage, or disrupt operations due to improper separation of sensitive and critical assets from less sensitive information',10,0,'Application',2),(29,1,15,'Yes',0,'',0,0,'',1),(30,1,15,'No',1,'Attackers can gain wireless access and bypass our security perimeters in order to steal data',10,0,'Network',2),(31,1,16,'Yes',0,'',0,0,'',1),(32,1,16,'No',1,'Attackers can impersonate legitimate users by exploting legitimate but inactive user accounts',10,0,'Application',2),(33,1,17,'Yes',0,'',0,0,'',1),(34,1,17,'No',1,'Attackers can exploit employee knowledge gaps to compromise systems and networks',10,0,'Application',2),(35,1,18,'Yes',0,'',0,0,'',1),(36,1,18,'No',1,'Attackers can take advantage of vulnerabilities in software to gain control over vulnerable machines',10,0,'Application',2),(37,1,19,'Yes',0,'',0,0,'',1),(38,1,19,'No',1,'An attacker may have a greater impact, cause more damage, infect more systems, and exfiltrate more sensitive data due to a poor incident response plan',10,0,'Application',2),(39,1,20,'Yes',0,'',0,0,'',1),(40,1,20,'No',1,'Attackers can take advantage of unknown vulnerabilities due to a lack of testing of organization defenses',10,0,'Application',2);
/*!40000 ALTER TABLE `assessment_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_questions`
--

DROP TABLE IF EXISTS `assessment_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assessment_id` int(11) NOT NULL,
  `question` varchar(1000) NOT NULL,
  `order` int(11) NOT NULL DEFAULT '999999',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_questions`
--

LOCK TABLES `assessment_questions` WRITE;
/*!40000 ALTER TABLE `assessment_questions` DISABLE KEYS */;
INSERT INTO `assessment_questions` VALUES (1,1,'Do you actively manage (inventory, track, and correct) all hardware devices on the network so that only authorized devices are given access, and unauthorized and unmanaged devices are found and prevented from gaining access?',1),(2,1,'Do you actively manage (inventory, track, and correct) all software on the network so that only authorized software is installed and can execute, and that unauthorized and unmanaged software is found and prevented from installation or execution?',2),(3,1,'Do you establish, implement, and actively manage (track, report on, correct) the security configuration of laptops, servers, and workstations using a rigorous configuration management and change control process in order to prevent attackers from exploiting vulnerable services and settings?',3),(4,1,'Do you continuously acquire, assess, and take action on new information in order to identify vulnerabilities, remediate, and minimize the window of opportunity for attackers?',4),(5,1,'Do you have processes and tools to track/control/prevent/correct the use, assignment, and configuration of administrative privileges on computers, networks, and applications?',5),(6,1,'Do you collect, manage, and analyze audit logs of events that could help detect, understand, or recover from an attack?',6),(7,1,'Do you minimize the attack surface and the opportunities for attackers to manipulate human behavior through their interaction with web browsers and emails systems?',7),(8,1,'Do you control the installation, spread, and execution of malicious code at multiple points in the enterprise, while optimizing the use of automation to enable rapid updating of defense, data gathering, and corrective action?',8),(9,1,'Do you manage (track/control/correct) the ongoing operational use of ports, protocols, and services on networked devices in order to minimize windows of vulnerability available to attackers?',9),(10,1,'Do you have processes and tools to properly back up critical information with a proven methodology for timely recovery of it?',10),(11,1,'Do you establish, implement, and actively manage (track, report on, correct) the security configuration of network infrastructure devices using a rigorous configuration management and change control process in order to prevent attackers from exploiting vulnerable services and settings?',11),(12,1,'Do you detect/prevent/correct the flow of information transferring networks of different trust levels with a focus on security-damaging data?',12),(13,1,'Do you have processes and tools to prevent data exfiltration, mitigate the effects of exfiltrated data, and ensure the privacy and integrity of sensitive information?',13),(14,1,'Do you have processes and tools to track/control/prevent/correct secure access to critical assets (e.g., information, resources, systems) according to the formal determination of which persons, computers, and applications have a need and right to access these critical assets based on an approved classification?',14),(15,1,'Do you have processes and tools to track/control/prevent/correct the security use of wireless local area networks (LANS), access points, and wireless client systems?',15),(16,1,'Do you actively manage the life cycle of system and application accounts - their creation, use, dormancy, deletion - in order to minimize opportunities for attackers to leverage them?',16),(17,1,'Do all functional roles in the organization (prioritizing those mission-critical to the business and its security) identiy the specific knowledge, skills, and abilities needed to support defense of the enterprise; develop and execute an integrated plan to assess, identify gaps, and remediate through policy, organizational planning, training, and awareness programs?',17),(18,1,'Do you manage the security life cycle of all in-house developed and acquired software in order to prevent, detect, and correct security weaknesses?',18),(19,1,'Do you protect the organization\'s information, as well as its reputation, by developing and implementing an incident response infrastructure (e.g., plans, defined roles, training, communications, management oversight) for quickly discovering an attack and then effectively containing the damage, eradicating the attacker\'s presence, and restoring the integrity of the network and systems?',19),(20,1,'Do you test the overall strength of your organization\'s defenses (the technology, the processes, and the people) by simulating the objectives and actions of an attacker?',20);
/*!40000 ALTER TABLE `assessment_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessments`
--

DROP TABLE IF EXISTS `assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessments`
--

LOCK TABLES `assessments` WRITE;
/*!40000 ALTER TABLE `assessments` DISABLE KEYS */;
INSERT INTO `assessments` VALUES (1,'Critical Security Controls','2016-02-27 23:21:27');
/*!40000 ALTER TABLE `assessments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_values`
--

DROP TABLE IF EXISTS `asset_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_values` (
  `id` int(11) NOT NULL,
  `min_value` int(11) NOT NULL,
  `max_value` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_values`
--

LOCK TABLES `asset_values` WRITE;
/*!40000 ALTER TABLE `asset_values` DISABLE KEYS */;
INSERT INTO `asset_values` VALUES (1,0,100000),(2,100001,200000),(3,200001,300000),(4,300001,400000),(5,400001,500000),(6,500001,600000),(7,600001,700000),(8,700001,800000),(9,800001,900000),(10,900001,1000000);
/*!40000 ALTER TABLE `asset_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `value` int(11) DEFAULT '5',
  `location` int(11) NOT NULL,
  `team` int(11) NOT NULL,
  `details` longtext,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assets`
--

LOCK TABLES `assets` WRITE;
/*!40000 ALTER TABLE `assets` DISABLE KEYS */;
/*!40000 ALTER TABLE `assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_log`
--

DROP TABLE IF EXISTS `audit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_log` (
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `risk_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_log`
--

LOCK TABLES `audit_log` WRITE;
/*!40000 ALTER TABLE `audit_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Acesso'),(2,'Ambiental'),(3,'Monitoração'),(4,'Segurança Física'),(5,'Política e Procedimento'),(6,'Dados Sensíveis'),(7,'Vulnerabilidades'),(8,'Terceiros');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `close_reason`
--

DROP TABLE IF EXISTS `close_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `close_reason` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `close_reason`
--

LOCK TABLES `close_reason` WRITE;
/*!40000 ALTER TABLE `close_reason` DISABLE KEYS */;
INSERT INTO `close_reason` VALUES (0,'Rejeitado'),(1,'Totalmente Mitigada'),(2,'Sistema Removido'),(3,'Cancelado'),(4,'Muito Insignificante');
/*!40000 ALTER TABLE `close_reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `closures`
--

DROP TABLE IF EXISTS `closures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `closures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `closure_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `close_reason` int(11) NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `closures`
--

LOCK TABLES `closures` WRITE;
/*!40000 ALTER TABLE `closures` DISABLE KEYS */;
/*!40000 ALTER TABLE `closures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user` int(11) NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_login_attempts`
--

DROP TABLE IF EXISTS `failed_login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expired` tinyint(4) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `ip` varchar(15) DEFAULT '0.0.0.0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_login_attempts`
--

LOCK TABLES `failed_login_attempts` WRITE;
/*!40000 ALTER TABLE `failed_login_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file_types`
--

DROP TABLE IF EXISTS `file_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file_types` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_types`
--

LOCK TABLES `file_types` WRITE;
/*!40000 ALTER TABLE `file_types` DISABLE KEYS */;
INSERT INTO `file_types` VALUES (1,'image/gif'),(2,'image/jpg'),(3,'image/png'),(4,'image/x-png'),(5,'image/jpeg'),(6,'application/x-pdf'),(7,'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),(8,'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),(9,'application/zip'),(10,'text/rtf'),(11,'application/octet-stream'),(12,'text/plain'),(13,'text/xml'),(14,'text/comma-separated-values'),(15,'application/vnd.ms-excel'),(16,'application/msword'),(17,'application/x-gzip'),(18,'application/force-download'),(19,'application/pdf');
/*!40000 ALTER TABLE `file_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) DEFAULT '0',
  `view_type` int(11) DEFAULT '1',
  `name` varchar(100) NOT NULL,
  `unique_name` varchar(30) NOT NULL,
  `type` varchar(30) NOT NULL,
  `size` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` int(11) NOT NULL,
  `content` longblob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files`
--

LOCK TABLES `files` WRITE;
/*!40000 ALTER TABLE `files` DISABLE KEYS */;
/*!40000 ALTER TABLE `files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `impact`
--

DROP TABLE IF EXISTS `impact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `impact` (
  `name` varchar(20) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impact`
--

LOCK TABLES `impact` WRITE;
/*!40000 ALTER TABLE `impact` DISABLE KEYS */;
INSERT INTO `impact` VALUES ('Insignificante',1),('Menor',2),('Moderado',3),('Maior',4),('Extremo/Catastrófico',5);
/*!40000 ALTER TABLE `impact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(5) NOT NULL,
  `full` varchar(50) NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'en','English'),(2,'bp','Brazilian Portuguese'),(3,'es','Spanish'),(4,'ar','Arabic'),(5,'ca','Catalan'),(6,'cs','Czech'),(7,'da','Danish'),(8,'de','German'),(9,'el','Greek'),(10,'fi','Finnish'),(11,'fr','French'),(12,'he','Hebrew'),(13,'hi','Hindi'),(14,'hu','Hungarian'),(15,'it','Italian'),(16,'ja','Japanese'),(17,'ko','Korean'),(18,'nl','Dutch'),(19,'no','Norwegian'),(20,'pl','Polish'),(21,'pt','Portuguese'),(22,'ro','Romanian'),(23,'ru','Russian'),(24,'sr','Serbian'),(25,'sv','Swedish'),(26,'tr','Turkish'),(27,'uk','Ukranian'),(28,'vi','Vietnamese'),(29,'zh-CN','Chinese Simplified'),(30,'zh-TW','Chinese Traditional');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likelihood`
--

DROP TABLE IF EXISTS `likelihood`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `likelihood` (
  `name` varchar(20) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likelihood`
--

LOCK TABLES `likelihood` WRITE;
/*!40000 ALTER TABLE `likelihood` DISABLE KEYS */;
INSERT INTO `likelihood` VALUES ('Remoto',1),('Improvável',2),('Acreditável',3),('Provável',4),('Quase Certo',5);
/*!40000 ALTER TABLE `likelihood` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location`
--

LOCK TABLES `location` WRITE;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
INSERT INTO `location` VALUES (1,'Todos os Sites');
/*!40000 ALTER TABLE `location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mgmt_reviews`
--

DROP TABLE IF EXISTS `mgmt_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mgmt_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `review` int(11) NOT NULL,
  `reviewer` int(11) NOT NULL,
  `next_step` int(11) NOT NULL,
  `comments` text NOT NULL,
  `next_review` varchar(10) NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mgmt_reviews`
--

LOCK TABLES `mgmt_reviews` WRITE;
/*!40000 ALTER TABLE `mgmt_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `mgmt_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mitigation_effort`
--

DROP TABLE IF EXISTS `mitigation_effort`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mitigation_effort` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mitigation_effort`
--

LOCK TABLES `mitigation_effort` WRITE;
/*!40000 ALTER TABLE `mitigation_effort` DISABLE KEYS */;
INSERT INTO `mitigation_effort` VALUES (1,'Trivial'),(2,'Menor'),(3,'Considerável'),(4,'Significativo'),(5,'Excepcional');
/*!40000 ALTER TABLE `mitigation_effort` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mitigations`
--

DROP TABLE IF EXISTS `mitigations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mitigations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `planning_strategy` int(11) NOT NULL,
  `mitigation_effort` int(11) NOT NULL,
  `mitigation_cost` int(11) NOT NULL DEFAULT '1',
  `mitigation_owner` int(11) NOT NULL,
  `mitigation_team` int(11) NOT NULL,
  `current_solution` text NOT NULL,
  `security_requirements` text NOT NULL,
  `security_recommendations` text NOT NULL,
  `submitted_by` int(11) NOT NULL DEFAULT '1',
  `planning_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mitigations`
--

LOCK TABLES `mitigations` WRITE;
/*!40000 ALTER TABLE `mitigations` DISABLE KEYS */;
/*!40000 ALTER TABLE `mitigations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `next_step`
--

DROP TABLE IF EXISTS `next_step`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `next_step` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `next_step`
--

LOCK TABLES `next_step` WRITE;
/*!40000 ALTER TABLE `next_step` DISABLE KEYS */;
INSERT INTO `next_step` VALUES (1,'Aceitar até a Próxima Revisão'),(2,'Considerar para Projeto'),(3,'Enviar como um Problema de Produção');
/*!40000 ALTER TABLE `next_step` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset`
--

DROP TABLE IF EXISTS `password_reset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset` (
  `username` varchar(200) DEFAULT NULL,
  `token` varchar(20) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset`
--

LOCK TABLES `password_reset` WRITE;
/*!40000 ALTER TABLE `password_reset` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pending_risks`
--

DROP TABLE IF EXISTS `pending_risks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pending_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assessment_id` int(11) NOT NULL,
  `subject` blob NOT NULL,
  `score` int(11) NOT NULL,
  `owner` int(11) DEFAULT NULL,
  `asset` varchar(200) DEFAULT NULL,
  `submission_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pending_risks`
--

LOCK TABLES `pending_risks` WRITE;
/*!40000 ALTER TABLE `pending_risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `pending_risks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `planning_strategy`
--

DROP TABLE IF EXISTS `planning_strategy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `planning_strategy` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planning_strategy`
--

LOCK TABLES `planning_strategy` WRITE;
/*!40000 ALTER TABLE `planning_strategy` DISABLE KEYS */;
INSERT INTO `planning_strategy` VALUES (1,'Pesquisa'),(2,'Aceitar'),(3,'Mitigar'),(4,'Observar'),(5,'Transferência');
/*!40000 ALTER TABLE `planning_strategy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `order` int(11) NOT NULL DEFAULT '999999',
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (0,'Riscos não Atribuídos',0,1);
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `regulation`
--

DROP TABLE IF EXISTS `regulation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `regulation` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regulation`
--

LOCK TABLES `regulation` WRITE;
/*!40000 ALTER TABLE `regulation` DISABLE KEYS */;
INSERT INTO `regulation` VALUES (1,'PCI DSS'),(2,'Sarbanes-Oxley (SOX)'),(3,'HIPAA'),(4,'ISO 27001');
/*!40000 ALTER TABLE `regulation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `review` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review`
--

LOCK TABLES `review` WRITE;
/*!40000 ALTER TABLE `review` DISABLE KEYS */;
INSERT INTO `review` VALUES (1,'Aprovar Risco'),(2,'Rechazar Riesgo y Cerrar');
/*!40000 ALTER TABLE `review` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review_levels`
--

DROP TABLE IF EXISTS `review_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `review_levels` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` int(11) NOT NULL,
  `name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_levels`
--

LOCK TABLES `review_levels` WRITE;
/*!40000 ALTER TABLE `review_levels` DISABLE KEYS */;
INSERT INTO `review_levels` VALUES (2,90,'High'),(3,180,'Medium'),(4,360,'Low'),(5,360,'Insignificant'),(1,90,'Very High');
/*!40000 ALTER TABLE `review_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_levels`
--

DROP TABLE IF EXISTS `risk_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_levels` (
  `value` decimal(3,1) NOT NULL,
  `name` varchar(20) NOT NULL,
  `color` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_levels`
--

LOCK TABLES `risk_levels` WRITE;
/*!40000 ALTER TABLE `risk_levels` DISABLE KEYS */;
INSERT INTO `risk_levels` VALUES (7.0,'High','orangered'),(4.0,'Medium','orange'),(0.0,'Low','yellow'),(10.1,'Very High','red');
/*!40000 ALTER TABLE `risk_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_models`
--

DROP TABLE IF EXISTS `risk_models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_models` (
  `value` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_models`
--

LOCK TABLES `risk_models` WRITE;
/*!40000 ALTER TABLE `risk_models` DISABLE KEYS */;
INSERT INTO `risk_models` VALUES (1,'Probabilidade x Impacto + 2(Impacto)'),(2,'Probabilidade x Impacto + Impacto'),(3,'Probabilidade x Impacto'),(4,'Probabilidade x Impacto + Probabilidade'),(5,'Probabilidade x Impacto + 2(Probabilidade)');
/*!40000 ALTER TABLE `risk_models` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_scoring`
--

DROP TABLE IF EXISTS `risk_scoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_scoring` (
  `id` int(11) NOT NULL,
  `scoring_method` int(11) NOT NULL,
  `calculated_risk` float NOT NULL,
  `CLASSIC_likelihood` float NOT NULL DEFAULT '5',
  `CLASSIC_impact` float NOT NULL DEFAULT '5',
  `CVSS_AccessVector` varchar(3) NOT NULL DEFAULT 'N',
  `CVSS_AccessComplexity` varchar(3) NOT NULL DEFAULT 'L',
  `CVSS_Authentication` varchar(3) NOT NULL DEFAULT 'N',
  `CVSS_ConfImpact` varchar(3) NOT NULL DEFAULT 'C',
  `CVSS_IntegImpact` varchar(3) NOT NULL DEFAULT 'C',
  `CVSS_AvailImpact` varchar(3) NOT NULL DEFAULT 'C',
  `CVSS_Exploitability` varchar(3) NOT NULL DEFAULT 'ND',
  `CVSS_RemediationLevel` varchar(3) NOT NULL DEFAULT 'ND',
  `CVSS_ReportConfidence` varchar(3) NOT NULL DEFAULT 'ND',
  `CVSS_CollateralDamagePotential` varchar(3) NOT NULL DEFAULT 'ND',
  `CVSS_TargetDistribution` varchar(3) NOT NULL DEFAULT 'ND',
  `CVSS_ConfidentialityRequirement` varchar(3) NOT NULL DEFAULT 'ND',
  `CVSS_IntegrityRequirement` varchar(3) NOT NULL DEFAULT 'ND',
  `CVSS_AvailabilityRequirement` varchar(3) NOT NULL DEFAULT 'ND',
  `DREAD_DamagePotential` int(11) DEFAULT '10',
  `DREAD_Reproducibility` int(11) DEFAULT '10',
  `DREAD_Exploitability` int(11) DEFAULT '10',
  `DREAD_AffectedUsers` int(11) DEFAULT '10',
  `DREAD_Discoverability` int(11) DEFAULT '10',
  `OWASP_SkillLevel` int(11) DEFAULT '10',
  `OWASP_Motive` int(11) DEFAULT '10',
  `OWASP_Opportunity` int(11) DEFAULT '10',
  `OWASP_Size` int(11) DEFAULT '10',
  `OWASP_EaseOfDiscovery` int(11) DEFAULT '10',
  `OWASP_EaseOfExploit` int(11) DEFAULT '10',
  `OWASP_Awareness` int(11) DEFAULT '10',
  `OWASP_IntrusionDetection` int(11) DEFAULT '10',
  `OWASP_LossOfConfidentiality` int(11) DEFAULT '10',
  `OWASP_LossOfIntegrity` int(11) DEFAULT '10',
  `OWASP_LossOfAvailability` int(11) DEFAULT '10',
  `OWASP_LossOfAccountability` int(11) DEFAULT '10',
  `OWASP_FinancialDamage` int(11) DEFAULT '10',
  `OWASP_ReputationDamage` int(11) DEFAULT '10',
  `OWASP_NonCompliance` int(11) DEFAULT '10',
  `OWASP_PrivacyViolation` int(11) DEFAULT '10',
  `Custom` float DEFAULT '10',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_scoring`
--

LOCK TABLES `risk_scoring` WRITE;
/*!40000 ALTER TABLE `risk_scoring` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_scoring` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_scoring_history`
--

DROP TABLE IF EXISTS `risk_scoring_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_scoring_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `calculated_risk` float NOT NULL,
  `last_update` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_scoring_history`
--

LOCK TABLES `risk_scoring_history` WRITE;
/*!40000 ALTER TABLE `risk_scoring_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_scoring_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risks`
--

DROP TABLE IF EXISTS `risks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(20) NOT NULL,
  `subject` varchar(300) NOT NULL,
  `reference_id` varchar(20) NOT NULL DEFAULT '',
  `regulation` int(11) DEFAULT NULL,
  `control_number` varchar(20) DEFAULT NULL,
  `location` int(11) NOT NULL,
  `source` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `team` int(11) NOT NULL,
  `technology` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `manager` int(11) NOT NULL,
  `assessment` longtext NOT NULL,
  `notes` longtext NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mitigation_id` int(11) DEFAULT '0',
  `mgmt_review` int(11) DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '0',
  `close_id` int(11) DEFAULT NULL,
  `submitted_by` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risks`
--

LOCK TABLES `risks` WRITE;
/*!40000 ALTER TABLE `risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `risks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risks_to_assets`
--

DROP TABLE IF EXISTS `risks_to_assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risks_to_assets` (
  `risk_id` int(11) DEFAULT NULL,
  `asset_id` int(11) NOT NULL,
  `asset` varchar(200) NOT NULL,
  UNIQUE KEY `risk_id` (`risk_id`,`asset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risks_to_assets`
--

LOCK TABLES `risks_to_assets` WRITE;
/*!40000 ALTER TABLE `risks_to_assets` DISABLE KEYS */;
/*!40000 ALTER TABLE `risks_to_assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scoring_methods`
--

DROP TABLE IF EXISTS `scoring_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scoring_methods` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scoring_methods`
--

LOCK TABLES `scoring_methods` WRITE;
/*!40000 ALTER TABLE `scoring_methods` DISABLE KEYS */;
INSERT INTO `scoring_methods` VALUES (1,'Classic'),(2,'CVSS'),(3,'DREAD'),(4,'OWASP'),(5,'Custom');
/*!40000 ALTER TABLE `scoring_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(32) NOT NULL,
  `access` int(10) unsigned DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `name` varchar(50) NOT NULL,
  `value` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES ('currency','$'),('db_version','20170312-001'),('default_asset_valuation','5'),('max_upload_size','5120000'),('pass_policy_alpha_required','1'),('pass_policy_attempt_lockout','0'),('pass_policy_attempt_lockout_time','10'),('pass_policy_digits_required','1'),('pass_policy_enabled','1'),('pass_policy_lower_required','1'),('pass_policy_max_age','0'),('pass_policy_min_age','0'),('pass_policy_min_chars','8'),('pass_policy_re_use_tracking','0'),('pass_policy_special_required','1'),('pass_policy_upper_required','1'),('phpmailer_from_email','noreply@simplerisk.it'),('phpmailer_from_name','SimpleRisk'),('phpmailer_host','smtp1.example.com'),('phpmailer_password','secret'),('phpmailer_port','587'),('phpmailer_replyto_email','noreply@simplerisk.it'),('phpmailer_replyto_name','SimpleRisk'),('phpmailer_smtpauth','false'),('phpmailer_smtpsecure','none'),('phpmailer_transport','sendmail'),('phpmailer_username','user@example.com'),('registration_registered','0'),('risk_model','3'),('strict_user_validation','1');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `source`
--

DROP TABLE IF EXISTS `source`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `source` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `source`
--

LOCK TABLES `source` WRITE;
/*!40000 ALTER TABLE `source` DISABLE KEYS */;
INSERT INTO `source` VALUES (1,'Pessoas'),(2,'Processo'),(3,'Sistema'),(4,'Externo');
/*!40000 ALTER TABLE `source` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status`
--

LOCK TABLES `status` WRITE;
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
INSERT INTO `status` VALUES (1,'Novo'),(2,'Mitigação Planejado'),(3,'Gestão Avaliado'),(4,'Fechadas'),(5,'Reaberta'),(6,'Não Tratada'),(7,'Tratado');
/*!40000 ALTER TABLE `status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team`
--

LOCK TABLES `team` WRITE;
/*!40000 ALTER TABLE `team` DISABLE KEYS */;
INSERT INTO `team` VALUES (1,'Gestão de Filial'),(2,'Colaboração'),(3,'Data Center & Storage'),(4,'Banco de Dados'),(5,'Segurança da Informação'),(6,'Sistemas de Gestão de TI'),(7,'Rede'),(8,'Unix'),(9,'Sistemas Web'),(10,'Windows');
/*!40000 ALTER TABLE `team` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `technology`
--

DROP TABLE IF EXISTS `technology`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `technology` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `technology`
--

LOCK TABLES `technology` WRITE;
/*!40000 ALTER TABLE `technology` DISABLE KEYS */;
INSERT INTO `technology` VALUES (1,'All'),(2,'Anti-Virus'),(3,'Backups'),(4,'Smartphone'),(5,'Switche'),(6,'Datacenter'),(7,'Rota de E-mail'),(8,'Colaboração em Tempo Real'),(9,'Mensagens'),(10,'Dispositivo Móvel'),(11,'Rede'),(12,'Energia'),(13,'Acesso Remoto'),(14,'Servidor de Arquivos'),(15,'Telefonia'),(16,'Unix'),(17,'Virtualização'),(18,'Web'),(19,'Windows');
/*!40000 ALTER TABLE `technology` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `value` int(11) NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `lockout` tinyint(4) NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT 'simplerisk',
  `username` blob NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` blob NOT NULL,
  `salt` varchar(20) DEFAULT NULL,
  `password` binary(60) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_password_change_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `teams` varchar(4000) NOT NULL DEFAULT 'none',
  `lang` varchar(5) DEFAULT NULL,
  `assessments` tinyint(1) NOT NULL DEFAULT '0',
  `asset` tinyint(1) NOT NULL DEFAULT '0',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `review_veryhigh` tinyint(1) NOT NULL DEFAULT '0',
  `review_high` tinyint(1) NOT NULL DEFAULT '0',
  `review_medium` tinyint(1) NOT NULL DEFAULT '0',
  `review_low` tinyint(1) NOT NULL DEFAULT '0',
  `review_insignificant` tinyint(1) NOT NULL DEFAULT '0',
  `submit_risks` tinyint(1) NOT NULL DEFAULT '0',
  `modify_risks` tinyint(1) NOT NULL DEFAULT '0',
  `plan_mitigations` tinyint(1) NOT NULL DEFAULT '0',
  `close_risks` tinyint(1) NOT NULL DEFAULT '1',
  `multi_factor` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,1,0,'simplerisk','admin','Admin','user@example.com','sAbwTbIFywWKcheyQw9a','$2a$15$7b2601b4979b1ad031b2fuqf1XkeSa4iNxsHK27tq5Va2jLhzkShW','2015-07-29 09:17:32','2017-01-03 05:59:57','all',NULL,1,1,1,1,1,1,1,1,1,1,1,1,1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_pass_history`
--

DROP TABLE IF EXISTS `user_pass_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_pass_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `salt` varchar(20) NOT NULL,
  `password` binary(60) NOT NULL,
  `add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_pass_history`
--

LOCK TABLES `user_pass_history` WRITE;
/*!40000 ALTER TABLE `user_pass_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_pass_history` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

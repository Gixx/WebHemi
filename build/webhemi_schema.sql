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
-- Create database `webhemi`
--
CREATE DATABASE IF NOT EXISTS `webhemi`;
USE `webhemi`;

--
-- Table structure for table `webhemi_application`
--

DROP TABLE IF EXISTS `webhemi_application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_application` (
  `id_application` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL DEFAULT '',
  `is_read_only` TINYINT(1) NOT NULL DEFAULT 0,
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_application`),
  UNIQUE KEY `unq_application_name` (`name`),
  UNIQUE KEY `unq_application_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_application`
--

LOCK TABLES `webhemi_application` WRITE;
/*!40000 ALTER TABLE `webhemi_application` DISABLE KEYS */;
INSERT INTO `webhemi_application` VALUES
  (1, 'admin',   'Admin',   'Administrative area.',                             1, NOW(), NULL),
  (2, 'website', 'Website', 'The default application for the `www` subdomain.', 1, NOW(), NULL);
/*!40000 ALTER TABLE `webhemi_application` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_lock`
--

DROP TABLE IF EXISTS `webhemi_lock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_lock` (
  `id_lock` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_ip` VARCHAR(15) NOT NULL,
  `failure_counter` INT(10) unsigned NOT NULL DEFAULT '0',
  `date_lock` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id_lock`),
  KEY `idx_lock_client_ip` (`client_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

-- AM - Access Management

--
-- Table structure for table `webhemi_am_resource`
--

DROP TABLE IF EXISTS `webhemi_am_resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_am_resource` (
  `id_am_resource` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL DEFAULT '',
  `is_read_only` TINYINT(1) NOT NULL DEFAULT 0,
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_am_resource`),
  UNIQUE KEY `unq_am_resource_name` (`name`),
  UNIQUE KEY `unq_am_resource_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_am_resource`
--

LOCK TABLES `webhemi_am_resource` WRITE;
/*!40000 ALTER TABLE `webhemi_am_resource` DISABLE KEYS */;
INSERT INTO `webhemi_am_resource` VALUES
  (1,  '\WebHemi\Middleware\Action\Admin\DashboardAction', 'The Dashboard page', '', 1, NOW(), NULL);
/*!40000 ALTER TABLE `webhemi_am_resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_am_policy`
--

DROP TABLE IF EXISTS `webhemi_am_policy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_am_policy` (
  `id_am_policy` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  -- If key is NULL, then the policy is applied to all resources
  `fk_am_resource` INT(10) UNSIGNED DEFAULT NULL,
  -- If key is NULL, then the policy is applied to all applications
  `fk_application` INT(10) UNSIGNED DEFAULT NULL,
  `name` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL DEFAULT '',
  `is_read_only` TINYINT(1) NOT NULL DEFAULT 0,
  `is_allowed` TINYINT(1) NOT NULL DEFAULT 1,
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_am_policy`),
  UNIQUE KEY `unq_am_policy` (`fk_am_resource`, `fk_application`),
  UNIQUE KEY `unq_am_policy_title` (`title`),
  KEY `idx_am_policy_fk_am_resource` (`fk_am_resource`),
  KEY `idx_am_policy_fk_application` (`fk_application`),
  CONSTRAINT `fk_am_policy_fk_am_resource` FOREIGN KEY (`fk_am_resource`) REFERENCES `webhemi_am_resource` (`id_am_resource`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_am_policy_fk_application` FOREIGN KEY (`fk_application`) REFERENCES `webhemi_application` (`id_application`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_am_policy`
--

LOCK TABLES `webhemi_am_policy` WRITE;
/*!40000 ALTER TABLE `webhemi_am_policy` DISABLE KEYS */;
INSERT INTO `webhemi_am_policy` VALUES
  (1, NULL, NULL, 'supervisor', 'Supervisor access', 'Access to all resources in every application.', 1, 1, NOW(), NULL),
  (2, 1, 1, 'dashboard', 'Dashboard visitor', 'Access to the Admin/Dashboard page.', 1, 1, NOW(), NULL);
/*!40000 ALTER TABLE `webhemi_am_policy` ENABLE KEYS */;
UNLOCK TABLES;

-- IM - Identity Management

--
-- Table structure for table `webhemi_user`
--

DROP TABLE IF EXISTS `webhemi_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user` (
  `id_user` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  -- Hashed password. MD5 and SHA1 are not recommended.
  `password` VARCHAR(60) NOT NULL,
  -- Hash is used in emails and auto-login cookie to identify user without credentials. Once used a new one should be generated.
  `hash` VARCHAR(32) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT '0',
  `is_enabled` TINYINT(1) NOT NULL DEFAULT '0',
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `unq_user_username` (`username`),
  UNIQUE KEY `unq_user_email` (`email`),
  UNIQUE KEY `unq_user_hash` (`hash`),
  KEY `idx_user_password` (`password`),
  KEY `idx_user_is_active` (`is_active`),
  KEY `idx_user_is_enabled` (`is_enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_user`
--

LOCK TABLES `webhemi_user` WRITE;
/*!40000 ALTER TABLE `webhemi_user` DISABLE KEYS */;
INSERT INTO `webhemi_user` VALUES
  (1, 'admin', 'admin@foo.org', '$2y$09$dmrDfcYZt9jORA4vx9MKpeyRt0ilCH/gxSbSHcfBtGaghMJ30tKzS', '', 1, 1, NOW(), NULL);
/*!40000 ALTER TABLE `webhemi_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_user_meta`
--

DROP TABLE IF EXISTS `webhemi_user_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user_meta` (
  `id_user_meta` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_user` INT(10) UNSIGNED NOT NULL,
  `meta_key` VARCHAR(255) NOT NULL,
  `meta_data` LONGTEXT NOT NULL,
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user_meta`),
  UNIQUE KEY `unq_user_meta` (`fk_user`,`meta_key`),
  CONSTRAINT `fk_user_meta_fk_user` FOREIGN KEY (`fk_user`) REFERENCES `webhemi_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_user_meta`
--

LOCK TABLES `webhemi_user_meta` WRITE;
/*!40000 ALTER TABLE `webhemi_user_meta` DISABLE KEYS */;
INSERT INTO `webhemi_user_meta` VALUES
  (NULL, 1, 'avatar',           '',          NOW(), NULL),
  (NULL, 1, 'details',          '',          NOW(), NULL),
  (NULL, 1, 'emailVisible',     '0',         NOW(), NULL),
  (NULL, 1, 'displayName' ,     'Admin Joe', NOW(), NULL),
  (NULL, 1, 'headLine',         '',          NOW(), NULL),
  (NULL, 1, 'instantMessengers','',          NOW(), NULL),
  (NULL, 1, 'location',         '',          NOW(), NULL),
  (NULL, 1, 'phoneNumbers',     '',          NOW(), NULL),
  (NULL, 1, 'socialNetworks',   '',          NOW(), NULL),
  (NULL, 1, 'websites',         '',          NOW(), NULL);
/*!40000 ALTER TABLE `webhemi_user_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_user_policy`
--

DROP TABLE IF EXISTS `webhemi_user_to_am_policy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user_to_am_policy` (
  `id_user_to_am_policy` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_user` INT(10) UNSIGNED NOT NULL,
  `fk_am_policy` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_user_to_am_policy`),
  UNIQUE KEY `unq_user_to_am_policy` (`fk_user`, `fk_am_policy`),
  KEY `idx_user_to_am_policy_fk_user` (`fk_user`),
  KEY `idx_user_to_am_policy_fk_am_policy` (`fk_am_policy`),
  CONSTRAINT `fk_user_to_am_policy_fk_user` FOREIGN KEY (`fk_user`) REFERENCES `webhemi_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_to_am_policy_fk_am_policy` FOREIGN KEY (`fk_am_policy`) REFERENCES `webhemi_am_policy` (`id_am_policy`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Users can be assigned to policies dierectly --
--
-- Dumping data for table `webhemi_user_policy`
--

LOCK TABLES `webhemi_user_to_am_policy` WRITE;
/*!40000 ALTER TABLE `webhemi_user_to_am_policy` DISABLE KEYS */;
INSERT INTO `webhemi_user_to_am_policy` VALUES
  (NULL, 1, 1);
/*!40000 ALTER TABLE `webhemi_user_to_am_policy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_user_group`
--

DROP TABLE IF EXISTS `webhemi_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user_group` (
  `id_user_group` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `title` VARCHAR(30) NOT NULL,
  `description` TEXT NOT NULL DEFAULT '',
  `is_read_only` TINYINT(1) NOT NULL DEFAULT 0,
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user_group`),
  UNIQUE KEY `unq_user_group_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_user_group`
--

LOCK TABLES `webhemi_user_group` WRITE;
/*!40000 ALTER TABLE `webhemi_user_group` DISABLE KEYS */;
INSERT INTO `webhemi_user_group` VALUES
  (1, 'admin', 'Administrators', 'Group for global administrators', 1, NOW(), NULL),
  (2, 'guest', 'Guests', 'Group for guests.', 1, NOW(), NULL);
/*!40000 ALTER TABLE `webhemi_user_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_user_to_user_group`
--

DROP TABLE IF EXISTS `webhemi_user_to_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user_to_user_group` (
  `id_user_to_user_group` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_user` INT(10) UNSIGNED NOT NULL,
  `fk_user_group` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_user_to_user_group`),
  UNIQUE KEY `unq_user_to_user_group` (`fk_user`, `fk_user_group`),
  KEY `idx_user_to_user_group_fk_user` (`fk_user`),
  KEY `idx_user_to_user_group_fk_user_group` (`fk_user_group`),
  CONSTRAINT `fk_user_to_user_group_fk_user` FOREIGN KEY (`fk_user`) REFERENCES `webhemi_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_to_user_group_fk_user_group` FOREIGN KEY (`fk_user_group`) REFERENCES `webhemi_user_group` (`id_user_group`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_user_to_user_group`
--

LOCK TABLES `webhemi_user_to_user_group` WRITE;
/*!40000 ALTER TABLE `webhemi_user_to_user_group` DISABLE KEYS */;
INSERT INTO `webhemi_user_to_user_group` VALUES
  (NULL, 1, 1);
/*!40000 ALTER TABLE `webhemi_user_to_user_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_user_group_to_am_policy`
--

DROP TABLE IF EXISTS `webhemi_user_group_to_am_policy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user_group_to_am_policy` (
  `id_user_group_to_am_policy` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_user_group` INT(10) UNSIGNED NOT NULL,
  `fk_am_policy` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_user_group_to_am_policy`),
  UNIQUE KEY `unq_user_group_to_am_policy` (`fk_user_group`, `fk_am_policy`),
  KEY `idx_user_group_to_am_policy_fk_user_group` (`fk_user_group`),
  KEY `idx_user_group_to_am_policy_fk_am_policy` (`fk_am_policy`),
  CONSTRAINT `fk_user_group_to_am_policy_fk_user_group` FOREIGN KEY (`fk_user_group`) REFERENCES `webhemi_user_group` (`id_user_group`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_group_to_am_policy_fk_am_policy` FOREIGN KEY (`fk_am_policy`) REFERENCES `webhemi_am_policy` (`id_am_policy`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_user_group_to_am_policy`
--

LOCK TABLES `webhemi_user_group_to_am_policy` WRITE;
/*!40000 ALTER TABLE `webhemi_user_group_to_am_policy` DISABLE KEYS */;
INSERT INTO `webhemi_user_group_to_am_policy` VALUES
  (NULL, 1, 1);
/*!40000 ALTER TABLE `webhemi_user_group_to_am_policy` ENABLE KEYS */;
UNLOCK TABLES;

-- FS - Filesystem

--
-- Table structure for table `webhemi_filesystem_folder`
--

DROP TABLE IF EXISTS `webhemi_filesystem_folder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_filesystem_folder` (
  `id_filesystem_folder` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  -- Folder types:
  -- * document: the index.html should display a list of the edited (HTML) contents under the given uri. Other contents should not be displayed directly.
  -- * gallery: the index.html should provide gallery functionality for the images under the given uri. Other contents should not be displayed directly.
  -- * binary: the index.html should display a list of links for all the contents under the given uri.
  `folder_type` ENUM('document','gallery','binary') NOT NULL,
  -- If auto indexing is 0 the application should lead to 404 or 403 when requesting the given uri
  `is_autoindex` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_filesystem_folder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webhemi_filesystem_file`
--

DROP TABLE IF EXISTS `webhemi_filesystem_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_filesystem_file` (
  `id_filesystem_file` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_hash` VARCHAR(255) NOT NULL,
  -- Typically a temporary filename with absolute path
  `path` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(255) NOT NULL,
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_filesystem_file`),
  UNIQUE KEY `unq_filesystem_file_file_hash` (`file_hash`),
  UNIQUE KEY `unq_filesystem_file_path` (`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `webhemi_filesystem_content`
--

DROP TABLE IF EXISTS `webhemi_filesystem_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_filesystem_content` (
  `id_filesystem_content` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_parent_revision` INT(10) UNSIGNED DEFAULT NULL,
  `fk_author` INT(10) UNSIGNED DEFAULT NULL,
  `content_revision` INT(10) UNSIGNED NOT NULL DEFAULT 1,
  `content_body` TEXT NOT NULL,
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_filesystem_content`),
  KEY `idx_filesystem_content_content_revision` (`content_revision`),
  KEY `idx_filesystem_content_fk_parent_revision` (`fk_parent_revision`),
  KEY `id_filesystem_content_fk_author` (`fk_author`),
  CONSTRAINT `fk_filesystem_content_fk_parent_revision` FOREIGN KEY (`fk_parent_revision`) REFERENCES `webhemi_filesystem_content` (`id_filesystem_content`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_filesystem_content_fk_author` FOREIGN KEY (`fk_author`) REFERENCES `webhemi_user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webhemi_filesystem`
--

DROP TABLE IF EXISTS `webhemi_filesystem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_filesystem` (
  `id_filesystem` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_application` INT(10) UNSIGNED NOT NULL,
  `fk_parent_node` INT(10) UNSIGNED DEFAULT NULL,
  -- The application should handle to (compulsorily) set only one of these at a time
  `fk_filesystem_content` INT(10) UNSIGNED DEFAULT NULL,
  `fk_filesystem_file` INT(10) UNSIGNED DEFAULT NULL,
  `fk_filesystem_folder` INT(10) UNSIGNED DEFAULT NULL,
  `fk_filesystem_link` INT(10) UNSIGNED DEFAULT NULL,
  `uri` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  -- Image caption, article summary, file description
  `description` TEXT,
  -- If the record is marked as hidden the application should exclude it from data sets
  `is_hidden` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_filesystem`),
  UNIQUE KEY `unq_uri` (`fk_application`,`uri`, `name`),
  KEY `idx_filesystem_fk_application` (`fk_application`),
  KEY `idx_filesystem_fk_parent_node` (`fk_parent_node`),
  KEY `idx_filesystem_fk_filesystem_content` (`fk_filesystem_content`),
  KEY `idx_filesystem_fk_filesystem_file` (`fk_filesystem_file`),
  KEY `idx_filesystem_fk_filesystem_folder` (`fk_filesystem_folder`),
  KEY `idx_filesystem_fk_filesystem_link` (`fk_filesystem_link`),
  KEY `idx_filesystem_file_name` (`name`),
  KEY `idx_filesystem_uri` (`uri`),
  KEY `idx_filesystem_is_deleted` (`is_deleted`),
  CONSTRAINT `fk_filesystem_fk_application` FOREIGN KEY (`fk_application`) REFERENCES `webhemi_application` (`id_application`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_filesystem_fk_parent_node` FOREIGN KEY (`fk_parent_node`) REFERENCES `webhemi_filesystem` (`id_filesystem`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_filesystem_fk_filesystem_content` FOREIGN KEY (`fk_filesystem_content`) REFERENCES `webhemi_filesystem_content` (`id_filesystem_content`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_filesystem_fk_filesystem_file` FOREIGN KEY (`fk_filesystem_file`) REFERENCES `webhemi_filesystem_file` (`id_filesystem_file`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_filesystem_fk_filesystem_folder` FOREIGN KEY (`fk_filesystem_folder`) REFERENCES `webhemi_filesystem_folder` (`id_filesystem_folder`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_filesystem_fk_filesystem_link` FOREIGN KEY (`fk_filesystem_link`) REFERENCES `webhemi_filesystem` (`id_filesystem`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webhemi_filesystem_content_attachment`
--

DROP TABLE IF EXISTS `webhemi_filesystem_content_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_filesystem_content_attachment` (
  `id_filesystem_content_attachment` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_filesystem_content` INT(10) UNSIGNED NOT NULL,
  `fk_filesystem` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_filesystem_content_attachment`),
  UNIQUE KEY `unq_filesystem_content_attachment` (`fk_filesystem_content`, `fk_filesystem`),
  KEY `idx_filesystem_content_attachment_filesystem_content` (`fk_filesystem_content`),
  KEY `idx_filesystem_content_attachment_filesystem` (`fk_filesystem`),
  CONSTRAINT `fk_filesystem_content_attachment_fk_filesystem_content` FOREIGN KEY (`fk_filesystem_content`) REFERENCES `webhemi_filesystem_content` (`id_filesystem_content`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_filesystem_content_attachment_fk_filesystem` FOREIGN KEY (`fk_filesystem`) REFERENCES `webhemi_filesystem` (`id_filesystem`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

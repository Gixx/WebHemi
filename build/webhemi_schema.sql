/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE = @@TIME_ZONE */;
/*!40103 SET TIME_ZONE = '+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;

--
-- Create database `webhemi`
--
UNLOCK TABLES;
CREATE DATABASE IF NOT EXISTS `webhemi`;
USE `webhemi`;

--
-- Cleanup old stuff
--

DROP TABLE IF EXISTS `webhemi_lock`;
DROP TABLE IF EXISTS `webhemi_filesystem_content`;
DROP TABLE IF EXISTS `webhemi_filesystem_content_attachment`;
DROP TABLE IF EXISTS `webhemi_filesystem_folder`;
DROP TABLE IF EXISTS `webhemi_am_policy`;
DROP TABLE IF EXISTS `webhemi_am_resource`;
DROP TABLE IF EXISTS `webhemi_user_to_am_policy`;
DROP TABLE IF EXISTS `webhemi_user_group_to_am_policy`;

--
-- Table structure for table `webhemi_application`
--

DROP TABLE IF EXISTS `webhemi_application`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_application` (
    `id_application` INT(10) UNSIGNED             NOT NULL AUTO_INCREMENT,
    `name`           VARCHAR(30)                  NOT NULL,
    `title`          VARCHAR(255)                 NOT NULL,
    -- introduction can be a fixed content on the index page
    `introduction`   TEXT                                  DEFAULT NULL,
    `subject`        VARCHAR(255)                 NOT NULL DEFAULT '',
    `description`    VARCHAR(255)                 NOT NULL DEFAULT '',
    `keywords`       VARCHAR(255)                 NOT NULL DEFAULT '',
    `copyright`      VARCHAR(255)                 NOT NULL DEFAULT '',
    `domain`         VARCHAR(255)                 NOT NULL DEFAULT '',
    `path`           VARCHAR(20)                  NOT NULL DEFAULT '/',
    `theme`          VARCHAR(20)                  NOT NULL DEFAULT 'deafult',
    `type`           ENUM ('domain', 'directory') NOT NULL DEFAULT 'directory',
    `locale`         VARCHAR(20)                  NOT NULL DEFAULT 'en_GB.UTF-8',
    `timezone`       VARCHAR(100)                 NOT NULL DEFAULT 'Europe/London',
    `is_read_only`   TINYINT(1)                   NOT NULL DEFAULT 0,
    `is_enabled`     TINYINT(1)                   NOT NULL DEFAULT 0,
    `date_created`   DATETIME                     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modified`  DATETIME                              DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_application`),
    UNIQUE KEY `unq_application_name` (`name`),
    UNIQUE KEY `unq_application_title` (`title`),
    INDEX `indx_application_domain` (`domain`),
    INDEX `indx_application_path` (`path`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_application`
--

LOCK TABLES `webhemi_application` WRITE;
/*!40000 ALTER TABLE `webhemi_application`
    DISABLE KEYS */;
INSERT INTO `webhemi_application` VALUES
    (1, 'admin', 'Admin', '', '', '', '', '', '', '/admin', 'default', 'directory', 'en_GB.UTF-8', 'Europe/London', 1, 1, NOW(), NULL),
    (2, 'website', 'Website', '<h1>Welcome to the WebHemi!</h1><p>After many years of endless development of a big, robust, super-universal blog engine which was suppose to build on a well known framework, I decided to re-think my goals and the way I want to reach them. Now I try to create a small, fast and "only as much as necessary", clean-code blog engine  that tries to completely apply the S.O.L.I.D. principles, uses the PSR-7 HTTP Messages Interfaces and the Middleware concept.</p>', 'Technical stuff', 'The default application for the `www` subdomain.', 'php,html,javascript,css', 'Copyright © 2017. WebHemi', '', '/', 'default', 'domain', 'en_GB.UTF-8', 'Europe/London', 1, 1, NOW(), NULL);
/*!40000 ALTER TABLE `webhemi_application`
    ENABLE KEYS */;
UNLOCK TABLES;

-- AM - Access Management

--
-- Table structure for table `webhemi_resource`
--

DROP TABLE IF EXISTS `webhemi_resource`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_resource` (
    `id_resource` INT(10) UNSIGNED         NOT NULL AUTO_INCREMENT,
    `name`           VARCHAR(255)             NOT NULL,
    `title`          VARCHAR(255)             NOT NULL,
    `description`    TEXT                     NOT NULL,
    `type`           ENUM ('route', 'custom') NOT NULL,
    `is_read_only`   TINYINT(1)               NOT NULL DEFAULT 0,
    `date_created`   DATETIME                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modified`  DATETIME                          DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_resource`),
    UNIQUE KEY `unq_resource_name` (`name`),
    UNIQUE KEY `unq_resource_title` (`title`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_resource`
--

LOCK TABLES `webhemi_resource` WRITE;
/*!40000 ALTER TABLE `webhemi_resource`
    DISABLE KEYS */;
INSERT INTO `webhemi_resource` VALUES
    (1000001, 'admin-dashboard', 'Dashboard', '', 'route', 1, NOW(), NULL),

    (1100001, 'admin-applications-list',   'List Applications', '', 'route', 1, NOW(), NULL),
    (1100002, 'admin-applications-view',   'View an Application', '', 'route', 1, NOW(), NULL),
    (1100003, 'admin-applications-add',    'Add a new Application', '', 'route', 1, NOW(), NULL),
    (1100004, 'admin-applications-edit',   'Edit an Application', '', 'route', 1, NOW(), NULL),
    (1100005, 'admin-applications-delete', 'Delete an Application', '', 'route', 1, NOW(), NULL),

    (1200001, 'admin-control-panel-index', 'List Control Panel items', '', 'route', 1, NOW(), NULL),
        (1200101, 'admin-control-panel-settings-list',   'List Settings', '', 'route', 1, NOW(), NULL),
        (1200102, 'admin-control-panel-settings-view',   'View a Setting', '', 'route', 1, NOW(), NULL),
        (1200103, 'admin-control-panel-settings-add',    'Add a new Setting', '', 'route', 1, NOW(), NULL),
        (1200104, 'admin-control-panel-settings-edit',   'Edit a Setting', '', 'route', 1, NOW(), NULL),
        (1200105, 'admin-control-panel-settings-delete', 'Delete a Setting', '', 'route', 1, NOW(), NULL),

        (1200201, 'admin-control-panel-themes-list',   'List Themes', '', 'route', 1, NOW(), NULL),
        (1200202, 'admin-control-panel-themes-view',   'View a Theme', '', 'route', 1, NOW(), NULL),
        (1200203, 'admin-control-panel-themes-add',    'Add a new Theme', '', 'route', 1, NOW(), NULL),
        (1200204, 'admin-control-panel-themes-delete', 'Delete a Theme', '', 'route', 1, NOW(), NULL),

        (1200301, 'admin-control-panel-addons-list',   'List AddOns', '', 'route', 1, NOW(), NULL),
        (1200302, 'admin-control-panel-addons-view',   'View a AddOn', '', 'route', 1, NOW(), NULL),
        (1200303, 'admin-control-panel-addons-add',    'Add a new AddOn', '', 'route', 1, NOW(), NULL),
        (1200304, 'admin-control-panel-addons-edit',   'Edit a AddOn', '', 'route', 1, NOW(), NULL),
        (1200305, 'admin-control-panel-addons-delete', 'Delete a AddOn', '', 'route', 1, NOW(), NULL),

        (1200401, 'admin-control-panel-users-list',   'List Users', '', 'route', 1, NOW(), NULL),
        (1200402, 'admin-control-panel-users-view',   'View a User', '', 'route', 1, NOW(), NULL),
        (1200403, 'admin-control-panel-users-add',    'Add a new User', '', 'route', 1, NOW(), NULL),
        (1200404, 'admin-control-panel-users-edit',   'Edit a User', '', 'route', 1, NOW(), NULL),
        (1200405, 'admin-control-panel-users-delete', 'Delete a User', '', 'route', 1, NOW(), NULL),

        (1200501, 'admin-control-panel-groups-list',   'List Groups', '', 'route', 1, NOW(), NULL),
        (1200502, 'admin-control-panel-groups-view',   'View a Group', '', 'route', 1, NOW(), NULL),
        (1200503, 'admin-control-panel-groups-add',    'Add a new Group', '', 'route', 1, NOW(), NULL),
        (1200504, 'admin-control-panel-groups-edit',   'Edit a Group', '', 'route', 1, NOW(), NULL),
        (1200505, 'admin-control-panel-groups-delete', 'Delete a Group', '', 'route', 1, NOW(), NULL),

        (1200601, 'admin-control-panel-resources-list',   'List Resources', '', 'route', 1, NOW(), NULL),
        (1200602, 'admin-control-panel-resources-view',   'View a Resource', '', 'route', 1, NOW(), NULL),
        (1200603, 'admin-control-panel-resources-add',    'Add a new Resource', '', 'route', 1, NOW(), NULL),
        (1200604, 'admin-control-panel-resources-edit',   'Edit a Resource', '', 'route', 1, NOW(), NULL),
        (1200605, 'admin-control-panel-resources-delete', 'Delete a Resource', '', 'route', 1, NOW(), NULL),

        (1200701, 'admin-control-panel-policies-list',   'List Policies', '', 'route', 1, NOW(), NULL),
        (1200702, 'admin-control-panel-policies-view',   'View a Policy', '', 'route', 1, NOW(), NULL),
        (1200703, 'admin-control-panel-policies-add',    'Add a new Policy', '', 'route', 1, NOW(), NULL),
        (1200704, 'admin-control-panel-policies-edit',   'Edit a Policy', '', 'route', 1, NOW(), NULL),
        (1200705, 'admin-control-panel-policies-delete', 'Delete a Policy', '', 'route', 1, NOW(), NULL),

        (1200801, 'admin-control-panel-logs-list',   'List Logs', '', 'route', 1, NOW(), NULL),
        (1200802, 'admin-control-panel-logs-view',   'View a Log', '', 'route', 1, NOW(), NULL),

    (1300001, 'admin-about-index', 'View the About page', '', 'route', 1, NOW(), NULL);
/*!40000 ALTER TABLE `webhemi_resource`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_policy`
--

DROP TABLE IF EXISTS `webhemi_policy`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_policy` (
    `id_policy`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    -- If key is NULL, then the policy is applied to all resources
    `fk_resource` INT(10) UNSIGNED          DEFAULT NULL,
    -- If key is NULL, then the policy is applied to all applications
    `fk_application` INT(10) UNSIGNED          DEFAULT NULL,
    `name`           VARCHAR(255)     NOT NULL,
    `title`          VARCHAR(255)     NOT NULL,
    `description`    TEXT             NOT NULL DEFAULT '',
    `method`         ENUM ('GET', 'POST', 'DELETE', 'PUT') DEFAULT NULL,
    `is_read_only`   TINYINT(1)       NOT NULL DEFAULT 0,
    `date_created`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modified`  DATETIME                  DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_policy`),
    UNIQUE KEY `unq_policy` (`fk_resource`, `fk_application`, `method`),
    UNIQUE KEY `unq_policy_title` (`title`),
    KEY `idx_policy_fk_resource` (`fk_resource`),
    KEY `idx_policy_fk_application` (`fk_application`),
    CONSTRAINT `fkx_policy_fk_resource` FOREIGN KEY (`fk_resource`) REFERENCES `webhemi_resource` (`id_resource`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fkx_policy_fk_application` FOREIGN KEY (`fk_application`) REFERENCES `webhemi_application` (`id_application`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_policy`
--

LOCK TABLES `webhemi_policy` WRITE;
/*!40000 ALTER TABLE `webhemi_policy`
    DISABLE KEYS */;
INSERT INTO `webhemi_policy` VALUES
    (777, NULL, NULL, 'supervisor', 'Supervisor access', 'Allow access to all resources in every application with any request method.', NULL, 1, NOW(), NULL),

    (1000001, 1000001, 1, 'dashboard', 'Dashborad access', 'Allow to view the dashboard.', 'GET', 1, NOW(), NULL),

    (1100001, 1100001, 1, 'applications-list',   'Application access', 'Allow to list the applications.', NULL, 1, NOW(), NULL),
    (1100002, 1100002, 1, 'applications-add',    'Application creator', 'Allow to create new application. Doesn\'t include "save".', NULL, 1, NOW(), NULL),
    (1100003, 1100003, 1, 'applications-view',   'Application viewer', 'Allow to access application detail page.', NULL, 1, NOW(), NULL),
    (1100004, 1100004, 1, 'applications-edit',   'Application save changes', 'Allow to edit the application preferences. Doesn\'t include "save".', NULL, 1, NOW(), NULL),
    (1100005, 1100005, 1, 'applications-delete', 'Application delete', 'Allow to delete the application that is about to be deleted. Doesn\'t include "confirm".', 'GET', 1, NOW(), NULL),

    (1200001, 1200001, 1, 'control-panel-index', 'Control panel access', 'Allow to view the Control Panel page.', NULL, 1, NOW(), NULL),
    (1200101, 1200101, 1, 'control-panel-settings-list',   'List Settings', '', NULL, 1, NOW(), NULL),
    (1200102, 1200102, 1, 'control-panel-settings-view',   'View a Setting', '', NULL, 1, NOW(), NULL),
    (1200103, 1200103, 1, 'control-panel-settings-add',    'Add a new Setting', '', NULL, 1, NOW(), NULL),
    (1200104, 1200104, 1, 'control-panel-settings-edit',   'Edit a Setting', '', NULL, 1, NOW(), NULL),
    (1200105, 1200105, 1, 'control-panel-settings-delete', 'Delete a Setting', '', NULL, 1, NOW(), NULL),

    (1200201, 1200201, 1, 'control-panel-themes-list',   'Theme Manager access', 'Allow to list the installed themes.', NULL, 1, NOW(), NULL),
    (1200202, 1200202, 1, 'control-panel-themes-view',   'Theme viewer', 'Allow to view theme properties.', NULL, 1, NOW(), NULL),
    (1200203, 1200203, 1, 'control-panel-themes-add',    'Theme uploader', 'Allow to upload a new theme.', NULL, 1, NOW(), NULL),
    (1200204, 1200204, 1, 'control-panel-themes-delete', 'Theme remover ', 'Allow to delete theme permanently.', NULL, 1, NOW(), NULL),

    (1200301, 1200301, 1, 'control-panel-addons-list',   'List AddOns', '', NULL, 1, NOW(), NULL),
    (1200302, 1200302, 1, 'control-panel-addons-view',   'View a AddOn', '', NULL, 1, NOW(), NULL),
    (1200303, 1200303, 1, 'control-panel-addons-add',    'Add a new AddOn', '', NULL, 1, NOW(), NULL),
    (1200304, 1200304, 1, 'control-panel-addons-edit',   'Edit a AddOn', '', NULL, 1, NOW(), NULL),
    (1200305, 1200305, 1, 'control-panel-addons-delete', 'Delete a AddOn', '', NULL, 1, NOW(), NULL),

    (1200401, 1200401, 1, 'control-panel-users-list',   'List Users', '', NULL, 1, NOW(), NULL),
    (1200402, 1200402, 1, 'control-panel-users-view',   'View a User', '', NULL, 1, NOW(), NULL),
    (1200403, 1200403, 1, 'control-panel-users-add',    'Add a new User', '', NULL, 1, NOW(), NULL),
    (1200404, 1200404, 1, 'control-panel-users-edit',   'Edit a User', '', NULL, 1, NOW(), NULL),
    (1200405, 1200405, 1, 'control-panel-users-delete', 'Delete a User', '', NULL, 1, NOW(), NULL),

    (1200501, 1200501, 1, 'control-panel-groups-list',   'List Groups', '', NULL, 1, NOW(), NULL),
    (1200502, 1200502, 1, 'control-panel-groups-view',   'View a Group', '', NULL, 1, NOW(), NULL),
    (1200503, 1200503, 1, 'control-panel-groups-add',    'Add a new Group', '', NULL, 1, NOW(), NULL),
    (1200504, 1200504, 1, 'control-panel-groups-edit',   'Edit a Group', '', NULL, 1, NOW(), NULL),
    (1200505, 1200505, 1, 'control-panel-groups-delete', 'Delete a Group', '', NULL, 1, NOW(), NULL),

    (1200601, 1200601, 1, 'control-panel-resources-list',   'List Resources', '', NULL, 1, NOW(), NULL),
    (1200602, 1200602, 1, 'control-panel-resources-view',   'View a Resource', '', NULL, 1, NOW(), NULL),
    (1200603, 1200603, 1, 'control-panel-resources-add',    'Add a new Resource', '', NULL, 1, NOW(), NULL),
    (1200604, 1200604, 1, 'control-panel-resources-edit',   'Edit a Resource', '', NULL, 1, NOW(), NULL),
    (1200605, 1200605, 1, 'control-panel-resources-delete', 'Delete a Resource', '', NULL, 1, NOW(), NULL),

    (1200701, 1200701, 1, 'control-panel-policies-list',   'List Policies', '', NULL, 1, NOW(), NULL),
    (1200702, 1200702, 1, 'control-panel-policies-view',   'View a Policy', '', NULL, 1, NOW(), NULL),
    (1200703, 1200703, 1, 'control-panel-policies-add',    'Add a new Policy', '', NULL, 1, NOW(), NULL),
    (1200704, 1200704, 1, 'control-panel-policies-edit',   'Edit a Policy', '', NULL, 1, NOW(), NULL),
    (1200705, 1200705, 1, 'control-panel-policies-delete', 'Delete a Policy', '', NULL, 1, NOW(), NULL),

    (1200801, 1200801, 1, 'control-panel-logs-list',   'List Logs', '', NULL, 1, NOW(), NULL),
    (1200802, 1200802, 1, 'control-panel-logs-view',   'View a Log', '', NULL, 1, NOW(), NULL),

    (1300001, 1300001, 1, 'about-index', 'View the About page', '', NULL, 1, NOW(), NULL);
/*!40000 ALTER TABLE `webhemi_policy`
    ENABLE KEYS */;
UNLOCK TABLES;

-- IM - Identity Management

--
-- Table structure for table `webhemi_user`
--

DROP TABLE IF EXISTS `webhemi_user`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user` (
    `id_user`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(255)     NOT NULL,
    `email`         VARCHAR(255)              DEFAULT NULL,
    -- Hashed password. MD5 and SHA1 are not recommended.
    `password`      VARCHAR(60)      NOT NULL,
    -- Hash is used in emails and auto-login cookie to identify user without credentials. Once used a new one should be generated.
    `hash`          VARCHAR(32)               DEFAULT NULL,
    `is_active`     TINYINT(1)       NOT NULL DEFAULT '0',
    `is_enabled`    TINYINT(1)       NOT NULL DEFAULT '0',
    `date_created`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modified` DATETIME                  DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_user`),
    UNIQUE KEY `unq_user_username` (`username`),
    UNIQUE KEY `unq_user_email` (`email`),
    UNIQUE KEY `unq_user_hash` (`hash`),
    KEY `idx_user_password` (`password`),
    KEY `idx_user_is_active` (`is_active`),
    KEY `idx_user_is_enabled` (`is_enabled`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_user`
--

LOCK TABLES `webhemi_user` WRITE;
/*!40000 ALTER TABLE `webhemi_user`
    DISABLE KEYS */;
INSERT INTO `webhemi_user` VALUES
    (1, 'admin', 'admin@foo.org', '$2y$09$dmrDfcYZt9jORA4vx9MKpeyRt0ilCH/gxSbSHcfBtGaghMJ30tKzS', 'hash-admin', 1, 1, NOW(), NULL);
/*!40000 ALTER TABLE `webhemi_user`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_user_meta`
--

DROP TABLE IF EXISTS `webhemi_user_meta`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user_meta` (
    `id_user_meta`  INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `fk_user`       INT(10) UNSIGNED NOT NULL,
    `meta_key`      VARCHAR(255)     NOT NULL,
    `meta_data`     LONGTEXT         NOT NULL,
    `date_created`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modified` DATETIME                  DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_user_meta`),
    UNIQUE KEY `unq_user_meta` (`fk_user`, `meta_key`),
    CONSTRAINT `fkx_user_meta_fk_user` FOREIGN KEY (`fk_user`) REFERENCES `webhemi_user` (`id_user`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_user_meta`
--

LOCK TABLES `webhemi_user_meta` WRITE;
/*!40000 ALTER TABLE `webhemi_user_meta`
    DISABLE KEYS */;
INSERT INTO `webhemi_user_meta` VALUES
    (NULL, 1, 'display_name', 'Admin Joe', NOW(), NULL),
    (NULL, 1, 'gender', 'male', NOW(), NULL),
    (NULL, 1, 'avatar', '/img/avatars/suit_man.svg', NOW(), NULL),
    (NULL, 1, 'avatar_type', 'file', NOW(), NULL),
    (NULL, 1, 'email_visible', '0', NOW(), NULL),
    (NULL, 1, 'location', '', NOW(), NULL),
    (NULL, 1, 'instant_messengers', '', NOW(), NULL),
    (NULL, 1, 'phone_numbers', '', NOW(), NULL),
    (NULL, 1, 'social_networks', '', NOW(), NULL),
    (NULL, 1, 'websites', '', NOW(), NULL),
    (NULL, 1, 'introduction', '', NOW(), NULL);
/*!40000 ALTER TABLE `webhemi_user_meta`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_user_policy`
--

DROP TABLE IF EXISTS `webhemi_user_to_policy`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user_to_policy` (
    `id_user_to_policy` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `fk_user`              INT(10) UNSIGNED NOT NULL,
    `fk_policy`         INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_user_to_policy`),
    UNIQUE KEY `unq_user_to_policy` (`fk_user`, `fk_policy`),
    KEY `idx_user_to_policy_fk_user` (`fk_user`),
    KEY `idx_user_to_policy_fk_policy` (`fk_policy`),
    CONSTRAINT `fkx_user_to_policy_fk_user` FOREIGN KEY (`fk_user`) REFERENCES `webhemi_user` (`id_user`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fkx_user_to_policy_fk_policy` FOREIGN KEY (`fk_policy`) REFERENCES `webhemi_policy` (`id_policy`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Users can be assigned to policies dierectly --
--
-- Dumping data for table `webhemi_user_policy`
--

LOCK TABLES `webhemi_user_to_policy` WRITE;
/*!40000 ALTER TABLE `webhemi_user_to_policy`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `webhemi_user_to_policy`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_user_group`
--

DROP TABLE IF EXISTS `webhemi_user_group`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user_group` (
    `id_user_group` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(255)     NOT NULL,
    `title`         VARCHAR(30)      NOT NULL,
    `description`   TEXT             NOT NULL DEFAULT '',
    `is_read_only`  TINYINT(1)       NOT NULL DEFAULT 0,
    `date_created`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modified` DATETIME                  DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_user_group`),
    UNIQUE KEY `unq_user_group_title` (`title`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_user_group`
--

LOCK TABLES `webhemi_user_group` WRITE;
/*!40000 ALTER TABLE `webhemi_user_group`
    DISABLE KEYS */;
INSERT INTO `webhemi_user_group` VALUES
    (1, 'admin', 'Administrators', 'Group for global administrators', 1, NOW(), NULL);
/*!40000 ALTER TABLE `webhemi_user_group`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_user_to_user_group`
--

DROP TABLE IF EXISTS `webhemi_user_to_user_group`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user_to_user_group` (
    `id_user_to_user_group` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `fk_user`               INT(10) UNSIGNED NOT NULL,
    `fk_user_group`         INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_user_to_user_group`),
    UNIQUE KEY `unq_user_to_user_group` (`fk_user`, `fk_user_group`),
    KEY `idx_user_to_user_group_fk_user` (`fk_user`),
    KEY `idx_user_to_user_group_fk_user_group` (`fk_user_group`),
    CONSTRAINT `fkx_user_to_user_group_fk_user` FOREIGN KEY (`fk_user`) REFERENCES `webhemi_user` (`id_user`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fkx_user_to_user_group_fk_user_group` FOREIGN KEY (`fk_user_group`) REFERENCES `webhemi_user_group` (`id_user_group`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_user_to_user_group`
--

LOCK TABLES `webhemi_user_to_user_group` WRITE;
/*!40000 ALTER TABLE `webhemi_user_to_user_group`
    DISABLE KEYS */;
INSERT INTO `webhemi_user_to_user_group` VALUES
    (NULL, 1, 1);
/*!40000 ALTER TABLE `webhemi_user_to_user_group`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_user_group_to_policy`
--

DROP TABLE IF EXISTS `webhemi_user_group_to_policy`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_user_group_to_policy` (
    `id_user_group_to_policy` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `fk_user_group`              INT(10) UNSIGNED NOT NULL,
    `fk_policy`               INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_user_group_to_policy`),
    UNIQUE KEY `unq_user_group_to_policy` (`fk_user_group`, `fk_policy`),
    KEY `idx_user_group_to_policy_fk_user_group` (`fk_user_group`),
    KEY `idx_user_group_to_policy_fk_policy` (`fk_policy`),
    CONSTRAINT `fkx_user_group_to_policy_fk_user_group` FOREIGN KEY (`fk_user_group`) REFERENCES `webhemi_user_group` (`id_user_group`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fkx_user_group_to_policy_fk_policy` FOREIGN KEY (`fk_policy`) REFERENCES `webhemi_policy` (`id_policy`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_user_group_to_policy`
--

LOCK TABLES `webhemi_user_group_to_policy` WRITE;
/*!40000 ALTER TABLE `webhemi_user_group_to_policy`
    DISABLE KEYS */;
INSERT INTO `webhemi_user_group_to_policy` VALUES
    (NULL, 1, 777);
/*!40000 ALTER TABLE `webhemi_user_group_to_policy`
    ENABLE KEYS */;
UNLOCK TABLES;

-- FS - Filesystem

--
-- Table structure for table `webhemi_filesystem_directory`
--

DROP TABLE IF EXISTS `webhemi_filesystem_directory`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_filesystem_directory` (
    `id_filesystem_directory` INT(10) UNSIGNED                       NOT NULL AUTO_INCREMENT,
    `description`             VARCHAR(255)                                    DEFAULT '',
    -- Directory types:
    -- * document: the index.html should display a list of the edited (HTML) contents under the given uri. Other contents should not be displayed directly.
    -- * gallery: the index.html should provide gallery functionality for the images under the given uri. Other contents should not be displayed directly.
    -- * binary: the index.html should display a list of links for all the contents under the given uri.
    `directory_type`          ENUM ('document', 'gallery', 'binary') NOT NULL,
    `proxy`                   ENUM ('list-category', 'list-tag', 'list-archive', 'list-gallery', 'list-binary', 'list-user') DEFAULT NULL,
    -- If auto indexing is 0 the application should lead to 404 or 403 when requesting the given uri
    `is_autoindex`            TINYINT(1) UNSIGNED                    NOT NULL DEFAULT 1,
    `date_created`            DATETIME                               NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modified`           DATETIME                                        DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_filesystem_directory`),
    UNIQUE KEY `unq_filesystem_directory_proxy` (`proxy`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_filesystem_directory`
--

LOCK TABLES `webhemi_filesystem_directory` WRITE;
/*!40000 ALTER TABLE `webhemi_filesystem_directory`
    DISABLE KEYS */;
INSERT INTO `webhemi_filesystem_directory` VALUES
    (1, 'Categories post collection', 'document', 'list-category', 1, NOW(), NOW()),
    (2, 'Tags post collection', 'document', 'list-tag', 1, NOW(), NOW()),
    (3, 'Archive post collection', 'document', 'list-archive', 1, NOW(), NOW()),
    (4, 'All uploaded images collection', 'gallery', 'list-gallery', 1, NOW(), NOW()),
    (5, 'All uploaded files collection', 'binary', 'list-binary', 1, NOW(), NOW()),
    (6, 'User page and post collection', 'document', 'list-user', 1, NOW(), NOW());
/*!40000 ALTER TABLE `webhemi_filesystem_directory`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhemi_filesystem_file`
--

DROP TABLE IF EXISTS `webhemi_filesystem_file`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_filesystem_file` (
    `id_filesystem_file` INT(10) UNSIGNED                           NOT NULL AUTO_INCREMENT,
    `file_hash`          VARCHAR(255)                               NOT NULL,
    -- Typically a temporary filename with absolute path
    `path`               VARCHAR(255)                               NOT NULL,
    `file_type`          ENUM ('image', 'video', 'audio', 'binary') NOT NULL,
    `mime_type`          VARCHAR(255)                               NOT NULL,
    `date_created`       DATETIME                                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modified`      DATETIME                                            DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_filesystem_file`),
    UNIQUE KEY `unq_filesystem_file_file_hash` (`file_hash`),
    UNIQUE KEY `unq_filesystem_file_path` (`path`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webhemi_filesystem_document`
--

DROP TABLE IF EXISTS `webhemi_filesystem_document`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_filesystem_document` (
    `id_filesystem_document` INT(10) UNSIGNED      NOT NULL AUTO_INCREMENT,
    `fk_parent_revision`     INT(10) UNSIGNED               DEFAULT NULL,
    `fk_author`              INT(10) UNSIGNED               DEFAULT NULL,
    `content_revision`       INT(10) UNSIGNED      NOT NULL DEFAULT 1,
    `content_lead`           TEXT                  NOT NULL,
    `content_body`           TEXT                  NOT NULL,
    `date_created`           DATETIME              NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modified`          DATETIME                       DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_filesystem_document`),
    KEY `idx_filesystem_document_content_revision` (`content_revision`),
    KEY `idx_filesystem_document_fk_parent_revision` (`fk_parent_revision`),
    KEY `id_filesystem_document_fk_author` (`fk_author`),
    CONSTRAINT `fkx_filesystem_document_fk_parent_revision` FOREIGN KEY (`fk_parent_revision`) REFERENCES `webhemi_filesystem_document` (`id_filesystem_document`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fkx_filesystem_document_fk_author` FOREIGN KEY (`fk_author`) REFERENCES `webhemi_user` (`id_user`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webhemi_filesystem_category`
--

DROP TABLE IF EXISTS `webhemi_filesystem_category`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_filesystem_category` (
    `id_filesystem_category` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `fk_application`         INT(10) UNSIGNED NOT NULL,
    `name`                   VARCHAR(255)     NOT NULL,
    `title`                  VARCHAR(30)      NOT NULL,
    `description`            TEXT             NOT NULL DEFAULT '',
    `item_order`             ENUM('ASC','DESC') NOT NULL DEFAULT 'DESC',
    `date_created`           DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modified`          DATETIME                  DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_filesystem_category`),
    UNIQUE KEY `unq_filesystem_file_file_hash` (`fk_application`, `name`),
    KEY `idx_filesystem_category_fk_application` (`fk_application`),
    CONSTRAINT `fkx_filesystem_category_fk_application` FOREIGN KEY (`fk_application`) REFERENCES `webhemi_application` (`id_application`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webhemi_filesystem_tag`
--

DROP TABLE IF EXISTS `webhemi_filesystem_tag`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_filesystem_tag` (
    `id_filesystem_tag` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `fk_application`    INT(10) UNSIGNED NOT NULL,
    `name`              VARCHAR(255)     NOT NULL,
    `title`             VARCHAR(30)      NOT NULL,
    `description`       TEXT             NOT NULL DEFAULT '',
    `date_created`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modified`     DATETIME                  DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_filesystem_tag`),
    UNIQUE KEY `unq_filesystem_file_file_hash` (`fk_application`, `name`),
    KEY `idx_filesystem_tag_fk_application` (`fk_application`),
    CONSTRAINT `fkx_filesystem_tag_fk_application` FOREIGN KEY (`fk_application`) REFERENCES `webhemi_application` (`id_application`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webhemi_filesystem`
--

DROP TABLE IF EXISTS `webhemi_filesystem`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_filesystem` (
    `id_filesystem`           INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
    `fk_application`          INT(10) UNSIGNED    NOT NULL,
    `fk_category`             INT(10) UNSIGNED             DEFAULT NULL,
    `fk_parent_node`          INT(10) UNSIGNED             DEFAULT NULL,
    -- The application should handle to (compulsorily) set only one of these at a time
    `fk_filesystem_document`  INT(10) UNSIGNED             DEFAULT NULL,
    `fk_filesystem_file`      INT(10) UNSIGNED             DEFAULT NULL,
    `fk_filesystem_directory` INT(10) UNSIGNED             DEFAULT NULL,
    `fk_filesystem_link`      INT(10) UNSIGNED             DEFAULT NULL,
    `path`                    VARCHAR(255)        NOT NULL DEFAULT '/',
    `basename`                VARCHAR(255)        NOT NULL,
    `title`                   VARCHAR(255)        NOT NULL,
    -- Image caption, article summary, file description
    `description`             TEXT,
    -- If the record is marked as hidden the application should exclude it from data sets
    `is_hidden`               TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    -- It marks that the record cannot be deleted (or marked as deleted) or change visibility, but can be renamed according to the application's language
    `is_read_only`            TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    `is_deleted`              TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    `date_created`            DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modified`           DATETIME                     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `date_published`          DATETIME                     DEFAULT NULL,
    `date_published_archive`  DATE                         DEFAULT NULL,
    PRIMARY KEY (`id_filesystem`),
    UNIQUE KEY `unq_uri` (`fk_application`, `path`, `basename`),
    KEY `idx_filesystem_fk_application` (`fk_application`),
    KEY `idx_filesystem_fk_category` (`fk_category`),
    KEY `idx_filesystem_fk_parent_node` (`fk_parent_node`),
    KEY `idx_filesystem_fk_filesystem_document` (`fk_filesystem_document`),
    KEY `idx_filesystem_fk_filesystem_file` (`fk_filesystem_file`),
    KEY `idx_filesystem_fk_filesystem_directory` (`fk_filesystem_directory`),
    KEY `idx_filesystem_fk_filesystem_link` (`fk_filesystem_link`),
    KEY `idx_filesystem_path` (`path`),
    KEY `idx_filesystem_file_basename` (`basename`),
    KEY `idx_filesystem_is_hidden` (`is_hidden`),
    KEY `idx_filesystem_is_read_only` (`is_read_only`),
    KEY `idx_filesystem_is_deleted` (`is_deleted`),
    KEY `idx_filesystem_date_published_archive` (`date_published_archive`),
    CONSTRAINT `fkx_filesystem_fk_application` FOREIGN KEY (`fk_application`) REFERENCES `webhemi_application` (`id_application`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fkx_filesystem_fk_category` FOREIGN KEY (`fk_category`) REFERENCES `webhemi_filesystem_category` (`id_filesystem_category`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT `fkx_filesystem_fk_parent_node` FOREIGN KEY (`fk_parent_node`) REFERENCES `webhemi_filesystem` (`id_filesystem`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT `fkx_filesystem_fk_filesystem_document` FOREIGN KEY (`fk_filesystem_document`) REFERENCES `webhemi_filesystem_document` (`id_filesystem_document`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT `fkx_filesystem_fk_filesystem_file` FOREIGN KEY (`fk_filesystem_file`) REFERENCES `webhemi_filesystem_file` (`id_filesystem_file`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT `fkx_filesystem_fk_filesystem_directory` FOREIGN KEY (`fk_filesystem_directory`) REFERENCES `webhemi_filesystem_directory` (`id_filesystem_directory`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT `fkx_filesystem_fk_filesystem_link` FOREIGN KEY (`fk_filesystem_link`) REFERENCES `webhemi_filesystem` (`id_filesystem`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhemi_filesystem`
--

LOCK TABLES `webhemi_filesystem` WRITE;
/*!40000 ALTER TABLE `webhemi_filesystem`
    DISABLE KEYS */;
INSERT INTO `webhemi_filesystem` VALUES
    (1, 2, NULL, NULL, NULL, NULL, 1, NULL, '/', 'category', 'Categories', '', 1, 1, 0, NOW(), NULL, NOW(), DATE_FORMAT(NOW(), '%Y-%m-01')),
    (2, 2, NULL, NULL, NULL, NULL, 2, NULL, '/', 'tag', 'Tags', '', 1, 1, 0, NOW(), NULL, NOW(), DATE_FORMAT(NOW(), '%Y-%m-01')),
    (3, 2, NULL, NULL, NULL, NULL, 3, NULL, '/', 'archive', 'Archive', '', 1, 1, 0, NOW(), NULL, NOW(), DATE_FORMAT(NOW(), '%Y-%m-01')),
    (4, 2, NULL, NULL, NULL, NULL, 4, NULL, '/', 'media', 'Uploaded images', '', 1, 1, 0, NOW(), NULL, NOW(), DATE_FORMAT(NOW(), '%Y-%m-01')),
    (5, 2, NULL, NULL, NULL, NULL, 5, NULL, '/', 'uploads', 'Uploaded files', '', 1, 1, 0, NOW(), NULL, NOW(), DATE_FORMAT(NOW(), '%Y-%m-01')),
    (6, 2, NULL, NULL, NULL, NULL, 6, NULL, '/', 'user', 'User', '', 1, 1, 0, NOW(), NULL, NOW(), DATE_FORMAT(NOW(), '%Y-%m-01'));
/*!40000 ALTER TABLE `webhemi_filesystem`
    ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `webhemi_filesystem_meta`
--

DROP TABLE IF EXISTS `webhemi_filesystem_meta`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_filesystem_meta` (
    `id_filesystem_meta`  INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `fk_filesystem`       INT(10) UNSIGNED NOT NULL,
    `meta_key`            VARCHAR(255)     NOT NULL,
    `meta_data`           LONGTEXT         NOT NULL,
    `date_created`        DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modified`       DATETIME                  DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_filesystem_meta`),
    UNIQUE KEY `unq_filesystem_meta` (`fk_filesystem`, `meta_key`),
    CONSTRAINT `fkx_filesystem_meta_fk_filesystem` FOREIGN KEY (`fk_filesystem`) REFERENCES `webhemi_filesystem` (`id_filesystem`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webhemi_filesystem_to_tag`
--

DROP TABLE IF EXISTS `webhemi_filesystem_to_filesystem_tag`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_filesystem_to_filesystem_tag` (
    `id_filesystem_to_filesystem_tag` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `fk_filesystem`        INT(10) UNSIGNED NOT NULL,
    `fk_filesystem_tag`    INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_filesystem_to_filesystem_tag`),
    UNIQUE KEY `unq_filesystem_to_filesystem_tag` (`fk_filesystem_tag`, `fk_filesystem`),
    KEY `idx_filesystem_to_filesystem_tag_filesystem_tag` (`fk_filesystem_tag`),
    KEY `idx_filesystem_to_filesystem_tag_filesystem` (`fk_filesystem`),
    CONSTRAINT `fkx_filesystem_to_filesystem_tag_fk_filesystem_tag` FOREIGN KEY (`fk_filesystem_tag`) REFERENCES `webhemi_filesystem_tag` (`id_filesystem_tag`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fkx_filesystem_to_filesystem_tag_fk_filesystem` FOREIGN KEY (`fk_filesystem`) REFERENCES `webhemi_filesystem` (`id_filesystem`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

--
-- Table structure for table `webhemi_filesystem_document_attachment`
--

DROP TABLE IF EXISTS `webhemi_filesystem_document_attachment`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhemi_filesystem_document_attachment` (
    `id_filesystem_document_attachment` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `fk_filesystem_document`            INT(10) UNSIGNED NOT NULL,
    `fk_filesystem`                     INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_filesystem_document_attachment`),
    UNIQUE KEY `unq_filesystem_document_attachment` (`fk_filesystem_document`, `fk_filesystem`),
    KEY `idx_filesystem_document_attachment_filesystem_document` (`fk_filesystem_document`),
    KEY `idx_filesystem_document_attachment_filesystem` (`fk_filesystem`),
    CONSTRAINT `fkx_filesystem_document_attachment_fk_filesystem_document` FOREIGN KEY (`fk_filesystem_document`) REFERENCES `webhemi_filesystem_document` (`id_filesystem_document`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fkx_filesystem_document_attachment_fk_filesystem` FOREIGN KEY (`fk_filesystem`) REFERENCES `webhemi_filesystem` (`id_filesystem`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE = @OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;


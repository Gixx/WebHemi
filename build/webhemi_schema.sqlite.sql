PRAGMA synchronous = OFF;
PRAGMA journal_mode = MEMORY;
BEGIN TRANSACTION;

DROP TABLE IF EXISTS "webhemi_application";
CREATE TABLE "webhemi_application" (
                                     "id_application" int(10)  NOT NULL ,
                                     "fk_domain" int(10)  NOT NULL,
                                     "path" varchar(255) NOT NULL DEFAULT '/',
                                     "name" varchar(30) NOT NULL,
                                     "title" varchar(255) NOT NULL,
                                     "theme" varchar(255) NOT NULL DEFAULT 'default',
                                     "locale" varchar(20) NOT NULL DEFAULT 'en_GB.UTF-8',
                                     "timezone" varchar(100) NOT NULL DEFAULT 'Europe/London',
                                     "introduction" text,
                                     "subject" text,
                                     "description" text,
                                     "keywords" varchar(255) NOT NULL DEFAULT '',
                                     "copyright" varchar(255) NOT NULL DEFAULT '',
                                     "is_read_only" tinyint(1) NOT NULL DEFAULT '0',
                                     "is_enabled" tinyint(1) NOT NULL DEFAULT '0',
                                     "date_created" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                     "date_modified" datetime ,
                                     PRIMARY KEY ("id_application")
                                     CONSTRAINT "fk_application_fk_domain" FOREIGN KEY ("fk_domain") REFERENCES "webhemi_domain" ("id_domain")
);
INSERT INTO "webhemi_application" VALUES (1,1,'/admin','admin','Admin','default','en_GB.UTF-8','Europe/London','','','','','',1,1,'2019-01-24 13:39:14',NULL),(2,1,'/','website','Website','default','en_GB.UTF-8','Europe/London','<h1>Welcome to the WebHemi!</h1>','Technical stuff','The default application for the `www` subdomain.','php,html,javascript,css','Copyright � 2019. WebHemi',1,1,'2019-01-24 13:39:14',NULL);

DROP TABLE IF EXISTS "webhemi_domain";
CREATE TABLE "webhemi_domain" (
                                "id_domain" int(10)  NOT NULL ,
                                "schema" varchar(30) NOT NULL DEFAULT 'http://',
                                "domain" varchar(30) NOT NULL,
                                "title" varchar(255) NOT NULL,
                                "is_default" tinyint(1) NOT NULL DEFAULT '0',
                                "is_read_only" tinyint(1) NOT NULL DEFAULT '0',
                                "date_created" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                "date_modified" datetime ,
                                PRIMARY KEY ("id_domain")
);
INSERT INTO "webhemi_domain" VALUES (1,'http://','localhost','WebHemi',1,1,'2019-01-24 13:39:14',NULL);

DROP TABLE IF EXISTS "webhemi_filesystem";
CREATE TABLE "webhemi_filesystem" (
                                    "id_filesystem" int(10)  NOT NULL ,
                                    "fk_application" int(10)  NOT NULL,
                                    "fk_category" int(10)  DEFAULT NULL,
                                    "fk_parent_node" int(10)  DEFAULT NULL,
                                    "fk_filesystem_document" int(10)  DEFAULT NULL,
                                    "fk_filesystem_file" int(10)  DEFAULT NULL,
                                    "fk_filesystem_directory" int(10)  DEFAULT NULL,
                                    "fk_filesystem_link" int(10)  DEFAULT NULL,
                                    "path" varchar(255) NOT NULL DEFAULT '/',
                                    "basename" varchar(255) NOT NULL,
                                    "title" varchar(255) NOT NULL,
                                    "description" text,
                                    "is_hidden" tinyint(1)  NOT NULL DEFAULT '0',
                                    "is_read_only" tinyint(1)  NOT NULL DEFAULT '0',
                                    "is_deleted" tinyint(1)  NOT NULL DEFAULT '0',
                                    "date_created" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    "date_modified" datetime ,
                                    "date_published" datetime DEFAULT NULL,
                                    "date_published_archive" date DEFAULT NULL,
                                    PRIMARY KEY ("id_filesystem")
                                      CONSTRAINT "fkx_filesystem_fk_application" FOREIGN KEY ("fk_application") REFERENCES "webhemi_application" ("id_application") ON DELETE CASCADE ON UPDATE CASCADE,
                                    CONSTRAINT "fkx_filesystem_fk_category" FOREIGN KEY ("fk_category") REFERENCES "webhemi_filesystem_category" ("id_filesystem_category") ON DELETE SET NULL ON UPDATE CASCADE,
                                    CONSTRAINT "fkx_filesystem_fk_filesystem_directory" FOREIGN KEY ("fk_filesystem_directory") REFERENCES "webhemi_filesystem_directory" ("id_filesystem_directory") ON UPDATE CASCADE,
                                    CONSTRAINT "fkx_filesystem_fk_filesystem_document" FOREIGN KEY ("fk_filesystem_document") REFERENCES "webhemi_filesystem_document" ("id_filesystem_document") ON UPDATE CASCADE,
                                    CONSTRAINT "fkx_filesystem_fk_filesystem_file" FOREIGN KEY ("fk_filesystem_file") REFERENCES "webhemi_filesystem_file" ("id_filesystem_file") ON UPDATE CASCADE,
                                    CONSTRAINT "fkx_filesystem_fk_filesystem_link" FOREIGN KEY ("fk_filesystem_link") REFERENCES "webhemi_filesystem" ("id_filesystem") ON UPDATE CASCADE,
                                    CONSTRAINT "fkx_filesystem_fk_parent_node" FOREIGN KEY ("fk_parent_node") REFERENCES "webhemi_filesystem" ("id_filesystem") ON UPDATE CASCADE
);
INSERT INTO "webhemi_filesystem" VALUES (1,2,NULL,NULL,NULL,NULL,1,NULL,'/','category','Categories','',1,1,0,'2019-01-24 13:39:16',NULL,'2019-01-24 13:39:16','2019-01-01'),(2,2,NULL,NULL,NULL,NULL,2,NULL,'/','tag','Tags','',1,1,0,'2019-01-24 13:39:16',NULL,'2019-01-24 13:39:16','2019-01-01'),(3,2,NULL,NULL,NULL,NULL,3,NULL,'/','archive','Archive','',1,1,0,'2019-01-24 13:39:16',NULL,'2019-01-24 13:39:16','2019-01-01'),(4,2,NULL,NULL,NULL,NULL,4,NULL,'/','media','Uploaded images','',1,1,0,'2019-01-24 13:39:16',NULL,'2019-01-24 13:39:16','2019-01-01'),(5,2,NULL,NULL,NULL,NULL,5,NULL,'/','uploads','Uploaded files','',1,1,0,'2019-01-24 13:39:16',NULL,'2019-01-24 13:39:16','2019-01-01'),(6,2,NULL,NULL,NULL,NULL,6,NULL,'/','user','User','',1,1,0,'2019-01-24 13:39:16',NULL,'2019-01-24 13:39:16','2019-01-01');

DROP TABLE IF EXISTS "webhemi_filesystem_category";
CREATE TABLE "webhemi_filesystem_category" (
                                             "id_filesystem_category" int(10)  NOT NULL ,
                                             "fk_application" int(10)  NOT NULL,
                                             "name" varchar(255) NOT NULL,
                                             "title" varchar(30) NOT NULL,
                                             "description" text NOT NULL,
                                             "item_order" text  NOT NULL DEFAULT 'DESC',
                                             "date_created" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                             "date_modified" datetime ,
                                             PRIMARY KEY ("id_filesystem_category")
                                               CONSTRAINT "fkx_filesystem_category_fk_application" FOREIGN KEY ("fk_application") REFERENCES "webhemi_application" ("id_application") ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE IF EXISTS "webhemi_filesystem_directory";
CREATE TABLE "webhemi_filesystem_directory" (
                                              "id_filesystem_directory" int(10)  NOT NULL ,
                                              "description" varchar(255) DEFAULT '',
                                              "directory_type" text  NOT NULL,
                                              "proxy" text  DEFAULT NULL,
                                              "is_autoindex" tinyint(1)  NOT NULL DEFAULT '1',
                                              "date_created" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                              "date_modified" datetime ,
                                              PRIMARY KEY ("id_filesystem_directory")
);
INSERT INTO "webhemi_filesystem_directory" VALUES (1,'Categories post collection','document','list-category',1,'2019-01-24 13:39:16','2019-01-24 13:39:16'),(2,'Tags post collection','document','list-tag',1,'2019-01-24 13:39:16','2019-01-24 13:39:16'),(3,'Archive post collection','document','list-archive',1,'2019-01-24 13:39:16','2019-01-24 13:39:16'),(4,'All uploaded images collection','gallery','list-gallery',1,'2019-01-24 13:39:16','2019-01-24 13:39:16'),(5,'All uploaded files collection','binary','list-binary',1,'2019-01-24 13:39:16','2019-01-24 13:39:16'),(6,'User page and post collection','document','list-user',1,'2019-01-24 13:39:16','2019-01-24 13:39:16');

DROP TABLE IF EXISTS "webhemi_filesystem_document";
CREATE TABLE "webhemi_filesystem_document" (
                                             "id_filesystem_document" int(10)  NOT NULL ,
                                             "fk_parent_revision" int(10)  DEFAULT NULL,
                                             "fk_author" int(10)  DEFAULT NULL,
                                             "content_revision" int(10)  NOT NULL DEFAULT '1',
                                             "content_lead" text NOT NULL,
                                             "content_body" text NOT NULL,
                                             "date_created" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                             "date_modified" datetime ,
                                             PRIMARY KEY ("id_filesystem_document")
                                               CONSTRAINT "fkx_filesystem_document_fk_author" FOREIGN KEY ("fk_author") REFERENCES "webhemi_user" ("id_user") ON DELETE SET NULL ON UPDATE CASCADE,
                                             CONSTRAINT "fkx_filesystem_document_fk_parent_revision" FOREIGN KEY ("fk_parent_revision") REFERENCES "webhemi_filesystem_document" ("id_filesystem_document") ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE IF EXISTS "webhemi_filesystem_document_attachment";
CREATE TABLE "webhemi_filesystem_document_attachment" (
                                                        "id_filesystem_document_attachment" int(10)  NOT NULL ,
                                                        "fk_filesystem_document" int(10)  NOT NULL,
                                                        "fk_filesystem" int(10)  NOT NULL,
                                                        PRIMARY KEY ("id_filesystem_document_attachment")
                                                          CONSTRAINT "fkx_filesystem_document_attachment_fk_filesystem" FOREIGN KEY ("fk_filesystem") REFERENCES "webhemi_filesystem" ("id_filesystem") ON DELETE CASCADE ON UPDATE CASCADE,
                                                        CONSTRAINT "fkx_filesystem_document_attachment_fk_filesystem_document" FOREIGN KEY ("fk_filesystem_document") REFERENCES "webhemi_filesystem_document" ("id_filesystem_document") ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE IF EXISTS "webhemi_filesystem_file";
CREATE TABLE "webhemi_filesystem_file" (
                                         "id_filesystem_file" int(10)  NOT NULL ,
                                         "file_hash" varchar(255) NOT NULL,
                                         "path" varchar(255) NOT NULL,
                                         "file_type" text  NOT NULL,
                                         "mime_type" varchar(255) NOT NULL,
                                         "date_created" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                         "date_modified" datetime ,
                                         PRIMARY KEY ("id_filesystem_file")
);

DROP TABLE IF EXISTS "webhemi_filesystem_meta";
CREATE TABLE "webhemi_filesystem_meta" (
                                         "id_filesystem_meta" int(10)  NOT NULL ,
                                         "fk_filesystem" int(10)  NOT NULL,
                                         "meta_key" varchar(255) NOT NULL,
                                         "meta_data" longtext NOT NULL,
                                         "date_created" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                         "date_modified" datetime ,
                                         PRIMARY KEY ("id_filesystem_meta")
                                           CONSTRAINT "fkx_filesystem_meta_fk_filesystem" FOREIGN KEY ("fk_filesystem") REFERENCES "webhemi_filesystem" ("id_filesystem") ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE IF EXISTS "webhemi_filesystem_tag";
CREATE TABLE "webhemi_filesystem_tag" (
                                        "id_filesystem_tag" int(10)  NOT NULL ,
                                        "fk_application" int(10)  NOT NULL,
                                        "name" varchar(255) NOT NULL,
                                        "title" varchar(30) NOT NULL,
                                        "description" text NOT NULL,
                                        "date_created" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                        "date_modified" datetime ,
                                        PRIMARY KEY ("id_filesystem_tag")
                                          CONSTRAINT "fkx_filesystem_tag_fk_application" FOREIGN KEY ("fk_application") REFERENCES "webhemi_application" ("id_application") ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE IF EXISTS "webhemi_filesystem_to_filesystem_tag";
CREATE TABLE "webhemi_filesystem_to_filesystem_tag" (
                                                      "id_filesystem_to_filesystem_tag" int(10)  NOT NULL ,
                                                      "fk_filesystem" int(10)  NOT NULL,
                                                      "fk_filesystem_tag" int(10)  NOT NULL,
                                                      PRIMARY KEY ("id_filesystem_to_filesystem_tag")
                                                        CONSTRAINT "fkx_filesystem_to_filesystem_tag_fk_filesystem" FOREIGN KEY ("fk_filesystem") REFERENCES "webhemi_filesystem" ("id_filesystem") ON DELETE CASCADE ON UPDATE CASCADE,
                                                      CONSTRAINT "fkx_filesystem_to_filesystem_tag_fk_filesystem_tag" FOREIGN KEY ("fk_filesystem_tag") REFERENCES "webhemi_filesystem_tag" ("id_filesystem_tag") ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE IF EXISTS "webhemi_policy";
CREATE TABLE "webhemi_policy" (
                                "id_policy" int(10)  NOT NULL ,
                                "fk_resource" int(10)  DEFAULT NULL,
                                "fk_application" int(10)  DEFAULT NULL,
                                "name" varchar(255) NOT NULL,
                                "title" varchar(255) NOT NULL,
                                "description" text NOT NULL,
                                "is_read_only" tinyint(1) NOT NULL DEFAULT '0',
                                "date_created" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                "date_modified" datetime ,
                                PRIMARY KEY ("id_policy")
                                  CONSTRAINT "fkx_policy_fk_application" FOREIGN KEY ("fk_application") REFERENCES "webhemi_application" ("id_application") ON DELETE CASCADE ON UPDATE CASCADE,
                                CONSTRAINT "fkx_policy_fk_resource" FOREIGN KEY ("fk_resource") REFERENCES "webhemi_resource" ("id_resource") ON DELETE CASCADE ON UPDATE CASCADE
);
INSERT INTO "webhemi_policy" VALUES (777,NULL,NULL,'supervisor','Supervisor access','Allow access to all resources in every application.',1,'2019-01-24 13:39:14',NULL),(1000001,1000001,1,'dashboard','Dashborad access','Allow to view the dashboard.',1,'2019-01-24 13:39:14',NULL),(1100001,1100001,1,'content-editor-index','Content Editor access','View the Content Editor page.',1,'2019-01-24 13:39:14',NULL),(1200001,1200001,1,'control-panel-index','Control Panel access','View the Control Panel page.',1,'2019-01-24 13:39:14',NULL),(1200101,1200101,1,'control-panel-domains-list','List Domains','',1,'2019-01-24 13:39:14',NULL),(1200102,1200102,1,'control-panel-domains-view','View a Domain','',1,'2019-01-24 13:39:14',NULL),(1200103,1200103,1,'control-panel-domains-add','Add a new Domain','',1,'2019-01-24 13:39:14',NULL),(1200104,1200104,1,'control-panel-domains-edit','Edit a Domain','',1,'2019-01-24 13:39:14',NULL),(1200105,1200105,1,'control-panel-domains-delete','Delete a Domain','',1,'2019-01-24 13:39:14',NULL),(1200201,1200201,1,'control-panel-applications-list','List Applications','',1,'2019-01-24 13:39:14',NULL),(1200202,1200202,1,'control-panel-applications-view','View an Application','',1,'2019-01-24 13:39:14',NULL),(1200203,1200203,1,'control-panel-applications-add','Add a new Application','',1,'2019-01-24 13:39:14',NULL),(1200204,1200204,1,'control-panel-applications-edit','Edit an Application','',1,'2019-01-24 13:39:14',NULL),(1200205,1200205,1,'control-panel-applications-delete','Delete an Application','',1,'2019-01-24 13:39:14',NULL),(1200301,1200301,1,'control-panel-settings-list','List Settings','',1,'2019-01-24 13:39:14',NULL),(1200302,1200302,1,'control-panel-settings-view','View a Setting','',1,'2019-01-24 13:39:14',NULL),(1200303,1200303,1,'control-panel-settings-add','Add a new Setting','',1,'2019-01-24 13:39:14',NULL),(1200304,1200304,1,'control-panel-settings-edit','Edit a Setting','',1,'2019-01-24 13:39:14',NULL),(1200305,1200305,1,'control-panel-settings-delete','Delete a Setting','',1,'2019-01-24 13:39:14',NULL),(1200401,1200401,1,'control-panel-themes-list','List Themes','',1,'2019-01-24 13:39:14',NULL),(1200402,1200402,1,'control-panel-themes-view','View a Theme','',1,'2019-01-24 13:39:14',NULL),(1200403,1200403,1,'control-panel-themes-add','Add a Theme','',1,'2019-01-24 13:39:14',NULL),(1200404,1200404,1,'control-panel-themes-delete','Delete a Theme','',1,'2019-01-24 13:39:14',NULL),(1200501,1200501,1,'control-panel-addons-list','List AddOns','',1,'2019-01-24 13:39:14',NULL),(1200502,1200502,1,'control-panel-addons-view','View a AddOn','',1,'2019-01-24 13:39:14',NULL),(1200503,1200503,1,'control-panel-addons-add','Add a new AddOn','',1,'2019-01-24 13:39:14',NULL),(1200504,1200504,1,'control-panel-addons-edit','Edit a AddOn','',1,'2019-01-24 13:39:14',NULL),(1200505,1200505,1,'control-panel-addons-delete','Delete a AddOn','',1,'2019-01-24 13:39:14',NULL),(1200601,1200601,1,'control-panel-users-list','List Users','',1,'2019-01-24 13:39:14',NULL),(1200602,1200602,1,'control-panel-users-view','View a User','',1,'2019-01-24 13:39:14',NULL),(1200603,1200603,1,'control-panel-users-add','Add a new User','',1,'2019-01-24 13:39:14',NULL),(1200604,1200604,1,'control-panel-users-edit','Edit a User','',1,'2019-01-24 13:39:14',NULL),(1200605,1200605,1,'control-panel-users-delete','Delete a User','',1,'2019-01-24 13:39:14',NULL),(1200701,1200701,1,'control-panel-groups-list','List Groups','',1,'2019-01-24 13:39:14',NULL),(1200702,1200702,1,'control-panel-groups-view','View a Group','',1,'2019-01-24 13:39:14',NULL),(1200703,1200703,1,'control-panel-groups-add','Add a new Group','',1,'2019-01-24 13:39:14',NULL),(1200704,1200704,1,'control-panel-groups-edit','Edit a Group','',1,'2019-01-24 13:39:14',NULL),(1200705,1200705,1,'control-panel-groups-delete','Delete a Group','',1,'2019-01-24 13:39:14',NULL),(1200801,1200801,1,'control-panel-resources-list','List Resources','',1,'2019-01-24 13:39:14',NULL),(1200802,1200802,1,'control-panel-resources-view','View a Resource','',1,'2019-01-24 13:39:14',NULL),(1200803,1200803,1,'control-panel-resources-add','Add a new Resource','',1,'2019-01-24 13:39:14',NULL),(1200804,1200804,1,'control-panel-resources-edit','Edit a Resource','',1,'2019-01-24 13:39:14',NULL),(1200805,1200805,1,'control-panel-resources-delete','Delete a Resource','',1,'2019-01-24 13:39:14',NULL),(1200901,1200901,1,'control-panel-policies-list','List Policies','',1,'2019-01-24 13:39:14',NULL),(1200902,1200902,1,'control-panel-policies-view','View a Policy','',1,'2019-01-24 13:39:14',NULL),(1200903,1200903,1,'control-panel-policies-add','Add a new Policy','',1,'2019-01-24 13:39:14',NULL),(1200904,1200904,1,'control-panel-policies-edit','Edit a Policy','',1,'2019-01-24 13:39:14',NULL),(1200905,1200905,1,'control-panel-policies-delete','Delete a Policy','',1,'2019-01-24 13:39:14',NULL),(1201001,1201001,1,'control-panel-logs-list','List Logs','',1,'2019-01-24 13:39:14',NULL),(1201002,1201002,1,'control-panel-logs-view','View a Log','',1,'2019-01-24 13:39:14',NULL),(1300001,1300001,1,'about-index','View the About page','',1,'2019-01-24 13:39:14',NULL);

DROP TABLE IF EXISTS "webhemi_resource";
CREATE TABLE "webhemi_resource" (
                                  "id_resource" int(10)  NOT NULL ,
                                  "name" varchar(255) NOT NULL,
                                  "title" varchar(255) NOT NULL,
                                  "description" text NOT NULL,
                                  "type" text  NOT NULL,
                                  "is_read_only" tinyint(1) NOT NULL DEFAULT '0',
                                  "date_created" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                  "date_modified" datetime ,
                                  PRIMARY KEY ("id_resource")
);
INSERT INTO "webhemi_resource" VALUES (1000001,'admin-dashboard','Dashboard','','route',1,'2019-01-24 13:39:14',NULL),(1100001,'admin-content-editor-index','Access to Content Editor','','route',1,'2019-01-24 13:39:14',NULL),(1200001,'admin-control-panel-index','List Control Panel items','','route',1,'2019-01-24 13:39:14',NULL),(1200101,'admin-control-panel-domains-list','List Domains','','route',1,'2019-01-24 13:39:14',NULL),(1200102,'admin-control-panel-domains-view','View a Domain','','route',1,'2019-01-24 13:39:14',NULL),(1200103,'admin-control-panel-domains-add','Add a new Domain','','route',1,'2019-01-24 13:39:14',NULL),(1200104,'admin-control-panel-domains-edit','Edit a Domain','','route',1,'2019-01-24 13:39:14',NULL),(1200105,'admin-control-panel-domains-delete','Delete a Domain','','route',1,'2019-01-24 13:39:14',NULL),(1200201,'admin-control-panel-applications-list','List Applications','','route',1,'2019-01-24 13:39:14',NULL),(1200202,'admin-control-panel-applications-view','View an Application','','route',1,'2019-01-24 13:39:14',NULL),(1200203,'admin-control-panel-applications-add','Add a new Application','','route',1,'2019-01-24 13:39:14',NULL),(1200204,'admin-control-panel-applications-edit','Edit an Application','','route',1,'2019-01-24 13:39:14',NULL),(1200205,'admin-control-panel-applications-delete','Delete an Application','','route',1,'2019-01-24 13:39:14',NULL),(1200301,'admin-control-panel-settings-list','List Settings','','route',1,'2019-01-24 13:39:14',NULL),(1200302,'admin-control-panel-settings-view','View a Setting','','route',1,'2019-01-24 13:39:14',NULL),(1200303,'admin-control-panel-settings-add','Add a new Setting','','route',1,'2019-01-24 13:39:14',NULL),(1200304,'admin-control-panel-settings-edit','Edit a Setting','','route',1,'2019-01-24 13:39:14',NULL),(1200305,'admin-control-panel-settings-delete','Delete a Setting','','route',1,'2019-01-24 13:39:14',NULL),(1200401,'admin-control-panel-themes-list','List Themes','','route',1,'2019-01-24 13:39:14',NULL),(1200402,'admin-control-panel-themes-view','View a Theme','','route',1,'2019-01-24 13:39:14',NULL),(1200403,'admin-control-panel-themes-add','Add a new Theme','','route',1,'2019-01-24 13:39:14',NULL),(1200404,'admin-control-panel-themes-delete','Delete a Theme','','route',1,'2019-01-24 13:39:14',NULL),(1200501,'admin-control-panel-addons-list','List AddOns','','route',1,'2019-01-24 13:39:14',NULL),(1200502,'admin-control-panel-addons-view','View a AddOn','','route',1,'2019-01-24 13:39:14',NULL),(1200503,'admin-control-panel-addons-add','Add a new AddOn','','route',1,'2019-01-24 13:39:14',NULL),(1200504,'admin-control-panel-addons-edit','Edit a AddOn','','route',1,'2019-01-24 13:39:14',NULL),(1200505,'admin-control-panel-addons-delete','Delete a AddOn','','route',1,'2019-01-24 13:39:14',NULL),(1200601,'admin-control-panel-users-list','List Users','','route',1,'2019-01-24 13:39:14',NULL),(1200602,'admin-control-panel-users-view','View a User','','route',1,'2019-01-24 13:39:14',NULL),(1200603,'admin-control-panel-users-add','Add a new User','','route',1,'2019-01-24 13:39:14',NULL),(1200604,'admin-control-panel-users-edit','Edit a User','','route',1,'2019-01-24 13:39:14',NULL),(1200605,'admin-control-panel-users-delete','Delete a User','','route',1,'2019-01-24 13:39:14',NULL),(1200701,'admin-control-panel-groups-list','List Groups','','route',1,'2019-01-24 13:39:14',NULL),(1200702,'admin-control-panel-groups-view','View a Group','','route',1,'2019-01-24 13:39:14',NULL),(1200703,'admin-control-panel-groups-add','Add a new Group','','route',1,'2019-01-24 13:39:14',NULL),(1200704,'admin-control-panel-groups-edit','Edit a Group','','route',1,'2019-01-24 13:39:14',NULL),(1200705,'admin-control-panel-groups-delete','Delete a Group','','route',1,'2019-01-24 13:39:14',NULL),(1200801,'admin-control-panel-resources-list','List Resources','','route',1,'2019-01-24 13:39:14',NULL),(1200802,'admin-control-panel-resources-view','View a Resource','','route',1,'2019-01-24 13:39:14',NULL),(1200803,'admin-control-panel-resources-add','Add a new Resource','','route',1,'2019-01-24 13:39:14',NULL),(1200804,'admin-control-panel-resources-edit','Edit a Resource','','route',1,'2019-01-24 13:39:14',NULL),(1200805,'admin-control-panel-resources-delete','Delete a Resource','','route',1,'2019-01-24 13:39:14',NULL),(1200901,'admin-control-panel-policies-list','List Policies','','route',1,'2019-01-24 13:39:14',NULL),(1200902,'admin-control-panel-policies-view','View a Policy','','route',1,'2019-01-24 13:39:14',NULL),(1200903,'admin-control-panel-policies-add','Add a new Policy','','route',1,'2019-01-24 13:39:14',NULL),(1200904,'admin-control-panel-policies-edit','Edit a Policy','','route',1,'2019-01-24 13:39:14',NULL),(1200905,'admin-control-panel-policies-delete','Delete a Policy','','route',1,'2019-01-24 13:39:14',NULL),(1201001,'admin-control-panel-logs-list','List Logs','','route',1,'2019-01-24 13:39:14',NULL),(1201002,'admin-control-panel-logs-view','View a Log','','route',1,'2019-01-24 13:39:14',NULL),(1300001,'admin-about-index','View the About page','','route',1,'2019-01-24 13:39:14',NULL);

DROP TABLE IF EXISTS "webhemi_user";
CREATE TABLE "webhemi_user" (
                              "id_user" int(10)  NOT NULL ,
                              "username" varchar(255) NOT NULL,
                              "email" varchar(255) DEFAULT NULL,
                              "password" varchar(60) NOT NULL,
                              "hash" varchar(32) DEFAULT NULL,
                              "is_active" tinyint(1) NOT NULL DEFAULT '0',
                              "is_enabled" tinyint(1) NOT NULL DEFAULT '0',
                              "date_created" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              "date_modified" datetime ,
                              PRIMARY KEY ("id_user")
);
INSERT INTO "webhemi_user" VALUES (1,'admin','admin@foo.org','$2y$09$dmrDfcYZt9jORA4vx9MKpeyRt0ilCH/gxSbSHcfBtGaghMJ30tKzS','hash-admin',1,1,'2019-01-24 13:39:15',NULL);

DROP TABLE IF EXISTS "webhemi_user_group";
CREATE TABLE "webhemi_user_group" (
                                    "id_user_group" int(10)  NOT NULL ,
                                    "name" varchar(255) NOT NULL,
                                    "title" varchar(30) NOT NULL,
                                    "description" text NOT NULL,
                                    "is_read_only" tinyint(1) NOT NULL DEFAULT '0',
                                    "date_created" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                    "date_modified" datetime ,
                                    PRIMARY KEY ("id_user_group")
);
INSERT INTO "webhemi_user_group" VALUES (1,'admin','Administrators','Group for global administrators',1,'2019-01-24 13:39:15',NULL);

DROP TABLE IF EXISTS "webhemi_user_group_to_policy";
CREATE TABLE "webhemi_user_group_to_policy" (
                                              "id_user_group_to_policy" int(10)  NOT NULL ,
                                              "fk_user_group" int(10)  NOT NULL,
                                              "fk_policy" int(10)  NOT NULL,
                                              PRIMARY KEY ("id_user_group_to_policy")
                                                CONSTRAINT "fkx_user_group_to_policy_fk_policy" FOREIGN KEY ("fk_policy") REFERENCES "webhemi_policy" ("id_policy") ON DELETE CASCADE ON UPDATE CASCADE,
                                              CONSTRAINT "fkx_user_group_to_policy_fk_user_group" FOREIGN KEY ("fk_user_group") REFERENCES "webhemi_user_group" ("id_user_group") ON DELETE CASCADE ON UPDATE CASCADE
);
INSERT INTO "webhemi_user_group_to_policy" VALUES (1,1,777);

DROP TABLE IF EXISTS "webhemi_user_meta";
CREATE TABLE "webhemi_user_meta" (
                                   "id_user_meta" int(10)  NOT NULL ,
                                   "fk_user" int(10)  NOT NULL,
                                   "meta_key" varchar(255) NOT NULL,
                                   "meta_data" longtext NOT NULL,
                                   "date_created" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                   "date_modified" datetime ,
                                   PRIMARY KEY ("id_user_meta")
                                     CONSTRAINT "fkx_user_meta_fk_user" FOREIGN KEY ("fk_user") REFERENCES "webhemi_user" ("id_user") ON DELETE CASCADE ON UPDATE CASCADE
);
INSERT INTO "webhemi_user_meta" VALUES (1,1,'display_name','Admin Joe','2019-01-24 13:39:15',NULL),(2,1,'gender','male','2019-01-24 13:39:15',NULL),(3,1,'avatar','/img/avatars/suit_man.svg','2019-01-24 13:39:15',NULL),(4,1,'avatar_type','file','2019-01-24 13:39:15',NULL),(5,1,'email_visible','0','2019-01-24 13:39:15',NULL),(6,1,'location','','2019-01-24 13:39:15',NULL),(7,1,'instant_messengers','','2019-01-24 13:39:15',NULL),(8,1,'phone_numbers','','2019-01-24 13:39:15',NULL),(9,1,'social_networks','','2019-01-24 13:39:15',NULL),(10,1,'websites','','2019-01-24 13:39:15',NULL),(11,1,'introduction','','2019-01-24 13:39:15',NULL);

DROP TABLE IF EXISTS "webhemi_user_to_policy";
CREATE TABLE "webhemi_user_to_policy" (
                                        "id_user_to_policy" int(10)  NOT NULL ,
                                        "fk_user" int(10)  NOT NULL,
                                        "fk_policy" int(10)  NOT NULL,
                                        PRIMARY KEY ("id_user_to_policy")
                                          CONSTRAINT "fkx_user_to_policy_fk_policy" FOREIGN KEY ("fk_policy") REFERENCES "webhemi_policy" ("id_policy") ON DELETE CASCADE ON UPDATE CASCADE,
                                        CONSTRAINT "fkx_user_to_policy_fk_user" FOREIGN KEY ("fk_user") REFERENCES "webhemi_user" ("id_user") ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE IF EXISTS "webhemi_user_to_user_group";
CREATE TABLE "webhemi_user_to_user_group" (
                                            "id_user_to_user_group" int(10)  NOT NULL ,
                                            "fk_user" int(10)  NOT NULL,
                                            "fk_user_group" int(10)  NOT NULL,
                                            PRIMARY KEY ("id_user_to_user_group")
                                              CONSTRAINT "fkx_user_to_user_group_fk_user" FOREIGN KEY ("fk_user") REFERENCES "webhemi_user" ("id_user") ON DELETE CASCADE ON UPDATE CASCADE,
                                            CONSTRAINT "fkx_user_to_user_group_fk_user_group" FOREIGN KEY ("fk_user_group") REFERENCES "webhemi_user_group" ("id_user_group") ON DELETE CASCADE ON UPDATE CASCADE
);
INSERT INTO "webhemi_user_to_user_group" VALUES (1,1,1);

CREATE INDEX "webhemi_policy_unq_policy_title" ON "webhemi_policy" ("title");
CREATE INDEX "webhemi_policy_unq_policy" ON "webhemi_policy" ("fk_resource","fk_application");
CREATE INDEX "webhemi_policy_idx_policy_fk_resource" ON "webhemi_policy" ("fk_resource");
CREATE INDEX "webhemi_policy_idx_policy_fk_application" ON "webhemi_policy" ("fk_application");
CREATE INDEX "webhemi_filesystem_meta_unq_filesystem_meta" ON "webhemi_filesystem_meta" ("fk_filesystem","meta_key");
CREATE INDEX "webhemi_filesystem_document_attachment_unq_filesystem_document_attachment" ON "webhemi_filesystem_document_attachment" ("fk_filesystem_document","fk_filesystem");
CREATE INDEX "webhemi_filesystem_document_attachment_idx_filesystem_document_attachment_filesystem_document" ON "webhemi_filesystem_document_attachment" ("fk_filesystem_document");
CREATE INDEX "webhemi_filesystem_document_attachment_idx_filesystem_document_attachment_filesystem" ON "webhemi_filesystem_document_attachment" ("fk_filesystem");
CREATE INDEX "webhemi_application_unq_application_name" ON "webhemi_application" ("name");
CREATE INDEX "webhemi_application_unq_application_title" ON "webhemi_application" ("title");
CREATE INDEX "webhemi_application_unq_application_url" ON "webhemi_application" ("fk_domain","path");
CREATE INDEX "webhemi_application_indx_application_fk_domain" ON "webhemi_application" ("fk_domain");
CREATE INDEX "webhemi_application_indx_application_path" ON "webhemi_application" ("path");
CREATE INDEX "webhemi_user_unq_user_username" ON "webhemi_user" ("username");
CREATE INDEX "webhemi_user_unq_user_email" ON "webhemi_user" ("email");
CREATE INDEX "webhemi_user_unq_user_hash" ON "webhemi_user" ("hash");
CREATE INDEX "webhemi_user_idx_user_password" ON "webhemi_user" ("password");
CREATE INDEX "webhemi_user_idx_user_is_active" ON "webhemi_user" ("is_active");
CREATE INDEX "webhemi_user_idx_user_is_enabled" ON "webhemi_user" ("is_enabled");
CREATE INDEX "webhemi_resource_unq_resource_name" ON "webhemi_resource" ("name");
CREATE INDEX "webhemi_resource_unq_resource_title" ON "webhemi_resource" ("title");
CREATE INDEX "webhemi_user_group_unq_user_group_title" ON "webhemi_user_group" ("title");
CREATE INDEX "webhemi_filesystem_tag_unq_filesystem_file_file_hash" ON "webhemi_filesystem_tag" ("fk_application","name");
CREATE INDEX "webhemi_filesystem_tag_idx_filesystem_tag_fk_application" ON "webhemi_filesystem_tag" ("fk_application");
CREATE INDEX "webhemi_user_to_policy_unq_user_to_policy" ON "webhemi_user_to_policy" ("fk_user","fk_policy");
CREATE INDEX "webhemi_user_to_policy_idx_user_to_policy_fk_user" ON "webhemi_user_to_policy" ("fk_user");
CREATE INDEX "webhemi_user_to_policy_idx_user_to_policy_fk_policy" ON "webhemi_user_to_policy" ("fk_policy");
CREATE INDEX "webhemi_domain_unq_domain_name" ON "webhemi_domain" ("domain");
CREATE INDEX "webhemi_domain_unq_domain_title" ON "webhemi_domain" ("title");
CREATE INDEX "webhemi_filesystem_category_unq_filesystem_file_file_hash" ON "webhemi_filesystem_category" ("fk_application","name");
CREATE INDEX "webhemi_filesystem_category_idx_filesystem_category_fk_application" ON "webhemi_filesystem_category" ("fk_application");
CREATE INDEX "webhemi_filesystem_document_idx_filesystem_document_content_revision" ON "webhemi_filesystem_document" ("content_revision");
CREATE INDEX "webhemi_filesystem_document_idx_filesystem_document_fk_parent_revision" ON "webhemi_filesystem_document" ("fk_parent_revision");
CREATE INDEX "webhemi_filesystem_document_id_filesystem_document_fk_author" ON "webhemi_filesystem_document" ("fk_author");
CREATE INDEX "webhemi_user_to_user_group_unq_user_to_user_group" ON "webhemi_user_to_user_group" ("fk_user","fk_user_group");
CREATE INDEX "webhemi_user_to_user_group_idx_user_to_user_group_fk_user" ON "webhemi_user_to_user_group" ("fk_user");
CREATE INDEX "webhemi_user_to_user_group_idx_user_to_user_group_fk_user_group" ON "webhemi_user_to_user_group" ("fk_user_group");
CREATE INDEX "webhemi_filesystem_to_filesystem_tag_unq_filesystem_to_filesystem_tag" ON "webhemi_filesystem_to_filesystem_tag" ("fk_filesystem_tag","fk_filesystem");
CREATE INDEX "webhemi_filesystem_to_filesystem_tag_idx_filesystem_to_filesystem_tag_filesystem_tag" ON "webhemi_filesystem_to_filesystem_tag" ("fk_filesystem_tag");
CREATE INDEX "webhemi_filesystem_to_filesystem_tag_idx_filesystem_to_filesystem_tag_filesystem" ON "webhemi_filesystem_to_filesystem_tag" ("fk_filesystem");
CREATE INDEX "webhemi_user_group_to_policy_unq_user_group_to_policy" ON "webhemi_user_group_to_policy" ("fk_user_group","fk_policy");
CREATE INDEX "webhemi_user_group_to_policy_idx_user_group_to_policy_fk_user_group" ON "webhemi_user_group_to_policy" ("fk_user_group");
CREATE INDEX "webhemi_user_group_to_policy_idx_user_group_to_policy_fk_policy" ON "webhemi_user_group_to_policy" ("fk_policy");
CREATE INDEX "webhemi_filesystem_file_unq_filesystem_file_file_hash" ON "webhemi_filesystem_file" ("file_hash");
CREATE INDEX "webhemi_filesystem_file_unq_filesystem_file_path" ON "webhemi_filesystem_file" ("path");
CREATE INDEX "webhemi_filesystem_directory_unq_filesystem_directory_proxy" ON "webhemi_filesystem_directory" ("proxy");
CREATE INDEX "webhemi_user_meta_unq_user_meta" ON "webhemi_user_meta" ("fk_user","meta_key");
CREATE INDEX "webhemi_filesystem_unq_uri" ON "webhemi_filesystem" ("fk_application","path","basename");
CREATE INDEX "webhemi_filesystem_idx_filesystem_fk_application" ON "webhemi_filesystem" ("fk_application");
CREATE INDEX "webhemi_filesystem_idx_filesystem_fk_category" ON "webhemi_filesystem" ("fk_category");
CREATE INDEX "webhemi_filesystem_idx_filesystem_fk_parent_node" ON "webhemi_filesystem" ("fk_parent_node");
CREATE INDEX "webhemi_filesystem_idx_filesystem_fk_filesystem_document" ON "webhemi_filesystem" ("fk_filesystem_document");
CREATE INDEX "webhemi_filesystem_idx_filesystem_fk_filesystem_file" ON "webhemi_filesystem" ("fk_filesystem_file");
CREATE INDEX "webhemi_filesystem_idx_filesystem_fk_filesystem_directory" ON "webhemi_filesystem" ("fk_filesystem_directory");
CREATE INDEX "webhemi_filesystem_idx_filesystem_fk_filesystem_link" ON "webhemi_filesystem" ("fk_filesystem_link");
CREATE INDEX "webhemi_filesystem_idx_filesystem_path" ON "webhemi_filesystem" ("path");
CREATE INDEX "webhemi_filesystem_idx_filesystem_file_basename" ON "webhemi_filesystem" ("basename");
CREATE INDEX "webhemi_filesystem_idx_filesystem_is_hidden" ON "webhemi_filesystem" ("is_hidden");
CREATE INDEX "webhemi_filesystem_idx_filesystem_is_read_only" ON "webhemi_filesystem" ("is_read_only");
CREATE INDEX "webhemi_filesystem_idx_filesystem_is_deleted" ON "webhemi_filesystem" ("is_deleted");
CREATE INDEX "webhemi_filesystem_idx_filesystem_date_published_archive" ON "webhemi_filesystem" ("date_published_archive");
END TRANSACTION;

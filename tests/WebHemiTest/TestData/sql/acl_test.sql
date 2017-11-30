INSERT INTO `webhemi_application` VALUES (90000, 'unit_test', 'Unit Test', 'Unit Test Application.', 'www', 'default', 'domain', 'en_GB.UTF-8', 'Europe/London', '',  0, 1, '2016-10-10 10:10:10', NULL);
INSERT INTO `webhemi_am_resource` VALUES (90000, '\WebHemiTestX\Some\Resource', 'Some resource', '', 'route', 0, '2016-10-10 10:10:10', NULL);
INSERT INTO `webhemi_am_policy` VALUES (90000, NULL, NULL, 'all', 'All - All - Any', 'Access to all resources in every application with any method', NULL, 1, '2016-10-10 10:10:10', NULL), (90001, 90000, NULL, 'some', 'Some - All - Get', 'Access to `Some Resource` in every application', 'GET', 1, '2016-10-10 10:10:10', NULL), (90002, NULL, 90000, 'all-unit', 'All - Unit - Post', 'Access to all resources in `Unit Test` application', 'POST', 1, '2016-10-10 10:10:10', NULL), (90003, 90000, 90000, 'some-unit', 'Some - Unit - Any', 'Access to `Some Resource` in `Unit Test` application', NULL, 1, '2016-10-10 10:10:10', NULL);
INSERT INTO `webhemi_user_group` VALUES (90000, 'test1', 'Testers 1', 'Group for testers 1', 1, '2016-10-10 10:10:10', NULL), (90001, 'test2', 'Testers 2', 'Group for testers 2', 1, '2016-10-10 10:10:10', NULL);
INSERT INTO `webhemi_user` VALUES (90000, 'test1', 'test1@foo.org', 'x', 'y1', 1, 1, '2016-10-10 10:10:10', NULL), (90001, 'test2', 'test2@foo.org', 'x', 'y2', 1, 1, '2016-10-10 10:10:10', NULL);
INSERT INTO `webhemi_user_to_am_policy` VALUES (90000, 90000, 90000), (90001, 90000, 90001);
INSERT INTO `webhemi_user_to_am_policy` VALUES (90002, 90001, 90003);
INSERT INTO `webhemi_user_to_user_group` VALUES (90000, 90000, 90000);
INSERT INTO `webhemi_user_to_user_group` VALUES (90001, 90001, 90001);
INSERT INTO `webhemi_user_group_to_am_policy` VALUES (90000, 90000, 90000), (90001, 90000, 90003);
INSERT INTO `webhemi_user_group_to_am_policy` VALUES (90002, 90001, 90001), (90003, 90001, 90003);

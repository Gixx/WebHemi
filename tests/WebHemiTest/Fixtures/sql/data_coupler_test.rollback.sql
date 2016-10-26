DELETE FROM `webhemi_user_to_am_policy` WHERE `id_user_to_am_policy` IN (90000, 90001, 90002, 90003, 90004);
DELETE FROM `webhemi_user_to_user_group` WHERE `id_user_to_user_group` IN (90000, 90001, 90002);
DELETE FROM `webhemi_user_group_to_am_policy` WHERE `id_user_group_to_am_policy` IN (90000, 90001, 90002, 90003);
DELETE FROM `webhemi_am_policy`  WHERE `id_am_policy` IN (90000, 90001, 90002, 90003);
DELETE FROM `webhemi_application` WHERE `id_application` IN (90000);
DELETE FROM `webhemi_am_resource` WHERE `id_am_resource` IN (90000);
DELETE FROM `webhemi_user_group` WHERE `id_user_group` IN (90000, 90001);
DELETE FROM `webhemi_user` WHERE `id_user` IN (90000, 90001);

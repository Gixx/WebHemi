SELECT
    ug.`id_user_group`,
    ug.`name`,
    ug.`title`,
    ug.`description`,
    ug.`is_read_only`,
    ug.`date_created`,
    ug.`date_modified`
FROM
    `webhemi_user_group` AS ug
WHERE
    ug.`id_user_group` = :idUserGroup

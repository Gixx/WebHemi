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
    INNER JOIN `webhemi_user_to_user_group` AS utug ON ug.`id_user_group` = utug.`fk_user_group`
WHERE
    utug.`fk_user` = :userId
ORDER BY
    ug.`name`
LIMIT
    :limit
OFFSET
    :offset

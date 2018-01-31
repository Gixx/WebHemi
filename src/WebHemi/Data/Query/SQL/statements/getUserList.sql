SELECT
    u.`id_user`,
    u.`username`,
    u.`email`,
    u.`password`,
    u.`hash`,
    u.`is_active`,
    u.`is_enabled`,
    u.`date_created`,
    u.`date_modified`
FROM
    `webhemi_user` AS u
ORDER BY
    u.`username`
LIMIT
    :limit
OFFSET
    :offset
